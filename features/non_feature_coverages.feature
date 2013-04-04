
Feature: Non-Feature Coverages
  As an item editor
  I want to be able to use the "Coverage" field for other purposes
  So that coverage data can be used for multiple purposes.

  Scenario: Add and Display Non-Feature Coverage Data
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Add Non-Feature Coverage Data" for the "Elements-50-0-text"  # Title
    And I enter "AddNonFeatureCoverageData" for the "Elements-49-0-text"  # Subject
    And I enter "Charlottesville, VA" into "Elements-38-0-free"
    And I click on "Add Item"
    When I click on "Cucumber: Add Non-Feature Coverage Data"
    Then I should see text "Charlottesville, VA" in "#dublin-core-coverage"

  Scenario: If existing coverage data does not contain features, don't show a map
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Default Raw Tab" for the "Elements-50-0-text"  # Title
    And I enter "Default Raw Tab" for the "Elements-49-0-text"  # Subject
    And I enter "Charlottesville, VA" into "Elements-38-0-free"
    And I click on "Add Item"
    And I click on "Default Raw Tab"
    When I click on "Edit"
    And I wait 5 seconds
    Then I should see "#Elements-38-0-free"
    But I should not see a map in "#element-38"

  @javascript
  Scenario: If a new coverage input is added, the existing map should still show
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: New Coverage, Existing Map" for the "Elements-50-0-text"   # Title
    And I enter "New Coverage, Existing Map" for the "Elements-49-0-text"             # Subject
    And I click "Use Map" checkbox in the path ".//*[@id='Elements-38-0-widget']/../.."
    And I draw a point on "div#Elements-38-0-map.olMap"
    When I click on "add_element_38"
    Then I should see "#Elements-38-1-free"
    And I should see "#Elements-38-0-map"
    And "Elements-38-0-mapon" should be checked

