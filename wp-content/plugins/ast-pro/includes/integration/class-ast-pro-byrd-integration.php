<?php

/* Hook into WooCommerce action and filter */
add_filter('woocommerce_rest_pre_insert_shop_order_object', 'ast_insert_tracking_from_byrd', 10, 3);

function ast_insert_tracking_from_byrd( $order, $request, $creating ) {
	
	if ( !function_exists( 'ast_pro' ) ) {
		return $order;
	}

	$metadata = isset( $request['meta_data'] ) ? $request['meta_data'] : array() ;

	foreach ( $metadata as $meta ) {
		if ( isset( $meta['key'] ) && 'tracking_id' == $meta['key'] ) {
			
			global $wpdb;
			$default_provider = get_option( 'wc_ast_default_provider' );
			
			if ( '' != $default_provider ) {
				$order_id = $order->get_id();
				$table = ast_pro()->shippment_provider_table();
				$provider_name = $wpdb->get_row( $wpdb->prepare( 'SELECT provider_name FROM %1s WHERE id= %d', $table, $default_provider ) );
				$status_shipped = get_option( 'autocomplete_byrd', 0 );
				$tracking_number = $meta['value'];
				$tracking_number = str_replace( '.', '', $tracking_number );
				$tracking_number = str_replace( ' ', '', $tracking_number );					
				$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
				$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );				
				if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
					return $order;
				}
				ast_insert_tracking_number( $order_id, $tracking_number, $provider_name->provider_name, 0, $status_shipped );
			}
		}
	}

	return $order;
} 
