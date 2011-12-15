
Feature: Non-Feature Coverages
  As an item editor
  I want to be able to use the "Coverage" field for other purposes
  So that coverage data can be used for multiple purposes.

  Scenario: AddNonFeatureCoverageData
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: AddNonFeatureCoverageData" for the "Elements-50-0-text"  # Title
    And I enter "AddNonFeatureCoverageData" for the "Elements-49-0-text"  # Subject
    And I click the "Raw" tab
    And I enter "Charlottesville, VA" into "Coverage"
    And I click on "Add Item"
    When I click on "AddNonFeatureCoverageData"
    Then I should see "Charlottesville, VA" in "Coverage"

