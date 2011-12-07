
# THIS IS A LOW-PRIORITY ITEM.
Feature: Raw Feature Data
  As an item editor
  I want to be able to edit the feature data directly
  So that I can use existing layer I have.

  Scenario: Enter Raw WKT
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "DrawOnCoverageMap" for the "Elements-50-0-text"      # Title
    And I enter "DrawOnCoverageMap" for the "Elements-49-0-text"      # Subject
    And I click on the "Raw" tab
    And I enter "POINT (38.03 -78.478889)" into the "Coverage" field
    When I click on the "Features" tab
    Then the map should have a point feature

  Scenario: Enter Raw KML
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "DrawOnCoverageMap" for the "Elements-50-0-text"      # Title
    And I enter "DrawOnCoverageMap" for the "Elements-49-0-text"      # Subject
    And I click on the "Raw" tab
    And I insert data from "features/data/charlottesville.kml" into the "Coverage" field
    When I click on the "Features" tab
    Then the map should have a point feature

