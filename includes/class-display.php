<?php
/**
 * GoDaddy Reseller Store display class.
 *
 * Handles the GoDaddy Reseller Store apperance.
 *
 * @class    Reseller_Store/Display
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Display {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_action( 'enqueue_embed_scripts', array( $this, 'wp_enqueue_scripts' ) );

		/**
		 * Register the domain search shortcode
		 *
		 * @shortcode [rstore-domain-search]
		 *
		 * @since  0.2.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain search container.
		 */
		add_shortcode( 'rstore-domain-search', function( $atts ) {

			return wp_kses_post( '<div class="rstore-domain-search"></div>' );

		} );

		/**
		 * Register the add to cart shortcode
		 *
		 * @shortcode [rstore-cart-button]
		 *
		 * @since  0.2.0
		 *
		 * @param  array $atts Defualt shortcode parameters.
		 *
		 * @return mixed Returns the HTML markup for the domain search container.
		 */
		add_shortcode( 'rstore-cart-button', function( $atts ) {

			return wp_kses_post( '<div class="rstore-domain-search"></div>' );

		} );

	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @action wp_enqueue_scripts
	 * @since  0.2.0
	 */
	public function wp_enqueue_scripts() {

		$rtl = is_rtl() ? '-rtl' : '';

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'rstore', Plugin::assets_url( "css/store{$rtl}{$suffix}.css" ), array( 'dashicons' ), rstore()->version );

		wp_enqueue_script( 'js-cookie', Plugin::assets_url( "js/js-cookie{$suffix}.js" ), array(), '2.1.3', true );
		wp_enqueue_script( 'rstore', Plugin::assets_url( "js/store{$suffix}.js" ), array( 'jquery', 'js-cookie' ), rstore()->version, true );
		wp_enqueue_script( 'rstore-domain', Plugin::assets_url( 'js/domain-search.min.js' ), array( 'jquery', 'js-cookie' ), rstore()->version, true );

		/**
		 * Filter the TTL for cookies (in seconds).
		 *
		 * @since 0.2.0
		 *
		 * @var int
		 */
		$cookie_ttl = (int) apply_filters( 'rstore_cookie_ttl', DAY_IN_SECONDS * 30 );

		$data = array(
			'pl_id'   => (int) rstore_get_option( 'pl_id' ),
			'urls'    => array(
				'cart'     => rstore()->api->urls['cart'],
				'cart_api' => esc_url_raw( rstore()->api->url( 'cart/{pl_id}' ) ),
				'domain_api' => rstore()->api->url( 'domains/{pl_id}' ),
			),
			'cookies' => array(
				'ttl'       => absint( $cookie_ttl ) * 1000, // Convert seconds to ms.
				'cartCount' => rstore_prefix( 'cart-count', true ),
			),
			'product' => array(
				'id' => ( Post_Type::SLUG === get_post_type() ) ? rstore_get_product_meta( get_the_ID(), 'id', '' ) : '',
			),
			'i18n'    => array(
				'add_to_cart'   => esc_html__( 'Add to cart', 'reseller-store' ),
				'available'     => esc_html__( 'Congrats, your domain is available!', 'reseller-store' ),
				'not_available' => esc_html__( 'Sorry that domain is taken', 'reseller-store' ),
				'placeholder'   => esc_html__( 'Find your perfect domain name', 'reseller-store' ),
				'view_cart'     => esc_html__( 'View cart', 'reseller-store' ),
				'error'         => esc_html__( 'An error has occurred', 'reseller-store' ),

			),
		);

		wp_localize_script( 'rstore', 'rstore', $data );

	}
}
