# Phase 8 — Style audit register (2026-07-12)

Method: prototypes served from `SecureFood School/` and screenshotted in the
same browser/viewport (1440×1000) as the live pages, then compared visually.
Status: ☐ open · ☑ fixed (re-verified by screenshot).

## Student Lab (l4c.html ↔ /local/learningplans/my.php)

- ☑ S1 Topbar search box (global search simpledb enabled) (site global search disabled; prototype
  always shows the pill). Fix: enable global search (simpledb) on the site.
- ☑ S2 Play icon on Continue (ghost Activity button still deferred); prototype also has a ghost
  "Activity" button (deferred — no activity page yet).
- ☑ S3 "Stage X of Y" label live (real stage data; L-level still deferred) next to the progress bar
  (real data available since 6.2); prototype also shows an L3 level stat
  (deferred — no level source on plans, see ADR-008).
- ☑ S4 "N modules" meta from modinfo (known nit: no singular form; effort/icons deferred) (real data via modinfo);
  "·2h 40m" effort deferred (no data source). Cover icons deferred (no
  per-course icon source).

## About (insights.html ↔ /?redirect=0)

- ☑ A1 Hero rebuilt per design (accent title, hub lede, glass logo panel, grid texture, inline divider stats): prototype hero = "Learning for resilience
  in an evolving world" (accent on last words), hub lede, logo panel right,
  4 stats inline at the bottom with dividers. Our hero uses the "shield"
  copy and stacks stats as right-column cards.
- ☑ A2 Shield card + Three reinforcing layers added (settings-driven, design defaults): white "A shield for the food systems of
  tomorrow" card (pill chip, two paragraphs) + dark "Three reinforcing
  layers" card (3 numbered items).

## Future Food (ffs.html ↔ /local/sfsgame/index.php)

- ☑ F1 Page head separated (Adaptive missions copy); Mission: Zero Hunger hero with CURRENT RANK/TOTAL XP panel, real progress caption and badge-count chip (streak/leaderboard deferred — no data source);
  hero = "Mission: Zero Hunger" panel with CTA buttons; XP panel labels
  "CURRENT RANK / TOTAL XP", progress caption "N / 1000 XP", chips (streak
  deferred — no source; badge count real).
- ☑ F2 Play circle (when a mission has a URL) + duration pill moved
  bottom-left (ours top-right); status badge styles match.
- ☑ F3 Real +100 XP captions on earned badges (from xp_policy; was: the real
  per-badge XP from xp_policy).

## Resources & Standards (governance.html ↔ /local/sfsresources/index.php)

- ☐ R1 Kicker should be "Governance hub" style and lede per design
  (settings defaults). "System operational" pill + "Download report"
  deferred (no data).
- ☐ R2 KPI stat cards row (3 tiles with progress bars) missing — could be
  settings-driven like About stats.
- ☐ R3 Management tools: icons + count footers missing (ours text-only).
- ☐ R4 Library filters: prototype filters by AUDIENCE (Director/Supplier/…)
  with pill styles; ours filters by kind. Add audience filter alongside.
- ☐ R5 Doc rows: circular download button at the right edge missing;
  audience tag pill styling differs.
- ☐ R6 Quick validation card + side stats — deferred (needs a choice
  activity backend, Phase 9 UX item).

## Course/activity (course.html/activity.html)

- ☐ C1 Right rail (progress ring, teacher card, info list) still missing
  (long-standing domain 06 item).
- ☐ C2 Prototype section headers show per-section progress fractions; ours
  plain names.

## Cross-cutting

- ☐ X1 Topbar order: prototype = search left of icon cluster, help "?"
  button present, no user chip (user card only in sidebar). Ours keeps the
  functional user menu (accepted deviation) and lacks the help button.
