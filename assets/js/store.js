/* global Cookies, jQuery, rstore */

( function ( $ ) {
  'use strict';

  var cart = {

    init: function () {
      var eventMethod, eventer, messageEvent;
      if ( window.self !== window.top ) {
        return;
      }

      // window listener
      eventMethod = window.addEventListener ? 'addEventListener' : 'attachEvent';
      eventer = window[eventMethod];
      messageEvent = eventMethod === 'attachEvent' ? 'onmessage' : 'message';
      eventer(messageEvent,function (e) {
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

    validateSecret: function ( secret ) {
      return $('iframe.wp-embedded-content').filter(function ( index, item ) {
         return ( $( item ).data('secret') === secret );
      }).length;
    },

    addItemApi: function ( data, success, error ) {
      var param = '&cart='+JSON.stringify(data);
      var settings = {
        type: 'GET',
        url: rstore.urls.cart_api + param,
        dataType: 'jsonp'
      };

      success = $.isFunction( success ) ? success : function () {
        return;
      };
      error = $.isFunction( error ) ? error : function () {
        return;
      };

      $.ajax( settings ).done( success ).fail( error );
    },

    addItem: function ( id, qty, redirect, $form ) {
      var data = { items: [{
        id: id,
        quantity: ( qty > 0 ) ? qty : 1 // Must be greater than 0
      }] };

      cart.addItemApi(data, function ( response ) {
        if (response.error) {
          return cart.addItemError( $form, response );
        }

        cart.updateCount( response );

        if ( redirect ) {
          window.location.href = $form.find( '.rstore-cart' ).find('a').attr('href');
          return;
        }

        cart.addItemSuccess( $form );
      }, function ( response ) {
        window.console.log( response );

        if ( $form ) {
          cart.addItemError( $form, response );
        }
      } );
    },

    updateCount: function ( value ) {
      var secret;
      if ( window.self !== window.top ) {
        secret = window.location.hash.replace( /.*secret=([\d\w]{10}).*/, '$1' );
        window.parent.postMessage( {
          message: 'addItemSuccess',
          value: value,
          secret: secret
        }, '*' );
      }
      else {
        value = ( undefined !== value.cartCount ) ? value.cartCount : value;
        value = ( value ) ? parseInt( value, 10 ) : 0;

        $( '.rstore-cart-count' ).text( value );

        $( '.widget.rstore-cart' ).each( function () {
          $( this ).toggle( ! $( this ).hasClass( 'hide-empty' ) || value > 0 );
        } );
      }
    },

    addItemButton: function ( e ) {
      var $this = $( this ),
          $form = $this.closest( '.rstore-add-to-cart-form' ),
          id = $this.attr( 'data-id' ),
          qty = parseInt( $this.attr( 'data-quantity' ), 10 ),
          redirect = ( $this.attr( 'data-redirect' ) === 'true' );

      e.preventDefault();
      if ( $this.attr( 'data-loading' ) ) {
        return false;
      }

      $this.attr( 'data-loading', 'true' );

      $form.find( '.rstore-message' ).empty();
      $form.find( '.rstore-loading' ).removeClass('rstore-loading-hidden');

      cart.addItem( id, qty, redirect, $form );
    },

    addItemSuccess: function ( $form ) {
      $form.find( '.rstore-add-to-cart' ).removeAttr( 'data-loading' );
      $form.find( '.rstore-loading' ).addClass('rstore-loading-hidden');
      $form.find( '.rstore-cart' ).removeClass('rstore-cart-hidden');

      return;
    },

    addItemError: function ( $form, response ) {
      var error, message, html;

      error = response.error;
      message = rstore.i18n.error;
      if ( undefined !== error.message ) {
        message = error.message;
      }

      html = '<span class="dashicons dashicons-warning rstore-error"></span> ' + message;

      $form.find( '.rstore-add-to-cart' ).removeAttr( 'data-loading' );
      $form.find( '.rstore-loading' ).addClass('rstore-loading-hidden');
      $form.find( '.rstore-message' ).html( html );
    },

    addItemParam: function () {
      var arg = window.location.search.match( /(\?|&)add-to-cart=(.*?)(&|$)/i );

      if ( arg !== null && rstore.product.id ) {
        cart.addItem( rstore.product.id, parseInt( arg[2], 10 ), false );

        // Remove args from the URL without redirecting
        window.history.replaceState( {}, '', window.location.href.split( '?' )[0] );
      }
    }

  };

  var login = {
    init: function () {
      var plid, sid, url;
      if ( window.self !== window.top ) {
        return;
      }

      plid = rstore.pl_id;
      sid = Cookies.get( rstore.cookies.shopperId );
      url = rstore.urls.gui;

      $.ajax({
        url: url,
        jsonp: 'callback',
        dataType: 'jsonp',
        data: {
          plid: plid,
          sid: sid
        },
        success: function ( response ) {
          if (response.status === 'partial') {
            $( '.rstore-welcome-block span.firstname' ).each( function () {
              $( this ).text( response.name );
            });
            $( '.rstore-welcome-block span.lastname' ).each( function () {
              $( this ).text( response.lastname );
            });

            $( '.rstore-welcome-block' ).each( function () {
              $( this ).show();
            });

            $( '.rstore-login-block' ).each( function () {
              $( this ).hide();
            });
          }

          cart.updateCount( response.carttotal );
        }
      });
    }
  };

  $( document ).ready( function ( $ ) {
    cart.addItemParam();

    cart.init();

    login.init();

    $( '.rstore-add-to-cart' ).on( 'click', cart.addItemButton );
  } );
}( jQuery ));
