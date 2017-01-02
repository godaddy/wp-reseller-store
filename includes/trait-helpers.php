<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Helpers {

	/**
	 * Add the plugin prefix to a string.
	 *
	 * @since NEXT
	 *
	 * @param  string $string
	 * @param  bool   $use_dashes (optional)
	 *
	 * @return string
	 */
	public static function prefix( $string, $use_dashes = false ) {

		$prefix = ( $use_dashes ) ? str_replace( '_', '-', self::PREFIX ) : self::PREFIX;

		return ( 0 === strpos( $string, $prefix ) ) ? $string : $prefix . $string;

	}

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
	public static function get_option( $key, $default = false ) {

		return get_option( self::prefix( $key ), $default );

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
	public static function update_option( $key, $value ) {

		return update_option( self::prefix( $key ), $value );

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
	public static function delete_option( $key ) {

		return delete_option( self::prefix( $key ) );

	}

	/**
	 * Return a transient value, and optionally set it if it doesn't exist.
	 *
	 * @since NEXT
	 *
	 * @param  string       $name
	 * @param  mixed        $default    (optional)
	 * @param  string|array $callback   (optional)
	 * @param  int          $expiration (optional)
	 *
	 * @return mixed
	 */
	public static function get_transient( $name, $default = null, $callback = null, $expiration = HOUR_IN_SECONDS ) {

		$name = self::prefix( $name );

		$value = get_transient( $name );

		/**
		 * 1. Transient exists: return the cached value
		 * 2. Transient doesn't exist and the callback isn't valid: return the default value
		 */
		if ( false !== $value || ! is_callable( $callback ) ) {

			return ( false !== $value ) ? $value : $default;

		}

		$value = $callback();
		$value = ( $value && ! is_wp_error( $value ) ) ? $value : $default;

		// Always set, even when the value is empty data
		self::set_transient( $name, $value, (int) $expiration );

		return $value;

	}

	/**
	 * Set a transient value.
	 *
	 * @since NEXT
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  int    $expiration (optional)
	 *
	 * @return bool
	 */
	public static function set_transient( $name, $value, $expiration = DAY_IN_SECONDS ) {

		return set_transient( self::prefix( $name ), $value, (int) $expiration );

	}

	/**
	 * Delete a transient value.
	 *
	 * @since NEXT
	 *
	 * @param  string $name
	 *
	 * @return bool
	 */
	public static function delete_transient( $name ) {

		return delete_transient( self::prefix( $name ) );

	}

	/**
	 * Return a product meta value, or its global setting fallback.
	 *
	 * @since NEXT
	 *
	 * @param  int    $post_id
	 * @param  string $key
	 * @param  mixed  $default          (optional)
	 * @param  bool   $setting_fallback (optional)
	 *
	 * @return mixed
	 */
	public static function get_product_meta( $post_id, $key, $default = false, $setting_fallback = false ) {

		$key = self::prefix( $key );

		$meta = get_post_meta( $post_id, $key, true );

		return ( $meta ) ? $meta : ( $setting_fallback ? get_option( $key, $default ) : $default );

	}

	/**
	 * Update post meta value(s).
	 *
	 * @since NEXT
	 *
	 * @param  int   $post_id
	 * @param  mixed $key
	 * @param  mixed $value   (optional)
	 *
	 * @return int|bool
	 */
	public static function update_post_meta( $post_id, $key, $value = '' ) {

		if ( ! is_array( $key ) && ! is_object( $key ) ) {

			return update_post_meta( $post_id, self::prefix( $key ), $value );

		}

		if ( 3 === func_num_args() ) {

			return false;

		}

		foreach ( $key as $_key => $_value ) {

			self::update_post_meta( $post_id, $_key, $_value );

		}

		return true;

	}

	/**
	 * Mark a product as imported.
	 *
	 * @since NEXT
	 *
	 * @param  int    $post_id
	 * @param  string $product_id
	 *
	 * @return bool
	 */
	public static function mark_product_as_imported( $post_id, $product_id ) {

		if ( Post_Type::SLUG !== get_post_type( $post_id ) ) {

			return false;

		}

		$imported = (array) self::get_option( 'imported', [] );

		$imported[ $post_id ] = $product_id;

		return self::update_option( 'imported', $imported );

	}

	/**
	 * Mark an imported product as deleted.
	 *
	 * @since NEXT
	 *
	 * @param  int $post_id
	 *
	 * @return bool
	 */
	public static function mark_product_as_deleted( $post_id ) {

		if ( Post_Type::SLUG !== get_post_type( $post_id ) ) {

			return false;

		}

		self::delete_transient( 'products' ); // Re-fetch products from API

		$imported = (array) self::get_option( 'imported', [] );

		unset( $imported[ $post_id ] );

		return self::update_option( 'imported', $imported );

	}

	/**
	 * Return an array of missing product IDs that can be imported.
	 *
	 * @since NEXT
	 *
	 * @return array
	 */
	public static function get_missing_products() {

		if ( ! self::is_setup() ) {

			return [];

		}

		$products = (array) self::get_transient( 'products', [], function () {

			return rstore()->api->get( 'catalog/{pl_id}/products' );

		} );

		if ( empty( $products[0]->id ) ) {

			return [];

		}

		$missing = array_diff(
			wp_list_pluck( $products, 'id' ),
			(array) self::get_option( 'imported', [] )
		);

		return ( $missing ) ? $missing : [];

	}

	/**
	 * Check if the site is missing products that can be imported.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public static function is_missing_products() {

		return ( self::get_missing_products() );

	}

	/**
	 * Check if the site has imported all available products.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public static function has_all_products() {

		return ! self::is_missing_products();

	}

	/**
	 * Check whether products exist.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public static function has_products() {

		$counts = (array) wp_count_posts( Post_Type::SLUG );

		unset( $counts['auto-draft'] );

		return ( array_sum( $counts ) > 0 );

	}

	/**
	 * Check if the plugin has been setup.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public static function is_setup() {

		return ( (int) self::get_option( 'pl_id' ) > 0 );

	}

	/**
	 * Check if we are on a specific admin screen.
	 *
	 * @since NEXT
	 *
	 * @param  string $request_uri
	 * @param  bool   $strict      (optional)
	 *
	 * @return bool
	 */
	public static function is_admin_uri( $request_uri, $strict = true ) {

		$strpos = strpos( basename( filter_input( INPUT_SERVER, 'REQUEST_URI' ) ), $request_uri );
		$result = ( $strict ) ? ( 0 === $strpos ) : ( false !== $strpos );

		return ( is_admin() && $result );

	}

	/**
	 * Safe redirect to any admin page.
	 *
	 * @since NEXT
	 *
	 * @param string $endpoint (optional)
	 * @param array  $args (optional)
	 * @param int    status (optional)
	 */
	public static function admin_redirect( $endpoint = '', $args = [], $status = 302 ) {

		wp_safe_redirect(
			esc_url_raw(
				add_query_arg( $args, admin_url( $endpoint ) )
			),
			$status
		);

		exit;

	}

	/**
	 * Insert a value into an array at a specific index point.
	 *
	 * @since NEXT
	 *
	 * @param  array $array
	 * @param  mixed $var
	 * @param  int   $index
	 * @param  bool  $preserve_keys (optional)
	 *
	 * @return array
	 */
	public static function array_insert( array $array, $var, $index, $preserve_keys = true ) {

		if ( 0 === $index ) {

			if ( is_array( $var ) ) {

				return array_merge( $var, $array );

			}

			array_unshift( $array, $var );

			return $array;

		}

		return array_merge(
			array_slice( $array, 0, $index, $preserve_keys ),
			is_array( $var ) ? $var : [ $var ],
			array_slice( $array, $index, count( $array ) - $index, $preserve_keys )
		);

	}

}
