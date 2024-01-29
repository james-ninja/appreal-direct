<?php
/**
 * Adds a tracking number to an order.
 *
 * @param int         $order_id        		The order id of the order you want to
 *                                     		attach this tracking number to.
 * @param string      $tracking_number 		The tracking number.
 * @param string      $tracking_provider	The tracking provider name.
 * @param int         $date_shipped    		The timestamp of the shipped date.
 *                                     		This is optional, if not set it will
 *                                     		use current time.
 * @param int 		  $status_shipped		0=no,1=shipped,2=partial shipped(if partial shipped order status is enabled)
 */
if ( !function_exists( 'ast_insert_tracking_number' ) ) { 
	function ast_insert_tracking_number( $order_id, $tracking_number, $tracking_provider, $date_shipped = null, $status_shipped = 0, $sku = null, $qty = null ) {	
		
		$ast_admin = AST_pro_admin::get_instance();
		$tracking_provider = $ast_admin->get_provider_slug_from_name( $tracking_provider );
		
		$args = array(
			'tracking_provider'     => $tracking_provider,		
			'tracking_number'       => $tracking_number,
			'date_shipped'          => $date_shipped,
			'status_shipped'		=> $status_shipped,
		);	
		
		if ( null != $sku && null != $qty ) {						
		
			$sku_array = explode( ',', $sku );
			$qty_array = explode( ',', $qty );
		
			$products_list = array();
				
			foreach ( $sku_array as $key => $sku ) {
				if ($qty_array[$key] > 0 ) {
					
					$product_id = ast_get_product_id_by_sku( $sku );
					
					$product_data =  (object) array (
						'product' => $product_id,
						'qty' => $qty_array[$key],
					);	
					array_push( $products_list, $product_data );								
				}
			}																			
			
			$product_args = array(
				'products_list' => $products_list,				
			);
			
			$args = array_merge( $args, $product_args );						
		}
		
		$ast = AST_Pro_Actions::get_instance();
		$ast->add_tracking_item( $order_id, $args );	
	}
}

/**
 * Adds a tracking number to an order.
 *
 * @param int         $order_id        		The order id of the order you want to
 *                                     		attach this tracking number to.
 * @param string      $tracking_number 		The tracking number.
 * @param string      $tracking_provider	The tracking provider slug.
 * @param int         $date_shipped    		The timestamp of the shipped date.
 *                                     		This is optional, if not set it will
 *                                     		use current time.
 * @param int 		  $status_shipped		0=no,1=shipped,2=partial shipped(if partial shipped order status is enabled)
 */
if ( !function_exists( 'ast_add_tracking_number' ) ) { 
	function ast_add_tracking_number( $order_id, $tracking_number, $tracking_provider, $date_shipped = null, $status_shipped = 0 ) {
		$ast = AST_Pro_Actions::get_instance();
		$args = array(
			'tracking_provider'     => $tracking_provider,		
			'tracking_number'       => $tracking_number,
			'date_shipped'          => $date_shipped,
			'status_shipped'		=> $status_shipped,
		);	
		$ast->add_tracking_item( $order_id, $args );	
	}
}

/**
 * Get a tracking information for an order.
 *
 * @param int         $order_id        		The order id of the order you want to
 *                                     		get tracking info. 
 */
if ( !function_exists( 'ast_get_tracking_items' ) ) {
	function ast_get_tracking_items( $order_id ) {
		$ast = AST_Pro_Actions::get_instance();
		$tracking_items = $ast->get_tracking_items( $order_id, true );	
		return $tracking_items;
	}
}

if ( !function_exists( 'ast_get_product_id_by_sku' ) ) {
	function ast_get_product_id_by_sku( $sku = false ) {
	
		global $wpdb;
	
		if ( !$sku ) {
			return null;
		}	
	
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value=%s LIMIT 1", $sku ) );
	
		if ( $product_id ) {
			return $product_id;
		}	
	
		return null;	
	}
}
