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
                flatten: true,
                src: [
                    '<%= goteo.app %>/assets/sass/*.scss',
                    'extend/**/<%= goteo.app %>/assets/sass/*.scss'
                ],
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
                flatten: true,
                src: [
                    '<%= goteo.app %>/assets/sass/*.scss',
                    'extend/**/<%= goteo.app %>/assets/sass/*.scss'
                ],
                dest: '<%= goteo.dist %>/assets/css',
                ext: '.css'
              }]
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
};
