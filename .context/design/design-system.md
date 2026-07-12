# Design system — tokens (source of truth: `SecureFood School/styles.css`)

Brand: teal + warm amber, derived from the SecureFood logo. Two colour schemes
(light default, dark via `[data-theme="dark"]`). These values go into
`theme/securefood/scss/_tokens.scss` as CSS custom properties — raw hex is allowed
**only there**.

## Colour tokens

| Token          | Light                       | Dark                        | Use |
|----------------|-----------------------------|-----------------------------|-----|
| `--bg`         | `#FAF7F2`                   | `#0B1E24`                   | page background |
| `--bg-2`       | `#F3EEE5`                   | `#0E2530`                   | recessed areas, progress track |
| `--surface`    | `#FFFFFF`                   | `#102C36`                   | cards, sidebar, topbar |
| `--surface-2`  | `#F7F3EC`                   | `#143744`                   | hover, chips, user card |
| `--ink`        | `#0F2A33`                   | `#F2EFE9`                   | headings, primary text |
| `--ink-2`      | `#2C4751`                   | `#D7D1C2`                   | body text |
| `--muted`      | `#6B7F87`                   | `#9BAEB6`                   | secondary text |
| `--muted-2`    | `#94A3AA`                   | `#7A8E96`                   | placeholders, disabled |
| `--line`       | `#E8E1D4`                   | `rgba(255,255,255,0.08)`    | borders |
| `--line-strong`| `#D6CDBA`                   | `rgba(255,255,255,0.14)`    | hover borders |
| `--primary`    | `#0F4C5E` (deep teal)       | `#4FC3C0`                   | actions, active nav, links |
| `--primary-700`| `#0B3A48`                   | `#2A8C8A`                   | hover on primary |
| `--primary-50` | `#E6F0F2`                   | `rgba(79,195,192,0.12)`     | active nav bg, soft badges |
| `--accent`     | `#C68A3B` (wheat amber)     | `#E0A458`                   | kickers, highlights, XP, active states |
| `--accent-50`  | `#F8EFDF`                   | `rgba(224,164,88,0.16)`     | soft amber backgrounds |
| `--teal`       | `#2A8C8A`                   | `#4FC3C0`                   | status pills, gradients |
| `--teal-50`    | `#E1F1F0`                   | `rgba(79,195,192,0.14)`     | soft teal backgrounds |
| `--success`    | `#3F8C5A`                   | same                        | done states |
| `--warn`       | `#C97A2B`                   | same                        | warnings |
| `--danger`     | `#B8463F`                   | same                        | errors, PDF icon |

Gradient recipes (used on covers, heroes, block icons):
`135deg #0F4C5E→#2A8C8A` (cov-1), `#2A8C8A→#4FC3C0` (cov-2), `#C68A3B→#E0A458`
(cov-3/amber), `#3F8C5A→#65B57F` (cov-4/green), `#5b6770→#8e9aa3` (cov-5/locked),
`#143E4D→#0F4C5E` (dark teal), hero: `135deg primary→#143E4D→#0B3A48`.

## Typography

| Role | Font | Notes |
|------|------|-------|
| Headings, numerals, KPI values | **Manrope** 400–800 | `letter-spacing: -0.01/-0.02em`, `text-wrap: balance` |
| Body, UI | **Inter** 400–700 | `font-feature-settings: "ss01","cv11"`, antialiased |
| Icons | **Material Icons (Rounded)** | 14–28px sizes, self-hosted (ADR-006) |

Scale: h1 2.1–2.2rem/700 · h2 1.5rem/700 · h3 1.1rem/700 · h4 0.95rem/600 ·
body 1rem, line-height 1.55 · meta 0.78–0.86rem · kicker 0.74rem/700 uppercase
letter-spacing 0.1em (amber, with 22×2px dash before) · section labels 0.68rem/600
uppercase 0.08em.

## Geometry & effects

| Token | Value |
|-------|-------|
| `--radius-xs / sm / md / lg` | 6 / 10 / 14 / 22 px |
| pill radius | 999px (buttons, chips, search, progress) |
| `--shadow-1` | `0 1px 2px rgba(15,42,51,.04), 0 1px 1px rgba(15,42,51,.03)` (resting card) |
| `--shadow-2` | `0 4px 14px rgba(15,42,51,.06), 0 1px 3px rgba(15,42,51,.04)` (hover lift) |
| `--shadow-3` | `0 14px 40px rgba(15,42,51,.10), 0 2px 6px rgba(15,42,51,.05)` (drawer/menu) |
| `--sidebar-w` | 248px (collapsed: 76px) |
| content | `max-width: 1320px`, padding `32px 36px 60px` (mobile `22px 18px`) |
| card hover | `translateY(-2px)` + shadow-2, 0.15s |
| progress bars | 4–8px tall, pill, animated width `0.6s cubic-bezier(.2,.8,.2,1)` |

## Motion

- Micro-transitions 0.12–0.25s ease; sidebar collapse 220ms; mobile drawer 240ms.
- `fadeIn` 0.4s (content mount), `slideIn` 0.3s (feedback), `pulse` 1.6s (live dot).
- All gated behind `prefers-reduced-motion: no-preference`.

## Breakpoints

- `≤1100px`: two-column grids collapse to one; KPI row 4→2; block stats hidden.
- `≤820px`: sidebar becomes fixed drawer (backdrop `rgba(0,0,0,.4)` + blur); search
  hidden; content padding reduced.

## Scheme switching mechanics

- `<html data-theme="light|dark">`; dark overrides custom properties only.
- Topbar toggle icon swaps `dark_mode` ⇄ `light_mode`.
- Logo swaps per scheme and per sidebar state: full logo (expanded) ⇄ icon logo
  (collapsed), light ⇄ dark variants (`assets/logo-*.svg`).
- Topbar background is translucent (`rgba` of bg + `backdrop-filter: blur(8px) saturate(140%)`).
