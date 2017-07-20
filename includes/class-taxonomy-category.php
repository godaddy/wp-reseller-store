<?php
/**
 * GoDaddy Reseller Store category class.
 *
 * Handles the Reseller store category taxonomy.
 *
 * @class    Reseller_Store/Taxonomy_Category
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    NEXT
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Taxonomy_Category {

	/**
	 * Taxonomy slug.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product_category';

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

		self::$default_permalink_base = sanitize_title( esc_html_x( 'product-category', 'slug name', 'godaddy-reseller-store' ) );

		add_action( 'init', [ $this, 'register' ] );

	}

	/**
	 * Return the taxonomy custom permalink base.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public static function permalink_base() {

		$permalinks     = (array) rstore_get_option( 'permalinks', [] );
		$permalink_base = ! empty( $permalinks['category_base'] ) ? $permalinks['category_base'] : self::$default_permalink_base;

		return sanitize_title( $permalink_base );

	}

	/**
	 * Register the taxonomy.
	 *
	 * @action init
	 * @since  0.2.0
	 */
	public function register() {

		$labels = [
			'name'              => esc_html_x( 'Categories', 'taxonomy general name', 'godaddy-reseller-store' ),
			'singular_name'     => esc_html_x( 'Category', 'taxonomy singular name', 'godaddy-reseller-store' ),
			'search_items'      => esc_html__( 'Search Categories', 'godaddy-reseller-store' ),
			'all_items'         => esc_html__( 'All Categories', 'godaddy-reseller-store' ),
			'parent_item'       => esc_html__( 'Parent Category', 'godaddy-reseller-store' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'godaddy-reseller-store' ),
			'edit_item'         => esc_html__( 'Edit Category', 'godaddy-reseller-store' ),
			'update_item'       => esc_html__( 'Update Category', 'godaddy-reseller-store' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'godaddy-reseller-store' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'godaddy-reseller-store' ),
			'menu_name'         => esc_html__( 'Categories', 'godaddy-reseller-store' ),
		];

		$args = [
			'label'             => esc_html__( 'Categories', 'godaddy-reseller-store' ),
			'labels'            => $labels,
			'show_admin_column' => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'hierarchical'      => true,
			'rewrite'           => [
				'slug'         => self::permalink_base(),
				'with_front'   => false,
				'hierarchical' => true,
			],
		];

		/**
		 * Filter the category taxonomy args.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_product_category_args', $args );

		register_taxonomy( self::SLUG, Post_Type::SLUG, $args );

	}

}
