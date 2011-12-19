
Feature: Non-Feature Coverages
  As an item editor
  I want to be able to use the "Coverage" field for other purposes
  So that coverage data can be used for multiple purposes.

  Scenario: Add and Display Non-Feature Coverage Data
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Add Non-Feature Coverage Data" for the "Elements-50-0-text"  # Title
    And I enter "AddNonFeatureCoverageData" for the "Elements-49-0-text"  # Subject
    And I click the "Raw" tab on "#Elements-38-0-widget"
    And I enter "Charlottesville, VA" into "Elements-38-0-text"
    And I click on "Add Item"
    When I click on "Cucumber: Add Non-Feature Coverage Data"
    Then I should see text "Charlottesville, VA" in "#dublin-core-coverage"

