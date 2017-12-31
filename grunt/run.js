// RUN
// starts nginx server to allow multithread php in develpment
module.exports = function(grunt) {

    'use strict';

    grunt.config('run', {
        options: {
            wait: false
            },
        fpm: {
            cmd: 'php-fpm7.0',
            args: ['-p', '<%= goteo.dir %>', '-y', '<%= goteo.dir %>/var/php/php-fpm.conf']
        },
        nginx: {
            cmd: 'nginx',
            args: ['-p', '<%= goteo.dir %>', '-c', '<%= goteo.dir %>/var/php/nginx.conf']
        },
    });
    grunt.loadNpmTasks('grunt-run');
};
