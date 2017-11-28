/* global ajaxurl, jQuery */

( function( $ ) {
	'use strict';

	$( document ).ready( function( $ ) {
		$('#rstore-update-error').children( ['button.notice-dismiss'] ).click(function (event) {
			event.preventDefault();
			var $this = $(this);

			var data = {
				'action': 'rstore_admin_notice',
				'nonce': rstore_admin_notice.nonce
			};

			$.post(ajaxurl, data);
		});
	} );
}( jQuery ));
