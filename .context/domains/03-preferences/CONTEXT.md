# Domain 03 — preferences (Phase 1)

Status: `[~]` server side done (2026-07-12): definitions in
`theme_securefood_user_preferences()` (lib.php) + privacy provider live.
Remaining: rendering (`data-theme`, body classes), AMD `scheme.js`, settings UI —
all land with the shell/layouts in Phase 2.

## Purpose

Server-side user preferences replacing the prototype's localStorage-only state
(ADR-004). Owned by the theme; consumed by shell and mode-switch.

## Preferences

| Preference | Values | Default | Written by |
|------------|--------|---------|------------|
| `theme_securefood_colourscheme` | `light` \| `dark` \| `system` | `system` | topbar toggle (AMD), settings page |
| `theme_securefood_sidebar` | `expanded` \| `collapsed` | `expanded` | shell toggle (AMD) |
| `theme_securefood_uimode` | `standard` \| `securefood` | admin default | domain 02 |
| `local_learningplans` active plan | plan id | — | **plugin-owned** (domain 09), listed here for the map only |

## Mechanics

- Server renders `data-theme` + body classes from preferences → no flash of wrong
  theme; `system` renders a tiny inline-free bootstrapping: server sends
  `data-theme="system"` and CSS `@media (prefers-color-scheme)` fallback layer,
  AMD refines and mirrors to localStorage purely as a first-paint cache.
- Writes via `core_user_set_user_preferences` from `core/ajax` (preferences must be
  whitelisted via the theme's `user_preferences` callback/definition so WS writes
  are permitted and typed).
- Preference definitions validated (choices), Privacy API declares all of them.
- Theme toggle cycles light → dark → system (icon reflects effective scheme).

## Tasks

- [x] Preference definitions + validation + Privacy API declarations (2026-07-12).
- [ ] Server-side rendering of `data-theme`/body classes in layouts.
- [ ] AMD `scheme.js`: toggle, system media-query listener, WS write, local cache.
- [ ] SCSS: dark-scheme custom-property overrides live purely in `_tokens.scss`.
- [ ] Settings UI: Appearance section (light/dark/system radio) on the (restyled)
      preferences page — no-JS path.
- [ ] PHPUnit: definitions, defaults, privacy provider; Behat: toggle persists.

## Acceptance criteria

- Scheme and sidebar state survive re-login and another device.
- No FOUC on either scheme; `system` follows OS switches live.
- Works with JS disabled via the preferences page.

## Dependencies

00. Consumed by 01/02.
