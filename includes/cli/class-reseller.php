<?php

namespace Reseller_Store\CLI;

use Reseller_Store\Setup;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Reseller extends \WP_CLI_Command {

	/**
	 * Import and install all Reseller Store products.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp reseller install 123456 --yes
	 *     Success: Reseller Store data imported and installed.
	 */
	public function install( $args, $assoc_args ) {

		WP_CLI::confirm( 'Are you sure you want to import all Reseller Store products into this site?', $assoc_args );

		Setup::install( $args[0] );

		WP_CLI::success( 'Reseller Store data imported.' );

	}

	/**
	 * Uninstall all plugin data.
	 *
	 * ## OPTIONS
	 *
	 * [--keep-attachments]
	 * : Preserve product attachments.
	 *
	 * [--keep-options]
	 * : Preserve plugin options.
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

		WP_CLI::confirm( 'Are you sure you want to remove all Reseller Store plugin data from this site? This cannot be undone.', $assoc_args );

		$keep_attachments = (bool) WP_CLI\Utils\get_flag_value( $assoc_args, 'keep-attachments', false );

		Setup::uninstall( $keep_attachments );

		WP_CLI::success( 'Reseller Store data uninstalled.' );

	}

}
