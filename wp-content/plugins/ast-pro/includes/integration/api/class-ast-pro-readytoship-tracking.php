<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * REST API shipment tracking controller.
 *
 * Handles requests to /order/<id>/tracking endpoint.
 *
 * @since 1.5.0
 */
class WC_AST_Pro_Readytoship_Tracking extends WC_API_Resource {

	/**
	* Route base.
	*
	* @var string
	*/
	protected $base = '/orders';
		
	protected $post_type = 'shop_order';
		
	protected $ast;
	
	/**
	* WC_AST_Pro_Readytoship_Tracking constructor.
	*/
	public function __construct( $server ) {
		parent::__construct( $server );
		$this->ast = AST_Pro_Actions::get_instance();
		$this->initHook();
	}
	
	public function initHook() {
		add_filter('woocommerce_api_index', array( $this, 'data_add_readytoship_Integration_api' ) );
	}
	
	public function data_add_readytoship_Integration_api( $data ) {
		$data['store']['meta']['readytoship_tracking_api_installed'] = true;
		return $data;
	}
	
	/**
	* Register the routes for this class
	*
	* GET /order/<id>/tracking
	* POST /order/<id>/tracking
	*
	* @since 2.1
	* @param array $routes
	* @return array
	*/
	public function register_routes( $routes ) {		
		# GET/POST /orders/<id>/tracking
		$routes[$this->base . '/(?P<order_id>\d+)/tracking'] = array(
			array(array( $this, 'get_tracking' ), WC_API_Server::READABLE),
			array(array( $this, 'create_tracking' ), WC_API_Server::CREATABLE | WC_API_Server::ACCEPT_DATA)
		);
		return $routes;
	}
	
	/**
	* Get the tracking for an order
	*
	* @since 2.1
	* @param string $order_id order ID
	* @return array
	*/
	public function get_tracking( $order_id ) {
		
		// ensure ID is valid order ID
		$order_id = $this->validate_request( $order_id, $this->post_type, 'read' );
	
		if ( is_wp_error( $order_id ) ) {
			return $order_id;
		}
	
		$trackingItems = $this->ast->get_tracking_items( $order_id );
	
		return array( 'tracking' => apply_filters('woocommerce_api_tracking_response', $trackingItems, $order_id ) );
	}
	
	/**
	* Create_tracking  for an order
	*
	* @since 2.1
	* @param string $order_id order ID
	* @param string $data  data
	* @return array
	*/
	public function create_tracking( $order_id, $data ) {
		try {
			if ( !isset( $data[ 'tracking' ] ) ) {
				throw new WC_API_Exception('woocommerce_api_missing_tracking_data', 
				/* translators: replace %s with required */
				sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'tracking' ), 400 );
			}

			$data = $data['tracking'];
			$ast = AST_Pro_Actions::get_instance();
			$order_id = $ast->get_formated_order_id( $order_id );
			$order_id = $this->validate_request( $order_id, $this->post_type, 'edit' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			foreach ( array( 'provider', 'tracking_number' ) as $required ) {
							
				if ( !isset( $data[ $required ] ) ) {
					throw new WC_API_Exception('woocommerce_api_invalid_' . $required, 
					/* translators: replace %s with required */
					sprintf(__('%s is required.', 'woocommerce'), ucfirst($required)), 400);
				}
			}
			global $wpdb;			
			
			$ast_admin = AST_pro_admin::get_instance();		
			
			$tracking_provider_name = isset($data['provider']) ? $data['provider'] : '';		
			
			$tracking_provider = $wpdb->get_var( $wpdb->prepare( 'SELECT ts_slug FROM %1s WHERE provider_name = %s', $ast_admin->table, $tracking_provider_name ) );

			if ( !$tracking_provider ) {
				$tracking_provider = $wpdb->get_var( $wpdb->prepare( 'SELECT ts_slug FROM %1s WHERE api_provider_name = %s', $ast_admin->table, $tracking_provider_name ) );
			}			

			if ( !$tracking_provider ) {
				$tracking_provider = sanitize_title( $tracking_provider_name );
			}
			
			$status_shipped = get_option( 'autocomplete_readytoship', 0 );

			$tracking_data = array(
				'tracking_provider' => $tracking_provider,		
				'tracking_number'   => $data['tracking_number'],
				'date_shipped'      => gmdate('Y-m-d'),
				'status_shipped'    => $status_shipped,
				'source'			=> 'REST_API',
			);				
			
			$tracking_info_exist = tracking_info_exist( $order_id, $data['tracking_number'] );
			if ( $tracking_info_exist ) {
				return;
			}
			
			$tracking_item = $ast->add_tracking_item( $order_id, $tracking_data );		
			
			$this->server->send_status( 201 );
			return $tracking_item;
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}	   
}
