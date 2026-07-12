# DECISIONS — architecture decision records (append-only)

Format: `ADR-NNN — title` / Date / Status / Context / Decision / Consequences.
Never edit an accepted ADR; supersede it with a new one.

---

## ADR-001 — Custom experience as a Boost child theme, not a PWA

Date: 2026-07-12 · Status: accepted

**Context.** The original brief mentions a custom PWA built on the Moodle App.
The current direction (owner decision) is a custom **Moodle theme** that reproduces
the design 1:1, with a switch between standard and custom modes.

**Decision.** Build `theme_securefood` as a child of Boost (Moodle 5.2, Bootstrap 5).
Inherit Boost SCSS/presets and renderers; override layouts, add SCSS token layer and
custom Mustache templates. No PWA work in this programme.

**Consequences.** Upgrade-safe; standard mode comes almost for free; all Moodle
functionality (mods, admin, reports) keeps working in both modes.

---

## ADR-002 — Mode switch = user preference selecting layouts + body class

Date: 2026-07-12 · Status: accepted

**Context.** Requirement: the single difference from the design is a toggle between
*Standard mode* (stock Boost look) and *SecureFood mode* (custom design). CSS-only
overrides cannot produce the prototype's sidebar-grid shell from Boost markup.

**Decision.**
- A user preference (`theme_securefood/uimode`: `standard` | `securefood`) plus an
  admin setting to force a mode site-wide.
- In SecureFood mode the theme serves its own layout files (custom app shell);
  in standard mode it falls back to Boost layouts untouched.
- `<body>` gets `sfs-mode` class in custom mode; all custom SCSS is scoped under it
  so standard mode is bit-identical to Boost.
- Admin pages (`admin`, `maintenance`, install/upgrade layouts) always use Boost
  layouts regardless of mode — safety first.

**Consequences.** Clean separation, easy A/B rollout, no `!important` wars. The
toggle lives in the topbar (custom mode) and in user preferences (both modes).

---

## ADR-003 — BEM with `sfs-` prefix; prototype classes are never copied verbatim

Date: 2026-07-12 · Status: accepted

**Context.** The prototype CSS (`SecureFood School/styles.css`) is a flat class
soup (`.plan-bar`, `.course-tile .ct-body h4`, inline styles). Org standard is BEM,
plugin-prefixed, token-driven.

**Decision.** Every custom class follows `sfs-block__element--modifier`. Design
tokens become SCSS variables (`_tokens.scss`) mirroring the prototype's CSS custom
properties; components are rebuilt per `design/components.md`. Inline styles in
prototypes are translated to modifiers or tokens.

**Consequences.** Visual output is 1:1; the code is not a copy-paste of the
prototype. Slightly more upfront work per component, far cheaper maintenance.

---

## ADR-004 — Dark mode via `data-theme` attribute + server-side preference

Date: 2026-07-12 · Status: accepted

**Context.** Prototype toggles `data-theme="dark"` on `<html>` and stores the value
in localStorage. Brief requires light/dark/system persisted as a Moodle preference.

**Decision.** Keep the `data-theme` mechanism (CSS custom properties swap under
`[data-theme="dark"]`), but the source of truth is the user preference
(`theme_securefood/colourscheme`: `light` | `dark` | `system`). The theme renders the
attribute server-side; a small AMD module handles the toggle, `system` media-query
tracking, and writes the preference via `core_user_set_user_preferences` web service.
localStorage is only a flash-of-wrong-theme cache.

**Consequences.** No FOUC, preference follows the user across devices, works with
Moodle privacy export.

---

## ADR-005 — Learning-plan data reaches the theme through plugin use cases only

Date: 2026-07-12 · Status: accepted

**Context.** The Student Lab page needs plan/stage/course/progress data.
`local_learningplans` already owns that domain behind ports and use cases.

**Decision.** The theme never queries `local_learningplans_*` tables or duplicates
domain logic. New read models needed by the UI (e.g. plan overview with per-stage
status, continue-learning target) are added as **use cases + ports inside
`local_learningplans`**, exposed via its service factory and, where AJAX is needed,
via its external functions. The theme's renderers call those and shape template data.

