<?php

/* Add Tracking Information in AST meta fields when you automatically sync tracking numbers from aliexpress orders */
add_action( 'wcae_after_add_tracking_code', 'ast_pro_ali2woo_update_order_tracking', 10, 2 );

if ( !function_exists( 'ast_pro_ali2woo_update_order_tracking' ) ) {
	function ast_pro_ali2woo_update_order_tracking( $order_id, $tracking_code ) {
		
		$default_provider = get_option( 'wc_ast_default_provider' );
			
		if ( class_exists( 'AST_Pro_Actions' ) ) {			
			
			if ( function_exists( 'ast_insert_tracking_number' ) && function_exists( 'ast_get_tracking_items' ) ) {
		
				$tracking_items = ast_get_tracking_items( $order_id );
				$tracking_exist = array_search( $tracking_code, array_column( $tracking_items, 'tracking_number'  ) );
				
				if ( false === $tracking_exist ) {
					ast_insert_tracking_number( $order_id, $tracking_code, $default_provider, date_i18n( 'Y-m-d', current_time( 'timestamp' ) ), 1 );	
				}									
			}
		}
	
	}
}
