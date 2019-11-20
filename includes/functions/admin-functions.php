<?php
/**
 * GoDaddy Reseller Store admin functionality.
 *
 * Contains the Reseller Store admin side functions.
 *
 * @package  Reseller_Store/Plugin
 * @author   GoDaddy
 * @since    1.0.0
 */

/**
 * Check if we are on a specific admin screen.
 *
 * @since 0.2.0
 *
 * @param  string $request_uri Request URL to check.
 * @param  bool   $strict      (optional) strict check.
 *
 * @return bool  Returns `true` if the current admin URL contains the specified URI, otherwise `false`.
 */
function rstore_is_admin_uri( $request_uri, $strict = true ) {

	$current = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : null; // input var ok.
	$strpos  = strpos( basename( $current ), $request_uri );
	$result  = ( $strict ) ? ( 0 === $strpos ) : ( false !== $strpos );

	return ( is_admin() && $result );

}

/**
 * Add error to error list for display.
 *
 * @since 1.3.0
 *
 * @param  WP_Error $error    Add error to item list.
 *
 * @return bool  Returns `true` on success, `false` on failure.
 */
function rstore_error( $error ) {

	if ( is_wp_error( $error ) ) {

		$errors   = rstore_get_option( 'errors', array() );
		$errors[] = $error;
		return rstore_update_option( 'errors', $errors );
	}

}

/**
 * Safe redirect to any admin page.
 *
 * @since 0.2.0
 *
 * @param string  $endpoint (optional) Endpoint to check.
 * @param array   $args     (optional) Arguments array.
 * @param integer $status   The redierct status to use.
 */
function rstore_admin_redirect( $endpoint = '', $args = array(), $status = 302 ) {

	// Allow full admin URL to be passed as $endpoint.
	$endpoint = preg_replace( '/^.*\/wp-admin(\/|$)/', '', $endpoint );

	wp_safe_redirect(
		esc_url_raw(
			add_query_arg( $args, admin_url( $endpoint ) )
		),
		$status
	);

	exit;

}
