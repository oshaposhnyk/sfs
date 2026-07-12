# Conventions — frontend (BEM / SCSS / Mustache / AMD / a11y)

## BEM

- Pattern: `sfs-block__element--modifier`. Prefix `sfs-` is mandatory on every
  custom class (theme and plugin UI alike).
- Blocks are components from `design/components.md` (e.g. `sfs-planbar`,
  `sfs-coursetile`, `sfs-shell`, `sfs-sidebar`).
- Modifiers express state and variants: `sfs-coursetile--locked`,
  `sfs-coursetile--done`, `sfs-stage--inprogress`.
- JS hooks are separate `data-` attributes or `js-sfs-*` classes — never style them,
  never bind JS to styling classes.
- No descendant-of-descendant selectors deeper than block scope; no styling of bare
  Moodle/Bootstrap classes inside custom components (wrap instead).
- Never copy prototype class names (`.plan-bar`, `.ct-body`, …) — they are the
  *visual* spec, not the markup spec (ADR-003).

## SCSS structure (theme/securefood/scss/)

```
scss/
├── _tokens.scss        # design tokens as SCSS + CSS custom properties (source: design/design-system.md)
├── _fonts.scss         # @font-face (self-hosted)
├── _mixins.scss        # focus ring, card, truncation, media queries
├── preset/default.scss # Boost preset composition
├── components/         # one file per BEM block: _planbar.scss, _coursetile.scss, …
├── layout/             # _shell.scss, _sidebar.scss, _topbar.scss, _content.scss
└── post.scss           # entry composed after Boost SCSS
```

Rules:
- Colours/spacing/radii/shadows **only** via tokens. Raw hex values may exist in
  exactly one file: `_tokens.scss`.
- Custom-mode styles are scoped under `.sfs-mode` (body class, ADR-002) so standard
  mode remains untouched Boost.
- Dark scheme via `[data-theme="dark"]` custom-property overrides (ADR-004) —
  components never know about dark mode.
- No `!important`. No element selectors for layout. Mobile breakpoints: 1100px and
  820px (match the prototype), expressed as mixins.

## Mustache

- Templates in `templates/`, one per component or layout region; context documented
  in the template docblock (`@template`, example JSON) per Moodle convention.
- Logic-free: booleans/arrays pre-computed in output classes.
- User content escaped by default `{{ }}`; `{{{ }}}` only for HTML produced by
  `format_text()`.
- Strings via `{{#str}}key, theme_securefood{{/str}}` or passed pre-localised.
- Icons: a single `sfs/icon` partial wrapping the Material Icons span with
  `aria-hidden="true"` and optional visually-hidden label.

## JavaScript (AMD/ESM)

- Modules in `amd/src/*.js`, ES module style, initialised from templates via
  `{{#js}}` blocks or `data-` auto-init. No inline `<script>`, no jQuery.
- Server communication only via `core/ajax` (external functions).
- Progressive enhancement: every page must be usable with JS disabled
  (sidebar visible expanded, plan switcher degrades to links, theme toggle falls
  back to the preferences page).
- Respect `prefers-reduced-motion`: gate all transitions/animations behind the
  media query (tokens define the durations).

## Accessibility (WCAG 2.1 AA — hard requirement)

- Full keyboard support: sidebar nav, plan switcher menu (roving focus, `Escape`
  closes), collapsible stages (`aria-expanded`), mode/theme toggles (`aria-pressed`
  or labelled switch).
- Visible focus states on every interactive element (token: focus ring).
- Landmarks: `nav`, `main`, `header`; skip-to-content link in the shell.
- Colour contrast ≥ 4.5:1 for text — verify token pairs in both light and dark
  (the prototype's muted-on-bg pairs must be checked, do not trust them blindly).
- Icon-only buttons need `aria-label`s (prototype already models this — keep it).
- Locked course tiles: convey state in text, not colour/opacity alone.
- Live regions for async updates (progress refresh, plan switch).

## Assets

- Logos from `SecureFood School/assets/` (SVG preferred) → `theme/securefood/pix/`.
- Fonts self-hosted WOFF2 (ADR-006). No external requests of any kind.
