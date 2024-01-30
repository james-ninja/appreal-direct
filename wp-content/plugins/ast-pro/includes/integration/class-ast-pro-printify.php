<?php
/*
* AST Pro: get Printify Tracking number from order note
* 
*/
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_printify_woocommerce_rest_insert_order_note', 10, 3 );
if ( !function_exists( 'ast_pro_printify_woocommerce_rest_insert_order_note' ) ) {
	function ast_pro_printify_woocommerce_rest_insert_order_note( $note, $request, $true ) {
		
		//check if AST is active
		if ( !function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}	
		
		global $wpdb;
		$default_provider = get_option( 'wc_ast_default_provider' );
		

		if ( '' != $default_provider ) {
			
			$table = ast_pro()->shippment_provider_table();
			$provider_name = $wpdb->get_row( $wpdb->prepare( 'SELECT provider_name FROM %1s WHERE id= %d', $table, $default_provider ) );
			$status_shipped = get_option( 'autocomplete_printify', 1 );	
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			//check if order note is for Royal Mail
			if ( false != strpos( $note->comment_content, "I'm A Ship Carpenter" ) ) {

				$order_id = $request['order_id'];				
				$tracking_number = ast_get_string_between( $note->comment_content, 'TN: ', 'URL: ' );
				$tracking_number = str_replace( '.', '', $tracking_number );
				$tracking_number = str_replace( ' ', '', $tracking_number );			

				$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
				if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
					return;
				}

				ast_insert_tracking_number( $order_id, $tracking_number, $provider_name->provider_name, '', $status_shipped );
			}

			//check if order note is for Royal Mail
			if ( false != strpos( $note->comment_content, 'shipped:' ) ) {

				$order_id = $request['order_id'];				
				$tracking_number = ast_get_string_between( $note->comment_content, 'TN: ', 'URL: ' );
				$tracking_number = str_replace( '.', '', $tracking_number );
				$tracking_number = str_replace( ' ', '', $tracking_number );			
				
				$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
				if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
					return;
				}
				
				ast_insert_tracking_number( $order_id, $tracking_number, $provider_name->provider_name, '', $status_shipped );
			}
		}
		
	}
}
