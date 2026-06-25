<?php
$metricCurrentStart = date('Y-m-01 00:00:00');
$metricNextStart = date('Y-m-01 00:00:00', strtotime('+1 month'));
$metricPreviousStart = date('Y-m-01 00:00:00', strtotime('-1 month'));

$fetchMetricValue = function (string $sql, array $params = []) use ($mysqli): float {
    $stmt = $mysqli->prepare($sql);

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $stmt->bind_result($value);
    $stmt->fetch();
    $stmt->close();

    return (float) ($value ?? 0);
};

$buildMetricTrend = function (float $current, float $previous): array {
    if ($previous <= 0 && $current <= 0) {
        $percent = 0;
    } elseif ($previous <= 0) {
        $percent = 100;
    } else {
        $percent = (($current - $previous) / $previous) * 100;
    }

    $rounded = round($percent, 1);
    $sign = $rounded > 0 ? '+' : '';

    return [
        'percent' => $sign . number_format($rounded, 1) . '%',
        'class' => $rounded < 0 ? 'text-danger' : 'text-success',
        'label' => 'from last month',
    ];
};

//1. Customers
$query = "SELECT COUNT(*) FROM `rpos_customers` ";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($customers);
$stmt->fetch();
$stmt->close();

//2. Orders
$query = "SELECT COUNT(*) FROM `invoice_products` ";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($orders);
$stmt->fetch();
$stmt->close();

//3. Orders
$query = "SELECT COUNT(*) FROM `rpos_products` ";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($products);
$stmt->fetch();
$stmt->close();

//4.Sales
$query = "SELECT SUM(pay_amt) FROM `rpos_payments` ";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($sales);
$stmt->fetch();
$stmt->close();

$productsCurrent = $fetchMetricValue(
    "SELECT COUNT(*) FROM rpos_products WHERE created_at >= ? AND created_at < ?",
    [$metricCurrentStart, $metricNextStart]
);
$productsPrevious = $fetchMetricValue(
    "SELECT COUNT(*) FROM rpos_products WHERE created_at >= ? AND created_at < ?",
    [$metricPreviousStart, $metricCurrentStart]
);
$productsTrend = $buildMetricTrend($productsCurrent, $productsPrevious);

$ordersCurrent = $fetchMetricValue(
    "SELECT COUNT(*) FROM invoice_products WHERE created_at >= ? AND created_at < ?",
    [$metricCurrentStart, $metricNextStart]
);
$ordersPrevious = $fetchMetricValue(
    "SELECT COUNT(*) FROM invoice_products WHERE created_at >= ? AND created_at < ?",
    [$metricPreviousStart, $metricCurrentStart]
);
$ordersTrend = $buildMetricTrend($ordersCurrent, $ordersPrevious);

$salesCurrent = $fetchMetricValue(
    "SELECT COALESCE(SUM(pay_amt), 0) FROM rpos_payments WHERE created_at >= ? AND created_at < ?",
    [$metricCurrentStart, $metricNextStart]
);
$salesPrevious = $fetchMetricValue(
    "SELECT COALESCE(SUM(pay_amt), 0) FROM rpos_payments WHERE created_at >= ? AND created_at < ?",
    [$metricPreviousStart, $metricCurrentStart]
);
$salesTrend = $buildMetricTrend($salesCurrent, $salesPrevious);

$customersCurrent = $fetchMetricValue(
    "SELECT COUNT(*) FROM rpos_customers WHERE created_at >= ? AND created_at < ?",
    [$metricCurrentStart, $metricNextStart]
);
$customersPrevious = $fetchMetricValue(
    "SELECT COUNT(*) FROM rpos_customers WHERE created_at >= ? AND created_at < ?",
    [$metricPreviousStart, $metricCurrentStart]
);
$customersTrend = $buildMetricTrend($customersCurrent, $customersPrevious);

