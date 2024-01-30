<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking Actions
*/
if ( ! class_exists( 'WC_Shipment_Tracking_Actions' ) ) {
	class WC_Shipment_Tracking_Actions {	
		/**
		 * Instance of this class.
		 *
		 * @var object Class Instance
		 */
		private static $instance;	
		/**
		 * Get the class instance    	 
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;			
		}	
		/*
		 * Gets all tracking itesm fron the post meta array for an order
		 *
		 * @param int  $order_id  Order ID
		 * @param bool $formatted Wether or not to reslove the final tracking link
		 *                        and provider in the returned tracking item.
		 *                        Default to false.
		 *
		 * @return array List of tracking items
		 */
		public function get_tracking_items( $order_id, $formatted = false ) {
			global $wpdb;	
			
			$order = wc_get_order( $order_id );

			$tracking_items = $order->get_meta( '_wc_shipment_tracking_items', true );	
			
			if ( is_array( $tracking_items ) ) {
				return $tracking_items;
			} else {
				return array();
			}
		}    
	}
}
