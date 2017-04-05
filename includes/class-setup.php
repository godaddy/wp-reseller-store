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
	 * @since NEXT
	 *
	 * @var string
	 */
	const SLUG = 'reseller-store-setup';

	/**
	 * Install nonce action name.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private static $install_nonce;

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

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
	 * @since  NEXT
	 */
	public function admin_enqueue_scripts() {

		if ( ! rstore_is_admin_uri( 'admin.php?page=' . self::SLUG ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-admin-setup', Plugin::assets_url( "js/admin-setup{$suffix}.js" ), [ 'jquery' ], rstore()->version, true );

		// @codingStandardsIgnoreStart
		wp_localize_script( 'rstore-admin-setup', 'rstore_admin_setup', [ 'install_nonce' => wp_create_nonce( self::$install_nonce ) ] );
		// @codingStandardsIgnoreEnd

	}

	/**
	 * Register admin page.
	 *
	 * @action admin_menu
	 * @global array $menu
	 * @since  NEXT
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
	 * @since NEXT
	 */
	public function content() {

		$sso_url = add_query_arg(
			[
				'app'     => 'reseller',
				'referer' => esc_url_raw(
					add_query_arg( 'page', self::SLUG, admin_url( 'admin.php' ) )
				),
			],
			'https://sso.godaddy.com/login'
		);

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
			visibility: hidden;
			max-width: 20px;
			height: auto;
			margin-bottom: -4px;
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
					<h3><?php esc_html_e( 'Enter your Private Label ID to get started.', 'reseller-store' ); ?></h3>
					<p>
						<form id="rstore-setup-form">
							<label class="screen-reader-text" for="rstore-pl-id-field"><?php esc_html_e( 'Enter your Private Label ID:', 'reseller-store' ); ?></label>
							<input type="number" id="rstore-pl-id-field" value="<?php echo rstore_get_option( 'pl_id', '' ); // xss ok ?>" min="0" autocomplete="off" required>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Install Now', 'reseller-store' ); ?></button>
							<img src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
						</form>
					</p>
					<p><?php esc_html_e( "Don't have an account?", 'reseller-store' ); ?> <a href="https://sso.godaddy.com/account/create?path=/&app=reseller"><?php esc_html_e( 'Create an account', 'reseller-store' ); ?></a></p>
				</div>
				<div class="rstore-setup-footer">
					<p><strong><?php esc_html_e( 'Need help? Call our award-winning support team 24/7 at (480) 505-8877.', 'reseller-store' ); ?></strong></p>
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Display an error notice if the required JS is not enqueued.
	 *
	 * @since NEXT
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
	 * @since  NEXT
	 *
	 * @param  int $pl_id (optional)
	 *
	 * @return true|WP_Error|void
	 */
	public static function install( $pl_id = 0 ) {

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

			if ( false === wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), self::$install_nonce ) ) {

				return self::install_error(
					'invalid_nonce',
					esc_html__( 'Sorry, you are not allowed to do that.', 'reseller-store' ) // Use core translation
				);

			}

			$pl_id = absint( filter_input( INPUT_POST, 'pl_id' ) );

		}

		$pl_id = ( $pl_id > 0 ) ? (int) $pl_id : 0;

		if ( 0 === $pl_id ) {

			return self::install_error(
				'invalid_pl_id',
				esc_html__( 'Private Label ID is invalid.', 'reseller-store' )
			);

		}

		rstore_update_option( 'pl_id', $pl_id );

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
	 * @since  NEXT
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
	 * @since NEXT
	 */
	public static function deactivate() {

		delete_option( rstore_prefix( 'pl_id' ) );

		flush_rewrite_rules();

	}

}
