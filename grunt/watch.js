// WATCH
// Watches files for changes and runs tasks based on the changed files
module.exports = function(grunt) {

    'use strict';

    grunt.config('watch', {
        js: {
            files: ['<%= goteo.app %>/view/js/{,*/}*.js',
                    '<%= goteo.app %>/assets/js/{,*/}*.js'],
            tasks: ['newer:jshint'],
            options: {
                livereload: {
                    host: '<%= goteo.localURL %>',
                    port: '<%= goteo.livePort %>'
                }
            }
        },

        livereload: {
            options: {
                livereload: {
                    host: '<%= goteo.localURL %>',
                    port: '<%= goteo.livePort %>'
                }
            },
            files: [
                'Resources/templates/**/*.php',
                'extend/**/templates/**/*.php',
                'extend/**/*.{js,css,gif,jpeg,jpg,png,svg,webp}',
                '<%= goteo.app %>/assets/**/*.{js,css,scss,gif,jpeg,jpg,png,svg,webp}',
                '<%= goteo.app %>/**/view/**/*.{js,css,scss,gif,jpeg,jpg,png,svg,webp}',
            ],
            'tasks': ['copy:devel', 'copy:plugins:devel', 'sass:devel']
        }
    });
    grunt.loadNpmTasks('grunt-contrib-watch');
};
