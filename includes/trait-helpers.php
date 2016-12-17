<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Helpers {

	/**
	 * Return a plugin option value.
	 *
	 * @since NEXT
	 *
	 * @param  string $key
	 * @param  mixed  $default (optional)
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false ) {

		return get_option( "rstore_{$key}", $default );

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
	 * Check if the plugin is setup.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_setup() {

		return ! empty( $this->get_option( 'id' ) );

	}

}
