<?php
/**
 * GoDaddy Reseller Store API tests
 */

namespace Reseller_Store;

final class TestAPI extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * @testdox Given rstore is not setup add_query_args should should return a url with the plid.
	 */
	public function test_add_query_args_no_market_or_currency() {

		$api = new API();
		$url = 'https://www.secureserver.net';

		$query_string = $api->add_query_args( $url );

		$this->assertEquals( $url, $query_string );

	}

	/**
	 * @testdox Given rstore is setup add_query_args should should return a url with the plid.
	 */
	public function test_add_query_args() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->add_query_args( 'https://www.secureserver.net' );

		$this->assertEquals( 'https://www.secureserver.net?plid=12345', $url );

	}

	/**
	 * @testdox Given a valid query args filter it should return query args.
	 */
	public function rstore_api_query_args() {

		$api = new API();
		$url = 'https://api.secureserver.net';

		add_filter(
			'rstore_api_query_args',
			function( $args ) {

				$args['currencyType'] = 'USD';
				return $args;

			}
		);

		$query_string = $api->add_query_args( $url, false );

		$this->assertEquals( $url . '?currencyType=USD', $query_string );

	}

	/**
	 * @testdox Given sso parameter url() should return a login url.
	 */
	public function test_sso_login_url() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->url( 'sso' );

		$this->assertEquals( 'https://sso.secureserver.net/?plid=12345', $url );

	}

	/**
	 * @testdox Given sso and logout parameter url should return a logout url.
	 */
	public function test_sso_logout_url() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->url( 'sso', 'logout' );

		$this->assertEquals( 'https://sso.secureserver.net/logout/?plid=12345', $url );

	}

	/**
	 * @testdox Given invalid url_key parameter should return www url.
	 */
	public function test_invalid_url_key() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->url( 'invalide' );

		$this->assertEquals( 'https://www.secureserver.net/?plid=12345', $url );

	}

}
