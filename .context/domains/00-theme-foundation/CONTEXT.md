# Domain 00 — theme-foundation (Phase 1)

Status: `[~]` mostly done (2026-07-12) — see task list; Boost-variable mapping
deferred to domain 02 (only meaningful once `.sfs-mode` exists).

## Purpose

Scaffold `theme_securefood` (Boost child) and lay the token/asset foundation every
other UI domain builds on. After this domain the theme installs and is selectable,
looks identical to Boost (custom mode not yet wired), and compiles the full token
layer.

## Design references

- `design/design-system.md` (extracted tokens — source of truth).
- `SecureFood School/styles.css` lines 1–124 (tokens, fonts, base typography).
- `SecureFood School/assets/` (logos, SVG + PNG, light/dark × full/icon).

## Deliverables / structure

```
public/theme/securefood/
├── version.php               # theme_securefood, requires Moodle 5.2
├── config.php                # parents=['boost'], sheets none, scss callback, layouts (Boost passthrough for now)
├── lib.php                   # *_get_main_scss_content, *_get_pre_scss, pluginfile for settings assets
├── settings.php              # tabs skeleton only here — full catalogue owned by domain 11 (ADR-007)
├── lang/en/theme_securefood.php + lang/uk/…
├── scss/  (structure per conventions/frontend.md: _tokens, _fonts, _mixins, components/, layout/, post.scss)
├── fonts/                    # Manrope, Inter, Material Icons Rounded — WOFF2 + licences (ADR-006)
├── pix/                      # logos from prototype assets, favicon, screenshot
└── templates/                # (empty until domain 01)
```

## Tasks

- [~] Login page SecureFood styling (2026-07-14): replaced Boost's generic
      split-screen login marketing treatment with SecureFood-branded panel and
      token-driven login form styling, without changing core auth flow. Verified
      with PHP lint and Moodle SCSS smoke compile; template HTTP smoke confirmed
      the SecureFood panel copy. Final cache purge/browser visual check remains
      pending after the final Boost-background specificity override.

- [x] Independent theme/design conformance audit (2026-07-12): verified the
      current prototypes against the implemented theme and page integrations;
      material gaps remain in fidelity, token compliance, no-code settings,
      accessibility and test coverage. Findings recorded in `PROGRESS.md`;
      no production code changed.

- [x] Scaffold theme (config/version/lib/lang) — installed via CLI upgrade,
      Boost-identical, set as site default (2026-07-12).
- [x] `_tokens.scss`: all custom properties from design-system.md, light + dark
      (`[data-theme="dark"]` block), radii, shadows, layout vars, motion durations.
      **Note:** properties are namespaced `--sfs-*` (prototype used bare `--bg`).
- [x] `_fonts.scss` + self-hosted WOFF2 (latin/latin-ext/cyrillic/cyrillic-ext,
      variable weights) + `fonts/LICENCES.txt`. Icon font family:
      `'SFS Material Icons'` + `.sfs-icon` primitive in `post.scss`.
- [x] `_mixins.scss`: focus ring, card, hover-lift (reduced-motion aware),
      breakpoints (1100/820), visually-hidden.
- [ ] Map key Boost/Bootstrap SCSS variables to tokens — **deferred to domain 02**
      (must be scoped to custom mode; doing it now would recolour standard mode).
- [x] Logos into `pix/` (SVG ×4). Logo *override settings* → domain 11 Brand tab.
- [x] Privacy provider (covers the three user preferences, not a null provider —
      preferences shipped early with domain 03).
- [x] Verify: installed, caches purged, CSS compiles (tokens/fonts/icon class
      present), homepage renders. PHPCS **not run** — no dev deps in container.

## Acceptance criteria

- Theme selectable in admin; zero visual diff vs Boost until custom mode ships.
- `grep -rn "#[0-9a-fA-F]\{3,6\}" scss/ --include="*.scss"` hits `_tokens.scss` only.
- No external font/icon requests anywhere.

## Dependencies

None (parallel with domain 10). Blocks 01, 02, 03 and all page domains.

## Open questions

- [ ] Material Icons: full font vs subset of the ~40 glyphs actually used
      (list them from prototypes before deciding).
- [ ] Reconcile the raw colour/rgba literals outside `_tokens.scss` before
      calling the foundation complete. 2026-07-13: inline `style` attributes
      were removed from custom production templates by replacing width spans
      with native `<progress>` and generated map-marker position classes; the
      four active custom `!important` declarations were removed. Remaining
      `!important` occurrences are confined to third-party Leaflet vendor CSS
      plus a comment in `_shell.scss`. Future Food and Student Lab colour
      tokenisation plus small component colour slices are complete; remaining
      raw colour/rgba hits outside `_tokens.scss`/vendor: 67 after completing
      the Course colour tokenisation slice. Shell colour tokenisation is
      complete; remaining raw colour/rgba hits outside `_tokens.scss`/vendor:
      62. About colour tokenisation is complete; remaining raw colour/rgba
      hits outside `_tokens.scss`/vendor: 30. Learningplans CSS colour
      tokenisation is complete; remaining raw colour/rgba hits outside
      `_tokens.scss`/vendor: 0.
