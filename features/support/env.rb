
require 'selenium-webdriver'
require 'capybara'
require 'capybara/cucumber'
require 'capybara/dsl'
require 'rspec/expectations'
require 'geo_magic/remote'

Capybara.app_host = 'http://features.dev'
Capybara.run_server = false
Capybara.default_wait_time = 15

Capybara.default_driver = :selenium

# browser = Selenium::WebDriver.for :firefox

at_exit do
  mysql = ENV['OMEKA_MYSQL'] || 'mysql -hfeatures.dev -uomeka -pomeka omeka'
  system %{#{mysql} < features/support/clean_db.sql}
end

