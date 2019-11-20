<?php
/**
 * GoDaddy Reseller Store restriction class.
 *
 * Handles the Reseller Store user restrictions.
 *
 * @class    Reseller_Store/Restrictions
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Restrictions {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'redirects' ), 1 );
		add_action( 'admin_menu', array( $this, 'admin_submenu' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_submenu' ) );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'edit_screen' ) );

	}

	/**
	 * Enforce admin page restrictions by redirecting.
	 *
	 * @action init
	 * @since  0.2.0
	 */
	public function redirects() {

		if ( ! is_admin() ) {

			return;

		}

		$is_post_type_screen = rstore_is_admin_uri( 'post_type=' . Post_Type::SLUG, false );
		$is_setup_screen     = rstore_is_admin_uri( 'admin.php?page=' . Setup::SLUG );
		$is_add_new_screen   = rstore_is_admin_uri( 'post-new.php?post_type=' . Post_Type::SLUG );

		if (
			$is_post_type_screen
			&&
			( ! rstore_is_setup() || ! rstore_has_products() )
		) {

			rstore_admin_redirect(
				'admin.php',
				array(
					'page' => Setup::SLUG,
				)
			);

		}

		if (
			( $is_setup_screen && rstore_is_setup() && rstore_has_products() )
			||
			( $is_add_new_screen && rstore_has_all_products() )
		) {

			rstore_admin_redirect(
				'edit.php',
				array(
					'post_type' => Post_Type::SLUG,
				)
			);

		}

	}

	/**
	 * Remove `Add New` from the Reseller Product submenu.
	 *
	 * @action admin_menu
	 * @since  0.2.0
	 */
	public function admin_submenu() {

		return remove_submenu_page(
			sprintf( 'edit.php?post_type=%s', Post_Type::SLUG ),
			sprintf( 'post-new.php?post_type=%s', Post_Type::SLUG )
		);

	}

	/**
	 * Remove `Reseller Product` from the `New` admin bar submenu.
	 *
	 * @action wp_before_admin_bar_render
	 * @global WP_Admin_Bar $wp_admin_bar
	 * @since  0.2.0
	 */
	public function admin_bar_submenu() {

		global $wp_admin_bar;

		if ( is_object( $wp_admin_bar ) ) {
			$wp_admin_bar->remove_node( sprintf( 'new-%s', Post_Type::SLUG ) );
		}
	}

	/**
	 * Customize the edit screen when there are no products.
	 *
	 * @action manage_posts_extra_tablenav
	 * @since  0.2.0
	 *
	 * @param string $which Table location.
	 */
	public function edit_screen( $which ) {

		if (
			Post_Type::SLUG !== get_post_type()
			||
			'bottom' !== $which
			||
			rstore_has_products()
		) {

			return;

		}

		?>
		<div class="rstore-blank">
			<h2 class="rstore-blank-message"><?php esc_html_e( 'No products have been added yet.', 'reseller-store' ); ?></h2>
			<p><a href="#" class="rstore-blank-button button button-primary"><?php esc_html_e( 'Import All Products', 'reseller-store' ); ?></a></p>
		</div>
		<?php

	}

}
