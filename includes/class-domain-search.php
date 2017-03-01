<?php

namespace Reseller_Store;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Domain_Search {

	/**
	 * Top-level domain for URLs.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $tld = 'dev-secureserver.net';

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
		$this->tld = (string) apply_filters( 'rstore_domain_api_tld', $this->tld );

		/**
		 *
		 *
		 * @since NEXT
		 *
		 * @var int
		 */
		$this->max_retries = (int) apply_filters( 'rstore_domain_api_max_retries', $this->max_retries );

		$this->urls['api'] = 'https://api.dev-secureserver.net/v1/domains/suggest?query=some%20domain%20name';

		add_action( 'wp_ajax_rstore_domain_search', [ __CLASS__, 'domain_search' ] );

	}

	public static function domain_search() {

		$args = [
			'query' => filter_input( INPUT_POST, 'domain_to_check' ),
		];

		$limit = absint( filter_input( INPUT_POST, 'limit' ) );
		$limit = ( $limit > 0 ) ? $limit : 5;

		$endpoint = add_query_arg( $args, 'v1/domains/suggest' );

		$response = rstore()->api->get( $endpoint, 'domain_api' );

		$domains = [];
		$count = 1;

		 if ( count( $response ) == 0 ) {
      $no_results = [
        'domains' => [],
        'pl_id' => rstore_get_option( 'pl_id', '' )
      ];
      return wp_send_json($no_results);
    }

		foreach ($response as $value) {
      $domains[] = $value->domain;
      $count++;
      if ($count > $limit) {
        break;
      }
    }

		$args = [
			'body' => wp_json_encode( $domains ),
		];

		$endpoint = 'v1/domains/available?checkType=FAST';
		$response = rstore()->api->post( $endpoint, 'domain_api', $args );
		$response->pl_id = rstore_get_option( 'pl_id', '' );

		wp_send_json( $response );

	}

}
