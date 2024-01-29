<?php
/*
* AST Pro: get Royal Mail Tracking number from order note
* 
*/
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_action_woocommerce_rest_insert_order_note', 10, 3 );
if ( !function_exists( 'ast_pro_action_woocommerce_rest_insert_order_note' ) ) {
	function ast_pro_action_woocommerce_rest_insert_order_note( $note, $request, $true ) {
		
		//check if AST is active
		if ( !function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}	
		
		//check if order note is for Royal Mail
		if ( false != strpos( $note->comment_content, 'https://www.royalmail.com' ) ) {
			
			$order_id = $request['order_id'];
			$status_shipped = 1;
			$tracking_number = ast_get_string_between( $note->comment_content, 'Your tracking number is ', 'Your order can be tracked here:' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = 'Royal Mail';
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
		}		
	}
}

/*
* AST: get specific string between two string
*/
if ( !function_exists( 'ast_get_string_between' ) ) {
	function ast_get_string_between( $input, $start, $end ) {
		$substr = substr( $input, strlen( $start ) + strpos( $input, $start ), ( strlen( $input ) - strpos( $input, $end ) ) * ( -1 ) );
		return $substr;
	}
}
