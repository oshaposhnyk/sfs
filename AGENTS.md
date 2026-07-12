# SecureFood School — agent guide

> This file is the entry point for coding agents (Codex, Cursor, etc.).
> It is kept in sync with `CLAUDE.md`; the detailed, always-current project
> knowledge lives in `.context/` — treat that directory as the source of truth.

Moodle 5.2.1 platform (public-root layout, code in `public/`). Current programme:
build the custom theme `theme_securefood` that reproduces the design prototypes in
`SecureFood School/` 1:1, with a Standard ⇄ SecureFood mode switch and full no-code
customisation (colours/texts/nav/blocks via theme settings). Learning plans are
owned by `public/local/learningplans` (hexagonal DDD); `public/enrol/learningplan`
is its enrolment adapter.

## Required reading order

1. `.context/README.md` — how to work with the context (status marks, update duties)
2. `.context/PROJECT.md` — stack, key paths, what exists
3. `.context/ROADMAP.md` — phases; `.context/PROGRESS.md` — live status board
4. `.context/conventions/` — architecture, Moodle, frontend, testing rules (binding)
5. `.context/domains/NN-*/CONTEXT.md` — pick your task here
6. `.context/design/` — tokens, BEM component map, prototype→page mapping
7. `.context/DECISIONS.md` — ADRs; never contradict an accepted ADR silently

**You must update `.context/PROGRESS.md` and the domain `CONTEXT.md` when you start
and finish work.** Record new architectural decisions as ADRs.

## Hard rules

- Hexagonal layering (see `conventions/architecture.md`): domain code never touches
  `$DB`/`$CFG`/`$USER`/`$OUTPUT`/`$PAGE`; only infrastructure adapters do. Follow
  `local_learningplans` as the reference implementation.
- `declare(strict_types=1);` in every new PHP file. No Moodle core modifications.
- Security: `require_login()`, `require_capability()`, sesskey on mutations,
  `PARAM_*` validation, escaped output — every endpoint.
- Frontend: BEM classes prefixed `sfs-`, design tokens only (no raw hex outside
  `_tokens.scss`), no `!important`, no jQuery, no inline JS/CSS, no CDNs
  (fonts/icons self-hosted). WCAG 2.1 AA is mandatory.
- Strings via `get_string()`; update `lang/en` **and** `lang/uk` together.
- Git: never commit/push/merge/reset without explicit owner approval. Never
  `rm -rf` — move files to `~/.Trash/` instead. Ask before touching `db/`,
  `lib.php`, capabilities, auth, cron, Docker.
- Prototypes in `SecureFood School/` are visual reference only — never copy their
  classes/markup verbatim, never treat their mock data as real.

## Commands

```bash
docker compose up -d                       # nginx :8080, php-fpm 8.3, mysql, redis
docker compose exec php-fpm php public/admin/cli/purge_caches.php
php -l <file>                              # lint every touched PHP file
vendor/bin/phpcs --standard=phpcs.xml.dist <path>
docker compose exec php-fpm vendor/bin/phpunit public/local/learningplans/tests
```

Definition of done: `.context/conventions/testing.md`. Don't claim done without
running lint + affected tests and updating `PROGRESS.md`.
