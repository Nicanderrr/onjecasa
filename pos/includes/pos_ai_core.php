<?php

function pos_ai_load_env(string $rootPath): array
{
    $env = [];
    $envPath = rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env';

    if (!is_file($envPath)) {
        return $env;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if ($key !== '') {
            $env[$key] = $value;
            if (getenv($key) === false) {
                putenv($key . '=' . $value);
            }
        }
    }

    return $env;
}

function pos_ai_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value !== false && $value !== '' ? $value : $default;
}

function pos_ai_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function pos_ai_scalar(mysqli $mysqli, string $sql): float
{
    $result = $mysqli->query($sql);
    if (!$result) {
        return 0;
    }

    $row = $result->fetch_row();
    return (float) ($row[0] ?? 0);
}

function pos_ai_rows(mysqli $mysqli, string $sql, int $limit = 5): array
{
    $result = $mysqli->query($sql);
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        if (count($rows) >= $limit) {
            break;
        }
    }

    return $rows;
}

function pos_ai_context(mysqli $mysqli): string
{
    $now = date('Y-m-d H:i:s');
    $today = date('Y-m-d');
    $monthStart = date('Y-m-01 00:00:00');

    $totalProducts = pos_ai_scalar($mysqli, 'SELECT COUNT(*) FROM rpos_products');
    $totalCustomers = pos_ai_scalar($mysqli, 'SELECT COUNT(*) FROM rpos_customers');
    $totalOrderRows = pos_ai_scalar($mysqli, 'SELECT COUNT(*) FROM invoice_products');
    $paidOrderRows = pos_ai_scalar($mysqli, "SELECT COUNT(*) FROM invoice_products WHERE order_status = 'Paid'");
    $unpaidOrderRows = pos_ai_scalar($mysqli, "SELECT COUNT(*) FROM invoice_products WHERE order_status = '' OR order_status IS NULL");
    $totalSales = pos_ai_scalar($mysqli, "SELECT COALESCE(SUM(CAST(REPLACE(pay_amt, ',', '') AS DECIMAL(15,2))), 0) FROM rpos_payments");
    $todaySales = pos_ai_scalar($mysqli, "SELECT COALESCE(SUM(CAST(REPLACE(pay_amt, ',', '') AS DECIMAL(15,2))), 0) FROM rpos_payments WHERE DATE(created_at) = '{$today}'");
    $monthSales = pos_ai_scalar($mysqli, "SELECT COALESCE(SUM(CAST(REPLACE(pay_amt, ',', '') AS DECIMAL(15,2))), 0) FROM rpos_payments WHERE created_at >= '{$monthStart}'");

    $popularItems = pos_ai_rows($mysqli, "
        SELECT PNAME, COALESCE(SUM(CAST(QTY AS DECIMAL(15,2))), 0) AS total_qty
        FROM invoice_products
        WHERE order_status = 'Paid'
        GROUP BY PNAME
        ORDER BY total_qty DESC
        LIMIT 5
    ");

    $recentOrders = pos_ai_rows($mysqli, "
        SELECT SID, GROUP_CONCAT(PNAME SEPARATOR ', ') AS products, SUM(TOTAL) AS total, order_status, MAX(created_at) AS created_at
        FROM invoice_products
        GROUP BY SID
        ORDER BY MAX(created_at) DESC
        LIMIT 5
    ");

    $recentPayments = pos_ai_rows($mysqli, "
        SELECT pay_code, SID, pay_method, pay_amt, created_at
        FROM rpos_payments
        ORDER BY created_at DESC
        LIMIT 5
    ");

    $lowStock = pos_ai_rows($mysqli, "
        SELECT prod_name, prod_stock
        FROM rpos_products
        WHERE CAST(prod_stock AS SIGNED) <= 10
        ORDER BY CAST(prod_stock AS SIGNED) ASC, prod_name ASC
        LIMIT 5
    ");

    $popularText = empty($popularItems)
        ? 'No paid popular-item data yet.'
        : implode("\n", array_map(static function ($item) {
            return '- ' . $item['PNAME'] . ': ' . number_format((float) $item['total_qty']) . ' sold';
        }, $popularItems));

    $ordersText = empty($recentOrders)
        ? 'No recent orders found.'
        : implode("\n", array_map(static function ($order) {
            $status = $order['order_status'] !== '' ? $order['order_status'] : 'Not Paid';
            return '- [' . $order['SID'] . '] ' . $order['products'] . ' | GHS ' . number_format((float) $order['total'], 2) . ' | ' . $status . ' | ' . $order['created_at'];
        }, $recentOrders));

    $paymentsText = empty($recentPayments)
        ? 'No recent payments found.'
        : implode("\n", array_map(static function ($payment) {
            return '- [' . $payment['pay_code'] . '] order ' . $payment['SID'] . ' | ' . $payment['pay_method'] . ' | GHS ' . number_format((float) $payment['pay_amt'], 2) . ' | ' . $payment['created_at'];
        }, $recentPayments));

    $lowStockText = empty($lowStock)
        ? 'No products are currently at or below 10 stock.'
        : implode("\n", array_map(static function ($item) {
            return '- ' . $item['prod_name'] . ': ' . $item['prod_stock'] . ' left';
        }, $lowStock));

    return "You are Command Center AI, the POS operations assistant for this PHP retail POS system.
Current System Time: {$now}

LIVE POS CONTEXT:
- Total Products: {$totalProducts}
- Total Customers: {$totalCustomers}
- Total Order Rows: {$totalOrderRows}
- Paid Order Rows: {$paidOrderRows}
- Unpaid Order Rows: {$unpaidOrderRows}
- Lifetime Sales: GHS " . number_format($totalSales, 2) . "
- Today's Sales: GHS " . number_format($todaySales, 2) . "
- Current Month Sales: GHS " . number_format($monthSales, 2) . "

MOST SOLD ITEMS:
{$popularText}

RECENT ORDERS:
{$ordersText}

RECENT PAYMENTS:
{$paymentsText}

LOW STOCK WATCH:
{$lowStockText}

INTERACTION RULES:
1. Help the admin understand sales, orders, payments, inventory, customer activity, and operational risks.
2. If asked what is happening, summarize sales, paid/unpaid order state, recent payments, popular products, and low stock.
3. If asked for recommendations, give practical POS actions such as restocking, checking unpaid orders, reviewing product pricing, or following up on customers.
4. Keep responses concise, specific, and based on the live context above.
5. For image analysis, focus on receipts, product shelves, stock conditions, damaged goods, handwritten order notes, or payment proof.
6. Do not claim to modify the database. If an action needs a system change, tell the user what page or workflow to use.";
}

function pos_ai_call_groq(array $messages, ?string $model = null): array
{
    $apiKey = pos_ai_env('GROQ_API_KEY');
    $model = $model ?: pos_ai_env('GROQ_MODEL', 'llama-3.3-70b-versatile');

    if (!$apiKey || $apiKey === 'gsk_placeholder_replace_me') {
        return ['ok' => false, 'status' => 400, 'message' => 'Groq API key is not configured. Add GROQ_API_KEY to the project .env file.'];
    }

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'status' => 500, 'message' => 'PHP cURL is not enabled. Enable curl in php.ini to use the AI assistant.'];
    }

    $payload = json_encode([
        'model' => $model,
        'messages' => $messages,
        'temperature' => 0.35,
    ]);

    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 45,
    ]);

    $body = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false) {
        return ['ok' => false, 'status' => 500, 'message' => $error ?: 'Unable to contact Groq API.'];
    }

    $decoded = json_decode($body, true);
    if ($status < 200 || $status >= 300) {
        $message = $decoded['error']['message'] ?? ('Groq API error: HTTP ' . $status);
        return ['ok' => false, 'status' => $status ?: 500, 'message' => $message];
    }

    $reply = $decoded['choices'][0]['message']['content'] ?? '';
    return ['ok' => true, 'status' => 200, 'reply' => $reply !== '' ? $reply : 'No response was returned.'];
}

