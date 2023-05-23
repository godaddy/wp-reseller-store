<?php
/**
 * GoDaddy Reseller Store setup class.
 *
 * Handles the Reseller Store setup functionality.
 *
 * @class    Reseller_Store/Setup
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Setup {

	/**
	 * Admin page slug.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const SLUG = 'reseller-store-setup';

	/**
	 * POST TYPE SLUG
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'edit.php?post_type=reseller_product';

	/**
	 * Site for the reseller control center
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $rcc_site = 'https://reseller.godaddy.com';


	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		/**
		 * Filter the base rcc site.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		$this->rcc_site = (string) apply_filters( 'rstore_setup_rcc', $this->rcc_site );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'page' ), PHP_INT_MAX - 1997 );
		add_action( 'wp_ajax_rstore_install', array( __CLASS__, 'install' ) );

	}

	/**
	 * Install nonce action name.
	 *
	 * @return string
	 */
	public static function install_nonce() {
		return rstore_prefix( 'install-' . get_current_user_id() );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  0.2.0
	 */
	public function admin_enqueue_scripts() {

		if ( ! rstore_is_admin_uri( 'admin.php?page=' . self::SLUG ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-admin-setup', Plugin::assets_url( "js/admin-setup{$suffix}.js" ), array( 'jquery' ), rstore()->version, true );

		$install_nonce = self::install_nonce();

		/**
		 * @todo Work on this logic
		 */
		$nonce = wp_verify_nonce( filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), $install_nonce );
		$plid  = filter_input( INPUT_GET, 'rstore_plid', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$error = '';

		if ( ! $nonce && $plid ) {

			$error = __( 'Invalid nonce token.  Please try again.', 'reseller-store' );

		}

		// @codingStandardsIgnoreStart
		wp_localize_script( 'rstore-admin-setup', 'rstore_admin_setup', [
			'install_nonce' => wp_create_nonce( $install_nonce ),
			'install_site' => get_site_url(),
			'install_admin_url' => admin_url('admin.php'),
			'rcc_site' => $this->rcc_site,
			'install_error' => $error,
			'install_plid' => $plid,
		] );

	}

	/**
	 * Register admin page.
	 *
	 * @action admin_menu
	 * @global array $menu
	 * @since  0.2.0
	 */
	public function page() {

		add_submenu_page(
			self::PAGE_SLUG,
			esc_html__( 'Reseller Store Setup', 'reseller-store' ),
			esc_html__( 'Setup', 'reseller-store' ),
			'activate_plugins',
			self::SLUG,
			[ $this, 'content' ]
		);

	}

	/**
	 * Admin page content.
	 *
	 * @see   $this->page()
	 * @since 0.2.0
	 */
	public function content() {
		?>
		<style type="text/css">
		.rstore-setup .notice {
			margin-top: 2em;
		}
		.rstore-setup-wrapper {
			margin: auto;
			width: 60%;
			text-align: center;
		}
		.rstore-setup-header {
			margin-top: 50px;
		}
		.rstore-setup-header img {
			float: left;
			width: 160px;
			height: auto;
			margin-right: 15px;
		}
		.rstore-setup-header h2 {
			float: left;
			width: calc(100% - 175px);
			margin: 40px 0;
			font-size: 3.4em;
			font-weight: 400;
			line-height: 1.15;
			text-align: left;
		}
		.rstore-setup-body {
			display: none;
			margin: 40px 0;
		}
		.rstore-setup-body h3 {
			margin-bottom: 30px;
			font-weight: 500;
		}
		.rstore-spinner {
			max-width: 20px;
			height: auto;
			margin-bottom: -4px;
		}
		.rstore-status {
			padding-top: 15px;
			visibility: hidden;
		}
		.rstore-setup button {
			min-width: 170px;
			min-height: 50px;
		}
		.rstore-error {
			color: #d9534f;
			font-weight: 900;
		}
		</style>
		<div class="rstore-setup">
			<?php $this->missing_script_notice(); ?>
			<div class="rstore-setup-wrapper">
				<div class="rstore-setup-header">
					<img src="<?php echo esc_url( Plugin::assets_url( 'images/store.svg' ) ); ?>">
					<h2><?php esc_html_e( "Let's setup your Reseller Store.", 'reseller-store' ); ?></h2>
					<div class="clear"></div>
				</div>
				<div class="rstore-setup-body">
					<h3><?php esc_html_e( 'Register your plugin to import your product catalog. Please select where you purchased your reseller plan from.', 'reseller-store' ); ?></h3>
                    <h2><?php esc_html_e( 'Sign in and Activate with your:', 'reseller-store' ); ?></h2>
					<p>
                        <button type="button" id="rstore-activate-gd" class="rstore-activate-button button button-primary" ><?php esc_html_e( 'GoDaddy Account', 'reseller-store' ); ?></button>
                        <button type="button" id="rstore-activate-other" class="rstore-activate-button button button-primary" ><?php esc_html_e( 'Other Reseller Account', 'reseller-store' ); ?></button>
                        <div class="rstore-status">
                            <?php esc_html_e( 'Importing product catalog and installing', 'reseller-store' ); ?>
                            <img id="rstore-spinner" src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
                        </div>
                        <div class="rstore-error"></div>
					</p>

					<p>
						<?php esc_html_e( 'A reseller plan is required to activate the plugin.', 'reseller-store' );?><br/>
						<?php esc_html_e( "We will provide a demo reseller plan if you don't have a reseller plan.", 'reseller-store' ); ?>&nbsp;
						<a href="https://www.godaddy.com/reseller-program"><?php esc_html_e( 'Get your own plan today.', 'reseller-store' ); ?></a>
					</p>
				</div>
				<div class="rstore-setup-footer">
					<p><strong><?php esc_html_e( 'Need help? Call our award-winning support team 24/7 at (480) 505-8857.', 'reseller-store' ); ?></strong></p>
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Display an error notice if the required JS is not enqueued.
	 *
	 * @since 0.2.0
	 */
	public function missing_script_notice() {

		if ( wp_script_is( 'rstore-admin-setup', 'enqueued' ) ) {

			return;

		}

		printf(
			'<div id="message" class="error notice"><p>%s</p></div>',
			sprintf(
				/* translators: name of missing script */
				esc_html__( 'Error: Missing required script for setup %s', 'reseller-store' ),
				'<code>admin-setup.js</code>'
			)
		);

	}

	/**
	 * Perform the plugin installation.
	 *
	 * @action wp_ajax_rstore_install
	 * @global wpdb $wpdb
	 * @since  0.2.0
	 *
	 * @param  int $pl_id (optional)
	 *
	 * @return true|\WP_Error|void
	 */
	public static function install( $pl_id = 0 ) {

		if (
			! current_user_can( 'manage_options' )
			&&
			( ! defined( 'WP_CLI' ) || ! WP_CLI )
		) {

			return self::install_error(
				'invalid_permissions',
				esc_html__( 'Sorry, you are not allowed to install plugins on this site.', 'reseller-store' ) // Use core translation
			);

		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			if ( false === wp_verify_nonce( filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), self::install_nonce() ) ) {

				return self::install_error(
					'invalid_nonce',
					esc_html__( 'Sorry, you are not allowed to do that.', 'reseller-store' ) // Use core translation
				);

			}

			$pl_id = filter_input( INPUT_POST, 'pl_id', FILTER_SANITIZE_NUMBER_INT );

		}

		$pl_id = ( $pl_id > 0 ) ? (int) $pl_id : 0;

		if ( 0 === $pl_id ) {

			return self::install_error(
				'invalid_pl_id',
				esc_html__( 'Private Label ID is invalid.', 'reseller-store' )
			);

		}

		rstore_update_option( 'pl_id', $pl_id );

		self::import();
	}

	/**
	 * Perform the plugin product import.
	 *
	 * @action wp_ajax_rstore_import
	 * @global wpdb $wpdb
	 * @since  2.2.0
	 *
	 * @return true|\WP_Error|void
	 */
	public static function import( ) {

		if (
			! current_user_can( 'publish_posts' )
			&&
			( ! defined( 'WP_CLI' ) || ! WP_CLI )
		) {

			return self::install_error(
				'invalid_permissions',
				esc_html__( 'Sorry, you are not allowed to install plugins on this site.', 'reseller-store' ) // Use core translation
			);

		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			if ( false === wp_verify_nonce( filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), self::install_nonce() ) ) {

				return self::install_error(
					'invalid_nonce',
					esc_html__( 'Sorry, you are not allowed to do that.', 'reseller-store' ) // Use core translation
				);

			}
		}

		$products = rstore_get_products( true );

		if ( is_wp_error( $products ) ) {

			rstore_delete_option( 'pl_id' ); // The ID might be unauthorized

			return self::install_error( $products );

		}

		if ( ! $products ) {

			return self::install_error(
				'no_products_found',
				esc_html__( 'There are no products available, please try again later.', 'reseller-store' )
			);

		}

		foreach ( (array) $products as $productData ) {

			$product = new Product( $productData );

			$result = $product->import();

			if ( is_wp_error( $result ) ) {

				return self::install_error( $result );

			}

		}

		rstore_clear_cache();

		if ( ! rstore_has_products() ) {

			return self::install_error(
				'products_import_failure',
				esc_html__( 'Product data could not be imported, please try again later.', 'reseller-store' )
			);

		}

		rstore_update_option( 'last_sync', time() );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			wp_send_json_success(
				[
					'redirect' => esc_url_raw(
						add_query_arg( 'post_type', Post_Type::SLUG, admin_url( 'edit.php' ) )
					),
				]
			);

		}
	}

	/**
	 * Return an install error.
	 *
	 * @param  string|\WP_Error $code
	 * @param  string          $message
	 * @param  string          $data
	 *
	 * @return \WP_Error|void  Returns a `WP_Error`, or prints an error as JSON and dies when called during an AJAX request.
	 */
	private static function install_error( $code = '', $message = '', $data = '' ) {

		$wp_error = is_wp_error( $code ) ? $code : false;

		$message = ( $message ) ? $message : esc_html__( 'An unknown error occurred.', 'reseller-store' ); // Use core translation

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			return ( $wp_error ) ? $wp_error : new \WP_Error( $code, $message, $data );

		}

		$message = ( $wp_error ) ? $wp_error->get_error_message() : $message;
		$data    = ( $wp_error ) ? $wp_error->get_error_data( $wp_error->get_error_code() ) : $data;

		wp_send_json_error(
			sprintf(
				/* translators: error message */
				esc_html__( 'Error: %s', 'reseller-store' ),
				sprintf( $message, $data )
			)
		);

	}

	/**
	 * Uninstall plugin data.
	 *
	 * @global wpdb $wpdb
	 * @see    register_uninstall_hook()
	 * @since  0.2.0
	 *
	 * @param bool $keep_attachments (optional)
	 */
	public static function uninstall( $keep_attachments = true ) {

		global $wpdb;

		$posts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = %s;",
				Post_Type::SLUG
			)
		);

		// Find the attachments we imported
		$attachments = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = %s;",
				rstore_prefix( 'image' )
			)
		);

		foreach ( $posts as $post_id ) {

			// Find all attachments tied to product posts
			$attachments = array_merge(
				$attachments,
				array_map(
					function ( $attachment ) {
						return $attachment->ID;
					},
					get_attached_media( 'image', $post_id )
				)
			);

			wp_delete_post( (int) $post_id, true );

		}

		if ( ! $keep_attachments ) {

			foreach ( $attachments as $attachment_id ) {

				wp_delete_attachment( (int) $attachment_id, true );

			}

		}

		foreach ( [ Taxonomy_Category::SLUG, Taxonomy_Tag::SLUG ] as $taxonomy ) {

			// @codingStandardsIgnoreStart
			$terms = get_terms( $taxonomy, [ 'fields' => 'ids', 'hide_empty' => false ] );
			// @codingStandardsIgnoreEnd

			if ( is_wp_error( $terms ) ) {

				continue;

			}

			foreach ( (array) $terms as $term_id ) {

				wp_delete_term( (int) $term_id, $taxonomy );

			}
		}

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE %s;",
				'%' . rstore_prefix( '%' ) // Transients too.
			)
		);

	}

	/**
	 * Runs on plugin deactivation.
	 * Clear the private label id and any errors.
	 *
	 * @see   register_deactivation_hook()
	 * @since 0.2.0
	 */
	public static function deactivate() {

		delete_option( rstore_prefix( 'pl_id' ) );
		delete_option( rstore_prefix( 'errors' ) );

		flush_rewrite_rules();

	}

}
