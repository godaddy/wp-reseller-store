<?php
/**
 * GoDaddy Reseller Store domain transfer module class.
 *
 * Handles the Reseller store domain search module for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLProduct
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

class FLProduct extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Product', 'reseller-store' ),
			'description'   	=> __( 'Display product post', 'reseller-store' ),
			'category'      	=> __( 'Product Modules', 'reseller-store' ),
			'group'      	    => __( 'Reseller Store Modules', 'reseller-store' ),
			'icon'              => 'slides.svg',
			'partial_refresh'	=> true,
		));
	}
}

\FLBuilder::register_module( '\Reseller_Store\Modules\FLProduct', array(
	'general'      => array(
		'title'         => __( 'General', 'fl-builder' ),
		'sections'      => array(
			'general'  => array(
				'title'         => '',
				'fields'        => array(
					'post_id'     => array(
						'type'          => 'select',
						'label'         => __( 'Product', 'fl-builder' ),
						'options'       => rstore_get_product_list(),
					)
				)
			),
			'labels'  => array(
				'title'         => 'Labels',
				'fields'        => array(
					'button_label'     => array(
						'type'          => 'text',
						'label'         => __( 'Button', 'reseller-store' ),
						'description'   => __('Leave blank to hide button', 'reseller-store'),
						'default'		=> __( 'Add to cart', 'reseller-store' )
					),
					'text_cart'     => array(
						'type'          => 'text',
						'label'         => __( 'Button', 'reseller-store' ),
						'default'		=> __( 'Continue to cart', 'reseller-store' )
					)

				)
			),
			'display'  => array(
				'title'         => 'Display',
				'fields'        => array(
					'show_title'     => array(
						'type'          => 'select',
						'label'         => __( 'Title', 'fl-builder' ),
						'default'       => '1',
						'options'       => array(
							'1'      => __( 'Show', 'fl-builder' ),
							'0'     => __( 'Hide', 'fl-builder' )
						),
					),
					'show_content'     => array(
						'type'          => 'select',
						'label'         => __( 'Post Content', 'fl-builder' ),
						'default'       => '1',
						'options'       => array(
							'1'      => __( 'Show', 'fl-builder' ),
							'0'     => __( 'Hide', 'fl-builder' )
						),
					),
					'show_price'     => array(
						'type'          => 'select',
						'label'         => __( 'Price', 'fl-builder' ),
						'default'       => '1',
						'options'       => array(
							'1'      => __( 'Show', 'fl-builder' ),
							'0'      => __( 'Hide', 'fl-builder' )
						),
					),
					'image_size'     => array(
						'type'          => 'select',
						'label'         => __( 'Image Size', 'fl-builder' ),
						'default'       => 'thumbnail',
						'options'       => array(
							'thumbnail'   => __( 'Thumbnail', 'fl-builder' ),
							'medium'      => __( 'Medium', 'fl-builder' ),
							'large'       => __( 'Large', 'fl-builder' ),
							'full'        => __( 'Full', 'fl-builder' ),
							'none'        => __( 'None', 'fl-builder' ),

						),
					),
					'redirect'     => array(
						'type'          => 'select',
						'label'         => __( 'Redirect', 'fl-builder' ),
						'description'   => __( 'Redirect to cart after adding item' , 'reseller-store' ),
						'default'       => '1',
						'options'       => array(
							'1'         => __( 'Yes', 'fl-builder' ),
							'0'        => __( 'No', 'fl-builder' )
						),
					),
				)
			),
		)
	)
) );
