<?php
/**
 * GoDaddy Reseller Store test basics
 */

namespace Reseller_Store;

final class TestPlugin extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * Test that Plugin exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Plugin' ) );
		$this->assertTrue( function_exists( 'rstore' ) );

	}

	/**
	 * Test plugin loads and sets version.
	 */
	public function test_php_version() {

		Plugin::reset();
		$plugin = Plugin::load();
		do_action( 'plugins_loaded' );

		$this->assertGreaterThan( '1.0.0', $plugin->version );

	}

}
