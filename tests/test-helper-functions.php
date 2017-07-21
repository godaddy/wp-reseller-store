<?php
/**
 * GoDaddy Reseller Store Base Tests
 */

namespace Reseller_Store;

final class TestHelperFunctions extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * Test the rstore_prefix() method, without underscores.
	 */
	public function test_rstore_prefix_no_dashes() {

		$string = rstore_prefix( 'string' );

		$this->assertEquals( 'rstore_string', $string );

	}

	/**
	 * Test the rstore_prefix() method, with dashes.
	 */
	public function test_rstore_prefix_dashes() {

		$string = rstore_prefix( 'string', true );

		$this->assertEquals( 'rstore-string', $string );

	}

	/**
	 * Test the rstore_is_setup() method.
	 */
	public function test_rstore_is_setup() {

		$this->assertFalse( rstore_is_setup() );

		rstore_update_option( 'pl_id', 1234 );

		$this->assertTrue( rstore_is_setup() );

	}

	/**
	 * Test the rstore_add_to_cart_vars() method.
	 */
	public function test_rstore_add_to_cart_vars() {

		$defaults = [
			'id',
			'quantity',
			'redirect',
			'label',
			'permalink',
		];

		$vars = rstore_get_add_to_cart_vars( Tests\Helper::create_product() );

		array_map( function( $key ) use ( $vars ) {

			$this->assertArrayHasKey( $key, $vars );

		}, $defaults );

	}

	/**
	 * Test the rstore_get_option() method.
	 */
	public function test_rstore_get_option() {

		$this->assertFalse( rstore_get_option( 'random_option' ) );

		$now = strtotime( 'now' );

		update_option( 'rstore_last_sync', $now );

		$this->assertEquals( $now, rstore_get_option( 'last_sync' ) );

	}

	/**
	 * Test the rstore_update_option() method.
	 */
	public function test_rstore_update_option() {

		$this->assertFalse( get_option( 'rstore_random_option' ) );

		rstore_update_option( 'random_option', 'string' );

		$this->assertEquals( 'string', get_option( 'rstore_random_option' ) );

	}

	/**
	 * Test the rstore_delete_option() method.
	 */
	public function test_rstore_delete_option() {

		$this->assertFalse( get_option( 'rstore_random_option' ) );

		update_option( 'rstore_random_option', 'string' );

		$this->assertEquals( 'string', get_option( 'rstore_random_option' ) );

		rstore_delete_option( 'random_option' );

		$this->assertFalse( get_option( 'rstore_random_option' ) );

	}

	/**
	 * Test the rstore_get_transient() method.
	 */
	public function test_rstore_get_transient() {

		$this->assertFalse( get_transient( 'rstore_products' ) );

		// Test a non-existing transient with no callback function.
		$rstore_transient = rstore_get_transient( 'products' );

		$this->assertNull( $rstore_transient );

		// Test a non-existing transient with a callback.
		$test_transient = rstore_get_transient( 'products', [], function() {

			return 'transient results';

		} );

		$this->assertEquals( 'transient results', $test_transient );

	}

}
