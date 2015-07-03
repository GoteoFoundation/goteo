// PHP
//  The php operation will start up php's built-in server, configure it's filepaths,
//  and open a web browser to the provided hostname.
module.exports = function(grunt) {

    'use strict';

    grunt.config('php', {
        options: {
            ini: '<%= goteo.phpINI %>',
            hostname: '<%= goteo.localURL %>',
            port: '<%= goteo.localPort %>',
            livereload: 35729,
            // keepalive: true,
        },
        // Configuration options for the "server" task (i.e. during development).
        livereload: {
            options: {
                base: '<%= goteo.app %>', //Set the document root to the src folder.
                router: '../var/php/router_dev.php',
                open: true,
            },
        },
        dist: { // The "server" task can pass in a "dist" arguement. Configure the server accordingly.
            options: {
                base: '<%= goteo.dist %>', //Set the document root to the dist folder.
                router: '../var/php/router.php',
                open: false
            }
        }
    });

    grunt.loadNpmTasks('grunt-php');
};
