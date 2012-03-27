
# This requires
# > npm install iniparser
#

fs        = require 'fs'
util      = require 'util'
iniparser = require 'iniparser'

files = [
  './views/shared/javascripts/nlfeatures.js'
  './views/admin/javascripts/editor/edit_features.js'
  './views/shared/javascripts/featureswidget.js'
]

pluginini = './plugin.ini'
builddir  = './views/shared/javascripts'

task 'build:browser', 'Compile and minify for use in browser', ->
  util.log "Reading plugin INI file #{pluginini}"
  config     = iniparser.parseSync pluginini
  version    = config.info.version.replace /['"]/g, ''
  targetfile = "neatline-features-#{version}"

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
    fs.mkdir builddir, 0755, ->
      fs.writeFile "#{builddir}/#{targetfile}.js", code, 'utf8', (err) ->
        console.log err if err
        try
          util.log "Creating #{builddir}/#{targetfile}-min.js"
          {parser, uglify} = require 'uglify-js'
          ast = parser.parse code
          code = uglify.gen_code uglify.ast_squeeze uglify.ast_mangle ast, extra: yes
          fs.writeFile "#{builddir}/#{targetfile}-min.js", code

task 'build', 'Compile', ->
  invoke 'build:browser'

