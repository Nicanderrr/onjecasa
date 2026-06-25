<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('../includes/paystack_core.php');

paystack_load_env(dirname(__DIR__, 2));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    paystack_json(['success' => false, 'message' => 'POST request required.'], 405);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    paystack_json(['success' => false, 'message' => 'Invalid JSON payload.'], 400);
}

$reference = trim((string) ($payload['reference'] ?? ''));
if ($reference === '') {
    paystack_json(['success' => false, 'message' => 'Missing Paystack reference.'], 422);
}

$verification = paystack_verify_reference($reference);
if (!$verification['ok']) {
    paystack_json(['success' => false, 'message' => $verification['message']], 422);
}

$save = paystack_save_verified_payment($mysqli, $payload, $verification['data']);
if (!$save['ok']) {
    paystack_json(['success' => false, 'message' => $save['message']], 500);
}

paystack_json(['success' => true, 'message' => $save['message'], 'redirect' => 'receipts.php']);
