( function( $ ) {

	$(document).on( 'ready', function () {

		$( 'input[name="wcbvp_variations_data[disable_purchasing]"]' ).on( 'change', function ( e ) {

			var $inputs = jQuery( 'input[name="wcbvp_variations_data[show_stock]"]' );
			if ( e.currentTarget.checked ) {
				$inputs.closest('fieldset').hide();
			} else {
				$inputs.closest('fieldset').show();
			}

		} );

		$( 'input[name="wcbvp_variations_data[variation_images]"]' ).on( 'change', function ( e ) {

			var $inputs = jQuery( 'input[name="wcbvp_variations_data[use_lightbox]"]' );
			if ( e.currentTarget.checked ) {
				$inputs.closest('tr').show();
			} else {
				$inputs.closest('tr').hide();
			}

		} ).change();

	} );

} )( jQuery );