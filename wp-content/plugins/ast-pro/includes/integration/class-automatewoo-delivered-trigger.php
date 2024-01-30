<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create class for - Trigger_Order_Completed
 * 
 * @class Trigger_Order_Completed
 */
class Trigger_Order_Delivered extends Trigger_Abstract_Order_Status_Base {

	public $_target_status = 'delivered';


	public function load_admin_details() {
		parent::load_admin_details();
		$this->title = __( 'Order Delivered', 'ast-pro' );
	}

}
