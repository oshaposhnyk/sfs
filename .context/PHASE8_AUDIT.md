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

- ☑ R1 Kicker "Governance hub" + design lede + "Resource library" heading
  (lang defaults, en+uk). "System operational" pill + "Download report"
  deferred (no data).
- ☑ R2 KPI stat cards row: settings JSON `local_sfsresources/stats`
  (label/value/suffix/percent/sub/variant teal|amber|deep), design defaults.
- ☑ R3 Management tools: icon tiles (assignment/groups/workspace_premium/
  school, colour variants) + count footers with live counts (plans, cohorts,
  badges, courses).
- ☑ R4 Library filters now filter by AUDIENCE (pills from doc audiences;
  uploaded files appear in All only — they carry no audience tag). `kind`
  param still honoured, no UI.
- ☑ R5 Circular download button (glyph, text kept visually-hidden for SR and
  standard mode) + audience tag as a pill chip. Curated rows without a URL
  still have no button (nothing to link to).
- ☐ R6 Quick validation card + side stats — deferred (needs a choice
  activity backend, Phase 9 UX item).

## Course/activity (course.html/activity.html)

- ☑ C1 Right rail added (layout-level, shell only): "Next up" card (first
  incomplete activity + Continue), "Course info" list (sections, activities,
  progress %, language, format — all real data), "Teachers" card (role-based,
  renders only when the course has teachers). Built by
  `theme_securefood\courserail`, template `course_rail.mustache`; stacks
  below content ≤1100px. Prototype's mentor/peers + resources cards deferred
  (no data source).
- ☑ C2 Per-section "done / total" fractions with Complete/In progress/
  Up next labels, decorated by the `sectionprogress` AMD module (progressive
  enhancement — plain headers without JS). Viewer's own completion only.

## Cross-cutting

- ☐ X1 Topbar order: prototype = search left of icon cluster, help "?"
  button present, no user chip (user card only in sidebar). Ours keeps the
  functional user menu (accepted deviation) and lacks the help button.
- ☑ X2 (bug, found during C) Mobile ≤820px reserved the collapsed icon-rail
  grid column under the drawer (`.sfs-sidebar-collapsed` rule outranked the
  mobile `1fr`); content now spans the full width.
