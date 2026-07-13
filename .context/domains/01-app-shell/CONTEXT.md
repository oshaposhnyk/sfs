# Domain 01 — app-shell (Phase 2)

Status: `[ ]` not started.

## Purpose

The SecureFood application shell rendered on every page in custom mode: sidebar
(brand, nav, user card), topbar (toggle, breadcrumbs, search, actions), content
container, responsive drawer. Behavioural reference: `SecureFood School/js/shell.js`;
visual reference: `styles.css` "APP SHELL" section; BEM map: `design/components.md`.

## Architecture

- Layout files `layout/drawers.php`-replacement: `layout/sfs.php` (+ variants
  `sfs_frontpage`, `sfs_course`, `sfs_incourse`) used **only** in SecureFood mode
  (domain 02 owns the switch). Standard/admin layouts stay Boost.
- `templates/shell/…`: `sidebar.mustache`, `topbar.mustache`, `usercard.mustache`,
  `nav_item.mustache`, composed by an output/renderable class
  (`theme_securefood\output\shell`) that builds the nav model.
- Nav model: array of sections/items (component, url, icon, active flag) —
  built from domain 11 navigation settings (defaults = prototype nav), filtered
  by capability checks (e.g. hide Student Lab if plugin absent); active detection
  from `$PAGE->url`/pagetype.
- AMD `amd/src/shell.js`: sidebar collapse (persist via domain 03 preference),
  mobile drawer + backdrop, tooltip on collapsed items, Escape handling.
- Breadcrumbs from `$PAGE->navbar` mapped to `sfs-breadcrumbs`.
- Search box submits to `/search` (global search) — progressive enhancement only.
- Notifications/messages buttons reuse core popover web services; restyled.
- User card: real user (initials avatar fallback, fullname via fullname()), role
  line = primary cohort name + role archetype; links to profile/preferences.

## Behaviour spec (from shell.js — must match)

- Desktop toggle: collapse to 76px, labels hidden, icon tooltips (`data-tip`
  equivalent), full logo ⇄ icon logo swap; state persists.
- Mobile (≤820px): sidebar becomes fixed drawer with backdrop; toggle opens/closes;
  backdrop click closes; collapsed state ignored (always expanded in drawer).
- Toggle icon: `menu_open` when expanded, `menu` when collapsed (desktop ≥821px).
- Skip-to-content link added (a11y improvement over prototype).

## Tasks

- [x] Sticky footer shell alignment (2026-07-14): Boost's fixed `.stickyfooter`
      form actions start at viewport `left: 0`, which clips action buttons under
      the SFS sidebar; aligned it to expanded/collapsed/mobile shell columns.

- [x] Core user/message admin-layout shell coverage (2026-07-14): owner reported
      unstyled profile edit, language, notification preferences and forum
      preference pages plus narrow notification detail headers; implemented via
      explicit user-facing allowlist and scoped global form/header polish.

- [x] Messaging/settings visual polish (2026-07-14): restyled topbar
      notification popover, `/message/index.php` message app and the custom
      settings hub to match the SFS card language.

- [x] Sidebar language submenu click hardening (2026-07-13): added a scoped
      SFS shell carousel bridge for the native `<details>` user menu so clicks
      on nested icons/labels work and submenu state resets on close.

- [x] User preferences/settings page styling (2026-07-13): restored
      `/user/preferences.php` to the SFS shell via an explicit admin-layout
      whitelist while keeping profile/admin edit forms on Boost.

- [x] Sidebar user-menu item styling (2026-07-13): replaced the raw core
      dropdown-item rendering in the sidebar popover with SFS BEM rows and
      per-item icons while preserving Moodle user-menu semantics/submenus.

- [x] Sidebar user-menu dropdown positioning (2026-07-13): force stable
      desktop expanded/collapsed and mobile drawer positioning for the Moodle
      user context menu inside the SecureFood sidebar.

- [x] SFS topbar/collapsed-menu polish (2026-07-13): hide the edit-mode switch
      in SecureFood mode and make the collapsed sidebar user menu open as a
      right-side context menu instead of overlaying the collapsed rail/content.

- [x] Sidebar user menu correction (2026-07-13): move the standard Moodle user
      context menu from the SecureFood topbar into the sidebar user card and
      remove the duplicate topbar avatar/user icon.

- [x] P0 hardening (2026-07-12): fixed blank guest Login styling in dark/system
      mode; hidden mobile drawer is now inert and its ARIA state stays synced.

- [ ] `output\shell` renderable + nav model (+ unit tests on context array).
- [ ] Mustache templates for sidebar/topbar/usercard/backdrop.
- [ ] `layout/sfs.php` wiring Boost internals (standard blocks region optional).
- [ ] SCSS: `layout/_shell.scss`, `_sidebar.scss`, `_topbar.scss`, `_content.scss`.
- [ ] AMD `shell.js` (no jQuery; preference write via `core/ajax`).
- [ ] Keyboard + SR pass: landmarks, aria-expanded on toggle, focus trap in drawer.
- [ ] Behat: nav renders, active item correct, collapse persists across reload.

## Acceptance criteria

- Every non-admin page in custom mode renders inside the shell without layout
  breakage (test: dashboard, course, mod, profile, preferences pages).
- Matches prototype pixel-close at 1440/1024/375px, light + dark.
- Zero console errors; works with JS disabled (expanded sidebar, no drawer).

## Dependencies

00 (tokens/fonts), 03 (preferences for collapse persist). Blocks all page domains.

## Open questions

- [x] Block regions in custom mode → **kept** (`side-pre`, `content-top`,
      `content-bottom`), styled as cards — ADR-007, implemented via domain 11.
- [x] Nav configurability → settings-driven from the start (ADR-007, domain 11).
