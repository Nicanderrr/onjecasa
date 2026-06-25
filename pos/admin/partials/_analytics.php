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
