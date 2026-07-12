# Page mapping — prototype → Moodle implementation

How each design prototype becomes a real Moodle page in `theme_securefood`
(SecureFood mode only; standard mode = stock Boost). Data must come from Moodle
APIs / `local_learningplans` use cases — prototype content is mock data.

## insights.html → About the Project (site home / dashboard)

- **Moodle page**: custom frontpage layout (`layout/frontpage.php`) or `/my`
  dashboard override in SecureFood mode. Breadcrumb: Dashboard → About.
- **Data**: KPI values from admin-configured settings + aggregations
  (`local_learningplans` stats use case, future); Living Labs map + hub list from
  theme settings (JSON) in first iteration; feed from course announcements or
  static theme setting blocks initially.
- **Note**: prototype includes a Leaflet-style map — decide replacement (static
  SVG map or self-hosted library) before implementation; no CDN.

## l4c.html → Student Lab (learner learning-plan dashboard)

- **Moodle page**: replaces/extends `local_learningplans/my.php` experience; nav
  item "Student Lab". Breadcrumb: Dashboard → Student Lab.
- **Data (all via plugin use cases — ADR-005)**:
  - Plan bar: active plan name, courses done/total, completion %, level, current
    stage, progress → `get_learning_plan_overview` + progress read model.
  - Plan switcher: user's memberships (`list` use case) + active-plan user preference.
  - Stages: plugin sequencing model; statuses done/in-progress/up-next/locked.
  - Course tiles: course fullname/summary, module count (course modinfo), effort
    (custom course field or plugin metadata — open question in domain 05), progress
    (completion reader), action by status.
  - Continue learning: dedicated use case (first active → first incomplete →
    first review-able course).
- **Locking**: locked tiles are not links; server re-checks on course access
  (sequential enrolment release already handles this — do not rely on UI).

## course.html → Course page

- **Moodle page**: `layout/course.php` + course-format template overrides in
  SecureFood mode. Banner (title, chips, due pill), section cards with per-section
  progress, right rail (overall progress ring, teacher card, info list).
- **Data**: course, sections and cm_info from modinfo; completion via completion
  API; teacher from role assignments; plan context (which plan/stage this course
  belongs to) via plugin use case.

## activity.html → Activity page

- **Moodle page**: `layout/incourse.php` styling around standard module rendering.
  The prototype's custom lesson/quiz player is **not** re-implemented — standard
  mods (page, quiz, h5p) get restyled via scoped SCSS; prev/next navigation uses
  Moodle activity navigation, restyled.

## ffs.html → Future Food

- **Moodle page**: theme page or `local_` page (phase 4 decision). Achievements ←
  Moodle badges API; XP/level ← derived (badges count / plugin metadata) in first
  iteration; missions ← curated courses/activities list from settings.
- Decision module (scenario quiz) → link into real quiz activities; no standalone
  quiz engine in the theme.

## governance.html → Resources & Standards

- **Moodle page**: theme page listing curated resources. Document library ←
  File API-backed area or a curated course's resources; filters by type/role.
  Management tools grid ← capability-gated links (admin/manager only).
- Download endpoints must re-check capabilities (`pluginfile` handler).

## Settings (brief only — no HTML prototype)

- Profile/language/timezone/password → link to (restyled) core pages first
  iteration; custom unified settings page later.
- Appearance (light/dark/system) → domain 03 preference.
- Notification toggles → message processor preferences.
- Privacy toggles + sessions → phase 5, custom preferences + sessions list.

## Shell chrome present on all pages (domain 01)

Sidebar nav: About the Project (`info`), Student Lab (`school`), Future Food
(`videogame_asset`), Resources & Standards (`gavel`) under "Learning";
Notifications (`notifications`), Messages (`forum`) under "Account".
User card pinned to bottom: avatar initials, name, "Student · Cohort 2026"-style
role line (real cohort from cohort membership).
Topbar: sidebar toggle, breadcrumbs, search (→ Moodle global search),
theme toggle, notifications popover, help.
