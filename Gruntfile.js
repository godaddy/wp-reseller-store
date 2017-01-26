/* global module, require */

module.exports = function( grunt ) {

	'use strict';

	var pkg = grunt.file.readJSON( 'package.json' );

	var BUILD_DIR    = 'build/',
	    SVN_USERNAME = false;

	if ( grunt.file.exists( 'svn-username' ) ) {

		SVN_USERNAME = grunt.file.read( 'svn-username' ).trim();

	}

	grunt.initConfig( {

		pkg: pkg,

		clean: {
			build: [ BUILD_DIR ]
		},

		compress: {
			build: {
				options: {
					archive: pkg.name + '.zip'
				},
				files: [
					{
						expand: true,
						cwd: BUILD_DIR,
						src: [ '**/*' ],
						dest: '/'
					}
				]
			}
		},

		copy: {
			build: {
				files: [
					{
						expand: true,
						src: [
							pkg.name + '.php',
							'license.txt',
							'readme.txt',
							'assets/**',
							'includes/**',
							'languages/*.{mo,pot}',
							'lib/**',
							'!**/*.{ai,DS_Store,eps,git,md,psd}'
						],
						dest: BUILD_DIR
					}
				]
			}
		},

		cssjanus: {
			options: {
				swapLtrRtlInUrl: false
			},
			all: {
				files: [
					{
						expand: true,
						cwd: 'assets/css',
						src: [ '**/*.css', '!**/*-rtl.css', '!**/*.min.css', '!**/*-rtl.min.css' ],
						dest: 'assets/css',
						ext: '-rtl.css'
					}
				]
			}
		},

		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: 5,
				processImport: false
			},
			all: {
				files: [
					{
						expand: true,
						cwd: 'assets/css',
						src: [ '**/*.css', '!**/*.min.css' ],
						dest: 'assets/css',
						ext: '.min.css'
					}
				]
			}
		},

		devUpdate: {
			options: {
				updateType: 'force',
				reportUpdated: false,
				semver: true,
				packages: {
					devDependencies: true,
					dependencies: false
				},
				packageJson: null,
				reportOnlyPkgs: []
			}
		},

		imagemin: {
			options: {
				optimizationLevel: 3
			},
			all: {
				files: [
					{
						expand: true,
						cwd: '/',
						src: [ 'assets/**/*.{gif,jpeg,jpg,png,svg}', 'wp-org-assets/**/*.{gif,jpeg,jpg,png,svg}' ],
						dest: '/'
					}
				]
			}
		},

		jshint: {
			all: [ 'Gruntfile.js', 'assets/js/**/*.js', '!assets/js/**/*.min.js' ]
		},

		makepot: {
			options: {
				domainPath: 'languages/',
				include: [ pkg.name + '.php', 'includes/.+\.php' ],
				potComments: 'Copyright (c) {year} GoDaddy Operating Company, LLC. All Rights Reserved.',
				potHeaders: {
					'x-poedit-keywordslist': true
				},
				processPot: function( pot, options ) {
					pot.headers['report-msgid-bugs-to'] = pkg.bugs.url;
					return pot;
				},
				type: 'wp-plugin',
				updatePoFiles: true
			}
		},

		potomo: {
			files: {
				expand: true,
				cwd: 'languages',
				src: [ '*.po' ],
				dest: 'languages',
				ext: '.mo'
			}
		},

		replace: {
			php: {
				src: [
					pkg.name + '.php',
					'includes/**/*.php'
				],
				overwrite: true,
				replacements: [
					{
						from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: 'Version:$1' + pkg.version
					},
					{
						from: /@version(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: '@version$1' + pkg.version
					},
					{
						from: /@since(.*?)NEXT/mg,
						to: '@since$1' + pkg.version
					},
					{
						from: /VERSION(\s*?={1}\s*?['"]{1})[a-zA-Z0-9\.\-\+]+/mg,
						to: 'VERSION$1' + pkg.version
					}
				]
			},
			readme: {
				src: 'readme.{md,txt}',
				overwrite: true,
				replacements: [
					{
						from: /^(\*\*|)Stable tag:(\*\*|)(\s*?)[a-zA-Z0-9.-]+(\s*?)$/mi,
						to: '$1Stable tag:$2$3<%= pkg.version %>$4'
					}
				]
			}
		},

		uglify: {
			options: {
				ASCIIOnly: true
			},
			all: {
				expand: true,
				cwd: 'assets/js',
				src: [ '**/*.js', '!**/*.min.js' ],
				dest: 'assets/js',
				ext: '.min.js'
			}
		},

		watch: {
			options: {
				nospawn: true
			},
			css: {
				options: {
					cwd: 'assets/css'
				},
				files: [ '**/*.css', '!**/*.min.css' ],
				tasks: [ 'cssjanus', 'cssmin' ]
			},
			js: {
				options: {
					cwd: 'assets/js'
				},
				files: [ '**/*.js', '!**/*.min.js' ],
				tasks: [ 'jshint', 'uglify' ]
			}
		},

		wp_deploy: {
			options: {
				plugin_slug: pkg.name,
				build_dir: BUILD_DIR,
				assets_dir: 'wp-org-assets',
				svn_user: SVN_USERNAME
			}
		}

	} );

	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	grunt.registerTask( 'default', [ 'cssjanus', 'cssmin', 'jshint', 'uglify', 'imagemin' ] );
	grunt.registerTask( 'build', [ 'default', 'version', 'clean:build', 'copy:build', 'compress:build' ] );
	grunt.registerTask( 'deploy', [ 'build', 'wp_deploy', 'clean:build' ] );
	grunt.registerTask( 'update-pot', [ 'makepot' ] );
	grunt.registerTask( 'update-mo', [ 'potomo' ] );
	grunt.registerTask( 'version', [ 'replace' ] );

};
