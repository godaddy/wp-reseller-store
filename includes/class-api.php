<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class API {

	/**
	 * API base URL.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $url = 'https://storefront.api.dev-secureserver.net/api/v1/catalog/';

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
		$this->url = (string) apply_filters( 'rstore_api_base_url', $this->url );

	}

	/**
	 * Make an API request.
	 *
	 * @since NEXT
	 *
	 * @param  string $endpoint (optional)
	 * @param  bool   $use_id (optional)
	 *
	 * @return mixed
	 */
	public function get( $endpoint = '', $use_id = true ) {

		$url = $this->url;

		if ( $use_id && rstore()->is_setup() ) {

			$url = sprintf(
				'%s/%d/',
				untrailingslashit( $url ),
				rstore()->get_option( 'reseller_id' )
			);

		}

		if ( ! empty( $endpoint ) ) {

			$url = sprintf( '%s/%s/', untrailingslashit( $url ), $endpoint );

		}

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

		$response = wp_remote_get( esc_url_raw( $url ), $args );

		if ( is_wp_error( $response ) ) {

			wp_die(
				$response->get_error_message(),
				esc_html__( 'API Error', 'reseller-store' )
			);

		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

}
