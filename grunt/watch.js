// WATCH
// Watches files for changes and runs tasks based on the changed files
module.exports = function(grunt) {

    'use strict';

    grunt.config('watch', {
        js: {
            files: ['<%= goteo.app %>/view/js/{,*/}*.js'],
            tasks: ['newer:jshint'],
            options: {
                livereload: true
            }
        },

        livereload: {
            options: {
                livereload: '<%= php.options.livereload %>'
            },
            files: [
                'templates/**/*.php',
                'extend/**/templates/**/*.php',
                '<%= goteo.app %>/**/view/**/*.php',
                '<%= goteo.app %>/**/view/css/**/*.css',
                '<%= goteo.app %>/**/view/js/**/*.js',
                '<%= goteo.app %>/**/view/**/*.{gif,jpeg,jpg,png,svg,webp}',
            ]
        }
    });
    grunt.loadNpmTasks('grunt-contrib-watch');
};
