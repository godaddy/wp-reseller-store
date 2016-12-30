<?php

namespace Reseller_Store;

use \WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class CLI extends \WP_CLI_Command {

	/**
	 * Basic description of the custom subcommand.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 */
	public function install( $args, $assoc_args ) {

		Setup::install();

		WP_CLI::success( 'Reseller Store installed.' );

	}

	/**
	 * Basic description of the custom subcommand.
	 *
	 * ## OPTIONS
	 *
	 * [--keep-attachments]
	 * : Preserve product attachments.
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp reseller uninstall --yes
	 *     Success: Reseller Store data uninstalled.
	 */
	public function uninstall( $args, $assoc_args ) {

		WP_CLI::confirm( 'Are you sure you want remove all Reseller Store data? This cannot be undone.', $assoc_args );

		$keep_attachments = (bool) WP_CLI\Utils\get_flag_value( $assoc_args, 'keep-attachments', false );

		Setup::uninstall( $keep_attachments );

		WP_CLI::success( 'Reseller Store data uninstalled.' );

	}

}
