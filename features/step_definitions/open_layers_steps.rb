
Given /^I draw a point on "([^"]*)"$/ do |map|
  find('.olControlDrawFeaturePointItemInactive').click

  browser = page.driver.browser
  map_el = browser.find_element(:css, map).
                   find_element(:css, 'div.olMapViewport').
                   find_element(:tag_name, 'div')
  browser.action.move_to(map_el, 50, 50).
                 click.
                 perform
end

Given /^I click and drag on the "([^"]*)" map$/ do |arg1|
  pending # express the regexp above with the code you wish you had
end

Then /^I should see a map in "([^"]*)"$/ do |parent|
  within(parent) do
    find(".olMap").should be_visible
  end
end

Then /^I should see a map$/ do |arg1|
  find('.olMap').should be_visible
end

Then /^I should not see a map$/ do |arg1|
  find('.olMap').should_not be_visible
end

Then /^I should see an OpenLayers map$/ do
  find('.olMap').should be_visible
end

Then /^I should see an OpenLayers map in the "([^"]*)" field$/ do |parent|
  within(parent) do
    find(".olMap").should be_visible
  end
end

Then /^a point is defined in "([^"]*)"$/ do |textarea|
  find(textarea).value.should match(/POINT/)
end

Then /^a line is visible on a map$/ do
  pending # express the regexp above with the code you wish you had
end

Then /^a line is visible on the map$/ do
  pending # express the regexp above with the code you wish you had
end

Then /^the map should have a point feature$/ do
  pending # express the regexp above with the code you wish you had
end

Then /^the map should have a line feature$/ do
  pending # express the regexp above with the code you wish you had
end

