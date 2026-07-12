# Domain 06 — course-experience (Phase 3)

Status: `[~]` v1 done (2026-07-12): CSS-only restyle (`components/_course.scss`),
**no template overrides** — course banner from core `#page-header`, flat
secondary-nav tabs, section cards, activity rows with icon tiles and pill
completion buttons, activity-page banner/typography/navigation. Verified with
screenshots (light + dark) on a seeded course incl. a real view-completion
round-trip (To do → Done). Owner decisions applied 2026-07-12: Moodle chrome (secondary nav tabs,
collapse-all, section chevrons) hidden in SFS mode — staff manage via standard
mode; sections always expanded; content full-width (no max-width). Mobile pass
done (compact banner/sections/rows).
Remaining: right rail (progress/teacher/info),
plan-context chips (needs domain 09 read model), Bootstrap link-colour
tokenisation, big-five mods visual pass (only mod_page verified), Behat.

## Purpose

Course and activity pages in SecureFood mode. Prototypes: `course.html`
(banner, section cards, progress rail) and `activity.html` (player look).
Strategy: restyle standard Moodle course/mod rendering — do **not** rebuild a
custom player (see `design/page-mapping.md`).

## Course page

- `layout/sfs_course.php` + course format template overrides (topics format):
  banner (`sfs-coursebanner`): title, chips (plan/stage context via plugin use
  case, category), meta (modules, effort), due pill (nearest due date from
  calendar/completion expectations).
- Section cards (`sfs-coursesection`): number, title, per-section completion
  fraction, activity rows.
- Activity rows (`sfs-activityrow`): mod icon, name, meta (type, duration if
  available), state (done tick / active / locked by availability), action arrow.
  Availability restrictions render as the lock reason (core `availability` info).
- Right rail (`sfs-courserail`): progress ring/track + fraction + stats, teacher
  card (first editing teacher: avatar, name, message link), info list (effort,
  cohort, plan link).

## Activity page

- `layout/sfs_incourse.php`: shell + narrow content column; standard mod output
  restyled via scoped SCSS (typography, cards, quiz question blocks per
  `activity.html` look). Prev/next from core activity navigation, restyled.
- The prototype's custom quiz/summary screens map to core quiz review — style,
  don't fork.

## Tasks

- [ ] Course format template overrides (document each override + Boost source
      version in this file — upgrade hotspot).
- [ ] Renderables for banner/rail data (plan context via `local_learningplans`
      use case; teacher via role assignments; completion via completion API).
- [ ] SCSS: `_coursebanner.scss`, `_coursesection.scss`, `_activityrow.scss`,
      `_courserail.scss`, `_activitypage.scss` (mod content restyle, incl. quiz).
- [ ] Verify the big five mods render acceptably: page, quiz, assign, h5pactivity,
      forum — light/dark, three breakpoints.
- [ ] Behat: completion tick updates section fraction; restricted activity shows
      lock reason and is not launchable.

## Acceptance criteria

- Course page matches `course.html` closely with real sections/activities.
- No mod functionality regressions (editing mode must remain usable — editing may
  fall back to plainer styling but must work).
- Availability/locking always mirrors server truth.

## Dependencies

Phases 0–2; plugin plan-context read model (domain 09).

## Open questions

- [ ] Which course format is canonical for content (topics assumed) — confirm.
- [ ] Editing mode in custom layout: full support or auto-fallback to standard
      Boost layout while editing (recommend fallback v1)?
