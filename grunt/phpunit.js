// JSHINT
//  The jshint operation will lint our javascript files
//  making sure that there are no errors or bad formatting.
//  The .jshintrc file in the project folder sets the options
//  for linting. If the operations fails, the grunt script will abort.

module.exports = function(grunt) {

    'use strict';

    grunt.config('phpunit', {
        classes: {
            dir: ''
        },
        options: {
            bin: 'vendor/bin/phpunit',
            // bootstrap: 'tests/php/phpunit.php',
            colors: true
        }
    });
    grunt.loadNpmTasks('grunt-phpunit');
};
