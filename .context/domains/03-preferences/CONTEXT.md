# Domain 03 — preferences (Phase 1)

Status: `[~]` mostly done (2026-07-13): definitions/privacy, server rendering,
shell AMD, no-JS settings UI, and PHPUnit coverage for preference
definitions/defaults/privacy exports are live. Browser/Behat coverage is still
pending.

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
  theme; explicit `light`/`dark` stamp `<html data-theme="…">`, while `system`
  omits `data-theme` so the CSS `@media (prefers-color-scheme)` fallback layer
  owns the effective scheme.
- Writes via `core_user_set_user_preferences` from `core/ajax` (preferences must be
  whitelisted via the theme's `user_preferences` callback/definition so WS writes
  are permitted and typed).
- Preference definitions validated (choices), Privacy API declares all of them.
- Theme toggle cycles light → dark → system (icon reflects effective scheme).

## Tasks

- [x] Core account preference forms SFS styling (2026-07-14): styled Moodle core
      profile edit, preferred language, notification preferences and forum
      preferences pages in the SecureFood shell, keeping mutations handled by
      core forms/endpoints.
- [x] Preference definitions + validation + Privacy API declarations (2026-07-12).
- [x] Server-side rendering of `data-theme`/body classes in layouts.
- [~] AMD shell toggle: preference cycle + WS write live; local first-paint
      cache and Behat/browser persistence checks pending.
- [x] SCSS: dark-scheme custom-property overrides live purely in `_tokens.scss`.
- [x] Settings UI: Appearance section (light/dark/system radio) on the (restyled)
      preferences page — no-JS path.
- [x] Core preferences hub SFS shell/style restored (2026-07-13): allowed
      `/user/preferences.php` back into the shell with an explicit admin-layout
      whitelist and retargeted the card styling to Moodle 5.2 markup.
- [x] Custom settings hub visual polish (2026-07-14): updated
      `theme/securefood/mysettings.php` card markup/styles to match the SFS
      design card language.
- [~] PHPUnit: definitions/defaults/privacy provider covered; Behat: toggle
      persists pending.

## Acceptance criteria

- Scheme and sidebar state survive re-login and another device.
- No FOUC on either scheme; `system` follows OS switches live.
- Works with JS disabled via the preferences page.

## Dependencies

00. Consumed by 01/02.
