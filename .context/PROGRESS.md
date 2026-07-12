# PROGRESS — live status board

> Update this file every time you start or finish work. Newest entries first in
> "Recent work". Keep it honest — blocked is blocked.

Last updated: 2026-07-12 (Phase 8 style audit complete — see PHASE8_AUDIT.md)

## Domain status

| # | Domain                      | Phase | Status        | Notes |
|---|-----------------------------|-------|---------------|-------|
| 10| enrol-learningplan          | 0     | `[x]` completed + E2E-verified | v0.1.0. Real enrol round-trip verified 2026-07-12 during Phase 3 seeding (sequential release → 1 course; immediate → all). PHPUnit still pending. |
| 00| theme-foundation            | 1     | `[~]` mostly done | Scaffold installed + verified; Boost-var mapping deferred to domain 02 |
| 03| preferences                 | 1     | `[~]` mostly done | Definitions/privacy/rendering (data-theme + system fallback)/AMD toggles live; no-JS preferences-page UI pending |
| 11| customisation-settings      | 1–2   | `[~]` in progress | General + Navigation + Colours + Advanced tabs live; Brand/Pages/Blocks pending |
| 01| app-shell                   | 2     | `[~]` core done | Sidebar/topbar/drawer/collapse live E2E; Behat + focus-trap + deep a11y pass pending |
| 02| mode-switch                 | 2     | `[~]` core done | Both directions E2E-verified incl. forced modes; navbar "SFS" toggle in standard mode; Behat pending |
| 05| student-lab                 | 3     | `[~]` core done | v1 single-stage grid (ADR-008); E2E-verified with real data both schemes; stage grouping + effort/level await schema decision |
| 09| learningplans-integration   | 3     | `[~]` core done | Overview/set-active use cases + preference port live; external functions deferred (needs db/services.php approval) |
| 06| course-experience           | 3–8   | `[x]` done | Restyle + plan-context strip + right rail (next-up/info/teachers) + per-section fractions (Phase 8 C1–C2) |
| 04| dashboard-insights          | 4     | `[~]` v1 done | Hero/stats/hubs+dot-map/feed all settings-driven; per-role routing = via nav (decided) |
| 07| future-food                 | 4     | `[~]` v1 done | XP/level from badges+completions, achievements, missions grid; real badges to be configured by admin |
| 08| resources-standards         | 4–8   | `[x]` done | Filearea library + audience filters + KPI stat cards + tool icons/counts (Phase 8 R1–R5); R6 validation card → Phase 9 |

## What already exists (baseline, verified 2026-07-12)

- Moodle 5.2.1+ running via Docker (nginx + php-fpm 8.3 + mysql + redis).
- `local_learningplans` v0.2.0: full hexagonal implementation — domain/application/
  ports/infrastructure layers, 6 DB tables, events, observers, 3 scheduled tasks,
  Privacy provider, PHPUnit + Behat tests, `en`/`uk` langs, basic (unstyled)
  Mustache pages: index/view/edit/my/enrol/cohorts.
- Design prototypes complete in `SecureFood School/` (6 pages, design system CSS,
  shell JS, logo assets, screenshots).
- No custom theme exists yet.

## Blockers / risks

| Risk | Impact | Mitigation |
|------|--------|------------|
| `enrol_learningplan` untested | Enrol round-trip may fail silently in edge cases | Domain 10 remaining tasks: PHPUnit + manual E2E before Phase 3 relies on it |
| Prototype CSS is not BEM and uses inline styles in HTML | 1:1 visual port must still be rewritten to BEM + tokens | `design/components.md` defines the mapping; never copy prototype classes verbatim |
| Prototype loads Google Fonts + Material Icons from CDN | Forbidden (privacy/no-CDN rule) | Self-host in theme fonts/ — Phase 1 |
| Prototype persists theme/sidebar in localStorage only | Product brief requires server-side preferences | Domain 03 user preferences with client cache |
| Boost (BS5) markup differs from prototype grid shell | Custom layouts required, not just CSS overrides | Domain 01/02 own the layout strategy (see DECISIONS.md ADR-002) |
| Gamification (XP/levels/missions) has no backend yet | Future Food page has no data source | Phase 4 decision: badges-based first iteration |

## Blockers needing owner decision

_None currently._ (Both 2026-07-12 blockers resolved — see Recent work.)

