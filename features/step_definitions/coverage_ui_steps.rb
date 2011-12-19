
Given /^I click(?: on)? the "([^"]*)" tab$/ do |tab_name|
  pending # These should really be changed to use the next rule.
end

Given /^I click(?: on)? the "([^"]*)" field "([^"]*)" tab$/ do |field_name, tab_name|
  within(field_name) do
    click_on tab_name
  end
end

Given /^I click(?: on)? the "([^"]*)" tab (?:in|on) "([^"]*)"$/ do |tab_name, field_name|
  within(field_name) do
    click_on tab_name
  end
end

Given /^I see text "([^"]*)" in input "([^"]*)"$/ do |text, el|
  find(el).value.should match("/#{text}/")
end

Then /^I see (\d+) "([^"]*)" in "([^"]*)"?$/ do |n, thing, parent|
  wait_until do
    find(parent).all(thing).length.should == n.to_i
  end
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

Then /^I(?: should)? see "([^"]*)" in "([^"]*)"$/ do |target, context|
  wait_until(30) do
    within(context) do
      find(target).should be_visible
    end
  end
end

Then /^I should not see "([^"]*)" in "([^"]*)"$/ do |target, context|
  within(context) do
    (page.has_no_selector?(target)) || (find(target).should_not be_visible)
  end
end

Then /^I(?: should)? see text "([^"]*)" in "([^"]*)"$/ do |text, context|
  find(context).should have_content(text)
end

Then /^I should see "([^"]*)"$/ do |target|
  find(target).should be_visible
end

Then /^I should not see "([^"]*)"$/ do |arg1|
  pending
end

