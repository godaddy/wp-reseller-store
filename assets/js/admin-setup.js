/* global ajaxurl, jQuery, rstore_admin_setup */

( function( $ ) {
	'use strict';

	var activate = function( e ) {
		var rccSite = rstore_admin_setup.rcc_site;
		var data = {
				action: 'rstore_install',
				nonce: rstore_admin_setup.install_nonce,
				site: rstore_admin_setup.install_site,
				admin: rstore_admin_setup.install_admin_url,
			},
			query = $.param( data );

		if ( e.target.id === 'rstore-activate-other' ) {
			rccSite = 'https://reseller.secureserver.net';
		}

		e.preventDefault();
		window.location = rccSite + '/activate?' + query;
	};

	var install = function() {
		var data = {
			action: 'rstore_install',
			nonce: rstore_admin_setup.install_nonce,
			pl_id: rstore_admin_setup.install_plid,
		};

		$( '#rstore-activate-gd' ).prop( 'disabled', true );
		$( '#rstore-activate-other' ).prop( 'disabled', true );
		$( '.rstore-status' ).css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				window.location.replace( response.data.redirect );

				return false;
			}

			$( '#rstore-activate-gd' ).prop( 'disabled', false );
			$( '#rstore-activate-other' ).prop( 'disabled', false );
			$( '.rstore-status' ).css( 'visibility', 'hidden' );
			$( '.rstore-error' ).text( response.data );
		} );
	};

	$( document ).ready( function( ) {
		// check if we are on the admin setup page
		if ( ! window.rstore_admin_setup ) {
			return;
		}

		$( '.rstore-setup-body' ).css( 'display', 'block' ); // Form is hidden by default
		$( '#rstore-activate-gd' ).on( 'click', activate );
		$( '#rstore-activate-other' ).on( 'click', activate );

		if ( rstore_admin_setup.install_error ) {
			$( '.rstore-error' ).text( rstore_admin_setup.install_error );

			return;
		}

		if ( rstore_admin_setup.install_plid ) {
			install();
		}
	} );
}( jQuery ) );
