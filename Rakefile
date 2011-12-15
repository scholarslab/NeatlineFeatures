
require 'cucumber/rake/task'

task :default => [
  'php:unit',
  'jasmine:ci',
  :cucumber,
]

Cucumber::Rake::Task.new do |t|
  t.cucumber_opts = %w{--format pretty}
end

require 'zayin/rake/vagrant/php'
Zayin::Rake::Vagrant::PhpTasks.new

namespace :php do
  VM_BASEDIR = '/vagrant/omeka/plugins/NeatlineFeatures'

  desc 'This runs PHPUnit on NeatlineFeatures.'
  task :unit do
    # Enabling the coverage report below causes memory issues, so I've
    # commented it out below.
    Rake::Task['vagrant:php:unit'].invoke(
      File.join(VM_BASEDIR, 'tests'),
      File.join(VM_BASEDIR, 'tests', 'phpunit.xml'),
      'unit'
      # File.join(VM_BASEDIR, 'coverage')
    )
  end
end

desc 'This generates tags for Omeka and NeatlineFeatures.'
task :tags do
  sh %{ctags -R ../..}
end

namespace :watch do
  desc 'This watches the CSS files.'
  task :sass do
    sh %{sass --watch views/admin/css/nlfeatures.scss:views/admin/css/nlfeatures.css}
  end

  desc 'This watches coffee script files.'
  task :coffee do
    sh %{coffee --watch --compile views/admin/javascripts/}
  end

  desc 'This watches the Jasmine spec Coffee Script files.'
  task :jasmine do
    sh %{coffee --watch --compile spec/javascripts/}
  end
end


begin
  require 'jasmine'
  load 'jasmine/tasks/jasmine.rake'
rescue LoadError
  task :jasmine do
    abort "Jasmine is not available. In order to run jasmine, you must: (sudo) gem install jasmine"
  end
end
