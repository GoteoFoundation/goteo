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
        banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
            '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
            '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
            '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
            ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',

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
    grunt.registerTask('server', function (target) {
        // grunt.log.writeln('server');
        if (target === 'dist') {
            // grunt.log.writeln('dist');

            return grunt.task.run(['build:dist', 'php:dist']);

        }
        if (target === 'stage') {
            // grunt.log.writeln('dist');

            // return grunt.task.run(['build:dev', 'php:dist:keepalive']);

        }

        //default, open development path
        grunt.task.run([
            'clean:server',
            'php:local',
            // 'watch'
        ]);
    });

    //build task, generates the distribution files
    //ready to deploy on a web server
    grunt.registerTask('build', function(target){
        if(target === 'stage') target = 'dev';
        if(target === 'dev') {
            //some changes
        }

        return grunt.task.run([
            'clean:dist',
            // 'newer:jshint',
            // 'newer:phplint',
            'newer:cssmin',

            'useminPrepare',
            'newer:imagemin',
            'concat:generated',
            // 'cssmin:generated', //no funciona con @imports se hace manualmente antes
            // 'uglify:generated',
            'sync',
            'filerev:dist',
            'usemin'
        ]);
    });
};
