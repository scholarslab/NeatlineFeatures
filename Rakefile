require 'peach'
require 'cucumber/rake/task'
require 'fileutils'
require 'rake/packagetask'
require 'tempfile'

task :default => [
  'php:unit',
  # 'jasmine:ci',
  :cucumber,
]

namespace :cucumber do
  Cucumber::Rake::Task.new(:default, 'Run cucumber tests') do |t|
    t.profile = 'default'
  end

  Cucumber::Rake::Task.new(:rerun, 'Rerun failed cucumber tests.') do |t|
    File.delete('rerun-orig.txt') if File.exist?('rerun-orig.txt')
    if File.exist?('rerun.txt')
      File.rename('rerun.txt', 'rerun-orig.txt')
    else
      File.open('rerun-orig.txt', 'w+').close
    end
    t.profile = 'rerun'
  end

  Cucumber::Rake::Task.new(:current, 'Run cucumber scenarios tagged @current.') do |t|
    t.cucumber_opts = %w{--profile default --tag @current}
  end
end

namespace :js do
  desc 'This runs JSHint on the JavaScript files (CoffeeScript are assume to be OK).'
  task :hint do
    sh %{jshint views/admin/javascripts/editor/edit_geometry.js views/shared/javascripts/nlfeatures.js}
  end
end

desc 'This generates tags for Omeka and NeatlineFeatures.'
task :tags do
  sh %{ctags -R ../..}
end

namespace :watch do
  desc 'This runs watch:sass, watch:coffee, and watch:jasmine in parallel.'
  multitask :all => ['watch:sass',
                     'watch:coffee',
#                     'watch:jasmine'
  ]

  desc 'This watches the CSS files.'
  task :sass do
    sh %{sass --watch views/shared/css/nlfeatures.scss:views/shared/css/nlfeatures.css}
  end

  desc 'This watches coffee script files.'
  task :coffee do
    sh %{coffee --watch --compile views/admin/javascripts/ views/shared/javascripts/}
  end

#  desc 'This watches the Jasmine spec Coffee Script files.'
#  task :jasmine do
#    sh %{coffee --watch --compile spec/javascripts/}
#  end
end

desc 'This compiles the SCSS/Compass files.'
task :compass do
  sh %{compass compile}
end

desc 'This compiles the CoffeeScript files.'
task :coffee do
  sh %{find . -name \\*.coffee | xargs coffee --compile}
end

desc "Updates the version in the 'plugin.in' and 'package.json' files. If given
the version parameter, it also updates the version in the 'version' file.
Before updating the metadata files."

task :version, [:version] do |t, args|
  if args[:version].nil?
    version = IO.readlines('version')[0].strip
  else
    version = args[:version]
    IO.write('version', version, :mode => 'w')
  end

  puts "updating plugin.ini and package.json to #{version}"

  tmp = Tempfile.new 'features'
  tmp.close
  puts "TMP = <#{tmp.path.inspect}>"

  # plugin.ini
  FileUtils.mv 'plugin.ini', tmp.path, :verbose => true
  sh %{cat #{tmp.path} | sed -e 's/^version=".*"/version="#{version}"/' > plugin.ini}

  # project.json
  FileUtils.mv 'package.json', tmp.path, :verbose => true
  sh %{cat #{tmp.path} | sed -e 's/^\\( *"version" *: *"\\).*\\(",*\\)/\\1#{version}\\2/' > package.json}

  Rake::Task[:minify].invoke
end

Rake::PackageTask.new('dist') do |p|
  p.name = 'NeatlineFeatures'
  p.version = IO.readlines('version')[0].strip
  p.need_tar_gz = true
  p.need_zip = true

  p.package_files.include('plugin.*')
  p.package_files.include('lib/**/*.php')
  p.package_files.include('LICENSE')
  p.package_files.include('models/**.php')
  p.package_files.include('NeatlineFeaturesPlugin.php')
  p.package_files.include('README.md')
  p.package_files.include('views/**/*.css')
  p.package_files.include('views/**/*.gif')
  p.package_files.include('views/**/*.js')
  p.package_files.include('views/**/*.php')
  p.package_files.include('views/**/*.png')
end

desc 'This calls the Cakefile to minify the JS.'
task :minify do
  sh %{cake build}
end

desc 'This updates the code and creates the distribution.'
task 'dist' => [:compass, :coffee, :minify, :package]

# begin
#   require 'jasmine'
#   load 'jasmine/tasks/jasmine.rake'
# rescue LoadError
#   task :jasmine do
#     abort "Jasmine is not available. In order to run jasmine, you must: (sudo) gem install jasmine"
#   end
# end

desc 'Updates POT files.'
task :update_pot do
  files = (Dir["*.{php,phtml}"] + Dir["**/*.{php,phtml}"]).select { |p| ! p.start_with?("tests/") }
  lang_dir = "languages"
  core_pot = "../../application/languages/Omeka.pot"
  pot_file = "#{lang_dir}/template.pot"
  pot_base = "#{lang_dir}/template.base.pot"
  pot_temp = Tempfile.new(".pot")
  pot_temp.close
  pot_duplicates = Tempfile.new("-duplicates.pot")
  pot_duplicates.close

  files.pmap do |filename|
    sh %{xgettext -L php --from-code=utf-8 -k__ --flag=__:1:pass-php-format --omit-header -F -o #{pot_temp.path} #{filename}}
  end

  sh %{msgcomm --omit-header -o #{pot_duplicates.path} #{pot_temp.path} #{core_pot}}

  sh %{msgcomm -u -o #{pot_temp.path} #{pot_temp.path} #{pot_duplicates.path}}

  sh %{msgcat -o #{pot_file} #{pot_base} #{pot_temp.path}}

  pot_temp.close(true)
  pot_duplicates.close(true)
  
end

