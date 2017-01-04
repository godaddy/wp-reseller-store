<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class ButterBean {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'plugins_loaded',      [ $this, 'load' ] );
		add_action( 'butterbean_register', [ $this, 'register_types' ], 10, 2 );
		add_action( 'butterbean_register', [ $this, 'register_metabox' ], 10, 2 );

		add_filter( 'butterbean_pre_control_template', [ $this, 'plain_text_control_template' ], 10, 2 );

	}

	/**
	 * Load ButterBean.
	 *
	 * @action plugins_loaded
	 * @since  NEXT
	 */
	public function load() {

		$path = rstore()->base_dir . 'lib/butterbean/butterbean.php';

		if ( is_readable( $path ) ) {

			require_once $path;

		}

	}

	/**
	 * Register custom setting and control types.
	 *
	 * @action butterbean_register
	 * @since  NEXT
	 *
	 * @param ButterBean $butterbean
	 * @param string     $post_type
	 */
	public function register_types( $butterbean, $post_type ) {

		if ( Post_Type::SLUG !== $post_type ) {

			return;

		}

		$butterbean->register_setting_type(
			Plugin::prefix( 'read-only', true ),
			__NAMESPACE__ . '\ButterBean\Settings\Read_Only'
		);

		$butterbean->register_control_type(
			Plugin::prefix( 'plain-text', true ),
			__NAMESPACE__ . '\ButterBean\Controls\Plain_Text'
		);

	}

	/**
	 * Register the control template for Plain Text.
	 *
	 * @filter butterbean_pre_control_template
	 * @since  NEXT
	 *
	 * @param  string $path
	 * @param  string $slug
	 *
	 * @return string
	 */
	public function plain_text_control_template( $path, $slug ) {

		if ( Plugin::prefix( 'plain-text', true ) === $slug ) {

			$path = rstore()->base_dir . 'includes/butterbean/templates/control-plain-text.php';

		}

		return $path;

	}

	/**
	 * Register custom metabox and sections.
	 *
	 * @action butterbean_register
	 * @since  NEXT
	 *
	 * @param ButterBean $butterbean
	 * @param string     $post_type
	 */
	public function register_metabox( $butterbean, $post_type ) {

		if ( Post_Type::SLUG !== $post_type ) {

			return;

		}

		$butterbean->register_manager(
			'product_options',
			[
				'label'     => esc_html__( 'Product Options', 'reseller-store' ),
				'post_type' => Post_Type::SLUG,
				'context'   => 'normal',
				'priority'  => 'high',
			]
		);

		$manager = $butterbean->get_manager( 'product_options' );

		$manager->register_section(
			'general',
			[
				'label' => esc_html__( 'General', 'reseller-store' ),
				'icon'  => 'dashicons-admin-tools',
			]
		);

		$this->list_price( $manager, 'general' );
		$this->sale_price( $manager, 'general' );

		$manager->register_section(
			'advanced',
			[
				'label' => esc_html__( 'Advanced', 'reseller-store' ),
				'icon'  => 'dashicons-admin-settings',
			]
		);

		$this->default_quantity( $manager, 'advanced' );
		$this->add_to_cart_button_label( $manager, 'advanced' );
		$this->add_to_cart_redirect( $manager, 'advanced' );

	}

	/**
	 * Register control and setting for List Price.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 * @param string             $section
	 */
	private function list_price( $manager, $section ) {

		$manager->register_control(
			Plugin::prefix( __FUNCTION__ ),
			[
				'type'    => Plugin::prefix( 'plain-text', true ),
				'section' => $section,
				'label'   => esc_html__( 'Price', 'reseller-store' ),
			]
		);

		$manager->register_setting(
			Plugin::prefix( __FUNCTION__ ),
			[
				'type' => Plugin::prefix( 'read-only', true ),
			]
		);

	}

	/**
	 * Register control and setting for Sale Price.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 * @param string             $section
	 */
	private function sale_price( $manager, $section ) {

		$manager->register_control(
			Plugin::prefix( __FUNCTION__ ),
			[
				'type'    => Plugin::prefix( 'plain-text', true ),
				'section' => $section,
				'label'   => esc_html__( 'Sale Price', 'reseller-store' ),
				'default' => esc_html_x( 'N/A', 'abbreviation for not applicable', 'reseller-store' ),
			]
		);

		$manager->register_setting(
			Plugin::prefix( __FUNCTION__ ),
			[
				'type' => Plugin::prefix( 'read-only', true ),
			]
		);

	}

	/**
	 * Register control and setting for Default Quantity.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 * @param string             $section
	 */
	private function default_quantity( $manager, $section ) {

		$manager->register_control(
			Plugin::prefix( __FUNCTION__ ),
			[
				'type'    => 'number',
				'section' => $section,
				'label'   => esc_html__( 'Default Quantity', 'reseller-store' ),
				'attr'    => [
					'min'         => 1,
					'placeholder' => absint( Plugin::get_option( __FUNCTION__, 1 ) ),
				],
			]
		);

		$manager->register_setting(
			Plugin::prefix( __FUNCTION__ ),
			[
				'sanitize_callback' => function ( $value ) {
					return ( 0 !== absint( $value ) ) ? absint( $value ) : null;
				},
			]
		);

	}

	/**
	 * Register control and setting for Add to Cart Button Label.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 * @param string             $section
	 */
	private function add_to_cart_button_label( $manager, $section ) {

		$manager->register_control(
			Plugin::prefix( __FUNCTION__ ),
			[
				'type'    => 'text',
				'section' => $section,
				'label'   => esc_html__( 'Add to Cart Button Label', 'reseller-store' ),
				'attr'    => [
					'placeholder' => esc_attr( Plugin::get_option( __FUNCTION__, esc_attr__( 'Add to cart', 'reseller-store' ) ) ),
				],
			]
		);

		$manager->register_setting(
			Plugin::prefix( __FUNCTION__ ),
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

	}

	/**
	 * Register control and setting for Add to Cart Redirect.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 * @param string             $section
	 */
	private function add_to_cart_redirect( $manager, $section ) {

		$args = [
			'type'    => 'checkbox',
			'section' => $section,
			'label'   => esc_html__( 'Redirect to the cart immediately after adding', 'reseller-store' ),
		];

		// TODO: Decide which setting wins?
		if ( 1 === Plugin::get_option( __FUNCTION__ ) ) {

			$args['attr']['checked']  = 'checked';
			$args['attr']['disabled'] = 'disabled';

		}

		$manager->register_control( Plugin::prefix( __FUNCTION__ ), $args );

		$manager->register_setting(
			Plugin::prefix( __FUNCTION__ ),
			[
				'sanitize_callback' => function ( $value ) {
					return ( 'true' === $value ) ? 1 : 0;
				},
			]
		);

	}

}
