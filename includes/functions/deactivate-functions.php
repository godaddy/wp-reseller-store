<?php
/**
 * Deactivate reseller store and display an admin notice
 *
 * @since NEXT
 */

function rstore_deactivate() {

	if ( filter_input( INPUT_GET, 'deactivated', FILTER_SANITIZE_NUMBER_INT ) ) {

		add_action( 'admin_notices', 'rstore_deactivate_notice' );

		deactivate_plugins( RSTORE_BASENAME );

		return;

	}

	wp_redirect( add_query_arg( 'deactivated', 1, admin_url( 'plugins.php' ) ) );

	exit;

}
add_action( 'admin_init', 'rstore_deactivate' );

/**
 * Display an admin notice as to why the plugin was deactivated
 *
 * @return mixed Markup for the admin notice
 *
 * @since NEXT
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
