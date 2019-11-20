<?php
/**
 * GoDaddy Reseller Store Widget tests
 */

namespace Reseller_Store;

final class TestWidget extends TestCase {

	/**
	 * Test that all required actions and filters are added as expected.
	 */
	function test_widgets_init() {

		$widget = new Widgets();

		$this->do_action_validation( 'widgets_init', array( $widget, 'register_widgets' ) );

	}

	/**
	 * Test that all required actions and filters are added as expected.
	 */
	function test_init() {

		$widget = new Widgets();

		$this->do_action_validation( 'init', array( $widget, 'load_fl_modules' ) );

	}

	/**
	 * Test that all required actions and filters are added as expected.
	 */
	function test_vc_before_init() {

		$widget = new Widgets();

		$this->do_action_validation( 'vc_before_init', array( $widget, 'load_vc_modules' ) );

	}

	/**
	 * Test for register_widget function.
	 */
	function test_register_widget() {

		$this->assertTrue(
			method_exists( __NAMESPACE__ . '\Widgets', 'register_widgets' ),
			'Method Widgets::register_widgets is not found'
		);

		new Widgets();

		do_action( 'widgets_init' );
		do_action( 'init' );
		do_action( 'vc_before_init' );

		global $wp_widget_factory;

	}

}
