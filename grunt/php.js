// PHP
//  The php operation will start up php's built-in server, configure it's filepaths,
//  and open a web browser to the provided hostname.
path = require('path');
absolute = path.resolve();
module.exports = function(grunt) {

    'use strict';

    grunt.config('php', {
        options: {
            // ini: '<%= goteo.phpINI %>',
            hostname: '<%= goteo.localURL %>',
            port: '<%= goteo.localPort %>',
            livereload: '<%= goteo.livePort %>',
            // bin: '/usr/bin/php56',
            directives: {
                memory_limit: '128M',
                short_open_tag: 'On',
                upload_tmp_dir: absolute + '/var/php',
                sys_temp_dir: absolute + '/var/php',
                display_errors: 'On',
                session: {save_path: absolute + '/var/php', cookie_domain: '<%= goteo.localURL %>'},
                allow_url_fopen: 'On'

            }
            // keepalive: true,
        },
        // Configuration options for the "server" task (i.e. during development).
        livereload: {
            options: {
                base: '.tmp', //Set the document root to the src folder.
                router: 'var/php/router_dev.php',
                open: true,
            },
        },
        dist: { // The "server" task can pass in a "dist" arguement. Configure the server accordingly.
            options: {
                base: '<%= goteo.dist %>', //Set the document root to the dist folder.
                router: 'var/php/router.php',
                open: false
            }
        }
    });

    grunt.loadNpmTasks('grunt-php');
};
