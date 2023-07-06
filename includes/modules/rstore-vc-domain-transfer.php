<?php
/**
 * GoDaddy Reseller Store domain transfer module class.
 *
 * Handles the Reseller store domain transfer shortcode map for Visual Composer.
 *
 * @class    Reseller_Store/Modules/VCDomainTransfer
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

class VCDomainTransfer extends \WPBakeryShortCode {

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
				'name'        => __( 'Domain Transfer', 'reseller-store' ),
				'base'        => 'rstore_domain_transfer',
				'description' => __( 'A search form for domain transfers.', 'reseller-store' ),
				'category'    => __( 'Reseller Store', 'reseller-store' ),
				'params'      => array(

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'title-class',
						'heading'     => __( 'Title', 'reseller-store' ),
						'param_name'  => 'title',
						'description' => __( 'Optional widget title', 'reseller-store' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => 'Custom Group',
					),

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'text-class',
						'heading'     => __( 'Placeholder', 'reseller-store' ),
						'param_name'  => 'text_placeholder',
						'value'       => __( 'Enter domain to transfer', 'reseller-store' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => 'Custom Group',
					),

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'text-class',
						'heading'     => __( 'Button', 'reseller-store' ),
						'param_name'  => 'text_search',
						'value'       => __( 'Transfer', 'reseller-store' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => 'Custom Group',
					),

					array(
						'type'       => 'checkbox',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Display results in a new tab', 'reseller-store' ),
						'param_name' => 'new_tab',
						'value'      => array(
							__( 'Show', 'reseller-store' ) => 1,
						),
						'group'      => 'Results',
					),

				),
			)
		);

	}
}

new VCDomainTransfer();
