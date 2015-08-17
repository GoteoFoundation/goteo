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
        },
        headers: {
            expand: true,
            dot: true,
            cwd: '<%= goteo.templates %>/default/',
            dest: '<%= goteo.dist %>/templates/',
            src: 'partials/header/styles.php'
        }
    });
    grunt.loadNpmTasks('grunt-contrib-copy');
};
