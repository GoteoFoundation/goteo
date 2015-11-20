// AWS S3
// Used by deploy task in cas S3 storage is configured
// Uploads assets to the static bucket (such as css, js, static images)
module.exports = function(grunt) {

    'use strict';

    grunt.config('aws_s3', {
        options: {
                // debug : true,
                accessKeyId: "<%= settings.filesystem.aws.key %>",
                secretAccessKey: "<%= settings.filesystem.aws.secret %>",
                region :  "<%= settings.filesystem.aws.region %>",
                differential: true,
                displayChangesOnly:true,
                uploadConcurrency: 5, // 5 simultaneous uploads
                downloadConcurrency: 5 // 5 simultaneous downloads
            },
            dev: {
                options: {
                    bucket:  "<%= settings.filesystem.bucket.static %>"
                },
                files: [{
                    expand: true,
                    cwd: "<%= goteo.dist %>/view/",
                    dest: "view/",
                    src: ["**/*.{css,png,jpg,gif,js,eot,svg,ttf,woff}"]
                } , {
                    expand: true,
                    cwd: "<%= goteo.dist %>/assets/",
                    dest: "assets/",
                    src: ["**/*.*"]
                }]
            },
    });
    grunt.loadNpmTasks('grunt-aws-s3');
};
