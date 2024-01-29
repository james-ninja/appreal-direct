<?php

/* Hook into WooCommerce action and filter */
add_action( 'woocommerce_rest_insert_order_note', 'ast_pro_action_customcat_woocommerce_rest_insert_order_note', 10, 3 );

/**
* When an order note is added via REST api, get the tracking info and add to AST
*/
if ( !function_exists( 'ast_pro_action_customcat_woocommerce_rest_insert_order_note' ) ) {
	function ast_pro_action_customcat_woocommerce_rest_insert_order_note( $note, $request, $creating ) {
		
		//check if AST is active
		if ( !function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}	
		
		$tracking = is_tracking_note( $note->comment_content );
		
		if ( $tracking ) {
			$order_id = $request['order_id'];
			$status_shipped = 1;
			$tracking_number = $tracking['number'];
			$tracking_provider = $tracking['provider'];
			ast_insert_tracking_number($order_id, $tracking_number, $tracking_provider, '', $status_shipped );
		}
	}
}

/**
* Parse provider & tracking number from order note
*/
if ( !function_exists( 'is_tracking_note' ) ) {
	function is_tracking_note( $note_content ) {
		if ( preg_match( '/ups\\.com/', $note_content ) ) {
			if ( preg_match( '/trackNums=([\dA-Z]+)/', $note_content, $matches ) ) {
				return array(
					'provider' => 'UPS',
					'number' => $matches[1]
				);
			}
		}
		/* Add more provider matches here, as appropriate */
		return false;
	}
}
