<?php
/*
* AST Pro: get PostNL Tracking number from order note
* 
*/
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_picqer_woocommerce_rest_insert_order_note', 10, 3 );
if ( !function_exists( 'ast_pro_picqer_woocommerce_rest_insert_order_note' ) ) {
	function ast_pro_picqer_woocommerce_rest_insert_order_note( $note, $request, $true ) {
		
		//check if AST is active
		if ( !function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}	
		
		$status_shipped = get_option( 'autocomplete_picqer', 1 );
		$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

		//check if order note is for PostNL
		if ( false != strpos( $note->comment_content, 'https://postnl.nl' ) ) {
			
			$order_id = $request['order_id'];			
			$tracking_number = ast_get_string_after( $note->comment_content, 'Tracking code: ' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = 'PostNL';
			
			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}

			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, 0, $status_shipped );
		}
		
		//check if order note is for DHLParcel NL
		if ( false != strpos( $note->comment_content, 'https://www.dhlparcel.nl' ) ) {
			
			$order_id = $request['order_id'];			
			$tracking_number = ast_get_string_after( $note->comment_content, 'Tracking code: ' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = 'DHLParcel NL';
			
			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, 0, $status_shipped );
		}
	}
}
