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

}
