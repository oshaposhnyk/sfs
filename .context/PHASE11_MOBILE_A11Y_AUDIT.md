# Phase 11 — Mobile & Accessibility audit + remediation

> Created 2026-08-11. Owner-reported mobile defects (overcrowded topbar; map/Network
> panel horizontal overflow) triggered a full sweep of `theme_securefood` mobile
> styles and a WCAG 2.1 AA pass. This file is the backlog for the fixes; update
> `PROGRESS.md` and domain `CONTEXT.md` (01 app-shell, 04 dashboard) as tasks land.

Breakpoints (`scss/_mixins.scss`): `sfs-media-compact` = ≤1100px, `sfs-media-mobile`
= ≤820px. Target phone ≈ 390px. All fixes are presentational (SCSS / template /
AMD) — no Moodle core changes, no endpoint changes.

## Reported symptoms → root causes

1. **Map + "Network" panel overflow, "Ukraine" → "Ukr"** — CSS source-order bug:
   the ≤1100px single-column override for `.sfs-hubs__grid` was written *before*
   the unconditional two-column base, so (equal specificity, media query adds no
   specificity) the later two-column rule won at every width. *Verified by
   compiling a minimal repro.* → **F6**.
2. **Topbar overcrowded / overflows** — `.sfs-topbar` / `.sfs-topbar__actions` are
   non-wrapping flex rows of fixed-width, non-shrinking buttons; `navbar_plugin_output`
   injects an uncontrolled number of core/plugin icons; only the search box is
   hidden on mobile. → **F1/F2/F5**.
3. **Hero heading clipped behind the sticky topbar** — the sticky topbar has no
   matching `scroll-padding-top`/`scroll-margin-top`; the skip-link target lands
   under the ~60px bar. → **F3**.

## Mobile findings

| ID | Sev | Issue | Location |
|----|-----|-------|----------|
| F6 | Critical | `.sfs-hubs__grid` collapse placed before the 2-col base → never applies (map overflow, "Ukr" clip) | `components/_about.scss` (was :331 vs :453) |
| F1 | High | Topbar action row: no `flex-wrap`, children don't shrink → overflow | `layout/_shell.scss:522-540, 1079-1102` |
| F3 | High | Sticky topbar has no scroll offset → headings/skip-link land under it | `layout/_shell.scss:522-525`; `shell.mustache:57,100` |
| F2 | Medium | Same overflow on 821–1100px tablet — search hidden only ≤820px | `layout/_shell.scss:696-706, 1100` |
| F5 | Medium | Current breadcrumb has no `min-width:0` → pushes width instead of ellipsing ("Ho…") | `layout/_shell.scss:664-672, 1090` |
| F4 | Medium | Icon buttons 36×36 (<44) — scheme/mode/help, core popover toggles | `layout/_shell.scss:630-633, 843-846` |
| F7 | Medium | `.sfs-hubs__name` lacks `min-width:0` → over-truncates country | `components/_about.scss:543-598` |
| F8 | Medium | Map markers 10–14px hit area (<44) | `components/_about.scss:475-479`; `aboutmap.js:139` |
| F9 | Low | Duplicate `.sfs-hero__title` (2.6rem dead) | `components/_about.scss` |
| F10 | Low | Mission grid `minmax(320px,1fr)` overflows ≤352px; raw `@media` instead of mixins in `_course/_preferences/_messaging` | various |

## Accessibility findings (WCAG 2.1 AA)

| ID | Sev | Issue | SC |
|----|-----|-------|-----|
| A1 | High | Collapsed sidebar nav: `display:none` label removes accessible name → unnamed links | 4.1.2, 2.4.4 |
| A2 | High | User menu: `role=menu`/`tabindex=-1` items with no roving focus / arrow keys / Escape → keyboard-inoperable | 2.1.1, 4.1.2 |
| A3 | Medium | Search `outline:none` with no replacement → invisible focus | 2.4.7 |
| A4 | Medium | Scheme/mode toggles don't expose current state | 4.1.2 |
| A5 | Medium | Front page (About) has no `<h1>` (core `#page-header` hidden `display:none`) | 1.3.1, 2.4.6 |
| A6 | Medium | `--sfs-muted2` as `.sfs-sidebar__label` text = 2.4–2.6:1 | 1.4.3 |
| A7 | Medium | `--sfs-success` done-text 3.6–4.1:1; `--sfs-teal` on `teal50` 3.46:1 | 1.4.3 |
| A8 | Low | Leaflet motion not reduced-motion-gated; some links miss `:focus-visible`; skip-target not focusable; dark danger/placeholder contrast; no live regions | 2.3.3, 2.4.7, 4.1.3 |

