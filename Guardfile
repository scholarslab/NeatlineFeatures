
group :frontend do
  guard 'compass' do
    watch(%r{^/views\/(.*).s[ac]ss})
  end

  guard 'shell' do
    watch(%r{/views\/.*\.js/}) do
      `cake build:browser`
    end
  end

  guard 'livereload' do
    watch(%r{.+\.(css|js|html?|php|inc)$})
  end
end

