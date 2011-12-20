
require 'cucumber/rake/task'

task :default => [
  'php:unit',
  'jasmine:ci',
  :cucumber,
]

Cucumber::Rake::Task.new do |t|
  t.cucumber_opts = %w{--format pretty}
end

# require 'zayin/rake/vagrant/php'
require '/Users/err8n/projects/zayin/lib/zayin/rake/vagrant/php'
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

  desc 'This runs PHP Copy/Paste Detection report on NeatlineFeatures.'
  task :cpd do
    Rake::Task['vagrant:php:cpd'].invoke(
      VM_BASEDIR,
      File.join(VM_BASEDIR, 'cpd')
    )
  end

  desc 'This runs PHP CodeSniffer on NeatlineFeatures.'
  task :cs do
    Rake::Task['vagrant:php:cs'].invoke(
      VM_BASEDIR,
      File.join(VM_BASEDIR, 'cs'),
      'Zend',
      %{--ignore=*/features/*,*/tests/*}
    )
  end

  desc 'This runs PHP Depend on NeatlineFeatures.'
  task :depend do
    Rake::Task['vagrant:php:depend'].invoke(
      VM_BASEDIR,
      File.join(VM_BASEDIR, 'depend')
    )
  end

  desc 'This runs PHP Documentor on NeatlineFeatures.'
  task :doc do
    Rake::Task['vagrant:php:doc'].invoke(
      VM_BASEDIR,
      File.join(VM_BASEDIR, 'doc')
    )
  end

  desc 'This run PHP Mess Detector on NeatlineFeatures.'
  task :md do
    Rake::Task['vagrant:php:md'].invoke(
      VM_BASEDIR,
      File.join(VM_BASEDIR, 'md')
    )
  end
end

desc 'This generates tags for Omeka and NeatlineFeatures.'
task :tags do
  sh %{ctags -R ../..}
end

namespace :watch do
  desc 'This runs watch:sass, watch:coffee, and watch:jasmine in parallel.'
  multitask :all => ['watch:sass', 'watch:coffee', 'watch:jasmine']

  desc 'This watches the CSS files.'
  task :sass do
    sh %{sass --watch views/shared/css/nlfeatures.scss:views/shared/css/nlfeatures.css}
  end

  desc 'This watches coffee script files.'
  task :coffee do
    sh %{coffee --watch --compile views/admin/javascripts/ views/shared/javascripts/}
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
