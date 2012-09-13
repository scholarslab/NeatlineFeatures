
Feature: Test Coverage Data
  As a theme developer
  I want to be able to test whether a coverage datum is feature data or not
  So that I can determine how to handle different types of coverage data correctly.

  @kml
  @file_fixture
  Scenario: Test All Non-Feature Coverages
    Given I am logged into the admin console
    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-test.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Test Iterate All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I enter "Charlottesville, VA" into "Elements-38-0-free"
    And I click "add_element_38"
    And I enter "UVa" into "Elements-38-1-free"
    And I click on "Add Item"
    And I click "Test Iterate All Non-Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | false |
      | false |

  @kml
  @file_fixture @javascript
  Scenario: All Feature Coverages
    Given I am logged into the admin console
    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-test.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Test Iterate All Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click "Use Map" checkbox in "#Elements-38-0-widget"
    And I draw a line on "div#Elements-38-0-map.olMap"
    And I click "add_element_38"
    And I click "Use Map" checkbox in "#Elements-38-1-widget"
    And I draw a point on "div#Elements-38-1-map.olMap"
    And I click on "Add Item"
    And I click "Cucumber: Test Iterate All Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | true  |
      | true  |

  @current
  @kml
  @file_fixture @javascript
  Scenario: Mixed Feature Coverages
    Given I am logged into the admin console
    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-test.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Test Iterate Mixed Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate Mixed Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click "Use Map" checkbox in "#Elements-38-0-widget"
    And I draw a line on "div#Elements-38-0-map.olMap"
    And I click "add_element_38"
    And I wait 15 seconds
    And I enter "UVa" into "Elements-38-1-free"
    And I click on "Add Item"
    And I click "Test Iterate Mixed Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | true  |
      | false |


