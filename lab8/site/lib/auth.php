<?php
declare(strict_types=1);

function require_export_access(): string
{
    $user = getenv('ADMIN_USER') ?: '';
    $password = getenv('ADMIN_PASS') ?: '';
    if ($user === '' || $password === '') {
        http_response_code(503);
        exit('Перегляд відповідей тимчасово недоступний.');
    }
    $providedUser = $_SERVER['PHP_AUTH_USER'] ?? '';
    $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? '';
    if (!hash_equals($user, $providedUser)
        || !hash_equals($password, $providedPassword)) {
        header('WWW-Authenticate: Basic realm="Survey export", charset="UTF-8"');
        http_response_code(401);
        exit('Для перегляду анкет потрібна авторизація.');
    }
    return base64_encode($providedUser . ':' . $providedPassword);
}
