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
 * @since    NEXT
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
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( ' Search', 'reseller-store' ),
			'description'   	=> __( 'Domain search form', 'reseller-store' ),
			'category'      	=> __( 'Domain Modules', 'reseller-store' ),
			'group'      	    => __( 'Reseller Store Modules', 'reseller-store' ),
			'icon'              => 'button.svg',
			'partial_refresh'	=> true,
		));
	}
}

\FLBuilder::register_module( '\Reseller_Store\Modules\FLDomainSearch', array(
	'general'      => array(
		'title'         => __( 'General', 'reseller-store' ),
		'sections'      => array(
			'general'  => array(
				'title'         => 'General Settings',
				'fields'        => array(
						'title'     => array(
						'type'          => 'text',
						'label'         => __( 'Title', 'reseller-store' ),
						'description'   => __( 'Optional widget title', 'reseller-store' ),
					),
					'text_placeholder'     => array(
						'type'          => 'text',
						'label'         => __( 'Placeholder Text', 'reseller-store' ),
						'default'		=> __( 'Enter domain to transfer', 'reseller-store' ),
					),
					'text_button'     => array(
						'type'          => 'text',
						'label'         => __( 'Search button label', 'reseller-store' ),
						'default'		=> __( 'Search', 'reseller-store' )
					)
				)
			),
			'results'  => array(
				'title'         => 'Results Settings',
				'fields'        => array(
					'page_size'     => array(
						'type'          => 'text',
						'label'         => __( 'Page size', 'reseller-store' ),
						'description'   => __( 'Number of suggested domains returned in results.', 'reseller-store' ),
					),
					'text_available'     => array(
						'type'          => 'text',
						'label'         => __( 'Available text', 'reseller-store' ),
						'default'		=> __( 'Enter domain to transfer', 'reseller-store' ),
					),
					'text_not_available'     => array(
						'type'          => 'text',
						'label'         => __( 'Not available text', 'reseller-store' ),
						'default'		=> __( 'Enter domain to transfer', 'reseller-store' ),
					),
					'text_cart'     => array(
						'type'          => 'text',
						'label'         => __( 'Cart button', 'reseller-store' ),
						'default'		=> __( 'Enter domain to transfer', 'reseller-store' ),
					),
					'text_selected'     => array(
						'type'          => 'text',
						'label'         => __( 'Selected button label', 'reseller-store' ),
						'default'		=> __( 'Transfer', 'reseller-store' )
					),
					'text_verify'     => array(
						'type'          => 'text',
						'label'         => __( 'Verify button label', 'reseller-store' ),
						'default'		=> __( 'Transfer', 'reseller-store' )
					),
					'text_disclaimer'     => array(
						'type'          => 'text',
						'label'         => __( 'Disclaimer', 'reseller-store' ),
						'description'   => __( 'Domain disclaimer notice.', 'reseller-store' ),
						'help'          => __( 'Disclaimer notice must be displayed for some regions and domains. Leave blank to display recommended disclaimer.', 'reseller-store' )

					)
				)
			)
		)
	)
) );
