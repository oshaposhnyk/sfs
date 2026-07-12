# Domain 10 — enrol_learningplan (Phase 0)

Status: `[x]` completed — owner sign-off 2026-07-12 (Phase 0 closed without
PHPUnit/E2E; the test tasks below remain as deferred follow-ups and should be
picked up before Phase 3 relies heavily on enrolment).
Present surface: `lib.php` (full `enrol_plugin` API, `customint1` = plan id,
instance naming from plan), `db/access.php`, `db/upgrade.php`, privacy provider,
`lang/en` + `lang/uk`. Missing: PHPUnit tests, E2E verification.

## Purpose

Course enrolment adapter (standard Moodle enrol plugin) that `local_learningplans`
already depends on. `moodle_learning_plan_enrolment_service` calls
`enrol_get_plugin('learningplan')` and throws `coding_exception` when absent —
plan enrolment is currently broken at runtime.

## Scope

**In**: `public/enrol/learningplan/` — minimal, correct enrol plugin:
- `version.php` (component `enrol_learningplan`), `lang/en` + `lang/uk`.
- Plugin class extending `enrol_plugin`: no manual instance creation via UI
  (instances are created programmatically by the service), `allow_unenrol()`,
  `allow_manage()` policy, `roles_protected()`, instance naming
  ("Learning plan: {plan name}" via customtext/customint fields).
- `customint1` = plan id (the service already writes instances — check its
  `find or create` logic and match the field contract exactly, see
  `classes/infrastructure/moodle/enrolment/moodle_learning_plan_enrolment_service.php:74`).
- Correct behaviour on plugin disable, course deletion, user deletion.
- `db/access.php` capabilities: `enrol/learningplan:unenrol`, `:manage` (gated to
  managers), following core enrol plugins' pattern (`enrol/manual` as reference).
- Privacy provider (null provider if no personal data stored beyond core enrolments).

**Out**: any plan/domain logic — the plan side lives in `local_learningplans`;
sequencing decisions come from the local plugin which calls `enrol_user`/
`unenrol_user`/suspend.

## Key contract (verified from local plugin code)

- `enrol_user($instance, $userid, $roleid, 0, 0, ENROL_USER_ACTIVE)` is called by
  the service; the service then reads back `user_enrolments` and stores the link
  in `local_learningplans_enrl`.
- Unenrol path: service looks up the instance by plan+course and calls
  `unenrol_user`.
- Sequential release: local plugin controls when enrolment happens; the enrol
  plugin itself stays dumb.

## Tasks

- [x] Implement plugin class + version + langs (en, uk) — *owner, 2026-07-12*.
- [x] Capabilities + privacy provider — *owner, 2026-07-12*.
- [ ] Review status/suspend handling against `membership_status` semantics.
- [ ] PHPUnit: instance creation, enrol/unenrol round-trip via the local plugin
      service, double-enrol idempotency, unenrol cleans `_enrl` link.
- [ ] Manual E2E: create plan → add course → enrol user → user in course →
      unenrol → user removed; observer events fire.

## Acceptance criteria

- `local_learningplans` enrol/unenrol use cases succeed on a live instance.
- Disabling the enrol plugin degrades gracefully (clear error surfaced, no fatals).
- Idempotent: re-running enrolment does not duplicate `user_enrolments`.

## Dependencies

None. Everything in Phase 3 depends on this.
