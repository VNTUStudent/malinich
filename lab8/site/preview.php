<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/http.php';

$demo = ($_GET['demo'] ?? '') === '1';
$authorization = $demo ? null : require_export_access();
$title = 'Відповіді відвідувачів — Океан Ельзи';
$responses = [];
$error = null;
try {
    $responses = fetch_json(INTERNAL_API_BASE . '/json.php' . ($demo ? '?demo=1' : ''),
        $authorization);
    if (!is_array($responses)) {
        throw new UnexpectedValueException('Очікується масив анкет.');
    }
    foreach ($responses as $response) {
        if (!$response instanceof stdClass) {
            throw new UnexpectedValueException('Некоректна анкета.');
        }
        foreach (['name', 'email', ...array_keys(survey_questions()), 'submitted_at'] as $key) {
            if (!isset($response->$key) || !is_string($response->$key)) {
                throw new UnexpectedValueException('Некоректне поле анкети.');
            }
        }
    }
} catch (Throwable $exception) {
    error_log((string) $exception);
    $responses = [];
    $error = 'Не вдалося завантажити відповіді. Спробуйте пізніше.';
    http_response_code(502);
}
require __DIR__ . '/templates/header.php';
?>
<h2>Відповіді відвідувачів</h2>
<?php if ($demo): ?><p class="info-box">Приклад заповнених анкет.</p><?php endif; ?>
<p><a class="btn" download="survey-responses.json" href="json.php<?= $demo ? '?demo=1' : '' ?>">Завантажити відповіді</a></p>
<?php if ($error !== null): ?>
<div class="error-box" role="alert"><?= h($error) ?></div>
<?php endif; ?>
<p>Кількість анкет: <strong><?= count($responses) ?></strong>.</p>
<?php if (!$responses && $error === null): ?>
<p>Збережених анкет поки немає.</p>
<?php endif; ?>
<div class="table-wrap"><table class="data-table admin-table"><thead><tr>
<th>Ім’я</th><th>Email</th><th>Відповіді на питання</th><th>Дата</th>
</tr></thead><tbody>
<?php foreach ($responses as $response): ?><tr>
<td><?= h($response->name) ?></td><td><?= h($response->email) ?></td>
<td><?php foreach (survey_questions() as $key => $question): ?>
<p><strong><?= h($question['label']) ?></strong><br>
<?= h($response->$key) ?></p><?php endforeach; ?></td>
<td><?= h($response->submitted_at) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/templates/footer.php'; ?>
