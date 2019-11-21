<?php
/**
 * GoDaddy Reseller Store shortcode class.
 *
 * Handles the GoDaddy Reseller Store shortcode processing.
 *
 * @class    Reseller_Store/Shortcodes
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.1.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Shortcodes {

	/**
	 * Widgets args.
	 *
	 * @since 1.1.0
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
	 * @since 1.1.0
	 */
	public function __construct() {

		/**
		 * Register the domain search shortcode
		 *
		 * @shortcode [rstore-domain-search]
		 *
		 * @since  1.0.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain search container.
		 */
		add_shortcode( 'rstore-domain-search', array( $this, 'domain_search' ) );

		/**
		 * Register the domain search shortcode
		 *
		 * @shortcode [rstore_domain_search]
		 *
		 * @since  1.1.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain search container.
		 */
		add_shortcode( 'rstore_domain_search', array( $this, 'domain_search' ) );

		/**
		 * Register the add to cart shortcode
		 *
		 * @shortcode [rstore_cart_button]
		 *
		 * @since  1.1.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the cart button
		 */
		add_shortcode( 'rstore_cart_button', array( $this, 'cart_button' ) );

		/**
		 * Register the add to product shortcode
		 *
		 * @shortcode [rstore_product]
		 *
		 * @since  1.1.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the product pod
		 */
		add_shortcode( 'rstore_product', array( $this, 'product' ) );

		/**
		 * Register the login shortcode
		 *
		 * @shortcode [rstore_login]
		 *
		 * @since  1.1.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the product pod
		 */
		add_shortcode( 'rstore_login', array( $this, 'login' ) );

		/**
		 * Register the domain transfer shortcode
		 *
		 * @shortcode [rstore_domain_transfer]
		 *
		 * @since 1.6.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain transfer container.
		 */
		add_shortcode( 'rstore_domain_transfer', array( $this, 'domain_transfer' ) );

		/**
		 * Register the domain search simple shortcode
		 *
		 * @shortcode [rstore_domain]
		 *
		 * @since 1.6.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain transfer container.
		 */
		add_shortcode( 'rstore_domain', array( $this, 'domain_simple' ) );

		/**
		 * Register the product icon shortcode
		 *
		 * @shortcode [rstore_icon]
		 *
		 * @since 2.0.4
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain transfer container.
		 */
		add_shortcode( 'rstore_icon', array( $this, 'product_icon' ) );

	}

	/**
	 * Render the domain search widget.
	 *
	 * @since 1.1.0
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the domain search container.
	 */
	public function domain_search( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-domain">';

		$domain = new Widgets\Domain_Search();

		return $domain->widget( $this->args, $atts );

	}

	/**
	 * Render the cart button widget.
	 *
	 * @since 1.1.0
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the cart button container.
	 */
	public function cart_button( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-cart">';

		$cart = new Widgets\Cart();

		return $cart->widget( $this->args, $atts );

	}

	/**
	 * Render the product widget.
	 *
	 * @since 1.1.0
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the product container.
	 */
	public function product( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-product">';

		$product = new Widgets\Product();

		return $product->widget( $this->args, $atts );

	}

	/**
	 * Render the login widget.
	 *
	 * @since 1.1.0
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the login container.
	 */
	public function login( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-login">';

		$login = new Widgets\Login();

		return $login->widget( $this->args, $atts );

	}

	/**
	 * Render the domain transfer widget.
	 *
	 * @since 1.6.0
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function domain_transfer( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-domain-transfer">';

		$domain = new Widgets\Domain_Transfer();

		return $domain->widget( $this->args, $atts );

	}

	/**
	 * Render the domain search simple widget.
	 *
	 * @since 1.6.0
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function domain_simple( $atts ) {

		$this->args['before_widget'] = '<div class="widget rstore-domain">';

		$domain = new Widgets\Domain_Simple();

		return $domain->widget( $this->args, $atts );

	}

	/**
	 * Render a product icon.
	 *
	 * @since 2.0.4
	 *
	 * @param array $atts        The shortcode attributes.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function product_icon( $atts ) {

		$class_name = isset( $atts['class'] ) ? $atts['class'] : '';

		if ( isset( $atts['post_id'] ) ) {

			$post_id = $atts['post_id'];

			$product = get_post( $post_id );

			if ( null === $product || 'publish' !== $product->post_status ||
				\Reseller_Store\Post_Type::SLUG !== $product->post_type ) {

				return esc_html__( 'Post id is not valid.', 'reseller-store' );

			}

			return Product_Icons::get_product_icon( $product, 'icon', $class_name );

		} else {

			$icon = isset( $atts['icon'] ) ? $atts['icon'] : 'default';

			return Product_Icons::get_icon( $icon, $class_name );

		}
	}
}
