//
// Goteo
// Gruntfile.js
//
// # Folder Paths
// to match only one level down:
// 'test/spec/{,*/}*.js'
// to recursively match all subfolders:
// 'test/spec/**/*.js'
//
GOTEO = {
    app: 'public',
    templates: 'Resources/templates',
    src: 'src',
    dist: 'dist',
    localHost: '0.0.0.0',
    localPort:8081,
    livePort:35729,

    // to override this parameter, simply copy:
    // var/php/php.ini file to config/php.ini
    // and edit it with your own data
    phpINI: 'var/php/php.ini',
    configFile: process.env.GOTEO_CONFIG_FILE || 'config/settings.yml'
};

module.exports = function(grunt) {
    if( ! grunt.file.exists(GOTEO.configFile)) {
        grunt.log.fail( '################################################\n' +
                        'Please configure a settings file with this name:\n' +
                        GOTEO.configFile + '\n\n' +
                        'You can use the config/demo-settings.yml as a sample file\n' +
                        '################################################\n'
                    );
    }
    if( grunt.file.exists('config/php.ini')) {
        GOTEO.phpINI = '../config/php.ini';
    }

    var config = grunt.file.readYAML(GOTEO.configFile);

    var localHost = config.url.main;
    var urlParts = localHost.split(':');
    var host = urlParts[urlParts.length - 2];
    if(host) {
        host = host.substring(host.indexOf('//') + 2);

        GOTEO.localHost = host;
        grunt.log.warn('Using Host from settings: ' + host);
    }

    var port = parseInt(urlParts[urlParts.length - 1], 10);
    if(port) {
        GOTEO.localPort = port;
        grunt.log.warn('Using local port from settings: ' + port);
    }

    var livePort = parseInt(config.plugins['goteo-dev'].liveport, 10);
    if(livePort) {
        GOTEO.livePort = livePort;
        grunt.log.warn('Using livePort from settings: ' + livePort);
    }

    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg: grunt.file.readJSON('package.json'),
        settings: config,
        //config values
        goteo: GOTEO
    });

    // show elapsed time at the end
    require('time-grunt')(grunt);

    // Load per-task config from separate files.
    grunt.loadTasks('grunt');

    // some non-configured tasks
    grunt.loadNpmTasks('grunt-newer');
    grunt.loadNpmTasks('grunt-notify');

    // Default task. Just linter
    grunt.registerTask('default', ['lint']);
    grunt.registerTask('lint', ['jshint', 'phplint']);

    // PRE-COMMIT ready hook
    // $ cd {repo}
    // $ nano .git/hooks/pre-commit
    //
    // #!/bin/sh
    // grunt precommit
    //
    // $ chmod +x .git/hooks/pre-commit
    grunt.registerTask('precommit', ['newer:jshint', 'newer:phplint']);



    // SERVER
    //  The server task is used to "start a server". If you are using php's built-in
    //  web server for development testing, it will be started up. We'll start watching
    //  any files that need to be watched for changes, and open a browser to our dev URL

    grunt.registerTask('serve', function (target) {
        if (target === 'dist') {
            return grunt.task.run([
                'build:dist',
                'php:dist:keepalive']);
        }

        if (target === 'devel') {
            return grunt.task.run([
                'clean:server',
                'build:devel',
                'php:dist:keepalive']);
        }
        if (target === 'nginx') {
            return grunt.task.run([
                'clean:server',
                'copy:devel',
                'copy:plugins:devel',
                'sass:devel',
                'run:fpm',
                'run:nginx',
                'watch'
                ]);
        }

        grunt.task.run([
            'clean:server',
            'copy:devel',
            'copy:plugins:devel',
            'sass:devel',
            'php:livereload',
            'watch'
        ]);

    });
    grunt.registerTask('server', function () {
        grunt.log.warn('The `server` task has been deprecated. Use `grunt serve` to start a server.');
        grunt.task.run(['serve']);
    });

    grunt.registerTask('build:dist', [
        'build'
    ]);
    // Build the dist folder without compression (useful for apache/nginx servers pointing that dir)
    grunt.registerTask('build:devel', [
        'clean:dist',
        'copy:dist',
        'copy:plugins:dist',
        'copy:fonts',
        'copy:fonts2',
        'sass:dist'
    ]);

    //build and uploads
    grunt.registerTask('deploy', function(){

        grunt.task.run(['build']);
        if(grunt.config.get('settings').filesystem.handler === 's3') {
            grunt.task.run(['aws_s3']);
        }
    });

    grunt.registerTask('build', [
        'clean:dist',
        // 'newer:jshint',
        // 'newer:phplint',

        'copy:devel', // copy assets to .tmp for postprocessing
        'sass:devel',
        'copy:plugins:devel',

        'copy:dist', // copy from to dist as well
        'copy:plugins:dist',
        'copy:fonts',
        'copy:fonts2',
        'sass:dist',

        'useminPrepare',
        // 'imagemin',
        'concat:generated',

        'cssmin:dist', // manually minify css
        'uglify:dist', // manually minify js
        'filerev:dist',
        'usemin'
    ]);
};
