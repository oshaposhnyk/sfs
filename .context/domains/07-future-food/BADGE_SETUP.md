# Domain 07 — Future Food pilot badge setup

This note is the operator-facing setup guide for the pilot cohort. It records
the badge state that Future Food expects and how to keep it aligned with the
v1 architecture.

## Goal

Future Food must show real badge state:

- earned badges come from the Moodle badges API;
- locked badges show a safe criteria preview;
- XP and levels are derived from earned badges plus completed courses;
- no custom gamification tables are introduced for v1.

## Current pilot expectation

The dev site already uses site badges as the source of truth. The pilot setup
should keep that model stable:

- use site badges, not ad-hoc theme content;
- keep the issuer identity consistent across pilot badges;
- use the SecureFood School badge artwork/logo for the badge image;
- prefer completion-backed criteria over manual-by-role criteria when a real
  Moodle completion signal exists.

## Verified dev-site state

Verified on 2026-07-13 through the Moodle runtime:

| Badge | State | Criteria | Awarded to |
|---|---|---|---|
| First Steps | Site badge, active locked | Manual award by Manager | `sfstestuser` |
| Pathfinder | Site badge, active locked | Courseset completion for `SFS101` | `sfstestuser` |

`Pathfinder` is the canonical pilot progress badge. Its criteria resolves to
the active language variant when rendered in Future Food.

No `quiz` or `choice` activities exist on the dev site yet, so the decision
badge pattern remains deferred until a real activity is authored.

## Recommended badge pattern

| Badge | Purpose | Recommended criteria | Notes |
|---|---|---|---|
| Pathfinder | Primary pilot progress badge | Courseset completion for `SFS101` | This is the badge that should reliably move the XP hero and prove the v1 path. |
| Pilot decision badge | Scenario or decision proof | Real quiz or choice activity completion | Use this only when a real activity exists; do not fake the decision flow in the theme. |

## Operator checklist

1. Create or verify the badge as a site badge.
2. Set a clear issuer name/contact that matches SecureFood School branding.
3. Attach the shared badge image.
4. Configure the criteria using a real Moodle completion source.
5. Keep the badge active so Future Food can render it.
6. Issue at least one badge to the test learner to validate XP/level updates.
7. Re-check the Future Food page after the award so the achievements row and
   XP total reflect the live state.

## Guardrails

- Do not use a manual badge as a permanent substitute for a real completion
  signal if a completion source exists.
- Do not store private administrative notes in the badge description if the
  badge is meant to be shown to learners.
- Keep the criteria wording public and concise, because Future Food renders a
  short criteria preview for locked badges.
- If the site adds new pilot badges, keep the Future Food settings and the
  badge setup note in sync.
