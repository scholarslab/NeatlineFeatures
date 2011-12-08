
require 'cucumber/rake/task'

task :default => :cucumber

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

