<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Helpers {

	/**
	 * Return the plugin base directory path.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $path (optional)
	 *
	 * @return string
	 */
	public static function base_dir( $path = '' ) {

		return rstore()->base_dir . $path;

	}

	/**
	 * Return the plugin assets URL.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $path (optional)
	 *
	 * @return string
	 */
	public static function assets_url( $path = '' ) {

		return rstore()->assets_url . $path;

	}

}
