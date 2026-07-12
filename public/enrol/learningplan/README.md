# enrol_learningplan

Moodle 5.2 enrol plugin used by `local_learningplans` to create and manage course enrolments tied to a Learning Plan.

## Key behavior

- Uses core `enrol` and `user_enrolments` tables.
- Stores the source Learning Plan id in `enrol.customint1`.
- Supports one instance per `(courseid, planid)` pair.
- Exposes no standalone management workflow; instances are created programmatically by `local_learningplans`.

