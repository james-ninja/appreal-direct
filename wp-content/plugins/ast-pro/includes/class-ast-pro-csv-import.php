<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_Csv_Import {
	
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
		
		global $wpdb;
		if ( is_multisite() ) {			
			
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			
			if ( is_plugin_active_for_network( 'ast-pro/ast-pro.php' ) ) {
				$main_blog_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );			
				$this->table = $main_blog_prefix . 'woo_shippment_provider';	
			} else {
				$this->table = $wpdb->prefix . 'woo_shippment_provider';
			}
			
		} else {
			$this->table = $wpdb->prefix . 'woo_shippment_provider';	
		}
		
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return AST_pro_admin
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
		add_action( 'wp_ajax_wc_ast_upload_csv_form_update', array( $this, 'upload_tracking_csv_fun') );
	}	
	
	/*
	* Ajax call for upload tracking details into order from bulk upload
	*/
	public function upload_tracking_csv_fun() {				
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		//check_ajax_referer( 'nonce_csv_import', 'security' );
		
		$replace_tracking_info = isset( $_POST['replace_tracking_info'] ) ? wc_clean( $_POST['replace_tracking_info'] ) : '';
		$date_format_for_csv_import = isset( $_POST['date_format_for_csv_import'] ) ? wc_clean( $_POST['date_format_for_csv_import'] ) : '';
		update_option( 'date_format_for_csv_import', $date_format_for_csv_import );
		$order_number = isset( $_POST['order_id'] ) ? wc_clean( $_POST['order_id'] ) : '';		
		
		$tpi = AST_Tpi::get_instance();

		$wast = AST_Pro_Actions::get_instance();
		$order_id = $wast->get_formated_order_id( $order_number );
		
		$tracking_provider = isset( $_POST['tracking_provider'] ) ? wc_clean( $_POST['tracking_provider'] ) : '';
		$tracking_number = isset( $_POST['tracking_number'] ) ? wc_clean( $_POST['tracking_number'] ) : '';
		$status_shipped = ( isset( $_POST['status_shipped'] ) ? wc_clean( $_POST['status_shipped'] ) : '' );
		$date_shipped = ( isset( $_POST['date_shipped'] ) ? wc_clean( $_POST['date_shipped'] ) : '' );
		$date_shipped = str_replace( '/', '-', $date_shipped );
		$trackings = ( isset( $_POST['trackings'] ) ? wc_clean( $_POST['trackings'] ) : '' );		
		
		$sku = isset( $_POST['sku'] ) ? wc_clean( $_POST['sku'] ) : '';
		$qty = isset( $_POST['qty'] ) ? wc_clean( $_POST['qty'] ) : '';	
		$date_shipped = empty( $date_shipped ) ? gmdate('d-m-Y') : $date_shipped ;									

		global $wpdb;					
		
		$shippment_provider = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %1s WHERE api_provider_name = %s', $this->table, $tracking_provider ) );
		
		if ( 0 == $shippment_provider ) {			
			$shippment_provider = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %1s WHERE JSON_CONTAINS(LOWER(api_provider_name), LOWER(%s))', $this->table, '["' . $tracking_provider . '"]' ) );
		}	
		
		if ( 0 == $shippment_provider ) {			
			$shippment_provider = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %1s WHERE provider_name = %s', $this->table, $tracking_provider ) );
		}
		
		if ( 0 == $shippment_provider ) {
			$shippment_provider = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %1s WHERE ts_slug = %s', $this->table, $tracking_provider ) );
		}
		
		$order = wc_get_order($order_id);		
		
		if ( false == $order ) {
			echo '<li class="invalid_order_id_error">Failed - Invalid Order Id - Order ' . esc_html( $order_number ) . '</li>';
			exit;
		}
		
		if ( 0 == $shippment_provider ) {
			echo '<li class="shipping_provider_error">Failed - Invalid Shipping Provider - Order ' . esc_html( $order_number ) . '</li>';
			exit;
		}
		
		if ( empty( $tracking_number ) ) {
			echo '<li class="tracking_number_error">Failed - Empty Tracking Number - Order ' . esc_html( $order_number ) . '</li>';
			exit;
		}

		if ( preg_match( '/^[+-]?[0-9]+(\.[0-9]+)?E[+-][0-9]+$/', $tracking_number ) ) {
			echo '<li class="tracking_number_error">Failed - Invalid Tracking Number - Order ' . esc_html( $order_number ) . '</li>';
			exit;
		}
		
		if ( empty( $date_shipped ) ) {
			echo '<li class="empty_date_shipped_error">Failed - Empty Date Shipped - Order ' . esc_html( $order_number ) . '</li>';
			exit;
		}			
		
		if ( !$this->isDate( $date_shipped, $date_format_for_csv_import ) ) {
			echo '<li class="invalid_date_shipped_error">Failed - Invalid Date Shipped- Order ' . esc_html( $order_number ) . '</li>';
			exit;
		}	
		
		if ( 'm-d-Y' == $date_format_for_csv_import ) {
			$date_array = explode( '-' , $date_shipped );
			$date_shipped = $date_array[1] . '-' . $date_array[0] . '-' . $date_array[2];			
		}
		
		$tracking_items = $wast->get_tracking_items( $order_id );	
		
		if ( 1 == $replace_tracking_info ) {
			
			$order = wc_get_order($order_id);
			
			if ( $order ) {	
						
				if ( count( $tracking_items ) > 0 ) {
					foreach ( $tracking_items as $key => $item ) {								
						
						$tracking_exist = false;
						
						$tpi_order = $tpi->check_if_tpi_order( $tracking_items, $order );
						if ( $tpi_order ) {
							$item_tracking_number = $item['tracking_number'];
							$tracking_exist = in_array( $item_tracking_number, array_column( $trackings, 'tracking_number' ) );
						}
						
						if ( false == $tracking_exist ) {	
							do_action( 'delete_tracking_number_from_trackship', $tracking_items, $item['tracking_id'], $order_id );						
							unset( $tracking_items[ $key ] );							
						}
					}
					
					$wast->save_tracking_items( $order_id, $tracking_items );					
				}
			}
		}
		
		if ( $shippment_provider && $tracking_number && $date_shipped ) {
					
			$tracking_provider = ast_pro()->ast_pro_admin->get_provider_slug_from_name( $tracking_provider );
				
			$args = array(
				'tracking_provider' => $tracking_provider,					
				'tracking_number'   => $tracking_number,
				'date_shipped'      => $date_shipped,
				'status_shipped'	=> $status_shipped,
			);
				
			if ( '' != $sku ) {				
				
				$products_list = array();
				
				if ( $qty > 0 ) {
					
					$product_id = ast_get_product_id_by_sku( $sku );
					
					
					if ( !$product_id ) {
						echo '<li class="invalid_product_sku_error">Failed - Invalid product SKU - Order ' . esc_html( $order_number ) . '</li>';
						exit;
					}
						
					$product_data =  (object) array (							
						'product' => $product_id,
						'qty' => $qty,
					);
					
					array_push( $products_list, $product_data );
					
					$product_data_array = array();
					$product_data_array[ $product_id ] = $qty;												
					
					$autocomplete_order_tpi = get_option( 'autocomplete_order_tpi', 0 );
					if ( 1 == $autocomplete_order_tpi ) {
						$status_shipped = ast_pro()->ast_pro_admin->autocomplete_order_after_adding_all_products( $order_id, $status_shipped, $products_list );
						$args['status_shipped'] = $status_shipped;
					}						
					
					if ( count( $tracking_items ) > 0 ) {								
						foreach ( $tracking_items as $key => $item ) {						
							if ( $item['tracking_number'] == $tracking_number ) {
								
								if ( isset( $item['products_list'] ) && !empty( $item['products_list'] ) ) {
									
									$product_list_array = array();
									foreach ( $item['products_list'] as $item_product_list ) {														
										$product_list_array[ $item_product_list->product ] = $item_product_list->qty;
									}																							
									
									$mearge_array = array();										
									foreach ( array_keys( $product_data_array + $product_list_array ) as $product ) {										
										$mearge_array[ $product ] = (int) ( isset( $product_data_array[ $product ] ) ? $product_data_array[ $product ] : 0 ) + (int) ( isset( $product_list_array[$product] ) ? $product_list_array[ $product ] : 0 );
									}																								
									
									foreach ( $mearge_array as $productid => $product_qty ) {
										$merge_product_data[] =  (object) array (							
											'product' => $productid,
											'qty' => $product_qty,
										);
									}
										
									if ( !empty( $merge_product_data ) ) {
										$tracking_items[ $key ]['products_list'] = $merge_product_data;	
										$wast->save_tracking_items( $order_id, $tracking_items );

										$order = new WC_Order( $order_id );
										
										do_action( 'update_order_status_after_adding_tracking', $status_shipped, $order );
		
										echo '<li class="success">Success - added tracking info to Order ' . esc_html( $order_number ) . '</li>';
										exit;
									}		
								}											
							}	 
						}																		
					} 
					
					$product_args = array(
						'products_list' => $products_list,				
					);							
					
				}																																	
				$args = array_merge( $args, $product_args );				
			}																												
			 
			$wast->add_tracking_item( $order_id, $args );
			
			echo '<li class="success">Success - added tracking info to Order ' . esc_html( $order_number ) . '</li>';
			exit;
		} else {
			echo '<li class="invalid_tracking_data_error">Failed - Invalid Tracking Data</li>';
			exit;
		}		
	}

	/**
	* Check if the value is a valid date
	*
	* @param mixed $value
	*
	* @return boolean
	*/
	public function isDate( $date, $format = 'd-m-Y' ) {
		if ( !$date ) {
			return false;
		}		
		$d = DateTime::createFromFormat( $format, $date );		
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format( $format ) === $date;
	}
}
