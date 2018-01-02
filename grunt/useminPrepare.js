// USEMINPREPARE
//  This operation is part of the usemin operation and is responsible for setting everything
//  up. This operation will parse the files listed in the options defined here looking for
//  comment blocks of the form:
//          <!-- build:css({.tmp,app}) styles/main.css -->
//          ...
//          <!-- endbuild -->
//  It will then parse the html between these blocks and update the configuration of the
//  cssmin, concat, and uglify operations to make sure they will operate properly on the
//  files defined in the html comment block. The Usemin operation (below) will then be
//  responsible for updating these references to point to the newly created, combined
//  and minified files. This operation should be run BEFORE the concat, cssmin, and uglify
//  operations to ensure they are properly configured.
module.exports = function(grunt) {

    'use strict';

    // removes PHP code from html tags
    // ie:
    // src="<?php echo SRC_URL ?>" will become src=""
    var replaceAssetsPath = function (context) {
        var generated = context.options.generated;
        generated.options = {
            separator: "\n"
        };
        for(var i in generated.files) {
            // grunt.log.writeln(generated.files[i].src);
            for(var k in generated.files[i].src) {
                generated.files[i].src[k] = generated.files[i].src[k].replace(/<\?(.*)\?>/g, '').replace('//','/');
                // grunt.log.writeln(generated.files[i].src[k]);
            }
        }
    };

    grunt.config('useminPrepare', {
        options: {
            dest: '<%= goteo.dist %>',
            root: '.tmp',
            flow: {
              steps: {
                js: ['concat'],
                css: ['concat']
              },
              post: {
                css: [{
                  name: 'concat',
                  createConfig: replaceAssetsPath
                }],
                js: [{
                  name: 'concat',
                  createConfig: replaceAssetsPath
                }]
              }
            }
        },
        // Processing files under dist/templates directory.
        // Plugins can copy files by using manifest.yml
        html: [
            '<%= goteo.dist %>/templates/**/*.php'
        ]
    });

    grunt.loadNpmTasks('grunt-usemin');
    grunt.loadNpmTasks('grunt-contrib-concat');

};
