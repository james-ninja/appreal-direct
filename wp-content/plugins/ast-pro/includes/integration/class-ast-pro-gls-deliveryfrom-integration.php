<?php

add_action( 'update_postmeta', 'add_gls_tracking_details', 10, 4 );
add_action( 'added_post_meta', 'add_gls_tracking_details', 10, 4 );

if ( !function_exists( 'add_gls_tracking_details' ) ) {
	function add_gls_tracking_details( $meta_id, $order_id, $meta_key, $meta_value ) {
		
		if ( 'gls_order_parcel_number' == $meta_key ) {
		
			global $wpdb;
			$default_provider = get_option( 'wc_ast_default_provider' );
			if ( '' != $default_provider ) {
				$table = ast_pro()->shippment_provider_table();
				$provider_name = $wpdb->get_row( $wpdb->prepare( 'SELECT provider_name FROM %1s WHERE id= %d', $table, $default_provider ) );
				$status_shipped = get_option( 'autocomplete_gls_deliveryfrom', 1 );
				$tracking_number = $meta_value;
				$tracking_number = str_replace( '.', '', $tracking_number );
				$tracking_number = str_replace( ' ', '', $tracking_number );					
				$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
				$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );				
				if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
					return;
				}
				ast_insert_tracking_number( $order_id, $tracking_number, $provider_name->provider_name, 0, $status_shipped );
			}            
		}
	}
}
