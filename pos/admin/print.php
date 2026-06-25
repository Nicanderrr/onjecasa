<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$receiptSalesPerson = '';
$stmt = $mysqli->prepare('SELECT admin_name FROM rpos_admin LIMIT 1');
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $receiptSalesPerson = $row['admin_name'] ?? '';
    }
    $stmt->close();
}

require_once('../includes/modern_receipt_print.php');
