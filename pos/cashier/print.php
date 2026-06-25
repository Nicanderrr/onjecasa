<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$receiptSalesPerson = '';
$staffId = $_SESSION['staff_id'] ?? '';

if ($staffId !== '') {
    $stmt = $mysqli->prepare('SELECT staff_name FROM rpos_staff WHERE staff_id = ? LIMIT 1');
    $stmt->bind_param('s', $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $receiptSalesPerson = $row['staff_name'] ?? '';
    }
    $stmt->close();
}

require_once('../includes/modern_receipt_print.php');
