<?php
/* Hook into WooCommerce action and filter */
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'insert_tracking_number_from_dear_systems', 10, 3 );

function insert_tracking_number_from_dear_systems( $order, $request, $creating ) {	
	$order_id = $order->get_id();
	$tracking_provider = $order->get_meta('tracking_provider');
	$tracking_number = $order->get_meta('tracking_number'); 
	if ( '' != $tracking_provider && '' != $tracking_number ) {
		$status_shipped = 0;
		ast_insert_tracking_number( $order_id, wc_clean( $tracking_number ), wc_clean( $tracking_provider ), '', $status_shipped );
	}
	
	return $order;
}
