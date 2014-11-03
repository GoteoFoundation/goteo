// COPY
//  The copy task does simply copying of files from one location to another.
//  Most of the otheroperations allow for putting their output files in a
//  particular location. However, some files are "static" and not used in
//  any operations. The copy operation can be used to copy those files as needed,
//  for example, moving files from the app folder to the dist folder for a push
//  to production.

// module.exports = function(grunt) {

//     'use strict';

//     grunt.config('copy', {
//         dist: {
//             files: [{
//                 expand: true,
//                 dot: true,
//                 cwd: 'app',
//                 dest: 'dist',
//                 src: [
//                     '**',
//                     '!config/demo-settings.php',
//                     '!data/**',
//                     '!logs/**',
//                     // '!view/css/**/*.css',
//                     '.htaccess',
//                     'data/.htaccess',
//                     'logs/.htaccess',
//                     'logs/**/README',
//                     '!**/*.{png,jpg,jpeg,gif}',
//                 ]
//             }
//             //, {
//             //     expand: false,
//             //     src: 'app/config/demo-settings.php',
//             //     dest: 'dist/config/settings.php'

//             // }
//             ]
//         }

//     });
//     grunt.loadNpmTasks('grunt-contrib-copy');
// };
