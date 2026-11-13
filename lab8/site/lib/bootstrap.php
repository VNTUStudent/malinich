<?php
declare(strict_types=1);

require_once getenv('LAB7_FUNCTIONS') ?: dirname(__DIR__, 3) . '/lab7/site/functions.php';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
define('SURVEY_DIR', getenv('SURVEY_DIR') ?: '/var/lib/lab-survey');
define('INTERNAL_API_BASE', getenv('INTERNAL_API_BASE') ?: 'http://127.0.0.1');
