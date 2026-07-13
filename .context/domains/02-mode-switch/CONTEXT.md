# Domain 02 — mode-switch (Phase 2)

Status: `[ ]` not started.

## Purpose

The single intended difference from the design: a switch between **Standard mode**
(stock Boost experience) and **SecureFood mode** (custom shell + pages).
Decision record: ADR-002.

## Behaviour

- User preference `theme_securefood_uimode`: `standard` | `securefood`.
- Admin setting `theme_securefood/forcemode`: `user choice` | `force standard` |
  `force securefood` (site-wide override; user control disabled when forced).
- Default for new users: admin setting (`defaultmode`), initially `securefood`.
- Admin/maintenance/install/login-error pages: always Boost layouts (safety).
- Login page: admin decides (setting), default standard.

## Mechanics

- Central resolver `theme_securefood\mode_manager` (small, unit-testable —
  preference + settings in, effective mode out; wrap globals behind a thin reader
  so logic is testable).
- `config.php` maps layouts; a hook early in page setup swaps layout + adds
  `sfs-mode` body class when effective mode = securefood
  (Boost pattern: `body_attributes` / `$THEME->extraclasscallback`-style callback
  in `lib.php` — confirm exact Moodle 5.2 hook when implementing).
- All custom SCSS scoped under `.sfs-mode` ⇒ standard mode is bit-identical Boost.
- Switch UI: (a) entry in user preferences page (works in both modes, no-JS safe);
  (b) toggle in custom-mode topbar (icon button, AMD writes preference and reloads).
- The switch is per-user and persists across sessions/devices (server-side pref).

## Tasks

- [x] Admin/profile settings shell safety (2026-07-13): keep core
      `pagelayout=admin` pages on stock Boost so Moodle settings/profile forms
      are not wrapped by the custom SecureFood sidebar.

- [ ] `mode_manager` + unit tests (forced modes, defaults, admin pages exclusion).
- [ ] Admin settings (`forcemode`, `defaultmode`, login-page mode).
- [ ] Layout selection wiring + `sfs-mode` body class.
- [ ] Preference registration + Privacy API declaration (user preference!).
- [ ] Preferences-page entry (both langs) + topbar toggle (AMD, aria-pressed,
      confirm-free instant switch with page reload).
- [ ] Behat: user switches mode → Boost markup vs shell markup assertions;
      forced mode hides the control.

## Acceptance criteria

- Standard mode renders stock Boost with zero custom CSS side effects (diff the
  rendered CSS/body classes to prove it).
- Mode survives logout/login and a different browser.
- Admin pages unaffected in both modes.

## Dependencies

00, 01, 03. Blocks nothing in Phase 3 conceptually, but ship with 01.
