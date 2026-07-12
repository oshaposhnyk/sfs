# Domain 08 — resources-standards / governance (Phase 4)

Status: `[ ]` not started.

## Purpose

"Resources & Standards" page. Prototype: `governance.html` (management tools grid,
document library with type/role filters, quick-validation card, side stats).

## Composition & data

| Section | Block | Data source |
|---------|-------|-------------|
| Management tools grid | `sfs-tool` | capability-gated links (plan management → `local_learningplans/index.php`, cohorts, reports); hidden entirely for learners without any tool capability |
| Document library | `sfs-doclist` | curated file area: a designated course's resources OR a theme/plugin filearea with metadata (type, audience role, updated). **Decision needed** — recommend plugin filearea + simple metadata table if filters must be structured; else resources of a hidden "Library" course v1 |
| Filters (type: PDF/DOC/XLS/ZIP; audience) | `sfs-filterpill` | client-side on rendered list v1 |
| Quick validation card | `sfs-validate` | choice/feedback activity link v1 (no custom voting backend) |
| Side stats | `sfs-sidestat` | cheap counts (documents, standards, updates) |

## Security notes (this page is the risky one)

- Every download goes through `pluginfile.php` with capability re-check — never
  direct file URLs.
- Role-audience tags are presentation hints, **not** access control; actual access
  enforced server-side per file context.
- Management tool tiles: capability check per tile, no leaking counts the user
  cannot see.

## Tasks

- [ ] ADR: document library storage model (course resources vs plugin filearea).
- [ ] Renderables + templates + SCSS (`_tool.scss`, `_doclist.scss`,
      `_validate.scss`, `_sidestat.scss`).
- [ ] File serving + capability checks + icon-by-mimetype mapping.
- [ ] Filters (progressive enhancement; full list without JS).
- [ ] Behat: learner sees library but no management tools; manager sees both;
      forbidden file returns 403 not 500.

## Acceptance criteria

- Matches `governance.html`; downloads audited (standard Moodle logging).
- No document metadata visible to users who cannot access the file.

## Dependencies

Phases 1–2.
