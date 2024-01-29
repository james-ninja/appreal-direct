(function ( $ ) {
	"use strict";
	jQuery( document ).ready( function( $ ) {

		$( "#woocommerce_usaepaytransapi_source_key1" ).focusin(function() {
				if($(this).attr("type") == "password"){ $(this).attr("type", "text");}else{$(this).attr("type", "password");}
			});
		$( "#woocommerce_usaepaytransapi_source_key1" ).focusout(function() {
			if($(this).attr("type") == "text"){ $(this).attr("type", "password");}else{$(this).attr("type", "text");}
		});

		$( "#woocommerce_usaepaytransapi_pin1" ).focusin(function() {
			if($(this).attr("type") == "password"){ $(this).attr("type", "text");}else{$(this).attr("type", "password");}
		});
		$( "#woocommerce_usaepaytransapi_pin1" ).focusout(function() {
			if($(this).attr("type") == "text"){ $(this).attr("type", "password");}else{$(this).attr("type", "text");}
		});

		// SandBox Keys
		$( "#woocommerce_usaepaytransapi_source_key2" ).focusin(function() {
			if($(this).attr("type") == "password"){ $(this).attr("type", "text");}else{$(this).attr("type", "password");}
		});
		$( "#woocommerce_usaepaytransapi_source_key2" ).focusout(function() {
			if($(this).attr("type") == "text"){ $(this).attr("type", "password");}else{$(this).attr("type", "text");}
		});

		$( "#woocommerce_usaepaytransapi_pin2" ).focusin(function() {
			if($(this).attr("type") == "password"){ $(this).attr("type", "text");}else{$(this).attr("type", "password");}
		});
		$( "#woocommerce_usaepaytransapi_pin2" ).focusout(function() {
			if($(this).attr("type") == "text"){ $(this).attr("type", "password");}else{$(this).attr("type", "text");}
		});
		
	});
	

}( jQuery ) );