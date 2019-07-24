<?php
/**
 * GoDaddy Reseller Store cart module class.
 *
 * Handles the Reseller store cart shortcode map for Visual Composer.
 *
 * @class    Reseller_Store/Modules/VCCart
 * @package  WPBakeryShortCode
 * @category Class
 * @author   GoDaddy
 * @since    1.6.0
 */

namespace Reseller_Store\Modules;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

class VCCart extends \WPBakeryShortCode {

	/**
	 * @method __construct
	 * @since 1.6.0
	 */
	function __construct() {
		$this->vc_mapping();
	}

	/**
	 * Visual composer shortcode mapping implementation.
	 *
	 * @method vc_mapping
	 *
	 * @since 1.6.0`
	 */
	public function vc_mapping() {

		vc_map(
			array(
				'name'        => __( 'Cart Link', 'reseller-store' ),
				'base'        => 'rstore_cart_button',
				'description' => __( 'A shopper cart status.', 'reseller-store' ),
				'category'    => __( 'Reseller Store', 'reseller-store' ),
				'params'      => array(

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'title-class',
						'heading'     => __( 'Title', 'reseller-store' ),
						'param_name'  => 'title',
						'description' => __( 'Optional widget title', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Button', 'reseller-store' ),
						'param_name' => 'button_label',
						'value'      => __( 'View Cart', 'reseller-store' ),
					),

				),
			)
		);

	}
}

new VCCart();
