require 'peach'
require 'fileutils'
require 'rake/packagetask'
require 'tempfile'

task :default => [
  # 'php:unit',
  # 'jasmine:ci',
]

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
    sh %{sass --watch _sass/shared/css/nlfeatures.scss:views/shared/css/nlfeatures.css}
  end

  desc 'This watches coffee script files.'
  task :coffee do
    sh %{coffee --watch --map --compile views/admin/javascripts/ views/shared/javascripts/}
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
  sh %{find . -name \\*.coffee | xargs coffee --map --compile}
end

desc "Updates the version in the 'plugin.ini' and 'package.json' files. If
given the version parameter, it also updates the version in the 'version' file.
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

class PackageTask < Rake::PackageTask
  def package_dir_path()
    "#{package_dir}/#{@name}"
  end
  def package_name
    @name
  end
  def basename
    @version ? "#{@name}-#{@version}" : @name
  end
  def tar_bz2_file
    "#{basename}.tar.bz2"
  end
  def tar_gz_file
    "#{basename}.tar.gz"
  end
  def tgz_file
    "#{basename}.tgz"
  end
  def zip_file
    "#{basename}.zip"
  end
end

PackageTask.new('NeatlineFeatures') do |p|
  p.version     = IO.readlines('version')[0].strip
  p.need_tar_gz = true
  p.need_zip    = true

  p.package_files.include('README.md')
  p.package_files.include('LICENSE')
  p.package_files.include('plugin.*')
  p.package_files.include('NeatlineFeaturesPlugin.php')
  p.package_files.include('lib/**/*.php')
  p.package_files.include('languages/*')
  p.package_files.include('models/**/*.php')
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

