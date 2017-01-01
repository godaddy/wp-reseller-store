<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Templates {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );

	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @action wp_enqueue_scripts
	 * @since  NEXT
	 */
	public function wp_enqueue_scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-cart', rstore()->assets_url . "js/cart{$suffix}.js", [ 'jquery' ], rstore()->version, true );

		wp_localize_script(
			'rstore-cart',
			'rstore',
			[
				'pl_id'        => (int) Plugin::get_option( 'pl_id' ),
				'cart_url'     => rstore()->api->urls['cart'], // xss ok
				'cart_api_url' => rstore()->api->url( 'cart/{pl_id}' ), // xss ok
			]
		);

	}

}
