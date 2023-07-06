<?php
/**
 * GoDaddy Reseller Store domain simple module class.
 *
 * Handles the Reseller store domain simple shortcode map for Visual Composer.
 *
 * @class    Reseller_Store/Modules/VCDomainSimple
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

class VCDomainSimple extends \WPBakeryShortCode {

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
				'name'        => __( 'Domain Search', 'reseller-store' ),
				'base'        => 'rstore_domain',
				'description' => __( 'A search form for domain registrations.', 'reseller-store' ),
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
					),

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'text-class',
						'heading'     => __( 'Placeholder', 'reseller-store' ),
						'param_name'  => 'text_placeholder',
						'value'       => __( 'Find your perfect domain name', 'reseller-store' ),
						'admin_label' => false,
						'weight'      => 0,
					),

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'text-class',
						'heading'     => __( 'Button', 'reseller-store' ),
						'param_name'  => 'text_search',
						'value'       => __( 'Search', 'reseller-store' ),
						'admin_label' => false,
						'weight'      => 0,
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

new VCDomainSimple();
