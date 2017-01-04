<?php

namespace Reseller_Store;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Product {

	/**
	 * Product object.
	 *
	 * @since NEXT
	 *
	 * @var stdClass
	 */
	private $product;

	/**
	 * Array of required properties and validation callbacks.
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	private $properties = [
		'id'         => 'strlen',
		'categories' => 'is_array',
		'image'      => 'strlen',
		'term'       => 'strlen',
		'listPrice'  => 'strlen',
		'title'      => 'strlen',
		'content'    => 'strlen',
	];

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 *
	 * @param stdClass $product
	 */
	public function __construct( $product ) {

		$this->product = $product;

	}

	/**
	 * Import the product.
	 *
	 * @since NEXT
	 *
	 * @param  int $post_id
	 *
	 * @return true|WP_Error
	 */
	public function import( $post_id = 0 ) {

		if ( ! $this->is_valid() ) {

			return new WP_Error( 'invalid_product', esc_html__( 'Invalid product: %s' ), json_encode( (array) $this->product ) );

		}

		$post_id = absint( $post_id );

		if ( ! $post_id && $this->exists() ) {

			return new WP_Error( 'product_exists', esc_html__( 'Product `%s` already exists.' ), $this->product->id );

		}

		$post_id = $this->insert_post( $post_id, true );

		if ( is_wp_error( $post_id ) ) {

			return $post_id;

		}

		$this->insert_post_meta( $post_id );

		$this->insert_categories( $this->product->categories, $post_id );

		$this->insert_attachment( $post_id );

		Plugin::mark_product_as_imported( $post_id, $this->product->id );

		return true;

	}

