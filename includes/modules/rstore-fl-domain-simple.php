<?php
/**
 * GoDaddy Reseller Store domain simple module class.
 *
 * Handles the Reseller store domain simple module for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLDomainSimple
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

class FLDomainSimple extends \FLBuilderModule {

	/**
	 * @method __construct
	 * @since 1.6.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Search', 'reseller-store' ),
				'description'     => __( 'A search form for domain registrations.', 'reseller-store' ),
				'category'        => __( 'Domain Modules', 'reseller-store' ),
				'group'           => __( 'Reseller Store Modules', 'reseller-store' ),
				'icon'            => 'button.svg',
				'partial_refresh' => true,
			)
		);
	}
}

\FLBuilder::register_module(
	'\Reseller_Store\Modules\FLDomainSimple',
	array(
		'general' => array(
			'title'    => __( 'General', 'reseller-store' ),
			'sections' => array(
				'general' => array(
					'title'  => '',
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
							'label'   => __( 'Button', 'reseller-store' ),
							'default' => __( 'Search', 'reseller-store' ),
						),
						'new_tab'     => array(
							'type'    => 'select',
							'label'   => __( 'Display results in a new tab', 'reseller-store' ),
							'default' => '0',
							'options' => array(
								'1' => __( 'Yes', 'reseller-store' ),
								'0' => __( 'No', 'reseller-store' ),
							),
						),
					),
				),
			),
		),
	)
);
