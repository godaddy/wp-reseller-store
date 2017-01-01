/* global ajaxurl, jQuery, rstore */

"use strict";

( function( $ ) {

	var cookie = 'rstore-cart-count';

	$( document ).ready( function( $ ) {

		check_count();

		$( document ).on( 'click', '.rstore-add-to-cart', add_to_cart );

	} );

	function check_count() {

		var value = parseInt( get_cookie( cookie ), 10 );

		set_count( value );

		if ( value > 0 ) {

			fetch_count(); // Verify

		}

	}

	function set_count( value ) {

		value = ( 'undefined' !== typeof value.cartCount ) ? value.cartCount : value;
		value = ( value ) ? parseInt( value, 10 ) : 0;

		set_cookie( cookie, value );

		$( '.rstore-cart-count' ).text( value );

	}

	function add_to_cart( e ) {

		e.preventDefault();

		var $this = $( this ),
		    id    = $this.attr( 'data-id' ),
		    qty   = parseInt( $this.attr( 'data-qty' ), 10 );

		$this.css( 'opacity', 0.5 );

		var request = { items: [ {
			id: id,
			quantity: ( qty > 0 ) ? qty : 1, // Must be greater than 0
			periodCount: 1
		} ] };

		$.post( {
			url: rstore.cart_api_url,
			dataType: 'json',
			contentType: 'application/json',
			data: JSON.stringify( request ),
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			}
		} )
		.done( function( data ) {
			set_count( data );
		} )
		.always( function() {
			$this.css( 'opacity', 1 );
		} );

	}

	function fetch_count() {

		$.get( {
			url: rstore.cart_api_url,
			dataType: 'json',
			contentType: 'application/json',
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			}
		} )
		.done( function( data ) {
			set_count( data );
		} );

	}

	function set_cookie( name, value, days ) {

		var date = new Date();

		days = ( days ) ? days : 30;

		date.setTime( date.getTime() + ( days * 86400 * 1000 ) ); // Convert to ms

		document.cookie = name + "=" + value + "; expires=" + date.toGMTString() + "; path=/";

	}

	function get_cookie( name ) {

		var parts = document.cookie.split( ';' );

		name = name + '=';

		for ( var i = 0; i < parts.length; i++ ) {

			var cookie = parts[i];

			while ( ' ' === cookie.charAt( 0 ) ) {

				cookie = cookie.substring( 1 );

			}

			if ( 0 === cookie.indexOf( name ) ) {

				return cookie.substring( name.length, cookie.length );

			}

		}

		return null;

	}

} )( jQuery );
