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
	},
];
