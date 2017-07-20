<?php
/**
 * GoDaddy Reseller Store post types class.
 *
 * Handles the Reseller Store post types.
 *
 * @class    Reseller_Store/Post_Type
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    NEXT
 */

namespace Reseller_Store;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Post_Type {

	/**
	 * Post type slug.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product';

	/**
	 * Post type menu position.
	 *
	 * @since 0.2.0
	 *
	 * @var int
	 */
	const MENU_POSITION = 52;

	/**
	 * Post type default permalink base.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	public static $default_permalink_base;

	/**
	 * Hold error object.
	 *
	 * @since 0.2.0
	 *
	 * @var WP_Error
	 */
	private $error;

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		self::$default_permalink_base = sanitize_title( esc_html_x( 'products', 'slug name', 'reseller-store' ) );

		add_action( 'init',                         [ $this, 'register' ] );
		add_action( 'admin_init',                   [ $this, 'process_product_reset' ] );
		add_action( 'admin_head',                   [ $this, 'column_styles' ] );
		add_action( 'manage_posts_custom_column',   [ $this, 'column_content' ], 10, 2 );
		add_action( 'delete_post',                  [ $this, 'delete_imported_product' ] );

		add_filter( 'manage_' . self::SLUG . '_posts_columns', [ $this, 'columns' ] );
		add_filter( 'posts_clauses',                           [ $this, 'order_by_price_clause' ], 10, 2 );
		add_filter( 'post_type_labels_' . self::SLUG,          [ $this, 'post_screen_edit_heading' ] );
		add_filter( 'the_content',                             [ $this, 'append_add_to_cart_form' ] );
		add_filter( 'the_excerpt',                             [ $this, 'append_add_to_cart_form' ] );

		add_filter( 'edit_' . self::SLUG . '_per_page', function () {

			return 50;

		} );

		add_filter( 'manage_edit-' . self::SLUG . '_sortable_columns', function ( $columns ) {

			// @codingStandardsIgnoreStart
			return array_merge( $columns, [ 'price' => 'price' ] );
			// @codingStandardsIgnoreEnd

		} );

