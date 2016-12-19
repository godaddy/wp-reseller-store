<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class API {

	/**
	 * Base API URL.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $base_url = 'https://storefront.api.dev-secureserver.net/api/v1/';

	/**
	 * Cart URL.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $cart_url = 'https://cart.dev-secureserver.net/';

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		/**
		 * Filter the API base URL.
		 *
		 * @since NEXT
		 *
		 * @var string
		 */
		$this->base_url = trailingslashit( (string) apply_filters( 'rstore_api_base_url', $this->base_url ) );

		/**
		 * Filter the Cart URL.
		 *
		 * @since NEXT
		 *
		 * @var string
		 */
		$this->cart_url = trailingslashit( (string) apply_filters( 'rstore_cart_url', $this->cart_url ) );

	}

	/**
	 * Return a Cart URL.
	 *
	 * @since NEXT
	 *
	 * @return string
	 */
	public function cart_url() {

		$url = rstore()->is_setup() ? add_query_arg( 'pl_id', (int) rstore()->get_option( 'pl_id' ), $this->cart_url ) : $this->cart_url;

		return esc_url_raw( $url );

	}

	/**
	 * Return an API endpoint URL.
	 *
	 * @since NEXT
	 *
	 * @param  string $endpoint (optional)
	 *
	 * @return string
	 */
	public function url( $endpoint = '' ) {

		$url = $this->base_url;

		if ( $endpoint ) {

			$url = sprintf(
				'%s/%s',
				untrailingslashit( $url ),
				str_replace( '{pl_id}', (int) rstore()->get_option( 'pl_id' ), $endpoint )
			);

		}

		return esc_url_raw( trailingslashit( $url ) );

	}

	/**
	 * Make an API request.
	 *
	 * @since NEXT
	 *
	 * @param  string $endpoint (optional)
	 *
	 * @return mixed
	 */
	public function get( $endpoint = '' ) {

		$args = [
			'headers'   => [ 'Content-Type: application/json' ],
			'sslverify' => false, // TODO: Should be true with prod API
		];

		/**
		 * Filter the default API request args.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_api_request_args', $args );

		$response = wp_remote_get( $this->url( $endpoint ), $args );

		if ( is_wp_error( $response ) ) {

			wp_die(
				$response->get_error_message(),
				esc_html__( 'API Error', 'reseller-store' )
			);

		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

}
