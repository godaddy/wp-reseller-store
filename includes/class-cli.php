<?php

namespace Reseller_Store;

use \WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class CLI extends \WP_CLI_Command {

	/**
	 * Basic description of the custom command.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 */
	public function __invoke( $args, $assoc_args ) {

		WP_CLI::success( 'Hello, world' );

	}

	/**
	 * Basic description of the custom subcommand.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 */
	public function product( $args, $assoc_args ) {

		WP_CLI::success( 'Hello, world' );

	}

}
