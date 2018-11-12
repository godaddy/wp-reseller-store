/* global ajaxurl, rstore_admin_notice, jQuery */

( function( $ ) {
	'use strict';

	$( document ).ready( function( ) {
		$( '#rstore-update-error' ).children( [ 'button.notice-dismiss' ] ).click( function( event ) {
			var data = {
				action: 'rstore_dismiss_admin_notice',
				nonce: rstore_admin_notice.nonce,
			};
			event.preventDefault();

			$.post( ajaxurl, data );
		} );
	} );
}( jQuery ) );
