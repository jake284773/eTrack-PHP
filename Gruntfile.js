module.exports = function(grunt) {

    //Initializing the configuration object
    grunt.initConfig({
        sass: {
            default: {
                options: {
                    style: 'compressed',
                    loadPath: [
                        'bower_components/bootstrap-sass-official/vendor/assets/stylesheets/',
                        'bower_components/chosen/'
                    ]
                },
                files: {
                    'public/stylesheets/main.css': 'app/assets/stylesheets/main.scss'
                }
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            js_main: {
                src: [
                    'bower_components/jquery/jquery.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/transition.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/collapse.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/dropdown.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/toggle.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/modal.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/button.js',
                    'bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/tooltip.js',
                    'bower_components/chosen/chosen.jquery.js',
                    'app/assets/javascripts/chosen-config.js'
                ],
                dest: 'public/javascript/main.js'
            },
            js_ie: {
                src: [
                    'bower_components/html5shiv/dist/html5shiv.js',
                    'bower_components/respond/dest/respond.src.js'
                ],
                dest: 'public/javascript/ie.js'
            }
        },
        uglify: {
            options: {
                // Use if you want the names of your functions and variables
                // unchanged
                mangle: true
            },
            main: {
                files: {
                    './public/javascript/main.js':
                        './public/javascript/main.js'
                }
            },
            ie: {
                files: {
                    './public/javascript/ie.js':
                        './public/javascript/ie.js'
                }
            }
        },
        copy: {
            glyphicons: {
                files: [{
                    expand: true,
                    src: ['bower_components/bootstrap-sass-official/vendor/assets/fonts/**'],
                    dest: './public/fonts',
                    flatten: true,
                    filter: 'isFile'
                }]
            },
            images: {
                files: [{
                    expand: true,
                    src: ['app/assets/images/**'],
                    dest: './public/images',
                    flatten: true,
                    filter: 'isFile'
                }]
            }
        },
        watch: {
            sass: {
                files: ['./app/assets/stylesheets/**/*.scss'],
                tasks: ['sass'],
                options: {
                    livereload: 8080
                }
            },
            js_main: {
                files: [
                    'app/assets/javascript/**/*.js'
                ],
                tasks: ['concat:js_main','uglify:main'],
                options: {
                    livereload: 8080
                }
            }
        }
    });

    // Plugin loading
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');

    // Task definition
    grunt.registerTask('default', ['build','watch']);
    grunt.registerTask('build', [
        'copy:glyphicons',
        'copy:images',
        'concat:js_main',
        'uglify:main',
        'concat:js_ie',
        'uglify:ie',
        'sass:default'
    ]);

};