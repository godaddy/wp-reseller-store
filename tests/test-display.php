<?php
/**
 * GoDaddy Reseller Store test display class
 */

namespace Reseller_Store;

final class TestClassDisplay extends TestCase {

	/**
	 * Tear Down.
	 */
	public function tearDown() {

		parent::tearDown();

		$_SERVER['REQUEST_URI'] = '';
		unset( $_REQUEST );

	}

	/**
	 * @testdox Test that Display class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Display' ) );

	}

	/**
	 * @testdox Given the action wp_enqueue_scripts the styles and scripts should render
	 */
	public function test_wp_enqueue_scripts() {

		new Display();
		do_action( 'wp_enqueue_scripts' );

		$this->assertTrue( wp_style_is( 'reseller-store-css' ), 'done' );
		$this->assertTrue( wp_script_is( 'js-cookie' ), 'done' );
		$this->assertTrue( wp_script_is( 'reseller-store-js' ), 'done' );
		$this->assertTrue( wp_script_is( 'reseller-store-domain-js' ), 'done' );

	}

	/**
	 * @testdox Given the action wp_enqueue_scripts the styles and scripts should render
	 */
	public function test_admin_enqueue_scripts() {

		set_current_screen( 'edit.php?post_type=reseller_product' );
		$_SERVER['REQUEST_URI'] = 'edit.php?post_type=reseller_product';

		new Display();
		do_action( 'admin_enqueue_scripts' );

		$this->assertTrue( wp_style_is( 'reseller-store-admin-css' ), 'done' );

	}


}
