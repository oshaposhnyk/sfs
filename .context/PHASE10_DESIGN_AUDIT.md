# Phase 10 — Design-vs-implementation audit (2026-07-17)

Method: each prototype in `SecureFood School/` served at :8899 and screenshotted
at 1440px next to its live learner page (SFS mode, uk). Design conformance first,
then functionality. Severity: 🔴 broken/empty · 🟠 fidelity gap · 🟡 minor /
intentional deferral.

Legend for cause: **[deferred]** no data source (accepted) · **[intent]**
deliberate deviation · **[gap]** actionable fidelity gap · **[bug]** defect.

## About — insights.html  ✅ close to 1:1
- Hero, shield card, "Three reinforcing layers", "Learning for Change" (4 steps),
  hubs map + list all match.
- ☑ [fixed] **A-1 Network panel header + lede added** (settings-driven).
- 🟠 [was] **A-1 Network panel header missing.** Prototype's side panel has a
  "Network" heading + lede ("SecureFood spans 21 partners… Click a row to focus
  the map."); live lists the labs with no heading/description.
- 🟡 [intent] Help "?" in the top bar is settings-driven (hidden by default);
  prototype always shows it. Map has no country labels (bundled GeoJSON).

## Student Lab — l4c.html  ✅ structurally 1:1
Kicker/title/lede, active-plan bar, staged course cards (Done/Active/Locked),
stage headings — all match.
- 🟡 [deferred] Per-course cover icons, per-course/stage effort ("2h 40m",
  "8h effort"), plan **Level** stat ("L3"), the "Activity" button beside
  "Continue learning", and the "Module X of Y" line — none have a data source
  (Phase 8 S2/S4, ADR-008). Module count is shown instead of effort.

## Future Food — ffs.html  🟠 several gaps + one intentional deviation
- Hero, achievements grid, mission cards, decision section all present.
- 🟠 [deferred] **FF-1 Hero extras missing:** "Leaderboard" button and the
  "N-day streak" chip (no streak/leaderboard backend).
- ☑ [fixed] **FF-2 Per-badge XP** via a badgexp name→XP settings map (FF-2).
- 🟠 [deferred] **FF-3 "New!" badge state + notification pip** on a freshly
  earned badge — no "recently earned" concept.
- ☑ [fixed] **FF-4 Per-mission progress** — course-linked missions show the
  learner's real course completion % as a bar (FF-4).
- 🟠 [intent] **FF-5 Decision Point differs by design (P7 owner decision).**
  Prototype = an in-page multiple-choice question ("A supplier offers cheaper
  vegetables… What do you do?" → Buy / Reject / Ignore). Live = a scenario
  "router": each choice opens a real Moodle activity and reflects its completion
  ("Decided"). Deliberate (real assessment in Moodle), but it is NOT the
  prototype's inline MCQ. Owner may want the inline MCQ too.
- Level size: prototype uses 1000 XP/level; live default 500 (now configurable,
  P10). Real per-user XP vs mock totals — expected.

## Resources & Standards — governance.html  🔴 largest divergence
Prototype: "System operational" pill + "Download report", 3 KPI cards, 4
Management-tool cards with counts, a 7-row resource library with audience tags +
downloads + role filters, a "Quick validation" card and side stats.
Live (learner): only the "Resource library" card with the single uploaded PDF.
- ☑ [fixed] **R-1 Learner page populated.** Demo curated documents + KPI JSON
  set (data/config) so the learner page mirrors governance.html; management
  tools remain staff-only. Original cause below kept for reference.
- 🔴 [was] **R-1 Learner page is nearly empty.** Causes: KPI cards default
  empty (no-mock hardening, P11); management tools are `learningplans:manage`-only
  (correctly hidden from learners); curated documents default empty (no-mock), so
  only real uploads show; audience filters need docs to exist. Net effect: the
  learner sees an almost-blank page unlike the rich prototype.
- ☑ [fixed] **R-2 filter label.** The "All" filter used core get_string('all'),
  which renders "Вибрати все" (Select all) in uk; replaced with a plugin
  string filter:all (All / Всі).
- 🔴 [was] **R-2 Unexpected "Вибрати все" (Select all) control** top-right of the
  library card — not produced by the current template (filters render as outline
  pills). sfsresources appears to be mid-refactor by the parallel session
  (`showlibrary` / `showfilters` toggles are new). Needs investigation.
- Note: "System operational" pill, "Download report", "Quick validation" and side
  stats have no data source (deferred).

## Course — course.html  ✅ 1:1 (modulo deferred rail cards)
Banner, plan-context strip, sections with per-section "done/total" fractions,
completion pills, right-rail "Course info" (sections/activities/progress/lang/
format) — match (Phase 8 C1/C2).
- 🟡 [deferred] Right rail "Next up" and "Teachers" cards render only when there
  is data (hidden here: course 100% complete, no teacher enrolled). Prototype's
  mentor/peers/resources rail cards have no data source.

## Functionality — matches the intended model
- Missions award XP only via completion-tracked activities (P4); course/external
  links show no XP. ✓
- Decision reflects real activity completion (P7). ✓
- Badges earned by course-completion criteria (P9); XP deduped (P8) and
  configurable (P10). ✓
- Stages are a first-class entity with rename + cross-stage drag (P1, ADR-011). ✓
- Cohort→plan auto-enrolment and badge award verified via cron earlier. ✓

## Actionable backlog (design conformance)
- ☑ **R-2** filter label fixed (filter:all).
- ☑ **R-1** learner Resources page populated (curated docs + KPI, data).
- ☑ **A-1** About "Network" heading + lede added.

Remaining — each needs a data source or a design decision, so NOT done as a
plain fidelity fix (implementing would mean fabricating data the project
deliberately avoids):
- ☑ **FF-2 per-badge XP** — done: `badgexp` JSON setting maps a badge name to
  its XP (unlisted badges use the flat xpperbadge); xp_policy takes a summed
  badge-XP override. Demo map set (Guardian 25 / Eco Pioneer 30 / Lab Rookie
  15 / …). Verified: earned badges show varied XP; total recomputes.
- ☑ **FF-4 per-mission progress bar** — done: a course-linked mission shows the
  learner's real course-completion % (mission_completion::course_progress_for_url);
  activity/external missions have no % (binary) so no bar, avoiding fake data.
- ❓ **FF-5 inline MCQ decision** — owner already chose the activity-router (P7);
  adding the prototype's inline multiple-choice is an additional feature, not a
  fix. Decide if both are wanted.
- 🟡 **FF-1 / FF-3 / Student-Lab extras / Course rail cards** — deferred, no data
  source: streak, leaderboard, "New!" state, per-course icons, effort/duration,
  plan level, mentor/peers/resources cards. Building these means inventing a
  backend, out of scope for a fidelity pass.
