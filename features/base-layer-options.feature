
Feature: Users can select a base layer from a set of options
  As an item editor
  I want to be able to select a base layer from a set of options
  So that I have more control over the information and look of the feature map

  @javascript
  Scenario: Select a Base Layer
    Given I am logged into the admin console
    And I click "Add a new item"
    And I click "Use Map" checkbox in "#element-38"
    And I switch to the "Google Physical" base layer on "#Elements-38-0-map"
    Then I see "#Elements-38-0-base_layer" contains "gphy"

  @javascript
  Scenario: Selected Base Layer should persist
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Base Layer Settings Persist" for the "Elements-50-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I switch to the "Google Physical" base layer on "#Elements-38-0-map"
    And I draw a point on "div.olMap"
    And I click on "Add Item"
    And I click "Base Layer Settings Persist"
    When I click "Edit"
    Then I see "#Elements-38-0-base_layer" contains "gphy"

  @javascript
  Scenario: Selected Base Layer should be visible on display
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Base Layer Settings Display" for the "Elements-50-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I switch to the "Google Physical" base layer on "#Elements-38-0-map"
    And I draw a point on "div.olMap"
    And I click on "Add Item"
    And I click "Base Layer Settings Display"
    When I click "View Public Page"
    Then ".nlfeatures" should have the base layer "gphy"

