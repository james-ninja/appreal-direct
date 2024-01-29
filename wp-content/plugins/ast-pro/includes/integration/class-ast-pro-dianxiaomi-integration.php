<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_Dianxiaomi {
	
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
	 * @return AST_Pro_Dianxiaomi
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
		add_filter( 'dianxiaomi_api_order_response', array( $this, 'dianxiaomi_api_order_response' ), 10, 4 );		
	}

	public function dianxiaomi_api_order_response( $order_data, $order, $fields, $server ) {
		
		if ( false !== strpos( $server->path, 'ship' ) && 'POST' == $server->method ) {
			$trackings = ( isset( $order_data['trackings'] ) && !empty( $order_data['trackings'] ) ) ? $order_data['trackings'] : array();
		
			$order_id = $order->get_id();	
			
			foreach ( $trackings as $item ) {
				if ( function_exists( 'ast_insert_tracking_number' ) ) {				
					$status_shipped = 1;
					ast_insert_tracking_number( $order_id, $item['tracking_number'], $item['tracking_provider'], $date_shipped = null, $status_shipped );
				}
			}
			return $order_data;	
		}
		return $order_data;	
	}
}
