
Given /^I replace "([^"]*)" with "([^"]*)"$/ do |dest, src|
  dest    = File.join(NeatlineFeatures.omeka_dir, dest)
  src     = File.join(NeatlineFeatures.omeka_dir, src)
  dirname = File.dirname(dest)

  FileUtils.mkdir_p(dirname) if !Dir.exists?(dirname)
  FileUtils.cp(src, dest)
  NeatlineFeatures.file_fixtures << dest
end

Given /^I edit "([^"]*)"$/ do |title|
  steps %Q{
    Given I click on "#{title}"
    Given I click "Edit"
  }
end

When /^I view the public page$/ do
  step 'I click "View Public Page"'
end

Then /^I should see the following output in unordered list "([^"]*)":$/ do |list_id, table|
  rows = find("ul#{list_id}").all('li')
  output = rows.map { |li| [li.text.strip] }

  # puts output
  table.diff!(output)
end

