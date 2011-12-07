
Given /^I click(?: on)? "([^"]*)"$/ do |link_text|
  click_on link_text
end

Given /^I enter "([^"]*)" for the "([^"]*)"(?:\s+\#.*)?$/ do |value, label|
  fill_in(label, :with => value)
end

When /^I press "([^"]*)"$/ do |button|
  click_on button
end

