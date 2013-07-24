
Feature: Delete Features from an Item
  In order to removee geospatial metadata from an item
  As an item editor
  I want to be able to remove feature annotations from an item.

  Scenario: Cannot Remove Only Coverage
    Given I am logged into the admin console
    When I click "Add a new item"
    Then I see 1 ".input-block" in "#element-38"
    But I should not see "#remove_element_38" in "#element-38"

  Scenario: Add Coverage
    Given I am logged into the admin console
    And I click "Add a new item"
    When I click "add_element_38"
    Then I should see "#Elements-38-1-free" in "#element-38"
    And I see 2 ".input-block" in "#element-38"
    And I see 2 "input.remove-element" in "#element-38"

  @javascript
  Scenario: Remove First Coverage
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "1" into "Elements-38-0-free"
    And I click "add_element_38"
    And I enter "2" into "Elements-38-1-free"
    When I click on XPath ".//div[@id='element-38']//div[@class='input-block'][1]//input[@value='Remove']"
    And I click "OK" in the alert
    Then I see 1 ".input-block" in "#element-38"
    And I see "#Elements-38-1-free" contains "2"
    But I should not see "#remove_element_38" in "#element-38"

  @javascript
  Scenario: Remove Second Coverage
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "1" into "Elements-38-0-free"
    And I click "add_element_38"
    And I enter "2" into "Elements-38-1-free"
    When I click on XPath ".//div[@id='element-38']//div[@class='input-block'][2]//input[@value='Remove']"
    And I click "OK" in the alert
    Then I see 1 ".input-block" in "#element-38"
    And I see "#Elements-38-0-text" contains "1"
    But I should not see "#remove_element_38" in "#element-38"

