// CSSMIN
//  The imagemin operation will minimify CSS files
module.exports = function(grunt) {

    'use strict';

    grunt.config('cssmin', {
      dist: {
        files: [{
          expand: true,
          // cwd: '.tmp',
          cwd: '<%= goteo.dist %>',
          src: ['assets/css/**/*.css'],
          dest: '<%= goteo.dist %>'
        }]
      }

    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
};
