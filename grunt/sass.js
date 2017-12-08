// SASS
// Convert SASS files to CSS
//
module.exports = function(grunt) {

    'use strict';

    grunt.config('sass', {
        devel: {
            options: {
            // style: 'compressed',
                compass: true
            },
            files: [{
                expand: true,
                cwd: '<%= goteo.app %>/assets/sass',
                src: ['*.scss'],
                dest: '.tmp/assets/css',
                ext: '.css'
              }]
        },
        dist: {
            options: {
            // style: 'compressed',
                compass: true
            },

            files: [{
                expand: true,
                cwd: '<%= goteo.app %>/assets/sass',
                src: ['*.scss'],
                dest: '<%= goteo.dist %>/assets/css',
                ext: '.css'
              }]
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
};
