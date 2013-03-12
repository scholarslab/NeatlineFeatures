
guard 'compass' do
  watch(/^_sass\/(.*)\.s[ac]ss/)
end

guard 'livereload' do
  watch(%r{views/.+\.(css|js|html|php|inc)})
  watch(%r{.*\.php$})
end

def cake_build
  `cake build`
end

guard :shell do
  watch(%r{ views/shared/javascripts/nlfeatures.js
          | views/shared/javascripts/featureswidget.js
          | views/admin/javascripts/editor/edit_features.js
        }x) do
    cake_build
  end
end

guard 'coffeescript', :input => 'views/shared/javascripts', :source_map => true do
  watch(/views\/shared\/javascripts\/(.*)\.coffee/)
end

