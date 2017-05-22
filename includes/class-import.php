<?php

namespace Reseller_Store;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Import {

	/**
	 * Product object instance.
	 *
	 * @since 0.2.0
	 *
	 * @var Product
	 */
	private $product;

	/**
	 * Product post ID.
	 *
	 * @since 0.2.0
	 *
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * Array of imported products.
	 *
	 * @since 0.2.0
	 *
	 * @var array
	 */
	private $imported = [];

	/**
	 * The result of the import.
	 *
	 * @var true|WP_Error
	 */
	private $result;

	/**
	 * Class constructor.
	 *
	 * @param stdClass $product
	 * @param int      $post_id (optional)
	 */
	public function __construct( $product, $post_id = 0 ) {

		$this->product  = $product->product;
		$this->post_id  = absint( $post_id );
		$this->imported = (array) rstore_get_option( 'imported', [] );

		$fallback_id = ( is_a( $this->product, 'stdClass' ) && ! empty( $this->product->id ) ) ? $this->product->id : strtolower( esc_html__( 'unknown', 'reseller-store' ) );

		if ( ! $product->is_valid() ) {

			$this->result = new WP_Error(
				'invalid_product',
				/* translators: product name */
				esc_html__( '`%s` is not a valid product.', 'reseller-store' ),
				$fallback_id
			);

			return;

		}

		if ( ! $this->post_id && $product->exists() ) {

			// product exists so don't import it
			return;

		}

		/**
		 * Validate product data reset.
		 *
		 * If we have a post ID then this is a re-import of an existing
		 * product that's already tied to a post. We'll need some extra
		 * validation for this scenario.
		 */
		if ( $this->post_id ) {

			if ( ! array_key_exists( $this->post_id, $this->imported ) ) {

				$this->result = new WP_Error(
					'product_not_imported',
					/* translators: product name */
					esc_html__( '`%s` must be imported as a product post before it can be reset.', 'reseller-store' ),
					$this->product->id
				);

				return;

			}

			if ( Post_Type::SLUG !== get_post_type( $this->post_id ) ) {

				$this->result = new WP_Error(
					'invalid_post_type',
					/* translators: post type name */
					esc_html__( '`%s` is not a valid post type for products.', 'reseller-store' ),
					$post->post_type
				);

				return;

			}
		}

		$this->post_id = $this->post( $this->post_id );

		if ( is_wp_error( $this->post_id ) ) {

			$this->result = $this->post_id; // Return the WP_Error

			return;

		}

		$this->post_meta();

		$this->categories();

		$this->featured_image( $product->image_exists() );

		$this->result = $this->mark_as_imported(); // Success!

	}

	/**
	 * Return the result of the import.
	 *
	 * @since 0.2.0
	 *
	 * @return true|WP_Error
	 */
	public function result() {

		return $this->result;

	}

	/**
	 * Import product as a post.
	 *
	 * @since 0.2.0
	 *
	 * @return int|WP_Error  Returns the post ID on success, `WP_Error` on failure.
	 */
	private function post() {

		$post_id = wp_insert_post(
			[
				'ID'           => absint( $this->post_id ),
				'post_type'    => Post_Type::SLUG,
				'post_status'  => 'publish',
				'post_title'   => sanitize_text_field( $this->product->title ),
				'post_name'    => sanitize_title( $this->product->title ),
				'post_content' => wp_filter_post_kses( $this->product->content ),
			],
			true
		);

		return $post_id;

	}

