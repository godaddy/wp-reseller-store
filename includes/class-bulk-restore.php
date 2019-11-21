<?php
/**
 * GoDaddy Reseller Store bulk edit class.
 *
 * Bulk restore the product data from the edit-post screen.
 *
 * @class    Reseller_Store/Bulk_Edit
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.3.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Bulk_Restore {

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		add_filter(
			'bulk_actions-edit-' . Post_Type::SLUG,
			function ( $bulk_actions ) {
				$bulk_actions['restore_product_data'] = esc_html__( 'Restore Product Data', 'reseller-store' );
				return $bulk_actions;
			}
		);

		add_filter( 'handle_bulk_actions-edit-' . Post_Type::SLUG, array( $this, 'bulk_action_handler' ), 10, 3 );

	}

	/**
	 * Action handler for bulk resetting product posts
	 *
	 * @since  1.3.0
	 *
	 * @param string $redirect_to  The redirect URL.
	 * @param string $do_action    The action being taken.
	 * @param array  $post_ids The items to take the action on.
	 * @return mixed               The url to redirect to after success.
	 */
	public function bulk_action_handler( $redirect_to, $do_action, $post_ids ) {
		if ( 'restore_product_data' !== $do_action ) {
			return $redirect_to;
		}

		$products = rstore_get_products( true );

		if ( is_wp_error( $products ) ) {
			rstore_error( $products );
			return $redirect_to;
		}

		$success = 0;

		foreach ( $post_ids as $post_id ) {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {

				$error = new \WP_Error(
					'invalid_permissions',
					esc_html__( 'Current user cannot modify post.', 'reseller-store' )
				);

				rstore_error( $error );

				continue;
			}

			$product_id = rstore_get_product_meta( $post_id, 'id' );

			if ( empty( $product_id ) ) {

				$error = new \WP_Error(
					'invalid_product_id',
					/* translators: product name */
					esc_html__( '`%s` does not have a valid product id.', 'reseller-store' ),
					get_the_title( $post_id )
				);

				rstore_error( $error );

				continue;
			}

			$product = $this->find_product( $product_id, $products );

			if ( false === $product ) {

				$error = new \WP_Error(
					'invalid_product',
					/* translators: product name */
					esc_html__( '`%s` is not a valid product.', 'reseller-store' ),
					get_the_title( $post_id )
				);

				rstore_error( $error );

				continue;

			}

			$import = new Import( $product, $post_id );
			$result = $import->import_product();

			if ( is_wp_error( $result ) ) {

				rstore_error( $result );

				continue;

			}

			$success += 1;

		} // End foreach().

		if ( $success > 0 ) {
			$redirect_to = add_query_arg( 'bulk_restore_posts', $success, $redirect_to );
		}

		return $redirect_to;
	}

	/**
	 * Find the product from the array by id.
	 *
	 * @since  1.3.0
	 *
	 * @param string $id       The product id (i.e. the needle).
	 * @param array  $products Array of products (i.e the haystack).
	 * @return bool|Product
	 */
	private function find_product( $id, $products ) {

		foreach ( (array) $products as $product ) {

			if ( $id === $product->id ) {

				$item = new Product( $product );

				if ( $item->is_valid() ) {

					return $item;

				}
			}
		}

		return false;

	}
}
