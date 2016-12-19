<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Helpers {

	/**
	 * Return a plugin option.
	 *
	 * @since NEXT
	 *
	 * @param  string $key
	 * @param  mixed  $default (optional)
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false ) {

		return get_option( self::PREFIX . $key, $default );

	}

	/**
	 * Update a plugin option.
	 *
	 * @since NEXT
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function update_option( $key, $value ) {

		return update_option( self::PREFIX . $key, $value );

	}

	/**
	 * Delete a plugin option.
	 *
	 * @since NEXT
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
	public function delete_option( $key ) {

		return delete_option( self::PREFIX . $key );

	}

	/**
	 * Return product meta value, or the global setting fallback.
	 *
	 * @since NEXT
	 *
	 * @param  int    $id
	 * @param  string $key
	 * @param  mixed  $default (optional)
	 *
	 * @return mixed
	 */
	public function get_product_meta( $id, $key, $default = false ) {

		return metadata_exists( 'post', $id, $key ) ? get_post_meta( $id, $key, true ) : $this->get_option( $key, $default );

	}

	/**
	 * Check if the plugin has been setup.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_setup() {

		return ( (int) $this->get_option( 'reseller_id' ) > 0 );

	}

	/**
	 * Safe redirect to any admin page.
	 *
	 * @param string $endpoint (optional)
	 * @param array  $args (optional)
	 * @param int    status (optional)
	 */
	public function admin_redirect( $endpoint = '', $args = [], $status = 302 ) {

		wp_safe_redirect(
			esc_url_raw(
				add_query_arg( $args, admin_url( $endpoint ) )
			),
			$status
		);

		exit;

	}

}
