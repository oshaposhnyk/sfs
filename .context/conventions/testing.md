# Conventions — testing & definition of done

## What gets tested where

| Layer | Test type | Pattern to follow |
|-------|-----------|-------------------|
| Domain entities/VOs/policies | Pure PHPUnit, no DB (`advanced_testcase` not required) | `tests/learning_plan_domain_test.php`, `tests/default_progress_calculation_policy_test.php` |
| Application use cases | PHPUnit with fake/in-memory port implementations | new — fakes live under `tests/fixtures/` |
| Infrastructure adapters | PHPUnit `advanced_testcase` with `$this->resetAfterTest()` + generators | `tests/moodle_learning_plan_repository_test.php` |
| External functions | `externallib_advanced_testcase`: capability rejection + happy path + return structure | new |
| Events | dispatch assertions via `redirectEvents()` | `tests/moodle_event_dispatcher_test.php` |
| User flows | Behat | `tests/behat/learningplans.feature` |
| Theme output classes | PHPUnit on template context arrays (no HTML string asserts) | new |
| Privacy providers | provider testcase per Moodle docs | new for theme prefs |

Minimum per new feature: domain/application tests + one adapter or external test +
capability-denial test for every new endpoint.

## Commands (run inside the php-fpm container)

```bash
# Bootstrap once per DB reset
docker compose exec php-fpm php public/admin/tool/phpunit/cli/init.php

# Run a plugin's tests
docker compose exec php-fpm vendor/bin/phpunit \
  --configuration public/phpunit.xml \
  public/local/learningplans/tests

# Single file
docker compose exec php-fpm vendor/bin/phpunit \
  public/local/learningplans/tests/learning_plan_domain_test.php

# Lint every touched PHP file (always, before claiming done)
php -l path/to/file.php

# Coding style
vendor/bin/phpcs --standard=phpcs.xml.dist public/local/learningplans
vendor/bin/phpcbf --standard=phpcs.xml.dist <paths>   # autofix

# Purge caches after SCSS/template/AMD/lang changes
docker compose exec php-fpm php public/admin/cli/purge_caches.php

# Behat (when configured)
docker compose exec php-fpm php public/admin/tool/behat/cli/init.php
docker compose exec php-fpm vendor/bin/behat --config <behat.yml> --tags=@local_learningplans
```

## Definition of done (every task)

1. `php -l` clean on all touched files; PHPCS clean.
2. New/affected PHPUnit tests pass — paste the actual output into your report,
   never claim green without running.
3. UI work: verified in a real browser in **both modes** (standard + SecureFood),
   both colour schemes, at 1440px / 1024px / 375px widths; keyboard-only walkthrough.
4. `en` + `uk` strings updated together; no missing-string debug notices.
5. Caches purged and page re-verified (template/SCSS changes are cached hard).
6. `.context/PROGRESS.md` + domain `CONTEXT.md` updated.
7. No git actions (commit/push) without explicit owner approval.

## Manual QA checklist template (copy into domain work)

- [ ] Renders correctly light + dark
- [ ] Standard mode unaffected
- [ ] Mobile drawer + collapsed sidebar states
- [ ] Keyboard: tab order, focus visible, Escape closes menus
- [ ] Screen reader labels on icon buttons
- [ ] Empty state / loading state / error state
- [ ] Locked content not launchable via UI or direct URL
- [ ] No console errors; no failed network requests; no CDN requests