function pos_ai_call_xai(array $messages, ?string $model = null): array
{
    $apiKey = pos_ai_env('XAI_API_KEY');
    $model = $model ?: pos_ai_env('XAI_MODEL', 'grok-4.3');

    if (!$apiKey) {
        return ['ok' => false, 'status' => 400, 'message' => 'xAI API key is not configured. Add XAI_API_KEY to the project .env file.'];
    }

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'status' => 500, 'message' => 'PHP cURL is not enabled. Enable curl in php.ini to use the AI assistant.'];
    }

    $payload = json_encode([
        'model' => $model,
        'input' => $messages,
    ]);

    $ch = curl_init('https://api.x.ai/v1/responses');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 45,
    ]);

    $body = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false) {
        return ['ok' => false, 'status' => 500, 'message' => $error ?: 'Unable to contact xAI API.'];
    }

    $decoded = json_decode($body, true);
    if ($status < 200 || $status >= 300) {
        $message = 'xAI API error: HTTP ' . $status;
        if (isset($decoded['error'])) {
            $message = is_array($decoded['error'])
                ? ($decoded['error']['message'] ?? $message)
                : (string) $decoded['error'];
        }
        return ['ok' => false, 'status' => $status ?: 500, 'message' => $message];
    }

    $reply = $decoded['output_text'] ?? '';

    if ($reply === '' && isset($decoded['output']) && is_array($decoded['output'])) {
        $parts = [];
        foreach ($decoded['output'] as $outputItem) {
            foreach (($outputItem['content'] ?? []) as $contentItem) {
                if (isset($contentItem['text'])) {
                    $parts[] = $contentItem['text'];
                }
            }
        }
        $reply = trim(implode("\n", $parts));
    }

    return ['ok' => true, 'status' => 200, 'reply' => $reply !== '' ? $reply : 'No response was returned.'];
}

