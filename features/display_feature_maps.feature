
Feature: Display Feature Maps on Item Pages
  As a theme developer
  I want to be able to include annotated maps on the item (and other) pages
  So that visitors to the site can view the feature data.

  @javascript
  Scenario: Display Feature Maps on Item Page
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Display Feature Maps" for the "Elements-50-0-text"      # Title
    And I enter "Display Feature Maps" for the "Elements-49-0-text"      # Subject
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div.olMap"
    And I draw a line on "div.olMap"
    And I click on "Add Item"
    And I click "Display Feature Maps"
    When I click "View Public Page"
    Then I should see an OpenLayers map
    And the map in "#dublin-core-coverage" should have a point feature
    And the map in "#dublin-core-coverage" should have a line feature
    And element "#dublin-core-coverage .freetext" should not be on the page

