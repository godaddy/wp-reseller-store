<?php

namespace Reseller_Store;

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
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'admin_menu', [ $this, 'page' ], 9 );

		add_action( 'admin_enqueue_scripts', function () {

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'rstore-admin-setup', rstore()->assets_url . "js/admin-setup{$suffix}.js", [ 'jquery' ], rstore()->version, true );

		} );

		add_action( 'wp_ajax_rstore_install', [ $this, 'install' ] );

	}

	/**
	 * Register admin page.
	 *
	 * @action admin_menu
	 * @global array $menu
	 * @since  NEXT
	 */
	public function page() {

		if ( Plugin::is_setup() ) {

			return;

		}

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
			<div class="rstore-setup-wrapper">
				<div class="rstore-setup-header">
					<img src="<?php echo esc_url( rstore()->assets_url . 'images/store.svg' ); ?>">
					<h2><?php esc_html_e( "Let's setup your GoDaddy Reseller Store.", 'reseller-store' ); ?></h2>
					<div class="clear"></div>
				</div>
				<div class="rstore-setup-body">
					<h3><?php esc_html_e( 'Enter your Private Label ID to get started.', 'reseller-store' ); ?></h3>
					<p>
						<form id="rstore-setup-form">
							<label class="screen-reader-text" for="rstore-pl-id-field"><?php esc_html_e( 'Enter your Private Label ID:', 'reseller-store' ); ?></label>
							<input type="number" id="rstore-pl-id-field" min="0" autocomplete="off" required="required">
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Submit', 'reseller-store' ); ?></button>
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
	 * Perform the plugin installation.
	 *
	 * @action wp_ajax_rstore_install
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @param int $pl_id (optional)
	 */
	public function install( $pl_id = 0 ) {

		$pl_id = ( (int) $pl_id > 0 ) ? (int) $pl_id : absint( filter_input( INPUT_POST, 'pl_id' ) );

		if ( 0 === $pl_id ) {

			wp_send_json_error( esc_html__( 'Error: Invalid Private Label ID', 'reseller-store' ) );

		}

		Plugin::update_option( 'pl_id', $pl_id );

		new Post_Type;
		new Taxonomy_Category;
		new Taxonomy_Tag;

		do_action( 'init' ); // Register post type and taxonomies for rewrite rules

		flush_rewrite_rules();

		$products = rstore()->api->get( 'catalog/{pl_id}/products' );

		if ( is_wp_error( $products ) ) {

			wp_send_json_error( $products->get_error_message() );

		}

		$products = (array) $products;

		set_transient( Plugin::prefix( 'products' ), $products, DAY_IN_SECONDS );

		foreach ( $products as $product ) {

			$import = new Import( $product );

			if ( ! $import->is_valid_product() || $import->product_exists() ) {

				continue;

			}

			$post_id = $import->post();

			if ( $post_id ) {

				$import->categories( $product->categories, $post_id );

				$import->attachment( $post_id );

			}

		}

		wp_send_json_success(
			[
				'redirect' => esc_url_raw(
					add_query_arg( 'post_type', Post_Type::SLUG, admin_url( 'edit.php' ) )
				)
			]
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

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE %s;",
				'%' . Plugin::prefix( '%' ) // Transients too
			)
		);

		$posts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = %s;",
				Post_Type::SLUG
			)
		);

		// Find the attachments we imported
		$attachments = $wpdb->get_col( "SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = 'rstore_image';" );

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

			$terms = get_terms( $taxonomy, [ 'fields' => 'ids', 'hide_empty' => false ] );

			if ( is_wp_error( $terms ) ) {

				continue;

			}

			foreach ( (array) $terms as $term_id ) {

				wp_delete_term( (int) $term_id, $taxonomy );

			}

		}

	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * @see   register_deactivation_hook()
	 * @since NEXT
	 */
	public static function deactivate() {

		delete_option( Plugin::prefix( 'pl_id' ) );

		flush_rewrite_rules();

	}

}