function pos_ai_call_provider(array $messages, ?string $model = null, bool $vision = false): array
{
    if (pos_ai_env('XAI_API_KEY')) {
        return pos_ai_call_xai($messages, $model ?: ($vision ? pos_ai_env('XAI_VISION_MODEL', pos_ai_env('XAI_MODEL', 'grok-4.3')) : null));
    }

    return pos_ai_call_groq($messages, $model);
}

function pos_ai_history(array $history): array
{
    $clean = [];
    foreach ($history as $message) {
        $role = $message['role'] ?? '';
        $content = trim((string) ($message['content'] ?? ''));
        if (!in_array($role, ['user', 'assistant'], true) || $content === '') {
            continue;
        }
        $clean[] = ['role' => $role, 'content' => substr($content, 0, 3000)];
    }

    return array_slice($clean, -10);
}

function pos_ai_local_reply(mysqli $mysqli, string $message): string
{
    $context = pos_ai_context($mysqli);
    $lower = strtolower($message);

    $prefix = "AI provider is not connected, so I am using the live POS fallback.\n\n";

    if (str_contains($lower, 'stock') || str_contains($lower, 'product') || str_contains($lower, 'inventory')) {
        $lowStock = pos_ai_rows($mysqli, "
            SELECT prod_name, prod_stock
            FROM rpos_products
            WHERE CAST(prod_stock AS SIGNED) <= 10
            ORDER BY CAST(prod_stock AS SIGNED) ASC, prod_name ASC
            LIMIT 6
        ");

        if (empty($lowStock)) {
            return $prefix . "Inventory looks stable. No products are currently at or below 10 stock.";
        }

        $lines = array_map(static function ($item) {
            return "- " . $item['prod_name'] . ": " . $item['prod_stock'] . " left";
        }, $lowStock);

        return $prefix . "Products needing attention:\n" . implode("\n", $lines);
    }

    if (str_contains($lower, 'popular') || str_contains($lower, 'sold') || str_contains($lower, 'item')) {
        $items = pos_ai_rows($mysqli, "
            SELECT PNAME, COALESCE(SUM(CAST(QTY AS DECIMAL(15,2))), 0) AS total_qty
            FROM invoice_products
            WHERE order_status = 'Paid'
            GROUP BY PNAME
            ORDER BY total_qty DESC
            LIMIT 6
        ");

        if (empty($items)) {
            return $prefix . "No paid item sales are available yet.";
        }

        $lines = array_map(static function ($item) {
            return "- " . $item['PNAME'] . ": " . number_format((float) $item['total_qty']) . " sold";
        }, $items);

        return $prefix . "Most sold items:\n" . implode("\n", $lines);
    }

    $summaryStart = strpos($context, 'LIVE POS CONTEXT:');
    return $prefix . "Current POS snapshot:\n" . trim(substr($context, $summaryStart !== false ? $summaryStart : 0, 900));
}
