<?php
/**
 * GoDaddy Reseller Store test restrictions class
 */

namespace Reseller_Store;

final class TestRestrictions extends TestCase {

	/**
	 * @testdox Test that Restrictions class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Restrictions' ) );

	}

	/**
	 * @testdox Given the admin_submenu should remove `Add New` from the Reseller Product submenu.
	 */
	public function test_admin_submenu() {

		$restrictions = new Restrictions();

		$this->assertFalse( $restrictions->admin_submenu() );

	}

	/**
	 * @testdox Given the remove_add_product_button should remove the `Add Product` button from the Reseller Store edit screen.
	 */
	public function test_remove_add_product_button() {

		$restrictions = new Restrictions();
		$restrictions->remove_add_product_button();

		$this->expectOutputRegex( '/body.post-type-reseller_product a.page-title-action { display: none; }/' );

	}

	/**
	 * @testdox Given the admin_bar_submenu should remove `Reseller Product` from the `New` admin bar submenu.
	 */
	public function test_admin_bar_submenu() {

		$restrictions = new Restrictions();

		$this->assertNull( $restrictions->admin_bar_submenu() );

	}

}

