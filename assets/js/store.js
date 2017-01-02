/* global jQuery, rstore */

( function( $ ) {

	'use strict';

	var cart = {

		init: function() {

			var value = parseInt( cookie.get( rstore.cookies.cartCount ), 10 );

			cart.updateCount( value );

			if ( value > 0 ) {

				cart.api( 'get', function( response ) {

					cart.updateCount( response );

				} );

			}

		},

		api: function( method, data, success, error ) {

			// The data arg is optional depending on the method
			success = ( 3 === arguments.length ) ? arguments[1] : success;
			error   = ( 3 === arguments.length ) ? arguments[2] : error;

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

			if ( arguments.length > 3 ) {

				settings.data = JSON.stringify( data );

			}

			success = $.isFunction( success ) ? success : function() { return; };
			error   = $.isFunction( error ) ? error : function() { return; };

			$.ajax( settings ).done( success ).fail( error );

		},

		addItem: function( id, qty, redirect, $form ) {

			var data = { items: [ {
				id: id,
				quantity: ( qty > 0 ) ? qty : 1 // Must be greater than 0
			} ] };

			cart.api( 'post', data, function( response ) {

				cart.updateCount( response );

				if ( redirect ) {

					window.location.href = rstore.urls.cart;

				}

				if ( $form ) {

					cart.addItemSuccess( $form );

				}

			}, function( response ) {

				if ( $form ) {

					cart.addItemError( $form, response );

				}

			} );

		},

		updateCount: function( value ) {

			value = ( undefined !== value.cartCount ) ? value.cartCount : value;
			value = ( value ) ? parseInt( value, 10 ) : 0;

			cookie.set( rstore.cookies.cartCount, value );

			$( '.rstore-cart-count' ).text( value );

			$( '.widget.rstore-cart' ).each( function() {

				$( this ).toggle( ! $( this ).hasClass( 'hide-empty' ) || value > 0 );

			} );

		},

		addItemButton: function( e ) {

			e.preventDefault();

			var $this    = $( this ),
			    $form    = $this.closest( 'form' ),
			    id       = $this.attr( 'data-id' ),
			    qty      = parseInt( $this.attr( 'data-quantity' ), 10 ),
			    redirect = ( 'true' === $this.attr( 'data-redirect' ) );

			$form.find( '.rstore-message' ).empty();
			$form.find( '.rstore-loading' ).show();

			cart.addItem( id, qty, redirect, $form );

		},

		addItemSuccess: function( $form ) {

			var html = '<span class="dashicons dashicons-yes rstore-success"></span> <a href="' + rstore.urls.cart + '">' + rstore.i18n.view_cart + '</a>';

			$form.find( '.rstore-loading' ).hide();
			$form.find( '.rstore-message' ).html( html );

		},

		addItemError: function( $form, response ) {

			var message = 'An unknown error has occurred';

			if ( undefined !== response.error.statusCode && undefined !== response.error.name && undefined !== response.error.message ) {

				message = response.error.statusCode + ' ' + response.error.name + ': ' + response.error.message;

			}

			var html = '<span class="dashicons dashicons-warning rstore-error"></span> ' + message;

			$form.find( '.rstore-loading' ).hide();
			$form.find( '.rstore-message' ).html( html );

		},

		addItemParam: function() {

			var arg = window.location.search.match( /(\?|&)add-to-cart=(.*?)(&|$)/i );

			if ( null !== arg ) {

				cart.addItem( arg[2], 1, false );

				// Remove args from the URL without redirecting
				window.history.pushState( {}, rstore.product.post_title, window.location.href.split( '?' )[0] );

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

		set: function( name, value, ttl ) {

			var date = new Date();

			ttl = ( ttl ) ? ttl : rstore.cookies.ttl;

			date.setTime( date.getTime() + ttl );

			document.cookie = name + "=" + value + "; expires=" + date.toGMTString() + "; path=/";

		}

	};

	$( document ).ready( function( $ ) {

		cart.addItemParam();

		cart.init();

		$( document ).on( 'click', '.rstore-add-to-cart', cart.addItemButton );
		$( document ).on( 'keyup blur', '.rstore-quantity', cart.updateQty );

	} );

} )( jQuery );
