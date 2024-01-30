<?php
/* Hook into WooCommerce action and filter */
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'insert_tracking_number_from_dear_systems', 10, 3 );

function insert_tracking_number_from_dear_systems( $order, $request, $creating ) {	
	$order_id = $order->get_id();
	
	$CarrierName = $order->get_meta('CarrierName');
	$TrackingNumber = $order->get_meta('TrackingNumber'); 
	
	$tracking_provider = $order->get_meta('tracking_provider');
	$tracking_number = $order->get_meta('tracking_number'); 
	$status_shipped = get_option( 'autocomplete_dear_inventory', 0 );
	$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

	if ( '' != $CarrierName && '' != $TrackingNumber ) {		
		
		$tracking_info_exist = tracking_info_exist( $order_id, $TrackingNumber );
		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}
		
		ast_insert_tracking_number( $order_id, wc_clean( $TrackingNumber ), wc_clean( $CarrierName ), '', $status_shipped );

	} elseif ( '' != $tracking_provider && '' != $tracking_number ) {
		
		$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}

		ast_insert_tracking_number( $order_id, wc_clean( $tracking_number ), wc_clean( $tracking_provider ), '', $status_shipped );
	}
	
	return $order;
}
