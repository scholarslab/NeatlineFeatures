
# This requires
# > npm install iniparser
# > npm install glob
#
# Or just do this:
# > npm link
#

fs        = require 'fs'
glob      = require 'glob'
util      = require 'util'
iniparser = require 'iniparser'

files = [
  './views/shared/javascripts/nlfeatures.js'
  './views/admin/javascripts/editor/edit_features.js'
  './views/shared/javascripts/featureswidget.js'
]

pluginini    = './plugin.ini'
builddir     = './views/shared/javascripts'
targetprefix = 'neatline-features'

task 'build:browser', 'Compile and minify for use in browser', ->
  util.log "Reading plugin INI file #{pluginini}"
  config     = iniparser.parseSync pluginini
  version    = config.info.version.replace /['"]/g, ''
  targetfile = "#{targetprefix}-#{version}"

  util.log "Creating browser file for Neatline Features version #{version}."
  remaining = files.length
  contents = new Array remaining
  for file, index in files
    do (file, index) ->
      fs.readFile file, 'utf8', (err, cnt) ->
        util.log err if err
        contents[index] = cnt

        util.log "[#{index + 1}/#{files.length}] #{file}"

        process() if --remaining is 0
  process = ->
    util.log "Creating #{builddir}/#{targetfile}.js"

    code = contents.join "\n\n"
    fs.mkdir builddir, 0o0755, ->
      fs.writeFile "#{builddir}/#{targetfile}.js", code, 'utf8', (err) ->
        console.log err if err
        try
          util.log "Creating #{builddir}/#{targetfile}-min.js"
          {code} = require('uglify-js').minify code, fromString: true
          fs.writeFile "#{builddir}/#{targetfile}-min.js", code
        catch e
          util.log "ERROR: #{e}"

task 'clean', 'Clean up all minified JS files.', ->
  pattern = "#{builddir}/#{targetprefix}-*.js"
  util.log "Cleaning up #{pattern}"
  glob pattern, (err, files) ->
    if err != null
      throw err
    for fn in files
      util.log "rm #{fn}"
      fs.unlink fn

task 'build', 'Compile', ->
  invoke 'clean'
  invoke 'build:browser'

