<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($action === 'survey') {
    $errors = validate_response($_POST);
    if ($errors) {
        echo json_encode(
            ['ok' => false, 'errors' => $errors],
            JSON_UNESCAPED_UNICODE
        );
        exit;
    }
    $response = build_response($_POST);
    $filename = save_response_file($response);
    save_response_db($response, $filename);
    echo json_encode(
        ['ok' => true, 'response' => $response, 'filename' => $filename],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

http_response_code(400);
echo json_encode(
    ['ok' => false, 'errors' => ['Невідома дія запиту.']],
    JSON_UNESCAPED_UNICODE
);
