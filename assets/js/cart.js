/* global jQuery, rstore */

( function ( $ ) {

	$( document ).ready( function() {

		fetch_cart_count(); // TODO: not on every page load

		$( '.rstore-add-to-cart' ).on( 'click', add_to_cart );

	} );

	function set_cart_count( data ) {

		var count = ( 'undefined' !== typeof data.cartCount && data.cartCount ) ? data.cartCount : 0;

		$( '.rstore-cart-count' ).text( parseInt( count ) );

	}

	function fetch_cart_count() {

		$.ajax( {
			method: 'GET',
			url: rstore.cart_api_url,
			crossDomain: true,
			xhrFields: { withCredentials: true },
			dataType: 'json'
		} )
		.done( function( data ) {
			set_cart_count( data );
		} );

	}

	function add_to_cart( e ) {

		e.preventDefault();

		 var $button  = $( this ),
		     redirect = $button.data( 'redirect' ),
		     body     = {
				items: [
					{
						id: $button.data( 'plan' ),
						quantity: 1,
						periodCount: 1
					}
				]
			};

		$button.prop( 'disabled', true );

		$.ajax( {
			method: 'POST',
			url: rstore.cart_api_url,
			crossDomain: true,
			xhrFields: { withCredentials: true },
			contentType: 'application/json',
			dataType: 'json',
			data: JSON.stringify( body )
		} )
		.done( function( data ) {

			if ( 'true' === redirect ) {

				window.location.href = rstore.cart_url;

				return;

			}

			set_cart_count( data );

		} )
		.fail( function( xhr, status, error ) {

			alert( error );

		} )
		.always( function() {

			$button.prop( 'disabled', false );

		} );

	}

} )( jQuery );
