<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Permalinks {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		if ( ! is_admin() ) {

			return;

		}

		add_action( 'admin_init',            [ $this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

	}

	/**
	 * Register custom section and settings for permalinks.
	 *
	 * @action current_screen
	 * @since  NEXT
	 */
	public function init() {

		add_settings_field(
			'rstore_category_base',
			esc_html__( 'Reseller category base', 'reseller-store' ),
			function () {

				printf(
					'<input name="%s" type="text" class="regular-text code" value="%s" placeholder="%s">',
					'rstore_category_base',
					( Taxonomy_Category::permalink_base() !== Taxonomy_Category::$default_permalink_base ) ? Taxonomy_Category::permalink_base() : '',
					Taxonomy_Category::$default_permalink_base
				);

			},
			'permalink',
			'optional'
		);

		add_settings_field(
			'rstore_tag_base',
			esc_html__( 'Reseller tag base', 'reseller-store' ),
			function () {

				printf(
					'<input name="%s" type="text" class="regular-text code" value="%s" placeholder="%s">',
					'rstore_tag_base',
					( Taxonomy_Tag::permalink_base() !== Taxonomy_Tag::$default_permalink_base ) ? Taxonomy_Tag::permalink_base() : '',
					Taxonomy_Tag::$default_permalink_base
				);

			},
			'permalink',
			'optional'
		);

		add_settings_section(
			'rstore-permalinks',
			esc_html__( 'Reseller Product Permalinks', 'reseller-store' ),
			[ $this, 'section' ],
			'permalink'
		);

		$this->save();

	}

	/**
	 * Display custom settings section.
	 *
	 * @since NEXT
	 */
	public function section() {

		printf(
			'<p>%s</p>',
			esc_html__( 'These settings control the permalinks used specifically for Reseller Store products.', 'reseller-store' )
		);

		$permalink_structure = get_option( 'permalink_structure' );

		$sample_product = sanitize_title( esc_html_x( 'sample-product', 'slug name', 'reseller-store' ) );

		$default_example = sprintf(
			'<code id="rstore-default-example" style="%s">%s</code>',
			( $permalink_structure ) ? 'display: none;' : '',
			esc_url( add_query_arg( Post_Type::$default_permalink_base, $sample_product, home_url() ) )
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
						<?php echo $default_example; // xss ok ?>
						<?php echo $custom_example; // xss ok ?>
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<input type="radio" name="rstore_permalink_structure" id="rstore-permalink-structure-custom" value="" <?php checked( $is_default, false ); ?>>
							<?php _e( 'Custom base', 'reseller-store' ); ?>
						</label>
					</th>
					<td>
						<input type="text" name="rstore_product_base" id="rstore-product-base" class="regular-text code" value="<?php echo esc_attr( Post_Type::permalink_base() ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<?php

	}

	/**
	 * Save custom permalink settings.
	 *
	 * @since NEXT
	 */
	private function save() {

		if (
			! Plugin::is_admin_uri( 'options-permalink.php' )
			||
			! isset( $_POST['permalink_structure'] )
		) {

			return;

		}

		$permalinks  = (array) Plugin::get_option( 'permalinks', [] );
		$_permalinks = $permalinks;

		$_permalinks['category_base'] = sanitize_title( filter_input( INPUT_POST, 'rstore_category_base' ) );
		$_permalinks['tag_base']      = sanitize_title( filter_input( INPUT_POST, 'rstore_tag_base' ) );
		$_permalinks['product_base']  = sanitize_title( filter_input( INPUT_POST, 'rstore_product_base' ) );

		if ( $_permalinks === $permalinks ) {

			return; // There is no change, do nothing

		}

		// Flush the oEmbed cache when product permalinks change
		Embed::flush_cache();

		Plugin::update_option( 'permalinks', $_permalinks );

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  NEXT
	 */
	public function admin_enqueue_scripts() {

		if ( ! Plugin::is_admin_uri( 'options-permalink.php' ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-admin-permalinks', rstore()->assets_url . "js/admin-permalinks{$suffix}.js", [ 'jquery' ], rstore()->version, true );

	}

}
