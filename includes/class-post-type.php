<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Post_Type {

	/**
	 * Post type slug.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product';

	/**
	 * Post type menu position.
	 *
	 * @since NEXT
	 *
	 * @var int
	 */
	const MENU_POSITION = 52;

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		/**
		 * Load Butterbean.
		 */
		add_action( 'plugins_loaded', function () {

			$path = rstore()->base_dir . 'lib/butterbean/butterbean.php';

			if ( is_readable( $path ) ) {

				require_once $path;

			}

		} );

		add_action( 'init',                       [ $this, 'register' ] );
		add_action( 'butterbean_register',        [ $this, 'metabox' ], 10, 2 );
		add_action( 'admin_head',                 [ $this, 'column_styles' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'column_content' ], 10, 2 );

		add_filter( 'view_mode_post_types',                            [ $this, 'view_mode' ] );
		add_filter( 'manage_' . self::SLUG . '_posts_columns',         [ $this, 'columns' ] );
		add_filter( 'manage_edit-' . self::SLUG . '_sortable_columns', [ $this, 'sortable_columns' ] );
		add_filter( 'posts_clauses',                                   [ $this, 'order_by_price_clause' ], 10, 2 );
		add_filter( 'post_type_labels_' . self::SLUG,                  [ $this, 'post_screen_edit_heading' ] );

	}

	/**
	 * Register the custom post type.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function register() {

		$labels = [
			'name'                  => esc_html_x( 'Products', 'post type general name', 'reseller-store' ),
			'singular_name'         => esc_html_x( 'Product', 'post type singular name', 'reseller-store' ),
			'menu_name'             => esc_html_x( 'Reseller Store', 'admin menu', 'reseller-store' ),
			'name_admin_bar'        => esc_html_x( 'Reseller Product', 'add new on admin bar', 'reseller-store' ),
			'add_new'               => esc_html_x( 'Add New', 'product', 'reseller-store' ),
			'add_new_item'          => esc_html__( 'Add New Product', 'reseller-store' ),
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
			'labels'             => $labels,
			'description'        => esc_html__( 'This is where you can add new products to your Reseller Store.', 'reseller-store' ),
			'menu_icon'          => 'dashicons-cart',
			'menu_position'      => self::MENU_POSITION,
			'capability_type'    => 'post',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'supports'           => [ 'title', 'editor', 'thumbnail' ],
			'rewrite'            => [
				'slug'       => self::SLUG,
				'with_front' => false,
				'feeds'      => true,
			],
		];

		/**
		 * Filter the post type args.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_post_type_args', $args );

		register_post_type( self::SLUG, $args );

	}

	/**
	 * Register custom metabox and sections.
	 *
	 * @action butterbean_register
	 * @since  NEXT
	 *
	 * @param ButterBean $butterbean
	 * @param string     $post_type
	 */
	public function metabox( $butterbean, $post_type ) {

		$butterbean->register_manager(
			'product_options',
			[
				'label'     => esc_html__( 'Product Options', 'reseller-store' ),
				'post_type' => self::SLUG,
				'context'   => 'normal',
				'priority'  => 'high',
			]
		);

		$manager = $butterbean->get_manager( 'product_options' );

		$this->general( $manager );

		$this->advanced( $manager );

	}

	/**
	 * Register General section.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 */
	private function general( $manager ) {

		$manager->register_section(
			__METHOD__,
			[
				'label' => esc_html__( 'General', 'reseller-store' ),
				'icon'  => 'dashicons-admin-tools',
			]
		);

		$manager->register_control(
			Plugin::prefix( 'listPrice' ),
			[
				'type'    => 'text',
				'section' => __METHOD__,
				'label'   => esc_html__( 'Price', 'reseller-store' ),
				'attr'    => [
					'class'    => 'disabled',
					'disabled' => 'disabled',
				],
			]
		);

		$manager->register_setting(
			Plugin::prefix( 'listPrice' ),
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		$manager->register_control(
			Plugin::prefix( 'salePrice' ),
			[
				'type'    => 'text',
				'section' => __METHOD__,
				'label'   => esc_html__( 'Sale Price', 'reseller-store' ),
				'attr'    => [
					'class'    => 'disabled',
					'disabled' => 'disabled',
				],
			]
		);

		$manager->register_setting(
			Plugin::prefix( 'salePrice' ),
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

	}

	/**
	 * Register Advanced section.
	 *
	 * @since NEXT
	 *
	 * @param ButterBean_Manager $manager
	 */
	private function advanced( $manager ) {

		$manager->register_section(
			__METHOD__,
			[
				'label' => esc_html__( 'Advanced', 'reseller-store' ),
				'icon'  => 'dashicons-admin-settings',
			]
		);

		$manager->register_control(
			'add_cart_button_label',
			[
				'type'    => 'text',
				'section' => __METHOD__,
				'label'   => esc_html__( 'Add to Cart Button Label', 'reseller-store' ),
				'attr'    => [
					'placeholder' => esc_attr( Plugin::get_option( 'add_cart_button_label', esc_attr__( 'Add to Cart', 'reseller-store' ) ) ),
				],
			]
		);

		$manager->register_setting(
			'add_cart_button_label',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		$args = [
			'type'    => 'checkbox',
			'section' => __METHOD__,
			'label'   => esc_html__( 'Redirect to the cart immediately after adding', 'reseller-store' ),
		];

		if ( 1 === Plugin::get_option( 'add_cart_redirect' ) ) {

			$args['attr']['checked']  = 'checked';
			$args['attr']['disabled'] = 'disabled';

		}

		$manager->register_control( 'add_cart_redirect', $args );

		$manager->register_setting(
			'add_cart_redirect',
			[
				'sanitize_callback' => function( $value ) {
					return ( 'true' === $value ) ? 1 : 0;
				},
			]
		);

	}

	/**
	 * Print styles for custom columns.
	 *
	 * @action admin_head
	 * @since  NEXT
	 */
	public function column_styles() {

		?>
		<style type="text/css">
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
	 * Enable view mode for this post type.
	 *
	 * @filter view_mode_post_types
	 * @since  NEXT
	 *
	 * @param  array $post_types
	 *
	 * @return array
	 */
	public function view_mode( $post_types ) {

		return array_diff( $post_types, [ self::SLUG => self::SLUG ] );

	}

	/**
	 * Add custom columns.
	 *
	 * @filter manage_{post_type}_posts_columns
	 * @since  NEXT
	 *
	 * @param  array $columns
	 *
	 * @return array
	 */
	public function columns( $columns ) {

		// Insert before Title column
		$columns = Plugin::array_insert(
			$columns,
			[
				'image' => sprintf(
					'<span class="dashicons dashicons-format-image" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
					__( 'Product Image', 'reseller-store' )
				),
			],
			(int) array_search( 'title', array_values( array_flip( $columns ) ) )
		);

		// Insert after Title column
		$columns = Plugin::array_insert(
			$columns,
			[ 'price' => __( 'Price', 'reseller-store' ) ],
			(int) array_search( 'title', array_values( array_flip( $columns ) ) ) + 1
		);

		return $columns;

	}

	/**
	 * Make custom columns sortable.
	 *
	 * @filter manage_edit-{post_type}_sortable_columns
	 * @since  NEXT
	 *
	 * @param  array $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {

		$columns['price'] = 'price';

		return $columns;

	}

	/**
	 * Display custom column content.
	 *
	 * @action manage_posts_custom_column
	 * @since  NEXT
	 *
	 * @param string $column
	 * @param int    $post_id
	 */
	public function column_content( $column, $post_id ) {

		if ( 'image' === $column ) {

			echo get_the_post_thumbnail( $post_id, [ 40, 40 ] );

		}

		if ( 'price' === $column ) {

			$price = get_post_meta( $post_id, Plugin::prefix( 'listPrice' ), true );
			$sale  = get_post_meta( $post_id, Plugin::prefix( 'salePrice' ), true );

			printf(
				'%s%s',
				( $sale ) ? sprintf( '<del>%s</del><br>', esc_html( $price ) ) : '',
				( $sale ) ? esc_html( $sale ) : esc_html( $price )
			);

		}

	}

	/**
	 * Strip `$` from meta values when ordering by price.
	 *
	 * @filter posts_clauses
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @param  array    $clauses
	 * @param  WP_Query $wp_query
	 *
	 * @return array
	 */
	public function order_by_price_clause( $clauses, $wp_query ) {

		global $wpdb;

		if ( self::SLUG === $wp_query->get( 'post_type' ) && 'price' === $wp_query->get( 'orderby' ) ) {

			$order = ( 'DESC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'DESC' : 'ASC';

			$clauses['join'] .= $wpdb->prepare(
				" LEFT JOIN `{$wpdb->postmeta}` pm ON ( `{$wpdb->posts}`.`ID` = pm.`post_id` AND pm.`meta_key` = %s ) ",
				Plugin::prefix( 'listPrice' )
			);

			$clauses['orderby'] = " CONVERT( REPLACE( pm.`meta_value`, '$', '' ), DECIMAL( 13, 2 ) ) {$order}"; // xss ok

		}

		return $clauses;

	}

	/**
	 * Customize the edit heading on the post screen.
	 *
	 * @filter post_type_labels_{post_type}
	 * @since  NEXT
	 *
	 * @param  array $labels
	 *
	 * @return array
	 */
	public function post_screen_edit_heading( $labels ) {

		if ( ! Plugin::is_admin_uri( 'post.php?post=' ) ) {

			return $labels;

		}

		$post_id = (int) filter_input( INPUT_GET, 'post' );

		$title = ( $post_id > 0 ) ? get_post_meta( $post_id, Plugin::prefix( 'title' ), true ) : null;

		$labels->edit_item = ! empty( $title ) ? sprintf( esc_html_x( 'Edit: %s', 'product title', 'reseller-store' ), $title ) : $labels->edit_item;

		return $labels;

	}

}