	/**
	 * Check if the product object is valid.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_valid() {

		if ( ! is_a( $this->product, 'stdClass' ) ) {

			return false;

		}

		foreach ( $this->properties as $property => $validator ) {

			if (
				property_exists( $this->product, $property )
				&&
				is_callable( $validator )
				&&
				$validator( $this->product->{$property} )
			) {

				return true;

			}

		}

		return false;

	}

	/**
	 * Check if a product has already been imported.
	 *
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @return bool
	 */
	public function exists() {

		global $wpdb;

		return (bool) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = %s AND `meta_value` = %s;",
				Plugin::prefix( 'id' ),
				$this->product->id
			)
		);

	}

	/**
	 * Check if an attachment URL has already been imported.
	 *
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @param  string $url
	 *
	 * @return int|false
	 */
	private function attachment_url_exists( $url ) {

		global $wpdb;

		$attachment_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` as p LEFT JOIN `{$wpdb->postmeta}` as pm ON ( p.`ID` = pm.`post_id` ) WHERE p.`post_type` = 'attachment' AND pm.`meta_key` = %s AND pm.`meta_value` = %s;",
				Plugin::prefix( 'image' ),
				$url
			)
		);

		return ( $attachment_id > 0 ) ? $attachment_id : false;

	}

	/**
	 * Import product as a product post.
	 *
	 * @since NEXT
	 *
	 * @param  int $post_id (optional)
	 *
	 * @return int|false
	 */
	private function insert_post( $post_id = 0 ) {

		if ( ! $post_id && Post_Type::SLUG !== get_post_type( $post_id ) ) {

			$post_id = 0;

		}

		$post_id = wp_insert_post(
			[
				'ID'           => absint( $post_id ),
				'post_type'    => Post_Type::SLUG,
				'post_status'  => 'publish',
				'post_title'   => $this->product->title,
				'post_name'    => sanitize_title( $this->product->title ),
				'post_content' => $this->product->content,
			]
		);

		if ( 0 === $post_id ) {

			return false;

		}

		return $post_id;

	}

	/**
	 * Add product meta as post meta.
	 *
	 * @since NEXT
	 *
	 * @param  int  $post_id
	 * @param  bool $replace (optional)
	 *
	 * @return int|bool
	 */
	private function insert_post_meta( $post_id, $replace = true ) {

		if ( $replace ) {

			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM `{$wpdb->postmeta}` WHERE `post_id` = %d;",
					$post_id
				)
			);

		}

		return Plugin::update_post_meta( $post_id, $this->product );

	}

	/**
	 * Import product categories as terms and preserve heirarchy.
	 *
	 * @since NEXT
	 *
	 * @param  int  $post_id
	 * @param  bool $replace (optional)
	 *
	 * @return array
	 */
	private function insert_categories( $post_id, $replace = true ) {

		if ( $replace ) {

			wp_delete_object_term_relationships( $post_id, Taxonomy_Category::SLUG );

		}

		return $this->create_categories( $this->product->categories, $post_id );

	}

	/**
	 * Import product categories as terms and preserve heirarchy.
	 *
	 * @since NEXT
	 *
	 * @param  array $categories
	 * @param  int   $post_id
	 * @param  int   $parent (optional)
	 *
	 * @return array
	 */
	private function create_categories( $categories, $post_id, $parent = 0 ) {

		$post_id = absint( $post_id );

		if ( ! $post_id ) {

			return [];

		}

		$term_ids = [];

		foreach ( $categories as $category ) {

			if ( is_string( $category ) ) {

				$term_ids[] = $this->create_category( $category, $post_id, $parent );

				continue;

			}

			foreach ( (array) $category as $index => $_categories ) {

				$parent = $this->create_category( $index, $post_id, $parent );

				$term_ids[] = $parent;

				$term_ids = array_merge( $term_ids, $this->create_categories( (array) $_categories, $post_id, $parent ) );

			}

		}

		return array_filter( $term_ids );

	}

	/**
	 * Import product category as a term.
	 *
	 * @since NEXT
	 *
	 * @param  string $name
	 * @param  int    $post_id
	 * @param  int    $parent (optional)
	 *
	 * @return int|false
	 */
	private function create_category( $name, $post_id, $parent = 0 ) {

		$term = term_exists( $name, Taxonomy_Category::SLUG );

		if ( ! is_array( $term ) ) {

			$term = wp_insert_term( $name, Taxonomy_Category::SLUG, [ 'parent' => (int) $parent ] );

		}

		if ( is_wp_error( $term ) ) {

			return false;

		}

		$term_id = isset( $term['term_id'] ) ? (int) $term['term_id'] : false;

		if ( $term_id ) {

			wp_set_object_terms( (int) $post_id, $term_id, Taxonomy_Category::SLUG, true );

		}

		return $term_id;

	}

	/**
	 * Import image from URL and set as product thumbnail.
	 *
	 * @since NEXT
	 *
	 * @param  int $post_id
	 *
	 * @return int|false
	 */
	private function insert_attachment( $post_id ) {

		$url = esc_url_raw( $this->product->image );

		$attachment_id = $this->attachment_url_exists( $url );
		$attachment_id = ( $attachment_id ) ? $attachment_id : $this->sideload_image( $url, $this->product->title );

		if ( ! $attachment_id ) {

			return false;

		}

		set_post_thumbnail( $post_id, $attachment_id );

		Plugin::update_post_meta(
			$attachment_id,
			[
				'id'      => $this->product->id,
				'image'   => $url,
				'post_id' => $post_id,
			]
		);

		return $attachment_id;

	}

	/**
	 * Sideload an image and return its attachment ID.
	 *
	 * @since NEXT
	 *
	 * @param  string $url
	 * @param  string $description (optional)
	 *
	 * @return int|false
	 */
	private function sideload_image( $url, $description = null ) {

		$file_array = [
			'name'     => basename( $url ),
			'tmp_name' => download_url( $url ),
		];

		$attachment_id = media_handle_sideload( $file_array, 0, $description );

		if ( is_wp_error( $attachment_id ) ) {

			@unlink( $file_array['tmp_name'] );

			return false;

		}

		return (int) $attachment_id;

	}

}
