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

		add_action( 'init',                       [ $this, 'redirects' ], 1 );
		add_action( 'admin_menu',                 [ $this, 'admin_submenu' ] );
		add_action( 'admin_head',                 [ $this, 'add_new_button' ] );
		add_action( 'wp_before_admin_bar_render', [ $this, 'admin_bar_submenu' ] );
		add_action( 'edit_form_advanced',         [ $this, 'new_post_screen' ] );

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

		if (
			$is_post_type_screen
			&&
			( ! Plugin::is_setup() || ! Plugin::has_products() )
		) {

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

		if ( ! Plugin::has_all_products() ) {

			return;

		}

		remove_submenu_page(
			sprintf( 'edit.php?post_type=%s', Post_Type::SLUG ),
			sprintf( 'post-new.php?post_type=%s', Post_Type::SLUG )
		);

	}

	/**
	 * Remove the `Add New` button from the Reseller Store edit screen.
	 *
	 * @action admin_head
	 * @since  NEXT
	 */
	public function add_new_button() {

		if ( ! Plugin::has_all_products() ) {

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

		if ( ! Plugin::has_all_products() ) {

			return;

		}

		global $wp_admin_bar;

		$wp_admin_bar->remove_node( sprintf( 'new-%s', Post_Type::SLUG ) );

	}

	/**
	 * Customize the new post screen when there are missing products.
	 *
	 * @action edit_form_advanced
	 * @since  NEXT
	 *
	 * @param WP_Post $post
	 */
	public function new_post_screen( $post ) {

		if ( Post_Type::SLUG !== $post->post_type || 'auto-draft' !== $post->post_status ) {

			return;

		}

		// Delete auto-draft after the page has loaded
		add_action( 'shutdown', function () use ( $post ) {

			wp_delete_post( $post->ID, true );

		} );

		$products = API::get_products();

		sort( $products );

		$missing = Plugin::get_missing_products();

		?>
		<style type="text/css">
		#post-body-content, #postbox-container-1, .postbox-container > div:not(.rstore-add-new) { display: none; }
		#poststuff #post-body.columns-2 { margin: 0; }
		.rstore-add-new th.manage-column.column-title { width: 50%; }
		</style>
		<script type="text/javascript">
		( function( $ ) {
			$( document ).ready( function( $ ) {
				var $cb = $( '.rstore-add-new .check-column input[type="checkbox"]' );
				$cb.not( ':disabled' ).prop( 'checked', true );
				$( '.rstore-add-new .check-column' ).on( 'change', 'input[type="checkbox"]', function() {
					var disabled = ( 0 === $cb.filter( ':checked' ).length );
					$( '.rstore-add-new button[type="submit"]' ).prop( 'disabled', disabled ).toggleClass( 'disabled', disabled );
				} );
			} );
		} )( jQuery );
		</script>
		<div class="rstore-add-new">
			<p><?php printf( esc_html__( '%d product(s) available for import' ), count( $missing ) ); ?></p>
			<form>
				<table class="wp-list-table widefat fixed striped rstore-product-list">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
							<th class="manage-column column-title column-primary"><?php esc_html_e( 'Product', 'reseller-store' ); ?></th>
							<th><?php esc_html_e( 'List Price', 'reseller-store' ); ?></th>
							<th><?php esc_html_e( 'Sale Price', 'reseller-store' ); ?></th>
						</tr>
					</thead>
					<tbody id="the-list">
					<?php foreach ( $products as $product ) : ?>
						<tr>
							<th scope="row" class="check-column"><input type="checkbox" id="select-<?php echo esc_attr( $product->id ); ?>" <?php disabled( ! in_array( $product->id, $missing ) ); ?>></th>
							<td class="title column-title"><label for="select-<?php echo esc_attr( $product->id ); ?>"><?php echo esc_html( $product->title ); ?></label></td>
							<td><?php echo esc_html( $product->listPrice ); ?></td>
							<td><?php echo ! empty( $product->salePrice ) ? esc_html( $product->salePrice ) : esc_html__( 'N/A', 'reseller-store' ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Import Selected', 'reseller-store' ); ?></button></p>
			</form>
		</div>
		<?php

	}

}
