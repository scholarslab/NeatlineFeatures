
Feature: Draw Features on a Map
  In order to add geospatial metadata to an item
  As an item editor
  I want to be able to annotate an item by drawing features on a map.

  Scenario: CoverageHasAMap
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "CoverageHasAMap" for the "Tile"
    And I enter "Coverage has a map" for the "Subject"
    When I click on the "Features" tab
    Then the "Coverage" field changes to an OpenLayers map

  Scenario: DrawPointOnAMap
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "DrawPointOnAMap" for the "Tile"
    And I enter "DrawPointOnAMap" for the "Subject"
    And I click on the "Features" tab
    When I click on the "Coverages" map
    Then a point is added to the map

  Scenario: DrawLineOnAMap
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "DrawLineOnAMap" for the "Tile"
    And I enter "DrawLineOnAMap" for the "Subject"
    And I click on the "Features" tab
    When I click and drag on the "Coverages" map
    Then a line is added to the map

  Scenario: DrawOnCoverageMap
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "DrawOnCoverageMap" for the "Tile"
    And I enter "DrawOnCoverageMap" for the "Subject"
    And I click on the "Features" tab
    And I click on the "Coverages" map
    And I click and drag on the "Coverages" map
    And I click on "Add Item"
    When I click on "DrawOnCoverageMap"
    Then I should see an OpenLayers map in the "Coverages" field
    And the map should have a point feature
    And the map should have a line feature

