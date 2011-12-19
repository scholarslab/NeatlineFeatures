
Given /^I replace "([^"]*)" with "([^"]*)"$/ do |dest, src|
  NeatlineFeatures.file_fixtures << dest
  dirname = File.dirname(dest)
  FileUtils.mkdir_p(dirname) if !Dir.exists?(dirname)
  FileUtils.cp(src, dest)
end

