// WATCH
// Watches files for changes and runs tasks based on the changed files
module.exports = function(grunt) {

    'use strict';

    grunt.config('watch', {
        options: {
            livereload: {
                host: '<%= goteo.localHost %>',
                port: '<%= goteo.livePort %>'
            }
        },
        js: {
            files: ['<%= goteo.app %>/view/js/{,*/}*.js',
                    '<%= goteo.app %>/assets/js/{,*/}*.js'],
            tasks: ['newer:jshint'],
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
            //         files: ['.tmp/{assets,view}/js/**/*.js']
                }
            }
        },

        php: {
            files: [
                'Resources/templates/**/*.php',
                'extend/**/templates/**/*.php',
            ],
            // tasks: ['copy:devel', 'copy:plugins:devel']
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
                }
            }
        },

        assets: {
            files: [
                'extend/**/*.{js,css,gif,jpeg,jpg,png,svg,webp}',
                '<%= goteo.app %>/assets/**/*.{php,js,css,gif,jpeg,jpg,png,svg,webp}',
                '<%= goteo.app %>/**/view/**/*.{js,css,gif,jpeg,jpg,png,svg,webp}'
            ],
            tasks: ['copy:devel', 'copy:plugins:devel'],
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
                }
            }
        },

        sass: {
            files: [
                '<%= goteo.app %>/assets/sass/**/*.scss'
            ],
            tasks: ['sass:devel'],
            options: {
                livereload: {
                    host: '<%= goteo.localHost %>',
                    port: '<%= goteo.livePort %>'
            //         files: ['.tmp/assets/css/**/*.css']
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-watch');
};
