<?php
/*
* Compatibility code for AutomateWoo plugin with AST PRO
*/

if ( ! class_exists( 'WC_Shipment_Tracking' ) ) {
	class WC_Shipment_Tracking {				
		
	}
}

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
		 *
		 * @return WC_Shipment_Tracking_Actions
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function get_tracking_items( $order_id, $formatted = false ) {
			$ast = AST_Pro_Actions::get_instance();
			$tracking_items = $ast->get_tracking_items( $order_id, $formatted );	
			return $tracking_items;
		}
	}
}

add_filter( 'automatewoo/triggers', 'trigger_delivered' );

if ( ! function_exists( 'trigger_delivered' ) ) {
	function trigger_delivered( $includes ) {
		include_once 'class-automatewoo-delivered-trigger.php';
		// set a unique name for the trigger and then the class name
		$includes['order_delivered'] = 'AutomateWoo\Trigger_Order_Delivered';
		return $includes;
	}
}
