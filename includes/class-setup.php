<?php

namespace Reseller_Store;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

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
	 * Install nonce action name.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	private static $install_nonce;

	/**
	 * Site for the reseller control center
	 *
	 * @since NEXT
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
		 * @since NEXT
		 *
		 * @var string
		 */
		$this->rcc_site = (string) apply_filters( 'rstore_setup_rcc', $this->rcc_site );

		add_action( 'init', function () {

			self::$install_nonce = rstore_prefix( 'install-' . get_current_user_id() );

		} );

		add_action( 'admin_enqueue_scripts',  [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_menu',             [ $this, 'page' ], 9 );
		add_action( 'wp_ajax_rstore_install', [ __CLASS__, 'install' ] );

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

		wp_enqueue_script( 'rstore-admin-setup', Plugin::assets_url( "js/admin-setup{$suffix}.js" ), [ 'jquery' ], rstore()->version, true );

		/**
		 * @todo Work on this logic
		 */
		$nonce = wp_verify_nonce( filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_STRING ), self::$install_nonce );
		$plid  = filter_input( INPUT_GET, 'rstore_plid', FILTER_SANITIZE_STRING );
		$error = '';

		if ( ! $nonce && $plid ) {

			$error = __( 'Invalid nonce token.  Please try again.', 'reseller-store' );

		}

		// @codingStandardsIgnoreStart
		wp_localize_script( 'rstore-admin-setup', 'rstore_admin_setup', [
			'install_nonce' => wp_create_nonce( self::$install_nonce ),
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

		add_menu_page(
			esc_html__( 'Reseller Store Setup', 'reseller-store' ),
			esc_html__( 'Reseller Store', 'reseller-store' ),
			'activate_plugins',
			self::SLUG,
			[ $this, 'content' ],
			'dashicons-cart',
			Post_Type::MENU_POSITION
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
					<h2><?php esc_html_e( "Let's setup your GoDaddy Reseller Store.", 'reseller-store' ); ?></h2>
					<div class="clear"></div>
				</div>
				<div class="rstore-setup-body">
					<h3><?php esc_html_e( 'Register your plugin to import your product catalog.', 'reseller-store' ); ?></h3>
					<p>
						<form id="rstore-setup-form">
							<button type="submit" id="rstore-activate" class="button button-primary" ><?php esc_html_e( 'Login & Activate', 'reseller-store' ); ?></button>
							<div class="rstore-status">
								<?php esc_html_e( 'Importing product catalog and installing', 'reseller-store' ); ?>
								<img id="rstore-spinner" src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
							</div>
							<div class="rstore-error"></div>
						</form>
					</p>

					<p>
						<?php esc_html_e( 'A GoDaddy Account is required to activate the plugin.', 'reseller-store' );?><br/>
						<?php esc_html_e( "We will provide a demo reseller plan if you don't have a reseller plan.", 'reseller-store' ); ?>&nbsp;
						<a href="https://www.godaddy.com/reseller-program"><?php esc_html_e( 'Get your own plan today.', 'reseller-store' ); ?></a>
					</p>
					<p><?php esc_html_e( 'Not interested in activating right now? You will only see two demo products on a demo storefront without the benefits of having your own plan.', 'reseller-store' ); ?>
						<a id="rstore-skip-activate" href="#"><?php esc_html_e( 'Skip activation.', 'reseller-store' ); ?></a>
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
	 * @return true|WP_Error|void
	 */
	public static function install( $pl_id = 0 ) {

		$skip_activation = false;

		if (
			! current_user_can( 'install_plugins' )
			&&
			( ! defined( 'WP_CLI' ) || ! WP_CLI )
		) {

			return self::install_error(
				'invalid_permissions',
				esc_html__( 'Sorry, you are not allowed to install plugins on this site.', 'reseller-store' ) // Use core translation
			);

		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			if ( false === wp_verify_nonce( filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING ), self::$install_nonce ) ) {

				return self::install_error(
					'invalid_nonce',
					esc_html__( 'Sorry, you are not allowed to do that.', 'reseller-store' ) // Use core translation
				);

			}

			$pl_id           = filter_input( INPUT_POST, 'pl_id', FILTER_SANITIZE_NUMBER_INT );
			$skip_activation = filter_input( INPUT_POST, 'skip_activation', FILTER_SANITIZE_STRING );

			if ( $skip_activation ) {

				$pl_id = 1592;

			}

		}

		$pl_id = ( $pl_id > 0 ) ? (int) $pl_id : 0;

		if ( 0 === $pl_id ) {

			return self::install_error(
				'invalid_pl_id',
				esc_html__( 'Private Label ID is invalid.', 'reseller-store' )
			);

		}

		rstore_update_option( 'pl_id', $pl_id );

		$products = [];

		$products = $skip_activation ? rstore_get_demo_products() : rstore_get_products( true );

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

		new Post_Type;
		new Taxonomy_Category;
		new Taxonomy_Tag;

		do_action( 'init' ); // Register post type and taxonomies for rewrite rules

		flush_rewrite_rules();

		foreach ( (array) $products as $product ) {

			$product = new Product( $product );

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

		return true;

	}

	/**
	 * Return an install error.
	 *
	 * @param  string|WP_Error $code
	 * @param  string          $message
	 * @param  string          $data
	 *
	 * @return WP_Error|void  Returns a `WP_Error`, or prints an error as JSON and dies when called during an AJAX request.
	 */
	private static function install_error( $code = '', $message = '', $data = '' ) {

		$wp_error = is_wp_error( $code ) ? $code : false;

		$message = ( $message ) ? $message : esc_html__( 'An unknown error occurred.', 'reseller-store' ); // Use core translation

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			return ( $wp_error ) ? $wp_error : new WP_Error( $code, $message, $data );

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
				'%' . rstore_prefix( '%' ) // Transients too
			)
		);

	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * @see   register_deactivation_hook()
	 * @since 0.2.0
	 */
	public static function deactivate() {

		delete_option( rstore_prefix( 'pl_id' ) );

		flush_rewrite_rules();

	}

}
