<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_PayPal_Tracking {
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return AST_tpi
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init() {
		$ptaa_enable = get_option( 'ptaa_enable', 1 );
		if ( $ptaa_enable ) {
		
			// add 'Export Status' orders and customers page column header
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'manage_order_columns' ), 20 );
			add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'manage_order_columns' ), 10 );
			
			//add bulk action - Send Tracking to PayPal
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_send_tracking_to_paypal'), 10, 1 );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions_send_tracking_to_paypal' ), 10, 1 );
			
			// Make the action from selected orders to send tracking to paypal
			add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'send_tracking_to_paypal_handle_bulk_action_edit_shop_order'), 10, 3 );
			add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'send_tracking_to_paypal_handle_bulk_action_edit_shop_order' ), 10, 3 );
			add_filter( 'woocommerce_bulk_action_ids', array( $this, 'send_tracking_to_paypal_handle_bulk_action_order' ), 10, 3 );
			
			
			// The results notice from send tracking on orders
			add_action( 'admin_notices', array( $this, 'send_tracking_to_paypa_bulk_action_admin_notice' ) );
			
			// add 'get_shipment_status' order meta box order action
			add_action( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_send_tracking_to_paypal_actions' ) );
			add_action( 'woocommerce_order_action_send_tracking_to_paypal_edit_order', array( $this, 'process_order_meta_box_actions_send_tracking_to_paypal' ) );
			
			// add 'Export Status' orders and users page column content
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_status_column_content' ) );
			add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'add_wc_order_status_column_content' ), 10, 2 );

			// trigger when order status changed to shipped or completed
			add_action( 'woocommerce_order_status_completed', array( $this, 'send_tracking_info_to_paypal'), 10, 1 );
			add_action( 'woocommerce_order_status_shipped', array( $this, 'send_tracking_info_to_paypal'), 10, 1 );
			add_action( 'export_order_to_paypal', array( $this, 'send_tracking_info_to_paypal'), 10, 1 );
			
			add_action( 'bulk_send_tracking_to_paypal', array( $this, 'send_tracking_info_to_paypal'), 10, 1 );
			
			add_action( 'ast_trigger_ts_status_change', array( $this, 'add_ts_shipment_details_to_paypal'), 10, 5 );
			
			// trigger when order status changed to updated tracking
			add_action( 'woocommerce_order_status_updated-tracking', array( $this, 'send_tracking_info_to_paypal' ), 10, 2 );
			
			// trigger when tracking info delete from order 
			add_action( 'delete_tracking_number_from_trackship', array( $this, 'delete_tracking_number_from_paypal'), 10, 3 );

			// add bulk order filter for paypal export
			add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_paypal_tracking'), 20 );
			add_action( 'woocommerce_order_list_table_restrict_manage_orders', array( $this, 'filter_listtable_orders_by_paypal_tracking'), 10, 2 );	
			add_filter( 'request', array( $this, 'filter_orders_by_paypal_tracking_query' ) );
			add_filter( 'woocommerce_shop_order_list_table_prepare_items_query_args', array( $this, 'filter_listtable_orders_by_paypal_tracking_query' ) );
		}
	}

	/**
	* Add 'PayPal Tracking Status' column header to 'Orders' page immediately after 'Order Status' column
	*
	* @since 1.0.0
	*
	* @param array $columns
	* @return array $new_columns
	*/
	public function manage_order_columns( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'woocommerce-advanced-shipment-tracking' === $column_name ) {
				$new_columns['ptaa'] = __( 'PayPal Tracking', 'ast-pro' );
			}
		}

		return $new_columns;
	}

	/*
	* add bulk action
	* Send Tracking to PayPal
	*/
	public function add_bulk_actions_send_tracking_to_paypal( $bulk_actions ) {
		$bulk_actions['send_tracking_to_paypal'] = __( 'Export Tracking to PayPal', 'ast-pro' );		
		return $bulk_actions;
	}

	public function send_tracking_to_paypal_handle_bulk_action_order( $order_array, $action, $post_type ) {
		
		if ( 'send_tracking_to_paypal' !== $action ) {
			return $order_array;
		}

		$processed_ids = array();							
		
		foreach ( $order_array as $order_id ) {
			$order = wc_get_order( $order_id );
			$transaction_id = $order->get_transaction_id();
			$wc_ppp_brasil_sale_id = $order->get_meta( 'wc_ppp_brasil_sale_id', true );
			$payment_method = $order->get_payment_method();
			
			$ptaa_payment_methods = get_option('ptaa_payment_methods');
			
			if ( !isset ( $ptaa_payment_methods[$payment_method] ) ) { 
				continue;
			}	
			
			if ( 1 != $ptaa_payment_methods[$payment_method] ) {
				continue;
			}	
			
			if ( null == $transaction_id && null == $wc_ppp_brasil_sale_id ) {
				continue;				
			}	
			
			$tracking_items = ast_get_tracking_items( $order_id );
			
			if ( empty ( $tracking_items ) ) {
				continue; 
			}	
			
			wp_schedule_single_event( time() + 1, 'bulk_send_tracking_to_paypal', array( $order_id ) );		
			$processed_ids[] = $order_id;
			
		}

		return $order_array;
	}

	/*
	* order bulk action for Send Tracking to PayPal
	*/
	public function send_tracking_to_paypal_handle_bulk_action_edit_shop_order( $redirect_to, $action, $post_ids ) {			

		if ( 'send_tracking_to_paypal' !== $action ) {
			return $redirect_to;
		}
		
		$processed_ids = array();							
		
		foreach ( $post_ids as $order_id ) {
			
			$order = wc_get_order( $order_id );
			$transaction_id = $order->get_transaction_id();
			$wc_ppp_brasil_sale_id = $order->get_meta( 'wc_ppp_brasil_sale_id', true );
			$payment_method = $order->get_payment_method();
			
			$ptaa_payment_methods = get_option('ptaa_payment_methods');
			
			if ( !isset ( $ptaa_payment_methods[$payment_method] ) ) { 
				continue;
			}	
			
			if ( 1 != $ptaa_payment_methods[$payment_method] ) {
				continue;
			}	
			
			if ( null == $transaction_id && null == $wc_ppp_brasil_sale_id ) {
				continue;				
			}	
			
			$tracking_items = ast_get_tracking_items( $order_id );
			
			if ( empty ( $tracking_items ) ) {
				continue; 
			}	
			
			wp_schedule_single_event( time() + 1, 'bulk_send_tracking_to_paypal', array( $order_id ) );		
			$processed_ids[] = $order_id;
			
		}
	
		return add_query_arg( array(
			'send_tracking_to_paypal' => '1',
			'processed_count' => count( $processed_ids ),
			'processed_ids' => implode( ',', $processed_ids ),
		), $redirect_to );
	}

	/*
	* The results notice from bulk action on orders
	*/
	public function send_tracking_to_paypa_bulk_action_admin_notice() {
		
		if ( empty ( $_REQUEST['send_tracking_to_paypal'] ) ) {
			return;
		}	
	
		$count = isset( $_REQUEST['processed_count'] ) ? intval( $_REQUEST['processed_count'] ) : '';
	
		echo '<div id="message" class="updated fade"><p>' . esc_html( 'Send tracking information to PayPal will run in background, It will take few minutes to update in PayPal.', 'ast-pro'
		) . '</p></div>';
	}

	/**
	* Add 'send_tracking_to_paypal' link to order actions select box on edit order page
	*
	* @since 1.0
	* @param array $actions order actions array to display
	* @return array
	*/
	public function add_order_meta_box_send_tracking_to_paypal_actions( $actions ) {
		// add download to CSV action
		$actions['send_tracking_to_paypal_edit_order'] = __( 'Export Tracking to PayPal', 'ast-pro' );
		return $actions;
	}
	
	/*
	* order details meta box action
	*/
	public function process_order_meta_box_actions_send_tracking_to_paypal( $order ) {
		$this->send_tracking_info_to_paypal( $order->get_id() );
	}

	/**
	* Adds 'Export Status' column content to 'Orders' page immediately after 'Order Status' column
	*
	* 'Not Exported' - if 'is_exported' order meta doesn't exist or is equal to 0
	* 'Exported' - if 'is_exported' order meta exists and is equal to 1
	*
	* @since 1.0.0
	*
	* @param array $column name of column being displayed
	*/
	public function add_order_status_column_content( $column ) {
		global $post;

		if ( 'ptaa' === $column ) {

			$order = wc_get_order( $post->ID );

			$is_tracking_updated = $order->get_meta('_wc_ast_tracking_added_to_paypal');

			printf( '<span class="dashicons zorem_export_icon %1$s"></span>', $is_tracking_updated ? 'dashicons-yes' : 'dashicons-minus' );
		}
	}

	public function add_wc_order_status_column_content( $column_name, $order ) {
		global $post;

		if ( 'ptaa' === $column_name ) {

			$is_tracking_updated = $order->get_meta('_wc_ast_tracking_added_to_paypal');

			printf( '<span class="dashicons zorem_export_icon %1$s"></span>', $is_tracking_updated ? 'dashicons-yes' : 'dashicons-minus' );
		}
	}

	/*
	 * function for send tracking information to paypal
	*/
	public function send_tracking_info_to_paypal( $order_id ) {
		
		$order = wc_get_order( $order_id );
		
		$transaction_id = $order->get_transaction_id();
		$wc_ppp_brasil_sale_id = $order->get_meta( 'wc_ppp_brasil_sale_id', true );
		$payment_method = $order->get_payment_method();
		$ptaa_payment_methods = get_option('ptaa_payment_methods');		
					
		if ( !isset( $ptaa_payment_methods[$payment_method] ) ) {
			return;
		}	
		
		if ( 1 != $ptaa_payment_methods[$payment_method] ) {
			return;
		}	
		
		if ( null == $transaction_id && null == $wc_ppp_brasil_sale_id ) {
			return;				
		}	
		
		$token = $this->get_token();		
		$this->add_tracking_to_paypal( $order_id, $token );		
	}
	
	/*
	 * function for get paypal authorization token from paypla client id and client secret
	*/
	public function get_token() {
		
		// Do we have this information in our transients already?
		$transient = get_transient( 'ptaa_token' );
	  
		// Yep!  Just return it and we're done.
		if ( ! empty( $transient ) ) {

			// The function will return here every time after the first time it is run, until the transient expires.
			return $transient;
			
		} else {
			
			$endpoint = $this->get_paypal_endpoint() . 'v1/oauth2/token';		
			
			$args['body'] = 'grant_type=client_credentials';
		
			$args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( $this->get_api_key() . ':' . $this->get_api_secret() ),				
			);	
			
			$args['timeout'] = 120;
			
			try {
				$response = wp_remote_post( $endpoint, $args );
				
				if ( is_array( $response ) && ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
					$body = wp_remote_retrieve_body ($response );
					$bodyArray = json_decode( $body, true );
					$access_token = $bodyArray['access_token'];
					$expires_in = $bodyArray['expires_in'];
					
					// Save the API response so we don't have to call again until 1 hour.
					$expire = $expires_in - 900;
					set_transient( 'ptaa_token', $access_token, $expire );
					
					// Return the list of subscribers.  The function will return here the first time it is run, and then once again, each time the transient expires.
					return $access_token; 
				} else {
					ast_pro()->logger->log(
						[
							'url'      => $endpoint,
							'request'  => $args,
							'response' => $response,
						],
						'debug'
					);
				}	
			} catch (Exception $e) {
				ast_pro()->logger->log(
					[
						'url'      => $endpoint,
						'request'  => $args,
						'response' => $e->getMessage(),
					],
					'debug'
				);
			}				
		}
	}

	/*
	 * function for add tracking information to Paypal
	*/
	public function add_tracking_to_paypal( $order_id, $token ) {
		
		$order = wc_get_order( $order_id );
		$transaction_id = $order->get_transaction_id();
		
		if ( null == $transaction_id ) {
			$transaction_id = $order->get_meta( 'wc_ppp_brasil_sale_id', true );
		}
		
		$endpoint = $this->get_paypal_endpoint() . 'v1/shipping/trackers-batch';				
		
		$tracking_items = ast_get_tracking_items( $order_id );			
		
		$shipment_status = $order->get_meta( 'shipment_status', true );
				
		if ( empty( $tracking_items ) ) {
			return;
		}	
		
		$notify_buyer = get_option('ptaa_enable_buyer_notification') ? true : false;
		
		$trackers = array();
		foreach ( (array) $tracking_items as $key => $item ) {
			
			if ( isset( $shipment_status[$key]['status'] ) ) {
				if ( 'delivered' == $shipment_status[$key]['status'] ) {
					$status = 'DELIVERED';
				} elseif ( 'on_hold' == $shipment_status[$key]['status'] ) {
					$status = 'ON_HOLD';
				} else {
					$status = 'SHIPPED';
				}	
			} else {
				$status = 'SHIPPED';
			} 
						
			global $wpdb;
			
			$provider = $wpdb->get_row( $wpdb->prepare( 'SELECT paypal_slug FROM %1s WHERE provider_name = %s', ast_pro()->shippment_provider_table(), $item['formatted_tracking_provider'] ) );
			
			if ( isset($provider->paypal_slug) && '' != $provider->paypal_slug ) {
				$trackers[] = array (
					'transaction_id' => $transaction_id,
					'tracking_number' => $item['tracking_number'],				
					'status' => $status,
					'carrier' => $provider->paypal_slug,
					'notify_buyer' => $notify_buyer,
				); 
			} else {
				$trackers[] = array (
					'transaction_id' => $transaction_id,
					'tracking_number' => $item['tracking_number'],				
					'status' => $status,
					'carrier' => 'OTHER',
					'carrier_name_other' => $item['formatted_tracking_provider'],
					'notify_buyer' => $notify_buyer,
				);
			}			
			
		}
		
		$tracking_data =  array (
			'trackers' => $trackers,			
		);
		$json_data = json_encode($tracking_data);		
		
		$response = wp_remote_post( $endpoint, array(
			'method' => 'POST',
			'redirection' => 10,
			'httpversion' => '1.0',
			'body'    => $json_data,
			'timeout'     => 120,
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Content-Type' => 'application/json',
			),
		) );				
		
		if ( is_wp_error( $response ) ) {
			ast_pro()->logger->log(
				[
					'url'      => $endpoint,
					'request'  => $tracking_data,
					'response' => $response,
				],
				'debug'
			);
		}
		
		if ( is_array( $response ) && ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {	
			
			$order->update_meta_data( '_wc_ast_tracking_added_to_paypal', 1 );
			$order->save();
			
			// The text for the note
			foreach ( (array) $tracking_items as $item ) {
				
				$provider = isset( $item['formatted_tracking_provider'] ) ? $item['formatted_tracking_provider'] : $item['tracking_provider'];
				
				/* translators: %1$s: replace with tracking provider name, %2$s: replace with tracking number, %3$s: replace with status */
				$note = sprintf( esc_html__( 'Tracking info exported to PayPal: %1$s - %2$s(%3$s)', 'ast-pro' ), $provider, $item['tracking_number'], $status  );
				$order->add_order_note( $note );
			}						
		} else {
			ast_pro()->logger->log(
				[
					'url'      => $endpoint,
					'request'  => $tracking_data,
					'response' => $response,
				],
				'debug'
			);
		}									
	}
	
	/**
	 * Delete tracking information from Paypal when tracking deleted from AST
	 */
	public function add_ts_shipment_details_to_paypal( $order_id, $old_status, $new_status, $tracking_item, $shipment_status ) {
		
		if ( $old_status == $new_status ) {
			return;
		}
		
		$order = wc_get_order( $order_id );
		$transaction_id = $order->get_transaction_id();
		
		if ( null == $transaction_id ) {
			$transaction_id = $order->get_meta( 'wc_ppp_brasil_sale_id', true );
		}
		
		$payment_method = $order->get_payment_method();
		
		$ptaa_payment_methods = get_option('ptaa_payment_methods');		
			
		if ( !isset($ptaa_payment_methods[$payment_method]) ) {
			return;
		}
		
		if ( 1 != $ptaa_payment_methods[$payment_method] ) {
			return;
		}
		
		if ( null == $transaction_id ) {
			return;	
		}	
		
		$token = $this->get_token();
		$endpoint = $this->get_paypal_endpoint() . 'v1/shipping/trackers';
		
		$updateto_paypal = false; 
		$notify_buyer = get_option('ptaa_enable_buyer_notification') ? true : false;
		
		if ( 'on_hold' == $new_status ) {
			$paypal_shipment_status = 'ON_HOLD';
			$updateto_paypal = true;
		} elseif ( 'delivered' == $new_status ) {
			$paypal_shipment_status = 'DELIVERED';
			$updateto_paypal = true;
		} 
		
		if ( $updateto_paypal ) {
			$tracking_number = $tracking_item['tracking_number'];
			$tracking_provider = $tracking_item['tracking_provider'];
			
			global $wpdb;
			
			$provider = $wpdb->get_row( $wpdb->prepare( 'SELECT paypal_slug FROM %1s WHERE provider_name = %s', ast_pro()->shippment_provider_table(), $tracking_item['formatted_tracking_provider'] ) );
			
			if ( isset($provider->paypal_slug) && '' != $provider->paypal_slug ) {
				$tracker_data = array (
					'transaction_id' => $transaction_id,
					'tracking_number' => $tracking_item['tracking_number'],				
					'status' => $paypal_shipment_status,
					'carrier' => $provider->paypal_slug,
					'notify_buyer' => $notify_buyer,					
				); 
			} else {
				$tracker_data = array (
					'transaction_id' => $transaction_id,
					'tracking_number' => $tracking_item['tracking_number'],				
					'status' => $paypal_shipment_status,
					'carrier' => 'OTHER',
					'carrier_name_other' => $tracking_item['formatted_tracking_provider'],	
					'notify_buyer' => $notify_buyer,	
				);
			}
			
			$json_data = json_encode($tracker_data);		

			$response = wp_remote_request( $endpoint . '/' . $transaction_id . '-' . $tracking_number, array(
				'method' => 'PUT',
				'redirection' => 10,
				'httpversion' => '1.0',
				'body'    => $json_data,
				'timeout'     => 120,
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Content-Type' => 'application/json',
				),
			) );							
				
			if ( is_array( $response ) && ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 204 ) {	
			
				$provider = isset( $tracking_item['formatted_tracking_provider'] ) ? $tracking_item['formatted_tracking_provider'] : $tracking_item['tracking_provider'];
				
				/* translators: %1$s: replace with tracking provider name, %2$s: replace with tracking number, %3$s: replace with status */
				$note = sprintf( esc_html__( 'Tracking info updated to PayPal: %1$s - %2$s(%3$s)', 'ast-pro' ), $provider, $tracking_item['tracking_number'], $paypal_shipment_status  );
				$order->add_order_note( $note );			
				
			} else {
				ast_pro()->logger->log(
					[
						'url'      => $endpoint . '/' . $transaction_id . '-' . $tracking_number,
						'request'  => $tracker_data,
						'response' => $response,
					],
					'debug'
				);
			}
		}						
	}
	
	/**
	 * Delete tracking information from Paypal when tracking deleted from AST
	 */
	public function delete_tracking_number_from_paypal( $tracking_items, $tracking_id, $order_id ) {
		
		$order = wc_get_order( $order_id );
		$transaction_id = $order->get_transaction_id();
		if ( null == $transaction_id ) {
			$transaction_id = $order->get_meta( 'wc_ppp_brasil_sale_id', true );	
		}
		$payment_method = $order->get_payment_method();
		
		$ptaa_payment_methods = get_option('ptaa_payment_methods');		
			
		if ( !isset($ptaa_payment_methods[$payment_method]) ) {
			return;
		}
		
		if ( 1 != $ptaa_payment_methods[$payment_method] ) {
			return;
		}
		
		if ( null == $transaction_id ) {
			return;	
		}		
		
		$token = $this->get_token();
		$endpoint = $this->get_paypal_endpoint() . 'v1/shipping/trackers';		
		
		foreach ( $tracking_items as $tracking_item ) {				
			if ( $tracking_item['tracking_id'] == $tracking_id ) {					
				
				$tracking_number = $tracking_item['tracking_number'];
				$tracking_provider = $tracking_item['tracking_provider'];
				$tracking_status   = 'CANCELLED';
				
				global $wpdb;
				
				$provider = $wpdb->get_row( $wpdb->prepare( 'SELECT paypal_slug FROM %1s WHERE provider_name = %s', ast_pro()->shippment_provider_table(), $tracking_item['formatted_tracking_provider'] ) );				

				if ( isset( $provider->paypal_slug ) ) {
					$tracker_data = array (
						'transaction_id' => $transaction_id,
						'tracking_number' => $tracking_item['tracking_number'],				
						'status' => 'CANCELLED',
						'carrier' => $provider->paypal_slug,						
					); 
				} else {
					$tracker_data = array (
						'transaction_id' => $transaction_id,
						'tracking_number' => $tracking_item['tracking_number'],				
						'status' => 'CANCELLED',
						'carrier' => 'OTHER',
						'carrier_name_other' => $tracking_item['formatted_tracking_provider'],						
					);
				}
				
				$json_data = json_encode($tracker_data);		

				$response = wp_remote_request( $endpoint . '/' . $transaction_id . '-' . $tracking_number, array(
					'method' => 'PUT',
					'redirection' => 10,
					'httpversion' => '1.0',
					'body'    => $json_data,
					'timeout'     => 120,
					'headers' => array(
						'Authorization' => 'Bearer ' . $token,
						'Content-Type' => 'application/json',
					),
				) );	
				
				if ( is_array( $response ) && ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 204 ) {	
			
					$provider = isset( $tracking_item['formatted_tracking_provider'] ) ? $tracking_item['formatted_tracking_provider'] : $tracking_item['tracking_provider'];
					
					/* translators: %1$s: replace with tracking provider name, %2$s: replace with tracking number, %3$s: replace with status */
					$note = sprintf( __( 'Tracking info updated to PayPal: %1$s - %2$s(%3$s)', 'ast-pro' ), $provider, $tracking_item['tracking_number'], $tracking_status  );
					$order->add_order_note( $note );							
					
				} else {
					ast_pro()->logger->log(
						[
							'url'      => $endpoint . '/' . $transaction_id . '-' . $tracking_number,
							'request'  => $tracker_data,
							'response' => $response,
						],
						'debug'
					);
				}
			}				
		}	
	}
	
	/**
	 * Add bulk filter for Paypal Export in orders list
	 *
	 * @since 2.4
	 */
	public function filter_orders_by_paypal_tracking() {
		global $typenow;		
		if ( 'shop_order' === $typenow ) {
			?>
			<select name="_shop_order_paypal_tracking" id="dropdown_shop_order_paypal_tracking">
				<option value=""><?php esc_html_e( 'Filter orders with PayPal Tracking', 'ast-pro' ); ?></option>
				<option value="paypal_not_exported" <?php echo esc_attr( isset( $_GET['_shop_order_paypal_tracking'] ) ? selected( 'paypal_not_exported', wc_clean( $_GET['_shop_order_paypal_tracking'] ), false ) : '' ); ?>><?php esc_html_e( 'Orders with tracking info that were paid with PayPal and not exported', 'ast-pro' ); ?></option>
				<option value="paypal_exported" <?php echo esc_attr( isset( $_GET['_shop_order_paypal_tracking'] ) ? selected( 'paypal_exported', wc_clean( $_GET['_shop_order_paypal_tracking'] ), false ) : '' ); ?>><?php esc_html_e( 'Orders with tracking info that were paid with PayPal and exported', 'ast-pro' ); ?></option>				
			</select>
		<?php
		}
	}

	public function filter_listtable_orders_by_paypal_tracking( $order_type, $which ) {
		if ( 'shop_order' === $order_type ) {
			?>
			<select name="_shop_order_paypal_tracking" id="dropdown_shop_order_paypal_tracking">
				<option value=""><?php esc_html_e( 'Filter orders with PayPal Tracking', 'ast-pro' ); ?></option>
				<option value="paypal_not_exported" <?php echo esc_attr( isset( $_GET['_shop_order_paypal_tracking'] ) ? selected( 'paypal_not_exported', wc_clean( $_GET['_shop_order_paypal_tracking'] ), false ) : '' ); ?>><?php esc_html_e( 'Orders with tracking info that were paid with PayPal and not exported', 'ast-pro' ); ?></option>
				<option value="paypal_exported" <?php echo esc_attr( isset( $_GET['_shop_order_paypal_tracking'] ) ? selected( 'paypal_exported', wc_clean( $_GET['_shop_order_paypal_tracking'] ), false ) : '' ); ?>><?php esc_html_e( 'Orders with tracking info that were paid with PayPal and exported', 'ast-pro' ); ?></option>				
			</select>
		<?php
		}
	}

	/**
	 * Process bulk filter action for Paypal Export orders
	 *
	 * @since 3.0.0
	 * @param array $vars query vars without filtering
	 * @return array $vars query vars with (maybe) filtering
	 */
	public function filter_orders_by_paypal_tracking_query( $vars ) {
		global $typenow;		
		if ( 'shop_order' === $typenow && isset( $_GET['_shop_order_paypal_tracking'] ) && 'paypal_not_exported' == $_GET['_shop_order_paypal_tracking'] ) {
			
			$payment_method = array();
			$ptaa_payment_methods = get_option( 'ptaa_payment_methods', array() );
			foreach ( $ptaa_payment_methods as $method => $value ) {
				if ( 1 == $value ) {
					$payment_method[] = $method;
				}
			}
			
			$vars['meta_query'][] = array(
				'key'       => '_payment_method',
				'value'     => $payment_method,
				'compare'   => 'IN'
			);

			$vars['meta_query'][] = array(
				'key'       => '_wc_ast_tracking_added_to_paypal',				
				'compare'   => 'NOT EXISTS'
			);

			$vars['meta_query'][] = array(
				'key'       => '_wc_shipment_tracking_items',				
				'compare'   => 'EXISTS'
			);			
		}

		if ( 'shop_order' === $typenow && isset( $_GET['_shop_order_paypal_tracking'] ) && 'paypal_exported' == $_GET['_shop_order_paypal_tracking'] ) {
			
			$payment_method = array();
			$ptaa_payment_methods = get_option( 'ptaa_payment_methods', array() );
			foreach ( $ptaa_payment_methods as $method => $value ) {
				if ( 1 == $value ) {
					$payment_method[] = $method;
				}
			}
			
			$vars['meta_query'][] = array(
				'key'       => '_payment_method',
				'value'     => $payment_method,
				'compare'   => 'IN'
			);

			$vars['meta_query'][] = array(
				'key'       => '_wc_ast_tracking_added_to_paypal',
				'value'     => 1,				
				'compare'   => 'LIKE'
			);

			$vars['meta_query'][] = array(
				'key'       => '_wc_shipment_tracking_items',				
				'compare'   => 'EXISTS'
			);			
		}
		return $vars;
	}

	public function filter_listtable_orders_by_paypal_tracking_query( $args ) {
			
		if ( isset( $_GET['_shop_order_paypal_tracking'] ) && 'paypal_not_exported' == $_GET['_shop_order_paypal_tracking'] ) {
			
			$payment_method = array();
			$ptaa_payment_methods = get_option( 'ptaa_payment_methods', array() );
			foreach ( $ptaa_payment_methods as $method => $value ) {
				if ( 1 == $value ) {
					$payment_method[] = $method;
				}
			}
			
			$args['meta_query'][] = array(
				'key'       => '_payment_method',
				'value'     => $payment_method,
				'compare'   => 'IN'
			);

			$args['meta_query'][] = array(
				'key'       => '_wc_ast_tracking_added_to_paypal',				
				'compare'   => 'NOT EXISTS'
			);

			$args['meta_query'][] = array(
				'key'       => '_wc_shipment_tracking_items',				
				'compare'   => 'EXISTS'
			);			
		}

		if ( isset( $_GET['_shop_order_paypal_tracking'] ) && 'paypal_exported' == $_GET['_shop_order_paypal_tracking'] ) {
			
			$payment_method = array();
			$ptaa_payment_methods = get_option( 'ptaa_payment_methods', array() );
			foreach ( $ptaa_payment_methods as $method => $value ) {
				if ( 1 == $value ) {
					$payment_method[] = $method;
				}
			}
			
			$args['meta_query'][] = array(
				'key'       => '_payment_method',
				'value'     => $payment_method,
				'compare'   => 'IN'
			);

			$args['meta_query'][] = array(
				'key'       => '_wc_ast_tracking_added_to_paypal',
				'value'     => 1,				
				'compare'   => 'LIKE'
			);

			$args['meta_query'][] = array(
				'key'       => '_wc_shipment_tracking_items',				
				'compare'   => 'EXISTS'
			);			
		}
		return $args;
	}

	/*
	 * Return Paypal API URL
	*/
	private function get_paypal_endpoint() {
		return get_option('ptaa_sandbox') ? 'https://api-m.sandbox.paypal.com/' : 'https://api-m.paypal.com/';	
	}
	
	/*
	 * Return Paypal API Key
	*/
	private function get_api_key() {
		return get_option( 'ptaa_client_id' );		
	}
	
	/*
	 * Return Paypal API Secret
	*/
	private function get_api_secret() {		
		return get_option( 'ptaa_client_secret' );
	}	
}
