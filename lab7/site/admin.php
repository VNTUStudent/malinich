<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

session_start();

$title = 'Адміністрування анкети — Океан Ельзи';
$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (hash_equals(ADMIN_USER, $username) && hash_equals(ADMIN_PASS, $password)) {
        $_SESSION['admin'] = $username;
        $_SESSION['login_time'] = date('d.m.Y H:i:s');
    } else {
        $_SESSION['login_error'] = 'Невірний логін або пароль.';
    }
    header('Location: admin.php');
    exit;
}

if (empty($_SESSION['admin'])) {
    require __DIR__ . '/templates/header.php';
    render_login_form($_SESSION['login_error'] ?? null);
    unset($_SESSION['login_error']);
    require __DIR__ . '/templates/footer.php';
    exit;
}

if ($action === 'export') {
    export_responses_json();
}

if ($action === 'delete') {
    delete_response((int)($_POST['id'] ?? 0));
    header('Location: admin.php');
    exit;
}

require __DIR__ . '/templates/header.php';
render_admin_panel(load_responses());
require __DIR__ . '/templates/footer.php';
