
Feature: Display Multiple Coverages
  As a theme developer
  I want to be able to display coverage data from a mixed collection of fields
  So that visitors can see all coverage data.

  Scenario: All Non-Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-delim.php"
    And I enter "Display All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Display All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click on the "Raw" tab
    And I enter "Charlottesville, VA" into "First Coverage"
    And I click on "Add Coverage"
    And I click on the "Raw" tab on "Second Coverage"
    And I enter "UVa" into "Second Coverage"
    And I click on "Add Item"
    And I click "Display All Non-Feature Coverages"
    When I click "View Public Page"
    Then I should see "Charlottesville, VA"
    And I should see "UVa"

  Scenario: All Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-delim.php"
    And I enter "Display All Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Display All Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    And I click on the "First Coverages" map
    And I click on "Add Coverage"
    And I click on the "Features" tab on "Second Coverage"
    And I click and drag on the "Second Coverages" map
    And I click on "Add Item"
    And I click "Display All Feature Coverages"
    When I click "View Public Page"
    Then a point is visible on a map
    And a line is visible on a map

  Scenario: Mixed Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-delim.php"
    And I enter "Display Mixed Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Display Mixed Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    And I click on the "First Coverages" map
    And I click on "Add Coverage"
    And I click on the "Raw" tab on "Second Coverage"
    And I enter "UVa" into "Second Coverage"
    And I click on "Add Item"
    And I click "Display Mixed Feature Coverages"
    When I click "View Public Page"
    Then a point is visible on a map
    And I should see "UVa"

