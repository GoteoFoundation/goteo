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
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
                }
            }
        },

        php: {
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
                }
            },
            files: [
                'Resources/templates/**/*.php',
                'extend/**/templates/**/*.php',
            ],
            // 'tasks': ['copy:devel', 'copy:plugins:devel']
        },

        assets: {
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
                }
            },
            files: [
                'extend/**/*.{js,css,gif,jpeg,jpg,png,svg,webp}',
                '<%= goteo.app %>/assets/**/*.{php,js,css,gif,jpeg,jpg,png,svg,webp}',
                '<%= goteo.app %>/**/view/**/*.{js,css,gif,jpeg,jpg,png,svg,webp}'
            ],
            'tasks': ['copy:devel', 'copy:plugins:devel']
        },

        css: {
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
                }
            },
            files: [
                '<%= goteo.app %>/assets/**/*.{scss}',
                '<%= goteo.app %>/**/view/**/*.{scss}',
            ],
            'tasks': ['sass:devel']
        }

    });
    grunt.loadNpmTasks('grunt-contrib-watch');
};
