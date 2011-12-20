
Feature: Display Non-Feature Coverage Data
  As a theme developer
  I want to be able to display non-feature coverage data
  So that I'm not locked into using coverage fields only with Neatline Features.

  Scenario: Display Non-Feature Coverage Data on Item Page
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Display Non-Feature Coverage" for the "Elements-50-0-text"      # Title
    And I enter "Display Non-Feature Coverage" for the "Elements-49-0-text"      # Subject
    And I click the "Raw" tab on "#element-38"
    And I enter "Charlottesville, VA" into "Elements-38-0-text"
    And I click on "Add Item"
    And I click "Display Non-Feature Coverage"
    When I click "View Public Page"
    Then I should see text "Charlottesville, VA" in "#dublin-core-coverage"
    But I should not see an OpenLayers map in the "#dublin-core-coverage" field