$salesChartAnchor = date('Y-m-01');
$salesAnchorQuery = "SELECT MAX(created_at) FROM rpos_payments";
$salesAnchorStmt = $mysqli->prepare($salesAnchorQuery);
$salesAnchorStmt->execute();
$salesAnchorStmt->bind_result($latestPaymentDate);
$salesAnchorStmt->fetch();
$salesAnchorStmt->close();

if (!empty($latestPaymentDate)) {
    $salesChartAnchor = date('Y-m-01', strtotime($latestPaymentDate));
}

$salesChartStart = date('Y-m-01 00:00:00', strtotime($salesChartAnchor . ' -5 months'));
$salesChartEnd = date('Y-m-01 00:00:00', strtotime($salesChartAnchor . ' +1 month'));
$salesPerformance = [];

for ($i = 0; $i < 6; $i++) {
    $monthTime = strtotime($salesChartStart . " +{$i} months");
    $monthKey = date('Y-m', $monthTime);

    $salesPerformance[$monthKey] = [
        'label' => date('M', $monthTime),
        'amount' => 0.0,
        'height' => 0,
    ];
}

$salesChartQuery = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS sales_month,
           COALESCE(SUM(CAST(REPLACE(pay_amt, ',', '') AS DECIMAL(15,2))), 0) AS month_total
    FROM rpos_payments
    WHERE created_at >= ? AND created_at < ?
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
";
$salesChartStmt = $mysqli->prepare($salesChartQuery);
$salesChartStmt->bind_param('ss', $salesChartStart, $salesChartEnd);
$salesChartStmt->execute();
$salesChartResult = $salesChartStmt->get_result();

while ($salesChartRow = $salesChartResult->fetch_assoc()) {
    if (isset($salesPerformance[$salesChartRow['sales_month']])) {
        $salesPerformance[$salesChartRow['sales_month']]['amount'] = (float) $salesChartRow['month_total'];
    }
}

$salesChartStmt->close();

$salesChartMax = max(array_column($salesPerformance, 'amount'));

if ($salesChartMax > 0) {
    foreach ($salesPerformance as $monthKey => $monthData) {
        $salesPerformance[$monthKey]['height'] = max(12, (int) round(($monthData['amount'] / $salesChartMax) * 100));
    }
}

$popularItemColors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'];
$popularItems = [];
$popularItemsTotal = 0.0;

$popularItemsQuery = "
    SELECT PNAME AS product_name,
           COALESCE(SUM(CAST(QTY AS DECIMAL(15,2))), 0) AS total_qty
    FROM invoice_products
    WHERE order_status = 'Paid'
    GROUP BY PNAME
    HAVING total_qty > 0
    ORDER BY total_qty DESC
";
$popularItemsStmt = $mysqli->prepare($popularItemsQuery);
$popularItemsStmt->execute();
$popularItemsResult = $popularItemsStmt->get_result();
$popularItemsRows = [];

while ($popularItemRow = $popularItemsResult->fetch_assoc()) {
    $quantity = (float) $popularItemRow['total_qty'];
    $popularItemsRows[] = [
        'name' => $popularItemRow['product_name'],
        'quantity' => $quantity,
    ];
    $popularItemsTotal += $quantity;
}

$popularItemsStmt->close();

$popularItemsTop = array_slice($popularItemsRows, 0, 5);
$popularItemsOtherQty = array_sum(array_column(array_slice($popularItemsRows, 5), 'quantity'));

if ($popularItemsOtherQty > 0) {
    $popularItemsTop[] = [
        'name' => 'Others',
        'quantity' => $popularItemsOtherQty,
    ];
}

$popularPieStops = [];
$popularPieStart = 0.0;

foreach ($popularItemsTop as $index => $popularItem) {
    $percent = $popularItemsTotal > 0 ? ($popularItem['quantity'] / $popularItemsTotal) * 100 : 0;
    $popularPieEnd = $popularPieStart + $percent;
    $color = $popularItemColors[$index] ?? '#64748b';

    $popularItems[] = [
        'name' => $popularItem['name'],
        'quantity' => $popularItem['quantity'],
        'percent' => $percent,
        'color' => $color,
    ];

    $popularPieStops[] = $color . ' ' . round($popularPieStart, 2) . '% ' . round($popularPieEnd, 2) . '%';
    $popularPieStart = $popularPieEnd;
}

