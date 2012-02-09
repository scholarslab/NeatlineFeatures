
Given /^I draw a point on "([^"]*)"$/ do |map|
  find(map).find('div.olMapViewport')
  find(map).find('.olControlDrawFeaturePointItemInactive').click

  browser = page.driver.browser
  map_el = browser.find_element(:css, map).
                   find_element(:css, 'div.olMapViewport').
                   find_element(:tag_name, 'div')
  browser.action.move_to(map_el, 50, 50).
                 click.
                 perform
end

Given /^I draw a line on "([^"]*)"$/ do |map|
  find(map).find('div.olMapViewport')
  find(map).find('.olControlDrawFeaturePathItemInactive').click

  browser = page.driver.browser
  map_el = browser.find_element(:css, map).
                   find_element(:css, 'div.olMapViewport').
                   find_element(:tag_name, 'div')
  browser.action.move_to(map_el, 50, 50).
                 click.
                 move_to(map_el, 100, 150).
                 double_click.
                 perform
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

Then /^I should not see a map in "([^"]*)"$/ do |parent|
  within(parent) do
    find('.olMap').should_not be_visible
  end
end

Then /^I should see an OpenLayers map$/ do
  find('.olMap').should be_visible
end

Then /^I should see an OpenLayers map in the "([^"]*)" field$/ do |parent|
  within(parent) do
    find(".olMap").should be_visible
  end
end

Then /^I should not see an OpenLayers map in the "([^"]*)" field$/ do |parent|
  find(parent).should have_no_css(".olMap")
end

Then /^a point is defined in "([^"]*)"$/ do |textarea|
  find(textarea).value.should match(/POINT/)
end

Then /^a line is defined in "([^"]*)"$/ do |textarea|
  find(textarea).value.should match(/LINESTRING/)
end

Then /^the map in "([^"]*)" should have a point feature$/ do |parent|
  within(parent) do
    find('script').should have_content('POINT')
  end
end

Then /^the map in "([^"]*)" should have a line feature$/ do |parent|
  within(parent) do
    find('script').should have_content('LINESTRING')
  end
end

Then /^the map at "([^"]*)" should have a point feature$/ do |xpath|
  parent = find(:xpath, xpath)
  script = parent.find(:xpath, 'following-sibling::script')
  script.should have_content('POINT')
end

Then /^the map at "([^"]*)" should have a line feature$/ do |xpath|
  parent = find(:xpath, xpath)
  script = parent.find(:xpath, 'following-sibling::script')
  script.should have_content('LINESTRING')
end

Then /^the map at "([^"]*)" should display a point feature$/ do |map|
  result = evaluate_script("jQuery('#{map}').data('nlfeatures').hasPoint()")
  result.should be_true
end

Then /^the map at "([^"]*)" should display a line feature$/ do |xpath|
  result = (evaluate_script("jQuery('#{map}').data('nlfeatures').hasLine()") == 'true')
  result.should be_true
end

Then /^"([^"]*)" should center on my location$/ do |map|
  map_lon = evaluate_script("jQuery('#{map}').data('nlfeatures').getCenterLonLat().lon").to_f
  map_lat = evaluate_script("jQuery('#{map}').data('nlfeatures').getCenterLonLat().lat").to_f
  my_loc = GeoMagic::Remote.my_location
  (map_lon - my_loc.longitude.to_f).should be < 1.0
  (map_lat - my_loc.latitude.to_f ).should be < 1.0
end

