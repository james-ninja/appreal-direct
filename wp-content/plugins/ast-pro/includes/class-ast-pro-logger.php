<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_Logger {
	
	/**
	 * AST_Pro_Logger
	 *
	 * Instance of this class.
	 *
	 * @var object Class Instance
	*/
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {

	}

	/**
	 * Get the class instance
	 *
	 * @return AST_Pro_Logger
	*/
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Log several events to log file.
	 *
	 * @param mixed  $message Message to log.
	 * @param string $level   Log level.
	 *
	 * @since 1.0.0
	 */
	public function log( $message, $level = 'error' ) {
		
		if ( get_option('ptaa_enable_log', 1) == 0 ) {
				
				return;
		}
		
		if ( !isset( $this->logger ) ) {
			
			$this->logger = wc_get_logger();
			
			$this->debug = 1;
			
		}
		
		if ( ! $this->debug ) {
			return;
		}

		if ( ! is_scalar( $message ) ) {
			$message = wc_print_r( $message, true );
		}

		$message = PHP_EOL . '---------------[START]---------------' . PHP_EOL . $message . PHP_EOL . '---------------[END]---------------';

		$this->logger->log( $level, $message, [ 'source' => 'paypal-tracking' ] );

	}
}