Confirmed OK: landmarks correct & uniquely labelled; skip link present; decorative
icons `aria-hidden`; map markers labelled with `aria-describedby`; `--sfs-muted`
darkened to AA; mobile drawer JS (focus trap / Escape / focus return / inert) is
exemplary.

Cross-plugin boundary (DDD): locked/done state markup is owned by
`local_learningplans` / `local_sfsgame` / `local_sfsresources`; contrast fixes are
theme-token-level, but "state carried in text not colour" is confirmed in those
plugins' templates.

## Remediation phases

### Phase 0 — Critical map regression `[x]` (done 2026-08-11)
- **T0.1** Moved `.sfs-hubs__grid → 1fr` (+ map `min-height:260px`) to *after* the
  2-col base in `_about.scss`; added `min-width:0` to `.sfs-hubs__map` /
  `.sfs-hubs__card`. Verified three ways: (a) standalone Dart Sass repro of the
  ordering; (b) Moodle theme compile clean (1,241,840 bytes) with the `1fr` media
  rule now emitted *after* the base; (c) CSSOM inspection of the browser-loaded
  CSS — the winning `.sfs-mode .sfs-hubs__grid` rule at ≤1100px (and ≤390px) is the
  `1fr` collapse (order 1, later, equal specificity). True mobile screenshot not
  captured: this environment's window resize does not shrink the render viewport
  (`matchMedia(max-width:1100px)` stayed false at reported 390px) — CSSOM proof
  stands in. Caches purged.

### Phase 1 — Topbar / header (High) `[~]` (implemented 2026-08-11; real-device QA owed)
- **T1.0 `[x]`** ADR-013 accepted: core plugin cluster stays inline; theme-owned
  controls (scheme/mode/help/language) collapse into a `sfs-topbar__more`
  disclosure on mobile; no-JS fallback = inline + `flex-wrap`.
- **T1.1 `[x]`** `.sfs-breadcrumbs` given `flex:0 1 auto`; current crumb
  `min-width:0` + `flex:0 1 auto` so it ellipses instead of pushing (F5).
- **T1.2 `[x]`** `shell_topbar.mustache` restructured (BEM `sfs-topbar__more`/
  `__secondary`/`__moretoggle`, rendered once); `shell` AMD extended
  (`applyMoreMode`/`setMoreOpen`/`closeMore`/`getMore`, click delegate,
  Escape + click-away, matchMedia reset); SCSS desktop-inline / mobile-popover
  gated behind JS-only `--collapsible`. New string `moreactions` (en+uk).
  Cascade gotcha fixed: toggle hide scoped `.sfs-topbar__more .sfs-topbar__moretoggle`
  (0,3,0) to beat `.sfs-iconbtn` (0,2,0).
- **T1.3 `[x]`** Search shrinks at `sfs-media-compact` (`flex:0 1 200px; min-width:0`),
  hidden only at mobile — fixes the 821–1100px overflow without losing tablet
  search (F2).
- **T1.4 `[x]`** `:root:has(.sfs-mode) { scroll-padding-top:72px }` +
  `#sfs-main { scroll-margin-top:72px }` (F3 / WCAG 2.4.1).
- **Verification:** theme compiles clean (1,242,897 bytes); `node --check` on
  `amd/src` + `amd/build`; PHP lang lint clean, en+uk in sync. Browser (desktop,
  themedesignermode): topbar is one clean row, "More" toggle `display:none`,
  secondary controls inline, `docOverflow=0`; CSSOM confirms the ≤820px popover
  rules; JS state machine verified end-to-end (toggle→open, Escape→close,
  click-away→close). **Owed:** true ≤820px viewport screenshot — the tooling
  cannot shrink the render viewport here (see [[mobile-verify-cssom]]).
- **T1.5 `[x]` (owner mobile feedback, 2026-08-11)** — two follow-ups from the
  owner testing on a real phone:
  - *Two logos in the drawer* — the mobile-collapsed logo overrides (0,3,0) lost
    to the desktop-collapsed and dark-mixin rules (0,4,0 / 0,6,0), so both full
    and icon logos rendered. Fixed by raising the mobile overrides to compound
    scheme selectors placed after the base rules (light) and adding a mobile
    override inside the dark mixin (dark). Verified deterministically from the
    compiled CSS: on ≤820px collapsed the `display:none` icon rule wins in both
    light and dark. Same cascade class as F6 / the toggle.
  - *"More" popover looked empty / bare icons* — inside the popover the icon
    buttons now render as full-width labelled rows (icon + text) via a
    `.sfs-topbar__morelabel` span (hidden on desktop, shown in the popover) and
    a row layout; the language menu spans the full row too. Labels reuse existing
    strings (togglescheme / switchtostandard / help) so no new lang keys and
    Label-in-Name (2.5.3) holds.

