// IMAGEMIN
//  The imagemin operation will minify jpeg and png files
//  using several methods to attempt to compress the size
//  of each file.
module.exports = function(grunt) {

    'use strict';

    grunt.config('imagemin', {
        dist: {
            files: [{
                expand: true,
                cwd: '<%= goteo.app %>/view',
                src: '**/*.{png,jpg,jpeg,gif}',
                dest: '<%= goteo.dist %>/view'
            }]
        }

    });
    grunt.loadNpmTasks('grunt-contrib-imagemin');
};
