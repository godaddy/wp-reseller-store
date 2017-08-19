/* global Cookies, jQuery, rstore */

( function( $ ) {

  'use strict';

  var cart = {

    init: function() {

      if ( window.self !== window.top ) {

        return;

      }

      //window listener
      var eventMethod = window.addEventListener ? 'addEventListener' : 'attachEvent';
      var eventer = window[eventMethod];
      var messageEvent = eventMethod == 'attachEvent' ? 'onmessage' : 'message';
      eventer(messageEvent,function(e) {

        if (e.data) {

          if ( e.data.message === 'link' && cart.validateSecret( e.data.secret ) ) {
            window.location = e.data.value;

          }

          if ( e.data.message === 'addItemSuccess' && cart.validateSecret( e.data.secret ) ) {

            cart.updateCount( e.data.value );
          }
        }
      }, false);

    },

    validateSecret: function( secret ) {

      return $('iframe.wp-embedded-content').filter(function( index, item ) {

         return ( $( item ).data('secret') === secret );

      }).length;

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

      if ( window.self !== window.top ) {

        var secret = window.location.hash.replace( /.*secret=([\d\w]{10}).*/, '$1' );
        window.parent.postMessage( {
          message: 'addItemSuccess',
          value: value,
          secret: secret
        }, '*' );

      } else {

        value = ( undefined !== value.cartCount ) ? value.cartCount : value;
        value = ( value ) ? parseInt( value, 10 ) : 0;

        $( '.rstore-cart-count' ).text( value );

        $( '.widget.rstore-cart' ).each( function() {

          $( this ).toggle( ! $( this ).hasClass( 'hide-empty' ) || value > 0 );

        } );
      }

    },

    addItemButton: function( e ) {
      e.preventDefault();

      var $this    = $( this ),
          $form    = $this.closest( '.rstore-add-to-cart-form' ),
          id       = $this.attr( 'data-id' ),
          qty      = parseInt( $this.attr( 'data-quantity' ), 10 ),
          redirect = ( 'true' === $this.attr( 'data-redirect' ) );

      if ( $this.attr( 'data-loading' ) ) {

        return false;

      }

      $this.attr( 'data-loading', 'true' );

      $form.find( '.rstore-message' ).empty();
      $form.find( '.rstore-loading' ).removeClass('rstore-loading-hidden');

      cart.addItem( id, qty, redirect, $form );

    },

    addItemSuccess: function( $form ) {

      var html = '<span class="dashicons dashicons-yes rstore-success"></span> <a href="' + rstore.urls.cart + '" >' + rstore.i18n.view_cart + '</a>';

      $form.find( '.rstore-add-to-cart' ).removeAttr( 'data-loading' );
      $form.find( '.rstore-loading' ).addClass('rstore-loading-hidden');
      $form.find( '.rstore-message' ).html( html );

      return;

    },

    addItemError: function( $form, response ) {
      var error = response.responseJSON.error;
      var message = rstore.i18n.error;
      if ( undefined !== error.message ) {

        message = error.message;

      }

      var html = '<span class="dashicons dashicons-warning rstore-error"></span> ' + message;

      $form.find( '.rstore-add-to-cart' ).removeAttr( 'data-loading' );
      $form.find( '.rstore-loading' ).addClass('rstore-loading-hidden');
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

  var login = {
    init: function(){

      if ( window.self !== window.top ) {

        return;

      }

      var plid = rstore.pl_id,
        sid = Cookies.get( rstore.cookies.shopperId ),
        url = rstore.urls.gui;

      $.ajax({
        url: url,
        jsonp: "callback",
        dataType: "jsonp",
        data: {
          plid: plid,
          sid: sid
        },
        success: function( response ) {
          if (response.status === 'partial') {
            $( '.rstore-welcome-name' ).each( function() {
              $( this ).text( response.name );
            });

            $( '.rstore-welcome-block' ).each( function() {
              $( this ).show();
            });

            $( '.rstore-login-button' ).each( function() {
              $( this ).hide();
            });
          }

          cart.updateCount( response.carttotal );


        }
      });
    }
  };

  $( document ).ready( function( $ ) {

    cart.addItemParam();

    cart.init();

    login.init();

    $( '.rstore-add-to-cart' ).on( 'click', cart.addItemButton );

  } );

} )( jQuery );
