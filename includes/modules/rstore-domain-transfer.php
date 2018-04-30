<?php
/**
 * GoDaddy Reseller Store domain transfer module class.
 *
 * Handles the Reseller store domain search module for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLDomainTransfer
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

class FLDomainTransfer extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( ' Transfer', 'reseller-store' ),
			'description'   	=> __( 'A search form for domain transfersbuttbuttasdfr', 'reseller-store' ),
			'category'      	=> __( 'Domain Modules', 'reseller-store' ),
			'group'      	    => __( 'Reseller Store Modules', 'reseller-store' ),
			'icon'              => 'button.svg',
			'partial_refresh'	=> true,
		));
	}
}

\FLBuilder::register_module( '\Reseller_Store\Modules\FLDomainTransfer', array(
	'general'      => array(
		'title'         => __( 'General', 'fl-builder' ),
		'sections'      => array(
			'general'  => array(
				'title'         => '',
				'fields'        => array(
					'text_placeholder'     => array(
						'type'          => 'text',
						'label'         => __( 'Placeholder Text', 'reseller-store' ),
						'default'		=> __( 'Enter domain to transfer', 'reseller-store' ),
					),
					'text_button'     => array(
						'type'          => 'text',
						'label'         => __( 'Button', 'reseller-store' ),
						'default'		=> __( 'Transfer', 'reseller-store' )
					)
				)
			)
		)
	)
) );
