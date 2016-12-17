<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Product_Category {

	/**
	 * Taxonomy slug.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product_category';

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'register' ] );

	}

	/**
	 * Register the taxonomy.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function register() {

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
				'slug'         => self::SLUG,
				'with_front'   => false,
				'hierarchical' => true,
			],
		];

		register_taxonomy( self::SLUG, Product::SLUG, $args );

	}

}
