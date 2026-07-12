# PROJECT — SecureFood School

## What we are building

A Moodle-based learning platform for the SecureFood project (food-systems education).
The deliverable of the current programme of work is a **custom Moodle theme
(`theme_securefood`) that reproduces the design prototypes 1:1**, plus the supporting
plugin work needed to feed it with real data.

The only intended divergence from the prototypes: the theme ships a **mode switch**
between *Standard mode* (stock Boost look) and *SecureFood mode* (the custom design).

Second core requirement (ADR-007): **maximum no-code customisation** — colours,
texts, navigation, blocks and page content are all admin-configurable through
theme settings; the prototype design is only the default.

Learning-plan functionality is owned by the existing `local_learningplans` plugin;
the theme consumes it — it never re-implements plan logic.

## Stack (verified)

| Item            | Value                                                        |
|-----------------|--------------------------------------------------------------|
| LMS             | Moodle 5.2.1+ (Build 20260708), version `2026042001.05`      |
| PHP             | 8.3 (Docker `php-fpm`)                                       |
| Infrastructure  | Docker Compose: nginx, php-fpm, mysql, redis (`docker-compose.yml`) |
| Web root        | `public/` (Moodle 5.x public-root layout)                    |
| Base theme      | Boost (Bootstrap 5) — `public/theme/boost`                   |
| Existing themes | boost, classic, `theme_securefood` v0.1.0 (MATURITY_ALPHA)   |

## Key paths

| Path                                  | What it is                                        |
|---------------------------------------|---------------------------------------------------|
| `SecureFood School/`                  | Design prototypes (static HTML/CSS/JS + assets). Reference only — never production code. |
| `SecureFood School/styles.css`        | Full design system: tokens, light/dark palettes, all components (1 414 lines) |
| `SecureFood School/js/shell.js`       | Prototype app shell: sidebar, topbar, theme toggle, sidebar collapse |
| `SecureFood School/assets/`           | Logos (full/icon × light/dark, SVG + PNG)         |
| `SecureFood School/*.html`            | Page prototypes — see `design/page-mapping.md`    |
| `public/local/learningplans/`         | Learning Plans plugin v0.2.0 — hexagonal DDD architecture, working |
| `public/theme/securefood/`            | Custom Boost-child theme; implemented, audit gaps remain |
| `public/enrol/learningplan/`          | Enrol adapter v0.1.0 (added 2026-07-12) — tests/E2E pending, see domain 10 |
| `securefood-school-ai-agent-prompt.txt` | Original product brief — requirements source of truth |

## Existing plugin: local_learningplans (v0.2.0)

Already implemented with the exact architecture we must continue to follow:

- `classes/domain/` — entities (`learning_plan`, `learning_plan_course`,
  `learning_plan_membership`), value objects (`progress`, `enrolment_mode`,
  `membership_source`, `membership_status`), progress policy. No Moodle globals.
- `classes/application/` — `learning_plan_service` + 17 explicit use cases;
  `port/` holds 15 interfaces (repositories, permission checker, clock, transactions,
  event dispatcher, URL resolver, enrolment service, completion reader, cohort reader).
- `classes/infrastructure/` — Moodle adapters (`persistence/`, `moodle/…`), thin
  page controllers, service factory.
- Presentation: `index.php`, `view.php`, `edit.php`, `my.php`, `enrol.php`,
  `cohorts.php` + Mustache templates + one AMD module (`view_reorder`).
- DB tables: `local_learningplans_plan`, `_crs`, `_mem`, `_coh`, `_enrl`, `_prog`.
- Events, observers, scheduled tasks, Privacy API, PHPUnit + Behat tests, `en`/`uk` langs.

**Enrolment dependency:** `moodle_learning_plan_enrolment_service` calls
`enrol_get_plugin('learningplan')`. The `enrol_learningplan` adapter (v0.1.0) was
added on 2026-07-12 and matches the `customint1` contract, but has no PHPUnit
tests and the enrol round-trip has not been verified end-to-end yet — tracked as
domain `10-enrol-learningplan`.

## Product navigation (from prototypes)

| Nav item              | Prototype          | Purpose                                        |
|-----------------------|--------------------|------------------------------------------------|
| About the Project     | `insights.html`    | Landing/dashboard: KPIs, hero, Living Labs map, feed |
| Student Lab           | `l4c.html`         | Learner's learning-plan dashboard (plan bar, stages, course tiles) |
| Future Food           | `ffs.html`         | Gamified missions, XP, achievements (badges)   |
| Resources & Standards | `governance.html`  | Document library, management tools, validation |
| Course page           | `course.html`      | Course detail: banner, sections, progress rail |
| Activity page         | `activity.html`    | Activity player: content, quiz, navigation     |
| Notifications/Messages| shell only         | Standard Moodle popover/pages, restyled        |
| Settings              | brief only         | Profile, locale, password, appearance, notifications, privacy, sessions |

## Non-goals (for this programme of work)

- No native mobile apps; PWA concerns are out of scope until the theme is complete.
- No Moodle core competency (`tool_lp`) integration.
- No Moodle core hacks of any kind.
