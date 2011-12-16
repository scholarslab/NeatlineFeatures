
Feature: Bug Fix Tests
  As programmer
  I want bug-free code
  So that people will get off my ass.

  Scenario: New Items Coverage should not use TinyMCE
    Given I am logged into the admin console
    When I click "Add a new item to your archive"
    # This is to work around the setTimeout described here https://github.com/scholarslab/NeatlineFeatures/blob/ea055c7f4900310ed79c20ea7914f96bd795ac9a/views/admin/coverage.php#L56
    And I click the "Raw" tab in "#element-38"
    Then I should see "#Elements-38-0-text" in "#element-38"
    But I should not see ".mceEditor" in "#element-38"

  Scenario: "Use HTML" is Unchecked for New Items
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    When I click the "Raw" tab in "#element-38"
    Then "Element-38-0-html" should not be checked

  Scenario: "Use HTML" is Checked When Editing Items with It Previously Set
    Given I am logged into the admin console
    And I click "Add a new item to your archive"
    And I enter "Cucumber: 'Use HTML' is Checked When Editing Items" into "Elements-50-0-text"
    And I click the "Raw" tab in "#element-38"
    And I enter "hi" into "Elements-38-0-text"
    And I click "Use HTML" checkbox in "#element-38"
    And I click on "Add Item"
    And I click "'Use HTML' is Checked When Editing Items"
    And I click on "Edit this Item"
    When I click the "Raw" tab in "#element-38"
    Then "Elements-38-0-html" should be checked
    And I should see ".mceEditor" in "#element-38"

