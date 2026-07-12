# ROADMAP — phased development plan

Phases must be delivered in order; domains inside a phase may run in parallel where
their dependency lists allow. Domain numbers refer to `domains/NN-*/CONTEXT.md`.

## Phase 0 — Unblock the data layer

> Without this the Student Lab page cannot show real enrolments.

- **Domain 10 — enrol_learningplan**: ~~implement~~ *added by owner 2026-07-12* —
  remaining: PHPUnit tests + E2E verification.
- Verify end-to-end: create plan → add courses → enrol user → user appears in course.

Exit criteria: `local_learningplans` enrol/unenrol use cases pass against a real
Moodle instance; PHPUnit green.

## Phase 1 — Theme foundation

- **Domain 00 — theme-foundation**: scaffold `theme_securefood` (Boost child),
  SCSS token layer from `styles.css`, self-hosted fonts (Manrope, Inter) and
  Material Icons, logo assets, light/dark palettes.
- **Domain 03 — preferences**: user preferences for colour scheme (light/dark/system),
  sidebar collapsed state, and UI mode — persisted server-side, cached client-side.
- **Domain 11 — customisation-settings (start)**: settings-page skeleton,
  `settings_provider`, colour settings → pre-SCSS pipeline (ADR-007).

Exit criteria: theme installs, inherits Boost cleanly, tokens compile, admin
colour override demonstrably restyles a sample component, no visual regressions
in standard mode.

## Phase 2 — App shell + mode switch

- **Domain 01 — app-shell**: sidebar (nav sections, brand, user card, collapse +
  tooltips), topbar (breadcrumbs, search, theme toggle, notifications, help),
  responsive/mobile drawer — as Mustache layouts + AMD module.
- **Domain 02 — mode-switch**: the Standard ⇄ SecureFood mode toggle; SecureFood
  layouts apply only when the mode preference (or forced theme setting) says so.
- **Domain 11 — customisation-settings (complete)**: navigation settings feeding
  the shell nav, block regions in custom layouts, pages/content settings, advanced
  tab (raw SCSS, footer).

Exit criteria: every Moodle page renders inside the SecureFood shell in custom mode
and stock Boost in standard mode; keyboard navigation works; WCAG AA passes.

## Phase 3 — Core learner pages

- **Domain 05 — student-lab**: plan bar with switcher, stage sections, course tiles,
  Continue learning — fed by `local_learningplans` use cases (new render-data ports
  where needed).
- **Domain 09 — learningplans-integration**: external functions / data providers the
  theme consumes; active-plan user preference; Continue-learning resolution policy.
- **Domain 06 — course-experience**: course page (banner, sections, progress rail)
  and activity page styling on top of standard Moodle course/mod rendering.

Exit criteria: a learner enrolled in a plan sees their real plan, real progress,
locked stages behave per sequencing rules, Continue learning resolves correctly.

## Phase 4 — Secondary pages

- **Domain 04 — dashboard-insights**: About the Project page (hero, KPI row,
  Living Labs, feed) as theme frontpage/dashboard.
- **Domain 07 — future-food**: achievements (Moodle badges), XP/levels, missions.
- **Domain 08 — resources-standards**: document library, filters, management tools.

## Phase 5 — Settings, polish, hardening

- Settings page sections mapped to Moodle profile/preferences (see product brief).
- Notifications/messages restyle, empty/error/loading states everywhere.
- Full accessibility audit, i18n audit (`en`/`uk`), performance pass, Behat coverage.

## Cross-cutting definition of done (every phase)

1. `php -l` on all touched PHP files; PHPCS clean.
2. Affected PHPUnit tests pass; new logic gets tests.
3. `en` and `uk` language strings both updated.
4. No hard-coded colours/spacing — tokens only; BEM-compliant class names.
5. `PROGRESS.md` and the domain `CONTEXT.md` updated.

## Phase 6 — Deepening (added 2026-07-12, after Phase 5 close)

> Closes the deferred cross-domain backlog. Infra items (Behat, PHPCS) were
> explicitly waived by the owner on 2026-07-12.

- **6.1 Course plan context (domains 06+09)**: "part of plan X · course N of M"
  chips + course progress on the course page, via a new read-only use case in
  local_learningplans (no schema changes) rendered through the theme renderer.
- **6.2 Stage grouping (domain 09)**: real sections in plans — REQUIRES a
  db/ schema addition → owner approval before starting.
- **6.3 External functions (domain 09)**: db/services.php — owner approval.
- **6.4 Resources file storage (domain 08)**: owner decision (course files vs
  plugin filearea).
- **6.5 Performance pass**: query counts on Student Lab/About, MUC where
  measured hot.

## Phase 7 — Activation & polish (added 2026-07-12)

- **7.1 Real achievements**: badge images in Future Food; seed real site
  badges (manual criteria) so XP/achievements run on live data.
- **7.2 AJAX plan switcher**: progressive enhancement over PRG using the
  Phase 6.3 externals.
- **7.3 Unified settings hub**: custom Settings page per the product brief
  (Profile/Language/Password/Appearance sections as design cards).
- **7.4 Cron service**: needs a docker-compose change → owner approval.

## Phase 8 — Pilot readiness (planned 2026-07-12)

> Goal: from "feature-complete dev site" to "real learners can use it".
> Ordered by risk; items marked ⚑ need an owner decision first.

- **8.1 Real content & data**: production courses/plans/cohorts; ⚑ content
  language strategy (uk-only vs multilang); cohort→plan auto-enrolment E2E
  now testable since cron runs; badge criteria wired to course completion.
- **8.2 Onboarding & auth** ⚑: how learners get accounts (manual/CSV upload/
  self-registration/OAuth via school accounts) + email transport (SMTP)
  config for confirmations and badge/notification mail.
- **8.3 Production deployment** ⚑: target host; HTTPS + wwwroot change
  (cookiesecure then becomes meaningful), secrets out of compose defaults
  (.env), DB+moodledata backup job, log rotation, container monitoring,
  documented upgrade procedure (composer install --no-dev, purge, upgrade.php).
- **8.4 CI quality gates**: GitHub Actions (or similar) running php -l,
  PHPUnit (all suites), moodle/codechecker PHPCS, and Behat with a selenium
  sidecar — the infra that was waived locally belongs in CI.
- **8.5 UX completion**: course right rail (course.html), Resources
  validation card → real choice activity, Future Food decision module →
  real quiz, empty-states audit across pages.
- **8.6 Language review** ⚑: human review of all uk strings (agent-written,
  never shipped unreviewed per org policy).
- **8.7 Hardening**: focused security review of mode.php/pluginfile/externals,
  account lockout policy, privacy-provider completeness check, load test with
  realistic cohort sizes.
- **8.8 Tech debt (optional)**: normalise stagename into a sections table;
  Boost-variable mapping for custom mode; AJAX re-render (no reload) for the
  plan switcher.
