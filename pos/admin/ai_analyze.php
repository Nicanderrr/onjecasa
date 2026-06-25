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

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    pos_ai_json(['error' => 'Upload a valid file for analysis.'], 422);
}

$file = $_FILES['file'];
$prompt = trim((string) ($_POST['prompt'] ?? 'Analyze this file for POS operations. Summarize useful findings and recommend action.'));
$maxBytes = 10 * 1024 * 1024;

if ((int) $file['size'] > $maxBytes) {
    pos_ai_json(['error' => 'File is too large. Maximum size is 10MB.'], 422);
}

$fileName = $file['name'] ?? 'uploaded file';
$tmpPath = $file['tmp_name'];
$mimeType = mime_content_type($tmpPath) ?: ($file['type'] ?? 'application/octet-stream');
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) || str_starts_with($mimeType, 'image/');

if ($isImage) {
    $base64 = base64_encode(file_get_contents($tmpPath));
    $messages = [
        [
            'role' => 'system',
            'content' => pos_ai_context($mysqli),
        ],
        [
            'role' => 'user',
            'content' => [
                ['type' => 'text', 'text' => $prompt],
                [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => 'data:' . $mimeType . ';base64,' . $base64,
                    ],
                ],
            ],
        ],
    ];

    $response = pos_ai_call_provider($messages, null, true);
    if (!$response['ok']) {
        pos_ai_json(['error' => $response['message']], $response['status']);
    }

    pos_ai_json(['reply' => $response['reply']]);
}

$textExtensions = ['txt', 'csv', 'json', 'log', 'md'];
if (in_array($extension, $textExtensions, true)) {
    $contents = file_get_contents($tmpPath);
    $contents = substr((string) $contents, 0, 12000);

    $messages = [
        ['role' => 'system', 'content' => pos_ai_context($mysqli)],
        ['role' => 'user', 'content' => $prompt . "\n\nFile name: {$fileName}\n\nFile contents:\n" . $contents],
    ];

    $response = pos_ai_call_provider($messages);
    if (!$response['ok']) {
        pos_ai_json(['error' => $response['message']], $response['status']);
    }

    pos_ai_json(['reply' => $response['reply']]);
}

pos_ai_json([
    'reply' => 'I received "' . $fileName . '". Image analysis is supported for JPG, PNG, and WEBP. Text analysis is supported for TXT, CSV, JSON, LOG, and MD files. PDF/DOC extraction is not enabled in this plain PHP build yet.',
]);
