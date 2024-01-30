<?php
/*
* AST Pro: get Stamps.com Tracking number from order meta
* 
*/
add_action( 'woocommerce_stampscomendicia_shipnotify', 'add_stampscom_tracking_information_to_order', 10, 2 );
if ( !function_exists( 'add_stampscom_tracking_information_to_order' ) ) {
	function add_stampscom_tracking_information_to_order( $order, $tracking_information ) {
		$order_id = $order->get_id();
		if ( isset( $tracking_information['tracking_number'] ) && isset( $tracking_information['tracking_number'] ) ) {
			$tracking_number = $tracking_information['tracking_number'];
			$tracking_provider = $tracking_information['carrier'];
			$ship_date = $tracking_information['ship_date'];
			$status_shipped = get_option( 'autocomplete_stamps_com', 1 );

			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}
			
			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, $ship_date, $status_shipped );
		}
	}
}

add_action( 'save_post_wc_stamps_label', 'add_stampscom_tracking_information_from_post', 10, 3);
if ( !function_exists( 'add_stampscom_tracking_information_from_post' ) ) {
	function add_stampscom_tracking_information_from_post( $post_ID, $post, $update ) {
		
		global $wpdb;
		$default_provider = get_option( 'wc_ast_default_provider' );
		if ( '' != $default_provider ) {		
			$table = ast_pro()->shippment_provider_table();
			$provider_data = $wpdb->get_row( $wpdb->prepare( 'SELECT provider_name FROM %1s WHERE id= %d', $table, $default_provider ) );
			$tracking_provider = $provider_data->provider_name;
			$tracking_number = $post->post_title;
			$order_id = $post->post_parent;
			$status_shipped = get_option( 'autocomplete_stamps_com', 1 );

			$tracking_info_exist = tracking_info_exist( $order_id, $tracking_number );
			$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

			if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
				return;
			}

			ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, null, $status_shipped );
		}
	}
}
