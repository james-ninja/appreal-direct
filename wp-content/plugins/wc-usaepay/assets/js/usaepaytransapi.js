(function ( $ ) {
	"use strict";
	function usaepaytransapi_event_handler(){

		var $checkout_form 		= $( 'form.checkout, form#order_review, form#add_payment_method' ),
			$prefix_ 			= 'usaepaytransapi',
	    	$cardnumber    		= $( '#'+ $prefix_ +'-card-number' ).val(),
	        $card_expiry   		= $.payment.cardExpiryVal( $( '#'+ $prefix_ +'-card-expiry' ).val() ),
	        $card_cvc 			= $("#" + $prefix_ + '-card-cvc').val(),
	        $card_expiry_month 	= $card_expiry.month,
	        $card_expiry_year 	= $card_expiry.year;

	        $cardnumber 		= $cardnumber.replace( /\s/g, '' );
	        $card_cvc 			= $card_cvc.replace( /\s/g, '' );

	    var $data = {
	    		'cardnumber' : $cardnumber,
	    		'card_cvc'   : $card_cvc,
	    		'expiry_m'   : $card_expiry_month,
	    		'expiry_y'   : $card_expiry_year
	     };   

	     usaepaytransapi_handle_build_card($data);

	     return false;

	}

	function usaepaytransapi_handle_build_card( params ) {
   
	   var	$form  			= $( 'form.checkout, form#order_review, form#add_payment_method' ),
	       	$usaepaytransapi  	= $( "#wc-usaepaytransapi-cc-form" );

			$usaepaytransapi.append( '<input type="hidden" class="usaepaytransapi-cardnumber" name="usaepaytransapi-cardnumber" value="' + params.cardnumber + '"/>' );
			$usaepaytransapi.append( '<input type="hidden" class="usaepaytransapi-card_cvc" name="usaepaytransapi-card_cvc" value="' + params.card_cvc + '"/>' );
			$usaepaytransapi.append( '<input type="hidden" class="usaepaytransapi-expiry_m" name="usaepaytransapi-expiry_m" value="' + params.expiry_m + '"/>' );
			$usaepaytransapi.append( '<input type="hidden" class="usaepaytransapi-expiry_y" name="usaepaytransapi-expiry_y" value="' + params.expiry_y + '"/>' );
	    
	    $form.submit();

	 }

	jQuery( document ).ready( function( $ ) {

		$( document.body ).on( 'checkout_error', function () {
	       $( '.usaepaytransapi-cardnumber' ).remove();
	       $( '.usaepaytransapi-card_cvc' ).remove();
	       $( '.usaepaytransapi-expiry_m' ).remove();
	       $( '.usaepaytransapi-expiry_y' ).remove();
	    });

	    $( "body" ).on( 'click', '#place_order', function( e ){
	        if (  $( '#payment_method_usaepaytransapi' ).is( ':checked' ) ) {
		          usaepaytransapi_event_handler();
		          return false;    
	        }

	    });

	    $( 'form.checkout, form#order_review, form#add_payment_method' ).on( 'change', '#wc-usaepaytransapi-cc-form input', function() {
	      	$( '.usaepaytransapi-cardnumber' ).remove();
	       	$( '.usaepaytransapi-card_cvc' ).remove();
	       	$( '.usaepaytransapi-expiry_m' ).remove();
	       	$( '.usaepaytransapi-expiry_y' ).remove();
	    });

	});


}( jQuery ) );