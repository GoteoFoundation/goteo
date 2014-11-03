// WATCH
// Automatic operations on file changes
module.exports = function(grunt) {

    'use strict';

    grunt.config('watch', {
        grunt: {
            files: '<%= jshint.gruntfiles.src %>',
            tasks: ['jshint:grunt']
        }
    });
    grunt.loadNpmTasks('grunt-contrib-watch');
};