$popularPieGradient = !empty($popularPieStops)
    ? implode(', ', $popularPieStops)
    : '#e2e8f0 0% 100%';

$teamActivities = [];
$dismissedTeamActivities = $_SESSION['dismissed_team_activities'] ?? [];

$pushTeamActivity = function (string $icon, string $class, string $title, string $description, string $dateTime) use (&$teamActivities): void {
    $teamActivities[] = [
        'id' => md5($title . '|' . $description . '|' . $dateTime),
        'icon' => $icon,
        'class' => $class,
        'title' => $title,
        'description' => $description,
        'time' => $dateTime ? date('d/M/Y g:i A', strtotime($dateTime)) : 'Just now',
        'sort_time' => $dateTime ? strtotime($dateTime) : time(),
    ];
};

$activityPaymentsStmt = $mysqli->prepare("
    SELECT pay_code, SID, pay_method, pay_amt, created_at
    FROM rpos_payments
    ORDER BY created_at DESC
    LIMIT 3
");
$activityPaymentsStmt->execute();
$activityPaymentsResult = $activityPaymentsStmt->get_result();

while ($paymentActivity = $activityPaymentsResult->fetch_assoc()) {
    $pushTeamActivity(
        'fas fa-credit-card',
        'activity-success',
        'Payment verified',
        $paymentActivity['pay_method'] . ' payment of ₵' . number_format((float) $paymentActivity['pay_amt'], 2) . ' for order ' . $paymentActivity['SID'] . '.',
        $paymentActivity['created_at']
    );
}
$activityPaymentsStmt->close();

$activityOrdersStmt = $mysqli->prepare("
    SELECT SID, GROUP_CONCAT(PNAME SEPARATOR ', ') AS products, SUM(TOTAL) AS total, order_status, MAX(created_at) AS created_at
    FROM invoice_products
    GROUP BY SID
    ORDER BY MAX(created_at) DESC
    LIMIT 3
");
$activityOrdersStmt->execute();
$activityOrdersResult = $activityOrdersStmt->get_result();

while ($orderActivity = $activityOrdersResult->fetch_assoc()) {
    $isPaid = trim((string) $orderActivity['order_status']) === 'Paid';
    $pushTeamActivity(
        $isPaid ? 'fas fa-receipt' : 'fas fa-clock',
        $isPaid ? 'activity-primary' : 'activity-warning',
        $isPaid ? 'Order completed' : 'Order awaiting payment',
        'Order ' . $orderActivity['SID'] . ' - ' . $orderActivity['products'] . ' totals ₵' . number_format((float) $orderActivity['total'], 2) . '.',
        $orderActivity['created_at']
    );
}
$activityOrdersStmt->close();

$activityStockStmt = $mysqli->prepare("
    SELECT prod_name, prod_stock, created_at
    FROM rpos_products
    WHERE CAST(prod_stock AS SIGNED) <= 10
    ORDER BY CAST(prod_stock AS SIGNED) ASC, created_at DESC
    LIMIT 2
");
$activityStockStmt->execute();
$activityStockResult = $activityStockStmt->get_result();

while ($stockActivity = $activityStockResult->fetch_assoc()) {
    $pushTeamActivity(
        'fas fa-box-open',
        'activity-danger',
        'Low stock alert',
        $stockActivity['prod_name'] . ' has ' . $stockActivity['prod_stock'] . ' units left.',
        $stockActivity['created_at']
    );
}
$activityStockStmt->close();

usort($teamActivities, static function (array $left, array $right): int {
    return $right['sort_time'] <=> $left['sort_time'];
});

$teamActivities = array_slice($teamActivities, 0, 5);
$teamActivities = array_values(array_filter($teamActivities, static function (array $activity) use ($dismissedTeamActivities): bool {
    return !in_array($activity['id'], $dismissedTeamActivities, true);
}));