### Phase 2 — Keyboard operability of menus (High a11y) `[~]` (implemented 2026-08-11; user-menu live QA owed)
- **T2.1 `[~]`** User-menu WAI-ARIA keyboard support in `shell` AMD:
  `activePaneItems`/`rovingFocus`/`focusFirstMenuItem`/`handleUserMenuKeydown` —
  Up/Down/Home/End roving `tabindex` across the active pane's `[role=menuitem]`,
  Escape closes the `<details>` and returns focus to the summary, and open/pane-
  switch now focuses the first item instead of the pane div (A2). `node --check`
  clean (src+build). **Live keyboard/SR test owed** — the user menu only renders
  for authenticated users and the dev admin session had logged out; do not log in
  on the owner's behalf.
- **T2.2 `[x]`** Nav links carry `aria-label="{{title}}"` and the visible label is
  `aria-hidden` so the link stays named in the desktop-collapsed rail without
  double announcement (A1, and 2.5.3 Label-in-Name). Fallback usercard link gets
  `aria-label="{{fullname}}"`. Verified live: `aria-label="About the Project"`,
  label `aria-hidden="true"`.
- **T2.3 `[x]`** `<main id="sfs-main" tabindex="-1">` so the skip link moves focus,
  not just the viewport (§1.5). Verified live: `tabindex="-1"`.

