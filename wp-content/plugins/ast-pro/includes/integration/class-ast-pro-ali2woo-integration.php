<?php

/* Add Tracking Information in AST meta fields when you automatically sync tracking numbers from aliexpress orders */
add_action( 'wcae_after_add_tracking_code', 'ast_pro_ali2woo_update_order_tracking', 10, 2 );

if ( !function_exists( 'ast_pro_ali2woo_update_order_tracking' ) ) {
	function ast_pro_ali2woo_update_order_tracking( $order_id, $tracking_code ) {
		
		global $wpdb;
		$default_provider = get_option( 'wc_ast_default_provider' );		
		if ( '' != $default_provider ) {
			$table = ast_pro()->shippment_provider_table();
			$shipping_provider = $wpdb->get_row( $wpdb->prepare( 'SELECT provider_name FROM %1s WHERE id= %d', $table, $default_provider ) );
		} else {
			$shipping_provider = '';
		}
		$shipping_provider = apply_filters( 'ast_ali2woo_shipping_provider', $shipping_provider );
		
		$status_shipped = get_option( 'autocomplete_ali2woo', 1 );

		$tracking_info_exist = tracking_info_exist( $order_id, $tracking_code );
		$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );
		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}
		
		ast_insert_tracking_number( $order_id, $tracking_code, $shipping_provider, 0, $status_shipped );	
	}
}
