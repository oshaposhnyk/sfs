# Domain 06 — course-experience (Phase 3)

Status: `[x]` done (updated 2026-10-09 per ADR-014): pivoted from invasive CSS restyle
to **Standard Moodle course/mod visual with SecureFood accent colour theming**.
Native course formats (accordion collapse/expand, section chevrons, bulk select,
secondary nav tabs) and native activity icons (retaining their semantic purpose colours)
are preserved 100% untouched. SFS styling applies brand tokens (primary teal, accent amber,
completion badges, links, focus rings, dark scheme Bootstrap tokens). Plan context strip
(`local_learningplans`) and course rail (`courserail`) remain active.

## Purpose

Course and activity pages in SecureFood mode (ADR-014).
Strategy: Preserve standard Moodle course and module rendering for maximum reliability,
accessibility, and compatibility across course formats and all activity types (Quiz, H5P,
Assign, Forum, etc.), applying SecureFood design tokens purely as an accent colour layer.


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
