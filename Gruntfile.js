/* global module, require */

module.exports = function( grunt ) {

	var pkg = grunt.file.readJSON( 'package.json' );

	grunt.initConfig( {

		pkg: pkg,

		cssjanus: {
			theme: {
				options: {
					swapLtrRtlInUrl: false
				},
				files: [
					{
						expand: true,
						cwd: 'assets/css',
						src: [ '*.css','!*-rtl.css','!*.min.css','!*-rtl.min.css' ],
						dest: 'assets/css',
						ext: '-rtl.css'
					}
				]
			}
		},

		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1,
				processImport: false
			},
			target: {
				files: [
					{
						expand: true,
						cwd: 'assets/css',
						src: [ '*.css', '!*.min.css' ],
						dest: 'assets/css',
						ext: '.min.css'
					}
				]
			}
		},

		devUpdate: {
			main: {
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
			}
		},

		jshint: {
			all: [ 'Gruntfile.js', 'assets/js/*.js', '!assets/js/*.min.js' ]
		},

		makepot: {
			target: {
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
			version_php: {
				src: [
					'**/*.php',
					'!lib/**'
				],
				overwrite: true,
				replacements: [ {
					from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
					to: 'Version:$1' + pkg.version
				}, {
					from: /@version(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
					to: '@version$1' + pkg.version
				}, {
					from: /@since(.*?)NEXT/mg,
					to: '@since$1' + pkg.version
				}, {
					from: /VERSION(\s*?)=(\s*?['"])[a-zA-Z0-9\.\-\+]+/mg,
					to: 'VERSION$1=$2' + pkg.version
				}, {
					from: /\$this->version(\s*?)=(\s*?)'[a-zA-Z0-9\.\-\+]+';/mg,
					to: '$this->version$1=$2\'' + pkg.version + '\';'
				}]
			},
			version_readme: {
				src: 'readme.*',
				overwrite: true,
				replacements: [ {
					from: /^(\*\*|)Stable tag:(\*\*|)(\s*?)[a-zA-Z0-9.-]+(\s*?)$/mi,
					to: '$1Stable tag:$2$3<%= pkg.version %>$4'
				} ]
			},
			pot: {
				src: 'languages/' + pkg.name + '.pot',
				overwrite: true,
				replacements: [
					{
						from: 'SOME DESCRIPTIVE TITLE.',
						to: pkg.title
					},
					{
						from: "YEAR THE PACKAGE'S COPYRIGHT HOLDER",
						to: new Date().getFullYear()
					},
					{
						from: 'FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.',
						to: 'GoDaddy Operating Company, LLC.'
					},
					{
						from: 'charset=CHARSET',
						to: 'charset=UTF-8'
					}
				]
			}
		},

		uglify: {
			options: {
				ASCIIOnly: true
			},
			core: {
				expand: true,
				cwd: 'assets/js',
				src: [ '*.js', '!*.min.js' ],
				dest: 'assets/js',
				ext: '.min.js'
			}
		},

		watch: {
			css: {
				files: [ '*.css', '!*.min.css' ],
				options: {
					nospawn: true,
					cwd: 'assets/css'
				},
				tasks: [ 'cssmin' ]
			},
			uglify: {
				files: [ '*.js', '!*.min.js' ],
				options: {
					nospawn: true,
					cwd: 'assets/js'
				},
				tasks: [ 'uglify' ]
			}
		}

	} );

	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	grunt.registerTask( 'default', [ 'cssjanus', 'cssmin', 'jshint', 'uglify' ] );
	grunt.registerTask( 'update-pot', [ 'makepot' ] );
	grunt.registerTask( 'update-mo', [ 'potomo' ] );
	grunt.registerTask( 'version', [ 'replace' ] );

};
