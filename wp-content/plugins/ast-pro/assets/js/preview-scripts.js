( function( $ ) {
	$('.hide').hide();
    /* Hide/Show Header */
	
	wp.customize( 'tracking_info_settings[header_text_change]', function( value ) {		
		value.bind( function( header_text ) {			
			if( header_text ){
				$( '.header_text' ).text(header_text);
			} else{
				$( '.header_text' ).text('Tracking Information');
			}			
		});
	});
	
	wp.customize( 'tracking_info_settings[additional_header_text]', function( value ) {		
		value.bind( function( additional_header_text ) {
			if( additional_header_text ){
				$( '.addition_header' ).text(additional_header_text);
			} else{
				$( '.addition_header' ).text('');
			}			
		});
	});
	
	wp.customize( 'woocommerce_customer_partial_shipped_order_settings[heading]', function( value ) {		
		value.bind( function( wcast_partial_shipped_email_heading ) {
					
			var str = wcast_partial_shipped_email_heading;
			var res = str.replace("{site_title}", wcast_preview.site_title);
			
			var res = res.replace("{order_number}", wcast_preview.order_number);
				
			if( wcast_partial_shipped_email_heading ){				
				$( '#header_wrapper h1' ).text(res);
			} else{
				$( '#header_wrapper h1' ).text('');
			}			
		});
	});
	
} )( jQuery );