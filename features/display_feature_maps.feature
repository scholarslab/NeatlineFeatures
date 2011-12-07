
Feature: Display Feature Maps on Item Pages
  As a theme developer
  I want to be able to include annotated maps on the item (and other) pages
  So that visitors to the site can view the feature data.

  Scenario: Display Feature Maps on Item Page
    Given I am logged into the admin console
    And I replace "../../themes/default/items/show.php" with "features/data/show-display-feature-maps.php"
    And I enter "Display Feature Maps" for the "Elements-50-0-text"      # Title
    And I enter "Display Feature Maps" for the "Elements-49-0-text"      # Subject
    And I click on the "Features" tab
    And I click on the "Coverages" map
    And I click and drag on the "Coverages" map
    And I click on "Add Item"
    And I click "Display Feature Maps"
    When I click "View Public Page"
    Then I should see an OpenLayers map
    And the map should have a line feature