## Ops notes

- **PHPUnit environment is live** (2026-07-12): composer dev deps installed in
  the php-fpm container (vendor/ and config.php are gitignored — on a fresh
  clone rerun `composer install` and re-add `$CFG->phpunit_prefix = 'phpu_';
  $CFG->phpunit_dataroot = '/var/www/moodledata/phpunit';` to config.php, then
  `php public/admin/tool/phpunit/cli/init.php`). Note: 5.2 keeps the phpunit
  init CLI under public/admin/tool/, unlike the root admin/cli scripts.
  PHPCS binary is NOT included by moodle-testing — codechecker still pending.

- ~~Cron is not running~~ **Resolved 2026-07-12 (Phase 7.4)**: dedicated cron
  container in docker-compose (60s loop over admin/cli/cron.php); task_log
  confirms fresh successful runs.

## Recent work

- **2026-07-12** — **Phase 8 style audit COMPLETE** (batches S, A, F, R, C, X;
  4 commits c116b89..2a70da6). Highlights: Future Food head/hero/XP panel per
  ffs.html; Resources = governance.html (KPI stat cards via new `stats` JSON
  setting, audience filter pills, tool icons + live counts, circular download
  buttons); course pages gained the right rail (`courserail` class +
  `course_rail.mustache`: next-up, info list, teachers) and per-section
  "done / total" fractions (`sectionprogress` AMD, progressive enhancement);
  topbar help "?" via new `helpurl` setting. Bug fixes along the way: mobile
  ≤820px no longer reserves the collapsed icon-rail column (specificity),
  rail Continue styled with brand tokens. All deferred items (no data
  source) listed in PHASE8_AUDIT.md → Phase 9. Theme suite 15/15 green.

- **2026-07-12** — **Activity icon fix + richer demo course** (owner feedback):
  mod icons were overflowing their tiles and kept Boost's per-purpose colours.
  Now: 44px (36px mobile) flex-centred tiles; a single design tint
  (CSS filter approximating `--sfs-primary`) applied with the same specificity
  shape as Boost's purpose filters (`container:not(.isbranded)
  icon:not(.nofilter)`) so it wins by order while branded icons keep their own
  colours. Demo course extended to 4 named modules with pages + external URLs
  for design evaluation. Verified full-page screenshot: uniform icons, all
  sections/rows correct.
  **Note**: the dark scheme keeps the same dark-teal tint — icons on dark may
  need a lighter filter variant (follow-up in domain 06).

- **2026-07-12** — **Owner feedback round (course view + mobile + width)**:
  1. *Hide Moodle chrome in SFS mode* (owner decision, "1:1 design"): course
     secondary navigation (Participants/Grades/… tabs), Collapse-all control,
     per-section collapse chevrons (`.icons-collapse-expand`) and bulk-select
     are `display:none` on course pages; sections are forced expanded
     (`.course-content-item-content.collapse { display: block }`). Staff reach
     management pages via standard mode (one-click "SFS"/⇄ switch).
  2. *Content max-width removed*: `.sfs-shell__content` is now full-width
     (owner request); `--sfs-content-max` token kept but unused.
  3. *Mobile pass* (≤820px): compact topbar (current-page breadcrumb only,
     hidden user-menu text, tighter gaps), Student Lab (smaller title,
     full-width Continue button, single-column tile grid, stacked plan bar),
     course pages (compact banner/sections/rows, 36px icon tiles).
  Verified with Playwright: desktop course page clean and full-width; mobile
  390×844 Student Lab + course page both lay out correctly.

- **2026-07-12** — **Course experience v1 (domain 06)** — deliberately CSS-only
  (`theme/securefood/scss/components/_course.scss`), zero template overrides,
  so editing mode and all modules keep stock behaviour:
  course banner styled from core `#page-header` (gradient + amber radial,
  `header-maxwidth` uncapped to match the shared content width); secondary nav
  as flat tabs on the page background; sections as design cards with gradient
  headers; activity rows with 44px icon tiles, hover states and pill completion
  buttons; activity pages get the banner, readable measure, styled
  completion-requirements card and pill prev/next navigation.
  **Verified** (Playwright, learner session): seeded 3 page activities with
  view-completion into SFS101 (+ renamed demo sections); viewing an activity
  flipped its course-page pill To do → **Done** (real completion round-trip);
  light + dark screenshots correct (dark tested by flipping the user's scheme
  preference — an explicit preference overrides emulated `prefers-color-scheme`,
  as designed).
  **Known polish left**: Bootstrap-blue links/chevrons not yet tokenised;
  prototype's right rail and plan-context chips need a domain 09 read model;
  only mod_page visually verified so far.

