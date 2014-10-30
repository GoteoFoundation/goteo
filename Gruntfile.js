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
module.exports = function(grunt) {
    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg: grunt.file.readJSON('package.json'),
        banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
            '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
            '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
            '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
            ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',
        // Task configuration.
        //
    //         concat: {
    //   options: {
    //     banner: '<%= banner %>',
    //     stripBanners: true
    //   },
    //   dist: {
    //     src: ['lib/<%= pkg.name %>.js'],
    //     dest: 'dist/<%= pkg.name %>.js'
    //   }
    // },
    // uglify: {
    //   options: {
    //     banner: '<%= banner %>'
    //   },
    //   dist: {
    //     src: '<%= concat.dist.dest %>',
    //     dest: 'dist/<%= pkg.name %>.min.js'
    //   }
    // },


        // JSHINT
        //  The jshint operation will lint our javascript files
        //  making sure that there are no errors or bad formatting.
        //  The .jshintrc file in the project folder sets the options
        //  for linting. If the operations fails, the grunt script will abort.
        jshint: {
            options: {
                reporter: require('jshint-stylish'),
                jshintrc: '.jshintrc'
            },
            all: [
                'Gruntfile.js',
                'app/view/js/*.js',
                '!app/view/js/jquery.*.js',
                '!app/view/js/slides.jquery.js',
                '!app/view/js/sha1*.js',
                '!app/view/js/datepicker*.js',
                '!app/view/js/jquery-*.js'
            ],
            gruntfile: {
                src: 'Gruntfile.js'
            }
        },

        // PHPLINT
        //  The phplint operation will lint your php files to make sure there
        //  are no syntax errors. Note that the linter does not execute your scripts,
        //  it only does a syntax check.
        phplint: {
            all: [
                'app/**/*.php',
                '!app/library/pear/**'
            ]
        },

        // CLEAN
        //  The clean operation is useful to clean out folders prior to copying
        //  over new files. This operation will delete the contents of the folder.
        //  This operation is usually one of the first called when running grunt tasks
        //  to clean up our output directories before the remaining tasks copy new files
        //  to them.
        clean: {
            // For the "server" task, we only need to clean the .tmp folder.
            // server: {
            //     src : [
            //          '.tmp',
            //          'php/**/*',
            //          '!php/php.ini'
            //     ]
            // },
            // For the "dist" task, we need to clean out several folders.
            dist: {
                options : {
                    "no-write": true,
                    // expand:true,
                },
                files: [{
                    dot : true,
                    src: [
                        // '.tmp',
                        // 'php/**/*',
                        // '!php/php.ini',
                        'dist/*',
                        'dist/**/*',
                        '!dist/**/',
                        // '!dist/**',
                        // '!dist/view',
                        // 'dist/view/**/*',
                        '!dist/**/*.{png,jpg,jpeg,gif}',
                        '!dist/data*',
                        '!dist/logs*',
                        '!dist/config*',
                        '!dist/.git*'
                    ]
                }]
            }
        },
        // SYNC
        // Alternative clean operation to only delete removed files from the
        // app/ folder that are present in the dist/
        sync: {
          main: {
            files: [{
                dot: true,
                cwd: 'app',
                src: [
                    '**',
                    '!**/*.{png,jpg,jpeg,gif}',
                    "!config/**",
                    "!data/**",
                    "data/.htaccess",
                    "!logs/**",
                    "logs/.htaccess",
                    "logs/README",
                    "logs/cron/README",
                    "!.git*"
                    ],
                dest: 'dist/'
            }],
            verbose: true,
            pretend: false, // Don't do any disk operations - just write log
            ignoreInDest: [
                            "data",
                            '**/*.{png,jpg,jpeg,gif}',
                            "config/**",
                            "data/**",
                            "logs/**",
                            ".git*"
                        ], // Never remove js files from destination
            updateAndDelete: true // Remove all files from desc that are not found in src

          }
        },
        // IMAGEMIN
        //  The imagemin operation will minify jpeg and png files
        //  using several methods to attempt to compress the size
        //  of each file.
        imagemin: {
            dist: {
                files: [{
                    expand: true,
                    cwd: 'app/view',
                    src: '**/*.{png,jpg,jpeg,gif}',
                    dest: 'dist/view'
                }]
            }
        },
        // COPY
        //  The copy task does simply copying of files from one location to another.
        //  Most of the otheroperations allow for putting their output files in a
        //  particular location. However, some files are "static" and not used in
        //  any operations. The copy operation can be used to copy those files as needed,
        //  for example, moving files from the app folder to the dist folder for a push
        //  to production.
        copy: {
            dist: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'app',
                    dest: 'dist',
                    src: [
                        '**',
                        '!config/demo-settings.php',
                        '!data/**',
                        '!logs/**',
                        // '!view/css/**/*.css',
                        '.htaccess',
                        'data/.htaccess',
                        'logs/.htaccess',
                        'logs/**/README',
                        '!**/*.{png,jpg,jpeg,gif}',
                    ]
                }
                //, {
                //     expand: false,
                //     src: 'app/config/demo-settings.php',
                //     dest: 'dist/config/settings.php'

                // }
                ]
            }
        },

        cssmin: {
          dist: {
            files: [{
              expand: true,
              cwd: 'app/view/css/',
              src: ['goteo.min.css'],
              dest: 'dist/view/css'
            }]
          }
        },

        // BANNER
        // Inserts the license message on the top of php and js files
        //
        usebanner: {
          options: {
            linebreak:false,
            position: 'top',
            banner: '<?php\n<%= banner %>\n?>'
          },
          files: {
            src: [
              'dist/**/*.php'
            ]
          }
        },

        chmod: {
            data : {
                options:{
                    mode: '777'
                },
                src: [
                    'dist/data/',
                    'dist/logs/'
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
        },
        watch: {
            gruntfile: {
                files: '<%= jshint.gruntfile.src %>',
                tasks: ['jshint:gruntfile']
            }
        }
    });


    // Default task. Just linter
    grunt.registerTask('default', ['lint']);
    grunt.registerTask('lint', ['jshint', 'phplint']);

    //build task, generates the distribution files
    //ready to deploy on a web server
    grunt.registerTask('build', function(target){
        if(target === 'stage') target = 'dev';
        if(target === 'dev') {
            //some changes
        }

        return grunt.task.run([
            'jshint',
            'phplint',
            'sync',
            'newer:imagemin',
            'cssmin'

        ]);
    });
};
