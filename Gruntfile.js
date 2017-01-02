/* global module, require */

module.exports = function( grunt ) {

	var pkg = grunt.file.readJSON( 'package.json' );

	grunt.initConfig( {

		pkg: pkg,

		clean: {
			po: {
				src: [ 'languages/*.po~' ]
			}
		},

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

		jshint: {
			all: [ 'Gruntfile.js', 'assets/js/*.js', '!assets/js/*.min.js' ]
		},

		po2mo: {
			files: {
				src: 'languages/*.po',
				expand: true
			}
		},

		pot: {
			options: {
				omit_header: false,
				text_domain: pkg.name,
				encoding: 'UTF-8',
				dest: 'languages/',
				keywords: [
					'__',
					'_e',
					'__ngettext:1,2',
					'_n:1,2',
					'__ngettext_noop:1,2',
					'_n_noop:1,2',
					'_c',
					'_nc:4c,1,2',
					'_x:1,2c',
					'_nx:4c,1,2',
					'_nx_noop:4c,1,2',
					'_ex:1,2c',
					'esc_attr__',
					'esc_attr_e',
					'esc_attr_x:1,2c',
					'esc_html__',
					'esc_html_e',
					'esc_html_x:1,2c'
				],
				msgmerge: true
			},
			files: {
				src: [
					pkg.name + '.php',
					'includes/**/*.php'
				],
				expand: true
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
				dest: 'assets/js',
				ext: '.min.js',
				src: [ '*.js', '!*.min.js' ]
			}
		},

		watch: {
			css: {
				files: [ '*.css', '!*.min.css' ],
				options: {
					cwd: 'assets/css',
					nospawn: true
				},
				tasks: [ 'cssmin' ]
			},
			uglify: {
				files: [ '*.js', '!*.min.js' ],
				options: {
					cwd: 'assets/js',
					nospawn: true
				},
				tasks: [ 'uglify' ]
			}
		}

	} );

	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	grunt.registerTask( 'default', [ 'cssjanus', 'cssmin', 'jshint', 'uglify' ] );
	grunt.registerTask( 'update-pot', [ 'pot', 'replace:pot', 'clean:po' ] );
	grunt.registerTask( 'version', [ 'replace' ] );

};
