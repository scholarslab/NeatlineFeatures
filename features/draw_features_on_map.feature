
# Elements-50-0-text => Title
# Elements-49-0-text => Subject
# Elements-38        => Coverage

Feature: Draw Features on a Map
  In order to add geospatial metadata to an item
  As an item editor
  I want to be able to annotate an item by drawing features on a map.

  Scenario: Coverage Has a Map
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    When I click on the "#element-38" field "NL Features" tab
    Then I should see a map in "#element-38"
    But I should not see ".nlfeatures-edit-raw" in "#element-38"

  Scenario: Draw Point on a Map
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    When I draw a point on "div.olMap"
    Then a point is defined in "#Elements-38-0-text"

  Scenario: Draw Line on a Map
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    When I draw a line on "div.olMap"
    Then a line is defined in "#Elements-38-0-text"

  Scenario: Save Data Drawn on Coverage Map
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Draw On Coverage Map" for the "Elements-50-0-text"     # Title
    And I enter "Draw On Coverage Map" for the "Elements-49-0-text"               # Subject
    And I click on the "#element-38" field "NL Features" tab
    And I click on the "Coverages" map
    And I click and drag on the "Coverages" map
    And I click on "Add Item"
    When I click on "Draw On Coverage Map"
    Then I should see a map
    And the map should have a point feature
    And the map should have a line feature

