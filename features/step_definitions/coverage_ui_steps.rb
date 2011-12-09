
Given /^I click(?: on)? the "([^"]*)" tab$/ do |tab_name|
  pending # These should really be changed to use the next rule.
end

Given /^I click(?: on)? the "([^"]*)" field "([^"]*)" tab$/ do |field_name, tab_name|
  within(field_name) do
    click_on tab_name
  end
end

Given /^I click(?: on)? the "([^"]*)" tab on "([^"]*)"$/ do |arg1, arg2|
  pending # express the regexp above with the code you wish you had
end

Then /^I see (\d+) "([^"]*)" fields?$/ do |arg1, arg2|
  pending # express the regexp above with the code you wish you had
end

Then /^the delete button is enabled on "([^"]*)"$/ do |arg1|
  pending # express the regexp above with the code you wish you had
end

Then /^the delete button is disabled$/ do
  pending # express the regexp above with the code you wish you had
end

Then /^the delete button is disabled on "([^"]*)"$/ do |arg1|
  pending # express the regexp above with the code you wish you had
end

Then /^I(?: should)? see "([^"]*)" in "([^"]*)"$/ do |arg1, arg2|
  pending # express the regexp above with the code you wish you had
end

Then /^I should see "([^"]*)"$/ do |arg1|
  pending
end

Then /^I should not see "([^"]*)"$/ do |arg1|
  pending
end

