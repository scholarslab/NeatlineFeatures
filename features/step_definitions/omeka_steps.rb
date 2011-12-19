
Given /^I replace "([^"]*)" with "([^"]*)"$/ do |dest, src|
  NeatlineFeatures.file_fixtures << dest
  dirname = File.dirname(dest)
  FileUtils.mkdir_p(dirname) if !Dir.exists?(dirname)
  FileUtils.cp(src, dest)
end

Then /^I should see the following output in unordered list "([^"]*)":$/ do |list_id, table|
  rows = find("ul#{list_id}").all('li')
  output = rows.map { |li| [li.text.strip] }

  table.diff!(output)
end

