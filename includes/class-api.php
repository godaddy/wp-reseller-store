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
	private $urls = array();

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

		$this->urls['api']      = sprintf( 'https://www.%s/api/v1/', $this->tld );
		$this->urls['cart_api'] = sprintf( 'https://www.%s/api/v1/cart/{pl_id}', $this->tld );
		$this->urls['cart']     = sprintf( 'https://cart.%s/', $this->tld );
		$this->urls['www']      = sprintf( 'https://www.%s/', $this->tld );
		$this->urls['sso']      = sprintf( 'https://sso.%s/', $this->tld );
		$this->urls['account']  = sprintf( 'https://account.%s/', $this->tld );
		$this->urls['gui']      = sprintf( 'https://gui.%s/pcjson/standardheaderfooter', $this->tld );

	}

	/**
	 * Add required query args to a given URL.
	 *
	 * @since 0.2.0
	 *
	 * @param string $url     The original URL.
	 * @param array  $args    (optional) Additional query arguments.
	 * @param string $url_key (optional) Url Key to use for bulding url.
	 *
	 * @return string
	 */
	public function add_query_args( $url, $args = array(), $url_key = '' ) {

		if ( rstore_is_setup() ) {

			$args['plid'] = (int) rstore_get_option( 'pl_id' );

		}

		$args = (array) apply_filters( 'rstore_api_query_args', $args, $url_key );

		return  esc_url_raw( add_query_arg( $args, $url ) );

	}

	/**
	 * Return an API endpoint URL.
	 *
	 * @since 0.2.0
	 *
	 * @param string $url_key  (optional) Url Key to use for bulding url.
	 * @param string $endpoint (optional) API endpoint to override the request with.
	 * @param array  $args     (optional) Additional query arguments.
	 *
	 * @return string
	 */
	public function url( $url_key, $endpoint = '', $args = array() ) {

		if ( ! array_key_exists( $url_key, $this->urls ) ) {
			return $this->url( 'www', $endpoint );
		}

		if ( 'cart' === $url_key && empty( $endpoint ) ) {

			$endpoint = 'go/checkout';

		}

		$url = trailingslashit( $this->urls[ $url_key ] );

		if ( $endpoint ) {

			$url = sprintf(
				'%s/%s',
				untrailingslashit( $url ),
				$endpoint
			);

		}

		$url = str_replace( '{pl_id}', (int) rstore_get_option( 'pl_id' ), $url );

		return $this->add_query_args( trailingslashit( $url ), $args, $url_key );

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
	private function request( $method, $endpoint, $args = array() ) {

		$defaults = array(
			'method'    => $method,
			'sslverify' => true,
			'headers'   => array(
				'Content-Type: application/json',
			),
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the default API request args.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$args = (array) apply_filters( 'rstore_api_request_args', $args );

		$response = wp_remote_request( $this->url( 'api', $endpoint ), $args );

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
	public function get( $endpoint, $args = array() ) {

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
	public function post( $endpoint, $args = array() ) {

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
	public function delete( $endpoint, $args = array() ) {

		return $this->request( 'DELETE', $endpoint, $args );

	}

}
