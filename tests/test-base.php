<?php
/**
 * GoDaddy Reseller Store Base Tests
 */

namespace Reseller_Store;

final class TestBase extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * Test the plugin data exists.
	 */
	public function test_plugin_data() {

		$static_attributes = [
			'version',
			'basename',
			'base_dir',
			'assets_url',
			'api',
		];

		array_map( function( $attr ) {

			$this->assertNotNull( rstore()->$attr );

		}, $static_attributes );

	}

	/**
	 * Test i18n.
	 */
	public function test_i18n() {

		wp_die( dirname( $this->basename ) . '/languages' );

		load_plugin_textdomain( 'reseller-store', false, dirname( $this->basename ) . '/languages' );

	}

}
