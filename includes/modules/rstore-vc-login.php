<?php
/**
 * GoDaddy Reseller Store login module class.
 *
 * Handles the Reseller store login shortcode map for Visual Composer.
 *
 * @class    Reseller_Store/Modules/VCLogin
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

class VCLogin extends \WPBakeryShortCode {

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
				'name'        => __( 'Shopper Login', 'reseller-store' ),
				'base'        => 'rstore_login',
				'description' => __( 'A shopper login status.', 'reseller-store' ),
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
						'heading'    => __( 'Sign In Button', 'reseller-store' ),
						'param_name' => 'login_button_text',
						'value'      => __( 'Sign In', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Welcome Message', 'reseller-store' ),
						'param_name' => 'welcome_message',
						'value'      => __( 'Welcome Back', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Log Out Button', 'reseller-store' ),
						'param_name' => 'logout_button_text',
						'value'      => __( 'Log Out', 'reseller-store' ),
					),

				),
			)
		);

	}
}

new VCLogin();
