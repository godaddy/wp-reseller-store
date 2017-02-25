<?php

namespace Reseller_Store;

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
	private $tld = 'secureserver.net';

	/**
	 * Maximum number of retries for API requests.
	 *
	 * @since NEXT
	 *
	 * @var int
	 */
	private $max_retries = 0;

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
			'currencyType' => rstore_get_option( 'currency', 'USD' ),
			'marketId'     => $this->get_market_id(),
		];

		if ( $add_pl_id && rstore_is_setup() ) {

			$args['pl_id'] = (int) rstore_get_option( 'pl_id' );

		}

		return esc_url_raw( add_query_arg( $args, $url ) );

	}

	/**
	 * Return the market ID for a given locale.
	 *
	 * @since NEXT
	 *
	 * @param  string $locale (optional)
	 *
	 * @return string
	 */
	public function get_market_id( $locale = null ) {

		$locale = ( $locale ) ? $locale : get_locale();

		$mappings = [
			'da-DK'  => 'da_DK', // Danish (Denmark)
			'de-DE'  => 'de_DE', // German
			'el-GR'  => 'el',    // Greek
			'es-ES'  => 'es_ES', // Spanish
			'es-MX'  => 'es_MX', // Spanish (Mexico)
			'fi-FI'  => 'fi',    // Finnish
			'fil-PH' => 'tl',    // Filipino (Philippines)
			'fr-FR'  => 'fr_FR', // French
			'hi-IN'  => 'hi_IN', // Hindi (India)
			'id-ID'  => 'id_ID', // Indonesian
			'it-IT'  => 'it_IT', // Italian
			'ja-JP'  => 'ja',    // Japanese
			'ko-KR'  => 'ko_KR', // Korean
			'mr-IN'  => 'mr',    // Marathi (India)
			'ms-MY'  => 'ms_MY', // Malay (Malaysia)
			'nb-NO'  => 'nb_NO', // Norwegian (Norway)
			'nl-NL'  => 'nl_NL', // Dutch (Netherlands)
			'pl-PL'  => 'pl_PL', // Polish
			'pt-BR'  => 'pt_BR', // Portuguese (Brazil)
			'pt-PT'  => 'pt_PT', // Portuguese (Portugal)
			'ru-RU'  => 'ru_RU', // Russian
			'sv-SE'  => 'sv_SE', // Swedish (Sweden)
			'th-TH'  => 'th',    // Thai
			'tl-PH'  => 'tl',    // Tagalog
			'tr-TR'  => 'tr_TR', // Turkish
			'uk-UA'  => 'uk',    // Ukranian
			'vi-VN'  => 'vi',    // Vietnamese
			'zh-CN'  => 'zh_CN', // Chinese
			'zh-TW'  => 'zh_TW', // Chinese (Taiwan)
		];

		$market_id = array_search( $locale, $mappings, true );

		/**
		 * Filter the market ID used in API requests.
		 *
		 * @since NEXT
		 *
		 * @var string
		 */
		$market_id = (string) apply_filters( 'rstore_api_market_id', $market_id );

		return ( $market_id ) ? $market_id : 'en-US';

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
				str_replace( '{pl_id}', (int) rstore_get_option( 'pl_id' ), $endpoint )
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
			'sslverify' => true,
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

		$code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $code && ! is_wp_error( $response ) ) {

			return json_decode( wp_remote_retrieve_body( $response ) );

		}

		static $errors = 0;

		$errors++;

		if ( $errors <= $this->max_retries ) {

			sleep( 1 ); // Pause between retries

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
	 * @since NEXT
	 *
	 * @param  string $endpoint
	 * @param  array  $args     (optional)
	 *
	 * @return array|WP_Error
	 */
	public function get( $endpoint, array $args = [] ) {

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

}
