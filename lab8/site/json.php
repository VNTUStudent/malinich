<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/responses.php';

$demo = ($_GET['demo'] ?? '') === '1';
if (!$demo) {
    require_export_access();
}
header('Content-Type: application/json; charset=utf-8');
try {
    $responses = $demo
        ? json_decode(file_get_contents(__DIR__ . '/fixtures/responses.json'),
            true, 64, JSON_THROW_ON_ERROR)
        : parse_response_files();
    echo json_encode($responses,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    error_log((string) $error);
    http_response_code(500);
    echo json_encode(['error' => 'Не вдалося прочитати анкети.'],
        JSON_UNESCAPED_UNICODE);
}
