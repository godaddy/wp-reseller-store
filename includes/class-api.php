<?php
/**
 * GoDaddy Reseller Store API class.
 *
 * Handles communication with the GoDaddy reseller API.
 *
 * @class    Reseller_Store/API
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

final class API {

	/**
	 * Top-level domain for URLs.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	private $tld = 'secureserver.net';

	/**
	 * Maximum number of retries for API requests.
	 *
	 * @since 0.2.0
	 *
	 * @var int
	 */
	private $max_retries = 0;

	/**
	 * Array of URLs.
	 *
	 * @since 0.2.0
	 *
	 * @var array
	 */
	public $urls = [];

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		/**
		 * Filter the base TLD.
		 *
		 * @since 0.2.0
		 *
		 * @var string
		 */
		$this->tld = (string) apply_filters( 'rstore_api_tld', $this->tld );

		/**
		 *
		 *
		 * @since 0.2.0
		 *
		 * @var int
		 */
		$this->max_retries = (int) apply_filters( 'rstore_api_max_retries', $this->max_retries );

		$this->urls['api']  = sprintf( 'https://storefront.api.%s/api/v1/', $this->tld );
		$this->urls['cart'] = $this->add_query_args( sprintf( 'https://cart.%s/', $this->tld ) );
		$this->urls['gui'] = sprintf( 'https://gui.%s/pcjson/standardheaderfooter', $this->tld );

	}


	/**
	 * Build a SSO login or logout url.
	 *
	 * @since NEXT
	 *
	 * @param bool $login        Generate a Login or Logout URL.
	 *
	 * @return string
	 */
	public function get_sso_url( $login = true ) {

		$args = [
			'plid'  => (int) rstore_get_option( 'pl_id' ),
			'realm' => 'idp',
			'app'   => 'www',

		];

		$url = sprintf(
			'https://%s.%s/%s',
			$login ? 'mya' : 'sso',
			$this->tld,
			$login ? '' : 'logout'
		);

		return esc_url_raw( add_query_arg( $args, $url ) );

	}

	/**
	 * Add required query args to a given URL.
	 *
	 * @since 0.2.0
	 *
	 * @param string $url        The original URL.
	 * @param bool   $add_pl_id (optional) 'pl_id' to add to the query.
	 *
	 * @return string
	 */
	public function add_query_args( $url, $add_pl_id = true ) {

		$args = [];

		if ( $add_pl_id && rstore_is_setup() ) {

			$args['pl_id'] = (int) rstore_get_option( 'pl_id' );

		}

		/**
		 * Filter the currency ID used in API requests.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		$currency = (string) apply_filters( 'rstore_api_currency', false );

		if ( $currency ) {

			$args['currencyType'] = $currency;

		}

		/**
		 * Filter the market ID used in API requests.
		 *
		 * @since 0.2.0
		 *
		 * @var string
		 */
		$market = (string) apply_filters( 'rstore_api_market_id', false );

		if ( $market ) {

			$args['marketId'] = $market;

		}

		return esc_url_raw( add_query_arg( $args, $url ) );

	}

	/**
	 * Return an API endpoint URL.
	 *
	 * @since 0.2.0
	 *
	 * @param string $endpoint (optional) API endpoint to override the request with.
	 *
	 * @return string
	 */
	public function url( $endpoint = '' ) {

		$url = trailingslashit( $this->urls['api'] );

		if ( $endpoint ) {

			$url = sprintf(
				'%s/%s',
				untrailingslashit( $url ),
				str_replace( '{pl_id}', (int) rstore_get_option( 'pl_id' ), $endpoint )
			);

		}

		return $this->add_query_args( trailingslashit( $url ), false );

	}

	/**
	 * Make an API request.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $method   HTTP request method.
	 * @param  string $endpoint API endpoint.
	 * @param  array  $args     (optional) Additional query arguments.
	 *
	 * @return array|WP_Error
	 */
	private function request( $method, $endpoint, $args = [] ) {

		$defaults = [
			'method'    => $method,
			'sslverify' => true,
			'headers'   => [
				'Content-Type: application/json',
			],
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the default API request args.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_api_request_args', $args );

		$response = wp_remote_request( $this->url( $endpoint ), $args );

		$code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $code && ! is_wp_error( $response ) ) {

			return json_decode( wp_remote_retrieve_body( $response ) );

		}

		static $errors = 0;

		$errors++;

		if ( $errors <= $this->max_retries ) {

			sleep( 1 ); // Pause between retries.

			return $this->request( $method, $endpoint, $args );

		}

		$code = is_wp_error( $response ) ? $response->get_error_code() : $code;

		$message = is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_response_message( $response );
		$message = trim( $message );
		$message = ( $message ) ? $message : esc_html__( 'An unknown error has occurred.', 'reseller-store' );

		return new WP_Error( $code, $message );

	}

	/**
	 * Make a GET request to the API.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $endpoint API endpoint to retrieve data from.
	 * @param  array  $args     (optional) Additional query arguments.
	 *
	 * @return array|WP_Error
	 */
	public function get( $endpoint, $args = [] ) {

		$key = rstore_prefix( 'api_get-' . md5( $endpoint . maybe_serialize( $args ) ) );

		$results = wp_cache_get( $key );

		if ( false === $results ) {

			$results = $this->request( 'GET', $endpoint, $args );

			wp_cache_set( $key, $results );

		}

		return $results;

	}

	/**
	 * Make a POST request to the API.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $endpoint API endpoint.
	 * @param  array  $args     Additional query arguments.
	 *
	 * @return array|WP_Error
	 */
	public function post( $endpoint, $args = [] ) {

		return $this->request( 'POST', $endpoint, $args );

	}

	/**
	 * Make a DELETE request to the API.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $endpoint API endpoint.
	 * @param  array  $args     Additional query arguments.
	 *
	 * @return array|WP_Error
	 */
	public function delete( $endpoint, $args = [] ) {

		return $this->request( 'DELETE', $endpoint, $args );

	}

}
