<?php

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function survey_questions()
{
    return [
        'album' => [
            'label' => 'Ваш улюблений альбом гурту?',
            'type' => 'radio',
            'options' => [
                'Там, де нас нема (1998)',
                'Модель (2001)',
                'Gloria (2005)',
                'Земля (2013)',
                'Без меж (2016)',
            ],
        ],
        'source' => [
            'label' => 'Як ви дізналися про цей сайт?',
            'type' => 'select',
            'options' => [
                'Пошукова система',
                'Соціальні мережі',
                'Порада друзів',
                'Інші джерела',
            ],
        ],
        'rating' => [
            'label' => 'Оцініть сайт за п’ятибальною шкалою:',
            'type' => 'radio',
            'options' => ['5', '4', '3', '2', '1'],
        ],
        'concert' => [
            'label' => 'Чи плануєте відвідати концерт туру 2026?',
            'type' => 'radio',
            'options' => [
                'Так, вже купив квиток',
                'Так, планую купити',
                'Ще не вирішив',
                'Ні, не планую',
            ],
        ],
        'suggestions' => [
            'label' => 'Ваші побажання та пропозиції щодо сайту:',
            'type' => 'textarea',
        ],
    ];
}

function validate_response($post)
{
    $errors = [];
    $name = trim($post['name'] ?? '');
    $email = trim($post['email'] ?? '');

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = 'Вкажіть ім’я респондента (від 2 до 100 символів).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Вкажіть коректну адресу електронної пошти.';
    }
    foreach (survey_questions() as $key => $question) {
        $answer = trim($post[$key] ?? '');
        if ($question['type'] === 'textarea') {
            if (mb_strlen($answer) > 1000) {
                $errors[] = 'Поле «' . $question['label'] . '» не може перевищувати 1000 символів.';
            }
            continue;
        }
        if (!in_array($answer, $question['options'], true)) {
            $errors[] = 'Оберіть відповідь на питання: «' . $question['label'] . '».';
        }
    }
    return $errors;
}

function build_response($post)
{
    $response = [
        'name' => trim($post['name']),
        'email' => trim($post['email']),
        'submitted_at' => date('d.m.Y H:i:s'),
    ];
    foreach (survey_questions() as $key => $question) {
        $response[$key] = trim($post[$key] ?? '');
    }
    return $response;
}

function save_response_file($response)
{
    $filename = date('Y-m-d_H-i-s') . '_' . bin2hex(random_bytes(3)) . '.json';
    file_put_contents(
        SURVEY_DIR . '/' . $filename,
        json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
    return $filename;
}

function db()
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('CREATE TABLE IF NOT EXISTS responses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            album TEXT NOT NULL,
            source TEXT NOT NULL,
            rating TEXT NOT NULL,
            concert TEXT NOT NULL,
            suggestions TEXT,
            submitted_at TEXT NOT NULL,
            filename TEXT NOT NULL
        )');
    }
    return $pdo;
}

function save_response_db($response, $filename)
{
    $stmt = db()->prepare(
        'INSERT INTO responses (name, email, album, source, rating, concert, suggestions, submitted_at, filename)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $response['name'],
        $response['email'],
        $response['album'],
        $response['source'],
        $response['rating'],
        $response['concert'],
        $response['suggestions'],
        $response['submitted_at'],
        $filename,
    ]);
    return (int)db()->lastInsertId();
}

function load_responses()
{
    return db()->query('SELECT * FROM responses ORDER BY id DESC')->fetchAll();
}

function find_response_by_filename($filename)
{
    $stmt = db()->prepare('SELECT * FROM responses WHERE filename = ?');
    $stmt->execute([$filename]);
    return $stmt->fetch() ?: null;
}

function delete_response($id)
{
    $stmt = db()->prepare('SELECT filename FROM responses WHERE id = ?');
    $stmt->execute([$id]);
    $filename = $stmt->fetchColumn();
    if ($filename === false) {
        return false;
    }
    $path = SURVEY_DIR . '/' . $filename;
    if (is_file($path)) {
        unlink($path);
    }
    db()->prepare('DELETE FROM responses WHERE id = ?')->execute([$id]);
    return true;
}

