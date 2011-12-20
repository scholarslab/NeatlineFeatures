
# THIS IS A LOW-PRIORITY ITEM.
Feature: Raw Feature Data
  As an item editor
  I want to be able to edit the feature data directly
  So that I can use existing layer data I have.

  Scenario: Enter Raw WKT
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Enter Raw WKT" for the "Elements-50-0-text"      # Title
    And I enter "Enter Raw WKT" for the "Elements-49-0-text"      # Subject
    And I click the "Raw" tab on "#Elements-38-0-widget"
    And I enter "POINT(888546.3715643873 8501594.843567504)" into "Elements-38-0-text"
    When I click the "NL Features" tab on "#Elements-38-0-widget"
    Then the map at "#Elements-38-0-map" should display a point feature

  # This will be uncommented when/if we decide to implement it.
  #Scenario: Enter Raw KML
    #Given I am logged into the admin console
    #And I click "Add a new item to your archive"
    #And I enter "Cucumber: Enter Raw KML" for the "Elements-50-0-text"      # Title
    #And I enter "Enter Raw KML" for the "Elements-49-0-text"      # Subject
    #And I click on the "Raw" tab
    #And I insert data from "features/data/charlottesville.kml" into the "Coverage" field
    #When I click on the "Features" tab
    #Then the map should have a point feature

