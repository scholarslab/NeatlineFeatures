
Given /^I click(?: on)? "([^"]*)"$/ do |link_text|
  click_on link_text
end

When /^I press "([^"]*)"$/ do |button|
  click_on button
end

