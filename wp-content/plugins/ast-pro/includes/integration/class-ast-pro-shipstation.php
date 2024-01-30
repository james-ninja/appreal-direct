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
			$status_shipped = get_option( 'autocomplete_shipstation', 0 );

			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}

			ast_insert_tracking_number( $order_id, $tracking_number, $provider , null, $status_shipped );
		}		
	}
}
