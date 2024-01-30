jQuery(document).on("click", "#decrease", function(){			
	var input = jQuery(this).next(".ast_product_number");			
	var value = jQuery(this).next(".ast_product_number").val();
	var this_element = jQuery(this);
	
	if(value > input.attr('min')) {
		value = isNaN(value) ? 0 : value;				
		value < 1 ? value = 1 : '';
		value--;		
		jQuery(input).val(value);
	}
	
	change_mark_order_as_checkbox(this_element);
});

jQuery(document).on("click", "#increase", function(){			
	var input = jQuery(this).prev(".ast_product_number");			
	var value = jQuery(this).prev(".ast_product_number").val();
	var this_element = jQuery(this);

	if(value < input.attr('max')) {
		value = isNaN(value) ? 0 : value;
		value++;						
		jQuery(input).val(value);
	}

	change_mark_order_as_checkbox(this_element);
	
});

jQuery(document).on("change", ".enable_tracking_per_item", function(){	
	if(jQuery(this).prop("checked") == true){
		jQuery( this ).closest('div').find( ".ast-product-table" ).show();					
	} else{
		jQuery( this ).closest('div').find( ".ast-product-table" ).hide();				
	}
});

jQuery(document).on( "input", ".ast_product_number", function(){	
	var this_element = jQuery(this);
	change_mark_order_as_checkbox(this_element);
});

function change_mark_order_as_checkbox( this_element ) {
	var ast_product_number = jQuery(this_element).closest('.ast-product-table').find('.ast_product_number');
	var total_available_qty = parseInt( jQuery('.total_qty').val() );	
	
	var total_qty = 0;
	jQuery( ast_product_number ).each(function( index ) {
		total_qty = parseInt(total_qty) + parseInt(jQuery( this ).val());		
	});	
	
	if( total_available_qty > total_qty ){
		jQuery('.mark_shipped_checkbox').prop('checked', false);
		jQuery("input[value=change_order_to_partial_shipped").prop('checked', true);
	} else {
		jQuery('.mark_shipped_checkbox').prop('checked', false);
		jQuery("input[value=change_order_to_shipped").prop('checked', true);
	}
}