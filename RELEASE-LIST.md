
# Release Checklist

It's easiest to start with a fresh repository, so the instructions start there.

1. `VERSION=42.0.13` — We'll use this value later.
1. `git clone git://github.com/omeka/Omeka.git` — We need Omeka for generating
  translations.
1. `cd Omeka/plugins`
1. `git clone git@github.com:scholarslab/NeatlineFeatures.git`
1. `cd NeatlineFeatures`
1. `git checkout develop`
1. `git checkout master`
1. `git flow init`
1. `git flow release start $VERSION`
1. `npm install`
1. `rake version[$VERSION]`
1. `rake compass coffee minify`
1. `git add --all views/shared/javascripts`
1. `git commit`
1. Update i18n:
   * `tx pull`
   * `rake update_pot build_mo`
   * `git commit` (if there are new translations)
1. `git commit`
1. create new db with installation and features package installed and dump to
  `features/data/db-dump.sql.gz` (`rake vm:dbdump`).
1. `git commit`
1. `rake package`
1. quick check the zip
1. test the zip
1. `git flow release finish $VERSION`
1. `git push`
1. `git push --tags`
1. upload the zip to http://omeka.org/add-ons/plugins/.

