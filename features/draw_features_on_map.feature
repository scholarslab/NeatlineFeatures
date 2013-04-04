
# Elements-50-0-text => Title
# Elements-49-0-text => Subject
# Elements-38        => Coverage

Feature: Draw Features on a Map
  In order to add geospatial metadata to an item
  As an item editor
  I want to be able to annotate an item by drawing features on a map.

  Scenario: Coverage has no map displayed by default
    Given I am logged into the admin console
    When I click "Add a new item"
    And I wait 5 seconds
    Then I should not see a map in "#element-38" 

  Scenario: Coverage has a "Use Map" checkbox
    Given I am logged into the admin console
    And I click "Add a new item"
    When I click "Use Map" checkbox in "#element-38"
    Then I should see a map in "#element-38"

  Scenario: Coverage Has a Map
    Given I am logged into the admin console
    And I click "Add a new item"
    When I click "Use Map" checkbox in "#element-38"
    Then I should see a map in "#element-38"
    And I should see "#Elements-38-0-free"
    But I should not see "#Elements-38-0-text"

  @kml
  @javascript
  Scenario: Draw Point on a Map
    Given I am logged into the admin console
    And I click "Add a new item"
    And I click "Use Map" checkbox in "#element-38"
    When I draw a point on "div.olMap"
    Then a point is defined in "#Elements-38-0-geo"

  @kml
  @javascript
  Scenario: Draw Line on a Map
    Given I am logged into the admin console
    And I click "Add a new item"
    And I click "Use Map" checkbox in "#element-38"
    When I draw a line on "div.olMap"
    Then a line is defined in "#Elements-38-0-geo"

  @kml
  @javascript
  Scenario: Save Data Drawn on Coverage Map
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Draw On Coverage Map" for the "Elements-50-0-text"     # Title
    And I enter "Draw On Coverage Map" for the "Elements-49-0-text"               # Subject
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div.olMap"
    And I draw a line on "div.olMap"
    And I click on "Add Item"
    When I click on "Draw On Coverage Map"
    Then I should see a map in "#dublin-core-coverage"
    And the map in "#dublin-core-coverage" should have a point feature
    And the map in "#dublin-core-coverage" should have a line feature

  @javascript
  Scenario: 'Use Map' True should Persist
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Use Map True should Persist" for the "Elements-50-0-text"
    And I enter "Use Map True should Persist" for the "Elements-49-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div.olMap"
    And I draw a line on "div.olMap"
    And I click on "Add Item"
    And I click "Use Map True should Persist"
    And I click "Edit"
    Then "Elements-38-0-mapon" should be checked
    And I should see a map in "#element-38"

  Scenario: 'Use Map' False should Persist
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Use Map False should Persist" for the "Elements-50-0-text"
    And I enter "Use Map False should Persist" for the "Elements-49-0-text"
    And I click on "Add Item"
    And I click "Use Map False should Persist"
    And I click "Edit"
    Then "Elements-38-0-mapon" should not be checked
    And I should not see a map in "#element-38"

  @kml
  @javascript
  Scenario: Free Text should not Contain KML
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Free Text should not Contain KML" for the "Elements-50-0-text"
    And I enter "Free Text should not Contain KML" for the "Elements-49-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div.olMap"
    And I draw a line on "div.olMap"
    And I click on "Add Item"
    And I click "Free Text should not Contain KML"
    And I click "Edit"
    Then "Elements-38-0-mapon" should be checked
    And I should see a map in "#element-38"
    And I should see that "#Elements-38-0-free" does not contain "kml"

  # This fails since GeoLocation isn't turned on automatically in the browser.
  #Scenario: Map Location Should Default to the User's Location
    #Given I am logged into the admin console
    #When I click "Add a new item"
    #Then "#Elements-38-0-map" should center on my location

