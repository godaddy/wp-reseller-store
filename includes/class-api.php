<?php

namespace Reseller_Store;

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

		add_action( 'plugins_loaded', function () {

			$this->urls['api']           = sprintf( 'https://storefront.api.%s/api/v1/', $this->tld );
			$this->urls['cart']          = $this->add_pl_id_arg( sprintf( 'https://cart.%s/', $this->tld ) );
			$this->urls['domain_search'] = $this->add_pl_id_arg( sprintf( 'https://www.%s/domains/search.aspx?checkAvail=1', $this->tld ) );

		} );

	}

	/**
	 * Add the `pl_id` query arg to a given URL.
	 *
	 * @since NEXT
	 *
	 * @param  string $url
	 *
	 * @return string
	 */
	public function add_pl_id_arg( $url ) {

		$url = rstore()->is_setup() ? add_query_arg( 'pl_id', (int) rstore()->get_option( 'pl_id' ), $url ) : $url;

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

		$url = trailingslashit( $this->urls['api'] );

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
