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
        // For the "server" task, we only need to clean the .tmp folder.
        server: {
            src : [
                 '.tmp',
                 'php/**/*',
                 '!php/php.ini'
            ]
        },
        // For the "dist" task, we need to clean out several folders.
        dist: {
            options : {
                // "no-write": true,
                // expand:true,
            },
            files: [{
                dot : true,
                src: [
                    '.tmp',
                    'php/**/*',
                    '!php/php.ini',
                    '<%= goteo.dist %>/**/view/css/{goteo,node,config}*.css',
                    '<%= goteo.dist %>/templates/**',
                    'var/cache/sql/**',
                    'var/cache/**/*',
                    '!var/cache/images/**',
                    '!var/cache/README',
                    '!var/cache/**/.*',
                ]
            }]
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
};
