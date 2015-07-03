// SYNC
// An Alternative to the clean->copy operation to only delete removed files from the
// app/ folder that are present in the dist/
module.exports = function(grunt) {

    'use strict';

    grunt.config('sync', {
      main: {
        files: [{
            dot: true,
            cwd: '<%= goteo.app %>',
            src: [
                '**',
                '!view/**/*.{png,jpg,jpeg,gif}',
                // "!**/view/css/{goteo,config,node}.css",
                ],
            dest: '<%= goteo.dist %>'
        }, {
            dot: true,
            cwd: '<%= goteo.templates %>/default',
            src: [
                'partials/header/styles.php',
                'partials/footer/javascript.php',
                ],
            dest: '<%= goteo.dist %>/templates'
        }],
        verbose: true,
        pretend: false, // Don't do any disk operations - just write log
        ignoreInDest: [
                        "data",
                        '**/*.{png,jpg,jpeg,gif}',
                        // "**/view/css/{goteo,config,node}.css",
                    ], // Never remove js files from destination
        updateAndDelete: true // Remove all files from desc that are not found in src

      }
    });

    grunt.loadNpmTasks('grunt-sync');
};
