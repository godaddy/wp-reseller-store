<?php
/**
 * GoDaddy Reseller Store AJAX test case.
 */

namespace Reseller_Store;

class AjaxTestCase extends \WP_Ajax_UnitTestCase {

	/**
	 * Override WP's expectDeprecated() to support PHPUnit 10.
	 *
	 * @see TestCase::expectDeprecated() for full explanation.
	 */
	public function expectDeprecated(): void {

		if ( ! class_exists( \PHPUnit\Metadata\Annotation\Parser\Registry::class ) ) {
			parent::expectDeprecated();
			return;
		}

		$parser      = \PHPUnit\Metadata\Annotation\Parser\Registry::getInstance();
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

}
