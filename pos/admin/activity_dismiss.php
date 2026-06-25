<?php
session_start();
include('config/checklogin.php');
check_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'POST request required.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
    exit;
}

$ids = $payload['ids'] ?? [];
if (isset($payload['id'])) {
    $ids[] = $payload['id'];
}

$ids = array_values(array_filter(array_map('strval', $ids)));
$_SESSION['dismissed_team_activities'] = array_values(array_unique(array_merge(
    $_SESSION['dismissed_team_activities'] ?? [],
    $ids
)));

echo json_encode(['success' => true]);
