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
	 * Test base endpoint URL.
	 */
	public function test_add_query_args_no_market_or_currency() {

		$api = new API();
		$url = 'https://api.secureserver.net';

		$query_string = $api->add_query_args( $url, false );

		$this->assertEquals( $url , $query_string );

	}

	/**
	 * Test base currencyType query args.
	 */
	public function test_add_query_args_currency_filter() {

		$api = new API();
		$url = 'https://api.secureserver.net';

		add_filter( 'rstore_api_currency', function() {

			return 'USD';

		} );

		$query_string = $api->add_query_args( $url, false );

		$this->assertEquals( $url . '?currencyType=USD' , $query_string );

	}

	/**
	 * Test base marketId query args.
	 */
	public function test_add_query_args_marketId_filter() {

		$api = new API();
		$url = 'https://api.secureserver.net';

		add_filter( 'rstore_api_market_id', function() {

			return 'fr-FR';

		} );

		$query_string = $api->add_query_args( $url, false );

		$this->assertEquals( $url . '?marketId=fr-FR' , $query_string );

	}

}
