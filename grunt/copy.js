// COPY
// Copies remaining files to places other tasks can use
module.exports = function(grunt) {

    'use strict';

    grunt.config('copy', {
        css: {
            expand: true,
            dot: true,
            cwd: '<%= goteo.app %>',
            dest: '.tmp',
            src: '**/view/css/**/*.css'
        }
    });
    grunt.loadNpmTasks('grunt-contrib-copy');
};