- **2026-07-12** — **Capability fix shipped** (owner-approved,
  `local_learningplans` → 2026071201): `db/access.php` now grants
  `local/learningplans:view` + `:viewprogress` to the `user` archetype
  (learners' student role is course-level and never applied at system
  context), and a guarded `db/upgrade.php` step assigns both to existing
  authenticated-user roles on upgrade (no overwrite of explicit settings).
  **Verified**: revoked the manual dev-site grant → ran CLI upgrade → learner
  capability restored by the upgrade step alone.
- **2026-07-12** — **Login "flakiness" investigated and closed: not a Moodle
  bug.** Forensics (Redis session inspection via `HGET sessdata`, header
  traces, controlled A/B loops) showed the login page legitimately contains
  **two** `logintoken` hidden inputs; the curl test harness scraped both and
  posted `"token\ntoken"`, which fails validation with the same generic
  reason=3 as a wrong password. Every historical failure/success in this
  session correlates 1:1 with broken vs correct scraping. With correct
  scraping and token validation **re-enabled**, 10/10 fresh-session logins
  pass. `disablelogintoken` removed; browsers were never affected.
  **Lesson**: `grep -oE` a form token → always `head -1`; and "intermittent"
  auth failures deserve a look at the *test harness* before the product.

- **2026-07-12** — **Owner-reported visual fixes** (wide-screen screenshots):
  1. *"Framed" look / white panel (and white box in dark mode)*: Boost ships
     `.main-inner` and `#region-main` with `background:#fff` — in SFS mode all
     content surfaces (`#topofscroll`, `.main-inner`, `#page-content`,
     `#region-main[-box]`) are now transparent so content sits straight on the
     token background; this also fixed invisible light-ink headings on the
     white box in the dark scheme.
  2. *Topbar icons not design-styled / invisible in dark*: notification and
     messaging popover toggles restyled as 36px circular icon buttons
     (`--sfs-line` border, `--sfs-surface` bg, `--sfs-ink2` icon colour,
     accent count badge); user-menu text (`usertext`/login-as) and edit-switch
     label now use `--sfs-ink2`. All token-driven → dark scheme correct
     automatically.
  Verified with Playwright screenshots in both schemes (server stamps
  `data-theme` from the preference — confirmed in served HTML).
  **Lesson**: after `purge_caches` the first page request can serve stale
  theme CSS while recompiling — always discard the first post-purge
  screenshot and re-shoot.

- **2026-07-12** — **Phase 3 core shipped: Student Lab with real data** (ADR-008).
  - **Plugin** (`local_learningplans` → 2026071200): new port
    `user_preference_repository_interface` (+ Moodle adapter, preference
    `local_learningplans_activeplan`, declared in privacy provider); pure domain
    policy `student_lab_status_policy` (done/active/upnext/locked + continue
    target); use cases `get_student_lab_overview` and `set_active_learning_plan`;
    factory wiring; `my_learning_plans_controller` rewritten into the Student
    Lab page (PRG plan switch, no JS required, `<details>` switcher);
    `studentlab_page.mustache` with `lp-*` BEM blocks; neutral base CSS in
    plugin `styles.css`; en+uk strings; PHPUnit test file for the policy.
  - **Theme**: `components/_studentlab.scss` re-skins `lp-*` under `.sfs-mode`
    to match `l4c.html` (plan bar gradient + pin icon, switcher menu, tile
    covers/status chips/progress). Fixed a global `.sfs-mode a{color}` rule
    that made `.btn-primary` text invisible; core `#page-header` hidden on the
    plugin path in SFS mode (template owns the page head).
  - **Verified E2E** (Playwright + Chrome, logged in as `sfstestuser`):
    2 seeded plans (sequential-release ×4 courses, immediate ×2) created via
    the real service path — which also **verified enrol_learningplan end to
    end** (3 course enrolments, sequential release correctly held back locked
    courses); tile statuses Active/Up next/Locked correct (up-next via
    cross-plan enrolment); Continue learning targets the active course;
    switcher opens, switches to plan 2 (2 tiles) and back, preference persists;
    light + dark screenshots match the prototype closely; no missing strings;
    `php -l` clean; policy verified standalone (10/10).
  - **Known gaps**: dark-scheme core-content colours (headings/cards) need a
    pass (Phase 5); stage grouping/effort/level deferred per ADR-008; standard
    mode shows the core page header above the template head (minor duplication,
    SFS mode unaffected).

