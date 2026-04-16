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
	 * Override WP's expectDeprecated() to support PHPUnit 10.
	 *
	 * PHPUnit\Util\Test::parseTestMethodAnnotations() was removed in PHPUnit 10.
	 * WordPress's abstract-testcase.php calls it as the PHPUnit >= 9.5 path,
	 * so we re-implement the method using PHPUnit 10's annotation parser API.
	 */
	public function expectDeprecated(): void {

		if ( ! class_exists( \PHPUnit\Metadata\Parser\Annotation\Registry::class ) ) {
			parent::expectDeprecated();
			return;
		}

		$parser      = \PHPUnit\Metadata\Parser\Annotation\Registry::getInstance();
		$annotations = array(
			'class'  => $parser->forClassName( static::class )->symbolAnnotations(),
			'method' => $parser->forMethod( static::class, $this->name() )->symbolAnnotations(),
		);

		foreach ( array( 'class', 'method' ) as $depth ) {
			if ( ! empty( $annotations[ $depth ]['expectedDeprecated'] ) ) {
				$this->expected_deprecated = array_merge(
					$this->expected_deprecated,
					$annotations[ $depth ]['expectedDeprecated']
				);
			}
			if ( ! empty( $annotations[ $depth ]['expectedIncorrectUsage'] ) ) {
				$this->expected_doing_it_wrong = array_merge(
					$this->expected_doing_it_wrong,
					$annotations[ $depth ]['expectedIncorrectUsage']
				);
			}
		}

		add_action( 'deprecated_function_run', array( $this, 'deprecated_function_run' ), 10, 3 );
		add_action( 'deprecated_argument_run', array( $this, 'deprecated_function_run' ), 10, 3 );
		add_action( 'deprecated_class_run', array( $this, 'deprecated_function_run' ), 10, 3 );
		add_action( 'deprecated_file_included', array( $this, 'deprecated_function_run' ), 10, 4 );
		add_action( 'deprecated_hook_run', array( $this, 'deprecated_function_run' ), 10, 4 );
		add_action( 'doing_it_wrong_run', array( $this, 'doing_it_wrong_run' ), 10, 3 );
		add_action( 'deprecated_function_trigger_error', '__return_false' );
		add_action( 'deprecated_argument_trigger_error', '__return_false' );
		add_action( 'deprecated_class_trigger_error', '__return_false' );
		add_action( 'deprecated_file_trigger_error', '__return_false' );
		add_action( 'deprecated_hook_trigger_error', '__return_false' );
		add_action( 'doing_it_wrong_trigger_error', '__return_false' );
	}

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
		$function_call = ( in_array( $function_call, array( 'has_action', 'has_filter' ), true ) ) ? $function_call : 'has_action';

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
