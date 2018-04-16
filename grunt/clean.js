// CLEAN
//  The clean operation is useful to clean out folders prior to copying
//  over new files. This operation will delete the contents of the folder.
//  This operation is usually one of the first called when running grunt tasks
//  to clean up our output directories before the remaining tasks copy new files
//  to them.
//
module.exports = function(grunt) {

    'use strict';

    grunt.config('clean', {
        options: {
            force: true
        },
        // For the "server" task, we only need to clean the .tmp folder.
        server: {
            src : [
                '.tmp',
                'var/php/**/*',
                '!var/php/php.ini',
                '!var/php/README',
                '!var/php/*.php',
                '!var/php/*.conf',
                'var/cache/sql/**',
                'var/cache/**/*',
                '!var/cache/images/**',
                '!var/cache/README',
                '!var/cache/**/.*'
            ]
        },
        // For the "dist" task, we need to clean out several folders.
        dist: {
            src: [
                '.tmp',
                '<%= goteo.dist %>',
            ]
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
};
