
group :frontend do
  guard 'livereload' do
    watch(%r{.+\.(css|js|html?|php|inc)$})
  end
end


guard 'compass' do
  watch('^views/(.*)\.s[ac]ss')
end

guard 'livereload' do
  watch(%r{public/.+\.(css|js|html|php|inc)})
end

# Add files and commands to this file, like the example:
#   watch(%r{file/path}) { `command(s)` }
#
guard 'shell' do
  watch(/views\/.*\.js/) do
    `cake build:browser`
  end
end