# TODO: Update POT, MO, and db dump.
desc 'This updates the code and creates the distribution.'
task 'dist' => [:compass, :coffee, :minify, :package]

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

  files.each do |filename|
    sh %{xgettext -L php --from-code=utf-8 -k__ --flag=__:1:pass-php-format --omit-header -F -o #{pot_temp.path} #{filename}}
  end

  sh %{msgcomm --omit-header -o #{pot_duplicates.path} #{pot_temp.path} #{core_pot}}

  sh %{msgcomm -u -o #{pot_temp.path} #{pot_temp.path} #{pot_duplicates.path}}

  sh %{msgcat -o #{pot_file} #{pot_base} #{pot_temp.path}}

  pot_temp.close(true)
  pot_duplicates.close(true)
  
end

desc 'Builds MO files from existing PO files.'
task :build_mo do
  files = Dir["languages/*.{po}"]

  files.pmap do |filename|
    targetfile = filename.sub(/\.po$/,'.mo')
    sh %{msgfmt -o #{targetfile} #{filename}}
  end
end

desc 'Dumps the database on the local system.'
task :dbdump, [:output] do |task, args|
  output = args[:output] || 'features/data/db-dump.sql.gz'
  sh %{mysqldump -uomeka -pomeka omeka | gzip > #{output}}
end

begin
  require 'cucumber/rake/task'

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

    Cucumber::Rake::Task.new(:tags, 'Run cucumber scenarios with a given tag.') do |t|
      tags = ENV['CUCUMBER_TAGS']
      t.cucumber_opts = ['--profile', 'default', '--tag', tags]
    end
  end

  desc 'Run tasks for Travis CI.'
  task :travis do
    system('export OMEKA_HOST=http://localhost OMEKA_USER=neatline OMEKA_PASSWD=neatline OMEKA_MYSQL=null && bundle exec cucumber')
  end

  desc 'Run tasks for Travis CI with MySQL cleaning.'
  task :travisclean do
    system('export OMEKA_HOST=http://localhost OMEKA_USER=neatline OMEKA_PASSWD=neatline OMEKA_MYSQL="mysql -uomeka -pomeka omeka" && bundle exec cucumber')
  end

  desc 'Run tagged tasks for Travis CI.'
  task :travistag, [:task] do |t, args|
    task = args[:task]
    system('export OMEKA_HOST=http://localhost OMEKA_USER=neatline OMEKA_PASSWD=neatline OMEKA_MYSQL="mysql -uomeka -pomeka omeka" && bundle exec cucumber --tags ' + task)
  end

  desc 'Run in neatline.dev environment. The task defaults to "cucumber:default".'
  task :neatlinecuke, [:task] do |t, args|
    task = args[:task] || 'cucumber:default'

    ENV['OMEKA_HOST']   = 'http://neatline.dev'
    ENV['OMEKA_USER']   = 'neatline'
    ENV['OMEKA_PASSWD'] = 'neatline'
    ENV['OMEKA_MYSQL']  = 'mysql -hneatline.dev -uomeka -pomeka omeka'

    Rake::Task[task].invoke()
  end
rescue LoadError
  desc 'Cucumber is not available. Run bundle with the "test" group.'
  task :cucumber do
    abort 'Cucumber is not available. Run bundle with the "test" group.'
  end

  desc 'Travis testing is unavailable because Cucumber is unavailable. So there.'
  task :travis do
    abort 'Travis testing is unavailable because Cucumber is unavailable. So there.'
  end
end

begin
  # require '/Users/err8n/p/zayin/lib/zayin/rake/vagrant/php'
  require 'vagrant'
  require 'zayin/rake/vagrant/php'
  Zayin::Rake::Vagrant::PhpTasks.new

  VM_BASEDIR = '/vagrant/omeka/plugins/NeatlineFeatures'

  namespace :php do
    desc 'This runs PHPUnit on NeatlineFeatures.'
    task :unit, [:target] do |task, args|
      target = args[:target] || '.'

      # Enabling the coverage report below causes memory issues, so I've
      # commented it out below.
      Rake::Task['vagrant:php:unit'].invoke(
        File.join(VM_BASEDIR, 'tests'),
        File.join(VM_BASEDIR, 'tests', 'phpunit.xml'),
        target
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
        File.join(VM_BASEDIR, 'php-testing-rules', 'phpcs.xml'),
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
        File.join(VM_BASEDIR, 'md'),
        File.join(VM_BASEDIR, 'php-testing-rules', 'phpmd.xml')
      )
    end

    desc 'This downloads the PHP style guides.'
    task :getstyle do
      sh %{git clone https://github.com/waynegraham/php-testing-rules}
    end
  end

  def vm_ssh(env, cmd, output_dir)
    puts ">>> #{cmd}"
    unless output_dir.nil?
      env.primary_vm.channel.execute(
        "if [ ! -d #{output_dir} ]; then mkdir -p #{output_dir}; fi"
      )
    end
    env.primary_vm.channel.execute cmd do |channel, data|
      out = $stdout
      if channel == :stderr
        out = $stderr
      end

      out.write(data)
      out.flush()
    end
  end

  namespace :vm do
    desc 'This removes all "Cucumber: ..." items from the DB in the VM.'
    task :clearitems do
      env = ::Vagrant::Environment.new
      vm_ssh(
        env,
        "mysql -uomeka -pomeka omeka < #{VM_BASEDIR}/features/support/clean_db.sql; echo 'DELETE FROM omeka_neatline_features;' | mysql -uomeka -pomeka omeka",
        nil
      )
    end

    desc 'This updates the database dump for Travis.'
    task :dbdump, [:output] do |task, args|
      output = args[:output] || 'features/data/db-dump.sql.gz'
      env = ::Vagrant::Environment.new
      vm_ssh(
        env,
        "cd #{VM_BASEDIR} && mysqldump -uomeka -pomeka omeka | gzip > #{output}",
        nil
      )
    end
  end
rescue LoadError
  desc 'The PHP unit tests are unavailable until you install the "vagrant" group.'
  task :vagrant do
    abort 'The PHP unit tests are unavailable until you install the "vagrant" group.'
  end
end

