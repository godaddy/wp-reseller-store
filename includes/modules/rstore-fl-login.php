<?php
/**
 * GoDaddy Reseller Store login module class.
 *
 * Handles the Reseller store login module for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLLogin
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

class FLLogin extends \FLBuilderModule {

	/**
	 * @method __construct
	 * @since 1.6.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Shopper Login', 'reseller-store' ),
				'description'     => __( 'A shopper login status.', 'reseller-store' ),
				'category'        => __( 'Shopper Modules', 'reseller-store' ),
				'group'           => __( 'Reseller Store Modules', 'reseller-store' ),
				'icon'            => 'star-filled.svg',
				'partial_refresh' => true,
			)
		);
	}
}

\FLBuilder::register_module(
	'\Reseller_Store\Modules\FLLogin',
	array(
		'general' => array(
			'title'    => __( 'General', 'reseller-store' ),
			'sections' => array(
				'general' => array(
					'title'  => '',
					'fields' => array(
						'title'              => array(
							'type'  => 'text',
							'label' => __( 'Title', 'reseller-store' ),
						),
						'login_button_text'  => array(
							'type'    => 'text',
							'label'   => __( 'Sign In Button', 'reseller-store' ),
							'default' => __( 'Sign In', 'reseller-store' ),
						),
						'welcome_message'    => array(
							'type'    => 'text',
							'label'   => __( 'Welcome Message', 'reseller-store' ),
							'default' => __( 'Welcome Back', 'reseller-store' ),
						),
						'logout_button_text' => array(
							'type'    => 'text',
							'label'   => __( 'Log Out Button', 'reseller-store' ),
							'default' => __( 'Log Out', 'reseller-store' ),
						),
					),
				),
			),
		),
	)
);
