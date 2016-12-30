/* global Cookies, jQuery, rstore */

( function( $ ) {

	var cart_cookie = 'rstore-cart-count',
	    $widget     = $( '.rstore-hide-empty-cart' ).closest( '.widget.rstore_cart' );

	$( document ).ready( function() {

		var cart_count = parseInt( Cookies.get( cart_cookie ) );

		if ( cart_count > 0 ) {

			$widget.show();

			$( '.rstore-cart-count' ).text( cart_count );

			fetch_cart_count();

		} else {

			$widget.hide();

		}

		$( '.rstore-add-to-cart' ).on( 'click', add_to_cart );

	} );

	function set_cart_count( data ) {

		var count = ( 'undefined' !== typeof data.cartCount && data.cartCount ) ? parseInt( data.cartCount ) : 0;

		$( '.rstore-cart-count' ).text( count );

		if ( count > 0 ) {

			$widget.show();

			Cookies.set( cart_cookie, count, { expires: 7 } );

		} else {

			$widget.hide();

			Cookies.remove( cart_cookie );

		}

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
						id: $button.data( 'id' ),
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

			window.alert( error );

		} )
		.always( function() {

			$button.prop( 'disabled', false );

		} );

	}

} )( jQuery );
