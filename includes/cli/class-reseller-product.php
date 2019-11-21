<?php
/**
 * GoDaddy Reseller Store CLI class.
 *
 * Handles the Reseller store 'product' CLI commands.
 *
 * @class    Reseller_Store/CLI/Reseller_Product
 * @package  WP_CLI_Command
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store\CLI;

use Reseller_Store\Post_Type;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Reseller_Product extends \WP_CLI_Command {

	/**
	 * Reset reseller product data on a post.
	 *
	 * @param array $args       Reset arguments array.
	 * @param array $assoc_args Assoc. arguments array.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : One or more post IDs to reset.
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp reseller product reset 1 --yes
	 *     Success: Reseller product data reset.
	 */
	public function reset( $args, $assoc_args ) {

		WP_CLI::confirm( 'Are you sure you want to reset product data? Any customizations will be lost.', $assoc_args );

		$results = array();

		foreach ( $args as $key => $post_id ) {

			$results[ $post_id ] = Post_Type::reset_product_data( $post_id );

		}

		$errors = array_filter(
			$results,
			function( $result ) {

				return is_wp_error( $result );

			}
		);

		if ( $errors ) {

			foreach ( $errors as $post_id => $error ) {

				WP_CLI::error(
					sprintf(
						'[%d] %s',
						$post_id,
						sprintf(
							$error->get_error_message(),
							$error->get_error_data( $error->get_error_code() )
						)
					),
					false
				);

			}
		}

		$no_errors = array_diff_key( $results, $errors );

		if ( ! count( $no_errors ) ) {

			exit;

		}

		WP_CLI::success(
			sprintf(
				'Product data reset for post(s): %s',
				implode( ', ', array_keys( $no_errors ) )
			)
		);

	}

}
