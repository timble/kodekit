module.exports = function(grunt) {

    // measures the time each task takes
    require('time-grunt')(grunt);

    // load time-grunt and all grunt plugins found in the package.json
    require('jit-grunt')(grunt);

    const sass = require('node-sass');

    grunt.registerMultiTask("appendKodekit", "...", function() {
        grunt.file
            .expand(this.data)
            .forEach(function(file) {
                let contents = grunt.file.read(file);
                contents += "\nif(typeof Kodekit === 'undefined') { var Kodekit = Koowa; }\n";

                grunt.file.write(file, contents);
            });
    });

    // grunt config
    grunt.initConfig({

        // Grunt variables
        kodekitAssetspath: 'code/resources/assets',
        KUIPath: '../kodekit-ui/dist',
        JUIPath: '../joomlatools-ui/dist',


        // Shell commands
        shell: {
            updateCanIUse: {
                command: 'npm update caniuse-db'
            }
        },

        appendKodekit: {
            files: [
                '<%= kodekitAssetspath %>/js/build/admin.js',
                '<%= kodekitAssetspath %>/js/min/admin.js',
                '<%= kodekitAssetspath %>/js/build/kodekit.js',
                '<%= kodekitAssetspath %>/js/min/kodekit.js'
            ]
        },


        // Copy Joomlatools UI files
        copy: {
            KUI: {
                files: [
                    {
                        expand: true,
                        cwd: '<%= KUIPath %>/css',
                        src: ['*.css', '!*.min.css'],
                        dest: '<%= kodekitAssetspath %>/css/build/'
                    },
                    {
                        expand: true,
                        cwd: '<%= KUIPath %>/css',
                        src: ['*.min.css'],
                        dest: '<%= kodekitAssetspath %>/css/',
                        rename: function(dest, src) {
                            return dest + src.replace(/\.min/, "");
                        }
                    },
                    {
                        expand: true,
                        cwd: '<%= KUIPath %>/fonts',
                        src: ['**'],
                        dest: '<%= kodekitAssetspath %>/fonts/'
                    },
                    {
                        expand: true,
                        cwd: '<%= KUIPath %>/js',
                        src: ['*.js', '!*.min.js'],
                        dest: '<%= kodekitAssetspath %>/js/build/',
                        rename: function(dest, src) {
                            return dest + src.replace(/koowa\./, "kodekit.");
                        }
                    },
                    {
                        expand: true,
                        cwd: '<%= KUIPath %>/js',
                        src: ['*.min.js'],
                        dest: '<%= kodekitAssetspath %>/js/min/',
                        rename: function(dest, src) {
                            return dest + src.replace(/koowa\./, "kodekit.").replace(/\.min/, "");
                        }
                    }
                ]
            },
            VUE: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/vue/dist',
                        src: ['vue.js'],
                        dest: '<%= kodekitAssetspath %>/js/build/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/vuex/dist',
                        src: ['vuex.js'],
                        dest: '<%= kodekitAssetspath %>/js/build/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/vue/dist',
                        src: ['vue.min.js'],
                        dest: '<%= kodekitAssetspath %>/js/min/',
                        rename: function(dest, src) {
                            return dest + src.replace(/\.min/, "");
                        }
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/vuex/dist',
                        src: ['vuex.min.js'],
                        dest: '<%= kodekitAssetspath %>/js/min/',
                        rename: function(dest, src) {
                            return dest + src.replace(/\.min/, "");
                        }
                    }
                ]
            }
        },


        // Compile sass files
        sass: {
            options: {
                implementation: sass,
                outputStyle: 'minified',
                includePaths: [
                    'bower_components',
                    'node_modules'
                ]
            },
            dist: {
                files: {
                    // Nooku Framework
                    '<%= kodekitAssetspath %>/css/bootstrap.css': '<%= kodekitAssetspath %>/scss/bootstrap.scss',
                    '<%= kodekitAssetspath %>/css/debugger.css': '<%= kodekitAssetspath %>/scss/debugger.scss',
                    '<%= kodekitAssetspath %>/css/dumper.css': '<%= kodekitAssetspath %>/scss/dumper.scss',
                    '<%= kodekitAssetspath %>/css/site.css': '<%= kodekitAssetspath %>/scss/site.scss'
                }
            }
        },


        // Autoprefixer
        autoprefixer: {
            options: {
                browsers: ['> 5%', 'last 2 versions']
            },
            files: {
                nooku: {
                    expand: true,
                    flatten: true,
                    src: '<%= kodekitAssetspath %>/css/*.css',
                    dest: '<%= kodekitAssetspath %>/css/'
                }
            }
        },



        // Watch files
        watch: {
            sass: {
                files: [
                    '<%= kodekitAssetspath %>/scss/*.scss',
                    '<%= kodekitAssetspath %>/scss/**/*.scss'
                ],
                tasks: ['sass', 'autoprefixer'],
                options: {
                    interrupt: true,
                    atBegin: true
                }
            }
        }


    });

    // The dev task will be used during development
    grunt.registerTask('default', ['shell', 'copy', 'watch']);

};