<?php
/**
 * GoDaddy Reseller Store CLI class.
 *
 * Handles the Reseller store 'reseller' CLI commands.
 *
 * @class    Reseller_Store/CLI/Reseller
 * @package  WP_CLI_Command
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

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
	 * @param array $args       Reset arguments array.
	 * @param array $assoc_args Assoc. arguments array.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Private Label ID.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp reseller install 123456
	 *     Success: Reseller Store data imported and installed.
	 */
	public function install( $args, $assoc_args ) {

		WP_CLI::line( 'Installing ...' );

		$result = Setup::install( $args[0] );

		if ( is_wp_error( $result ) ) {

			WP_CLI::error(
				sprintf(
					$result->get_error_message(),
					$result->get_error_data( $result->get_error_code() )
				)
			);

		}

		WP_CLI::success( 'Reseller Store installed.' );

	}

	/**
	 * Uninstall all plugin data.
	 *
	 * @param array $args       Reset arguments array.
	 * @param array $assoc_args Assoc. arguments array.
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

		WP_CLI::line( 'Uninstalling ...' );

		Setup::uninstall( $keep_attachments );

		WP_CLI::success( 'Reseller Store data uninstalled.' );

	}

}
