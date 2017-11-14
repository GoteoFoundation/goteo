// CSSMIN
//  The imagemin operation will minimify CSS files
module.exports = function(grunt) {

    'use strict';
    var licenseOk = false;

    grunt.config('uglify', {
      dist: {
        options: {
            output: {
                comments: function(node, comment) {
                    // console.log('comment', comment.value);
                    if(!licenseOk && comment.value.indexOf('@licstart') > -1 && comment.value.indexOf('@licend') > -1) {
                        licenseOk = true;
                        return true;
                    }
                    return false;
                }
            }
        },
        files: [{
          expand: true,
          // cwd: '.tmp',
          cwd: '<%= goteo.dist %>',
          src: ['assets/js/**/*.js'],
          dest: '<%= goteo.dist %>'
        }]
      }

    });
    grunt.loadNpmTasks('grunt-contrib-uglify');
};
