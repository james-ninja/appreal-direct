<?php

add_action( 'rest_api_init', 'register_rest_route_shippypro' );

if ( !function_exists( 'register_rest_route_shippypro' ) ) {
	function register_rest_route_shippypro() {
		
		register_rest_route( 'bm/v1', '/shippypro/', array(
			'methods'  => 'POST',
			'callback' => 'ast_pro_shippypro_func',
			'permission_callback' => '__return_true'
		) );
	}
}

if ( !function_exists( 'ast_pro_shippypro_func' ) ) {
	function ast_pro_shippypro_func( $request ) {
		
		$data = $request->get_json_params();
		
		// get WC_Order object 
		$order = wc_get_order( $data['TransactionID'] );
		// print_r($order);
		if ( $order ) {
			
			if ( 'ORDER_SHIPPED' == $data['Event'] ) {			
				ast_insert_tracking_number( $data['TransactionID'], $data['TrackingNumber'], $data['TrackingCarrier'], null, 1 );	  	
			}		
		}		
	}
}