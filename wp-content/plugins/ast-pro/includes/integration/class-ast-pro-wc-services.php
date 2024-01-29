<?php 

/*
* Compatibility code for WooCommerce Services plugin with Advanced Shipment Tracking for WooCommerce(AST)
*/
if ( ! class_exists( 'WC_Connect_Extension_Compatibility' ) ) {
	class WC_Connect_Extension_Compatibility {
		/**
		 * Function called when a new tracking number is added to the order
		 *
		 * @param $order_id - order ID
		 * @param $carrier_id - carrier ID, as returned on the label objects returned by the server
		 * @param $tracking_number - tracking number string
		 */
		public static function on_new_tracking_number( $order_id, $carrier_id, $tracking_number ) {
			//call WooCommerce Shipment Tracking if it's installed
			if ( function_exists( 'ast_insert_tracking_number' ) ) {
				//note: the only carrier ID we use at the moment is 'usps', which is the same in WC_ST, but this might require a mapping
				$status_shipped = 0;
				ast_insert_tracking_number( $order_id, $tracking_number, strtolower( $carrier_id ), $date_shipped = null, $status_shipped );
			}
		}

		/**
		 * Checks if WooCommerce Services should email the tracking details, or if another extension is taking care of that already
		 *
		 * @param $order_id - order ID
		 * @return boolean true if WCS should send the tracking info, false otherwise
		 */
		public static function should_email_tracking_details( $order_id ) {
			return false;
		}		
	}
}
