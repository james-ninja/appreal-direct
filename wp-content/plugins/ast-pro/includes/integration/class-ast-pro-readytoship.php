<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_Readytoship {
	
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
	 * @return AST_pro_readytoship
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
		add_action(	'woocommerce_api_loaded', array( $this, 'loadResources' ) );
		add_filter(	'woocommerce_api_classes', array( $this, 'registerResources' ) );	
	}

	public function loadResources() {                       
		include_once( 'api/class-ast-pro-readytoship-tracking.php' );
	}
		
	public function registerResources( $classes ) {
		$classes[] = 'WC_AST_Pro_Readytoship_Tracking';
		return $classes;
	}	
}
