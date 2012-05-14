
guard 'compass' do
  watch(/^_sass\/(.*)\.s[ac]ss/)
end

guard 'livereload' do
  watch(%r{views/.+\.(css|js|html|php|inc)})
end

# Add files and commands to this file, like the example:
#   watch(%r{file/path}) { `command(s)` }
#
guard 'shell' do
  watch(/views\/.*\.js/) do
    `cake build`
  end
end

guard 'coffeescript', :input => 'views/shared/javascripts' do
  watch(/views\/shared\/javascripts\/(.*)\.coffee/)
end

