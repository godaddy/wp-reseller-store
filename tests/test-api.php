<?php

namespace Reseller_Store;

final class TestAPI extends TestCase {

	function setUp() {

		parent::setUp();
	}

	public function test_add_query_args_no_market_or_currency() {
		$api = new API();
		$url = 'https://api.secureserver.net';
		$query_string = $api->add_query_args( $url, false );
		$this->assertEquals( $url , $query_string );
	}

	public function test_add_query_args_currency_filter() {
		$api = new API();
		$url = 'https://api.secureserver.net';

		add_filter( 'rstore_api_currency', function() {
			return 'USD';
		} );

		$query_string = $api->add_query_args( $url, false );
		$this->assertEquals( $url . '?currencyType=USD' , $query_string );
	}

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
