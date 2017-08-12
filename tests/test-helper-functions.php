<?php
/**
 * GoDaddy Reseller Helper Function Tests
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
	 * Tear Down.
	 */
	function tearDown() {

		parent::tearDown();

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

		rstore_update_option( 'last_sync', $now );

		$this->assertEquals( $now, rstore_get_option( 'last_sync' ) );

	}

	/**
	 * Test the rstore_update_option() method.
	 */
	public function test_rstore_update_option() {

		$this->assertFalse( rstore_get_option( 'random_option' ) );

		rstore_update_option( 'random_option', 'string' );

		$this->assertEquals( 'string', rstore_get_option( 'random_option' ) );

	}

	/**
	 * Test the rstore_delete_option() method.
	 */
	public function test_rstore_delete_option() {

		rstore_update_option( 'random_option', 'string' );

		$this->assertEquals( 'string', rstore_get_option( 'random_option' ) );

		rstore_delete_option( 'random_option' );

		$this->assertFalse( get_option( 'random_option' ) );

	}

	/**
	 * Test the rstore_get_transient() method.
	 */
	public function test_rstore_get_transient() {

		$this->assertFalse( get_transient( 'rstore_transient' ) );

		// Test a non-existing transient with no callback function.
		$rstore_transient = rstore_get_transient( 'transient' );

		$this->assertNull( $rstore_transient );

		// Test a non-existing transient with a callback.
		$test_transient = rstore_get_transient( 'transient', array(), function() {

			return 'transient results';

		} );

		$this->assertEquals( 'transient results', $test_transient );
		$this->assertEquals( 'transient results', rstore_get_transient( 'transient' ) );

	}

	/**
	 * Test the rstore_set_transient() method.
	 */
	public function test_rstore_set_transient() {

		$this->assertNull( rstore_get_transient( 'transient' ) );

		$this->assertTrue( rstore_set_transient( 'transient', 'value' ) );

		$this->assertEquals( 'value', rstore_get_transient( 'transient' ) );

	}

	/**
	 * Test the rstore_update_post_meta() method.
	 */
	public function test_rstore_update_post_meta() {

		$product = Tests\Helper::create_product();

		rstore_update_post_meta( $product->ID, 'listPrice', '$10.00' );

		$this->assertEquals( '$10.00', get_post_meta( $product->ID, rstore_prefix( 'listPrice' ), true ) );

	}

	/**
	 * Test the rstore_bulk_update_post_meta() method.
	 */
	public function test_rstore_bulk_update_post_meta() {

		$product = Tests\Helper::create_product();

		$this->assertEquals( 'year', get_post_meta( $product->ID, rstore_prefix( 'term' ), true ) );
		$this->assertEquals( '$70.00', get_post_meta( $product->ID, rstore_prefix( 'listPrice' ), true ) );

		$meta = [
			'term'      => 'hello-world',
			'listPrice' => '$50.00',
		];

		rstore_bulk_update_post_meta( $product->ID, $meta );

		$this->assertEquals( 'hello-world', get_post_meta( $product->ID, rstore_prefix( 'term' ), true ) );
		$this->assertEquals( '$50.00', get_post_meta( $product->ID, rstore_prefix( 'listPrice' ), true ) );

	}

	/**
	 * Test the test_rstore_array_insert() method.
	 */
	public function test_rstore_array_insert() {

		$array = [
			'cb'    => '<input type="checkbox" />',
			'title' => 'Title',
			'cat'   => 'Categories',
			'term'  => 'Tags',
			'date'  => 'Date',
		];

		$this->assertArrayNotHasKey( 'custom', $array );

		$inject = [
			'image' => sprintf(
				'<span class="rstore-image dashicons dashicons-format-image" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
				__( 'Product Image', 'reseller-store' )
			),
		];

		$array = rstore_array_insert( $array, $inject, 3 );
		$keys  = array_keys( $array );

		$this->assertArrayHasKey( 'image', $array );
		$this->assertEquals( 'image', $keys[3] );

	}

}
