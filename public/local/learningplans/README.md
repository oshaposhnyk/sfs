# local_learningplans

Moodle 5.2 local plugin for managing course-based Learning Plans.

## What it provides

- Create, update, archive/activate, and delete Learning Plans.
- Add/remove/reorder courses in each plan.
- Sequential mode with optional sequential enrolment release.
- Enrol and unenrol users in a plan (single and bulk by user IDs).
- Automatic course enrolment through `enrol_learningplan`.
- Shared plan progress calculation and caching.
- Moodle events for all key plan lifecycle and enrolment actions.
- Observer-based progress updates on core completion/enrolment events.
- Privacy API provider for membership, enrolment link, and progress data.

## Architecture

- Domain: entities, value objects, and progress policy.
- Application: service + explicit use case classes.
- Ports/adapters: Moodle DML, completion, enrolment, events, access, cache, transactions.
- Presentation: thin controllers + Moodle forms + Mustache templates.

## Plugin suite boundary

- `local_learningplans` owns plan domain, orchestration, UI, progress, and custom events.
- `enrol_learningplan` is a course enrolment adapter (Moodle enrol plugin).

## Notes

- User-facing term remains "Learning Plan".
- Internal component/table/event names avoid conflicts with competency `tool_lp` naming.
- Sequencing is enforced through controlled enrolment release. Hard course locking is not forced by UI hacks.