**Consequences.** One owner for plan logic; theme stays presentation-only; the same
read models can later power the PWA/web services.

---

## ADR-006 — Fonts and icons self-hosted in the theme

Date: 2026-07-12 · Status: accepted

**Context.** Prototype imports Manrope, Inter and Material Icons from Google CDN.
CDN dependencies are forbidden (privacy/GDPR + offline reliability).

**Decision.** Ship WOFF2 subsets of Manrope (400–800), Inter (400–700) and the
Material Icons (Rounded) font in `theme/securefood/fonts/`; declare `@font-face`
in SCSS. Licences (OFL/Apache-2.0) documented alongside the font files.

**Consequences.** ~300 KB of assets in the theme; zero third-party requests.

---

## ADR-007 — Everything customisable through admin settings (no-code branding)

Date: 2026-07-12 · Status: accepted

**Context.** Owner requirement: the theme must support maximum customisation via
settings — colours, texts, blocks, content must all be configurable by an
administrator without code changes.

**Decision.**
- **Colours**: every design token gets an admin setting (colour picker), light and
  dark variants. Settings are emitted as CSS custom-property overrides via the
  theme's pre-SCSS callback so components pick them up automatically; defaults =
  the prototype palette. Plus Boost-style `rawscsspre`/`rawscss` escape hatches.
- **Texts/content**: every non-Moodle string rendered by custom pages (hero titles,
  ledes, footer, KPI labels, empty-state copy) is a theme setting
  (`admin_setting_configtext`/`confightmleditor`, multilang-aware via
  `format_text` + filters) with the design copy as default. Moodle UI strings stay
  in lang packs.
- **Blocks**: custom-mode layouts keep real Boost block regions (side-pre +
  content-top/bottom) so admins can add/remove/rearrange blocks; the shell styles
  block cards to match the design. Custom pages (About, Future Food, Resources)
  are composed of settings-driven sections that can be toggled on/off.
- **Navigation**: sidebar menu configurable (add/hide/rename/reorder items, icons)
  through a structured theme setting; defaults mirror the prototype nav.
- Settings organised in an admin tabs page (`theme_boost_admin_settingspage_tabs`
  pattern): Brand · Colours · Navigation · Pages/Content · Blocks · Advanced.

**Consequences.** New domain 11 owns the settings infrastructure; every page
domain must consume settings, never hard-code copy or colour. Slightly more
plumbing per component; zero-code rebranding and content management in return.
Supersedes the "hard-coded nav first iteration" open question in domain 01.

---

## ADR-008 — Student Lab lives in the plugin; stages deferred; no schema change

Date: 2026-07-12 · Status: accepted

**Context.** Phase 3 needs the Student Lab page. Open questions: where the page
lives, how "stages" map onto a schema that has no section grouping
(`local_learningplans_crs` is a flat ordered list), and where effort/level data
comes from.

**Decision.**
- The Student Lab page is `local_learningplans` **presentation** (an upgraded
  `my.php`): controller → new use cases → plugin Mustache templates with
  plugin-prefixed BEM classes (`lp-*`). `theme_securefood` skins those blocks
  under `.sfs-mode` to match the prototype; standard mode gets neutral plugin
  CSS. Rationale: plan UI is plan-domain presentation (ADR-005); both modes
  benefit; no theme→plugin data coupling.
- **v1 renders a single course grid** (one implicit stage). Real stage/section
  grouping requires a schema addition (grouping table or column) — deferred
  until the owner approves a `db/` change; the tile statuses
  (done/active/up-next/locked) already carry the sequencing semantics.
- Active plan = user preference `local_learningplans_activeplan` behind a new
  `user_preference_repository_interface` port.
- Effort/duration and learner level are **omitted in v1** (no data source —
  no fake numbers). Module count comes from course modinfo later; omitted v1.
- No web services in v1: page renders server-side; plan switching is a
  sesskey-guarded PRG action on `my.php`. External functions come with the
  first AJAX consumer (needs `db/services.php` → owner approval).

