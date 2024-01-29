(function( $, window, document, undefined ) {
	"use strict";
	
	function validate_is_bulk_variation( variations_count, source ) {
				
		var is_bulk_variation = false;
		var attributes_qtt    = 0;
		var product_type = jQuery( '#product-type' ).val(); 
		if ( product_type == 'variable' ) {
			
			jQuery( ".woocommerce_attribute_data .enable_variation input" ).each(function( index ) {
				if ( jQuery( this ).is( ":checked" ) ) {
					attributes_qtt++;
				}
			} );
			
			if ( jQuery( "#variable_product_options .woocommerce_variations .woocommerce_variation" ).length ) {
				variations_count = 0;
				jQuery( "#variable_product_options .woocommerce_variations .woocommerce_variation" ).each(function( index ) {
					variations_count++; 
				} );
			}
			
			if ( source == 'removed' ) {
				variations_count--;
			} else if ( source == 'added' ) {
				variations_count++;
			}
					
			if ( ( attributes_qtt == 1 || attributes_qtt == 2 ) && variations_count > 0 ) {
				is_bulk_variation = true;
			}
		}
					
		if ( !is_bulk_variation ) {
			
			jQuery( '.show_if_bulk_variations' ).hide();
		}
		else {
			jQuery( '.show_if_bulk_variations' ).show();
		}
		
		return variations_count;
	}
	
	var is_bulk_variation = wcbvp_data.is_bulk_variation;
	var variations_count  = wcbvp_data.variations_count;
	
	jQuery( '#variable_product_options' ).on( 'woocommerce_variations_added', function() {
		variations_count = validate_is_bulk_variation( variations_count, 'added' );
	} );
	
	jQuery( '#woocommerce-product-data' ).on( 'woocommerce_variations_removed', function() {
		variations_count = validate_is_bulk_variation( variations_count, 'removed' );
	} );
		
	jQuery( "#product-type" ).change(function() {
		variations_count = validate_is_bulk_variation( variations_count );
	} );
	
	jQuery( "#variable_product_options" ).on( 'reload', function() {
		variations_count = validate_is_bulk_variation( variations_count );
		
		var attributes     = {};
		var attributes_qtt = 0;
		
		jQuery( "#product_attributes .woocommerce_attribute" ).each(function( index ) {
			var tx_key   = jQuery( this ).data( 'taxonomy' );
			var tx_value = jQuery( this ).find( 'strong.attribute_name' ).text();
			
			if ( tx_key == '' ) {
				tx_key = tx_value;
			}
			
			if ( jQuery( this ).find( ".enable_variation input" ).is( ":checked" ) ) {
				attributes[tx_key] = tx_value;
				attributes_qtt++;
			}
		} );
		
		jQuery( '#wcbvp_variations_structure_rows' )
			.find('option')
			.remove()
			.end()
			.append('<option>Select attribute</option>');
			
		jQuery( '#wcbvp_variations_structure_columns' )
			.find('option')
			.remove()
			.end()
			.append('<option>Select attribute</option>');
					
		jQuery.each( attributes, function( key, value ) {
			
			 jQuery( '#wcbvp_variations_structure_columns' )
				  .append( jQuery( '<option>', { value : key } )
				  .text( value ) );
		} ); 
		
		jQuery.each( attributes, function( key, value ) {
			 jQuery( '#wcbvp_variations_structure_rows' )
				  .append( jQuery( '<option>', { value : key })
				  .text( value ) );
		} ); 
				
		if ( attributes_qtt == 1 ) {
			
			if ( wcbvp_data.settings.variation_attribute == 'vert' ) {
				jQuery( "#wcbvp_variations_structure_rows" ).val( jQuery( "#wcbvp_variations_structure_rows option:last" ).val() );
			} else {
				jQuery( "#wcbvp_variations_structure_columns" ).val( jQuery( "#wcbvp_variations_structure_columns option:last" ).val() );
			}
		}
	} );
	
	jQuery( '#variable_product_options' ).on( 'woocommerce_variations_save_variations_button', function() {
		variations_count = validate_is_bulk_variation( variations_count );	    
	} );
		
	if ( !is_bulk_variation ) {
		
		jQuery( '.show_if_bulk_variations' ).hide();
	}
	else {
		jQuery( '.show_if_bulk_variations' ).show();
	}
			
	if ( jQuery( 'input[type=checkbox][name="' + wcbvp_data.option_variation_data  + '_override' + '"]' ).is( ":checked" ) ) {
		jQuery( "#" + wcbvp_data.option_variation_data + '_hide_add_to_cart_div' ).hide();
	} else {
		jQuery( "#" + wcbvp_data.option_variation_data + '_hide_add_to_cart_div' ).show();
	}
	
	jQuery( 'input[type=checkbox][name="' + wcbvp_data.option_variation_data  + '_override' + '"]' ).change( function() {
		
		if ( this.checked ) {
			jQuery( "#" + wcbvp_data.option_variation_data + '_hide_add_to_cart_div' ).hide();
		} else {
			jQuery( "#" + wcbvp_data.option_variation_data + '_hide_add_to_cart_div' ).show();
		}
	} );

	$( 'input[name="wcbvp_variations_data[disable_purchasing]"]' ).on( 'change', function ( e ) {

		var $inputs = jQuery( 'input[name="wcbvp_variations_data[show_stock]"]' );
		if ( e.currentTarget.checked ) {
			$inputs.closest('p').hide();
		} else {
			$inputs.closest('p').show();
		}

	} );

	$( 'input[name="wcbvp_variations_data[variation_images]"]' ).on( 'change', function ( e ) {

		var $inputs = jQuery( 'input[name="wcbvp_variations_data[use_lightbox]"]' );
		if ( e.currentTarget.checked ) {
			$inputs.closest('p').show();
		} else {
			$inputs.closest('p').hide();
		}

	} ).change();
		
})( jQuery, window, document );