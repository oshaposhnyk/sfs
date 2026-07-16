# Phase 9 — Functional audit of the new features (2026-07-16)

Logical review of the recently built functionality: where each is configured,
how it is added, how the learner uses it, and the problems found with fixes.
Status: ☐ open · ◐ in progress · ☑ done (verified).

Priority: 🔴 high (misleading / wrong data) · 🟠 medium (UX / management) ·
🟡 low (docs / by-design).

## Tasks

- ☐ 🔴 **P4 — Mission XP never awards for a course URL.**
  `mission_completion::state_for_url` maps only `/mod/.../view.php?id=cmid`
  to a completion; a `/course/view.php?id=X` link is `untracked`, so the
  advertised mission XP is never granted. Fix: settings-time validation /
  warning when a mission URL is not a completion-tracked activity; document
  the requirement in `missions_desc`. (Config-shape change — discuss first.)

- ☑ 🔴 **P8 — XP double-counts a course that is in several plans.**
  `index.php` sums `get_user_progress(plan)->completed_courses()` per
  membership, so a course in 2 plans = 2×50 XP. Fix: count DISTINCT completed
  course ids across the learner's plans.

- ☐ 🟠 **P9 — Demo achievement badges can't be earned by activity.**
  The seeded badges use a manual "award by Manager" criterion, so a learner
  never earns them by doing anything. Fix (production): switch to course /
  activity completion criteria (as done for Pathfinder).

- ☐ 🟠 **P1 — No whole-stage rename / stage ordering on the plan page.**
  Stage grouping is by exact name match, so renaming means retyping the name
  on every course in the stage; a typo splits the stage. Fix: a stage-level
  rename (updates all courses by stageid, not by name) + order controls;
  `learning_plan_stage` already models it, needs `rename_stage()` + UI.

- ☑ 🟠 **P5 — Mission status chip is static admin text, not learner state.**
  The "New mission" / "In progress" chip is the same for everyone and can
  contradict the learner's real completion. Fix: derive the chip from the
  real completion state (completed / in progress / not tracked), or drop the
  static chip and keep it purely decorative.

- ☑ 🟡 **P12 — Hub map: lat/lon undocumented in the setting hint.**
  A marker only renders with numeric `lat`/`lon`, but `abouthubs_desc` shows
  only name/country/type. Fix: add lat/lon to the example (en+uk).

- ☐ 🟡 **P7 — Decision module is a router, not an assessed decision.**
  A click records nothing and awards nothing. By design (routes to real
  activities); align the copy/label with the behaviour, or wire choices to a
  real choice/quiz activity if a genuine decision is wanted.

- ☐ 🟡 **P11 — Resources KPI numbers are static admin JSON.**
  Risk of mock-as-fact; default is already empty. Fix (optional): bind to
  real metrics (e.g. staff trained = L4C completions).

- ☐ 🟡 **P10 — XP constants (100/50/500) are hard-coded.** Optional: expose
  as plugin settings.

- ☐ 🟡 **P6 — Mission link may target a course the learner can't access.**
  Fix (with P4): check access and hide/disable Start when unreachable.

- ☐ 🟡 **P13 — Standard-navbar plugin icons are bare currentColor SVGs.**
  Cosmetic; acceptable (matches the long-standing graduation-cap icon).

## Batch plan

1. Low-risk code — ☑ DONE 2026-07-16: **P8** (distinct completed courses; verified 700→650 XP), **P12** (lat/lon in hint), **P5** (chip from real state, admin label fallback).
2. Discuss config-shape changes before: **P4**, **P9** (change how admins
   set missions / badge criteria).
3. Backlog: P1 (stage UI), P7, P11, P10, P6, P13.
