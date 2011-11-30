
require 'selenium-webdriver'
require 'capybara'
require 'capybara/cucumber'
require 'capybara/dsl'

Capybara.app_host = 'http://features.dev'
Capybara.run_server = false
Capybara.default_wait_time = 15

Capybara.default_driver = :selenium

# browser = Selenium::WebDriver.for :firefox

