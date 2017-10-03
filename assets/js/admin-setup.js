/* global ajaxurl, jQuery, rstore_admin_setup */

( function( $ ) {

	'use strict';

	var activate = function( e ) {

		e.preventDefault();

		var data  = {
			'action': 'rstore_install',
			'nonce' : rstore_admin_setup.install_nonce,
			'site'  : rstore_admin_setup.install_site,
			'admin' : rstore_admin_setup.install_admin_url
		},
			query = $.param( data );

		window.location = rstore_admin_setup.rcc_site + '/activate?' + query;

	};

	var skip = function( e ) {

		e.preventDefault();

		var data = {
			'action'         : 'rstore_install',
			'nonce'          : rstore_admin_setup.install_nonce,
			'skip_activation': true
		};

		$( '#rstore-activate' ).prop( 'disabled', true );
		$( '.rstore-status' ).css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {

			if ( response.success ) {

				window.location.replace( response.data.redirect );

				return false;

			}

			$( '#rstore-activate' ).prop( 'disabled', false );
			$( '.rstore-status' ).css( 'visibility', 'hidden' );
			$( '.rstore-error' ).text( response.data );

		} );

		return false;

	};

	var install = function() {

		var data = {
			'action': 'rstore_install',
			'nonce' : rstore_admin_setup.install_nonce,
			'pl_id' : rstore_admin_setup.install_plid
		};

		$( '#rstore-activate' ).prop( 'disabled', true );
		$( '.rstore-status' ).css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {

			if ( response.success ) {

				window.location.replace( response.data.redirect );

				return false;

			}

			$( '#rstore-activate' ).prop( 'disabled', false );
			$( '.rstore-status' ).css( 'visibility', 'hidden' );
			$( '.rstore-error' ).text( response.data );

		} );

	};

	var republish = function( e ) {
    e.preventDefault();



    var $this    = $( this ),
        $form    = $this.closest( 'form' );

    if ($form.length > 0) {
    	$form[0].children.hiddenaction.value='rstore_republish';
    }

    // if ( $this.attr( 'data-loading' ) ) {

    //   return false;

    // }

    // $this.attr( 'data-loading', 'true' );

    // $form.find( '.rstore-message' ).empty();
    // $form.find( '.rstore-loading' ).removeClass('rstore-loading-hidden');

    // cart.addItem( id, qty, redirect, $form );

  };

	$( document ).ready( function( $ ) {

		$( '.rstore-setup-body' ).css( 'display', 'block' ); // Form is hidden by default
		$( '#rstore-setup-form' ).on( 'submit', activate );
		$( '#rstore-skip-activate' ).on( 'click', skip );
		// $( '#republish-product' ).on( 'change', republish );

		if ( ! window.rstore_admin_setup ) {
			return;
		}

		if ( rstore_admin_setup.install_error ) {

			$( '.rstore-error' ).text( rstore_admin_setup.install_error );

			return;

		}

		if ( rstore_admin_setup.install_plid ) {

			install();

		}

	} );

} )( jQuery );
