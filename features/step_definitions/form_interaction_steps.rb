
Given /^I click(?: on)? "([^"]*)"$/ do |link_text|
  click_on link_text
end

Given /^I enter "([^"]*)" for the "([^"]*)"(?:\s+\#.*)?$/ do |value, label|
  fill_in(label, :with => value)
end

Given /^I enter "([^"]*)" into "([^"]*)"$/ do |arg1, arg2|
  pending # express the regexp above with the code you wish you had
end

When /^I press "([^"]*)"$/ do |button|
  click_on button
end

When /^I click "([^"]*)" on the "([^"]*)"$/ do |arg1, arg2|
  pending # express the regexp above with the code you wish you had
end

