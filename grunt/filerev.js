// REV
//  The rev operation will apply revision numbers to filenames (filename.ext will become filename.revision_no.ext)
//  This is usually applied only for production on files for which we want to force browser cache expiration.

module.exports = function(grunt) {

    'use strict';

    grunt.config('filerev', {
        dist: {
            src: [
                '<%= goteo.dist %>/view/css/allp.css',
                '<%= goteo.dist %>/assets/css/all.css'
            ]
        }

    });
    grunt.loadNpmTasks('grunt-filerev');
};
