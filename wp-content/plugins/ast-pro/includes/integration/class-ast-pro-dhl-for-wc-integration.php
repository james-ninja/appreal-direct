<?php
/*
* AST Pro: get DHL For WooCommerce Tracking number from order meta
* 
*/
add_action( 'pr_save_dhl_label_tracking', 'pr_save_dhl_label_tracking_callback', 10, 2 );
if ( !function_exists( 'pr_save_dhl_label_tracking_callback' ) ) {
	function pr_save_dhl_label_tracking_callback( $order_id, $tracking_details ) {
		if ( isset( $tracking_details['carrier'] ) && isset( $tracking_details['tracking_number'] ) ) {
			$tracking_number = wc_clean( $tracking_details['tracking_number'] );
			$tracking_provider = wc_clean( $tracking_details['carrier'] );
			$ship_date = wc_clean( $tracking_details['ship_date'] );
			$status_shipped = get_option( 'autocomplete_dhl_for_woocommerce', 1 );

			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, $ship_date, $status_shipped );
		}
	}
}
