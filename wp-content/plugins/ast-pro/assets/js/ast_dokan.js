jQuery( '#legacy_date_shipped' ).datepicker({
	dateFormat: 'yy-mm-dd'
});

jQuery(document).on("click", "#ast-show-tracking-form", function(e){
	e.preventDefault();
	var self = jQuery(this);

	self.closest('div').find('form#ast-add-shipping-tracking-form').slideDown( 300, function() {
		jQuery(this).removeClass('dokan-hide');
	});
});

jQuery(document).on("click", "#ast-dokan-cancel-tracking-note", function(e){
	e.preventDefault();
    var self = jQuery(this);

    self.closest('form#ast-add-shipping-tracking-form').slideUp( 300, function() {
        jQuery(this).addClass('dokan-hide');
    });
});

jQuery(document).on("click", "#ast-add-tracking-details", function(){
	
	var form = jQuery('#ast-add-shipping-tracking-form');
	var error;
	var tracking_provider = jQuery("#ast-add-shipping-tracking-form #tracking_provider");
	var tracking_number = jQuery("#ast-add-shipping-tracking-form #tracking_number");
	var date_shipped = jQuery("#ast-add-shipping-tracking-form #legacy_date_shipped");
			
	if( tracking_provider.val() === '' ){					
		//jQuery("#tracking_provider").siblings('.select2-container').find('.select2-selection').css('border-color','red');
        showerror(tracking_provider);
		error = true;
	} else{
		//jQuery("#tracking_provider").siblings('.select2-container').find('.select2-selection').css('border-color','#ddd');
		hideerror(tracking_provider);
	}
	
	if( tracking_number.val() === '' ){				
		showerror(tracking_number);
		error = true;
	} else{		
		hideerror(tracking_number);
	}
	
	if( date_shipped.val() === '' ){				
		showerror(date_shipped);
		error = true;
	} else{		
		hideerror(date_shipped);
	}
	
	if(jQuery('#ast-add-shipping-tracking-form .enable_tracking_per_item').prop("checked") == true){
		if(jQuery("tr").hasClass("ASTProduct_row")){
			var qty = false;
			jQuery(".ASTProduct_row").each(function(index){
				var ASTProduct_qty = jQuery(this).find('input[type="number"]').val();
				if(ASTProduct_qty > 0){
					qty = true;		
					return false;					
				}
			});						
		}
	}

	if(qty == false){
		jQuery('.qty_validation').show();
		return false;
	} else{
		jQuery('.qty_validation').hide();
	} 
	
	if(error == true){
		return false;
	}	
	
	jQuery("#ast-add-shipping-tracking-form").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}	
    });
	jQuery.ajax({
		url: dokan.ajaxurl,		
		data: form.serialize(),
		type: 'POST',		
		success: function(response) {
			jQuery( '#ast-add-shipping-tracking-form' ).unblock();			
			location.reload();
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

jQuery(document).on("click", ".ast-dokan-delete-tracking", function(){
	var tracking_id = jQuery( this ).attr( 'rel' );

	jQuery( '#tracking-item-' + tracking_id ).block({
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	});

	var data = {
		action:      'ast_dokan_delete_item',
		order_id:    jQuery( '#order_id' ).val(),
		tracking_id: tracking_id,
		security:    jQuery( '#ast_dona_delete_tracking_nonce' ).val()
	};

	jQuery.ajax({
		url: dokan.ajaxurl,		
		data: data,
		type: 'POST',		
		success: function(response) {
			jQuery( '#tracking-item-' + tracking_id ).unblock();		
			if ( response != '-1' ) {
				jQuery( '#tracking-item-' + tracking_id ).remove();
			}
			location.reload();
		},
		error: function(response) {
			console.log(response);			
		}
	});

	return false;
});

jQuery(document).ready(function() {
	jQuery('#tracking_provider').select2({
		matcher: modelMatcher
	});
});

function showerror(element){
	element.css("border","1px solid red");
}
function hideerror(element){
	element.css("border","1px solid #ddd");
}

function modelMatcher (params, data) {				
	data.parentText = data.parentText || "";
	
	// Always return the object if there is nothing to compare
	if (jQuery.trim(params.term) === '') {
		return data;
	}
	
	// Do a recursive check for options with children
	if (data.children && data.children.length > 0) {
		// Clone the data object if there are children
		// This is required as we modify the object to remove any non-matches
		var match = jQuery.extend(true, {}, data);
	
		// Check each child of the option
		for (var c = data.children.length - 1; c >= 0; c--) {
		var child = data.children[c];
		child.parentText += data.parentText + " " + data.text;
	
		var matches = modelMatcher(params, child);
	
		// If there wasn't a match, remove the object in the array
		if (matches == null) {
			match.children.splice(c, 1);
		}
		}
	
		// If any children matched, return the new object
		if (match.children.length > 0) {
		return match;
		}
	
		// If there were no matching children, check just the plain object
		return modelMatcher(params, match);
	}
	
	// If the typed-in term matches the text of this term, or the text from any
	// parent term, then it's a match.
	var original = (data.parentText + ' ' + data.text).toUpperCase();
	var term = params.term.toUpperCase();
	
	
	// Check if the text contains the term
	if (original.indexOf(term) > -1) {
		return data;
	}
	
	// If it doesn't contain the term, don't return anything
	return null;
}
