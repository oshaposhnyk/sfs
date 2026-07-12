# Component inventory — prototype class → BEM block mapping

Rule (ADR-003): rebuild every component as a BEM block with the `sfs-` prefix.
Left column = prototype selector (visual spec in `SecureFood School/styles.css`
and per-page `<style>` blocks); right = target block + home.

## Shell (domain 01)

| Prototype | BEM block | Elements / modifiers |
|-----------|-----------|----------------------|
| `.app` grid | `sfs-shell` | `__sidebar`, `__main`; `--collapsed`, `--drawer-open` |
| `.sidebar`, `.brand` | `sfs-sidebar` | `__brand`, `__logo` (`--full`, `--icon`), `__section-label`, `__spacer` |
| `.nav-item` | `sfs-nav` | `__item` (`--active`), `__icon`, `__label`, `__tooltip` |
| `.user-card` | `sfs-usercard` | `__avatar`, `__name`, `__role` |
| `.topbar` | `sfs-topbar` | `__toggle`, `__crumbs`, `__search`, `__actions` |
| `.crumb` | `sfs-breadcrumbs` | `__item` (`--current`), `__separator` |
| `.search` | `sfs-search` | `__icon`, `__input` |
| `.icon-btn` | `sfs-iconbtn` | `--dot` (notification pip) |
| `.sidebar-backdrop` | `sfs-shell__backdrop` | |

## Primitives (domain 00, shared)

| Prototype | BEM block | Notes |
|-----------|-----------|-------|
| `.card`, `.card-pad` | `sfs-card` | `--pad`, hover-lift only where interactive |
| `.btn` + variants | `sfs-btn` | `--primary`, `--accent`, `--ghost`, `--link`; pill shape |
| `.chip` + `.solid-*` | `sfs-chip` | `--teal`, `--amber`, `--success`, `--warn` |
| `.tag` | `sfs-tag` | uppercase micro label |
| `.progress` | `sfs-progress` | `__bar`; `--amber`, `--gradient`, sizes |
| `.page-head`, `.kicker`, `.lede` | `sfs-pagehead` | `__kicker`, `__title`, `__lede`, `__actions` |
| `.section-head`, `.filters`, `.filter-btn` | `sfs-sectionhead`, `sfs-filterbar` | `__btn` (`--active`) |
| `.filter-pill` | `sfs-filterpill` | `--active` |
| `.divider` | `sfs-divider` | |

## Student Lab (domain 05) — from `l4c.html`

| Prototype | BEM block | Elements / modifiers |
|-----------|-----------|----------------------|
| `.plan-bar` | `sfs-planbar` | `__pin`, `__label`, `__title`, `__stats`, `__stat` (`__num`, `__lbl`), `__progress` (`__bar`, `__pct`) |
| `.plan-switcher`, `.plan-menu` | `sfs-planmenu` | `__trigger`, `__list`, `__item` (`--active`), `__icon`, `__name`, `__meta`; keyboard menu semantics |
| `.course-section` | `sfs-stage` | `__head`, `__badge`, `__title`, `__meta`; `--done`, `--inprogress`, `--locked` |
| `.course-grid` | `sfs-stage__grid` | auto-fill minmax(280px, 1fr) |
| `.course-tile` (`.ct-*`) | `sfs-coursetile` | `__cover` (`--v1…--v5`), `__tag`, `__status`, `__icon`, `__body`, `__title`, `__desc`, `__meta`, `__progress`, `__foot`, `__state`, `__action`; `--done`, `--active`, `--upnext`, `--locked` |

Statuses (must match plugin semantics): done / active / up-next / locked.
Actions by status: Review / Resume / Start / (none — lock reason text).

## Course + activity (domain 06) — from `course.html`, `activity.html`

| Prototype | BEM block |
|-----------|-----------|
| `.course-banner`, `.banner-chip`, `.banner-meta`, `.due-pill` | `sfs-coursebanner` |
| `.course-layout` (content + rail) | `sfs-courselayout` |
| `.section-card`, `.sec-num`, `.sec-progress`, `.section-body` | `sfs-coursesection` |
| activity rows `.a-icon/.a-title/.a-meta/.a-state/.a-go` | `sfs-activityrow` (`--done`, `--active`, `--locked`) |
| `.course-progress`, `.progress-track`, `.frac`, `.progress-stats` | `sfs-courseprogress` |
| `.rail-card`, `.rail-row`, `.rr-avatar`, `.info-list` | `sfs-courserail` |

## Dashboard / About (domain 04) — from `insights.html`

| Prototype | BEM block |
|-----------|-----------|
| `.kpi-row`, `.kpi` | `sfs-kpi` (`__head`, `__value`, `__sub` `--up/--down/--warn`) |
| `.hero-summary`, `.hero-stats`, `.hero-stat` | `sfs-hero` |
| `.health-card`, `.status-pill`, `.signal-list`, `.signal` | `sfs-health`, `sfs-signal` (`--good/--warn/--info`) |
| `.map-card`, `.hub-card`, `.hub-row` | `sfs-map`, `sfs-hublist` (`__row` `--active/--maint`) |
| `.feed-grid`, `.feed-card`, `.feed-thumb.t-*` | `sfs-feedcard` (`__thumb` `--twin/--culture/--water/--supply`) |

## Future Food (domain 07) — from `ffs.html`

| Prototype | BEM block |
|-----------|-----------|
| `.ff-hero`, `.ff-stats`, `.ff-level-row`, `.ff-xp-bar` | `sfs-ffhero`, `sfs-xp` |
| `.achievement-row`, `.ach` | `sfs-achievement` (`--locked`, `__badge`, `__name`, `__xp`, `__pip`) |
| `.mission-grid`, `.mission`, `.mission-thumb.m-*` | `sfs-mission` (`__thumb`, `__play`, `__duration`, `__tags`, `__foot`, `__xp`) |
| `.decision`, `.choice-grid`, `.choice-btn`, `.feedback` | `sfs-decision` (`__choice` `--correct/--wrong`, `__feedback`) |

## Resources & Standards (domain 08) — from `governance.html`

| Prototype | BEM block |
|-----------|-----------|
| `.gov-tools`, `.tool` | `sfs-tool` (`__icon`, `__desc`, `__count`) |
| `.resource-card`, `.doc-list`, `.doc-row`, `.doc-icon.pdf/.doc/.xls/.zip` | `sfs-doclist` (`__row`, `__icon` `--pdf/--doc/--xls/--zip`, `__title`, `__sub`, `__download`) |
| `.validate-card`, `.vote-row`, `.expert-block` | `sfs-validate` |
| `.gov-side-stats`, `.side-stat` | `sfs-sidestat` |

## Porting rules

1. Inline styles in prototype HTML (e.g. `style="background:var(--accent)"` on stage
   badges) become modifiers or data-driven token classes — never inline styles.
2. Hover-lift and cursor:pointer only on genuinely interactive elements (a11y).
3. Every status conveyed by colour must also appear as text/icon.
4. One SCSS file per block in `scss/components/`.
