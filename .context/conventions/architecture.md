# Conventions — architecture (SOLID / DDD / hexagonal)

These rules already govern `local_learningplans` — study it as the reference
implementation before writing anything new. All new code (plugin *and* theme)
follows the same layering.

## Layers and dependency direction

```
presentation → application → domain
       ↘ infrastructure ↗
```

Dependencies point inwards only. Domain knows nothing about Moodle.

### Domain (`classes/domain/`)
- Pure PHP: entities, value objects, policies, domain exceptions.
- **Forbidden:** `$DB`, `$CFG`, `$USER`, `$OUTPUT`, `$PAGE`, `$_REQUEST`,
  `get_string()`, `moodle_url`, any `core_*` API, any I/O.
- Entities enforce their invariants (see `learning_plan::rename()` style guards).
- Value objects are immutable and validate on construction (`progress`,
  `enrolment_mode`, `membership_status`, `membership_source`).
- Policies capture business rules as interfaces + default implementations
  (`progress_calculation_policy`).

### Application (`classes/application/`)
- One class per use case, single `execute()`-style public method
  (`create_learning_plan`, `enrol_user_into_learning_plan`, …).
- Orchestrates: permission check (via port) → transaction (via port) → domain
  operations → repository writes → event dispatch (via port).
- **Forbidden:** HTML, SQL strings, direct output, Moodle globals.
- `port/` holds the interfaces the use cases depend on. New external dependency ⇒
  new port interface, never a concrete Moodle call.

### Infrastructure (`classes/infrastructure/`)
- Moodle adapters implementing the ports: `persistence/` (DML repositories),
  `moodle/access`, `moodle/completion`, `moodle/enrolment`, `moodle/event`,
  `moodle/time`, `moodle/transaction`, `moodle/url`, `moodle/cohort`.
- The only layer allowed to touch `$DB` and Moodle APIs.
- `moodle/factory/learning_plan_service_factory.php` wires the object graph —
  extend the factory when adding ports; no service locators elsewhere.
- Controllers (`moodle/controller/`) are thin: resolve context, `require_login()`,
  call use case, hand a view model to the renderer/template. No business logic.

### Presentation
- Entry scripts (`index.php`, `view.php`, …) only: bootstrap, params via
  `required_param()`/`optional_param()` with proper `PARAM_*`, delegate to a controller.
- Mustache templates receive pre-shaped scalar/array data — no logic beyond
  sections/inverted sections.
- Theme renderers/output classes shape template context; they may call use cases
  via the factory but contain no domain rules.

## SOLID application notes

- **S** — one use case per action; one repository per aggregate; small final classes.
- **O** — extend via new policies/ports, not by editing existing use cases.
- **L** — adapters must honour port contracts exactly (including exception types).
- **I** — many narrow ports (see the 15 existing ones) over god-interfaces.
- **D** — application depends on port interfaces; the factory injects adapters.

## DDD boundaries in this project

| Bounded context | Owner | Notes |
|-----------------|-------|-------|
| Learning plans (plans, stages, membership, progress, sequencing) | `local_learningplans` | Sole owner of plan domain logic |
| Course enrolment mechanics | `enrol_learningplan` | Pure Moodle enrol adapter, no plan logic |
| Visual experience (shell, pages, tokens, mode/theme preferences) | `theme_securefood` | Presentation-only; consumes plugin use cases |
| Gamification (XP, levels, missions) | TBD (phase 4) | Do not smuggle into the theme; likely `local_` plugin later |

Cross-context communication: Moodle events + explicit use cases/external functions.
Never reach into another context's tables.

## General rules

- `declare(strict_types=1);` in every new PHP file, after the GPL header.
- Final classes by default; interfaces for every port.
- Meaningful domain exceptions extending `domain_exception`.
- No drive-by refactors: minimal diffs, one concern per change.
- Ask before touching: `db/`, `lib.php`, capabilities, auth, cron definitions.
