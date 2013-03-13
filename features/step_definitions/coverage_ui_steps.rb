
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
  find(parent).all(thing).length.should == n.to_i
end

Then /^I(?: should)? see "([^"]*)" in "([^"]*)"$/ do |target, context|
  using_wait_time(30) do
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

Then /^I should not see text "([^"]*)" in "([^"]*)"$/ do |text, context|
  find(context).should_not have_content(text)
end

Then /^I should see "([^"]*)"$/ do |target|
  find(target).should be_visible
end

Then /^I should see XPath "([^"]*)"$/ do |target|
  find(:xpath, target).should be_visible
end

Then /^I should not see "([^"]*)"$/ do |target|
  find(target).should_not be_visible
end

Then /^element "([^"]*)" should not be on the page$/ do |target|
  page.should_not have_css(target)
end

