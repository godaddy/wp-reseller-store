<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Product_Caps {

	/**
	 * Array of product caps.
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	private $caps = [
		// Post Type
		'edit_reseller_product',
		'read_reseller_product',
		'delete_reseller_product',
		'edit_reseller_products',
		'edit_others_reseller_products',
		'publish_reseller_products',
		'read_private_reseller_products',
		'delete_reseller_products',
		'delete_private_reseller_products',
		'delete_published_reseller_products',
		'delete_others_reseller_products',
		'edit_private_reseller_products',
		'edit_published_reseller_products',
		// Terms
		'manage_reseller_product_terms',
		'edit_reseller_product_terms',
		'delete_reseller_product_terms',
		'assign_reseller_product_terms',
	];

	/**
	 * Array of product cap user roles.
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	private $roles = [ 'administrator', 'editor' ];

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		/**
		 * Filter the array of product caps.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$this->caps = (array) apply_filters( 'rstore_product_caps', $this->caps );

		/**
		 * Filter the array of product cap user roles.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$this->roles = (array) apply_filters( 'rstore_product_cap_roles', $this->roles );

		add_action( 'init', [ $this, 'add' ] );

	}

	/**
	 * Add/remove product caps to/from user roles.
	 *
	 * @global WP_Roles $wp_roles
	 * @since  NEXT
	 *
	 * @param string $action Supports `add` or `remove`
	 */
	private function do( $action = 'add' ) {

		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {

			return;

		}

		if ( ! isset( $wp_roles ) ) {

			$wp_roles = new WP_Roles;

		}

		$method = "{$action}_cap";

		if ( ! is_callable( [ $wp_roles, $method ] ) ) {

			return;

		}

		foreach ( $this->roles as $role ) {

			foreach ( $this->caps as $cap ) {

				$wp_roles->$method( $role, $cap );

			}

		}

	}

	/**
	 * Add product caps to user roles.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function add() {

		$this->do( 'add' );

	}

	/**
	 * Remove product caps from user roles.
	 *
	 * @since NEXT
	 */
	public function remove() {

		$this->do( 'remove' );

	}

}
