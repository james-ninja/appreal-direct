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
			$status_shipped = get_option( 'autocomplete_customcat', 1 );
			$tracking_number = $tracking['number'];
			$tracking_provider = $tracking['provider'];

			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}

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

		if ( preg_match( '/usps\\.com/', $note_content ) ) {
			if ( preg_match( '/qtc_tLabels1=([\dA-Z]+)/', $note_content, $matches ) ) {
				return array(
					'provider' => 'USPS',
					'number' => $matches[1]
				);
			}
		}

		if ( preg_match( '/usps\\.com/', $note_content ) ) {
			if ( preg_match( '/tLabels=([\dA-Z]+)/', $note_content, $matches ) ) {
				return array(
					'provider' => 'USPS',
					'number' => $matches[1]
				);
			}
		}

		/* Add more provider matches here, as appropriate */
		return false;
	}
}
