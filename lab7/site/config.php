<?php

define('ADMIN_USER', getenv('ADMIN_USER') ?: 'admin');
define('ADMIN_PASS', getenv('ADMIN_PASS') ?: 'oe-admin-2026');
define('DB_PATH', __DIR__ . '/data/survey.sqlite');
define('SURVEY_DIR', __DIR__ . '/survey');
