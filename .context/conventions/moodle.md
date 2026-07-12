# Conventions — Moodle 5.2 specifics

Target: **Moodle 5.2.1+** (`$version = 2026042001.05`), PHP 8.3, public-root layout
(code under `public/`).

## Plugin/theme hygiene

- Standard component structure; `version.php` with `$plugin->component`,
  `$plugin->version` (YYYYMMDDXX), `$plugin->requires`, `$plugin->supported`.
- Bump the plugin version on every `db/` change and pair it with a guarded
  `db/upgrade.php` step (savepoints!).
- Language strings: `lang/en/` is canonical; keep `lang/uk/` in sync in the same
  commit. Never machine-translate silently — mark uncertain translations for review.
- Capabilities in `db/access.php` with correct `contextlevel`, `captype`,
  `riskbitmask`, and archetype defaults. Permission checks only via the
  `permission_checker_interface` port (plugin) or `has_capability()` in theme
  presentation code.
- Privacy API provider for anything storing personal data (preferences included —
  the theme must declare its user preferences in a `privacy\provider`).

## Security checklist (every endpoint / external function)

1. `require_login()` (+ `require_course_login()` where relevant).
2. Correct context resolution (`context_system`, `context_course`) and
   `$PAGE->set_context()`.
3. `require_capability()` before any read of protected data and any mutation.
4. `require_sesskey()` for state-changing GET/POST; web services validate params via
   `external_function_parameters` and return via declared structures.
5. `required_param()`/`optional_param()` with strict `PARAM_*` types — never raw
   `$_GET`/`$_POST`.
6. Output escaping: `format_string()`/`format_text()` for user content; `s()` where
   needed; Mustache auto-escaping (`{{var}}`, never `{{{var}}}` for user data).
7. DML API only — `$DB->get_records_sql()` with placeholders when SQL is unavoidable;
   prefer higher-level APIs (completion, enrol, badges) over table access.
8. Never leak other users' progress/PII; learners see only their own plan data.

## APIs to use (not reinvent)

| Need                | API |
|---------------------|-----|
| Completion/progress | `completion_info`, core completion API (already wrapped by `completion_reader_interface`) |
| Enrolment           | enrol plugin API (`enrol_plugin` base class) — see domain 10 |
| Cohorts             | `cohort_*` functions (wrapped by `cohort_reader_interface`) |
| Preferences         | `get_user_preferences()` / `set_user_preference()`; client: `core_user_set_user_preferences` WS |
| AJAX                | External Services API + `core/ajax` AMD — never bare `fetch` to ad-hoc scripts |
| Events              | Events API (`\core\event\base` subclasses) — pattern in `classes/event/` |
| Background work     | Task API (`db/tasks.php` scheduled tasks; ad-hoc tasks for fan-out) |
| Caching             | Cache API (MUC) — definitions in `db/caches.php` (progress cache exists) |
| Files               | File API + `pluginfile.php` handlers; never direct paths |
| Badges              | core badges API (Future Food achievements) |
| Templates           | Output API: renderables + `$OUTPUT->render_from_template()` |

## Theme-specific (theme_securefood)

- Child of Boost: `$THEME->parents = ['boost'];`, inherit its SCSS via
  `theme_securefood_get_main_scss_content()` composing pre/post SCSS.
- Layout files under `theme/securefood/layout/`; templates under `templates/`;
  override Boost templates only when structurally necessary (document each override
  in the domain CONTEXT.md — overrides are an upgrade-cost hotspot).
- AMD modules in `amd/src/`, built to `amd/build/` (grunt). ES modules per Moodle 5 style,
  `core/ajax`, `core/notification`, `core/pubsub` — **no jQuery**.
- SCSS lives in `scss/`; compiled by Moodle's SCSS pipeline. No CSS files, no inline styles.
- Icons: self-hosted Material Icons font for the custom mode (ADR-006); do not
  replace Moodle's icon system in standard mode.
- After changing SCSS/templates/AMD: purge caches (`php admin/cli/purge_caches.php`
  inside the php-fpm container).

## Local environment

- `docker compose up -d` (nginx :8080, php-fpm, mysql, redis).
- CLI inside container: `docker compose exec php-fpm php public/admin/cli/purge_caches.php`.
- PHPUnit: `docker compose exec php-fpm vendor/bin/phpunit --testsuite …` (see
  `conventions/testing.md`).
