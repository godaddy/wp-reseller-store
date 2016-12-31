<?php

namespace Reseller_Store;

use \stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Import {

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
	private $required = [
		'id'         => 'strlen',
		'categories' => 'is_array',
		'title'      => 'strlen',
		'content'    => 'strlen',
		'image'      => 'strlen',
		'term'       => 'strlen',
		'listPrice'  => 'strlen',
		'salePrice'  => 'strlen',
	];

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 *
	 * @param stdClass $product
	 */
	public function __construct( stdClass $product ) {

		$this->product = $product;

	}

	/**
	 * Check if the product object is valid.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_valid_product() {

		foreach ( $this->required as $property => $callback ) {

			if (
				isset( $this->product->$property )
				&&
				is_callable( $callback )
				&&
				$callback( $this->product->$property )
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
	public function product_exists() {

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
	public function attachment_url_exists( $url ) {

		global $wpdb;

		$attachment_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` as p LEFT JOIN `{$wpdb->postmeta}` as pm ON ( p.`ID` = pm.`post_id` ) WHERE p.`post_type` = 'attachment' AND pm.`meta_key` = %s AND pm.`meta_value` = %s;",
				Plugin::prefix( 'image' ),
				$url
			)
		);

		return ! empty( $attachment_id ) ? (int) $attachment_id : false;

	}

	/**
	 * Import product as a product post.
	 *
	 * @since NEXT
	 *
	 * @return int|false
	 */
	public function post() {

		$post_id = wp_insert_post(
			[
				'post_type'    => Post_Type::SLUG,
				'post_status'  => 'publish',
				'post_title'   => $this->product->title,
				'post_content' => $this->product->content,
			]
		);

		foreach ( $this->product as $property => $value ) {

			update_post_meta( $post_id, Plugin::prefix( $property ), $value );

		}

		return ( $post_id > 0 ) ? $post_id : false;

	}

	/**
	 * Import product categories as terms and preserve heirarchy.
	 *
	 * @param  array $categories
	 * @param  int   $post_id
	 * @param  int   $parent (optional)
	 *
	 * @return array
	 */
	public function categories( array $categories, $post_id, $parent = 0 ) {

		if ( 0 === ( $post_id = absint( $post_id ) ) ) {

			return [];

		}

		$term_ids = [];

		foreach ( $categories as $category ) {

			if ( is_string( $category ) ) {

				$term_ids[] = $this->category( $category, $post_id, $parent );

				continue;

			}

			foreach ( (array) $category as $index => $categories ) {

				$parent = $this->category( $index, $post_id, $parent );

				$term_ids[] = $parent;

				$term_ids = array_merge( $term_ids, $this->categories( $categories, $post_id, $parent ) );

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
	private function category( $name, $post_id, $parent = 0 ) {

		$term = term_exists( $name, Taxonomy_Category::SLUG );

		if ( ! is_array( $term ) ) {

			$term = wp_insert_term( $name, Taxonomy_Category::SLUG, [ 'parent' => (int) $parent ] );

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
	public function attachment( $post_id ) {

		$url = esc_url_raw( $this->product->image );

		$attachment_id = $this->attachment_url_exists( $url );
		$attachment_id = ( $attachment_id ) ? $attachment_id : $this->sideload_image( $url, 0, $this->product->title );

		if ( ! $attachment_id ) {

			return false;

		}

		set_post_thumbnail( $post_id, $attachment_id );

		update_post_meta( $attachment_id, Plugin::prefix( 'id' ), $this->product->id );
		update_post_meta( $attachment_id, Plugin::prefix( 'image' ), $url );
		update_post_meta( $attachment_id, Plugin::prefix( 'post_id' ), $post_id );

		return $attachment_id;

	}

	/**
	 * Sideload an image and return its attachment ID.
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
