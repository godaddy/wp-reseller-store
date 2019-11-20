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
 * @since    1.0.0
 */

namespace Reseller_Store;

use Reseller_Store\Product_Icons;
use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
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

		add_action( 'init', array( $this, 'register' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'delete_imported_product' ) );

		add_filter( 'manage_' . self::SLUG . '_posts_columns', array( $this, 'columns' ) );
		add_filter( 'posts_clauses', array( $this, 'order_by_price_clause' ), 10, 2 );
		add_filter( 'post_type_labels_' . self::SLUG, array( $this, 'post_screen_edit_heading' ) );
		add_filter( 'the_content', 'rstore_append_add_to_cart_form' );
		add_filter( 'the_excerpt', 'rstore_append_add_to_cart_form' );
		add_filter( 'post_thumbnail_html', array( $this, 'post_thumbnail_html' ), 10, 5 );

		add_filter(
			'edit_' . self::SLUG . '_per_page',
			function () {

				return 50;

			}
		);

		add_filter(
			'manage_edit-' . self::SLUG . '_sortable_columns',
			function ( $columns ) {

			// @codingStandardsIgnoreStart
			return array_merge( $columns, [ 'price' => 'price' ] );
			// @codingStandardsIgnoreEnd

			}
		);

		add_filter(
			'view_mode_post_types',
			function ( $post_types ) {

			// @codingStandardsIgnoreStart
			return array_diff_key( $post_types, [ self::SLUG => self::SLUG ] );
			// @codingStandardsIgnoreEnd

			}
		);

		add_filter(
			'rest_prepare_' . self::SLUG,
			function ( $data, $post ) {

				$sale = rstore_get_product_meta( $post->ID, 'salePrice' );

				// Product properties.
				$data->data['plid']          = rstore_get_option( 'pl_id' );
				$data->data['sku']           = rstore_get_product_meta( $post->ID, 'id' );
				$data->data['price']         = $sale ? $sale : rstore_get_product_meta( $post->ID, 'listPrice' );
				$data->data['regular_price'] = rstore_get_product_meta( $post->ID, 'listPrice' );
				$data->data['sale_price']    = rstore_get_product_meta( $post->ID, 'salePrice' );
				$data->data['price_html']    = rstore_price( $post->ID );
				$data->data['icon_html']     = Product_Icons::get_product_icon( $post, 'icon' );

				return $data;
			},
			10,
			3
		);

		add_action( 'save_post', array( $this, 'republish_post' ) );
	}

	/**
	 * Republishes the product post from the catalog api
	 *
	 * @since 1.1.0
	 *
	 * @param int $post_id Product post ID.
	 *
	 * @return void
	 */
	public function republish_post( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( get_post_status( $post_id ) !== 'publish' ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['republish_product'] ) || ! 'Reset' === $_POST['republish_product'] ) {
			return;
		}

		if ( ! isset( $_POST['post_type'] ) || ! self::SLUG === $_POST['post_type'] ) {
			return;
		}

		// Unhook this function so it doesn't loop infinitely.
		remove_action( 'save_post', array( $this, 'republish_post' ) );

		$result = $this->reset_product_data( $post_id );

		// Re-hook this function.
		add_action( 'save_post', array( $this, 'republish_post' ) );

		if ( ! is_wp_error( $result ) ) {

			return;

		}

		/**
		 * If there is an error, display it in an admin notice.
		 */
		add_action(
			'edit_form_top',
			function () use ( $result ) {

				echo printf(
					'<div class="notice notice-error is-dismissible"><p>Error: %s</p></div>',
					esc_html(
						sprintf(
							$result->get_error_message(),
							$result->get_error_data( $result->get_error_code() )
						)
					)
				);

			},
			0
		);

	}

	/**
	 * Return the post type custom permalink base.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public static function permalink_base() {

		$permalinks     = (array) rstore_get_option( 'permalinks', array() );
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

		$labels = array(
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
		);

		$args = array(
			'labels'       => $labels,
			'description'  => esc_html__( 'This is where you can add new products to your Reseller Store.', 'reseller-store' ),
			'menu_icon'    => 'dashicons-cart',
			'public'       => true,
			'show_in_rest' => true,
			'rest_base'    => self::permalink_base(),
			'query_var'    => self::permalink_base(),
			'has_archive'  => true,
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'      => array(
				'slug'       => self::permalink_base(),
				'with_front' => false,
				'feeds'      => true,
			),
		);

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
	 * Reset a product's data.
	 *
	 * @since  0.2.0
	 *
	 * @param  int $post_id Product post ID.
	 *
	 * @return true|\WP_Error
	 */
	public function reset_product_data( $post_id ) {

		$product_id = rstore_get_product_meta( $post_id, 'id' );

		if ( empty( $product_id ) ) {

			return new \WP_Error(
				'invalid_product_id',
				esc_html__( 'Product id not found or invalid.', 'reseller-store' )
			);

		}

		$product = rstore_get_product( $product_id );

		if ( is_wp_error( $product ) ) {
			return $product;
		}

		$import = new Import( $product, $post_id );

		if ( is_wp_error( $import ) ) {
			return $import;

		}

		return $import->import_product();

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
			array(
				'image' => sprintf(
					'<span class="rstore-image dashicons dashicons-format-image" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
					__( 'Product Image', 'reseller-store' )
				),
			),
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

			echo get_the_post_thumbnail( $post_id, array( 40, 40 ) );

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

		$imported = (array) rstore_get_option( 'imported', array() );

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
	 * Filters the post thumbnail HTML.
	 *
	 * @filter post_thumbnail_html
	 * @since  2.0.5
	 *
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param string       $post_thumbnail_id The post thumbnail ID.
	 * @param string|array $size              The post thumbnail size. Image size or array of width and height
	 *                                        values (in that order). Default 'post-thumbnail'.
	 * @param string       $attr              Query string of attributes.
	 *
	 * @return string                         The post thumbnail HTML.
	 */
	public function post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

		if ( Product_Icons::PRODUCT_IMAGE_SLUG === $attr ) {
			return $html;
		}

		$product = get_post( $post_id );

		if ( null === $product || 'publish' !== $product->post_status ||
			\Reseller_Store\Post_Type::SLUG !== $product->post_type ) {

			return $html;

		}

		return Product_Icons::get_product_icon( $product, 'icon' );

	}

}
