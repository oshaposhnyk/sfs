@local @local_learningplans
Feature: Learning plan management
  In order to orchestrate multiple courses
  As an authorized user
  I need to create and manage learning plans.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | lpstudent | LP        | Student  | lpstudent@example.com |

  Scenario: Manager creates a learning plan
    Given I log in as "admin"
    And I am on "/local/learningplans/edit.php"
    When I set the field "Learning plan name" to "Plan Behat"
    And I press "Save changes"
    Then I should see "Learning plan created."
    And I should see "Plan Behat"

  Scenario: User without capability cannot access management
    Given I log in as "lpstudent"
    When I am on "/local/learningplans/index.php"
    Then I should see "You do not have permission"

