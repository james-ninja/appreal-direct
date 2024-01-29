<?php

/* Hook into WooCommerce action and filter */
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_quickbooks_commerce_integrations', 10, 3 );

function ast_pro_quickbooks_commerce_integrations( $note, $request, $true ) {
	
	// check if the Advanced Shipment Tracking (AST) plugin is active
	if ( !function_exists( 'ast_insert_tracking_number' ) ) {
		return;
	}
	
	$order_id = $request['order_id'];
	$status_shipped = 1;
	
	//check if the order note contains FedEx
	if ( strpos( $note->comment_content, 'Carrier: FedEx' ) !== false ) {
		
		$tracking_number = get_string_between( $note->comment_content, 'Tracking Number:', 'Tracking URL:' );
		$tracking_provider = 'FedEx';
		
		ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
	}
	
	//check if the order note contains UPS
	if ( strpos( $note->comment_content, 'Carrier: UPS' ) !== false ) {
		
		$tracking_number = get_string_between( $note->comment_content, 'Tracking Number:', 'Tracking URL:' );
		$tracking_provider = 'UPS';
		
		ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
	}
	
	//check if the order note contains USPS
	if ( strpos( $note->comment_content, 'Carrier: USPS' ) !== false ) {
		
		$tracking_number = get_string_between( $note->comment_content, 'Tracking Number:', 'Tracking URL:' );
		$tracking_provider = 'USPS';
		
		ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
	}
}

/*
* AST: get specific string between two string
*/
if ( !function_exists( 'get_string_between' ) ) {
	function get_string_between( $input, $start, $end ) {
		$substr = substr( $input, strlen( $start ) + strpos( $input, $start ), ( strlen( $input ) - strpos( $input, $end ) ) * ( -1 ) );
		return $substr;
	}
}
