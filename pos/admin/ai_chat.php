<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('../includes/pos_ai_core.php');

pos_ai_load_env(dirname(__DIR__, 2));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    pos_ai_json(['error' => 'POST request required.'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    pos_ai_json(['error' => 'Invalid JSON request.'], 400);
}

$message = trim((string) ($input['message'] ?? ''));
if ($message === '') {
    pos_ai_json(['error' => 'Message is required.'], 422);
}

$history = pos_ai_history($input['history'] ?? []);
$messages = array_merge(
    [['role' => 'system', 'content' => pos_ai_context($mysqli)]],
    $history,
    [['role' => 'user', 'content' => substr($message, 0, 4000)]]
);

$response = pos_ai_call_provider($messages);

if (!$response['ok']) {
    pos_ai_json([
        'reply' => pos_ai_local_reply($mysqli, $message),
        'provider_error' => $response['message'],
    ]);
}

pos_ai_json(['reply' => $response['reply']]);
