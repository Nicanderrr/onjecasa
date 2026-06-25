<?php

function paystack_load_env(string $rootPath): array
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

function paystack_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value !== false && $value !== '' ? $value : $default;
}

function paystack_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function paystack_requires_gateway(string $method): bool
{
    return in_array(strtolower(trim($method)), ['mobile money', 'bank transfer'], true);
}

function paystack_channels_for(string $method): array
{
    return strtolower(trim($method)) === 'mobile money' ? ['mobile_money'] : ['bank_transfer'];
}

function paystack_verify_reference(string $reference): array
{
    $secretKey = paystack_env('PAYSTACK_SECRET_KEY');

    if (!$secretKey) {
        return ['ok' => false, 'message' => 'Paystack secret key is not configured.'];
    }

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'message' => 'PHP cURL is not enabled.'];
    }

    $ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $secretKey,
            'Cache-Control: no-cache',
        ],
        CURLOPT_TIMEOUT => 30,
    ]);

    $body = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false) {
        return ['ok' => false, 'message' => $error ?: 'Unable to contact Paystack.'];
    }

    $decoded = json_decode($body, true);
    if ($status < 200 || $status >= 300 || empty($decoded['status'])) {
        return ['ok' => false, 'message' => $decoded['message'] ?? ('Paystack verification failed with HTTP ' . $status)];
    }

    $data = $decoded['data'] ?? [];
    if (($data['status'] ?? '') !== 'success') {
        return ['ok' => false, 'message' => 'Paystack transaction is not successful yet.'];
    }

    return ['ok' => true, 'data' => $data];
}

function paystack_save_verified_payment(mysqli $mysqli, array $payload, array $paystackData): array
{
    $SID = trim((string) ($payload['sid'] ?? ''));
    $payId = trim((string) ($payload['pay_id'] ?? ''));
    $method = trim((string) ($payload['pay_method'] ?? ''));
    $reference = trim((string) ($paystackData['reference'] ?? ($payload['reference'] ?? '')));
    $gatewayAmount = ((float) ($paystackData['amount'] ?? 0)) / 100;
    $postedAmount = (float) str_replace(',', '', (string) ($payload['pay_amt'] ?? 0));
    $amount = $gatewayAmount > 0 ? $gatewayAmount : $postedAmount;

    if ($SID === '' || $payId === '' || $reference === '' || !paystack_requires_gateway($method)) {
        return ['ok' => false, 'message' => 'Invalid Paystack payment payload.'];
    }

    $existsStmt = $mysqli->prepare('SELECT COUNT(*) FROM rpos_payments WHERE pay_code = ? OR (SID = ? AND pay_method = ?)');
    $existsStmt->bind_param('sss', $reference, $SID, $method);
    $existsStmt->execute();
    $existsStmt->bind_result($existingPayments);
    $existsStmt->fetch();
    $existsStmt->close();

    if ((int) $existingPayments > 0) {
        return ['ok' => true, 'message' => 'Payment already recorded.'];
    }

    $status = 'Paid';
    $payAmount = number_format($amount, 2, '.', '');

    $postStmt = $mysqli->prepare('INSERT INTO rpos_payments (pay_id, pay_code, SID, pay_amt, pay_method) VALUES (?, ?, ?, ?, ?)');
    $postStmt->bind_param('sssss', $payId, $reference, $SID, $payAmount, $method);

    $updateStmt = $mysqli->prepare('UPDATE invoice_products SET order_status = ? WHERE SID = ?');
    $updateStmt->bind_param('ss', $status, $SID);

    if ($postStmt->execute() && $updateStmt->execute()) {
        $postStmt->close();
        $updateStmt->close();
        return ['ok' => true, 'message' => 'Payment verified and recorded.'];
    }

    $message = $mysqli->error ?: 'Unable to save verified payment.';
    $postStmt->close();
    $updateStmt->close();

    return ['ok' => false, 'message' => $message];
}
