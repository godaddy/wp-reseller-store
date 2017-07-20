/* global module, require */

module.exports = function( grunt ) {

	'use strict';

	var pkg = grunt.file.readJSON( 'package.json' );

	grunt.initConfig( {

		pkg: pkg,

		clean: {
			build: [ 'build/' ]
		},

		copy: {
			build: {
				files: [
					{
						expand: true,
						src: [
							pkg.name + '.php',
							'*.txt',
							'assets/**',
							'includes/**',
							'languages/*.{mo,pot}',
							'lib/**',
							'!lib/**/*.md'
						],
						dest: 'build/'
					}
				]
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
						cwd: 'assets/css/',
						src: [ '*.css', '!*-rtl.css', '!*.min.css', '!*-rtl.min.css' ],
						dest: 'assets/css/',
						ext: '-rtl.css'
					}
				]
			}
		},

		cssmin: {
			options: {
				inline: [ 'none' ],
				roundingPrecision: 5,
				shorthandCompacting: false
			},
			all: {
				files: [
					{
						expand: true,
						cwd: 'assets/css/',
						src: [ '**/*.css', '!**/*.min.css' ],
						dest: 'assets/css/',
						ext: '.min.css'
					}
				]
			}
		},

		devUpdate: {
			packages: {
				options: {
					updateType: 'force'
				}
			}
		},

		imagemin: {
			options: {
				optimizationLevel: 3
			},
			assets: {
				expand: true,
				cwd: 'assets/images/',
				src: [ '**/*.{gif,jpeg,jpg,png,svg}' ],
				dest: 'assets/images/'
			},
			wp_org_assets: {
				expand: true,
				cwd: '.dev/wp-org-assets/',
				src: [ '**/*.{gif,jpeg,jpg,png,svg}' ],
				dest: '.dev/wp-org-assets/'
			}
		},

		jshint: {
			assets: [ 'assets/js/**/*.js', '!assets/js/**/*.min.js' ],
			gruntfile: [ 'Gruntfile.js' ]
		},

		makepot: {
			target: {
				options: {
					domainPath: 'languages/',
					include: [ pkg.name + '.php', 'includes/.+\.php' ],
					mainFile: pkg.name + '.php',
					potComments: 'Copyright (c) {year} GoDaddy Operating Company, LLC. All Rights Reserved.',
					potFilename: pkg.name + '.pot',
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
				cwd: 'languages/',
				src: [ '*.po' ],
				dest: 'languages/',
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
						from: /@since(.*?)NEXT/mg,
						to: '@since$1' + pkg.version
					},
					{
						from: /VERSION(\s*?)=(\s*?['"])[a-zA-Z0-9\.\-\+]+/mg,
						to: 'VERSION$1=$2' + pkg.version
					}
				]
			},
			readme: {
				src: 'readme.*',
				overwrite: true,
				replacements: [
					{
						from: /^(\*\*|)Stable tag:(\*\*|)(\s*?)[a-zA-Z0-9.-]+(\s*?)$/mi,
						to: '$1Stable tag:$2$3<%= pkg.version %>$4'
					},
					{
						from: /@NEXT/g,
						to: '<%= pkg.version %>'
					}
				]
			},
			tests: {
				src: 'tests/**/*.php',
				overwrite: true,
				replacements: [
					{
						from: /@NEXT/g,
						to: '<%= pkg.version %>'
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
				cwd: 'assets/js/',
				src: [ '**/*.js', '!**/*.min.js' ],
				dest: 'assets/js/',
				ext: '.min.js'
			}
		},

		watch: {
			css: {
				files: [ 'assets/css/**/*.css', '!assets/css/**/*.min.css' ],
				tasks: [ 'cssjanus', 'cssmin' ]
			},
			images: {
				files: [
					'assets/images/**/*.{gif,jpeg,jpg,png,svg}',
					'.dev/wp-org-assets/**/*.{gif,jpeg,jpg,png,svg}'
				],
				tasks: [ 'imagemin' ]
			},
			js: {
				files: [ 'assets/js/**/*.js', '!assets/js/**/*.min.js' ],
				tasks: [ 'jshint', 'uglify' ]
			},
			readme: {
				files: 'readme.txt',
				tasks: [ 'readme' ]
			}
		},

		wp_deploy: {
			plugin: {
				options: {
					assets_dir: '.dev/wp-org-assets/',
					build_dir: 'build/',
					plugin_main_file: pkg.name + '.php',
					plugin_slug: pkg.name,
					svn_user: grunt.file.exists( 'svn-username' ) ? grunt.file.read( 'svn-username' ).trim() : false
				}
			}
		},

		wp_readme_to_markdown: {
			options: {
				post_convert: function( readme ) {
					var matches = readme.match( /\*\*Tags:\*\*(.*)\r?\n/ ),
					    tags    = matches[1].trim().split( ', ' ),
					    section = matches[0];

					for ( var i = 0; i < tags.length; i++ ) {
						section = section.replace( tags[i], '[' + tags[i] + '](https://wordpress.org/plugins/tags/' + tags[i] + '/)' );
					}

					// Banner
					if ( grunt.file.exists( '.dev/wp-org-assets/banner-1544x500.png' ) ) {
						readme = readme.replace( '**Contributors:**', "![Banner Image](.dev/wp-org-assets/banner-1544x500.png)\r\n\r\n**Contributors:**" );
					}

					// Tag links
					readme = readme.replace( matches[0], section );

					// Badges
					readme = readme.replace( '## Description ##', grunt.template.process( pkg.badges.join( ' ' ) ) + "  \r\n\r\n## Description ##" );

					// YouTube
					readme = readme.replace( /\[youtube\s+(?:https?:\/\/www\.youtube\.com\/watch\?v=|https?:\/\/youtu\.be\/)(.+?)\]/g, '[![Play video on YouTube](https://img.youtube.com/vi/$1/maxresdefault.jpg)](https://www.youtube.com/watch?v=$1)' );

					return readme;
				}
			},
			main: {
				files: {
					'readme.md': 'readme.txt'
				}
			}
		}

	} );

	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	grunt.registerTask( 'default',    [ 'cssjanus', 'cssmin', 'jshint', 'uglify', 'imagemin', 'readme' ] );
	grunt.registerTask( 'check',      [ 'devUpdate' ] );
	grunt.registerTask( 'build',      [ 'default', 'clean:build', 'copy:build' ] );
	grunt.registerTask( 'deploy',     [ 'build', 'wp_deploy', 'clean:build' ] );
	grunt.registerTask( 'readme',     [ 'wp_readme_to_markdown' ] );
	grunt.registerTask( 'update-mo',  [ 'potomo' ] );
	grunt.registerTask( 'update-pot', [ 'makepot' ] );
	grunt.registerTask( 'version',    [ 'replace', 'readme' ] );

};
