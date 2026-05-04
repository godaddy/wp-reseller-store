import wordpress from '@wordpress/eslint-plugin';

export default [
	{
		ignores: [
			'vendor/**',
			'node_modules/**',
			'build/**',
			'assets/js/**/*.min.js',
			'lib/**',
		],
	},
	...wordpress.configs.recommended,
	{
		files: [ '**/*.js', '**/*.jsx' ],
		languageOptions: {
			globals: {
				window: 'readonly',
				document: 'readonly',
				jQuery: 'readonly',
				wp: 'readonly',
			},
		},
		rules: {
			// @wordpress/* packages are provided by WordPress at runtime and
			// externalized via webpack — they are not bundled as local npm packages.
			'import/no-unresolved': [ 'error', { ignore: [ '^@wordpress/' ] } ],
			'import/no-extraneous-dependencies': [
				'error',
				{ peerDependencies: true },
			],
		},
	},
];
