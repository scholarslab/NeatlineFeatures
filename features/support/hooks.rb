
Around('@file_fixture') do |scenario, block|
  NeatlineFeatures.file_fixtures = []
  begin
    block.call
  ensure
    NeatlineFeatures.file_fixtures.each do |filename|
      if File::exists?(filename)
        # puts "rm #{filename}"
        File::delete(filename)
      end
    end
    NeatlineFeatures.file_fixtures = []
  end
end

Before do
  NeatlineFeatures.omeka_dir = ENV['OMEKA_DIR'] || '../..'
end

