
Feature: Display Multimodal Coverages
  As an item editor
  I want to be able to have geospatial and textual data in the same coverage field
  So that visitors can see all coverage data.

  @kml
  @javascript
  Scenario: Multimodal Coverages
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Multimodal Coverage" for the "Elements-50-0-text"
    And I enter "Multimodal Coverage" for the "Elements-49-0-text"
    And I click "Use Map" checkbox in "#element-38"
    And I draw a point on "div#Elements-38-0-map.olMap"
    And I enter "A pointed question" into "Elements-38-0-free"
    And I click on "Add Item"
    And I click "Multimodal Coverage"
    When I click "View Public Page"
    Then the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[1]" should have a point feature
    And I should see text "A pointed question" in "#dublin-core-coverage"
    But I should not see text "kml" in "#dublin-core-coverage .nlfeatures"

