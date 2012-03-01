
Feature: Display Multiple Individual Coverages
  As a theme developer
  I want to be able to iterate over a mixed collection of coverage data and display each datum appropriately
  So that visitors can see all coverage data and I can customize their display.

  @file_fixture
  Scenario: All Non-Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-indiv.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Iterate All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I enter "Charlottesville, VA" into "Elements-38-0-free"
    And I click on "add_element_38"
    And I enter "UVa" into "Elements-38-1-free"
    And I click on "Add Item"
    And I click "Iterate All Non-Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | Charlottesville, VA |
      | UVa                 |
    But I should not see text "WKT" in "#dublin-core-coverage"

  @file_fixture @selenium
  Scenario: All Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-indiv.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Iterate All Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click "Use Map" checkbox in "#Elements-38-0-widget"
    And I draw a point on "div#Elements-38-0-map.olMap"
    And I click on "add_element_38"
    And I click "Use Map" checkbox in "#Elements-38-1-widget"
    And I draw a line on "div#Elements-38-1-map.olMap"
    And I click on "Add Item"
    And I click "Iterate All Feature Coverages"
    When I click "View Public Page"
    Then the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[1]" should have a point feature
    And the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[2]" should have a line feature
    But I should not see text "WKT" in "#dublin-core-coverage .nlfeatures"

  @file_fixture @selenium
  Scenario: Mixed Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-indiv.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Iterate Mixed Feature Coverages" for the "Elements-50-0-text"       # Title
    And I enter "Iterate Mixed Feature Coverages" for the "Elements-49-0-text"       # Subject
    And I click "Use Map" checkbox in "#Elements-38-0-widget"
    And I draw a point on "div#Elements-38-0-map.olMap"
    And I click on "add_element_38"
    And I wait 1 seconds
    And I enter "UVa" into "Elements-38-1-free"
    And I click on "Add Item"
    And I click "Iterate Mixed Feature Coverages"
    When I click "View Public Page"
    Then the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[1]" should have a point feature
    And I should see text "UVa" in "#dublin-core-coverage"
    But I should not see text "WKT" in "#dublin-core-coverage .nlfeatures"

