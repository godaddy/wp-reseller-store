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

	}

	/**
	 * Register admin page.
	 *
	 * @action admin_menu
	 * @global array $menu
	 * @since  NEXT
	 */
	public function page() {

		global $menu;

		if ( current_user_can( 'activate_plugins' ) ) {

			$menu[ '55.54' ] = [ '', 'read', 'separator-reseller-store', '', 'wp-menu-separator reseller-store' ];

		}

		add_menu_page(
			esc_html__( 'Reseller Store Setup', 'reseller-store' ),
			esc_html__( 'Reseller Store', 'reseller-store' ),
			'activate_plugins',
			self::SLUG,
			[ $this, 'content' ],
			'dashicons-cart',
			'55.55'
		);

	}

	/**
	 * Admin page content.
	 *
	 * @see   $this->menu()
	 * @since NEXT
	 */
	public function content() {

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
					<p><button href="https://sso.godaddy.com/login?app=reseller" class="button button-primary button-hero"><?php esc_html_e( 'Sign In', 'reseller-store' ); ?></button></p>
					<p><?php esc_html_e( "Don't have an account?", 'reseller-store' ); ?> <a href="https://sso.godaddy.com/account/create?path=/&app=reseller"><?php esc_html_e( 'Create an account', 'reseller-store' ); ?></a></p>
				</div>
				<div class="rstore-setup-footer">
					<p><strong><?php esc_html_e( 'Need help? Call our award-winning support team 24/7 at (480) 505-8877.', 'reseller-store' ); ?></strong></p>
				</div>
			</div>
		</div>
		<?php

	}

}