		add_filter( 'view_mode_post_types', function ( $post_types ) {

			// @codingStandardsIgnoreStart
			return array_diff_key( $post_types, [ self::SLUG => self::SLUG ] );
			// @codingStandardsIgnoreEnd

		} );

	}

	/**
	 * Return the post type custom permalink base.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public static function permalink_base() {

		$permalinks     = (array) rstore_get_option( 'permalinks', [] );
		$permalink_base = ! empty( $permalinks['product_base'] ) ? $permalinks['product_base'] : self::$default_permalink_base;

		return sanitize_title( $permalink_base );

	}

	/**
	 * Register the custom post type.
	 *
	 * @action init
	 * @since  0.2.0
	 */
	public function register() {

		$labels = [
			'name'                  => esc_html_x( 'Products', 'post type general name', 'reseller-store' ),
			'singular_name'         => esc_html_x( 'Product', 'post type singular name', 'reseller-store' ),
			'menu_name'             => esc_html_x( 'Reseller Store', 'admin menu', 'reseller-store' ),
			'name_admin_bar'        => esc_html_x( 'Reseller Product', 'add new on admin bar', 'reseller-store' ),
			'add_new'               => esc_html_x( 'Add New', 'product', 'reseller-store' ),
			'add_new_item'          => esc_html__( 'Add New Products', 'reseller-store' ),
			'edit_item'             => esc_html__( 'Edit Product', 'reseller-store' ),
			'new_item'              => esc_html__( 'New Product', 'reseller-store' ),
			'view_item'             => esc_html__( 'View Product', 'reseller-store' ),
			'view_items'            => esc_html__( 'View Products', 'reseller-store' ),
			'search_items'          => esc_html__( 'Search Products', 'reseller-store' ),
			'not_found'             => esc_html__( 'No products found.', 'reseller-store' ),
			'not_found_in_trash'    => esc_html__( 'No products found in Trash.', 'reseller-store' ),
			'parent_item_colon'     => esc_html__( 'Parent Products:', 'reseller-store' ),
			'all_items'             => esc_html__( 'All Products', 'reseller-store' ),
			'archives'              => esc_html__( 'Product Archives', 'reseller-store' ),
			'insert_into_item'      => esc_html__( 'Insert into product', 'reseller-store' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this product', 'reseller-store' ),
			'featured_image'        => esc_html__( 'Product Image', 'reseller-store' ),
			'set_featured_image'    => esc_html__( 'Set product image', 'reseller-store' ),
			'remove_featured_image' => esc_html__( 'Remove product image', 'reseller-store' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'reseller-store' ),
		];

		$args = [
			'labels'        => $labels,
			'description'   => esc_html__( 'This is where you can add new products to your Reseller Store.', 'reseller-store' ),
			'menu_icon'     => 'dashicons-cart',
			'menu_position' => self::MENU_POSITION,
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => self::permalink_base(),
			'query_var'     => self::permalink_base(),
			'has_archive'   => true,
			'supports'      => [ 'title', 'editor', 'thumbnail' ],
			'rewrite'       => [
				'slug'       => self::permalink_base(),
				'with_front' => false,
				'feeds'      => true,
			],
		];

		/**
		 * Filter the post type args.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_product_args', $args );

		register_post_type( self::SLUG, $args );

	}

	/**
	 * Process a product data reset in the admin.
	 *
	 * @since 0.2.0
	 *
	 * @action admin_init
	 */
	public function process_product_reset() {

		$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		$nonce   = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );

		if (
			! rstore_is_admin_uri( 'post.php?post=' )
			||
			! $post_id
			||
			! $nonce
			||
			false === wp_verify_nonce( $nonce, sprintf( 'rstore_reset_product_nonce-%d-%d', $post_id, get_current_user_id() ) )
		) {

			return;

		}

		$result = $this->reset_product_data( $post_id );

		if ( ! is_wp_error( $result ) ) {

			return;

		}

		/**
		 * If there is an error, display it in an admin notice.
		 */
		add_action( 'edit_form_top', function () use ( $result ) {

			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html(
					sprintf(
						$result->get_error_message(),
						$result->get_error_data( $result->get_error_code() )
					)
				)
			);

		} );

	}

	/**
	 * Reset a product's data.
	 *
	 * @since  0.2.0
	 *
	 * @param  int $post_id Product post ID.
	 *
	 * @return true|WP_Error
	 */
	public function reset_product_data( $post_id ) {

		$product = rstore_get_product( rstore_get_product_meta( $post_id, 'id' ) );

		if ( is_wp_error( $product ) ) {

			return $product; // Return the WP_Error.

		}

		return $product->import( $post_id );

	}

	/**
	 * Print styles for custom columns on the edit screen.
	 *
	 * @action admin_head
	 * @since  0.2.0
	 */
	public function column_styles() {

		if ( ! rstore_is_admin_uri( 'post_type=' . self::SLUG, false ) ) {

			return;

		}

		?>
		<style type="text/css">
		#screen-options-wrap .rstore-image {
			display: inline;
			font-family: inherit;
			font-size: inherit;
			line-height: inherit;

			 -webkit-font-smoothing: auto;
			-moz-osx-font-smoothing: auto;
		}
		#screen-options-wrap .rstore-image:before {
			display: none;
		}
		#screen-options-wrap .rstore-image .screen-reader-text {
			visibility: visible;
			position: static;
		}
		table.wp-list-table .column-image {
			width: 52px;
			text-align: center;
			white-space: nowrap;
		}
		table.wp-list-table .column-title {
			width: 33%;
		}
		@media only screen and (max-width: 782px) {
			.post-type-<?php echo esc_attr( self::SLUG ); ?> .wp-list-table .column-image {
				display: none;
				text-align: left;
				padding-bottom: 0;
			}
			.post-type-<?php echo esc_attr( self::SLUG ); ?> .wp-list-table .column-image img {
				max-width: 32px;
				height: auto;
			}
			.post-type-<?php echo esc_attr( self::SLUG ); ?> .wp-list-table tr td.column-image::before {
				display: none !important;
			}
		}
		</style>
		<?php

	}

	/**
	 * Add custom columns.
	 *
	 * @filter manage_{post_type}_posts_columns
	 * @since  0.2.0
	 *
	 * @param  array $columns Admin post columns.
	 *
	 * @return array
	 */
	public function columns( $columns ) {

		// Insert before Title column.
		$columns = rstore_array_insert(
			$columns,
			[
				'image' => sprintf(
					'<span class="rstore-image dashicons dashicons-format-image" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
					__( 'Product Image', 'reseller-store' )
				),
			],
			(int) array_search( 'title', array_values( array_flip( $columns ) ), true )
		);

		// Insert after Title column.
		$columns = rstore_array_insert(
			$columns,
			// @codingStandardsIgnoreStart
			[ 'price' => esc_html__( 'Price', 'reseller-store' ) ],
			// @codingStandardsIgnoreEnd
			(int) array_search( 'title', array_values( array_flip( $columns ) ), true ) + 1
		);

		return $columns;

	}

	/**
	 * Display custom column content.
	 *
	 * @action manage_posts_custom_column
	 * @since  0.2.0
	 *
	 * @param string $column  Admin column name.
	 * @param int    $post_id Post ID.
	 */
	public function column_content( $column, $post_id ) {

		if ( 'image' === $column ) {

			echo get_the_post_thumbnail( $post_id, [ 40, 40 ] );

		}

		if ( 'price' === $column ) {

			$list = rstore_get_product_meta( $post_id, 'listPrice' );
			$sale = rstore_get_product_meta( $post_id, 'salePrice' );

			printf( // xss ok.
				'%s%s',
				( $sale ) ? sprintf( '<del>%s</del><br>', esc_html( $list ) ) : '',
				( $sale ) ? esc_html( $sale ) : esc_html( $list )
			);

		}

	}

	/**
	 * Mark an imported product as deleted.
	 *
	 * @action delete_post
	 * @since  0.2.0
	 *
	 * @param  int $post_id Product post ID.
	 *
	 * @return bool  Returns `true` on success, `false` on failure.
	 */
	public function delete_imported_product( $post_id ) {

		if ( Post_Type::SLUG !== get_post_type( $post_id ) ) {

			return false;

		}

		// Re-fetch products from the API to ensure `rstore_has_all_products()` is accurate.
		rstore_delete_transient( 'products' );

		$imported = (array) rstore_get_option( 'imported', [] );

		unset( $imported[ $post_id ] );

		return rstore_update_option( 'imported', $imported );

	}

	/**
	 * Strip `$` from meta values when ordering by price.
	 *
	 * @filter posts_clauses
	 * @global wpdb $wpdb
	 * @since  0.2.0
	 *
	 * @param  array  $clauses  $wpdb query clauses.
	 * @param  object $wp_query $wp_query instance.
	 *
	 * @return array
	 */
	public function order_by_price_clause( $clauses, $wp_query ) {

		global $wpdb;

		if ( self::SLUG === $wp_query->get( 'post_type' ) && 'price' === $wp_query->get( 'orderby' ) ) {

			$order = ( 'DESC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'DESC' : 'ASC';

			$clauses['join'] .= $wpdb->prepare(
				" LEFT JOIN `{$wpdb->postmeta}` pm ON ( `{$wpdb->posts}`.`ID` = pm.`post_id` AND pm.`meta_key` = %s ) ",
				rstore_prefix( 'listPrice' )
			);

			$clauses['orderby'] = " CONVERT( REPLACE( pm.`meta_value`, '$', '' ), DECIMAL( 13, 2 ) ) {$order}"; // xss ok.

		}

		return $clauses;

	}

	/**
	 * Customize the edit heading on the post screen.
	 *
	 * @filter post_type_labels_{post_type}
	 * @since  0.2.0
	 *
	 * @param  stdClass $labels Product labels.
	 *
	 * @return array
	 */
	public function post_screen_edit_heading( $labels ) {

		if ( ! rstore_is_admin_uri( 'post.php?post=' ) ) {

			return $labels;

		}

		$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		$title = ( $post_id > 0 ) ? rstore_get_product_meta( $post_id, 'title' ) : null;

		/* translators: product title */
		$labels->edit_item = ( $title ) ? sprintf( esc_html__( 'Edit: %s', 'reseller-store' ), $title ) : $labels->edit_item;

		return $labels;

	}

	/**
	 * Append an `Add to cart` form the end of product post content.
	 *
	 * @action the_content
	 * @global WP_Post $post
	 * @since  0.2.0
	 *
	 * @param  string $content Product content.
	 *
	 * @return string
	 */
	public function append_add_to_cart_form( $content ) {

		global $post;

		$is_rest_request = ( defined( 'REST_REQUEST' ) && REST_REQUEST );

		if ( self::SLUG === $post->post_type && ! is_feed() && ! $is_rest_request ) {

			$content .= wpautop( rstore_price( $post->ID, false ) );
			$content .= rstore_add_to_cart_form( $post->ID, false );

		}

		return $content;

	}

}
