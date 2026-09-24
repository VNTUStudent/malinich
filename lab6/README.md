# Okean Elzy Tour — Survey Backend

PHP backend for the Okean Elzy tour site: visitor survey with a POST form,
dual JSON/SQLite storage, and a session-protected admin panel.

## Run

Local (PHP built-in server):

```bash
php -S 127.0.0.1:8091 -t site
```

Docker (Apache + php:8-apache):

```bash
docker compose up -d --build
```

Live: https://oe-survey.3.120.206.129.sslip.io (survey: `/survey.php`, admin: `/admin.php`)

## Structure

- `site/survey.php` — survey page: POST handler, validation, PRG redirect to a thank-you page
- `site/functions.php` — validation, JSON file + SQLite (PDO) storage, render helpers
- `site/config.php` — constants (DB path, admin login), `ADMIN_PASS` from environment
- `site/admin.php` — admin panel: login via `$_SESSION`, responses table, JSON export, delete
- `site/templates/` — shared header/footer, included via `require`
- `site/survey/`, `site/data/` — JSON responses and `survey.sqlite` (bind volumes in Docker)

## Notes

- Each response is saved both as a JSON file (`survey/Y-m-d_H-i-s_<rand>.json`) and to SQLite
- Admin password is provided via the `ADMIN_PASS` env variable (see `docker-compose.yml`)
