<?php
/*
* Compatibility code for WooCommerce Shipstation Integration plugin with Advanced Shipment Tracking for WooCommerce(AST)
*/
if ( ! class_exists( 'WC_Shipment_Tracking' ) ) {
	class WC_Shipment_Tracking {				
		
	}
}

if ( ! function_exists( 'wc_st_add_tracking_number' ) ) {
	function wc_st_add_tracking_number( $order_id, $tracking_number, $provider, $date_shipped = null, $custom_url = false ) {		
		if ( function_exists( 'ast_insert_tracking_number' ) ) {
			$status_shipped = 0;
			ast_insert_tracking_number( $order_id, $tracking_number, $provider , $date_shipped, $status_shipped );
		}		
	}
}
