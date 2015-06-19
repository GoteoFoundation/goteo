// USEMIN
//  The usemin operation will update references to javascript and css files that
//  have beem concatinated and minified. See the USEMINPREPARE operation for instructions
//  on how to identify references in your html/php with comment blocks. This operation
//  should be run AFTER the concat, cssmin, and uglify operations. This is because this
//  operation will ensure that the final output file(s) have been created before updating
//  references to point to them.
module.exports = function(grunt) {

    'use strict';

    var _dist = GOTEO.dist + '/';
    var _templates = GOTEO.templates + '/';

    // Remplaza el archivo por la version con "revision"
    // ej: goteo.css por goteo.1f005531.css
    var replaceRevmap = function(file) {
        grunt.log.writeln("FILE: ",file);
        if(grunt.filerev && grunt.filerev.summary) {
            for(var i in grunt.filerev.summary) {
                if(i === _dist + file) {
                    // grunt.log.writeln(i,' === ',file,' | ',_dist,' ',grunt.filerev.summary[i]);
                    file = grunt.filerev.summary[i].substr(_dist.length); // 'dist'
                }
                // if(i === _templates + file) {
                //     // grunt.log.writeln(i,' === ',file,' | ',_dist,' ',grunt.filerev.summary[i]);
                //     file = grunt.filerev.summary[i].substr(_templates.length); // 'dist'
                // }
            }
        }
        return file;
    };

    grunt.config('usemin', {
        options: {
            // dirs: ['<%= goteo.dist %>'],

            blockReplacements: {
              css: function (block) {
                  // grunt.log.writeln('USEMIN CSS BLOCK');
                  // for(var i in block) grunt.log.writeln(i,': ',block[i]);

                  return '<link href="<?= SRC_URL ?>/' + replaceRevmap(block.dest) + '" type="text/css" rel="stylesheet" />';
              },
              js: function (block) {
                  var html = '<script type="text/javascript" src="<?= SRC_URL ?>' + replaceRevmap(block.dest) + '"></script>';
                  if(Config.analytics) {
                    html += '\n\n' + Config.analytics;
                  }
                  return html;
              }
            }
        },
        html: [
        // '<%= goteo.dist %>/view/prologue.html.php',
        '<%= goteo.dist %>/templates/partials/header/styles.php'

        ]
    });
    // grunt.loadNpmTasks('grunt-usemin');
    // grunt.loadNpmTasks('grunt-contrib-concat');
    // grunt.loadNpmTasks('grunt-contrib-cssmin');
    // grunt.loadNpmTasks('grunt-contrib-uglify');

};
