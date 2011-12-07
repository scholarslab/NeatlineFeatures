
Feature: Display Non-Feature Coverage Data
  As a theme developer
  I want to be able to display non-feature coverage data
  So that I'm not locked into using coverage fields only with Neatline Features.

  Scenario: Display Non-Feature Coverage Data on Item Page
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage.php"
    And I enter "Display Non-Feature Coverage" for the "Elements-50-0-text"      # Title
    And I enter "Display Non-Feature Coverage" for the "Elements-49-0-text"      # Subject
    And I click on the "Raw" tab
    And I enter "Charlottesville, VA" into "Coverage"
    And I click on "Add Item"
    And I click "Display Non-Feature Coverage"
    When I click "View Public Page"
    Then I should see "Charlottesville, VA"
    But I should not see a map

