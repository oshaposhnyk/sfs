# Domain 07 — future-food / gamification (Phase 4)

Status: `[ ]` not started.

## Purpose

Gamified "Future Food" page. Prototype: `ffs.html` (hero with level + XP bar,
achievements row, mission grid, decision module).

## Scope decision (v1 = badges-backed, no custom XP engine)

| Prototype element | v1 implementation |
|-------------------|-------------------|
| Achievements row | Moodle badges API: earned + locked (criteria preview); `sfs-achievement` |
| Level + XP bar | derived: XP = weighted sum of earned badges + completed courses (pure policy class, unit-tested); level thresholds in settings |
| Mission grid | curated list (theme/plugin setting) linking to real courses/activities with duration + XP labels |
| Decision module (scenario) | link into a real quiz activity styled as `sfs-decision`; **no quiz engine in the theme** |
| "Mission: Zero Hunger" hero | settings-driven content |

A real XP/levelling engine (events → points ledger) is a **future `local_` plugin**
— do not build it inside the theme. If the owner wants it, open a new domain.

## Architecture

- Theme page (`/theme/securefood/pages/futurefood.php`-style entry is NOT allowed —
  use a proper page: recommend a small `local_` page or reuse frontpage-style
  layout on a theme-owned route via standard page + capability `theme` pages are
  not standard: **decision needed** — likely thin `local_sfsgame` plugin housing
  page + policy; record ADR when decided).
- XP/level policy = pure domain class regardless of home.
- Badges data via core badges API only.

## Tasks

- [ ] ADR: where the page + XP policy live (recommend thin `local_sfsgame`).
- [ ] Badge issuers/criteria set up for the pilot cohort (admin task, document).
- [ ] Renderables + templates + SCSS (`_ffhero.scss`, `_xp.scss`,
      `_achievement.scss`, `_mission.scss`, `_decision.scss`).
- [ ] Level policy + PHPUnit.
- [ ] Empty states (no badges yet, no missions configured).

## Acceptance criteria

- Matches `ffs.html`; achievements reflect real badge state per user.
- Locked badges show criteria without leaking restricted info.
- No fake numbers: XP/level derived reproducibly from real data.

## Dependencies

Phases 1–2; badges enabled site-wide.
