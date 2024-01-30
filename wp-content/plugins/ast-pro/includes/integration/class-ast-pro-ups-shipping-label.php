<?php

/**
* Add tracking numbers to the Advanced Shipment Tracking for WooCommerce meta fields when generating shipping labels using the WooCommerce UPS Shipping Plugin with Print Label plugin by PluginHive
*/
add_action( 'ph_ups_shipment_tracking_detail_ids', 'ast_pro_add_tracking_information_into_order_ph_ups', 10, 2  );	

if ( !function_exists( 'ast_pro_add_tracking_information_into_order_ph_ups' ) ) {
	function ast_pro_add_tracking_information_into_order_ph_ups( $shipment_id_cs, $order_id ) {
		if ( class_exists( 'AST_Pro_Actions' ) ) {
			if ( function_exists( 'ast_insert_tracking_number' ) ) {
				$status_shipped = get_option( 'autocomplete_ups_shipping', 1 );

				$tracking_info_exist = tracking_info_exist( $order_id, $shipment_id_cs );
				$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

				if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
					return;
				}

				ast_insert_tracking_number( $order_id, wc_clean( $shipment_id_cs ), 'UPS', gmdate('Y-m-d'), $status_shipped );
			}
		}
	}
}
