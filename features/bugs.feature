
Feature: Bug Fix Tests
  As programmer
  I want bug-free code
  So that people will get off my ass.

  Scenario: New Items Coverage should not use TinyMCE
    Given I am logged into the admin console
    When I click "Add a new item"
    # This is to work around the setTimeout described here https://github.com/scholarslab/NeatlineFeatures/blob/ea055c7f4900310ed79c20ea7914f96bd795ac9a/views/admin/coverage.php#L56
    And I wait 30 seconds
    Then I should see "#Elements-38-0-free" in "#element-38"
    But I should not see ".mceEditor" in "#element-38"

  Scenario: "Use HTML" is Unchecked for New Items
    Given I am logged into the admin console
    And I click "Add a new item"
    Then "Element-38-0-html" should not be checked

  ## This one fails intermittently, and I'm not sure why. I'm commenting this
  ## out until it starts failing either consistently or on someone else.
  # Scenario: "Use HTML" is Checked When Editing Items with it Previously Set
    # Given I am logged into the admin console
    # And I click "Add a new item"
    # And I enter "Cucumber: 'Use HTML' is Checked When Editing Items" into "Elements-50-0-text"
    # Then I should see "#Elements-38-0-free" in "#element-38"
    # And I enter "hi" into "Elements-38-0-free"
    # And I click "Use HTML" checkbox in "#element-38"
    # And I click on "Add Item"
    # And I click "'Use HTML' is Checked When Editing Items"
    # When I click on "Edit"
    # Then I should see ".mceEditor" in "#element-38"
    # And "Elements-38-0-html" should be checked

  @javascript
  Scenario: Editing buttons should have unique IDs.
    Given I am logged into the admin console
    When I click "Add a new item"
    And I click "Use Map" checkbox in the path ".//*[@id='Elements-38-0-widget']/../.."
    And I should see "#Elements-38-0-map"
    And I click on "add_element_38"
    And I click "Use Map" checkbox in the path ".//*[@id='Elements-38-1-widget']/../.."
    And I should see "#Elements-38-1-map"
    Then I should see "#Elements-38-0-drag-button"
    And I wait 5 seconds
    And I should see "#Elements-38-1-drag-button"

  @javascript
  Scenario: Viewports sufficiently west should not overflow
    Given I am logged into the admin console
    And I click "Add a new item"
    And I enter "Cucumber: Viewport overflow" for the "Elements-50-0-text"
    And I click "Use Map" checkbox in the path ".//*[@id='Elements-38-0-widget']/../.."
    And I move "#Elements-38-0-map" to "-111.883333, 40.75"
    And I zoom "#Elements-38-0-map" to "10"
    And I draw a point on "div.olMap"
    And I click "Save View" in "#element-38"
    And I click on "Add Item"
    When I click "Viewport overflow"
    Then "#dublin-core-coverage .map" should center on "-111.8833, 40.75"

  @javascript
  Scenario: TinyMCE should be hidden when editing an existing file
    Given I have existing feature data named "Cucumber: Tiny Zombie"
    And I am logged into the admin console
    And I click "Items" in "#content-nav"
    When I edit "Cucumber: Tiny Zombie"
    Then I should see "#Elements-38-0-free"

