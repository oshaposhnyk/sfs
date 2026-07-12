# Domain 05 — student-lab (Phase 3, core page)

Status: `[~]` core done (2026-07-12, ADR-008): page lives in the plugin
(`my.php` → rewritten controller → `studentlab_page.mustache`, `lp-*` BEM),
skinned by the theme under `.sfs-mode`. E2E-verified with real data, both
schemes. v1 = single course grid; stage grouping, effort, level and module
count deferred pending owner decisions (see ADR-008 and open questions).

## Purpose

The learner's learning-plan dashboard — the product's central page. Prototype:
`SecureFood School/l4c.html`. BEM map: `design/components.md` (Student Lab section).
Data flows exclusively through `local_learningplans` use cases (ADR-005) — the
theme renders, the plugin decides.

## UI composition

1. **Page head**: kicker "My learning", title, lede, actions: Activity (ghost) +
   Continue learning (primary).
2. **Plan bar** (`sfs-planbar`): pin icon, "Active learning plan" label, plan name
   with switcher dropdown, stats (courses done/total, completion %, level),
   progress bar + "Stage X of Y".
3. **Stage sections** (`sfs-stage`): status badge (number / done tick / lock),
   title, meta (course count · effort · status), course-tile grid.
4. **Course tiles** (`sfs-coursetile`): cover variant, course code tag, status
   chip, icon, title, description, module count + duration, progress bar,
   state text + action (Review/Resume/Start; locked = reason text, no link).

## Data contract (needs domain 09)

| UI element | Source |
|------------|--------|
| Active plan + switcher list | memberships list use case + active-plan preference |
| Done/total, %, current stage | `get_user_learning_plan_progress` / overview read model |
| Level (`L3`) | plan metadata — **open question**: field does not exist yet |
| Stage status | derived: all done → done; any started/unlocked → in progress; sequencing not released → locked |
| Tile: title/desc | course fullname/summary (`format_string`/`format_text`) |
| Tile: module count | course modinfo (visible activities) |
| Tile: effort/duration | **open question**: custom course field vs plugin column |
| Tile: progress % | completion reader port |
| Tile: action URL | course view URL via URL resolver port |
| Continue learning | new use case: first active course → first incomplete → first review-able |

## Architecture

- Page = SecureFood-mode replacement for `local_learningplans/my.php` flow:
  thin controller → use cases → renderable (`output\studentlab\page`) →
  `templates/studentlab/*.mustache`.
- Plan switch + Continue learning as external functions (domain 09) consumed via
  `core/ajax`; both also work as plain links/forms (progressive enhancement).
- Empty states: no plan (call-to-action card), plan without courses, all locked.

## Tasks

- [ ] Renderable + template set (page, planbar, planmenu, stage, coursetile).
- [ ] SCSS components `_planbar.scss`, `_planmenu.scss`, `_stage.scss`,
      `_coursetile.scss` (cover variants from tokens).
- [ ] AMD: plan switcher menu (accessible menu pattern), continue-learning action.
- [ ] Wire data via domain 09 use cases; no direct `$DB` anywhere in theme.
- [ ] Empty/loading/error states.
- [ ] PHPUnit on renderable context; Behat: learner sees plan, locked tile has no
      link, switcher changes active plan.

## Acceptance criteria

- 1:1 visual match with `l4c.html` (light + dark, three breakpoints).
- All numbers real (completion API), consistent with `local_learningplans/view.php`.
- Locked course not reachable: UI shows no link **and** direct URL access denied
  (enrolment not released — verify, this is plugin behaviour).
- Keyboard: switcher menu operable, tiles focusable in DOM order.

## Dependencies

Phases 0–2 complete; domain 09 read models.

## Open questions

- [ ] Learner "level" (L1–Ln): plan field, derived from progress, or dropped in v1?
- [ ] Effort/duration per course: custom course field (recommended) or plugin table
      column? Decide with owner; record as ADR.
- [ ] "Activity" button target: Moodle logs-based recent activity page (phase 4?).
