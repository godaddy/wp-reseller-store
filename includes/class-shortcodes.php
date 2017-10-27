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
 * @since    NEXT
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Shortcodes {

	/**
	 * Widgets args.
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	private $args = [
		'before_widget' => '',
		'before_title' => '',
		'after_title' => '',
		'after_widget' => '',
	];

	/**
	 * Class constructor.
	 *
	 * @since NEXT
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
		add_shortcode( 'rstore-domain-search', [ $this, 'domain_search' ] );

		/**
		 * Register the domain search shortcode
		 *
		 * @shortcode [rstore_domain_search]
		 *
		 * @since  NEXT
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain search container.
		 */
		add_shortcode( 'rstore_domain_search', [ $this, 'domain_search' ] );

		 /**
		 * Register the add to cart shortcode
		 *
		 * @shortcode [rstore_cart_button]
		 *
		 * @since  NEXT
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the cart button
		 */
		add_shortcode( 'rstore_cart_button', [ $this, 'cart_button' ] );

		 /**
		 * Register the add to product shortcode
		 *
		 * @shortcode [rstore_product]
		 *
		 * @since  NEXT
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the product pod
		 */
		add_shortcode( 'rstore_product', [ $this, 'product' ] );

		 /**
		 * Register the login shortcode
		 *
		 * @shortcode [rstore_login]
		 *
		 * @since  NEXT
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the product pod
		 */
		add_shortcode( 'rstore_login', [ $this, 'login' ] );

	}

	/**
	 * Render the domain search widget.
	 *
	 * @since NEXT
	 *
	 * @param array $atts        The shortcode attributes.
	 */
	public function domain_search( $atts ) {

		$domain = new Widgets\Domain_Search();

		$domain->widget( $this->args, $atts );

	}

	/**
	 * Render the cart button widget.
	 *
	 * @since NEXT
	 *
	 * @param array $atts        The shortcode attributes.
	 */
	public function cart_button( $atts ) {

		$cart = new Widgets\Cart();

		$cart->widget( $this->args, $atts );

	}

	/**
	 * Render the domain search widget.
	 *
	 * @since NEXT
	 *
	 * @param array $atts        The shortcode attributes.
	 */
	public function product( $atts ) {

		$product = new Widgets\Product();

		$product->widget( $this->args, $atts );

	}

	/**
	 * Render the login widget.
	 *
	 * @since NEXT
	 *
	 * @param array $atts        The shortcode attributes.
	 */
	public function login( $atts ) {

		$login = new Widgets\Login();

		$login->widget( $this->args, $atts );

	}

}
