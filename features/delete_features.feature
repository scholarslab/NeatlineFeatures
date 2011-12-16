
Feature: Delete Features from an Item
  In order to removee geospatial metadata from an item
  As an item editor
  I want to be able to remove feature annotations from an item.

  Scenario: Cannot Remove Only Coverage
    Given I am logged into the admin console
    When I click "Add a new item to your archive"
    Then I see 1 ".input-block" in "#element-38"
    But I should not see "#remove_element_38" in "#element-38"

  Scenario: Add Coverage
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    When I click "add_element_38"
    Then I should see "#Elements-38-1-widget" in "#element-38"
    And I see 2 ".input-block" in "#element-38"
    And I see 2 "input.remove-element" in "#element-38"

  Scenario: RemoveFirstCoverage
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I click the "Raw" tab on "First Coverage"
    And I enter "1" into "First Coverage"
    And I click "Add Coverage"
    And I click the "Raw" tab on "Second Coverage"
    And I enter "2" into "Second Coverage"
    When I click "Delete" on the "First Coverage"
    Then I see 1 "Coverage" field
    And I see "2" in "Second Coverage"
    And the delete button is disabled on "Second Coverage"

  Scenario: RemoveSecondCoverage
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I click the "Raw" tab on "Second Coverage"
    And I enter "1" into "Second Coverage"
    And I click "Add Coverage"
    And I click the "Raw" tab on "Second Coverage"
    And I enter "2" into "Second Coverage"
    When I click "Delete" on the "Second Coverage"
    Then I see 1 "Coverage" field
    And I see "1" in "First Coverage"
    And the delete button is disabled on "First Coverage"

