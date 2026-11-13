<?php
declare(strict_types=1);

/** @return list<array<string, string>> */
function parse_response_files(): array
{
    if (!is_dir(SURVEY_DIR) || !is_readable(SURVEY_DIR)) {
        throw new RuntimeException('Каталог анкет недоступний.');
    }
    $paths = glob(SURVEY_DIR . '/*.json');
    if ($paths === false) {
        throw new RuntimeException('Не вдалося прочитати каталог анкет.');
    }
    rsort($paths, SORT_STRING);
    $responses = [];
    $fields = ['name', 'email', ...array_keys(survey_questions()), 'submitted_at'];
    foreach ($paths as $path) {
        $text = file_get_contents($path);
        if ($text === false) {
            throw new RuntimeException('Не вдалося прочитати анкету.');
        }
        $record = json_decode($text, false, 64, JSON_THROW_ON_ERROR);
        if (!$record instanceof stdClass) {
            throw new UnexpectedValueException('Некоректна структура анкети.');
        }
        $response = [];
        foreach ($fields as $field) {
            if (!isset($record->$field) || !is_string($record->$field)) {
                throw new UnexpectedValueException(
                    'Некоректне поле анкети: ' . $field
                );
            }
            $response[$field] = $record->$field;
        }
        $responses[] = $response;
    }
    return $responses;
}
