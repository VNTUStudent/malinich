# Okean Elzy — REST API and Structured Data

Lab 8: PHP HTTP client, grouped JSON flattened into a people table, and all
three survey-export bonuses. Visual styles and the existing `h()` and
`survey_questions()` helpers come from lab 7; lab 7 files are unchanged.

Live: https://lab8.depscian.com

Public pages retain the tour website presentation. Assignment details, teacher
instructions and API implementation notes belong in this README and the report,
not the visitor interface. Informational tour pages are local copies of lab 7;
the survey link opens the existing survey without replacing its deployment.

- `/index.php`: fetches static `data.json` over HTTP and combines 12 synthetic
  people from four groups into one array.
- `/index.php?source=teacher`: retains the original teacher API request with
  the assigned GET credentials.
- `/json.php`: password-protected JSON array built by parsing survey text files.
- `/preview.php`: password-protected HTTP client of `json.php`, rendering HTML.
- `/json.php?demo=1`, `/preview.php?demo=1`: public synthetic survey examples.

The teacher instructed the class on 2026-11-06 at 13:32 to generate a separate
JSON file with generative AI and use static JSON instead of `lab.vntu.org`,
whose operation was suspended indefinitely. The user supplied this instruction
for lab 8. Static JSON is therefore the default supported source, not a fallback
that requires an unavailable service. The optional original API request returned
HTTP 502 on 2026-11-13. The recursive collector accepts nested groups of objects
with a `name` field; affiliation, role and city are optional string columns.

## Local development

From this directory, use PHP 8.4+ and keep the sibling lab 7 checkout available:

```sh
mkdir -p runtime/survey
export SURVEY_DIR="$PWD/runtime/survey"
export INTERNAL_API_BASE=http://127.0.0.1:8093
export ADMIN_USER=admin
read -rs ADMIN_PASS
export ADMIN_PASS
PHP_CLI_SERVER_WORKERS=4 php -d opcache.enable=0 -S 127.0.0.1:8093 -t site
```

Multiple CLI workers are required: preview.php and index.php call
another endpoint on the same HTTP server. Apache handles concurrent requests.
Use synthetic survey JSON files in the runtime directory for local experiments;
empty storage exports `[]`. Malformed files fail the export and log an error.

## Deployment

Docker Compose uses the parent teacher repository as its build context, copying
only lab 8's site and the existing lab 7 helper file. Create the external Docker
network `web`, configure `.env` with `ADMIN_USER`, `ADMIN_PASS`, and optionally
`SURVEY_SOURCE`, then run `docker compose up -d --build`.

Current deployment: `oci:~/apps/oe-rest/lab8`, container `okean-elzy-rest`, Caddy
host `lab8.depscian.com`. The runtime survey directory is mounted read-only from
`/home/ubuntu/apps/oe-survey/runtime/survey`. JSON text files are authoritative
for this export; SQLite is not copied or queried. Each request recomputes its
projection from the files. Real responses require HTTP Basic authentication on
both endpoints. The password is supplied through the private host `.env`.

Lab 7 remains at https://survey.depscian.com in its original container. Deployment
of lab 8 does not rebuild, restart, or overwrite it.

## Structure

`site/lib/` owns HTTP/JSON decoding, nested people collection, file parsing and
authorization. `site/data.json` is the static grouped people dataset.
`site/templates/` renders the shared shell. `site/fixtures/`
contains explicitly synthetic examples and is blocked from direct HTTP access
by Apache. All real survey data stays outside the document root and repository.
Report generators, screenshots, DOCX and PDF outputs are excluded from Git.
