
require 'fileutils'
require 'capybara'
require 'capybara/cucumber'
require 'capybara/dsl'
require 'capybara/webkit'
require 'capybara-screenshot'
require 'capybara-screenshot/cucumber'
require 'rspec/expectations'
require 'geo_magic/remote'
require 'mechanize'

Capybara.app_host = ENV['OMEKA_HOST'] || 'http://features.dev'
Capybara.run_server = false
Capybara.default_wait_time = 60

Capybara.default_driver    = :webkit
# Capybara.default_driver    = :selenium
Capybara.javascript_driver = :selenium


# A bad, bad place to put this. But breaking it out into it's own file seems
# premature.
module NeatlineFeatures
  class << self
    attr_accessor :file_fixtures
    attr_accessor :omeka_dir
  end
end

at_exit do
  mysql = ENV['OMEKA_MYSQL'] || 'mysql -hfeatures.dev -uomeka -pomeka omeka'
  system %{#{mysql} < features/support/clean_db.sql} unless mysql == 'null'
end

