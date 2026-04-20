const { defineConfig } = require( 'cypress' );

module.exports = defineConfig( {
	e2e: {
		baseUrl: 'http://localhost:8888',
		viewportWidth: 1280,
		viewportHeight: 900,
		defaultCommandTimeout: 10000,
		requestTimeout: 15000,
		video: false,
		screenshotOnRunFailure: true,
		specPattern: 'cypress/e2e/**/*.cy.js',
		supportFile: 'cypress/support/e2e.js',
		env: {
			wpUser: 'admin',
			wpPass: 'password',
			plId: 1,
		},
	},
} );
