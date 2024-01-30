<?php

/* Hook into WooCommerce action and filter */
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_quickbooks_commerce_integrations', 10, 3 );

function ast_pro_quickbooks_commerce_integrations( $note, $request, $true ) {
	
	// check if the Advanced Shipment Tracking (AST) plugin is active
	if ( !function_exists( 'ast_insert_tracking_number' ) ) {
		return;
	}
	
	$order_id = $request['order_id'];
	$status_shipped = get_option( 'autocomplete_quickbooks_commerce', 1 );
	$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

	if ( false != strpos( $note->comment_content, 'following items of your order have been shipped' ) ) {
		return;
	}

	//check if the order note contains FedEx
	if ( strpos( $note->comment_content, 'Carrier: FedEx' ) !== false ) {
		
		$tracking_number = ast_get_string_between( $note->comment_content, 'Tracking Number:', 'Tracking URL:' );
		$tracking_provider = 'FedEx';
		
		$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}

		ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
	}
	
	//check if the order note contains UPS
	if ( strpos( $note->comment_content, 'Carrier: UPS' ) !== false ) {
		
		$tracking_number = ast_get_string_between( $note->comment_content, 'Tracking Number:', 'Tracking URL:' );
		$tracking_provider = 'UPS';
		
		$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}

		ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
	}
	
	//check if the order note contains USPS
	if ( strpos( $note->comment_content, 'Carrier: USPS' ) !== false ) {
		
		$tracking_number = ast_get_string_between( $note->comment_content, 'Tracking Number:', 'Tracking URL:' );
		$tracking_provider = 'USPS';
		
		$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}
		
		ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
	}
}