### Phase 3 — State exposure & live regions `[~]` (done 2026-08-11; login toggle + live map interaction owed)
Added a shared polite live region: `#sfs-live` (`role="status" aria-live="polite"`,
visually hidden via `.sfs-live`) in the shell, plus a tiny `theme_securefood/live`
AMD module exporting `announce(message)` — single responsibility, reused by shell
and map (SOLID).
- **T3.1 `[x]`** The colour-scheme toggle announces the new scheme through the
  live region; localized names reuse the **existing** `scheme_light/dark/system`
  strings (passed as `data-sfs-scheme-*` on the button, resolved server-side).
  Verified live: toggling announced "Light". (Caught + removed duplicate lang keys
  I'd added — the strings already existed for the preferences page.)
- **T3.2 `[x]`** The About hero is now `<h1>` (was `<h2>`); the core `#page-header`
  h1 stays `display:none` on the front page, so the hero is the sole
  a11y-visible h1 (A5/1.3.1). Verified live.
- **T3.3 `[x]`** `aboutmap` announces the focused/clicked hub (name, country,
  status) through the same live region (§4.4). Mechanism proven via the scheme
  announce + a direct `announce()` module test; live marker interaction test owed.
- **Owed:** the login page scheme toggle (`loginscheme.js`, separate layout with
  no `#sfs-live`) is not yet wired — follow-up.
- **Bugs caught during verification:** (1) `announce()` first used
  `requestAnimationFrame`, paused in background/automated tabs — switched to
  `setTimeout(…,50)` so it fires regardless of tab visibility; (2) disabling
  `cachejs` breaks **all** theme AMD in this env (requirejs.php returns a
  "cannot be loaded" stub for every `theme_securefood/*` module, touched or not) —
  keep `cachejs=1`; the bundle rebuilds on purge. See [[mobile-verify-cssom]].
- **Verification:** all JS `node --check` (src+build for live/shell/aboutmap);
  lang lint clean; en/uk in sync (243 keys each, zero diff); theme compiles clean;
  `cachejs=1` / `themedesignermode=0` restored; caches purged.

### Phase 4 — Colour contrast (token-level) `[x]` (done 2026-08-11)
Added three text-safe semantic tokens in `_tokens.scss` (the only file with raw
hex), each with a lightened dark-scheme value, mirroring the `--sfs-accent-ink`
precedent: `--sfs-success-ink` (#2F6F46 / #6FBF8E), `--sfs-teal-ink`
(#1C6664 / #4FC3C0), `--sfs-danger-ink` (#B8463F / #E5837C). Ratios computed with
the WCAG formula against every relevant surface before choosing values.
- **T4.1 `[x]`** `.sfs-sidebar__label` and the dark input placeholder switched
  `--sfs-muted2` → `--sfs-muted` (5.17:1 light label; 5.50:1 dark placeholder) (A6/§3.5).
- **T4.2 `[x]`** Done-state text (`lp-stage--done` status, `lp-coursetile--done`
  state, `sfs-mission__completion--completed`, `sfs-decision__state`) →
  `--sfs-success-ink` (light 5.45–6.03, dark 5.74–7.77) (A7/§3.2).
- **T4.3 `[x]`** Teal status text (`sfs-hubs__status`, course `.btn-success`
  "Done") → `--sfs-teal-ink` (light 5.76 on teal50, dark ≥5.2) (A7/§3.3).
- **T4.4 `[x]`** PDF file-type label → `--sfs-danger-ink` (dark 4.74–6.42; light
  4.76–5.27) (A8/§3.4).
- **Verification:** theme compiles clean (1,244,937 bytes); all six token defs
  present in the compiled CSS; usages reference the ink tokens (success×4/teal×2/
  danger×1); zero new raw hex outside `_tokens.scss`. Ratios are guaranteed by the
  literal token values (all ≥4.5:1 in light **and** dark). Background pills/
  gradients left unchanged — only text-as-colour usages were retargeted.

### Phase 5 — Touch targets & focus `[x]` (done 2026-08-11)
- **T5.1 `[x]`** At `sfs-media-mobile`, `.sfs-iconbtn` and the topbar core popover
  toggles (`.sfs-topbar__plugins .nav-link.popover-region-toggle`) go 44×44;
  desktop keeps the 36px design (pointer, ≥ WCAG 2.5.8 AA). Popover row buttons
  keep their own full-width sizing (higher specificity) (F4/§2.5).
- **T5.2 `[x]`** `.sfs-search:focus-within` now shows the focus ring (the input's
  `outline:none` had no replacement) (A3/§2.1).
- **T5.3 `[x]`** Added `:focus-visible` rings to the restyled links that lacked
  them: `.sfs-breadcrumbs__item`, `.sfs-sidebar__brand`, `.sfs-plancontext__chip`,
  `.lp-coursetile__link`, `.sfsres-tool`, `.sfsres-doc__open` (§2.2).
- **T5.4 `[x]`** Static hub markers get a 24px tap target via `::after` inset
  (WCAG 2.5.8 AA) without enlarging the dot; a full 44px would overlap on the
  clustered map, so the large hub-list rows remain the accessible alternative for
  both static and live markers (F8).
- **Verification:** compiles clean (1,245,849 bytes); all rules present in the
  compiled CSS; zero raw hex outside `_tokens.scss`. SCSS-only — no PHPUnit
  affected. Focus rings are standard `:focus-visible`; desktop 44px scoping
  confirmed by the mobile-media guard.

### Phase 6 — Motion & convention hygiene `[x]` (done 2026-08-11)
- **T6.1 `[x]`** Reduced-motion override for the vendored Leaflet transitions,
  theme-owned (not a vendor edit) and scoped to `.sfs-hubs__map--live`:
  `@media (prefers-reduced-motion: reduce)` sets the fade/zoom `transition: none`
  (higher specificity than vendor, no `!important`) (A8/§2.3). Compiled + confirmed.
- **T6.2 `[x]` (verified, no change)** `local_sfsgame`'s decision template already
  gives the locked choice a textual `missionnoaccess` note (not opacity-only), so
  §2.4 is satisfied. No cross-plugin change needed.
- **T6.3 `[x]`** `.sfs-hubs__name` gets `min-width:0` + ellipsis so a long name
  ellipses instead of over-truncating the country (F7); mission grid uses
  `minmax(min(320px,100%),1fr)` so it never overflows <320px phones (F10); the
  duplicate `.sfs-hero__title` block (dead `2.6rem`) is consolidated into one
  (F9, compiled count = 1). Convention: the three raw `@media` blocks in
  `_course`/`_preferences`/`_messaging` now use the `sfs-media-compact/mobile`
  mixins; the `_login` Bootstrap breakpoints are left as-is (documented — they key
  off Boost's `d-lg` split).
- **Verification:** theme compiles clean (1,246,223 bytes); reduced-motion +
  Leaflet override present in compiled CSS; zero raw hex outside `_tokens.scss`;
  theme PHPUnit 22/22 (111 assertions); caches purged.

---

## Phase 11 status (2026-08-11)
Done & verified: **Phase 0** (map overflow), **1** (topbar + owner mobile
feedback), **3** (state/live/h1), **4** (contrast), **5** (touch/focus),
**6** (motion/hygiene). **Phase 2** implemented (A1/A2/skip) with the user-menu
live keyboard/SR test still owed (needs a logged-in admin session).

**Owed verification / follow-ups** (none code-blocking):
- Real ≤820px device screenshot (tooling can't shrink the viewport here).
- User-menu keyboard/SR walkthrough (auth-only UI).
- Login-page scheme toggle live-region wiring (`loginscheme.js`, separate layout).
- Live Leaflet marker announce interaction (needs configured hubs).
All changes are uncommitted pending owner review (owner commits via "fix styles").

## Definition of done (per phase)
Dart Sass compile → purge caches → browser 360/390/768/1100px light+dark →
keyboard + SR pass → `php -l` + affected PHPUnit → update `PROGRESS.md` + domain
`CONTEXT.md` → record ADRs (topbar). BEM `sfs-`, tokens only, no `!important`, no
jQuery, progressive enhancement, `sfs-motion` for transitions, `get_string()` with
en+uk in sync.
