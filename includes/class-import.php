<?php
/**
 * GoDaddy Reseller Store import class.
 *
 * Handles importing products from the GoDaddy reseller API.
 *
 * @class    Reseller_Store/Embed
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

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
	private $imported = array();

	/**
	 * The result of the import.
	 *
	 * @var true|WP_Error
	 */
	private $result;

	/**
	 * Class constructor.
	 *
	 * @param stdClass $product Reseller product instance.
	 * @param int      $post_id (optional) Post ID to map the reseller product to.
	 */
	public function __construct( $product, $post_id = 0 ) {
		$this->product  = $product;
		$this->post_id  = absint( $post_id );
		$this->imported = (array) rstore_get_option( 'imported', array() );
	}

	/**
	 * Import the product.
	 *
	 * @return bool|WP_Error
	 */
	public function import_product() {

		if ( ! $this->product->is_valid() ) {

			$fallback_id = ( is_a( $this->product, 'stdClass' ) && ! empty( $this->product->fields->id ) ) ? $this->product->fields->id : strtolower( esc_html__( 'unknown', 'reseller-store' ) );

			return new WP_Error(
				'invalid_product',
				/* translators: product name */
				esc_html__( '`%s` is not a valid product.', 'reseller-store' ),
				$fallback_id
			);
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

				return new WP_Error(
					'product_not_imported',
					/* translators: product name */
					esc_html__( '`%s` must be imported as a product post before it can be reset.', 'reseller-store' ),
					$this->product->fields->id
				);

			}

			if ( Post_Type::SLUG !== get_post_type( $this->post_id ) ) {

				return new WP_Error(
					'invalid_post_type',
					/* translators: post type name */
					esc_html__( '`%s` is not a valid post type for products.', 'reseller-store' ),
					$post->post_type
				);

			}
		}

		$this->post_id = $this->insert_post();

		if ( is_wp_error( $this->post_id ) ) {

			return $this->post_id; // Return the WP_Error.

		}

		$this->post_meta();

		$this->taxonomies();

		$this->featured_image( $this->image_exists() );

		return $this->mark_as_imported(); // Success!
	}

	/**
	 * Import product as a post.
	 *
	 * @since 0.2.0
	 *
	 * @return int|WP_Error  Returns the post ID on success, `WP_Error` on failure.
	 */
	private function insert_post() {

		wp_reset_postdata();

		$post_id = wp_insert_post(
			array(
				'ID'           => absint( $this->post_id ),
				'post_type'    => Post_Type::SLUG,
				'post_status'  => 'publish',
				'post_title'   => sanitize_text_field( $this->product->fields->title ),
				'post_name'    => sanitize_title( $this->product->fields->title ),
				'post_content' => wp_filter_post_kses( $this->product->fields->content ),
			),
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

		rstore_bulk_update_post_meta( $this->post_id, $this->product->fields );

	}

	/**
	 * Import product taxonomies.
	 *
	 * @since 0.2.0
	 */
	private function taxonomies() {

		/**
		 * Since the `Restore Product Data` button triggers the
		 * Product::import() method, we'll need to make sure that
		 * existing product category relationships are deleted.
		 *
		 * Note: This only deletes the relationship between the
		 * post and product category terms, it does not delete the
		 * terms themselves.
		 */
		$taxonomies = array( Taxonomy_Category::SLUG, Taxonomy_Tag::SLUG );
		wp_delete_object_term_relationships( $this->post_id, $taxonomies );
		$this->process_categories( $this->product->fields->categories, $this->post_id );
		$this->process_tags( $this->product->fields->tags, $this->post_id );

	}

	/**
	 * Process product category terms recursively and preserve heirarchy.
	 *
	 * @since 0.2.0
	 *
	 * @param array $categories        Categories to assign the product to.
	 * @param int   $post_id           Product post ID.
	 * @param int   $parent (optional) Product parent ID.
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
	 * @param  string $name              Category name.
	 * @param  int    $post_id           Reseller product post ID.
	 * @param  int    $parent (optional) Reseller product parent ID.
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
	 * Process product tag terms.
	 *
	 * @since 1.1.0
	 *
	 * @param array $tags        tags to assign the product to.
	 * @param int   $post_id           Product post ID.
	 * @param int   $parent (optional) Product parent ID.
	 */
	private function process_tags( $tags, $post_id, $parent = 0 ) {

		foreach ( $tags as $tag ) {

			if ( is_string( $tag ) ) {

				$this->add_tag( $tag, $post_id, $parent );

				continue;

			}
		}
	}

	/**
	 * Create a product tag and assign to the post.
	 *
	 * @since 1.1.0
	 *
	 * @param  string $name              tag name.
	 * @param  int    $post_id           Reseller product post ID.
	 * @param  int    $parent (optional) Reseller product parent ID.
	 *
	 * @return int|false  Returns a term ID on success, `false` on failure.
	 */
	private function add_tag( $name, $post_id, $parent = 0 ) {

		// Returns 0 or NULL if the term does not exist.
		// Returns an array if a term/taxonomy pairing exists.
		$term = term_exists( $name, Taxonomy_Tag::SLUG );

		if ( ! is_array( $term ) ) {

			// Returns an array on success, WP_Error on failure.
			// @codingStandardsIgnoreStart
			$term = wp_insert_term( $name, Taxonomy_Tag::SLUG, [ 'parent' => (int) $parent ] );
			// @codingStandardsIgnoreEnd

		}

		if ( is_wp_error( $term ) ) {

			return false;

		}

		$term_id = isset( $term['term_id'] ) ? (int) $term['term_id'] : false;

		if ( $term_id ) {

			wp_set_object_terms( (int) $post_id, $term_id, Taxonomy_Tag::SLUG, true );

		}

		return $term_id;

	}

	/**
	 * Check if an product image has already been imported.
	 *
	 * @global wpdb $wpdb
	 * @since  0.2.0
	 *
	 * @return int|false  Returns the attachment ID if it exists, otherwise `false`.
	 */
	private function image_exists() {

		$key = rstore_prefix( 'product_attachment_id-' . md5( $this->product->fields->image ) );

		$attachment_id = (int) wp_cache_get( $key );

		if ( ! $attachment_id ) {

			global $wpdb;

			$attachment_id = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `ID` FROM `{$wpdb->posts}` as p LEFT JOIN `{$wpdb->postmeta}` as pm ON ( p.`ID` = pm.`post_id` ) WHERE p.`post_type` = 'attachment' AND pm.`meta_key` = %s AND pm.`meta_value` = %s;",
					rstore_prefix( 'image' ),
					esc_url_raw( $this->product->fields->image ) // Image URLs are sanitized on import.
				)
			);

			wp_cache_set( $key, $attachment_id );

		}

		return ( $attachment_id > 0 ) ? $attachment_id : false;

	}

	/**
	 * Import image as an attachment and set as the post's featured image.
	 *
	 * @since 0.2.0
	 *
	 * @param int $attachment_id Reseller product image attachment ID.
	 *
	 * @return bool
	 */
	private function featured_image( $attachment_id = 0 ) {

		$url = esc_url_raw( $this->product->fields->image );

		$attachment_id = ( $attachment_id > 0 ) ? (int) $attachment_id : $this->sideload_image( $url, $this->product->fields->title );

		if ( ! $attachment_id ) {

			return false;

		}

		set_post_thumbnail( $this->post_id, $attachment_id );

		$meta = array(
			'image' => esc_url_raw( $url ),
		);

		rstore_bulk_update_post_meta( $attachment_id, $meta );

	}

	/**
	 * Sideload an image and return its attachment ID.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $url                    Reseller product featured image URL.
	 * @param  string $description (optional) Reseller product featured image description.
	 *
	 * @return int|false  Attachment ID on success, else `false`.
	 */
	private function sideload_image( $url, $description = '' ) {

		if ( ! function_exists( 'download_url' ) ) {

			require_once ABSPATH . 'wp-admin/includes/file.php';

		}

		$file_array = array(
			'name'     => basename( $url ),
			'tmp_name' => download_url( $url ),
		);

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
		$post_id = array_search( $this->product->fields->id, $this->imported, true );

		if ( $post_id ) {

			unset( $this->imported[ $post_id ] );

		}

		$this->imported[ $this->post_id ] = $this->product->fields->id;

		return rstore_update_option( 'imported', $this->imported );

	}

}
