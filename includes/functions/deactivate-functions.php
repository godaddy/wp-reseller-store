<?php
/**
 * Deactivate reseller store and display an admin notice
 *
 * @since NEXT
 */

/**
 * Deactivate the plugin
 *
 * @since NEXT
 */
function rstore_deactivate() {

	if ( filter_input( INPUT_GET, 'rstore-deactivated', FILTER_SANITIZE_NUMBER_INT ) ) {

		add_action( 'admin_notices', 'rstore_deactivate_notice' );

		deactivate_plugins( RSTORE_BASENAME );

		return;

	}

	wp_redirect( add_query_arg( 'rstore-deactivated', 1, admin_url( 'plugins.php' ) ) );

	exit;

}
add_action( 'admin_init', 'rstore_deactivate' );

/**
 * Remove custom query arg from URL
 *
 * @since NEXT
 *
 * @param array $args Initial query arguemnts array
 *
 * @return array Final query argument array
 */
function rstore_removable_arg( $args ) {

	array_push( $args, 'rstore-deactivated' );

	return $args;

}
add_filter( 'removable_query_args', 'rstore_removable_arg' );

/**
 * Display an admin notice
 *
 * @since NEXT
 *
 * @return mixed Markup for the admin notice
 */
function rstore_deactivate_notice() {

	printf(
		'<div class="notice notice-error">
			<p>%s</p>
		</div>',
		sprintf(
			esc_html__( 'Oh no! It looks like your using an outdated version of PHP. The GoDaddy Reseller Plugin requires PHP v5.4.0 or later. You currently have PHP v%1$s installed. Please upgrade.', 'reseller-store' ),
			phpversion()
		)
	);

	return;

}
