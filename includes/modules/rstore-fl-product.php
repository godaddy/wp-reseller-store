<?php
/**
 * GoDaddy Reseller Store product module class.
 *
 * Handles the Reseller store product module for Beaver Builder.
 *
 * @class    Reseller_Store/Modules/FLProduct
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

class FLProduct extends \FLBuilderModule {

	/**
	 * @method __construct
	 * @since 1.6.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Product', 'reseller-store' ),
				'description'     => __( 'Display a product post.', 'reseller-store' ),
				'category'        => __( 'Product Modules', 'reseller-store' ),
				'group'           => __( 'Reseller Store Modules', 'reseller-store' ),
				'icon'            => 'slides.svg',
				'partial_refresh' => true,
			)
		);
	}
}

\FLBuilder::register_module(
	'\Reseller_Store\Modules\FLProduct',
	array(
		'general' => array(
			'title'    => __( 'General', 'reseller-store' ),
			'sections' => array(
				'general'  => array(
					'title'  => '',
					'fields' => array(
						'post_id' => array(
							'type'    => 'select',
							'label'   => __( 'Product', 'reseller-store' ),
							'options' => rstore_get_product_list(),
						),
					),
				),
				'display'  => array(
					'title'  => 'Display',
					'fields' => array(
						'image_size'     => array(
							'type'    => 'select',
							'label'   => __( 'Image Size', 'reseller-store' ),
							'default' => 'icon',
							'options' => array(
								'icon'      => __( 'Product Icon', 'reseller-store' ),
								'thumbnail' => __( 'Thumbnail', 'reseller-store' ),
								'medium'    => __( 'Medium resolution', 'reseller-store' ),
								'large'     => __( 'Large resolution', 'reseller-store' ),
								'full'      => __( 'Original resolution', 'reseller-store' ),
								'none'      => __( 'Hide image', 'reseller-store' ),
							),
						),
						'content_height' => array(
							'type'        => 'unit',
							'label'       => __( 'Content Height (set to 0 for full height)', 'reseller-store' ),
							'default'     => 250,
							'description' => 'px',
						),
						'button_label'   => array(
							'type'        => 'text',
							'label'       => __( 'Button', 'reseller-store' ),
							'description' => __( 'Leave blank to hide button', 'reseller-store' ),
							'default'     => __( 'Add to cart', 'reseller-store' ),
						),
						'button_new_tab'     => array(
							'type'    => 'select',
							'label'   => __( 'Open Results In A New Tab', 'reseller-store' ),
							'default' => '0',
							'options' => array(
								'1' => __( 'Yes', 'reseller-store' ),
								'0' => __( 'No', 'reseller-store' ),
							),
						),
						'show_title'     => array(
							'type'    => 'select',
							'label'   => __( 'Title', 'reseller-store' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Show', 'reseller-store' ),
								'0' => __( 'Hide', 'reseller-store' ),
							),
						),
						'show_content'   => array(
							'type'    => 'select',
							'label'   => __( 'Post Content', 'reseller-store' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Show', 'reseller-store' ),
								'0' => __( 'Hide', 'reseller-store' ),
							),
						),
						'show_price'     => array(
							'type'    => 'select',
							'label'   => __( 'Price', 'reseller-store' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Show', 'reseller-store' ),
								'0' => __( 'Hide', 'reseller-store' ),
							),
						),
						'text_more'      => array(
							'type'    => 'text',
							'label'   => __( 'Product Permalink', 'reseller-store' ),
							'default' => __( 'More info', 'reseller-store' ),
						),
						'layout_type'    => array(
							'type'    => 'select',
							'label'   => __( 'Layout type', 'reseller-store' ),
							'default' => 'default',
							'options' => array(
								'default' => __( 'Default', 'reseller-store' ),
								'classic' => __( 'Classic', 'reseller-store' ),
							),
						),
					),
				),
				'redirect' => array(
					'title'  => 'Redirect',
					'fields' => array(
						'redirect'  => array(
							'type'        => 'select',
							'description' => __( 'Redirect to cart after adding item', 'reseller-store' ),
							'default'     => '1',
							'options'     => array(
								'1' => __( 'Yes', 'reseller-store' ),
								'0' => __( 'No', 'reseller-store' ),
							),
							'toggle'      => array(
								'1' => array(
									'fields' => array( 'text_cart' ),
								),
								'0' => array(),
							),
						),
						'text_cart' => array(
							'type'    => 'text',
							'label'   => __( 'Cart Link', 'reseller-store' ),
							'default' => __( 'Continue to cart', 'reseller-store' ),
						),
					),
				),
			),
		),
	)
);
