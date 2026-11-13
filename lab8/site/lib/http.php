<?php
declare(strict_types=1);

function fetch_json(string $url, ?string $authorization = null): mixed
{
    $headers = "Accept: application/json\r\n";
    if ($authorization !== null) {
        $headers .= 'Authorization: Basic ' . $authorization . "\r\n";
    }
    $context = stream_context_create(['http' => [
        'method' => 'GET', 'header' => $headers, 'timeout' => 5,
        'ignore_errors' => true, 'follow_location' => 0,
    ]]);
    set_error_handler(static function (int $severity, string $message): never {
        throw new RuntimeException($message);
    });
    try {
        $body = file_get_contents($url, false, $context, 0, 2097153);
        $headers = http_get_last_response_headers() ?? [];
    } finally {
        restore_error_handler();
    }
    if ($body === false || strlen($body) > 2097152) {
        throw new RuntimeException('Відповідь відсутня або перевищує 2 МіБ.');
    }
    if (!preg_match('/^HTTP\/\S+ (\d{3})/', $headers[0] ?? '', $matches)) {
        throw new RuntimeException('Не отримано HTTP-статус відповіді.');
    }
    if ($matches[1] !== '200') {
        throw new RuntimeException('Сервер повернув HTTP ' . $matches[1] . '.');
    }
    return json_decode($body, false, 64, JSON_THROW_ON_ERROR);
}
