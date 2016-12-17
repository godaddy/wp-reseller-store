<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Taxonomies {

	/**
	 * Category taxonomy slug.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const CAT_SLUG = 'reseller_product_category';

	/**
	 * Tag taxonomy slug.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const TAG_SLUG = 'reseller_product_tag';

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'category' ] );

		add_action( 'init', [ $this, 'tag' ] );

	}

	/**
	 * Register the category taxonomy.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function category() {

		$labels = [
			'name'              => esc_html_x( 'Categories', 'taxonomy general name', 'reseller-store' ),
			'singular_name'     => esc_html_x( 'Category', 'taxonomy singular name', 'reseller-store' ),
			'search_items'      => esc_html__( 'Search Categories', 'reseller-store' ),
			'all_items'         => esc_html__( 'All Categories', 'reseller-store' ),
			'parent_item'       => esc_html__( 'Parent Category', 'reseller-store' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'reseller-store' ),
			'edit_item'         => esc_html__( 'Edit Category', 'reseller-store' ),
			'update_item'       => esc_html__( 'Update Category', 'reseller-store' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'reseller-store' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'reseller-store' ),
			'menu_name'         => esc_html__( 'Categories', 'reseller-store' ),
		];

		$args = [
			'labels'            => $labels,
			'description'       => esc_html__( 'This is only a Test', 'reseller-store' ),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [
				'slug'         => self::CAT_SLUG,
				'with_front'   => false,
				'hierarchical' => true,
			],
		];

		register_taxonomy( self::CAT_SLUG, Post_Type::SLUG, $args );

	}

	/**
	 * Register the tag taxonomy.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function tag() {

		$labels = [
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
		];

		$args = [
			'labels'            => $labels,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [
				'slug'       => self::TAG_SLUG,
				'with_front' => false,
			],
		];

		register_taxonomy( self::TAG_SLUG, Post_Type::SLUG, $args );

	}

}
