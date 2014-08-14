
# Release Checklist

This is, um, the release checklist for [NeatlineFeatures][nlf]

## Requirements

This assumes that you have `NeatlineFeatures` located in an Omeka `plugins`
directory.

You'll need these tools installed:

* [git][git]
* [git flow][flow]
* [NodeJS][node]
* [NPM][npm]
* [Ruby][ruby]
* [Rake][rake]
* [Transifex client][tx]

## Set up

There are a few things that you'll need to do once to get started working on
NeatlineFeatures. If you've gotten this far, chances are that you've already
performed these steps. But for the sake of completeness, here they are:

1. Run `git flow init` in the `NeatlineFeatures` directory.
1. Run `npm install` in the `NeatlineFeatures` directory.
1. Log into `tx` by filling in `~/.transifexrc`. See the instructions on
   [Transifex authentication][txauth].
1. `PATH=$PATH:./node_modules/.bin` which will add the JavaScript utilities to
   your path.

## The List

1. `VERSION=42.0.13` — We'll use this value later.
1. `git flow release start $VERSION`
1. `rake version[$VERSION]`
1. `git commit -m "Updated versions."`
1. `rake compass coffee minify`
1. `git add --all views/shared/javascripts`
1. `git commit -m "Regenerated JS and CSS assets."`
1. Update i18n:
   * `tx pull --all`
   * `rake update_pot build_mo`
   * `git add --all languages`
   * `git commit` (if there are new translations)
1. `rake dbdump` which creates new db with admin, installation, and features
package installed. (You may want to delete the database and do a fresh
installation before running this.)
1. `git commit -m "Updated the DB dump for testing on TravisCI."`
1. `rake package`
1. `zip -l pkg/*.zip` to quick check the zip in `./pkg/`
1. test the zip more thoroughly.
1. `git flow release finish $VERSION`
1. `git push --all`
1. `git push --tags`
1. upload the zip to http://omeka.org/add-ons/plugins/.

[flow]: https://github.com/nvie/gitflow
[git]: http://git-scm.com/
[nlf]: https://github.com/scholarslab/NeatlineFeatures
[node]: http://nodejs.org/
[npm]: https://www.npmjs.org/
[rake]: https://github.com/jimweirich/rake
[ruby]: https://www.ruby-lang.org/en/
[tx]: http://docs.transifex.com/developer/client/
[txauth]: http://docs.transifex.com/developer/client/setup#configuration
