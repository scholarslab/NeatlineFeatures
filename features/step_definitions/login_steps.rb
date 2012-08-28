
Given /^I visit the admin page$/ do
  # browser.navigate.to 'http://features.dev/admin'
  visit('/admin')
end

Given /^I am logged into the admin console$/ do
  visit '/admin/users/login'
  fill_in "Username", :with => (ENV['OMEKA_USER']   || "features")
  fill_in "Password", :with => (ENV['OMEKA_PASSWD'] || "features")
  click_on "Log In"
end

Then /^I should see a page title of "([^"]*)"$/ do |page_title|
  find(:xpath, '//title').has_content?(page_title)
end

Then /^I should see a header of "([^"]*)"$/ do |header|
  find(:xpath, '//h1').has_content?(header)
end