function export_responses_json()
{
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="survey-export-' . date('Y-m-d_H-i-s') . '.json"');
    echo json_encode(load_responses(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function render_survey_form($errors, $values)
{
    if ($errors) {
        echo '<div class="error-box"><strong>Будь ласка, виправте такі помилки:</strong><ul>';
        foreach ($errors as $error) {
            echo '<li>' . h($error) . '</li>';
        }
        echo '</ul></div>';
    }
    echo '<form class="survey-form" method="post" action="survey.php">';
    echo '<div class="field"><label for="name">Ім’я респондента:</label>';
    echo '<input type="text" id="name" name="name" maxlength="100" required value="' . h($values['name'] ?? '') . '"></div>';
    echo '<div class="field"><label for="email">Email респондента:</label>';
    echo '<input type="email" id="email" name="email" maxlength="100" required value="' . h($values['email'] ?? '') . '"></div>';
    foreach (survey_questions() as $key => $question) {
        echo '<fieldset><legend>' . h($question['label']) . '</legend>';
        if ($question['type'] === 'radio') {
            foreach ($question['options'] as $option) {
                $checked = ($values[$key] ?? '') === $option ? ' checked' : '';
                echo '<label class="option"><input type="radio" name="' . $key . '" value="' . h($option) . '"' . $checked . ' required> ' . h($option) . '</label>';
            }
        } elseif ($question['type'] === 'select') {
            echo '<select name="' . $key . '" required><option value="">— оберіть відповідь —</option>';
            foreach ($question['options'] as $option) {
                $selected = ($values[$key] ?? '') === $option ? ' selected' : '';
                echo '<option value="' . h($option) . '"' . $selected . '>' . h($option) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<textarea name="' . $key . '" maxlength="1000" rows="5" cols="60">' . h($values[$key] ?? '') . '</textarea>';
        }
        echo '</fieldset>';
    }
    echo '<button class="btn" type="submit">Надіслати анкету</button>';
    echo '</form>';
}

function render_thank_you($response)
{
    echo '<div class="success-box">';
    echo '<h3>Дякуємо за участь в опитуванні, ' . h($response['name']) . '!</h3>';
    echo '<p>Вашу відповідь збережено у текстовому файлі <code>' . h($response['filename']) . '</code> у папці сайту <code>survey/</code> та додано до бази даних.</p>';
    echo '<p><strong>Час та дата заповнення форми:</strong> ' . h($response['submitted_at']) . '</p>';
    echo '</div>';
    echo '<p><a class="btn" href="index.html">На головну сторінку</a></p>';
}

function render_login_form($error)
{
    echo '<h2>Вхід адміністратора</h2>';
    echo '<p>Сторінка адміністрування анкети доступна лише після введення логіна та пароля.</p>';
    if ($error) {
        echo '<div class="error-box">' . h($error) . '</div>';
    }
    echo '<form class="survey-form login-form" method="post" action="admin.php">';
    echo '<input type="hidden" name="action" value="login">';
    echo '<div class="field"><label for="username">Логін:</label>';
    echo '<input type="text" id="username" name="username" maxlength="50" required></div>';
    echo '<div class="field"><label for="password">Пароль:</label>';
    echo '<input type="password" id="password" name="password" required></div>';
    echo '<button class="btn" type="submit">Увійти</button>';
    echo '</form>';
}

function render_admin_panel($responses)
{
    echo '<h2>Панель адміністратора — відповіді на анкету</h2>';
    echo '<div class="admin-bar">';
    echo '<span>Адміністратор: <strong>' . h($_SESSION['admin']) . '</strong></span>';
    echo '<span>Вхід: <strong>' . h($_SESSION['login_time']) . '</strong> (зафіксовано у $_SESSION)</span>';
    echo '<form method="post" action="admin.php"><input type="hidden" name="action" value="export">';
    echo '<button class="btn" type="submit">Експортувати у JSON</button></form>';
    echo '<a class="btn btn-danger" href="logout.php">Вийти</a>';
    echo '</div>';
    echo '<p>Усього відповідей: <strong>' . count($responses) . '</strong>. Кожна відповідь також збережена окремим текстовим файлом у папці <code>survey/</code>.</p>';
    echo '<table class="data-table admin-table"><thead><tr>';
    echo '<th>№</th><th>Дата</th><th>Ім’я</th><th>Email</th><th>Улюблений альбом</th><th>Джерело</th><th>Оцінка</th><th>Концерт</th><th>Побажання</th><th>Файл</th><th></th>';
    echo '</tr></thead><tbody>';
    foreach ($responses as $response) {
        echo '<tr>';
        echo '<td>' . (int)$response['id'] . '</td>';
        echo '<td>' . h($response['submitted_at']) . '</td>';
        echo '<td>' . h($response['name']) . '</td>';
        echo '<td>' . h($response['email']) . '</td>';
        echo '<td>' . h($response['album']) . '</td>';
        echo '<td>' . h($response['source']) . '</td>';
        echo '<td>' . h($response['rating']) . '</td>';
        echo '<td>' . h($response['concert']) . '</td>';
        echo '<td>' . h($response['suggestions']) . '</td>';
        echo '<td><code>' . h($response['filename']) . '</code></td>';
        echo '<td><form method="post" action="admin.php" onsubmit="return confirm(\'Видалити цю відповідь?\')">';
        echo '<input type="hidden" name="action" value="delete">';
        echo '<input type="hidden" name="id" value="' . (int)$response['id'] . '">';
        echo '<button class="btn btn-danger" type="submit">Видалити</button></form></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