	/**
	 * Import product data as post meta.
	 *
	 * @since 0.2.0
	 */
	private function post_meta() {

		global $wpdb;

		/**
		 * The custom post meta values in ButterBean are added
		 * by the user, they are not part of the Product object
		 * that comes from the Storefront API.
		 *
		 * And since the `Restore Product Data` button triggers
		 * the Product::import() method, we'll need to make sure
		 * all post meta with our plugin's prefix is deleted.
		 */
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `{$wpdb->postmeta}` WHERE `post_id` = %d AND `meta_key` LIKE %s;",
				$this->post_id,
				rstore_prefix( '%' )
			)
		);

		rstore_bulk_update_post_meta( $this->post_id, $this->product );

	}

	/**
	 * Import product categories.
	 *
	 * @since 0.2.0
	 */
	private function categories() {

		/**
		 * Since the `Restore Product Data` button triggers the
		 * Product::import() method, we'll need to make sure that
		 * existing product category relationships are deleted.
		 *
		 * Note: This only deletes the relationship between the
		 * post and product category terms, it does not delete the
		 * terms themselves.
		 */
		wp_delete_object_term_relationships( $this->post_id, Taxonomy_Category::SLUG );

		$this->process_categories( $this->product->categories, $this->post_id );

	}

	/**
	 * Process product category terms recursively and preserve heirarchy.
	 *
	 * @since 0.2.0
	 *
	 * @param array $categories
	 * @param int   $post_id
	 * @param int   $parent (optional)
	 */
	private function process_categories( $categories, $post_id, $parent = 0 ) {

		foreach ( $categories as $category ) {

			if ( is_string( $category ) ) {

				$this->add_category( $category, $post_id, $parent );

				continue;

			}

			foreach ( (array) $category as $index => $children ) {

				$term_id = $this->add_category( $index, $post_id, $parent );

				if ( $term_id ) {

					$this->process_categories( $children, $post_id, $term_id );

				}
			}
		}

	}

	/**
	 * Create a product category and assign to the post.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $name
	 * @param  int    $post_id
	 * @param  int    $parent (optional)
	 *
	 * @return int|false  Returns a term ID on success, `false` on failure.
	 */
	private function add_category( $name, $post_id, $parent = 0 ) {

		// Returns 0 or NULL if the term does not exist.
		// Returns an array if a term/taxonomy pairing exists.
		$term = term_exists( $name, Taxonomy_Category::SLUG );

		if ( ! is_array( $term ) ) {

			// Returns an array on success, WP_Error on failure.
			// @codingStandardsIgnoreStart
			$term = wp_insert_term( $name, Taxonomy_Category::SLUG, [ 'parent' => (int) $parent ] );
			// @codingStandardsIgnoreEnd

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
	 * Import image as an attachment and set as the post's featured image.
	 *
	 * @since 0.2.0
	 *
	 * @param int $attachment_id
	 */
	private function featured_image( $attachment_id = 0 ) {

		$url = esc_url_raw( $this->product->image );

		$attachment_id = ( $attachment_id > 0 ) ? (int) $attachment_id : $this->sideload_image( $url, $this->product->title );

		if ( ! $attachment_id ) {

			return false;

		}

		set_post_thumbnail( $this->post_id, $attachment_id );

		$meta = [
			'id'      => sanitize_title( $this->product->id ),
			'image'   => esc_url_raw( $url ),
			'post_id' => $this->post_id,
		];

		rstore_bulk_update_post_meta( $attachment_id, $meta );

	}

	/**
	 * Sideload an image and return its attachment ID.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $url
	 * @param  string $description (optional)
	 *
	 * @return int|false  Returns the attachment ID on success, `false` on failure.
	 */
	private function sideload_image( $url, $description = '' ) {

		if ( ! function_exists( 'download_url' ) ) {

			require_once ABSPATH . 'wp-admin/includes/file.php';

		}

		$file_array = [
			'name'     => basename( $url ),
			'tmp_name' => download_url( $url ),
		];

		if ( ! function_exists( 'media_handle_sideload' ) ) {

			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

		}

		$attachment_id = media_handle_sideload( $file_array, 0, $description );

		if ( is_wp_error( $attachment_id ) ) {

			// @codingStandardsIgnoreStart
			@unlink( $file_array['tmp_name'] );
			// @codingStandardsIgnoreEnd

			return false;

		}

		return (int) $attachment_id;

	}

	/**
	 * Mark a product as imported.
	 *
	 * @since 0.2.0
	 *
	 * @return bool  Returns `true` on success, `false` on failure.
	 */
	private function mark_as_imported() {

		/**
		 * It's possible that a product was previously imported and
		 * assigned to another post ID. We need to make sure to unset
		 * those to prevent duplicates.
		 *
		 * This could happen if a post was deleted and the user somehow
		 * circumvented the `delete_post` action, such as deleting it
		 * manually from the database.
		 */
		if ( $post_id = array_search( $this->product->id, $this->imported, true ) ) {

			unset( $this->imported[ $post_id ] );

		}

		$this->imported[ $this->post_id ] = $this->product->id;

		return rstore_update_option( 'imported', $this->imported );

	}

}
