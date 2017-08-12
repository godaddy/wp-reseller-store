<?php
/**
 * GoDaddy Reseller Store tag class.
 *
 * Handles the Reseller store tag taxonomy.
 *
 * @class    Reseller_Store/Taxonomy_Tag
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Taxonomy_Tag {

	/**
	 * Taxonomy slug.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product_tag';

	/**
	 * Taxonomy default permalink base.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	public static $default_permalink_base;

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		self::$default_permalink_base = sanitize_title( esc_html_x( 'product-tag', 'slug name', 'reseller-store' ) );

		add_action( 'init', array( $this, 'register' ) );

	}

	/**
	 * Return the taxonomy custom permalink base.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public static function permalink_base() {

		$permalinks     = (array) rstore_get_option( 'permalinks', array() );
		$permalink_base = ! empty( $permalinks['tag_base'] ) ? $permalinks['tag_base'] : self::$default_permalink_base;

		return sanitize_title( $permalink_base );

	}

	/**
	 * Register the taxonomy.
	 *
	 * @action init
	 * @since  0.2.0
	 */
	public function register() {

		$labels = array(
			'name'              => esc_html_x( 'Tags', 'taxonomy general name', 'reseller-store' ),
			'singular_name'     => esc_html_x( 'Tag', 'taxonomy singular name', 'reseller-store' ),
			'search_items'      => esc_html__( 'Search Tags', 'reseller-store' ),
			'all_items'         => esc_html__( 'All Tags', 'reseller-store' ),
			'parent_item'       => esc_html__( 'Parent Tag', 'reseller-store' ),
			'parent_item_colon' => esc_html__( 'Parent Tag:', 'reseller-store' ),
			'edit_item'         => esc_html__( 'Edit Tag', 'reseller-store' ),
			'update_item'       => esc_html__( 'Update Tag', 'reseller-store' ),
			'add_new_item'      => esc_html__( 'Add New Tag', 'reseller-store' ),
			'new_item_name'     => esc_html__( 'New Tag Name', 'reseller-store' ),
			'menu_name'         => esc_html__( 'Tags', 'reseller-store' ),
		);

		$args = array(
			'label'             => esc_html__( 'Tags', 'reseller-store' ),
			'labels'            => $labels,
			'show_admin_column' => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'hierarchical'      => false,
			'rewrite'           => array(
				'slug'         => self::permalink_base(),
				'with_front'   => false,
				'hierarchical' => false,
			),
		);

		/**
		 * Filter the tag taxonomy args.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_product_tag_args', $args );

		register_taxonomy( self::SLUG, Post_Type::SLUG, $args );

	}

}
