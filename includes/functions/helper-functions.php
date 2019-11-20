<?php
/**
 * GoDaddy Reseller Store helper functions.
 *
 * Contains the Reseller Store helper functions used throughout the plugin.
 *
 * @package  Reseller_Store/Plugin
 * @author   GoDaddy
 * @since    1.0.0
 */

/**
 * Returns the plugin instance.
 *
 * @since 0.2.0
 *
 * @return Plugin
 */
function rstore() {

	return Reseller_Store\Plugin::load();

}

/**
 * Add the plugin prefix to a string.
 *
 * @since 0.2.0
 *
 * @param  string $string      Reseller store prefix.
 * @param  bool   $use_dashes (optional) Whether the prefix should use dashes.
 *
 * @return string  Returns a string prepended with the plugin prefix.
 */
function rstore_prefix( $string, $use_dashes = false ) {

	$prefix = ( $use_dashes ) ? str_replace( '_', '-', Reseller_Store\Plugin::PREFIX ) : Reseller_Store\Plugin::PREFIX;

	return ( 0 === strpos( $string, $prefix ) ) ? $string : $prefix . $string;

}

/**
 * Check if the plugin has been setup.
 *
 * @since 0.2.0
 *
 * @return bool Returns `true` if a private label ID exists, otherwise `false`.
 */
function rstore_is_setup() {

	return ( (int) rstore_get_option( 'pl_id' ) > 0 );

}

/**
 * Return a plugin option.
 *
 * @since 0.2.0
 *
 * @param  string $key      Option key to retrieve.
 * @param  mixed  $default (optional) Default option value.
 *
 * @return mixed  Returns the option value if the key exists, otherwise the `$default` parameter value.
 */
function rstore_get_option( $key, $default = false ) {

	return get_option( rstore_prefix( $key ), $default );

}

/**
 * Update a plugin option.
 *
 * @since 0.2.0
 *
 * @param  string $key   Option key to update.
 * @param  mixed  $value New option value.
 *
 * @return bool  Returns `true` on success, `false` on failure.
 */
function rstore_update_option( $key, $value ) {

	return update_option( rstore_prefix( $key ), $value );

}

/**
 * Delete a plugin option.
 *
 * @since 0.2.0
 *
 * @param  string $key Option key to delete.
 *
 * @return bool Returns `true` on success, `false` on failure.
 */
function rstore_delete_option( $key ) {

	return delete_option( rstore_prefix( $key ) );

}

/**
 * Return a transient value, and optionally set it if it doesn't exist.
 *
 * @since 0.2.0
 *
 * @param  string       $name       Transient name to retrieve.
 * @param  mixed        $default    (optional) Default transient value.
 * @param  string|array $callback   (optional) Callback function.
 * @param  int          $expiration (optional) Transient expiration.
 *
 * @return mixed|WP_Error
 */
function rstore_get_transient( $name, $default = null, $callback = null, $expiration = 0 ) {

	$name = rstore_prefix( $name );

	$value = get_transient( $name );

	/**
	 * 1. Transient exists: return the cached value.
	 * 2. Transient doesn't exist and the callback isn't valid: return the default value.
	 */
	if ( false !== $value || ! is_callable( $callback ) ) {

		return ( false !== $value ) ? $value : $default;

	}

	$value = $callback();

	if ( is_wp_error( $value ) ) {

		return $value; // Return the WP_Error.

	}

	$value = ( $value ) ? $value : $default;

	// Always set, even when the value is empty.
	rstore_set_transient( $name, $value, $expiration );

	return $value;

}

/**
 * Set a transient value.
 *
 * @since 0.2.0
 *
 * @param  string $name        Transient name to set.
 * @param  mixed  $value       Transient value to set.
 * @param  int    $expiration (optional) Transient expiration.
 *
 * @return bool  Returns `true` on success, `false` on failure.
 */
function rstore_set_transient( $name, $value, $expiration = 0 ) {

	return set_transient( rstore_prefix( $name ), $value, absint( $expiration ) );

}

/**
 * Delete a transient value.
 *
 * @since 0.2.0
 *
 * @param  string $name Transient to delete.
 *
 * @return bool  Returns `true` on success, `false` on failure.
 */
function rstore_delete_transient( $name ) {

	return delete_transient( rstore_prefix( $name ) );

}

/**
 * Update post meta value(s).
 *
 * @since 0.2.0
 *
 * @param  int                 $post_id Post ID.
 * @param  string|array|object $key     Post meta name.
 * @param  mixed               $value   (optional) Post meta value.
 *
 * @return bool  Returns `true` on success, `false` on failure.
 */
function rstore_update_post_meta( $post_id, $key, $value = '' ) {

	$result = update_post_meta( $post_id, rstore_prefix( $key ), $value );

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
 * @since 0.2.0
 *
 * @param  int          $post_id Post ID.
 * @param  array|object $meta    Post meta.
 *
 * @return bool  Returns `true` on success of _all_ post meta, `false` on failure of _any_ post meta.
 */
function rstore_bulk_update_post_meta( $post_id, $meta ) {

	$results = array();

	foreach ( $meta as $key => $value ) {

		$results[] = rstore_update_post_meta( (int) $post_id, $key, $value );

	}

	return ! in_array( false, $results, true );

}

/**
 * Insert a value into an array at a specific index point.
 *
 * @since 0.2.0
 *
 * @param  array $array          Array to insert value into.
 * @param  mixed $var            Value to insert into array.
 * @param  int   $index          Location in array to insert at.
 * @param  bool  $preserve_keys (optional) Whether or not the array keys should be preserved.
 *
 * @return array
 */
function rstore_array_insert( $array, $var, $index, $preserve_keys = true ) {

	if ( 0 === $index ) {

		if ( is_array( $var ) ) {

			return array_merge( $var, $array );

		}

		array_unshift( $array, $var );

		return $array;

	}

	return array_merge(
		array_slice( $array, 0, $index, $preserve_keys ),
		is_array( $var ) ? $var : array( $var ),
		array_slice( $array, $index, count( $array ) - $index, $preserve_keys )
	);

}
