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
		
		$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );
		$status_shipped = get_option( 'autocomplete_royalmail', 1 );

		//check if order note is for Royal Mail
		if ( false != strpos( $note->comment_content, 'https://www.royalmail.com' ) ) {
			
			$order_id = $request['order_id'];
			
			$tracking_number = ast_get_string_between( $note->comment_content, 'Your tracking number is ', 'Your order can be tracked here:' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = 'Royal Mail';
			
			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}

			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
		} else if ( false != strpos( $note->comment_content, 'Your delivery confirmation number is' ) ) {
			
			$order_id = $request['order_id'];			
			$tracking_number = ast_get_string_after( $note->comment_content, 'Your delivery confirmation number is ' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = 'Royal Mail';
			
			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
		} else if ( false != strpos( $note->comment_content, 'Sent By Courier Service: Royal Mail' ) ) {
			
			$order_id = $request['order_id'];			
			$tracking_number = ast_get_string_between( $note->comment_content, 'Tracking Number: ', 'Track' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
			$tracking_provider = 'Royal Mail';
			
			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, '', $status_shipped );
		}
	}
}
