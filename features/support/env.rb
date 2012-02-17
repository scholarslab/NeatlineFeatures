
require 'fileutils'
require 'capybara'
require 'capybara/cucumber'
require 'capybara/dsl'
require 'capybara/webkit'
require 'rspec/expectations'
require 'geo_magic/remote'

Capybara.app_host = 'http://features.dev'
Capybara.run_server = false
Capybara.default_wait_time = 30

Capybara.default_driver = :webkit

# browser = Selenium::WebDriver.for :firefox


# A bad, bad place to put this. But breaking it out into it's own file seems
# premature.
module NeatlineFeatures
  class << self
    attr_accessor :file_fixtures
  end
end

at_exit do
  mysql = ENV['OMEKA_MYSQL'] || 'mysql -hfeatures.dev -uomeka -pomeka omeka'
  system %{#{mysql} < features/support/clean_db.sql}
end

