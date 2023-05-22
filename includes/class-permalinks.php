<?php
/**
 * GoDaddy Reseller Store permalinks class.
 *
 * Handles the Reseller Store permalinks.
 *
 * @class    Reseller_Store/Permalinks
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Permalinks {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		if ( ! is_admin() ) {

			return;

		}

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

	}

	/**
	 * Register custom section and settings for permalinks.
	 *
	 * @action current_screen
	 * @since  0.2.0
	 */
	public function init() {

		add_settings_field(
			'rstore_category_base',
			esc_html__( 'Reseller category base', 'reseller-store' ),
			function () {

				printf( // xss ok.
					'<input name="rstore_category_base" type="text" class="regular-text code" value="%s" placeholder="%s">',
					( Taxonomy_Category::permalink_base() !== Taxonomy_Category::$default_permalink_base ) ? esc_attr( Taxonomy_Category::permalink_base() ) : '',
					esc_attr( Taxonomy_Category::$default_permalink_base )
				);

			},
			'permalink',
			'optional'
		);

		add_settings_field(
			'rstore_tag_base',
			esc_html__( 'Reseller tag base', 'reseller-store' ),
			function () {

				printf( // xss ok.
					'<input name="rstore_tag_base" type="text" class="regular-text code" value="%s" placeholder="%s">',
					( Taxonomy_Tag::permalink_base() !== Taxonomy_Tag::$default_permalink_base ) ? esc_attr( Taxonomy_Tag::permalink_base() ) : '',
					esc_attr( Taxonomy_Tag::$default_permalink_base )
				);

			},
			'permalink',
			'optional'
		);

		add_settings_section(
			'rstore-permalinks',
			esc_html__( 'Reseller Product Permalinks', 'reseller-store' ),
			array( $this, 'section' ),
			'permalink'
		);

		$this->save();

	}

	/**
	 * Display custom settings section.
	 *
	 * @since 0.2.0
	 */
	public function section() {

		printf(
			'<p>%s</p>',
			esc_html__( 'These settings control the permalinks used specifically for Reseller Store products.', 'reseller-store' )
		);

		$permalink_structure = get_option( 'permalink_structure' );

		$post_type = get_post_type_object( Post_Type::SLUG );

		$sample_product = sanitize_title( esc_html_x( 'sample-product', 'slug name', 'reseller-store' ) );

		$default_example = sprintf(
			'<code id="rstore-default-example" style="%s">%s</code>',
			( $permalink_structure ) ? 'display: none;' : '',
			esc_url( add_query_arg( $post_type->query_var, $sample_product, home_url() ) )
		);

		$custom_example = sprintf(
			'<code id="rstore-custom-example" style="%s">%s</code>',
			( $permalink_structure ) ? '' : 'display: none;',
			esc_url( home_url( sprintf( '%s/%s/', Post_Type::$default_permalink_base, $sample_product ) ) )
		);

		$is_default = ( Post_Type::permalink_base() === Post_Type::$default_permalink_base );

		?>
		<table class="form-table rstore-permalink-structure">
			<tbody>
				<tr>
					<th>
						<label>
							<input type="radio" name="rstore_permalink_structure" id="rstore-permalink-structure-default" value="<?php echo esc_attr( Post_Type::$default_permalink_base ); ?>" <?php checked( $is_default ); ?>>
							<?php esc_html_e( 'Default', 'reseller-store' ); ?>
						</label>
					</th>
					<td>
						<?php
							echo $default_example; // xss ok.
							echo $custom_example; // xss ok.
						?>
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<input type="radio" name="rstore_permalink_structure" id="rstore-permalink-structure-custom" value="" <?php checked( ! $is_default ); ?>>
							<?php esc_html_e( 'Custom base', 'reseller-store' ); ?>
						</label>
					</th>
					<td>
						<input type="text" name="rstore_product_base" id="rstore-product-base" class="regular-text code" value="<?php echo ! $is_default ? esc_attr( Post_Type::permalink_base() ) : ''; ?>" placeholder="<?php echo esc_attr( Post_Type::permalink_base() ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<?php

	}

	/**
	 * Save custom permalink settings.
	 *
	 * @since 0.2.0
	 */
	private function save() {

		if (
			false === wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), 'update-permalink' )
			||
			! isset( $_POST['permalink_structure'] ) // input var ok.
		) {

			return;

		}

		check_admin_referer( 'update-permalink' );

		$old_permalinks = (array) rstore_get_option( 'permalinks', array() );
		$new_permalinks = $old_permalinks;

		$new_permalinks['category_base'] = sanitize_title( filter_input( INPUT_POST, 'rstore_category_base', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$new_permalinks['tag_base']      = sanitize_title( filter_input( INPUT_POST, 'rstore_tag_base', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$new_permalinks['product_base']  = sanitize_title( filter_input( INPUT_POST, 'rstore_product_base', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );

		$old_structure = get_option( 'permalink_structure', '' );
		$new_structure = filter_input( INPUT_POST, 'permalink_structure', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $new_permalinks === $old_permalinks && $old_structure === $new_structure ) {

			return; // There is no change, do nothing.

		}

		$post_type = get_post_type_object( Post_Type::SLUG );

		$old_base = ( $old_permalinks['product_base'] ) ? $old_permalinks['product_base'] : Post_Type::$default_permalink_base;
		$new_base = ( $new_permalinks['product_base'] ) ? $new_permalinks['product_base'] : Post_Type::$default_permalink_base;

		$old_base_url = ( $old_structure ) ? home_url( trailingslashit( $old_base ) ) : add_query_arg( $post_type->query_var, '', home_url( '/' ) ) . '=';
		$new_base_url = ( $new_structure ) ? home_url( trailingslashit( $new_base ) ) : add_query_arg( $post_type->query_var, '', home_url( '/' ) ) . '=';

		if ( $old_base_url !== $new_base_url ) {

			// Update post content containing URLs of the old base.
			Embed::search_replace_post_content( $old_base_url, $new_base_url );

			// Flush the oEmbed cache when the product base changes.
			Embed::flush_cache();

		}

		rstore_update_option( 'permalinks', $new_permalinks );

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  0.2.0
	 */
	public function admin_enqueue_scripts() {

		if ( ! rstore_is_admin_uri( 'options-permalink.php' ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-admin-permalinks', Plugin::assets_url( "js/admin-permalinks{$suffix}.js" ), array( 'jquery' ), rstore()->version, true );

	}

}
