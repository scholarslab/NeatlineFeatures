Feature: AdminLogin
  In order to make changes to the site
  As the site author
  I want to be able to log into the admin console

  Scenario: Login
    Given I visit the admin page
    And I enter "features" for the "Username"
    And I enter "features" for the "Password"
    When I press "Log In"
    Then I should see a page title of "Omeka Admin:"
    And I should see a header of "Dashboard"
