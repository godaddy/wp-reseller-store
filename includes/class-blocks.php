<?php
/**
 * GoDaddy Reseller Store product widget class.
 *
 * Handles the Reseller store product widget.
 *
 * @class    Reseller_Store/Blocks
 * @package  WP_Widget
 * @category Class
 * @author   GoDaddy
 * @since    2.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Blocks {

	/**
	 * Widgets args.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $args = array(
		'before_widget' => '',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		'after_widget'  => '</div>',
	);

	/**
	 * Class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

		add_filter( 'block_categories_all', array( $this, 'block_categories' ), 10, 2 );

		add_action(
			'init',
			function() {

				register_block_type(
					'reseller-store/product',
					array(
						'render_callback' => array(
							$this,
							'product',
						),
					)
				);

				register_block_type(
					'reseller-store/domain-search',
					array(
						'render_callback' => array(
							$this,
							'domain_search',
						),
					)
				);

			}
		);
	}

	/**
	 * Enqueue admin block styles.
	 *
	 * @action enqueue_block_editor_assets
	 */
	public function enqueue_block_editor_assets() {

		$suffix        = SCRIPT_DEBUG ? '' : '.min';
		$block_js_path = 'js/editor.blocks.min.js';

		wp_enqueue_script(
			'reseller-store-blocks-js',
			Plugin::assets_url( $block_js_path ),
			array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components' ),
			rstore()->version,
			true
		);

		wp_enqueue_style( 'reseller-store-blocks-css', Plugin::assets_url( "css/blocks-editor{$suffix}.css" ), array(), rstore()->version );

	}

	/**
	 * Enqueue admin block styles.
	 *
	 * @filter block_categories
	 * @param array $categories     Array of block categories.
	 * @return array
	 */
	public function block_categories( $categories ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'reseller-store',
					'title' => __( 'Reseller Store Modules', 'reseller-store' ),
				),
			)
		);
	}

	/**
	 * Render the product widget.
	 *
	 * @since 2.0.0
	 *
	 * @param array $atts        The block attributes.
	 *
	 * @return mixed Returns the HTML markup for the product container.
	 */
	public function product( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-product">';

		$product = new Widgets\Product();

		$result = $product->widget( $this->args, $atts );

		return $result;

	}

	/**
	 * Render the domain search simple widget.
	 *
	 * @since 2.0.0
	 *
	 * @param array $atts        The block attributes.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function domain_search( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-domain">';

		if ( isset( $atts['search_type'] ) && 'advanced' === $atts['search_type'] ) {

			$domain = new Widgets\Domain_Search();

		} elseif ( isset( $atts['search_type'] ) && 'transfer' === $atts['search_type'] ) {

			$domain = new Widgets\Domain_Transfer();

		} else {

			$domain = new Widgets\Domain_Simple();

		}

		$result = $domain->widget( $this->args, $atts );

		return $result;

	}

}
