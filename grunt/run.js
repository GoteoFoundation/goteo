// RUN
// starts nginx server to allow multithread php in develpment
module.exports = function(grunt) {

    'use strict';

    grunt.file.mkdir('/tmp/nginx/client_temp');
    grunt.file.mkdir('/tmp/nginx/cache');

    var dir = process.cwd();
    console.log('CURRENT DIR',dir);

    grunt.config('run', {
        options: {
            },
        fpm: {
            cmd: 'php-fpm7.0',
            args: ['-p', dir, '-y', dir +'/var/php/php-fpm.conf']
        },
        nginx: {
            cmd: 'nginx',
            args: ['-p', dir, '-c', dir +'/var/php/nginx.conf']
        },
    });
    grunt.loadNpmTasks('grunt-run');
};
