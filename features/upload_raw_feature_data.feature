
# THIS IS A LOW-PRIORITY ITEM.
Feature: Upload Raw Feature Data
  As an item editor
  I want to be able to upload feature data from a file
  So that I can use existing layer data I have.

  Scenario: Upload WKT
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Upload WKT" for the "Elements-50-0-text"      # Title
    And I enter "Upload WKT" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    When I upload "features/data/charlottesville.wkt" into the "Coverage File" field
    Then the map should have a point feature

  Scenario: Upload KML
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Upload KML" for the "Elements-50-0-text"      # Title
    And I enter "Upload KML" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    When I upload "features/data/charlottesville.kml" into the "Coverage File" field
    Then the map should have a point feature


