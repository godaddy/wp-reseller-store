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
 * @since    NEXT
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
	 * @since NEXT
	 *
	 * @var array
	 */
	private $args = [
		'before_widget' => '',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		'after_widget'  => '</div>',
	];

	public function __construct() {

		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

 		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );

		add_filter( 'block_categories', [ $this, 'block_categories' ], 10, 2 );

		register_block_type( 'reseller-store/product', array(
			'render_callback' => [$this, 'product'] ));

		register_block_type( 'reseller-store/domain-search', array(
			'render_callback' => [$this, 'domain_search'] ));
 	}

 	/**
	 * Enqueue admin block styles.
	 *
	 * @action enqueue_block_editor_assets
	 */
	public function enqueue_block_editor_assets() {

		$block_path = 'js/editor.blocks.min.js';

		wp_enqueue_script(
			'rstore-blocks-js',
			Plugin::assets_url( $block_path ),
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components' ],
			rstore()->version, true
		);

 	}

	public function block_categories( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'reseller-store',
					'title' => __( 'Reseller Store Modules', 'reseller-store' ),
				),
			)
		);
	}

	/**
	 * Render the product widget.
	 *
	 * @since NEXT
	 *
	 * @param array $atts        The block attributes.
	 *
	 * @return mixed Returns the HTML markup for the product container.
	 */
	public function product($atts) {

		$this->args['before_widget'] = '<div class="widget rstore-product">';

		$product = new Widgets\Product();

		$result = $product->widget( $this->args, $atts );

		$output = str_replace(array("\r", "\n"), '', $result);

		return $output;

	}

	/**
	 * Render the domain search simple widget.
	 *
	 * @since 1.6.0
	 *
	 * @param array $atts        The block attributes.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function domain_search( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-domain">';



		if ( in_array ( "redirect", $atts ) && false === $atts["redirect"] ) {

			$domain = new Widgets\Domain_Search();

		}
		else
		{

			$domain = new Widgets\Domain_Simple();

		}

		$result = $domain->widget( $this->args, $atts );

		$output = str_replace(array("\r", "\n"), '', $result);

		return $output;

	}

}
