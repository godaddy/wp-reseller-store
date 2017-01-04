/* global jQuery */

( function( $ ) {

	'use strict';

	var $default = $( '#rstore-permalink-structure-default' ),
	    $custom  = $( '#rstore-permalink-structure-custom' ),
	    $input   = $( '#rstore-product-base' ),
	    $inputs  = $( '.rstore-permalink-structure' ).find( 'input' );

	var permalinks = {

		init: function() {

			var hasStructure = ( '' !== $( this ).val() );

			if ( ! hasStructure ) {

				$default.click();

			}

			$( '#rstore-default-example' ).toggle( ! hasStructure );
			$( '#rstore-custom-example' ).toggle( hasStructure );

			$inputs.prop( 'disabled', ! hasStructure );

		},

		radioSelect: function() {

			var value = $( this ).val();

			$input.attr( 'placeholder', value );

			if ( ! value ) {

				$input.focus();

			}

			if ( value ) {

				$input.val( '' );

			}

		},

		forceCustom: function() {

			$custom.click();

		},

		forceDefault: function() {

			if ( ! $.trim( $input.val() ) || $input.val() === $default.val() ) {

				$default.click();

			}

		}

	};

	$( document ).ready( function( $ ) {

		$( '.permalink-structure input' ).on( 'change', permalinks.init );
		$( 'input[name="rstore_permalink_structure"]' ).on( 'change', permalinks.radioSelect );
		$( '#rstore-product-base' ).on( 'focus', permalinks.forceCustom );
		$( '#rstore-product-base' ).on( 'blur', permalinks.forceDefault );

	} );

} )( jQuery );
