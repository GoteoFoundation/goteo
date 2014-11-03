// CSSMIN
//  The imagemin operation will minimify CSS files
module.exports = function(grunt) {

    'use strict';

    grunt.config('cssmin', {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= goteo.app %>/view/css/',
          src: ['goteo.min.css'],
          dest: '<%= goteo.dist %>/view/css'
        }]
      }

    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
};
