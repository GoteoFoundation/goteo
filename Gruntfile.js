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
    app: 'app',
    dist: 'dist',
    localURL: 'localhost',
    localPort:8081
};

module.exports = function(grunt) {
    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg: grunt.file.readJSON('package.json'),
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
            return grunt.task.run(['build', 'php:dist:keepalive']);
        }

        grunt.task.run([
            'clean:server',
            'php:livereload',
            'watch'
        ]);

    });
    grunt.registerTask('server', function () {
        grunt.log.warn('The `server` task has been deprecated. Use `grunt serve` to start a server.');
        grunt.task.run(['serve']);
    });


    grunt.registerTask('build', [
        'clean:dist',
        // 'newer:jshint',
        // 'newer:phplint',

        'useminPrepare',
        'sync',
        'newer:imagemin',
        'concat:generated',
        // 'cssmin:generated', //no funciona con @imports se hace manualmente despues
        // 'uglify:generated',
        'cssmin:dist',
        'filerev:dist',
        'usemin',
        'processhtml'
    ]);
};
