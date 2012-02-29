
Around('@file_fixture') do |scenario, block|
  NeatlineFeatures.file_fixtures = []
  begin
    block.call
  ensure
    NeatlineFeatures.file_fixtures.each do |filename|
      if File::exists?(filename)
        puts "rm #{filename}"
        File::delete(filename)
      end
    end
    NeatlineFeatures.file_fixtures = []
  end
end

Around('@selenium') do |scenario, block|
  NeatlineFeatures.driver = Capybara.default_driver
  Capybara.default_driver = :selenium
  begin
    block.call
  ensure
    Capybara.default_driver = NeatlineFeatures.driver
  end
end

