# SecureFood School — AI Agent Context

This directory is the single source of truth for any AI agent (or human) working on the
SecureFood School platform. Read it top-down before touching code.

## How to use this context

1. **Start here**, then read `PROJECT.md` for the big picture.
2. Read `ROADMAP.md` to understand the phase order and what is currently in flight.
3. Check `PROGRESS.md` for the live status board — it tells you what is done, in progress
   and blocked. **Never start work that another agent has marked in progress.**
4. Before writing any code, read every file in `conventions/` — they are non-negotiable.
5. Pick a domain from `domains/` and read its `CONTEXT.md`. Each domain is a bounded
   context with its own scope, tasks, acceptance criteria and dependencies.
6. When designing UI, use `design/` — it maps the HTML prototypes to Moodle pages,
   defines the design tokens and the BEM component inventory.

## How to update this context (mandatory)

- **Before starting a task**: mark it `[~]` (in progress) in the domain `CONTEXT.md`
  and update the domain status line in `PROGRESS.md`.
- **After finishing a task**: mark it `[x]`, note the commit-worthy summary in
  `PROGRESS.md` under "Recent work", and tick acceptance criteria you verified.
- **When you make an architectural decision**: append an ADR entry to `DECISIONS.md`
  (never rewrite existing entries).
- **When you discover a blocker or a wrong assumption**: record it in `PROGRESS.md`
  under "Blockers / risks" and in the affected domain's "Open questions".

Task status legend used across all files:

| Mark  | Meaning       |
|-------|---------------|
| `[ ]` | not started   |
| `[~]` | in progress   |
| `[x]` | done + verified |
| `[!]` | blocked       |

## Directory map

```
.context/
├── README.md                  ← you are here
├── PROJECT.md                 ← what we are building and why
├── ROADMAP.md                 ← phased development plan
├── PROGRESS.md                ← live status board (keep updated!)
├── DECISIONS.md               ← append-only ADR log
├── conventions/               ← architecture, Moodle, frontend, testing rules
├── design/                    ← design system, components, page mapping
└── domains/                   ← one folder per bounded context, each with CONTEXT.md
```

## Hard rules (summary — details in conventions/)

- Moodle 5.2 target. PHP 8.3 runtime, `declare(strict_types=1)` in every new file.
- Hexagonal architecture: domain → application → ports → infrastructure → presentation.
  Domain code never touches `$DB`, `$CFG`, `$USER`, `$OUTPUT`, `$PAGE`.
- No Moodle core modifications. Everything lives in `theme/securefood`,
  `local/learningplans` and `enrol/learningplan`.
- BEM with the `sfs-` prefix for all custom CSS. No hard-coded colours — design tokens only.
- Every user-facing string via `get_string()`; keep `en` and `uk` language packs in sync.
- No external CDNs (fonts and icons must be self-hosted in the theme).
- WCAG 2.1 AA is a requirement, not a stretch goal.
- Verify before claiming done: `php -l`, the affected PHPUnit tests, and a browser check
  for UI work.
