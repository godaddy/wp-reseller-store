/* global ajaxurl, rstore, jQuery */

( function( $ ) {
	'use strict';

	var save = function( e ) {
		var $this = $( this ),
			submit = $this.find( 'button' ),
			spinner = $this.find( 'img' );

		e.preventDefault();

		$this.find( "[name='action']" ).val( 'rstore_options_save' );

		submit.prop( 'disabled', true );
		spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, $this.serialize(), function( response ) {
			submit.prop( 'disabled', false );
			spinner.css( 'visibility', 'hidden' );
			if ( response.success ) {
				return false;
			}

			// eslint-disable-next-line no-alert
			window.alert( response.data );
		} );
	};

	var importProduct = function( e ) {
		var $this = $( this ),
			submit = $this.find( 'button' ),
			spinner = $this.find( 'img' );

		e.preventDefault();

		submit.prop( 'disabled', true );
		spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, $this.serialize(), function( response ) {
			if ( response.success ) {
				window.location.replace( response.data.redirect );

				return false;
			}
			$( '#rstore-product-import-error' ).text( response.data );
		} );
		return false;
	};

	$( document ).ready( function() {
		if ( $( '#rstore-branding-info' ).length ) {
			$.ajax( {
				type: 'GET',
				url: rstore.urls.api,
			} ).done( function( response ) {
				$( '#displayName' ).text( response.displayName );
				$( '#homeUrl' ).text( response.homeUrl );
				$( '#customDomain' ).text( response.domain );
			} );
		}

		$( '#rstore-options-form' ).on( 'submit', save );
		$( '#rstore-product-import' ).on( 'submit', importProduct );
	} );
}( jQuery ) );