- **2026-07-12** — **Shell grid fix, round 2 — actual root cause** (owner
  screenshot): Boost ships `#page-wrapper { display: flex; flex-direction:
  column; }` — an id-strength selector that beats `.sfs-mode .sfs-shell`, so
  the grid never applied and the shell stacked vertically. Fixed by using
  `#page-wrapper.sfs-shell` in the theme (documented exception to the no-id
  rule; the only alternative was `!important`). Also fixed the dark-scheme
  logo swap showing full + icon logos simultaneously (dark override now
  targets `--full`/`--icon` variants explicitly per collapsed state).
  **Verified visually** with headless Chrome/Playwright screenshots
  (`npx playwright screenshot --channel chrome --color-scheme=light|dark`) —
  grid correct and single logo in both schemes.
  **Lessons**: (1) when overriding Boost furniture carrying ids
  (`#page-wrapper`, `#page`), check Boost's own rules for id-strength
  conflicts first; (2) visual changes must be verified with a real browser
  screenshot, not curl greps — Playwright + system Chrome works headlessly;
  (3) `--headless=new` follows the host OS colour scheme — use Playwright's
  `--color-scheme` to pin it.
- **2026-07-12** — **Phase 2 core shipped** (theme v0.1.0 → 2026071201):
  - **App shell (01)**: `layout/sfs.php` + `shell/shell_sidebar/shell_topbar/icon`
    templates — sidebar (brand logos light/dark, settings-driven nav, user card
    with cohort), topbar (toggle, breadcrumbs, global-search form, scheme toggle,
    edit switch, messaging/notification popovers, lang + user menus), blocks
    region kept below content, skip-link. SCSS `layout/_shell.scss` fully
    token-driven, BEM `sfs-*`, collapsed sidebar with tooltips, mobile drawer
    with backdrop, reduced-motion guards. AMD `theme_securefood/shell`
    (src + hand-built AMD in build/) persisting sidebar + colourscheme via
    `core_user/repository`.
  - **Mode switch (02)**: `mode_manager` (pure policy + runtime), user-facing
    layouts route through `sfs.php` with Boost-drawers fallback (standard mode,
    excluded layouts, editing); `mode.php` endpoint (login + sesskey + local
    returnurl); General settings tab (forcemode/defaultmode); navbar override
    (`templates/theme_boost/navbar.mustache`, documented) adds an "SFS" link so
    standard mode can switch back — owner-requested mid-phase.
  - **Preferences rendering (03)**: `core_renderer::htmlattributes()` stamps
    `data-theme`; `system` scheme via `prefers-color-scheme` fallback in tokens
    (incl. admin dark overrides emitted for both explicit and system dark).
  - **Navigation settings (11)**: JSON setting + `navigation` parser
    (sanitised icons, visibility all/loggedin, longest-prefix active match,
    localised defaults).
  - **Verified E2E** (curl against live Docker site, test user `sfstestuser`):
    guest frontpage renders shell with only public nav item; logged-in dashboard
    shows user card + 6 nav items; switch to standard → stock Boost navbar with
    "SFS" toggle; switch back → shell; forcemode=standard kills shell site-wide
    (and restored). CSS contains all shell blocks + 2 `prefers-color-scheme`
    fallbacks; zero missing strings; `php -l` clean; pure logic verified
    standalone (mode resolution 5/5, navigation parse 5/5, tokens emission).
  - **Notes**: local site has `cookiesecure=1` over http — browsers/curl drop
    the session cookie unless HTTPS or manual header (cost an hour of E2E
    debugging; flag to owner). Test account `sfstestuser` (id 3) left on the
    dev site for future E2E. PHPUnit runner still absent in container —
    3 test files ready but unrun.

