/* global jQuery, rstore */

"use strict";

( function( $ ) {

	var cookies = {
		cartCount: 'rstore-cart-count'
	};

	var cart = {

		init: function() {

			var value = parseInt( cookie.get( cookies.cartCount ), 10 );

			cart.updateCount( value );

			if ( value > 0 ) {

				cart.api( 'get', function( response ) {

					cart.updateCount( response );

				} );

			}

		},

		api: function( method, data, callback ) {

			var settings = {
				method: method,
				url: rstore.urls.cart_api,
				dataType: 'json',
				contentType: 'application/json',
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				}
			};

			if ( 3 === arguments.length ) {

				settings.data = JSON.stringify( data );

			}

			callback = ( 2 === arguments.length ) ? data : callback;

			$.ajax( settings ).done( callback );

		},

		addItem: function( id, qty, redirect ) {

			var data = { items: [ {
				id: id,
				quantity: ( qty > 0 ) ? qty : 1, // Must be greater than 0
				periodCount: 1
			} ] };

			cart.api( 'post', data, function( response ) {

				cart.updateCount( response );

				if ( redirect ) {

					window.location.href = rstore.urls.cart;

				}

			} );

		},

		updateCount: function( value ) {

			value = ( undefined !== value.cartCount ) ? value.cartCount : value;
			value = ( value ) ? parseInt( value, 10 ) : 0;

			cookie.set( cookies.cartCount, value );

			$( '.rstore-cart-count' ).text( value );

			$( '.widget.rstore-cart' ).each( function() {

				$( this ).toggle( ! $( this ).hasClass( 'hide-empty' ) || value > 0 );

			} );

		},

		addItemButton: function( e ) {

			e.preventDefault();

			var $this    = $( this ),
			    id       = $this.attr( 'data-id' ),
			    qty      = parseInt( $this.attr( 'data-quantity' ), 10 ),
			    redirect = ( 'true' === $this.attr( 'data-redirect' ) );

			cart.addItem( id, qty, redirect );

		},

		addItemParam: function() {

			var arg = window.location.search.match( /(\?|&)add-to-cart=(.*?)(&|$)/i );

			if ( null !== arg ) {

				cart.addItem( arg[2], 1, false );

			}

		},

		updateQty: function( e ) {

			var $this = $( this ),
			    qty   = $this.val();

			if ( '' === qty ) {

				if ( 'focusout' !== e.type ) {

					return false;

				}

				qty = 1;

			}

			qty = parseInt( qty, 10 );
			qty = ( qty > 1 ) ? qty : 1;

			$this.val( qty );
			$this.next( '.rstore-add-to-cart' ).attr( 'data-quantity', qty );

		}

	};

	var cookie = {

		get: function( name ) {

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

		},

		set: function( name, value, days ) {

			var date = new Date();

			days = ( days ) ? days : 30;

			date.setTime( date.getTime() + ( days * 86400 * 1000 ) ); // Convert to ms

			document.cookie = name + "=" + value + "; expires=" + date.toGMTString() + "; path=/";

		}

	};

	$( document ).ready( function( $ ) {

		cart.addItemParam();

		cart.init();

		$( document ).on( 'click', '.rstore-add-to-cart', cart.addItemButton );
		$( document ).on( 'keyup blur', '.rstore-add-to-cart-quantity', cart.updateQty );

	} );

} )( jQuery );
