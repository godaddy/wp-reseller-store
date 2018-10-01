<?php
/**
 * GoDaddy Reseller Store domain search module class.
 *
 * Handles the Reseller store domain search widget for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLDomainSearch
 * @package  FLBuilderModule
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

class FLDomainSearch extends \FLBuilderModule {

	/**
	 * @method __construct
	 * @since 1.6.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Advanced Search', 'reseller-store' ),
				'description'     => __( 'An advanced search form with on page results for domain names.', 'reseller-store' ),
				'category'        => __( 'Domain Modules', 'reseller-store' ),
				'group'           => __( 'Reseller Store Modules', 'reseller-store' ),
				'icon'            => 'button.svg',
				'partial_refresh' => true,
			)
		);
	}
}

\FLBuilder::register_module(
	'\Reseller_Store\Modules\FLDomainSearch',
	array(
		'general' => array(
			'title'    => __( 'General', 'reseller-store' ),
			'sections' => array(
				'general' => array(
					'title'  => 'General Settings',
					'fields' => array(
						'title'            => array(
							'type'        => 'text',
							'label'       => __( 'Title', 'reseller-store' ),
							'description' => __( 'Optional widget title', 'reseller-store' ),
						),
						'text_placeholder' => array(
							'type'    => 'text',
							'label'   => __( 'Placeholder', 'reseller-store' ),
							'default' => __( 'Find your perfect domain name', 'reseller-store' ),
						),
						'text_search'      => array(
							'type'    => 'text',
							'label'   => __( 'Search Button', 'reseller-store' ),
							'default' => __( 'Search', 'reseller-store' ),
						),
					),
				),
				'results' => array(
					'title'  => 'Results Settings',
					'fields' => array(
						'page_size'          => array(
							'type'        => 'unit',
							'label'       => __( 'Page Size', 'reseller-store' ),
							'description' => __( 'domains', 'reseller-store' ),
							'default'     => 5,
						),
						'modal'              => array(
							'type'    => 'select',
							'label'   => __( 'Display results in a modal', 'reseller-store' ),
							'default' => '0',
							'options' => array(
								'1' => __( 'Show', 'reseller-store' ),
								'0' => __( 'Hide', 'reseller-store' ),
							),
						),
						'text_available'     => array(
							'type'    => 'text',
							'label'   => __( 'Available Text', 'reseller-store' ),
							'default' => __( 'Congrats, {domain_name} is available!', 'reseller-store' ),
						),
						'text_not_available' => array(
							'type'    => 'text',
							'label'   => __( 'Not Available Text', 'reseller-store' ),
							'default' => __( 'Sorry, {domain_name} is taken.', 'reseller-store' ),
						),
						'text_cart'          => array(
							'type'    => 'text',
							'label'   => __( 'Cart Button', 'reseller-store' ),
							'default' => __( 'Continue to cart', 'reseller-store' ),
						),
						'text_select'        => array(
							'type'    => 'text',
							'label'   => __( 'Select Button', 'reseller-store' ),
							'default' => __( 'Select', 'reseller-store' ),
						),
						'text_selected'      => array(
							'type'    => 'text',
							'label'   => __( 'Deselect Button', 'reseller-store' ),
							'default' => __( 'Selected', 'reseller-store' ),
						),
					),
				),
			),
		),
	)
);
