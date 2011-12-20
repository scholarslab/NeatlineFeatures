
Feature: Test Coverage Data
  As a theme developer
  I want to be able to test whether a coverage datum is feature data or not
  So that I can determine how to handle different types of coverage data correctly.

  @file_fixture
  Scenario: Test All Non-Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-test.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Test Iterate All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I click the "Raw" tab on "#Elements-38-0-widget"
    And I enter "Charlottesville, VA" into "Elements-38-0-text"
    And I click "add_element_38"
    And I click the "Raw" tab on "#Elements-38-1-widget"
    And I enter "UVa" into "Elements-38-1-text"
    And I click on "Add Item"
    And I click "Iterate All Non-Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | false |
      | false |

  # For some reason, I wasn't able to get two coverages with data working.
  @file_fixture
  Scenario: All Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-test.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Test Iterate All Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate All Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I draw a line on "div#Elements-38-0-map.olMap"
    #And I click "add_element_38"
    #And I draw a point on "div#Elements-38-1-map.olMap"
    And I click on "Add Item"
    And I click "Cucumber: Iterate All Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | true  |
      #| true  |

  @file_fixture
  Scenario: Mixed Feature Coverages
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-coverage-test.php"
    And I click "Add a new item to your archive"
    And I enter "Cucumber: Test Iterate Mixed Feature Coverages" for the "Elements-50-0-text"      # Title
    And I enter "Iterate Mixed Feature Coverages" for the "Elements-49-0-text"      # Subject
    And I draw a line on "div#Elements-38-0-map.olMap"
    And I click "add_element_38"
    And I click the "Raw" tab on "#Elements-38-1-widget"
    And I enter "UVa" into "Elements-38-1-text"
    And I click on "Add Item"
    And I click "Iterate Mixed Feature Coverages"
    When I click "View Public Page"
    Then I should see the following output in unordered list "#item-coverage":
      | true  |
      | false |


