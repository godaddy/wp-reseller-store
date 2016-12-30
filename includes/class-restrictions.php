<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Restrictions {

	/**
	 * Array of product IDs that can be imported.
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	public $available_products = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'init', function () {

			if ( is_user_logged_in() ) {

				$this->available_products = $this->get_available_products();

			}

		}, 0 );

		add_action( 'init', [ $this, 'redirects' ], 1 );

		add_action( 'admin_menu', [ $this, 'admin_submenu' ] );

		add_action( 'admin_head', [ $this, 'add_product_button' ] );

		add_action( 'wp_before_admin_bar_render', [ $this, 'admin_bar_submenu' ] );

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

		$is_post_type_screen = Plugin::is_admin_screen( 'post_type=' . Post_Type::SLUG, false );
		$is_setup_screen     = Plugin::is_admin_screen( 'admin.php?page=' . Setup::SLUG );
		$is_add_new_screen   = Plugin::is_admin_screen( 'post-new.php?post_type=' . Post_Type::SLUG );

		if ( $is_post_type_screen && ! Plugin::is_setup() ) {

			Plugin::admin_redirect(
				'admin.php',
				[
					'page' => Setup::SLUG,
				]
			);

		}

		if (
			( $is_setup_screen && Plugin::is_setup() )
			||
			( $is_add_new_screen && empty( $this->available_products ) )
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

		if ( ! empty( $this->available_products ) ) {

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

		if ( ! empty( $this->available_products ) ) {

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

		if ( ! empty( $this->available_products ) ) {

			return;

		}

		global $wp_admin_bar;

		$wp_admin_bar->remove_node( sprintf( 'new-%s', Post_Type::SLUG ) );

	}

	/**
	 * Return an array of product IDs that can be imported.
	 *
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @return array
	 */
	public function get_available_products() {

		if ( ! Plugin::is_setup() ) {

			return [];

		}

		$available = (array) Plugin::get_transient( 'products', [], function () {

			return rstore()->api->get( 'catalog/{pl_id}/products' );

		} );

		if ( empty( $available[0]->id ) ) {

			return [];

		}

		$available = wp_list_pluck( $available, 'id' );

		global $wpdb;

		$imported = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `meta_value` FROM {$wpdb->postmeta} as pm LEFT JOIN {$wpdb->posts} as p ON ( pm.`post_id` = p.`ID` ) WHERE p.`post_type` = %s AND pm.`meta_key` = %s;",
				Post_Type::SLUG,
				Plugin::prefix( 'id' )
			)
		);

		$missing = array_diff( $available, $imported );

		return ! empty( $missing ) ? $missing : [];

	}

}
