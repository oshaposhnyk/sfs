# Domain 11 — customisation-settings (Phase 1–2, cross-cutting)

Status: `[~]` Phase 1 slice done (2026-07-12): tabs page live with **Colours**
(40 token overrides, colour pickers + rgba text fields for translucent dark
tints; emission via `theme_securefood\tokens::overrides_css()`, E2E-verified)
and **Advanced** (raw pre/post SCSS). 2026-07-13 slices complete: base typed
`settings_provider` + Brand display-name setting; About/Pages content provider
port; Advanced custom footer HTML; Navigation JSON save-time validation and
capability visibility; About page/section toggles; Brand logo file serving.
Remaining: broader Student Lab/Future Food/Resources content controls.
Decision record: ADR-007.

## Purpose

The no-code customisation layer: **colours, texts, navigation, blocks and page
content are all admin-configurable**, with the prototype design as defaults.
Every other UI domain consumes this infrastructure — nothing user-visible may be
hard-coded.

## Settings catalogue (admin tabs page)

### Tab: Brand
- Logos: full/icon × light/dark (file settings; defaults = theme pix), favicon.
- Site display name used by the shell.

### Tab: Colours
- One colour-picker setting per design token, light **and** dark variants:
  `bg`, `bg2`, `surface`, `surface2`, `ink`, `ink2`, `muted`, `muted2`, `line`,
  `linestrong`, `primary`, `primary700`, `primary50`, `accent`, `accent50`,
  `teal`, `teal50`, `success`, `warn`, `danger`.
- Implementation: `theme_securefood_get_pre_scss()` emits only the overridden
  values as CSS custom-property overrides (light in `:root`/`.sfs-mode`, dark in
  `[data-theme="dark"]`); unset = token defaults from `_tokens.scss`.
- "Reset to SecureFood palette" guidance in setting descriptions.
- Warning in help text: admins own contrast when overriding (we validate defaults
  only; consider a contrast hint in the setting description).

### Tab: Navigation
- Structured sidebar menu setting (repeatable rows or validated JSON textarea v1):
  section label, item title (multilang), URL/internal route, Material icon name,
  visibility (all / capability / logged-in), order.
- Defaults = prototype nav (About / Student Lab / Future Food / Resources &
  Standards / Notifications / Messages).
- Toggle per built-in item rather than forcing admins to rebuild the whole menu.

### Tab: Pages & content
- Per custom page (About, Student Lab, Future Food, Resources): enable/disable
  page, per-section toggles, and content settings —
  hero kicker/title/lede (HTML editor, multilang via filters), KPI definitions,
  hub list JSON, feed fallbacks, footer text.
- Empty-state copy overrides.
- All rendered through `format_text()` with filters so `{mlang}` works.

### Tab: Blocks
- Custom-mode layouts expose real block regions: `side-pre`, `content-top`,
  `content-bottom` (styled as `sfs-card`).
- Settings: show/hide side region per page type; default blocks documented.

### Tab: Advanced
- `rawscsspre` / `rawscss` (Boost pattern).
- Mode settings (from domain 02: forcemode, defaultmode, login-page mode).
- Custom footer HTML.

## Architecture rules

- A single `theme_securefood\settings_provider` (or config value-object reader)
  wraps `get_config('theme_securefood')` — renderables read typed values from it,
  never `get_config()` scattered around. Unit-test default fallbacks.
- Every setting has a sane default = the prototype design; a fresh install with
  zero configuration must look exactly like the prototypes.
- Colour settings invalidate theme caches on change (`theme_reset_all_caches()`
  via `updatedcallback`).
- Nav model (domain 01) is built from the Navigation settings; capability
  visibility enforced server-side.
- File settings served via theme `pluginfile` handler with proper context checks.

## Tasks

- [x] Tabs settings page skeleton + base `settings_provider`
      (2026-07-12/13). Provider now owns Brand display name, Help URL,
      navigation JSON, forced/default mode reads; broader typed reads still
      need to be expanded across all page content settings.
- [x] Colour settings → extra-SCSS emission + `theme_reset_all_caches`
      invalidation; override verified end-to-end in compiled CSS, both schemes
      (2026-07-12).
- [x] Brand/logo settings + pluginfile serving (2026-07-13). Brand display
      name, full/icon logos for light/dark schemes, favicon, shell/fav icon URL
      resolution and system-context `pluginfile` serving are live.
- [~] Navigation settings model + validation + integration with domain 01 nav.
      Runtime integration reads through `settings_provider`; save-time JSON
      validation and capability-based visibility added (2026-07-13). Structured
      repeatable admin UI still deferred.
- [~] Pages/content settings (per-page groups) + `format_text` rendering path.
      About page settings now read through `settings_provider` (2026-07-13);
      About page/section toggles live. Broader controls for Student Lab/Future
      Food/Resources still pending.
- [x] Block regions in `layout/sfs*.php` + block card styling (2026-07-13).
      SecureFood shell layouts expose `content-top`, `side-pre` and
      `content-bottom`; Blocks tab can hide/show each rendered slot.
- [x] Advanced tab — raw SCSS pre/post (2026-07-12); custom footer HTML
      setting/rendering (2026-07-13).
- [~] Langs en/uk for every setting name + help text. Current live settings
      covered; future Student Lab/Future Food/Resources controls still pending.
- [ ] Behat: change a colour + a hero title + hide a nav item → assert rendered.

## Acceptance criteria

- Fresh install with no settings = pixel-match with prototypes.
- Admin can: recolour the whole UI, rename/hide any nav item, edit every hero/
  footer text, toggle any custom page/section, add a block — all without code.
- No user-visible hard-coded copy or hex colour in any page domain (grep-able).

## Dependencies

00 (tokens/scss pipeline). Feeds 01, 02, 04, 05, 06, 07, 08.

## Open questions

- [ ] Navigation setting UX: repeatable admin settings vs JSON textarea v1
      (recommend JSON with validation v1, structured UI later).
- [ ] Should colour overrides also remap Boost/Bootstrap brand variables in
      standard mode, or is customisation custom-mode-only? (Recommend
      custom-mode-only; standard mode stays stock Boost — matches ADR-002.)
