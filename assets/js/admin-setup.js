/* global ajaxurl, jQuery, rstore_admin_setup */

( function( $ ) {

	'use strict';

	var install = function( e ) {

		e.preventDefault();

		var $this    = $( this ),
		    $input   = $this.find( 'input' ),
		    $submit  = $this.find( 'button' ),
		    $spinner = $this.find( 'img' ),
		    data     = {
				'action': 'rstore_install',
				'nonce': rstore_admin_setup.install_nonce,
				'pl_id': $input.val()
			};

		$input.prop( 'disabled', true );
		$submit.prop( 'disabled', true );
		$spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {

			if ( response.success ) {

				window.location.replace( response.data.redirect );

				return false;

			}

			$input.prop( 'disabled', false );
			$submit.prop( 'disabled', false );
			$spinner.css( 'visibility', 'hidden' );

			window.console.log( response );

			window.alert( response.data );

		} );

	};

	$( document ).ready( function( $ ) {

		$( '#rstore-setup-form' ).on( 'submit', install );

	} );

} )( jQuery );
