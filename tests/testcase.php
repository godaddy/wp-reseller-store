<?php

namespace Reseller_Store;

class TestCase extends \WP_UnitTestCase {

	/**
	 * @var object Holds the plugin instance
	 */
	protected $plugin;

	/**
	 * Helper function to check validity of action
	 *
	 * @param string       $action
	 * @param array|string $callback
	 * @param string       $function_call
	 */
	protected function do_action_validation( $action, $callback, $function_call = 'has_action' ) {

		// Default WP priority
		$priority = isset( $test[3] ) ? $test[3] : 10;

		// Default function call
		$function_call = ( in_array( $function_call, [ 'has_action', 'has_filter' ] ) ) ? $function_call : 'has_action';

		if ( is_array( $callback ) ) {

			$callback_name = is_string( $callback[0] ) ? $callback[0] : get_class( $callback[0] ) . ':' . $callback[1];

		} else {

			$callback_name = $callback;

		}

		// Run assertion here
		$this->assertEquals(
			$priority,
			$function_call( $action, $callback ),
			"$action is not attached to $callback_name. It might also have the wrong priority (validated priority: $priority)"
		);

		$this->assertTrue(
			is_callable( $callback ),
			"$callback_name is not implemented."
		);

	}

	/**
	 * Helper function to check validity of filters
	 *
	 * @param string       $action
	 * @param array|string $callback
	 */
	protected function do_filter_validation( $action, $callback ) {

		$this->do_action_validation( $action, $callback, 'has_filter' );

	}

}
