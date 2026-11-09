# Okean Elzy Tour — AJAX Data Updates

Lab 7: dynamic data updates on top of the lab 6 PHP survey. A jokes page
fetches a text file via `XMLHttpRequest`, the survey form submits through a
jQuery AJAX endpoint that answers in JSON, and a bonus page renders a dynamic
table from a static JSON file (generated with AI per teacher's instruction,
since `lab.vntu.org` is offline) with a refresh button and `name` /
`affiliation` sorting.

## Run

Local (PHP built-in server):

```bash
php -S 127.0.0.1:8091 -t site
```

Docker (Apache + php:8-apache):

```bash
docker compose up -d --build
```

Live: https://oe-survey.3.120.206.129.sslip.io
(jokes: `/jokes.html`, table: `/table.html`, survey: `/survey.php`)

## Structure

- `site/jokes.html` — random joke loaded from `site/jokes.txt` via `XMLHttpRequest`
- `site/api.php` — AJAX endpoint: validates and saves the survey, returns JSON
- `site/survey.js` — intercepts form submit, posts via jQuery `$.post`, renders errors/thanks without reload
- `site/table.html` — dynamic table: `$.getJSON('data.json')`, refresh Button, sort by `name` / `affiliation`
- `site/data.json` — static dataset (replaces the offline `lab.vntu.org` API)
- `site/survey.php`, `site/functions.php` — lab 6 survey with the non-JS POST fallback kept intact

## Notes

- The survey keeps the lab 6 PRG fallback for browsers without JavaScript
- `novalidate` is set from JS only, so native validation still guards the non-JS path
- Survey responses go to the same JSON files and SQLite DB as in lab 6
