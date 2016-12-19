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

		add_action( 'init', [ $this, 'redirects' ], 1 );

		add_action( 'init', [ $this, 'listener' ] );

		add_action( 'admin_menu', [ $this, 'page' ], 9 );

	}

	/**
	 * Check if we are on the Setup screen.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_setup_screen() {

		return ( is_admin() && 0 === strpos( basename( filter_input( INPUT_SERVER, 'REQUEST_URI' ) ), 'admin.php?page=' . Setup::SLUG ) );

	}

	/**
	 * Check if we are on a screen for our post type.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_post_type_screen() {

		return ( is_admin() && false !== strpos( basename( filter_input( INPUT_SERVER, 'REQUEST_URI' ) ), 'post_type=' . Post_Type::SLUG ) );

	}

	/**
	 * Do admin redirects depending on setup status.
	 *
	 * @action init - 1
	 * @since  NEXT
	 */
	public function redirects() {

		if ( ! is_admin() ) {

			return;

		}

		if ( ! rstore()->is_setup() && $this->is_post_type_screen() ) {

			rstore()->admin_redirect( 'admin.php', [ 'page' => self::SLUG ] );

		}

		if ( rstore()->is_setup() && $this->is_setup_screen() ) {

			rstore()->admin_redirect( 'edit.php', [ 'post_type' => Post_Type::SLUG ] );

		}

	}

	/**
	 * Listen for SSO handoff and install.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function listener() {

		if ( ! $this->is_setup_screen() ) {

			return;

		}

		if ( $pl_id = (int) filter_input( INPUT_GET, 'pl_id' ) ) {

			$this->install( $pl_id );

			rstore()->admin_redirect( 'edit.php', [ 'post_type' => Post_Type::SLUG ] );

		}

	}

	/**
	 * Register admin page.
	 *
	 * @action admin_menu
	 * @global array $menu
	 * @since  NEXT
	 */
	public function page() {

		if ( rstore()->is_setup() ) {

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
		.wp-core-ui .rstore-setup .button.button-hero {
			padding: 0 72px;
			font-size: 18px;
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
					<h3><?php esc_html_e( 'Sign in with your Reseller account to get started.', 'reseller-store' ); ?></h3>
					<p><a href="<?php echo esc_url( $sso_url ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'Sign In', 'reseller-store' ); ?></a></p>
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
	 * Install the plugin.
	 *
	 * @since NEXT
	 *
	 * @param  int $pl_id
	 *
	 * @return bool
	 */
	public function install( $pl_id ) {

		if ( rstore()->is_setup() || ! is_int( $pl_id ) ) {

			return false;

		}

		rstore()->update_option( 'pl_id', $pl_id );

		flush_rewrite_rules();

		return true;

	}

	/**
	 * Uninstall plugin data.
	 *
	 * @global wpdb $wpdb
	 * @see    register_uninstall_hook()
	 * @since  NEXT
	 */
	public static function uninstall() {

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE %s;",
				Plugin::PREFIX . '%'
			)
		);

		$posts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = %s;",
				Post_Type::SLUG
			)
		);

		foreach ( $posts as $post_id ) {

			wp_delete_post( (int) $post_id, true );

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

		delete_option( Plugin::PREFIX . 'pl_id' );

		flush_rewrite_rules();

	}

}
