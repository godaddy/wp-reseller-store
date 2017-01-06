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
	 * @return string  Returns a string prepended with the plugin prefix.
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
	 * @return mixed  Returns the option value if the key exists, otherwise the `$default` parameter value.
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
	 * @return bool  Returns `true` on success, `false` on failure.
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
	 * @return bool  Returns `true` on success, `false` on failure.
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
	 * @return mixed|WP_Error
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

		if ( is_wp_error( $value ) ) {

			return $value; // Return the WP_Error

		}

		$value = ( $value ) ? $value : $default;

		// Always set, even when the value is empty
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
	 * @return bool  Returns `true` on success, `false` on failure.
	 */
	public static function set_transient( $name, $value, $expiration = HOUR_IN_SECONDS ) {

		return set_transient( self::prefix( $name ), $value, (int) $expiration );

	}

	/**
	 * Delete a transient value.
	 *
	 * @since NEXT
	 *
	 * @param  string $name
	 *
	 * @return bool  Returns `true` on success, `false` on failure.
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
	 * @param  int                 $post_id
	 * @param  string|array|object $key
	 * @param  mixed               $value   (optional)
	 *
	 * @return bool  Returns `true` on success, `false` on failure.
	 */
	public static function update_post_meta( $post_id, $key, $value = '' ) {

		$result = update_post_meta( $post_id, self::prefix( $key ), $value );

		/**
		 * WordPress returns the meta_id if the post meta was "added" rather
		 * than "updated". We don't really care, so just returning `true` in
		 * those cases since the meta was created.
		 */
		return is_int( $result ) ? true : $result;

	}

	/**
	 * Update post meta key/value pairs in bulk.
	 *
	 * @since NEXT
	 *
	 * @param  int          $post_id
	 * @param  array|object $meta
	 *
	 * @return bool  Returns `true` on success of _all_ post meta, `false` on failure of _any_ post meta.
	 */
	public static function bulk_update_post_meta( $post_id, $meta ) {

		$results = [];

		foreach ( $meta as $key => $value ) {

			$results[] = self::update_post_meta( (int) $post_id, $key, $value );

		}

		return ! in_array( false, $results, true );

	}

	/**
	 * Return an array of missing product IDs that can be imported.
	 *
	 * @since NEXT
	 *
	 * @return array  Returns an array of product IDs, otherwise an empty array.
	 */
	public static function get_missing_products() {

		if ( ! self::is_setup() ) {

			return [];

		}

		$products = API::get_products();

		if ( is_wp_error( $products ) || empty( $products[0]->id ) ) {

			return [];

		}

		$missing = array_diff(
			wp_list_pluck( $products, 'id' ),
			(array) self::get_option( 'imported', [] )
		);

		return ( $missing ) ? $missing : [];

	}

	/**
	 * Check if the site has imported all available products.
	 *
	 * @since NEXT
	 *
	 * @return bool  Returns `true` if all available products have been imported, otherwise `false`.
	 */
	public static function has_all_products() {

		return ! (bool) self::get_missing_products();

	}

	/**
	 * Check whether products exist.
	 *
	 * Product count is cached in memory to prevent duplicate
	 * queries on the same page load.
	 *
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @return bool  Returns `true` if there are product posts, otherwise `false`. Ignores the `auto-draft` post status.
	 */
	public static function has_products() {

		static $count;

		if ( ! isset( $count ) ) {

			global $wpdb;

			$count = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM `{$wpdb->posts}` WHERE `post_type` = %s AND `post_status` != 'auto-draft';",
					Post_Type::SLUG
				)
			);

		}

		return ( $count > 0 );

	}

	/**
	 * Check if the plugin has been setup.
	 *
	 * @since NEXT
	 *
	 * @return bool  Returns `true` if a private label ID exists, otherwise `false`.
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
	 * @return bool  Returns `true` if the current admin URL contains the specified URI, otherwise `false`.
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

		// Allow full admin URL to be passed as $endpoint
		$endpoint = preg_replace( '/^.*\/wp-admin(\/|$)/', '', $endpoint );

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
