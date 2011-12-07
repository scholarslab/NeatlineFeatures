
Feature: Test Coverage Data
  As a theme developer
  I want to be able to test whether a coverage datum is feature data or not
  So that I can determine how to handle different types of coverage data correctly.

  Scenario: Test All Non-Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-test.php"
    And I enter "Iterate All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click on the "Raw" tab
    And I enter "Charlottesville, VA" into "First Coverage"
    And I click on "Add Coverage"
    And I click on the "Raw" tab on "Second Coverage"
    And I enter "UVa" into "Second Coverage"
    And I click on "Add Item"
    And I click "Iterate All Non-Feature Coverages"
    When I click "View Public Page"
    Then I should see "false"
    And I should not see "true"

  Scenario: All Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-test.php"
    And I enter "Iterate All Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    And I click on the "First Coverages" map
    And I click on "Add Coverage"
    And I click on the "Features" tab on "Second Coverage"
    And I click and drag on the "Second Coverages" map
    And I click on "Add Item"
    And I click "Iterate All Feature Coverages"
    When I click "View Public Page"
    Then I should see "true"
    And I should not see "false"

  Scenario: Mixed Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-test.php"
    And I enter "Iterate Mixed Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate Mixed Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    And I click on the "First Coverages" map
    And I click on "Add Coverage"
    And I click on the "Raw" tab on "Second Coverage"
    And I enter "UVa" into "Second Coverage"
    And I click on "Add Item"
    And I click "Iterate Mixed Feature Coverages"
    When I click "View Public Page"
    Then I should see "true"
    And I should see "false"


