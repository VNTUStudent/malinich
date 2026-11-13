<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/http.php';
require_once __DIR__ . '/lib/people.php';

$title = 'Виконавці туру — Океан Ельзи';
$teacher = ($_GET['source'] ?? '') === 'teacher';
$people = [];
$error = null;
$query = http_build_query(['user' => 'student', 'pass' => 'p@ssw0rd']);
$url = $teacher ? 'http://lab.vntu.org/api-server/lab8.php?' . $query
    : INTERNAL_API_BASE . '/data.json';
try {
    $people = collect_people(fetch_json($url));
    if ($people === []) {
        throw new UnexpectedValueException('Відповідь не містить записів про людей.');
    }
} catch (Throwable $exception) {
    error_log((string) $exception);
    $error = 'Не вдалося завантажити список виконавців. Спробуйте пізніше.';
}
require __DIR__ . '/templates/header.php';
?>
<h2>Виконавці туру</h2>
<p>Знайомтеся з музикантами та гуртами, які створюють атмосферу концертів.</p>
<div class="table-controls"><a class="btn" href="index.php">Оновити список</a></div>
<?php if ($error !== null): ?>
<div class="error-box" role="alert"><?= h($error) ?></div>
<?php endif; ?>
<p>Учасників: <strong><?= count($people) ?></strong>.</p>
<div class="table-wrap"><table class="data-table"><thead><tr>
    <th>№</th><th>Ім’я</th><th>Гурт</th><th>Роль</th><th>Місто</th>
</tr></thead><tbody>
<?php foreach ($people as $index => $person): ?>
<tr><td><?= $index + 1 ?></td><td><?= h($person->name) ?></td>
<td><?= h($person->affiliation ?? '—') ?></td>
<td><?= h($person->role ?? '—') ?></td><td><?= h($person->city ?? '—') ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/templates/footer.php'; ?>
