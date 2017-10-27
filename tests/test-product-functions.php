<?php
/**
 * GoDaddy Reseller Store Product Functions tests
 */

namespace Reseller_Store;

final class TestProductFunctions extends TestCase {

	/**
	 * @testdox Given rstore does not have products it should return false.
	 */
	public function test_restore_has_products_false() {

		$this->assertFalse( rstore_has_products() );

	}

	/**
	 * @testdox Given rstore does have products it should return true.
	 */
	public function test_restore_has_products_true() {

		$post = Tests\Helper::create_product();

		$this->assertTrue( rstore_has_products() );

	}

	/**
	 * @testdox Given a cache restore_clear_cache should return true.
	 */
	public function test_restore_clear_cache() {

		wp_cache_set( rstore_prefix( 'products_count' ), 1 );

		$this->assertTrue( rstore_clear_cache() );

	}

	/**
	 * @testdox Given missing products restore_has_all_products should return false.
	 */
	public function test_rstore_has_all_products() {

		rstore_update_option( 'pl_id', 1592 );

		$this->assertFalse( rstore_has_all_products() );

	}

	/**
	 * @testdox rstore_get_demo_products should return a list of demo products
	 */
	public function test_rstore_get_demo_products() {

		rstore_update_option( 'pl_id', 1592 );

		$products = rstore_get_demo_products();

		$this->assertTrue( count( $products ) === 2 );

	}

}
