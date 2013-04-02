
Feature: Display coverage data on Item Pages
  As a theme developer
  I want to be able to include coverage data in item displays
  So that visitors to the site can view the feature data.

  @kml
  @javascript
  Scenario: Display feature maps on item page
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Display Feature Maps" for the "Elements-50-0-text"      # Title
    And I enter "Display Feature Maps" for the "Elements-49-0-text"      # Subject
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div.olMap"
    And I draw a line on "div.olMap"
    And I click on "Add Item"
    When I click "Display Feature Maps"
    Then I should see an OpenLayers map
    And the map in "#dublin-core-coverage" should have a point feature
    And the map in "#dublin-core-coverage" should have a line feature
    And element "#dublin-core-coverage .freetext" should not be on the page

  @kml
  @javascript
  Scenario: Display multimodal coverages on item page
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Multimodal Coverage" for the "Elements-50-0-text"
    And I enter "Multimodal Coverage" for the "Elements-49-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div#Elements-38-0-map.olMap"
    And I enter "A pointed question" into "Elements-38-0-free"
    And I click on "Add Item"
    When I click "Multimodal Coverage"
    Then the map at "#dublin-core-coverage .map" should display a point feature
    And I should see text "A pointed question" in "#dublin-core-coverage"
    But I should not see text "kml" in "#dublin-core-coverage .nlfeatures"

  @kml
  @javascript
  Scenario: Display multimodal HTML coverages on the item page
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Multimodal HTML Coverage" for the "Elements-50-0-text"
    And I enter "Multimodal Coverage" for the "Elements-49-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div#Elements-38-0-map.olMap"
    And I enter "A pointed question" into "Elements-38-0-free"
    And I check "Elements[38][0][html]"
    And I click on "Add Item"
    When I click "Multimodal HTML Coverage"
    And I take a screenshot
    Then the map at "#dublin-core-coverage .map" should display a point feature
    And I should see text "A pointed question" in "#dublin-core-coverage"
    But I should not see text "kml" in "#dublin-core-coverage .nlfeatures"

  Scenario: Display non-feature coverage data on item page
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Display Non-Feature Coverage" for the "Elements-50-0-text"      # Title
    And I enter "Display Non-Feature Coverage" for the "Elements-49-0-text"      # Subject
    And I enter "Charlottesville, VA" into "Elements-38-0-free"
    And I click on "Add Item"
    When I click "Display Non-Feature Coverage"
    Then I should see text "Charlottesville, VA" in "#dublin-core-coverage"
    But I should not see an OpenLayers map in the "#dublin-core-coverage" field

  @javascript
  Scenario: Display HTML non-feature coverage data on item page
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Display HTML Non-Feature Coverage" for the "Elements-50-0-text"      # Title
    And I enter "Display Non-Feature Coverage" for the "Elements-49-0-text"      # Subject
    And I enter "Charlottesville, VA" into "Elements-38-0-free"
    And I check "Elements[38][0][html]"
    And I click on "Add Item"
    When I click "Display HTML Non-Feature Coverage"
    Then I should see text "Charlottesville, VA" in "#dublin-core-coverage"
    But I should not see an OpenLayers map in the "#dublin-core-coverage" field
    And I should not see text "OpenLayers export" in "#dublin-core-coverage"