- **2026-07-12** — **Phase 1 slice shipped**: `theme_securefood` v0.1.0 created,
  installed via CLI upgrade and set as the site default theme (standard Boost
  passthrough — no visual change yet). Delivered: Boost-child scaffold
  (config/version/lib), full token layer (`_tokens.scss`, light+dark, `--sfs-*`
  namespace), self-hosted Manrope/Inter/Material Icons (latin+cyrillic WOFF2,
  licences documented), `_mixins.scss`, `.sfs-icon` primitive, logos in `pix/`,
  user preferences (colourscheme/sidebar/uimode) + privacy provider, settings
  tabs (Colours: 40 token overrides; Advanced: raw SCSS) with
  `theme_securefood\tokens` emission class + unit tests.
  **Verified**: `php -l` clean; SCSS compiles (1.07 MB, tokens present); colour
  override E2E (set → compiled CSS contains override → reset); fonts served via
  `font.php` (HTTP 200); homepage renders HTTP 200 with the theme active; tokens
  logic checked standalone (9/9 assertions).
  **Not verified**: PHPUnit runner absent in the container (vendor has no dev
  deps) — `tests/tokens_test.php` is ready but unrun; PHPCS likewise unavailable.

- **2026-07-12** — Owner added `enrol_learningplan` v0.1.0 (`public/enrol/learningplan/`).
  Verified surface + `customint1` contract; tests/E2E still pending (domain 10).
- **2026-07-12** — Owner requirement recorded (ADR-007): maximum customisation via
  settings — colours, texts, navigation, blocks, page content. New domain 11.
- **2026-07-12** — Root `CLAUDE.md` + `AGENTS.md` added for coding agents.
- **2026-07-12** — `.context/` structure created: project analysis, roadmap, conventions,
  design system extraction, domain contexts. No production code written yet.

## Next actions (in order)

1. Environment: install composer dev deps (or a phpunit phar) in the container so
   `tests/tokens_test.php` and future tests can run; init Moodle PHPUnit.
2. Phase 2: app shell (domain 01, nav from settings) + mode switch (domain 02) —
   this is also where preference *rendering* (data-theme, sidebar) and the AMD
   scheme/shell modules land (finishing domain 03).
3. Domain 11 remainder: Brand / Navigation / Pages & content / Blocks tabs.
4. Deferred from Phase 0: PHPUnit + E2E for `enrol_learningplan`.

- **2026-07-12** — **Domain 04 slice 1**: front page now renders a
  settings-driven About hero (kicker/title/lede + 3 stat cards) and a 4-tile
  KPI row above site content (`theme_securefood\about` + `about.mustache` +
  `_about.scss`; Pages & content settings tab, en+uk). Verified with a
  screenshot at /?redirect=0 — matches insights.html. Remaining for 04:
  Living Labs map, updates feed, per-role home routing decision.
  Domain 04 status: `[~]` slice 1 done.
  Also: all prior work committed as five logical commits (unsigned — signing
  key passphrase unavailable to the agent).

- **2026-07-12** — **Domain 04 slice 2**: updates feed on the front page
  (sfs-feedcard grid, 4 gradient thumb variants cycling, chip/title/text/time
  from the aboutfeed JSON setting + aboutfeedtitle heading; design defaults).
  Verified full-page screenshot: hero → KPI row → feed → site content, matches
  insights.html. Remaining for 04: Living Labs map (SVG, no-CDN decision),
  per-role home routing.

- **2026-07-12** — **Domain 04 slice 3** (owner feedback): About defaults now
  the exact insights.html copy (kicker/lede/4 hero stats); KPI row hidden by
  default (not in the design); Living Labs & partners hub-list block with
  lab/partner pills + abouthubs settings; About nav uses /?redirect=0 (was
  bouncing logged-in users to the dashboard); heading double-escape fixed.
  Map visual itself still deferred (no-CDN decision).

- **2026-07-12** — **Domain 07 slice 1 (owner picked option 1)**: local_sfsgame
  plugin — XP hero (pure xp_policy: 100/badge + 50/completed course, level per
  500 XP), achievements from site badges (earned/locked), honest empty state;
  theme skin ffg-* per ffs.html + nav item. Verified live as learner (Level 1,
  0 XP, empty-state). Follow-ups: missions grid (settings JSON), badge images,
  xp_policy PHPUnit, decision module link, header-hide re-verify.

