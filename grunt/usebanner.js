// BANNER
// Inserts the license message on the top of php and js files
//
module.exports = function(grunt) {

    'use strict';

    grunt.config('banner', {
        options: {
            linebreak:false,
            position: 'top',
            banner: '<?php\n<%= banner %>\n?>'
          },
          files: {
            src: [
              '<%= goteo.dist %>/**/*.php'
            ]
        }

    });

    grunt.loadNpmTasks('grunt-banner');
};
