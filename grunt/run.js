// RUN
// starts nginx server to allow multithread php in develpment
module.exports = function(grunt) {

    'use strict';

    var commandExists = require('command-exists').sync;

    var phps = ['php-fpm7.1', 'php-fpm7.0', 'php-fpm7.2', 'php-fpm5.6', 'php-fpm'];
    var php = phps[0];

    phps.some(function(p){
        php = p;
        return commandExists(p);
    });
    grunt.log.ok('using PHP ' + php );

    grunt.config('run', {
        options: {
            wait: false
            },
        fpm: {
            cmd: php,
            args: ['-p', '<%= goteo.dir %>', '-y', '<%= goteo.dir %>/var/php/php-fpm.conf', '-d', 'upload_tmp_dir=<%= goteo.dir %>/var/php', '-d', 'sys_temp_dir=<%= goteo.dir %>/var/php', '-d', 'session.save_path=<%= goteo.dir %>/var/php/sessions']
        },
        nginx: {
            cmd: 'nginx',
            args: ['-p', '<%= goteo.dir %>', '-c', '<%= goteo.dir %>/var/php/nginx.conf']
        },
        fpmlog: {
            cmd: 'tail',
            args: ['-f', '<%= goteo.dir %>/php.log']
        }
    });
    grunt.loadNpmTasks('grunt-run');
};
