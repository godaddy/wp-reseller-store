<?php

namespace Reseller_Store;

use stdClass;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class API {

	/**
	 * Top-level domain for URLs.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $tld = 'dev-secureserver.net'; // TODO: use prod TLD here

	/**
	 * Maximum number of retries for API requests.
	 *
	 * @since NEXT
	 *
	 * @var int
	 */
	private $max_retries = 1;

	/**
	 * Array of URLs.
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	public $urls = [];

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		/**
		 * Filter the base TLD.
		 *
		 * @since NEXT
		 *
		 * @var string
		 */
		$this->tld = (string) apply_filters( 'rstore_api_tld', $this->tld );

		/**
		 *
		 *
		 * @since NEXT
		 *
		 * @var int
		 */
		$this->max_retries = (int) apply_filters( 'rstore_api_max_retries', $this->max_retries );

		$this->urls['api']           = sprintf( 'https://storefront.api.%s/api/v1/', $this->tld );
		$this->urls['cart']          = $this->add_query_args( sprintf( 'https://cart.%s/', $this->tld ) );
		$this->urls['domain_search'] = $this->add_query_args( sprintf( 'https://www.%s/domains/search.aspx?checkAvail=1', $this->tld ) );

	}

	/**
	 * Add required query args to a given URL.
	 *
	 * @since NEXT
	 *
	 * @param  string $url
	 * @param  bool   $add_pl_id (optional)
	 *
	 * @return string
	 */
	public function add_query_args( $url, $add_pl_id = true ) {

		$args = [
			'currencyType' => Plugin::get_option( 'currency', 'USD' ),
			'marketId'     => Plugin::get_option( 'market_id', 'en-US' ),
		];

		if ( $add_pl_id && Plugin::is_setup() ) {

			$args['pl_id'] = (int) Plugin::get_option( 'pl_id' );

		}

		return esc_url_raw( add_query_arg( $args, $url ) );

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

		$url = trailingslashit( $this->urls['api'] );

		if ( $endpoint ) {

			$url = sprintf(
				'%s/%s',
				untrailingslashit( $url ),
				str_replace( '{pl_id}', (int) Plugin::get_option( 'pl_id' ), $endpoint )
			);

		}

		return esc_url_raw( $this->add_query_args( trailingslashit( $url ), false ) );

	}

	/**
	 * Make an API request.
	 *
	 * @since NEXT
	 *
	 * @param  string $method
	 * @param  string $endpoint
	 * @param  array  $args     (optional)
	 *
	 * @return array|WP_Error
	 */
	private function request( $method, $endpoint, array $args = [] ) {

		$defaults = [
			'method'    => $method,
			'sslverify' => ! WP_DEBUG, // This should be true for PROD
			'headers'   => [
				'Content-Type: application/json',
			],
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the default API request args.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_api_request_args', $args );

		$response = wp_remote_request( $this->url( $endpoint ), $args );

		if ( ! is_wp_error( $response ) ) {

			return json_decode( wp_remote_retrieve_body( $response ) );

		}

		static $errors = 0;

		$errors++;

		if ( $errors <= $this->max_retries ) {

			sleep( 2 ); // Pause between retries

			return $this->request( $method, $endpoint, $args );

		}

		return $response;

	}

	/**
	 * Make a GET request to the API.
	 *
	 * @since NEXT
	 *
	 * @param  string $endpoint
	 * @param  array  $args     (optional)
	 *
	 * @return array|WP_Error
	 */
	public function get( $endpoint, array $args = [] ) {

		return $this->request( 'GET', $endpoint, $args );

	}

	/**
	 * Make a POST request to the API.
	 *
	 * @since NEXT
	 *
	 * @param  string $endpoint
	 * @param  array  $args     (optional)
	 *
	 * @return array|WP_Error
	 */
	public function post( $endpoint, array $args = [] ) {

		return $this->request( 'POST', $endpoint, $args );

	}

	/**
	 * Make a DELETE request to the API.
	 *
	 * @since NEXT
	 *
	 * @param  string $endpoint
	 * @param  array  $args     (optional)
	 *
	 * @return array|WP_Error
	 */
	public function delete( $endpoint, array $args = [] ) {

		return $this->request( 'DELETE', $endpoint, $args );

	}

	/**
	 * Return an array of products and cache them.
	 *
	 * @param  bool $force (optional)
	 *
	 * @return array|WP_Error
	 */
	public static function get_products( $force = false ) {

		if ( $force ) {

			Plugin::delete_transient( 'products' );

		}

		return (array) Plugin::get_transient( 'products', [], function () {

			return rstore()->api->get( 'catalog/{pl_id}/products' );

		} );

	}

	/**
	 * Return a product and cache it.
	 *
	 * @param  string $product_id
	 * @param  bool   $force (optional)
	 *
	 * @return stdClass|WP_Error
	 */
	public static function get_product( $product_id, $force = false ) {

		if ( $force ) {

			Plugin::delete_transient( 'product_' . $product_id );

		}

		return Plugin::get_transient( 'product_' . $product_id, [], function () use ( $product_id ) {

			return rstore()->api->get( 'catalog/{pl_id}/products/' . $product_id );

		} );

	}

}
