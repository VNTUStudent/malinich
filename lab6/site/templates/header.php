<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Сайт туру «Океан Ельзи»') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="header">
        <h1>Океан Ельзи — Всеукраїнський тур 2026</h1>
        <p>Офіційний сайт всеукраїнського туру легендарного українського гурту.
        Незабутня атмосфера, улюблені хіти та нова програма!</p>
    </div>
    <nav>
        <ul class="menu">
            <li><a href="index.html">Головна</a></li>
            <li><a href="about.html">Про гурт</a></li>
            <li><a href="tour.html">Тур 2026</a></li>
            <li><a href="media.html">Медіа</a></li>
            <li><a href="survey.php">Анкета</a></li>
            <li><a href="contacts.html">Контакти</a></li>
        </ul>
    </nav>
    <div id="container">
        <div id="sidebar">
            <h2>Розділи сайту</h2>
            <ul>
                <li><a href="about.html">Про гурт</a> — історія та склад
                    колективу.</li>
                <li><a href="tour.html">Тур 2026</a> — дати, міста та
                    програма концертів.</li>
                <li><a href="media.html">Медіа</a> — дискографія та відео.</li>
                <li><a href="survey.php">Анкета</a> — опитування відвідувачів
                    сайту.</li>
                <li><a href="contacts.html">Контакти</a> — організатори туру
                    та зв'язок.</li>
            </ul>
        </div>
        <div id="content">