**Consequences.** Zero `db/` changes; learners get the designed experience with
real data; stages/effort/level appear once their data model is agreed.

## ADR-009 — Future Food v1 is backed by core badges + completion data

**Status.** Accepted (2026-07-12, Phase 4; recorded retroactively during
Phase 9 after the conformance audit flagged the missing entry — the code has
referenced ADR-009 since local_sfsgame v1).

**Context.** The Future Food page needs XP, levels, achievements and missions,
but the platform has no gamification backend and inventing one before the
pilot would be speculative.

**Decision.**
- Achievements = core **site badges**; earned badges and completed courses
  drive XP through the pure `xp_policy` domain class (100 XP per badge,
  50 XP per completed plan course, a level per 500 XP).
- Missions = administrator-curated JSON setting only; no invented backend.
- No new tables; everything derives from data Moodle already stores.

**Consequences.** Zero schema risk; admins control missions/badges entirely
through settings and the badges UI. Known limit (Phase 9 finding): a completed
course that belongs to several plans is counted once per plan — XP should
move to counting distinct completed courses.

## ADR-010 — Content language strategy: multilang (uk + en)

**Status.** Accepted (2026-07-12, owner decision for Phase 9.1).

**Context.** The pilot serves Ukrainian schools but the network is
international; plugin lang packs already ship en+uk. The open question was
user-authored content (course names, plan/stage names, settings-driven copy).

**Decision.**
- The official **uk core language pack is installed**; users pick their
  interface language (learner default may be uk).
- The core **multilang filter is enabled** for content *and* strings
  (`stringfilters=multilang`, `filterall=1`), so `format_string()` sites
  (plan names, course names, headings) resolve per-language variants.
- Bilingual content is authored with core span syntax, both variants in one
  value:
  `<span lang="en" class="multilang">…</span><span lang="uk" class="multilang">…</span>`
- Settings-driven copy (About, Future Food hero, Resources docs) accepts the
  same markup — plugin code must keep passing such values through
  `format_string()`/`format_text()`, never raw.
- Interface strings stay in lang packs (en+uk in sync — hard rule); the
  owner performs the human uk review (9.6).

**Consequences.** No code changes required; content authors carry the
duty of providing both variants (single-language values simply render as-is).
Verified E2E on Student Lab: uk interface + uk plan/course names via
`?lang=uk`.

## ADR-011 — Stages are a first-class entity of the plan aggregate

**Status.** Accepted (2026-07-12, owner decision; supersedes the "denormalised
stagename column" part of ADR-008 and upgrade step 2026071202).

**Context.** Stage names lived as a text column on plan courses. The owner
requires stages as a proper domain model and drag-and-drop that moves courses
between stages.

**Decision.**
- New entity `learning_plan_stage` (id, planid, name, sortorder) in table
  `local_learningplans_stg`; `local_learningplans_crs.stageid` FK replaces
  `stagename` (dropped by upgrade 2026071204 after migrating one stage per
  distinct name per plan).
- Stages belong to the **learning_plan aggregate**: no separate repository —
  `learning_plan_repository` owns them. Invariants enforced centrally in the
  adapter's `apply_structure()`: stage blocks stay contiguous in the global
  course order (grouped by first appearance), stage `sortorder` mirrors block
  order, and a stage that loses its last course is garbage-collected.
- Stage lifecycle is **implicit**: `set_course_stage()`/`add_course()` use
  find-or-create by name ('' = unstaged); there is no separate stage CRUD UI.
- New `restructure_courses(planid, orderedcourseids[], stageids[])` applies a
  full order + assignment in one transaction — this is the drag-and-drop
  contract (each stage renders its own sortable list; the client serialises
  all lists into the two parallel sequences).
- `learning_plan_course::stage_name()` remains as a read-model projection
  hydrated by the repository (LEFT JOIN), so the Student Lab grouping policy
  and the external functions are unchanged.

**Consequences.** Learner-facing behaviour is identical; managers get true
cross-stage drag-and-drop and implicit stage management. Moving a course out
of a plan's last-stage course deletes the stage — recreate it by typing the
name again (find-or-create).
