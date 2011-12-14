
Feature: Bug Fix Tests
  As programmer
  I want bug-free code
  So that people will get off my ass.

  Scenario: New Items Coverage should not use TinyMCE
    Given I am logged into the admin console
    When I click "Add a new item to your archive"
    Then I should not see ".mceEditor" in "#element-38"
    But I should see "textarea#Elements-38-0-text" in "#element-38"

