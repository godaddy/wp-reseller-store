<?php
/**
 * GoDaddy Reseller Store Widget test cases.
 */

namespace Reseller_Store;

class TestCase extends \WP_UnitTestCase {

	/**
	 * Holds the plugin instance
	 *
	 * @var object
	 */
	protected $plugin;

	/**
	 * Helper function to check validity of action.
	 *
	 * @param string       $action         Action name.
	 * @param array|string $callback       Callback function.
	 * @param string       $function_call  Function call.
	 */
	protected function do_action_validation( $action, $callback, $function_call = 'has_action' ) {

		// Default WP priority.
		$priority = isset( $test[3] ) ? $test[3] : 10;

		// Default function call.
		$function_call = ( in_array( $function_call, [ 'has_action', 'has_filter' ], true ) ) ? $function_call : 'has_action';

		if ( is_array( $callback ) ) {

			$callback_name = is_string( $callback[0] ) ? $callback[0] : get_class( $callback[0] ) . ':' . $callback[1];

		} else {

			$callback_name = $callback;

		}

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
	 * Helper function to check validity of filters.
	 *
	 * @param string       $action   Action name.
	 * @param array|string $callback Callback function.
	 */
	protected function do_filter_validation( $action, $callback ) {

		$this->do_action_validation( $action, $callback, 'has_filter' );

	}

}
