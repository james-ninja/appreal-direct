<?php
/*
* AST Pro: get GLS Sell & Sned Tracking number from order note
* 
*/
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_gls_rest_insert_order_note', 10, 3 );
if ( !function_exists( 'ast_pro_gls_rest_insert_order_note' ) ) {
	function ast_pro_gls_rest_insert_order_note( $note, $request, $true ) {
		
		//check if AST is active
		if ( !function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}	
		
		//check if order note is for shiptheory
		if ( false != strpos( $note->comment_content, 'GLS-ITA' ) ) {
			
			$order_id = $request['order_id'];
			$status_shipped = get_option( 'autocomplete_gls_sell_send_italy', 1 );
			$tracking_number = ast_get_string_after( $note->comment_content, '|' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = ast_get_string_between( $note->comment_content, 'TA]', '|' );
			$tracking_provider = trim( $tracking_provider );
			
			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
		}						
	}
}
