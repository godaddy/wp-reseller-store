<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Restrictions {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'init',                        [ $this, 'redirects' ], 1 );
		add_action( 'admin_menu',                  [ $this, 'admin_submenu' ] );
		add_action( 'admin_head',                  [ $this, 'add_product_button' ] );
		add_action( 'wp_before_admin_bar_render',  [ $this, 'admin_bar_submenu' ] );
		add_action( 'manage_posts_extra_tablenav', [ $this, 'edit_screen' ] );
//		add_action( 'manage_posts_extra_tablenav', [ $this, 'new_post_screen' ] );

	}

	/**
	 * Enforce admin page restrictions by redirecting.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function redirects() {

		if ( ! is_admin() ) {

			return;

		}

		$is_post_type_screen = Plugin::is_admin_uri( 'post_type=' . Post_Type::SLUG, false );
		$is_setup_screen     = Plugin::is_admin_uri( 'admin.php?page=' . Setup::SLUG );
		$is_add_new_screen   = Plugin::is_admin_uri( 'post-new.php?post_type=' . Post_Type::SLUG );

		if ( $is_post_type_screen && ( ! Plugin::is_setup() || ! Plugin::has_products() ) ) {

			Plugin::admin_redirect(
				'admin.php',
				[
					'page' => Setup::SLUG,
				]
			);

		}

		if (
			( $is_setup_screen && Plugin::is_setup() && Plugin::has_products() )
			||
			( $is_add_new_screen && Plugin::has_all_products() )
		) {

			Plugin::admin_redirect(
				'edit.php',
				[
					'post_type' => Post_Type::SLUG,
				]
			);

		}

	}

	/**
	 * Remove `Add New` from the Reseller Product submenu.
	 *
	 * @action admin_menu
	 * @since  NEXT
	 */
	public function admin_submenu() {

		if ( Plugin::is_missing_products() ) {

			return;

		}

		remove_submenu_page(
			sprintf( 'edit.php?post_type=%s', Post_Type::SLUG ),
			sprintf( 'post-new.php?post_type=%s', Post_Type::SLUG )
		);

	}

	/**
	 * Remove the `Add Product` button from the Reseller Store edit screen.
	 *
	 * @action admin_head
	 * @since  NEXT
	 */
	public function add_product_button() {

		if ( Plugin::is_missing_products() ) {

			return;

		}

		?>
		<style type="text/css">
		body.post-type-<?php echo esc_attr( Post_Type::SLUG ); ?> a.page-title-action { display: none; }
		</style>
		<?php

	}

	/**
	 * Remove `Reseller Product` from the `New` admin bar submenu.
	 *
	 * @action wp_before_admin_bar_render
	 * @global WP_Admin_Bar $wp_admin_bar
	 * @since  NEXT
	 */
	public function admin_bar_submenu() {

		if ( Plugin::is_missing_products() ) {

			return;

		}

		global $wp_admin_bar;

		$wp_admin_bar->remove_node( sprintf( 'new-%s', Post_Type::SLUG ) );

	}

	/**
	 * Customize the edit screen when there are no products.
	 *
	 * @action manage_posts_extra_tablenav
	 * @since  NEXT
	 *
	 * @param string $which
	 */
	public function edit_screen( $which ) {

		if (
			Post_Type::SLUG !== get_post_type()
			||
			'bottom' !== $which
			||
			Plugin::has_products()
		) {

			return;

		}

		?>
		<style type="text/css">
		.rstore-blank { margin-top: 50px; text-align: center; }
		.rstore-blank h2 { font-weight: 400; }
		#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav-pages, .bulkactions, .search-box, #screen-meta-links, .wrap .subsubsub { display: none; }
		.tablenav a.rstore-blank-button { display: inline-block; }
		</style>
		<div class="rstore-blank">
			<h2 class="rstore-blank-message"><?php esc_html_e( 'No products have been added yet.', 'reseller-store' ); ?></h2>
			<p><a href="#" class="rstore-blank-button button button-primary"><?php esc_html_e( 'Import All Products', 'reseller-store' ); ?></a></p>
		</div>
		<?php

	}

}
