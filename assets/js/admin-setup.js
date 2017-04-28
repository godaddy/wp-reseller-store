/* global ajaxurl, jQuery, rstore_admin_setup */

( function( $ ) {

	'use strict';

	var install = function( e ) {

		e.preventDefault();

		var data     = {
				'action': 'rstore_install',
				'nonce': rstore_admin_setup.install_nonce,
				'site': rstore_admin_setup.install_site
			},
			query = $.param(data);

		window.location = "https://reseller.dev-godaddy.com/activate?"+query;

	};

	$( document ).ready( function( $ ) {

		$( '.rstore-setup-body' ).css( 'display', 'block' ); // Form is hidden by default

		$( '#rstore-setup-form' ).on( 'submit', install );

	} );

} )( jQuery );
