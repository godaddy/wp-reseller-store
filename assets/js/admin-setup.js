/* jQuery */

( function( $ ) {

	$( '#rstore-setup-form' ).on( 'submit', function( e ) {

		e.preventDefault();

		var $input   = $( this ).find( 'input' ),
		    $submit  = $( this ).find( 'button' ),
		    $spinner = $( this ).find( 'img' ),
		    data     = {
				'action': 'rstore_install',
				'pl_id': $input.val()
			};

		$input.prop( 'disabled', true );
		$submit.prop( 'disabled', true );
		$spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {

			window.location.replace( response.data.redirect );

		} )
		.fail( function( xhr, status, error ) {

			$input.val( '' ).prop( 'disabled', false );
			$submit.prop( 'disabled', false );
			$spinner.css( 'visibility', 'hidden' );

			window.alert( error );

		} );

	} );

} )( jQuery );
