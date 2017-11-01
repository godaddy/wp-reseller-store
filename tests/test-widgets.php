<?php
/**
 * GoDaddy Reseller Store Widget tests
 */

namespace Reseller_Store;

final class TestWidget extends TestCase {

	/**
	 * Test that all required actions and filters are added as expected.
	 */
	function test_init() {

		new Widgets();

		$this->do_action_validation( 'widgets_init', [ __NAMESPACE__ . '\Widgets', 'register_widgets' ] );

	}

	/**
	 * Test for register_widget function.
	 */
	function test_register_widget() {

		$this->assertTrue(
			method_exists( __NAMESPACE__ . '\Widgets', 'register_widgets' ),
			'Method Widgets::register_widgets is not found'
		);

		Widgets::register_widgets();

		global $wp_widget_factory;

	}

}
