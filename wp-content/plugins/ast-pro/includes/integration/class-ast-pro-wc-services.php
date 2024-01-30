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
				$status_shipped = get_option( 'autocomplete_wc_shipping', 0 );

				$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
				$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

				if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
					return;
				}

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

/**
 * WC_Shipment_Tracking class
 */
if ( ! class_exists( 'WC_Shipment_Tracking' ) ) {
	
	class WC_Shipment_Tracking {
		
		public $actions;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Include required files.
			$this->includes();
		}

		/**
		 * Include required files.
		 */
		private function includes() {
			require 'class-wc-shipment-tracking.php';
			$this->actions = WC_Shipment_Tracking_Actions::get_instance();			
		}
	}	
}

/**
 * Returns an instance of WC_Shipment_Tracking.
 */
if ( !function_exists( 'wc_shipment_tracking' ) ) {
	function wc_shipment_tracking() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new WC_Shipment_Tracking();
		}

		return $instance;
	}
}
