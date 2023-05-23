<?php
/**
 * GoDaddy Reseller Store admin notices.
 *
 * Display Reseller Store admin notices.
 *
 * @class    Reseller_Store/Admin_Notices
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

final class Admin_Notices {

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_rstore_dismiss_admin_notice', array( __CLASS__, 'dismiss_admin_notice' ) );

		$is_post_type_screen = rstore_is_admin_uri( 'post_type=' . Post_Type::SLUG, false );
		if ( ! $is_post_type_screen ) {
			return;
		}

		$errors = rstore_get_option( 'errors', array() );

		if ( count( $errors ) > 0 ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_notices', array( $this, 'error_notices' ) );
		}

		if ( ! empty( $_REQUEST['bulk_restore_posts'] ) ) {
			add_action( 'admin_notices', array( $this, 'restore_notice' ) );
		}

	}

	/**
	 * Get the key used to generate the nonce.
	 *
	 * @return string Generated nonce key.
	 * @since  1.3.0
	 */
	private static function nonce_key() {
		return  rstore_prefix( 'notice-' . get_current_user_id() );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  1.3.0
	 */
	public function admin_enqueue_scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-admin-notice', Plugin::assets_url( "js/admin-notice{$suffix}.js" ), array( 'jquery' ), rstore()->version, true );

		wp_localize_script(
			'rstore-admin-notice',
			'rstore_admin_notice',
			array(
				'nonce' => wp_create_nonce( self::nonce_key() ),
			)
		);

	}

	/**
	 * Display restore notices
	 *
	 * @since  1.3.0
	 */
	public function restore_notice() {

		$count = intval( $_REQUEST['bulk_restore_posts'] );
		printf(
			'<div id="rstore-update-success" class="notice notice-success is-dismissible"><p>' .
			/* translators: product count */
			_n(
				'Finished restoring products. %s product updated.',
				'Finished restoring products. %s products updated.',
				$count,
				'reseller-store'
			) . '</p></div>',
			$count
		);
	}

	/**
	 * Display admin notices
	 *
	 * @since  1.3.0
	 */
	public function error_notices() {

		$errors = rstore_get_option( 'errors', array() );

		print( '<div id="rstore-update-error" class="notice notice-error is-dismissible">' );
		foreach ( $errors as $error ) {
			printf( '<p>' . $error->get_error_message() . '</p>', $error->get_error_data() );
		}
		print ( '</div>' );

	}

	/**
	 * Handles Ajax request to persist notices dismissal.
	 *
	 * @since  1.3.0
	 */
	public static function dismiss_admin_notice() {

		if ( false === wp_verify_nonce( filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), self::nonce_key() ) ) {

			$data = esc_html__( 'Sorry, you are not allowed to do that.', 'reseller-store' );

			wp_send_json_error( $data );

		}

		rstore_delete_option( 'errors' );

		wp_send_json_success();
	}

}
