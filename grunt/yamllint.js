// YAMLLINT
//  The yamllint operation is useful to validate yml files before publishing as
//  the may throw a http-500 error in the app
//
module.exports = function(grunt) {

    'use strict';

    grunt.config('yamllint', {
        // For the "server" task, we only need to clean the .tmp folder.
        config: [ 'config/*.yml' ],
        app: [ 'Resources/**/*.yml', 'extend/**/*.yml' ]
    });

    grunt.loadNpmTasks('grunt-yamllint');
};
