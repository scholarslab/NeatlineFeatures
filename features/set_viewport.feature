
Feature: Set Viewport
  As an item editor
  I want to be able to set the viewport
  So that visitors to the site can have a consistent experience, which I have more control over

  Scenario: Can Set Viewport
    Given I am logged into the admin console
    And I click "Add a new item"
    And I click "Use Map" checkbox in "#element-38"
    And I move "#Elements-38-0-map" to "-78.5057164, 38.0365267"
    And I zoom "#Elements-38-0-map" to "6"
    And I wait 5 seconds
    When I click "Save View" in "#element-38"
    Then I should see text "View Saved" in "#element-38 .nlflash"
    And the viewport is defined in "Elements-38-0"

  @javascript
  Scenario: Viewport Settings Persist
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Viewport Settings Persist" for the "Elements-50-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I move "#Elements-38-0-map" to "-78.5057164, 38.0365267"
    And I zoom "#Elements-38-0-map" to "6"
    And I draw a point on "div.olMap"
    And I click on "Save View" in "#element-38"
    And I click on "Add Item"
    When I click "Viewport Settings Persist"
    Then "#dublin-core-coverage .map" should center on "-78.5057, 38.03652"
    And "#dublin-core-coverage .map" should be zoomed to "6"

