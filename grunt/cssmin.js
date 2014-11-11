// CSSMIN
//  The imagemin operation will minimify CSS files
module.exports = function(grunt) {

    'use strict';

    grunt.config('cssmin', {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= goteo.app %>',
          src: ['**/view/css/{goteo,config,node}.css'],
          dest: '<%= goteo.dist %>'
        }]
      }

    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
};