- **2026-07-12** — **Domain 07 slice 2**: Active missions grid on Future Food —
  local_sfsgame\missions parser (admin JSON, ffs.html defaults), template +
  ffg-mission theme skin (4 gradient variants, badge/duration pills, tag chips,
  +XP foot, Start button only with a URL), plugin settings page, xp_policy
  PHPUnit (standalone 6/6). Verified full-page screenshot. Domain 07 remaining:
  badge artwork in achievements, decision module link, wiring real badges.

- **2026-07-12** — **Domain 08 v1 (option в)**: local_sfsresources — curated
  document library (admin JSON, governance.html defaults; type badges,
  audience/updated, Open pill only with URL; en+uk; theme skin sfsres-* +
  nav item; single-escape fix for ampersands). Verified via screenshot +
  served HTML. Follow-ups: real file storage decision (course resources vs
  filearea), filter pills, management-tools grid (capability-gated),
  validation card.

- **2026-07-12** — **Phase 4 completed (v1)**. Final slice:
  - About: Living Labs dot map — hubs plotted from their real design
    coordinates (equirectangular Europe window, lon −12…42 / lat 34…62) on a
    stylised CDN-free panel; two-column map+list layout, mobile stacks.
    Decision: no tile map (Leaflet) — dot map keeps the no-CDN rule; per-role
    home routing decided as "via sidebar nav", no forced redirects.
  - Resources: server-side kind filter pills (?kind=, no JS needed) and a
    capability-gated management-tools grid (plans/cohorts/badges/courses,
    shown only with local/learningplans:manage).
  Verified via screenshots (About full page with map; PDF filter active;
  tools absent for a learner). Deferred to Phase 5+: real document storage
  backend, validation card, badge artwork, mods dark-scheme pass.

- **2026-07-12** — **Phase 5 slice 1**: Appearance preferences page
  (scheme + mode radios, PRG + sesskey, no JS needed — closes the domain 03
  no-JS gap and the brief's Appearance section), linked into the core
  preferences hub via theme_securefood_extend_navigation_user_settings;
  hub cards restyled. Verified: POST round-trip persisted the preference;
  hub link present; i18n audit en↔uk in sync (90/194/16/18 keys, 4
  components); missing-string sweep over 7 pages: zero. Remaining Phase 5:
  notifications/messages restyle, WCAG pass, dark-scheme core-content pass,
  Behat, performance.

- **2026-07-12** — **Phase 5 slice 2 (WCAG + dark core)**: contrast audit of
  all token pairs (script in session log) found muted 4.19:1 and accent-as-
  text 2.96:1 in light — fixed via darkened --sfs-muted and new derived
  --sfs-accent-ink/--sfs-accent-ondark tokens swapped into every failing
  usage; dark scheme now covers core furniture (dropdown/menu/cards/forms/
  alerts/tables) through a token mixin; mobile drawer got a focus trap with
  focus restore; 'admin'-layout user pages (preferences hub) now render in
  the shell (only /admin/ paths stay Boost) and the hub restyle applies.
  Verified: dark preferences hub + open user menu screenshot; light hub in
  shell; build JS syntax-checked. Remaining Phase 5: notifications/messages
  pages polish, Behat, performance pass, PHPCS/PHPUnit environment.

- **2026-07-12** — **Phase 5 slice 3: PHPUnit environment + full test run.**
  Dev dependencies installed, phpunit prefix/dataroot configured, environment
  initialised. **All suites green**: 23/23 new tests (91 assertions — theme
  tokens/mode_manager/navigation, learningplans student_lab_status_policy,
  sfsgame xp_policy) and the full local_learningplans suite 11/11 (36
  assertions). Directory args don't run under Moodle's PHPUnit 11 config —
  use file paths or --testsuite <component>_testsuite. Remaining Phase 5:
  notifications/messages polish, Behat, performance pass, PHPCS via
  moodle codechecker, enrol_learningplan tests.

- **2026-07-12** — **Phase 5 slice 4**: enrol_learningplan PHPUnit written and
  green (3/3: instance customint1, enrol/unenrol round-trip incl. idempotent
  re-enrol, can_add_instance=false) — Phase 0 test debt closed; messaging/
  notifications surfaces tokenised (message-app card, unread tint, popover
  container) and verified in the shell. Remaining Phase 5: Behat (needs
  browser infra in docker), PHPCS via moodle/codechecker, performance pass.

- **2026-07-12** — **Phase 6 opened (infra items waived by owner); slice 6.1
  shipped**: course pages now show the learner's plan context (chip → Student
  Lab, "Course N of M", live completion bar) via a new read-only
  get_course_plan_context use case and a course_content_header renderer
  override. Verified on SFS101 as the test learner (real 33% completion).
  Next candidates need owner input: 6.2 stage grouping (db/), 6.3 external
  functions (db/services.php), 6.4 resources file storage.

