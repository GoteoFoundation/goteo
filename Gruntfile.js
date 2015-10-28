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
    localURL: '0.0.0.0',
    localPort:8081,

    // to override this parameter, simply copy:
    // var/php/php.ini file to config/php.ini
    // and edit it with your own data
    phpINI: 'var/php/php.ini',

    configFile: 'config/settings.yml'
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
    var port = parseInt(config.url.main.split(':')[1], 10);
    if(port) {
        GOTEO.localPort = port;
        grunt.log.warn('Using local port from settings: ' + port);
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
                'clean:server',
                'build:devel',
                'php:dist:keepalive']);
        }

        grunt.task.run([
            'clean:server',
            'copy:devel',
            'copy:plugins:devel',
            'php:livereload',
            'watch'
        ]);

    });
    grunt.registerTask('server', function () {
        grunt.log.warn('The `server` task has been deprecated. Use `grunt serve` to start a server.');
        grunt.task.run(['serve']);
    });

    // Build the dist folder without compression (useful for apache/nginx servers pointing that dir)
    grunt.registerTask('build:devel', [
        'clean:dist',
        'copy:dist',
        'copy:plugins:dist',
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
        'copy:plugins:devel',

        'copy:dist', // copy from to dist as well
        'copy:plugins:dist',

        // TODO: make usemin work for headers
        'useminPrepare',

        // 'imagemin',
        'concat:generated',
        // 'cssmin:generated', //Doesn't work with @imports clauses
        // 'uglify:generated',

        'cssmin:dist', // manually minify css
        'filerev:dist',
        'usemin'
    ]);
};
