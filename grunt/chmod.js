// CHMOD
// Ensures that some dirs are writeable
//
module.exports = function(grunt) {

    'use strict';

    grunt.config('chmod', {
        data : {
            options:{
                mode: '777'
            },
            src: [
                '<%= goteo.dist %>/data/images/',
                '<%= goteo.dist %>/data/',
                '<%= goteo.dist %>/logs/'
            ]
        },
        scripts : {
            options:{
                mode: '755'
            },
            src: [
                '*.sh'
            ]
        }
    });

    grunt.loadNpmTasks('grunt-chmod');
};
