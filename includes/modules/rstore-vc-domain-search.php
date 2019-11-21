<?php
/**
 * GoDaddy Reseller Store domain search module class.
 *
 * Handles the Reseller store domain search shortcode map for Visual Composer.
 *
 * @class    Reseller_Store/Modules/VCDomainSearch
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

class VCDomainSearch extends \WPBakeryShortCode {

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
				'name'        => __( 'Advanced Domain Search', 'reseller-store' ),
				'base'        => 'rstore_domain_search',
				'description' => __( 'An advanced search form with on page results for domain names.', 'reseller-store' ),
				'category'    => __( 'Reseller Store', 'reseller-store' ),
				'params'      => array(

					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'title-class',
						'heading'     => __( 'Title', 'reseller-store' ),
						'param_name'  => 'title',
						'description' => __( 'Optional widget title', 'reseller-store' ),
						'group'       => __( 'General', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Placeholder', 'reseller-store' ),
						'param_name' => 'text_placeholder',
						'value'      => __( 'Find your perfect domain name', 'reseller-store' ),
						'group'      => __( 'General', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Button', 'reseller-store' ),
						'param_name' => 'text_search',
						'value'      => __( 'Search', 'reseller-store' ),
						'group'      => __( 'General', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Page Size', 'reseller-store' ),
						'param_name' => 'page_size',
						'value'      => 5,
						'group'      => __( 'Results', 'reseller-store' ),
					),

					array(
						'type'       => 'checkbox',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Display results in a modal', 'reseller-store' ),
						'param_name' => 'modal',
						'value'      => array(
							__( 'Show', 'reseller-store' ) => 1,
						),
						'group'      => 'Results',
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Available Text', 'reseller-store' ),
						'param_name' => 'text_available',
						'value'      => __( 'Congrats, {domain_name} is available!', 'reseller-store' ),
						'group'      => __( 'Results', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Not Available Text', 'reseller-store' ),
						'param_name' => 'text_not_available',
						'value'      => __( 'Sorry, {domain_name} is taken.', 'reseller-store' ),
						'group'      => __( 'Results', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Cart Button', 'reseller-store' ),
						'param_name' => 'text_cart',
						'value'      => __( 'Continue to cart', 'reseller-store' ),
						'group'      => __( 'Results', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Select Button', 'reseller-store' ),
						'param_name' => 'text_select',
						'value'      => __( 'Select', 'reseller-store' ),
						'group'      => __( 'Results', 'reseller-store' ),
					),

					array(
						'type'       => 'textfield',
						'holder'     => 'div',
						'class'      => 'text-class',
						'heading'    => __( 'Deselect Button', 'reseller-store' ),
						'param_name' => 'text_selected',
						'value'      => __( 'Selected', 'reseller-store' ),
						'group'      => __( 'Results', 'reseller-store' ),
					),

				),
			)
		);

	}
}

new VCDomainSearch();