- **2026-07-12** — **Phase 6.2 shipped (stage grouping)**: stagename column on
  _crs (upgrade 2026071202), full chain entity→repo→service→form, pure
  group_stages() with tests (suite 12/12), Student Lab renders named stages
  with number/done/lock badges and per-stage meta — verified on the demo plan
  (Foundations active, Pattern practice + Reporting locked), matching
  l4c.html. Design decision: denormalised column v1, sections table later.
  Domain 05/09 stage items closed. Gotcha: entity file edit initially matched
  a comment-stripped pattern and silently half-applied — caught by the test
  suite (undefined variable warning), fixed properly.

- **2026-07-12** — **Phase 6.3 shipped (external functions)**: get_student_lab
  + set_active_plan externals (own-data, viewprogress-gated, ajax + mobile
  service), db/services.php, externallib tests — suite 16/16 (53 assertions).
  Tests exposed and fixed a latent prod bug: planprogress MUC cache key used
  "planid:userid" — invalid for simple-keys definitions (only errors under
  debug, silently unvalidated in prod); now "planid_userid". Domain 09 now
  fully closed except optional AJAX consumer in the theme (PRG still primary).

- **2026-07-12** — **Phase 6.4 shipped (filearea storage, owner-picked)**:
  Library files upload setting → system-context documents filearea; files
  render above curated links (size/date/type badge via pure
  kind_from_filename, tests green), join the kind filters, and download via
  pluginfile with require_login (verified: learner 200 + file bytes,
  anonymous 303 → login). Remaining Phase 6: only 6.5 performance pass.

- **2026-07-12** — **Phase 6.5 shipped — Phase 6 complete.** Performance
  baseline (warm, learner, perfdebug): Student Lab 39 reads/78ms, About 44/91,
  course 36/130, Resources 20/64, Appearance 20/66 — all healthy (0 writes
  everywhere; per-course cost on Student Lab ≈3 reads/course, plan progress
  MUC-cached). One fix applied: Future Food computed XP via the full Student
  Lab overview — now sums MUC-cached progress across all plans: 41→21 reads
  and semantically better (all plans, not just active). perfdebug flag was
  temporary in config.php and removed.

- **2026-07-12** — **Phase 7 opened; 7.1 shipped (real achievements)**:
  badge artwork in the achievements grid (pluginfile f1, greyscale when
  locked); two real site badges seeded (manual criteria, badge API records +
  badges_process_badge_image with the design logo) and one issued to the
  test learner — XP hero now shows genuine Level 1 · 100 XP with a moving
  bar. Verified by screenshot. Next: 7.2 AJAX plan switcher, 7.3 unified
  settings hub, 7.4 cron (docker — owner approval).

- **2026-07-12** — **Phase 7.2 shipped (AJAX plan switcher)**: planswitcher
  AMD (src + build) calls the 6.3 external over core/ajax with PRG links as
  no-JS fallback. Playwright E2E: switch via WS confirmed (service.php call
  observed), clean URL, switch-back OK. Remaining: 7.3 settings hub,
  7.4 cron (owner approval for docker).

- **2026-07-12** — **Phase 7.3 shipped (settings hub)**: mysettings.php with
  six design cards per the brief (real profile/language/scheme facts, links
  into core flows), nav Settings retargeted. Verified by screenshot; icon
  gotcha: the self-hosted Material Icons v145 set lacks newer glyphs
  (shield_person) — stick to classic names (verified_user). Remaining: 7.4
  cron service (docker — owner approval).

- **2026-07-12** — **Phase 7.4 shipped — Phase 7 complete.** Cron service
  added to docker-compose (owner-approved): php image, 60s loop, logs to the
  container stdout; verified via fresh successful task_log entries (badge
  notification adhoc tasks ran immediately). Phase 8 (pilot readiness)
  planned in ROADMAP with owner-decision markers: content/auth/deployment/
  CI/UX-completion/uk-review/hardening/tech-debt.
