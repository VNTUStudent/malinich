<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$values = [];
$saved = null;
$title = 'Анкета відвідувача — Океан Ельзи';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = $_POST;
    $errors = validate_response($_POST);
    if (!$errors) {
        $response = build_response($_POST);
        $filename = save_response_file($response);
        save_response_db($response, $filename);
        header('Location: survey.php?saved=' . urlencode($filename));
        exit;
    }
}

if (isset($_GET['saved'])) {
    $saved = find_response_by_filename(basename($_GET['saved']));
}

require __DIR__ . '/templates/header.php';
if ($saved) {
    render_thank_you($saved);
} else {
    render_survey_form($errors, $values);
}
require __DIR__ . '/templates/footer.php';
