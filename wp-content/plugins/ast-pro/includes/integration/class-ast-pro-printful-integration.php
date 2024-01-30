<?php
/*
* AST Pro: get Printful Tracking number from order note
* Order note text - <b>The following items of your order have been shipped</b><br />Glossy mug – 1x<br /><b>Shipment information</b><br />Carrier: USPS First Class Mail<br />Tracking number:  <a href="https://myorders.co/tracking/42013854/9200190256038125676813">9200190256038125676813</a>
*/
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_action_printful_woocommerce_rest_insert_order_note', 10, 3 );
if ( !function_exists( 'ast_pro_action_printful_woocommerce_rest_insert_order_note' ) ) {
	function ast_pro_action_printful_woocommerce_rest_insert_order_note( $note, $request, $true ) {
		
		//check if AST is active
		if ( !function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}	
		
		//check if order note is for shiptheory
		if ( false != strpos( $note->comment_content, 'following items of your order have been shipped' ) ) {
			
			$order_id = $request['order_id'];
			$status_shipped = get_option( 'autocomplete_printful', 1 );
			$tracking_number = ast_get_string_between( $note->comment_content, '">', '</a>' );
			$tracking_number = str_replace( '.', '', $tracking_number );
			$tracking_number = str_replace( ' ', '', $tracking_number );
					
			$tracking_provider = ast_get_string_between_html( $note->comment_content, 'Carrier:', '<' );
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
