<?php
/**
 * GoDaddy Reseller Store test display class
 */

namespace Reseller_Store;

final class TestClassDisplay extends TestCase {

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

		$display = new Display();
		do_action( 'wp_enqueue_scripts' );

		$this->assertTrue( wp_style_is( 'rstore' ), 'done' );
		$this->assertTrue( wp_script_is( 'js-cookie' ), 'done' );
		$this->assertTrue( wp_script_is( 'rstore' ), 'done' );
		$this->assertTrue( wp_script_is( 'rstore-domain' ), 'done' );

	}

}
