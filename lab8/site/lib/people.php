<?php
declare(strict_types=1);

/** @return list<stdClass> */
function collect_people(mixed $node, int $depth = 0): array
{
    if ($depth > 32) {
        throw new UnexpectedValueException('Завелика вкладеність даних.');
    }
    if ($node instanceof stdClass && property_exists($node, 'name')) {
        if (!is_string($node->name) || trim($node->name) === '') {
            throw new UnexpectedValueException('Некоректне ім’я у записі.');
        }
        foreach (['affiliation', 'role', 'city'] as $field) {
            if (isset($node->$field) && !is_string($node->$field)) {
                throw new UnexpectedValueException('Некоректне поле ' . $field);
            }
        }
        return [$node];
    }
    if (!is_array($node) && !$node instanceof stdClass) {
        return [];
    }
    $people = [];
    foreach ($node as $child) {
        foreach (collect_people($child, $depth + 1) as $person) {
            $people[] = $person;
        }
    }
    return $people;
}
