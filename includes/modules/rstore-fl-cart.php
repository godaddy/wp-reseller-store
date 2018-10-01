<?php
/**
 * GoDaddy Reseller Store cart module class.
 *
 * Handles the Reseller store cart module for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLCart
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

class FLCart extends \FLBuilderModule {

	/**
	 * @method __construct
	 * @since 1.6.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Cart Link', 'reseller-store' ),
				'description'     => __( 'A shopper cart status.', 'reseller-store' ),
				'category'        => __( 'Shopper Modules', 'reseller-store' ),
				'group'           => __( 'Reseller Store Modules', 'reseller-store' ),
				'icon'            => 'button.svg',
				'partial_refresh' => true,
			)
		);
	}
}

\FLBuilder::register_module(
	'\Reseller_Store\Modules\FLCart',
	array(
		'general' => array(
			'title'    => __( 'General', 'reseller-store' ),
			'sections' => array(
				'general' => array(
					'title'  => '',
					'fields' => array(
						'title'        => array(
							'type'  => 'text',
							'label' => __( 'Title', 'reseller-store' ),
						),
						'button_label' => array(
							'type'    => 'text',
							'label'   => __( 'Button', 'reseller-store' ),
							'default' => __( 'View Cart', 'reseller-store' ),
						),
					),
				),
			),
		),
	)
);
