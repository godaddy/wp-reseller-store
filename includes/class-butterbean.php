<?php
/**
 * GoDaddy Reseller Store ButterBean class.
 *
 * Handles the reseller store post metaboxes.
 *
 * @class    Reseller_Store/Butterbean
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class ButterBean {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'load' ) );
		add_action( 'butterbean_register', array( $this, 'register_types' ), 10, 2 );
		add_action( 'butterbean_register', array( $this, 'register_metabox' ), 10, 2 );

		add_filter( 'butterbean_pre_control_template', array( $this, 'control_templates' ), 10, 2 );

	}

	/**
	 * Load ButterBean.
	 *
	 * @action plugins_loaded
	 * @since  0.2.0
	 */
	public function load() {

		$path = Plugin::base_dir( 'lib/butterbean/butterbean.php' );

		if ( is_readable( $path ) ) {

			require_once $path;

		}

	}

	/**
	 * Register custom setting and control types.
	 *
	 * @action butterbean_register
	 * @since  0.2.0
	 *
	 * @param object $butterbean ButterBean object.
	 * @param string $post_type  Current post type.
	 */
	public function register_types( $butterbean, $post_type ) {

		if ( Post_Type::SLUG !== $post_type ) {

			return;

		}

		$butterbean->register_setting_type(
			rstore_prefix( 'read-only', true ),
			__NAMESPACE__ . '\ButterBean\Settings\Read_Only'
		);

		$butterbean->register_control_type(
			rstore_prefix( 'anchor', true ),
			__NAMESPACE__ . '\ButterBean\Controls\Anchor'
		);

		$butterbean->register_control_type(
			rstore_prefix( 'plain-text', true ),
			__NAMESPACE__ . '\ButterBean\Controls\Plain_Text'
		);

	}

	/**
	 * Register custom control templates.
	 *
	 * @filter butterbean_pre_control_template
	 * @since  0.2.0
	 *
	 * @param  string $path Path to ButterBean template file.
	 * @param  string $slug rstore_prefix slug.
	 *
	 * @return string Path to the ButterBean template file.
	 */
	public function control_templates( $path, $slug ) {

		switch ( $slug ) {

			case rstore_prefix( 'anchor', true ):
				$path = Plugin::base_dir( 'includes/butterbean/templates/control-anchor.php' );

				break;

			case rstore_prefix( 'plain-text', true ):
				$path = Plugin::base_dir( 'includes/butterbean/templates/control-plain-text.php' );

				break;

		}

		return $path;

	}

	/**
	 * Register custom metabox and sections.
	 *
	 * @action butterbean_register
	 * @since  0.2.0
	 *
	 * @param object $butterbean ButterBean instance.
	 * @param string $post_type  Current post type.
	 */
	public function register_metabox( $butterbean, $post_type ) {

		if ( Post_Type::SLUG !== $post_type ) {

			return;

		}

		$butterbean->register_manager(
			'product_options',
			array(
				'label'     => esc_html__( 'Product Options', 'reseller-store' ),
				'post_type' => Post_Type::SLUG,
				'context'   => 'normal',
				'priority'  => 'high',
			)
		);

		$manager = $butterbean->get_manager( 'product_options' );

		$manager->register_section(
			'general',
			array(
				'label' => esc_html__( 'General', 'reseller-store' ),
				'icon'  => 'dashicons-admin-tools',
			)
		);

		$this->list_price( $manager, 'general' );
		$this->sale_price( $manager, 'general' );
		$this->add_to_cart_button_label( $manager, 'general' );
		$this->cart_link_text( $manager, 'general' );
		$this->skip_cart_redirect( $manager, 'general' );

		$manager->register_section(
			'advanced',
			array(
				'label' => esc_html__( 'Advanced', 'reseller-store' ),
				'icon'  => 'dashicons-admin-settings',
			)
		);

		$this->reset_product_data( $manager, 'advanced' );

	}

	/**
	 * Register control and setting for List Price.
	 *
	 * @since 0.2.0
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function list_price( $manager, $section ) {

		$manager->register_control(
			rstore_prefix( 'listPrice' ),
			array(
				'type'    => rstore_prefix( 'plain-text', true ),
				'section' => $section,
				'label'   => esc_html__( 'Price', 'reseller-store' ),
			)
		);

		$manager->register_setting(
			rstore_prefix( 'listPrice' ),
			array(
				'type' => rstore_prefix( 'read-only', true ),
			)
		);

	}

	/**
	 * Register control and setting for Sale Price.
	 *
	 * @since 0.2.0
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function sale_price( $manager, $section ) {

		$manager->register_control(
			rstore_prefix( 'salePrice' ),
			array(
				'type'    => rstore_prefix( 'plain-text', true ),
				'section' => $section,
				'label'   => esc_html__( 'Sale Price', 'reseller-store' ),
				'default' => esc_html_x( 'N/A', 'abbreviation for not applicable', 'reseller-store' ),
			)
		);

		$manager->register_setting(
			rstore_prefix( 'salePrice' ),
			array(
				'type' => rstore_prefix( 'read-only', true ),
			)
		);

	}

	/**
	 * Register control and setting for Default Quantity.
	 *
	 * @since 0.2.0
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function default_quantity( $manager, $section ) {

		$manager->register_control(
			rstore_prefix( __FUNCTION__ ),
			array(
				'type'    => 'number',
				'section' => $section,
				'label'   => esc_html__( 'Default Quantity', 'reseller-store' ),
				'attr'    => array(
					'min'         => 1,
					'placeholder' => absint( rstore_get_option( __FUNCTION__, 1 ) ),
				),
			)
		);

		$manager->register_setting(
			rstore_prefix( __FUNCTION__ ),
			array(
				'sanitize_callback' => function ( $value ) {
					return ( 0 !== absint( $value ) ) ? absint( $value ) : null;
				},
			)
		);

	}

	/**
	 * Register control and setting for Add to Cart Button Label.
	 *
	 * @since 0.2.0
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function add_to_cart_button_label( $manager, $section ) {

		$manager->register_control(
			rstore_prefix( __FUNCTION__ ),
			array(
				'type'    => 'text',
				'section' => $section,
				'label'   => esc_html__( 'Add to Cart Button Label', 'reseller-store' ),
				'attr'    => array(
					'placeholder' => esc_attr( rstore_get_option( __FUNCTION__, esc_attr__( 'Add to cart', 'reseller-store' ) ) ),
				),
			)
		);

		$manager->register_setting(
			rstore_prefix( __FUNCTION__ ),
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

	}

	/**
	 * Register control and setting for Cart Link Text.
	 *
	 * @since 1.1.1
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function cart_link_text( $manager, $section ) {

		$manager->register_control(
			rstore_prefix( __FUNCTION__ ),
			array(
				'type'    => 'text',
				'section' => $section,
				'label'   => esc_html__( 'Cart link text', 'reseller-store' ),
				'attr'    => array(
					'placeholder' => esc_attr( rstore_get_option( __FUNCTION__, esc_attr__( 'Continue to cart', 'reseller-store' ) ) ),
				),
			)
		);

		$manager->register_setting(
			rstore_prefix( __FUNCTION__ ),
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

	}

	/**
	 * Register control and setting for Add to Cart Redirect.
	 *
	 * @since 0.2.0
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function skip_cart_redirect( $manager, $section ) {

		$args = array(
			'type'    => 'checkbox',
			'section' => $section,
			'label'   => esc_html__( 'Do not redirect to cart after adding product', 'reseller-store' ),
		);

		$manager->register_control( rstore_prefix( __FUNCTION__ ), $args );

		$manager->register_setting(
			rstore_prefix( __FUNCTION__ ),
			array(
				'sanitize_callback' => function ( $value ) {
					return ( 'true' === $value ) ? 'true' : '';
				},
			)
		);

	}

	/**
	 * Register control for Reset Product Data.
	 *
	 * @since 0.2.0
	 *
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $section The section to register the settings to.
	 */
	private function reset_product_data( $manager, $section ) {

		$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		$manager->register_control(
			rstore_prefix( __FUNCTION__ ),
			array(
				'type'        => rstore_prefix( 'anchor', true ),
				'section'     => $section,
				'label'       => esc_html__( 'Restore Product Data', 'reseller-store' ),
				'description' => esc_html__( 'Need to start over? You can restore the original product title, content, featured image, and category assignments. Note: Your customizations will be lost.', 'reseller-store' ),
			)
		);

	}

}
