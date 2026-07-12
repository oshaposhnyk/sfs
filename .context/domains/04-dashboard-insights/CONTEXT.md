# Domain 04 — dashboard-insights / "About the Project" (Phase 4)

Status: `[ ]` not started.

## Purpose

Landing/dashboard page in SecureFood mode. Prototype: `insights.html`
(hero summary, KPI row, network health, Living Labs map + hub list, updates feed).

## Composition & data (first iteration = mostly curated content)

| Section | Block | Data source v1 |
|---------|-------|----------------|
| Hero ("A shield for the food systems of tomorrow") | `sfs-hero` | theme settings (title, text, stat trio) |
| KPI row (4) | `sfs-kpi` | admin-configured + cheap live counts (users, courses, plans via plugin stats use case) |
| Learning for Change / health card | `sfs-health`, `sfs-signal` | curated signals from settings v1 |
| Living Labs map + hub list | `sfs-map`, `sfs-hublist` | JSON theme setting (name, country, status, coords); map = static SVG Europe map v1 (no CDN, no Leaflet until approved) |
| Updates feed | `sfs-feedcard` | site announcements forum posts (filtered) or curated settings v1 |

## Architecture

- `layout/sfs_frontpage.php` (or dashboard override — decide with owner which URL
  is "home" in custom mode: `/` vs `/my`; recommend `/my` → Student Lab redirect
  for learners, About page as site front).
- Renderable per section; settings via `settings.php` (structured repeatable
  settings or one JSON textarea validated on save — prefer admin-friendly fields).
- Filters on feed (All/Research/Community…) — client-side filtering, AMD.

## Tasks

- [ ] Owner decision: home-page routing per role (learner vs visitor vs manager).
- [ ] Renderables + templates + SCSS per block (see `design/components.md`).
- [ ] Theme settings for hero/KPIs/hubs/feed fallbacks (en/uk help strings).
- [ ] Static SVG map with accessible hub markers (buttons, not areas), synced
      hover/focus with hub list.
- [ ] Feed adapter: announcements → cards (author, category chip, relative time).
- [ ] Behat smoke: page renders for guest and learner without capability leaks.

## Acceptance criteria

- Matches `insights.html` visually; all content manageable without code edits.
- Map fully keyboard-accessible; statuses text-labelled.
- Page renders under 200ms server time with caches warm (it is the landing page).

## Dependencies

Phases 1–2. Plugin stats use case optional (can ship with settings-only data).
