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

		$this->assertEquals( $url , $query_string );

	}

	/**
	 * @testdox Given rstore is setup add_query_args should should return a url with the plid.
	 */
	public function test_add_query_args() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->add_query_args( 'https://www.secureserver.net' );

		$this->assertEquals( 'https://www.secureserver.net?pl_id=12345' , $url );

	}

	/**
	 * @testdox Given a valid query args filter it should return query args.
	 */
	public function rstore_api_query_args() {

		$api = new API();
		$url = 'https://api.secureserver.net';

		add_filter(
			'rstore_api_query_args', function( $args ) {

				$args['currencyType'] = 'USD';
				return $args;

			}
		);

		$query_string = $api->add_query_args( $url, false );

		$this->assertEquals( $url . '?currencyType=USD' , $query_string );

	}

	/**
	 * @testdox Given paramter true get_sso_url should return a login url.
	 */
	public function test_sso_login_url() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->get_sso_url( true );

		$this->assertEquals( 'https://mya.secureserver.net/?plid=12345&realm=idp&app=www' , $url );

	}

	/**
	 * @testdox Given paramter false get_sso_url should return a logout url.
	 */
	public function test_sso_logout_url() {

		rstore_update_option( 'pl_id', 12345 );

		$api = new API();

		$url = $api->get_sso_url( false );

		$this->assertEquals( 'https://sso.secureserver.net/logout?plid=12345&realm=idp&app=www' , $url );

	}

}
