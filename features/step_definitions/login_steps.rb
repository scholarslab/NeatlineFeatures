
Given /^I visit the admin page$/ do
  # browser.navigate.to 'http://features.dev/admin'
  visit('/admin')
end

Given /^I enter "([^"]*)" for the "([^"]*)"$/ do |value, label|
  fill_in(label, :with => value)
end

Given /^I am logged into the admin console$/ do
  pending # express the regexp above with the code you wish you had
end

Then /^I should see a page title of "([^"]*)"$/ do |page_title|
  find(:xpath, '//title').has_content?(page_title)
end

Then /^I should see a header of "([^"]*)"$/ do |header|
  find(:xpath, '//h1').has_content?(header)
end

