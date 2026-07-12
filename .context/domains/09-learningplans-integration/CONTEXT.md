# Domain 09 — learningplans-integration (Phase 3)

Status: `[~]` core done (2026-07-12): `get_student_lab_overview` +
`set_active_learning_plan` use cases, `user_preference_repository` port +
adapter, `student_lab_status_policy` (continue-learning resolution) — all live
and E2E-verified. Remaining: external functions (needs `db/services.php` —
owner approval), stage modelling decision, N+1 audit/caching pass.

## Purpose

Everything the theme needs from `local_learningplans` that does not exist yet.
All work in this domain happens **inside the plugin** (ADR-005), following its
existing hexagonal layout exactly (see `conventions/architecture.md`).

## New read models / use cases (application layer)

- [ ] `get_student_lab_overview` — one call returning: active plan, memberships
      list (for switcher), per-stage models (status, counts, effort), per-course
      tile models (status, progress, module count, action, URL). Composes existing
      ports (`learning_plan_repository`, `completion_reader`, `membership_repository`,
      `progress_repository`, `url_resolver`) — extend ports only if data is missing.
- [ ] `resolve_continue_learning` — policy: first in-progress course → first
      incomplete unlocked course → first completed (review) course; returns target
      URL + label. Policy class in domain layer (pure, tested).
- [ ] `set_active_learning_plan` — validates membership, stores user preference
      (via a new `user_preference_repository` port — do **not** call
      `set_user_preference()` from application code).
- [ ] Stage semantics: current schema has no explicit stage/section table
      (`_crs` has ordering + sequential mode). **Decide**: v1 derives "stages" from
      the sequential course list (each course = step) or adds a `section` grouping
      (schema change → db/upgrade + owner approval). Record as ADR before coding.

## External functions (for theme AJAX)

- [ ] `local_learningplans_get_student_lab` (read; capability `:viewown`).
- [ ] `local_learningplans_set_active_plan` (write; own membership only).
- [ ] `local_learningplans_get_continue_learning` (read).
- Register in `db/services.php`; full parameter/return structures; context checks;
  PHPUnit `externallib` tests incl. capability-denial and cross-user denial.

## Rules

- Learners see only their own progress. No other users' data in any read model.
- New capabilities only if the existing `view/viewown/manage` set is insufficient.
- Progress values come from the existing cached progress infrastructure
  (`_prog` table + cache definition) — do not recompute in the theme request path.
- Events for new mutations (`active plan changed`) follow `classes/event/` pattern.
- Langs `en` + `uk` in the same commit; version bump + upgrade step for any
  `db/services.php` addition.

## Acceptance criteria

- Theme renders Student Lab with a single use-case call (one DB round-trip per
  repository, no N+1 across courses — verify with query logging).
- External functions reject: guests, non-members, other users' ids.
- All new logic covered by PHPUnit at domain/application level with fakes.

## Dependencies

Domain 10 (enrolment must work E2E). Blocks domain 05.

## Open questions

- [ ] Stage/section modelling (see above) — needs owner decision.
- [ ] Should `get_student_lab_overview` be cached (MUC, per user+plan) or is the
      `_prog` cache sufficient? Measure first.
