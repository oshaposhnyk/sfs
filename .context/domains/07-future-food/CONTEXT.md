# Domain 07 — future-food / gamification (Phase 4)

Status: `[~]` v1 implemented (2026-07-13) — badges, missions and decision links are real-data backed; browser QA and the real decision badge activity/content remain pending.

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

- Page host is `public/local/sfsgame` (ADR-012). The theme only skins it.
- XP/level policy is pure domain code in `local_sfsgame\domain\xp_policy`.
- Badges data via core badges API only.

## Tasks

- [x] P0 hardening (2026-07-12): localised hero fallbacks and stopped rendering
      prototype missions as live content; an honest empty state now appears.

- [x] P1 decision slice (2026-07-13): real activity links + empty state,
      settings/localisation/tests. Browser QA skipped by owner request.

- [x] Locked badge criteria preview (2026-07-13): safe preview text now
      renders for locked achievements from the Moodle badges model; multilang
      course names are filtered before HTML is stripped.

- [x] ADR: where the page + XP policy live (thin `local_sfsgame`, ADR-012).
- [x] Badge issuers/criteria set up for the current pilot path (2026-07-13):
      `Pathfinder` is an active site badge with courseset completion for
      `SFS101`, issued to `sfstestuser`; operator guide lives in
      `BADGE_SETUP.md`. Decision badge remains deferred because no real
      quiz/choice activity exists yet.
- [x] Renderables + templates + SCSS (2026-07-13): Future Food markup/SCSS
      normalised from legacy `ffg-*` to `sfs-*` BEM; XP inline width replaced
      by a native `<progress>` element. Browser QA skipped by owner request.
- [x] Level policy + PHPUnit: `local_sfsgame\domain\xp_policy` derives XP,
      level and level progress from earned badges + completed courses.
- [x] Empty states (no badges yet, no missions configured).

## Acceptance criteria

- Matches `ffs.html`; achievements reflect real badge state per user.
- Locked badges show criteria without leaking restricted info.
- No fake numbers: XP/level derived reproducibly from real data.

## Dependencies

Phases 1–2; badges enabled site-wide.
