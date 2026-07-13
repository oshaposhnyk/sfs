# Domain 04 — dashboard-insights / "About the Project" (Phase 4)

Status: `[~]` v1 implemented; Moodle WS feed updates live
(2026-07-13). Living Labs static fallback markers are labelled links tied to
the hub list with `:target` highlighting; live Leaflet markers expose
keyboard/ARIA semantics and synchronise hover/focus/click with the hub list.
The "Latest from the network" feed can now be replaced by a standard Moodle
External Services function.

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
| Updates feed | `sfs-feedcard` | Moodle WS live payload → curated settings fallback → hidden when empty |

## Architecture

- `layout/sfs_frontpage.php` (or dashboard override — decide with owner which URL
  is "home" in custom mode: `/` vs `/my`; recommend `/my` → Student Lab redirect
  for learners, About page as site front).
- Renderable per section; settings via `settings.php` (structured repeatable
  settings or one JSON textarea validated on save — prefer admin-friendly fields).
- Filters on feed (All/Research/Community…) — client-side filtering, AMD.

## Tasks

- [x] Hide core frontpage fallback content in SFS About mode (2026-07-14):
      owner reported Moodle "Available courses" rendering below the designed
      About/home content; suppressed core `main_content` only when the settings-
      driven SFS About page is active.

- [~] P1 fidelity slice 1 (2026-07-12): L4C mission band implemented,
      settings-driven and localised; 17/17 theme tests + desktop browser pass.
      Mobile grid overflow fix awaits cache purge and repeat screenshot.

- [x] Latest from the network Moodle WS (2026-07-13): external service can
      replace the About-page feed cards through a standard Moodle External
      Services function; payload is small presentation data stored via Config
      API with settings/manual fallback. Prototype mock cards are not used as
      live fallback; the feed hides when both real sources are empty.

- [x] P0 hardening (2026-07-12): moved built-in About fallback copy to matching
      en/uk language keys. Live uk QA awaits installation of the core pack.

- [ ] Owner decision: home-page routing per role (learner vs visitor vs manager).
- [ ] Renderables + templates + SCSS per block (see `design/components.md`).
- [ ] Theme settings for hero/KPIs/hubs/feed fallbacks (en/uk help strings).
- [x] Map with accessible hub markers: static fallback markers are labelled
      links tied to hub-list rows with `:target` highlighting; live Leaflet
      markers expose keyboard/ARIA semantics and synchronise hover/focus/click
      with the hub list.
- [x] Feed adapter: Moodle WS payload → cards (`chip`, `title`, `text`,
      `time`, `variant`, optional `url`) with settings/manual fallback and no
      mock design-data fallback.
- [ ] Feed adapter: announcements → cards (author, category chip, relative time)
      remains optional/backlog if the owner wants Moodle forum announcements
      instead of external network updates.
- [ ] Behat smoke: page renders for guest and learner without capability leaks.

## Acceptance criteria

- Matches `insights.html` visually; all content manageable without code edits.
- Map fully keyboard-accessible; statuses text-labelled.
- Page renders under 200ms server time with caches warm (it is the landing page).

## Dependencies

Phases 1–2. Plugin stats use case optional (can ship with settings-only data).
