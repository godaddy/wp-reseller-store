/* global Cookies, jQuery, rstore */

( function( $ ) {

	'use strict';

	var cart = {

		init: function() {

			var value = parseInt( Cookies.get( rstore.cookies.cartCount ), 10 );

			cart.updateCount( value );

			if ( value > 0 ) {

				cart.api( 'get', {}, function( response ) {

					cart.updateCount( response );

				} );

			}

		},

		api: function( method, data, success, error ) {

			var settings = {
				method: method,
				url: rstore.urls.cart_api,
				dataType: 'json',
				contentType: 'application/json',
				data: JSON.stringify( data ),
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				}
			};

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

				if ( $form && ! redirect ) {

					cart.addItemSuccess( $form );

				}

			}, function( response ) {

				window.console.log( response );

				if ( $form ) {

					cart.addItemError( $form, response );

				}

			} );

		},

		updateCount: function( value ) {

			value = ( undefined !== value.cartCount ) ? value.cartCount : value;
			value = ( value ) ? parseInt( value, 10 ) : 0;

			Cookies.set( rstore.cookies.cartCount, value, { expires: new Date( new Date().getTime() + rstore.cookies.ttl ), path: '/' } );

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

			if ( $this.attr( 'data-loading' ) ) {

				return false;

			}

			$this.attr( 'data-loading', 'true' );

			$form.find( '.rstore-message' ).empty();
			$form.find( '.rstore-loading' ).show();

			cart.addItem( id, qty, redirect, $form );

		},

		addItemSuccess: function( $form ) {

			var html = '<span class="dashicons dashicons-yes rstore-success"></span> <a href="' + rstore.urls.cart + '">' + rstore.i18n.view_cart + '</a>';

			$form.find( '.rstore-add-to-cart' ).removeAttr( 'data-loading' );
			$form.find( '.rstore-loading' ).hide();
			$form.find( '.rstore-message' ).html( html );

		},

		addItemError: function( $form, response ) {

			var message = rstore.i18n.error;

			if ( undefined !== response.error.statusCode && undefined !== response.error.name && undefined !== response.error.message ) {

				message = response.error.statusCode + ' ' + response.error.name + ': ' + response.error.message;

			}

			var html = '<span class="dashicons dashicons-warning rstore-error"></span> ' + message;

			$form.find( '.rstore-add-to-cart' ).removeAttr( 'data-loading' );
			$form.find( '.rstore-loading' ).hide();
			$form.find( '.rstore-message' ).html( html );

		},

		addItemParam: function() {

			var arg = window.location.search.match( /(\?|&)add-to-cart=(.*?)(&|$)/i );

			if ( null !== arg && rstore.product.id ) {

				cart.addItem( rstore.product.id, parseInt( arg[2], 10 ), false );

				// Remove args from the URL without redirecting
				window.history.replaceState( {}, '', window.location.href.split( '?' )[0] );

			}

		}

	};

	$( document ).ready( function( $ ) {

		cart.addItemParam();

		cart.init();

		$( document ).on( 'click', '.rstore-add-to-cart', cart.addItemButton );

	} );

} )( jQuery );
