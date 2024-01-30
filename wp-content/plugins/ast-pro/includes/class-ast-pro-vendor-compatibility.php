<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_PRO_Vendor_Compatibility {
	
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
	 * @return AST_PRO_Vendor_Compatibility
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
		
		add_action( 'admin_menu', array( $this, 'register_fulfillment_menu' ), 99 );				
		
		add_action( 'wp_ajax_get_vendor_unfulfilled_orders', array($this, 'get_vendor_unfulfilled_orders') );						
		
		// Ajax hook for open inline tracking form
		add_action( 'wp_ajax_ast_open_inline_tracking_form_for_vendor', array( $this, 'ast_open_inline_tracking_form_for_vendor_fun' ) );
		
		add_filter( 'tracking_info_args', array( $this, 'save_vendor_in_tracking_info_args' ), 10, 3 );
		add_filter( 'tracking_item_args', array( $this, 'save_vendor_in_tracking_item_args' ), 10, 3 );
		
		add_action( 'ast_save_tracking_details_end', array( $this, 'ast_save_tracking_details_end' ), 10, 2 );		
		add_action( 'wcpv_vendor_order_detail_order_data_column', array( $this, 'wcpv_vendor_order_detail_order_data_column' ) );				
	}	
	
	/* 
	* Register Fulfillment Menu
	*/
	public function register_fulfillment_menu() {
		
		$fullfillment_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjQwcHgiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNDAgNDAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggaWQ9IlhNTElEXzEyXyIgZmlsbD0iI0YwRjZGQyIgZD0iTTI1LjMsMjIuNWMzLjQtMi4zLDguMy0yLDExLjQsMC44YzEuOSwxLjYsMywzLjksMy4zLDYuM3YxLjVjLTAuMyw0LjctNC4yLDguNi04LjksOC45aC0xLjMNCgljLTQtMC4zLTcuNy0zLjMtOC42LTcuMkMyMC4zLDI5LDIyLDI0LjYsMjUuMywyMi41IE0zMy42LDI3Yy0xLjUsMS4xLTIuNywyLjYtNCwzLjljLTAuNy0wLjctMS4zLTEuNC0yLjEtMg0KCWMtMS4xLTAuNi0yLjcsMC4yLTIuNywxLjVjMCwxLjMsMS4yLDIsMiwzYzEsMC44LDEuOSwyLjMsMy4zLDJjMS4xLTAuNSwxLjgtMS41LDIuNi0yLjJjMS4xLTEuMiwyLjUtMi4zLDMuNS0zLjcNCglDMzYuOSwyNy45LDM1LjEsMjYuMiwzMy42LDI3TDMzLjYsMjd6Ii8+DQo8cGF0aCBpZD0iWE1MSURfMTBfIiBmaWxsPSIjRjBGNkZDIiBkPSJNMzIsMy4yYy0wLjgtMS4zLTEuNC0zLjQtMy40LTMuMkMyMSwwLDEzLjMsMCw1LjcsMGMtMi0wLjItMi41LDEuOS0zLjQsMy4yDQoJQzEuNCw0LjktMC4yLDYuNSwwLDguNmMwLDcuMywwLDE0LjYsMCwyMS45Yy0wLjEsMiwxLjcsMy44LDMuNywzLjdjNC42LDAuMSw5LjIsMCwxMy45LDBjLTEuMy00LjcsMC0xMC4xLDMuNi0xMy41DQoJYzMuNC0zLjMsOC41LTQuNCwxMy0zLjFjMC0zLDAtNi4xLDAtOS4xQzM0LjQsNi41LDMyLjksNC45LDMyLDMuMnogTTIzLjYsMTMuNWMwLDEtMC45LDEuOS0xLjksMS45SDEzYy0xLDAtMS45LTAuOS0xLjktMS45di0wLjMNCgljMC0xLDAuOS0xLjksMS45LTEuOWg4LjdjMSwwLDEuOSwwLjksMS45LDEuOUwyMy42LDEzLjVMMjMuNiwxMy41eiBNNC4xLDcuNmMwLjgtMS4zLDEuNS0yLjUsMi4yLTMuOGM3LjIsMCwxNC40LDAsMjEuNiwwDQoJYzAuOCwxLjMsMS41LDIuNSwyLjIsMy44QzIxLjUsNy42LDEyLjgsNy42LDQuMSw3LjZ6Ii8+DQo8L3N2Zz4NCg==';
		
		$user = wp_get_current_user();		
		
		if ( class_exists( 'WC_Product_Vendors' ) && in_array( 'wc_product_vendors_manager_vendor', (array) $user->roles ) ) {
			add_menu_page( __( 'Fulfillment', 'ast-pro' ), __( 'Fulfillment', 'ast-pro' ), 'manage_product', 'vendor-fulfillment-dashboard', array( $this, 'ast_pro_fulfillment_dashboard_wc_vendor_page_callback' ), $fullfillment_icon, '55.7' );
			add_submenu_page( 'vendor-fulfillment-dashboard', __( 'Unfulfilled', 'ast-pro' ), __( 'Unfulfilled Orders', 'ast-pro' ), 'manage_product', 'vendor-fulfillment-dashboard', array( $this, 'ast_pro_fulfillment_dashboard_wc_vendor_page_callback' ) );
		}
	}
	
	public function fulfillment_header( $heading, $parent_label, $parent_url ) {
		include 'views/fulfillment_settings_header.php';
	}
	
	public function ast_pro_fulfillment_dashboard_wc_vendor_page_callback() {
		?>
		<div class="zorem-layout">			
			<?php $this->fulfillment_header( 'Unfulfilled Orders', 'Fulfillment', admin_url() . 'admin.php?page=fulfillment-dashboard' ); ?>
			<div class="woocommerce zorem_admin_layout ast_fulfillment_dashboard">
				<div class="ast_menu_container">					
					<?php require_once( 'views/vendor_fulfillment_dashboard.php' ); ?>		
				</div>						
			</div>						
		</div>		
		<?php
	}
	
	public function get_vendor_unfulfilled_orders() {
		
		if ( ! current_user_can( 'manage_product' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_fullfillment_dashbaord', 'ajax_nonce' );						
				
		$default_order_status = array();
		$default_order_status[] = 'wc-processing';
		
		$fulfillment_filter = isset( $_POST['fulfillment_filter'] ) ? wc_clean( $_POST['fulfillment_filter'] ) : '';
		
		if ( 'unfulfilled' == $fulfillment_filter ) {
			$orders_data = $this->get_unfulfilled_orders_data();
			$orders = $orders_data['orders'];
			$total_orders = $orders_data['total_orders'];	
		} else {		
			$orders_data = $this->get_fulfilled_orders_data();
			$orders = $orders_data['orders'];
			$total_orders = $orders_data['total_orders'];
		}
		
		
		$i = 0;
		$result = array();
		foreach ( $orders as $order_data ) {
			
			$result[$i] = new \stdClass();
			$order = wc_get_order( $order_data->order_id);
			
			if ( is_a( $order, 'WC_Order_Refund' ) ) {
				continue;
			}
			
			$datetime = $order->get_date_created();
						
			$result[$i]->order_id = $order->get_id();
			$result[$i]->order_number = $order->get_order_number();
			$result[$i]->order_items = $order_data->product_quantity;						
		
			$result[$i]->order_date = $datetime->format('M d') . ' at ' . $datetime->format('h:i a');
			$result[$i]->order_status = sprintf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ), esc_html( wc_get_order_status_name( $order->get_status() ) ) );
			$result[$i]->ship_to = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
			if ( null != $order->get_shipping_country() ) {
				$result[$i]->shipping_country = WC()->countries->countries[ $order->get_shipping_country() ];			
			} else {
				$result[$i]->shipping_country = '';
			}
			$result[$i]->shipping_method = $order->get_shipping_method();	
			$result[$i]->shipment_tracking = $this->vendor_shipment_tracking_cl( $order->get_id(), get_current_user_id() );
			$result[$i]->shipment_status = do_action( 'vendor_shipment_status_cl', $order->get_id(), get_current_user_id() );	
			
			$actions['vendor_add_tracking'] = array(
				'url'       => '#' . $order->get_id(),
				'name'      => __( 'Add Tracking', 'woo-advanced-shipment-tracking' ),
				'icon' => '<i class="fa fa-map-marker">&nbsp;</i>',
				'action'    => 'add_inline_tracking_vendor', // keep "view" class for a clean button CSS
			);			
			
			$result[$i]->actions_html = ast_pro()->ast_pro_fulfillment_dashboard->ast_render_action_buttons( $actions );
		
			$i++;
		}
		
		$draw = ( isset( $_POST['draw'] ) ) ? wc_clean( $_POST['draw'] ) : '' ;
		
		$obj_result = new \stdclass();
		$obj_result->draw = intval( $draw );
		$obj_result->recordsTotal = intval( count($total_orders) );
		$obj_result->recordsFiltered = intval( count($total_orders) );
		$obj_result->data = $result;
		$obj_result->filter = 100;
		$obj_result->last_sql = $paginated_query;
		$obj_result->is_success = true;
		echo json_encode($obj_result);
		exit;
	}

	public function get_unfulfilled_orders_data() {
		
		if ( ! current_user_can( 'manage_product' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_fullfillment_dashbaord', 'ajax_nonce' );					
		
		$row = ( isset( $_POST['start'] ) ) ? wc_clean( $_POST['start'] ) : '' ;
		$rowperpage = ( isset( $_POST['length'] ) ) ? wc_clean( $_POST['length'] ) : '' ;
		$shipping_method = ( isset( $_POST['shipping_method_filter'] ) ) ? wc_clean( $_POST['shipping_method_filter'] ) : '' ;
		$search_input = ( isset( $_POST['fulfillment_search_input'] ) ) ? wc_clean( $_POST['fulfillment_search_input'] ) : null ;
		
		if ( null != $search_input ) {
			$order_query = "AND commission.order_id LIKE '" . $search_input . "%'";
		} else {
			$order_query = '';
		}
		
		global $wpdb;
		global $sitepress;
		$vendor_table = $wpdb->prefix . 'wcpv_commissions';
		
		// remove WPML term filters
		remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );
		remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
		remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
		
		$terms_objects = ( class_exists( 'WC_Product_Vendors_Utils' ) ) ? WC_Product_Vendors_Utils::get_all_vendor_data() : get_current_user_id();
		
		// restore WPML term filters
		add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 3 );
		add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );
		add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter'), 10, 2 );
		
		$vendor_ids = implode(',', array_keys($terms_objects));
		$shipping_method = "AND order_item_name REGEXP '" . $shipping_method . "' )";
		
		$orders = $wpdb->get_results( $wpdb->prepare( "
		SELECT commission.id,commission.order_id,commission.product_quantity 
		FROM %1s AS commission 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta
		ON order_itemmeta.order_item_id = commission.order_item_id
		LEFT JOIN {$wpdb->posts} AS posts
		ON posts.ID = commission.order_id
		WHERE 
			commission.vendor_id IN (%2s) 
			%3s 
			AND posts.post_status IN ('wc-processing','wc-partial-shipped')
			GROUP BY commission.order_id
			ORDER BY commission.order_id DESC
			LIMIT %4d, %5d				
			", $vendor_table, $vendor_ids, $order_query, $row, $rowperpage ) );

		$total_orders = $wpdb->get_results( $wpdb->prepare( "
		SELECT commission.id,commission.order_id,commission.product_quantity 
		FROM %1s AS commission 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta
		ON order_itemmeta.order_item_id = commission.order_item_id
		LEFT JOIN {$wpdb->posts} AS posts
		ON posts.ID = commission.order_id
		WHERE 
			commission.vendor_id IN (%2s) 
			%3s 
			AND posts.post_status IN ('wc-processing','wc-partial-shipped')
			GROUP BY commission.order_id
			ORDER BY commission.order_id DESC				
			", $vendor_table, $vendor_ids, $order_query ) );

		$orders_data = array();
		$orders_data['total_orders'] = $total_orders;
		$orders_data['orders'] = $orders;
		return $orders_data;	
	}

	public function get_fulfilled_orders_data() {
		if ( ! current_user_can( 'manage_product' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_fullfillment_dashbaord', 'ajax_nonce' );					
		
		$row = ( isset( $_POST['start'] ) ) ? wc_clean( $_POST['start'] ) : '' ;
		$rowperpage = ( isset( $_POST['length'] ) ) ? wc_clean( $_POST['length'] ) : '' ;
		$shipping_method = ( isset( $_POST['shipping_method_filter'] ) ) ? wc_clean( $_POST['shipping_method_filter'] ) : '' ;
		$search_input = ( isset( $_POST['fulfillment_search_input'] ) ) ? wc_clean( $_POST['fulfillment_search_input'] ) : null ;
		
		if ( null != $search_input ) {
			$order_query = "AND commission.order_id LIKE '" . $search_input . "%'";
		} else {
			$order_query = '';
		}
		
		global $wpdb;
		global $sitepress;
		$vendor_table = $wpdb->prefix . 'wcpv_commissions';
		
		// remove WPML term filters
		remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );
		remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
		remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
		
		$terms_objects = ( class_exists( 'WC_Product_Vendors_Utils' ) ) ? WC_Product_Vendors_Utils::get_all_vendor_data() : get_current_user_id();
		
		// restore WPML term filters
		add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 3 );
		add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );
		add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter'), 10, 2 );
		
		$vendor_ids = implode(',', array_keys($terms_objects));
		$shipping_method = "AND order_item_name REGEXP '" . $shipping_method . "' )";

		$orders = $wpdb->get_results( $wpdb->prepare( "
		SELECT commission.id,commission.order_id,commission.product_quantity 
		FROM %1s AS commission 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta
		ON order_itemmeta.order_item_id = commission.order_item_id
		LEFT JOIN {$wpdb->posts} AS posts
		ON posts.ID = commission.order_id
		WHERE 
			commission.vendor_id IN (%2s) 
			%3s 
			AND posts.post_status IN ('wc-completed','wc-shipped','wc-delivered')
			GROUP BY commission.order_id
			ORDER BY commission.order_id DESC
			LIMIT %4d, %5d				
			", $vendor_table, $vendor_ids, $order_query, $row, $rowperpage ) );

		$total_orders = $wpdb->get_results( $wpdb->prepare( "
		SELECT commission.id,commission.order_id,commission.product_quantity 
		FROM %1s AS commission 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta
		ON order_itemmeta.order_item_id = commission.order_item_id
		LEFT JOIN {$wpdb->posts} AS posts
		ON posts.ID = commission.order_id
		WHERE 
			commission.vendor_id IN (%2s) 
			%3s 
			AND posts.post_status IN ('wc-completed','wc-shipped','wc-delivered')
			GROUP BY commission.order_id
			ORDER BY commission.order_id DESC				
			", $vendor_table, $vendor_ids, $order_query ) );

		$orders_data = array();
		$orders_data['total_orders'] = $total_orders;
		$orders_data['orders'] = $orders;
		return $orders_data;
	}

	public function vendor_shipment_tracking_cl( $order_id, $vendor_id ) {
		ob_start();
		$tracking_items = ast_get_tracking_items( $order_id );
		if ( count( $tracking_items ) > 0 ) {
			echo '<ul class="wcast-tracking-number-list">';

			foreach ( $tracking_items as $tracking_item ) {
				if ( isset( $tracking_item[ 'vendor_id' ] ) && get_current_user_id() == $tracking_item[ 'vendor_id' ] ) {
					global $wpdb;
					
					$tracking_provider = isset( $tracking_item['tracking_provider'] ) ? $tracking_item['tracking_provider'] : $tracking_item['custom_tracking_provider'];
					$tracking_provider = apply_filters( 'convert_provider_name_to_slug', $tracking_provider );
	
					$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE ts_slug = %s', $this->table, $tracking_provider ) );
					
					$provider_name = apply_filters('get_ast_provider_name', $tracking_provider, $results);								
					
					if ( $tracking_item['ast_tracking_link'] ) {
						printf(
							'<li id="tracking-item-%s" class="tracking-item-%s"><div><b>%s</b></div><a href="%s" target="_blank" class=ft11>%s</a><a class="inline_tracking_delete" rel="%s" data-order="%s" data-nonce="' . esc_html( wp_create_nonce( 'delete-tracking-item' ) ) . '"><span class="dashicons dashicons-trash"></span></a></li>',
							esc_attr( $tracking_item['tracking_id'] ),
							esc_attr( $tracking_item['tracking_id'] ),
							esc_html( $provider_name ),
							esc_url( $tracking_item['ast_tracking_link'] ),
							esc_html( $tracking_item['tracking_number'] ),
							esc_attr( $tracking_item['tracking_id'] ),
							esc_attr( $order_id )
						);						
					} else {
						printf(
							'<li id="tracking-item-%s" class="tracking-item-%s"><div><b>%s</b></div>%s<a class="inline_tracking_delete" rel="%s" data-order="%s" data-nonce="' . esc_html( wp_create_nonce( 'delete-tracking-item' ) ) . '"><span class="dashicons dashicons-trash"></span></a></li>',
							esc_attr( $tracking_item['tracking_id'] ),
							esc_attr( $tracking_item['tracking_id'] ),
							esc_html( $provider_name ),
							esc_html( $tracking_item['tracking_number'] ),
							esc_attr( $tracking_item['tracking_id'] ),
							esc_attr( $order_id )
						);						
					}
					//do_action(	'ast_after_tracking_number', $order_id, $tracking_item['tracking_id'] );	
				}							
			}			
			echo '</ul>';
		} else {
			echo '–';			
		}
		return ob_get_clean();		
	}	
	
	public function ast_open_inline_tracking_form_for_vendor_fun() {
		
		if ( ! current_user_can( 'manage_product' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'ast-order-list', 'security' );
		
		$order_id = ( isset($_POST['order_id']) ) ? wc_clean( $_POST['order_id'] ) : '';
		
		global $wpdb;
		$vendor_table = $wpdb->prefix . 'wcpv_commissions';
		
		$order = wc_get_order( $order_id );
		$order_number = $order->get_order_number();
				
		$WC_Countries = new WC_Countries();
		$countries = $WC_Countries->get_countries();
		$shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s', $this->table ) );
		$shippment_countries = $wpdb->get_results( $wpdb->prepare( 'SELECT shipping_country FROM %1s WHERE display_in_order = 1 GROUP BY shipping_country', $this->table ) );		
		$default_provider = get_option( 'wc_ast_default_provider' );

		foreach ( $shippment_providers as $provider ) {
			$provider_array[ sanitize_title( $provider->provider_name ) ] = urlencode( $provider->provider_url );
		}
				
		ob_start();
		?>
		<div id="" class="trackingpopup_wrapper add_tracking_popup" style="display:none;">
			<div class="trackingpopup_row">
				<div class="popup_header">
					<h3 class="popup_title"><?php esc_html_e( 'Add Tracking - order	', 'ast-pro'); ?> - #<?php esc_html_e( $order_number ); ?></h2>					
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body">
					<form id="add_tracking_number_form" method="POST" class="add_tracking_number_form">	
						<?php do_action( 'ast_vendor_tracking_form_between_form', $order_id, 'inline' ); ?>
						<p class="form-field tracking_number_field form-50">
							<label for="tracking_number"><?php esc_html_e( 'Tracking number:', 'ast-pro'); ?></label>
							<input type="text" class="short" name="tracking_number" id="tracking_number" value="" autocomplete="off"> 
						</p>
						<p class="form-field form-50">
							<label for="tracking_number"><?php esc_html_e( 'Shipping Provider:', 'ast-pro'); ?></label>
							<select class="chosen_select tracking_provider_dropdown" id="tracking_provider" name="tracking_provider">
								<option value=""><?php esc_html_e( 'Shipping Provider:', 'ast-pro' ); ?></option>
								<?php 
									
								foreach ( $shippment_countries as $s_c ) {
									
									if ( 'Global' != $s_c->shipping_country ) {
										$country_name = esc_attr( $WC_Countries->countries[ $s_c->shipping_country ] );
									} else {
										$country_name = 'Global';
									}
									
									echo '<optgroup label="' . esc_html( $country_name ) . '">';
									
									$country = $s_c->shipping_country;				
									$shippment_providers_by_country = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE shipping_country = %s AND display_in_order = 1', $this->table, $country ) );
									
									foreach ( $shippment_providers_by_country as $providers ) {
										$selected = ( esc_attr( $providers->id ) == $default_provider ) ? 'selected' : '';
										echo '<option value="' . esc_attr( $providers->ts_slug ) . '" ' . esc_html( $selected ) . '>' . esc_html( $providers->provider_name ) . '</option>';
									}
									
									echo '</optgroup>';
								}
								?>
							</select>
						</p>					
						<p class="form-field tracking_product_code_field form-50">
							<label for="tracking_product_code"><?php esc_html_e( 'Product Code:', 'ast-pro'); ?></label>
							<input type="text" class="short" name="tracking_product_code" id="tracking_product_code" value=""> 
						</p>
						<p class="form-field date_shipped_field form-50">
							<label for="date_shipped"><?php esc_html_e( 'Date shipped:', 'ast-pro'); ?></label>
							<input type="text" class="ast-date-picker-field" name="date_shipped" id="date_shipped" value="<?php echo esc_html( date_i18n( __( 'Y-m-d', 'ast-pro' ), current_time( 'timestamp' ) ) ); ?>" placeholder="<?php echo esc_html( date_i18n( esc_html_e( 'Y-m-d', 'ast-pro' ), time() ) ); ?>">						
						</p>								
						<?php do_action( 'ast_after_tracking_field', $order_id ); ?>
						<hr>
						<p>		
							<?php wp_nonce_field( 'wc_ast_inline_tracking_form', 'wc_ast_inline_tracking_form_nonce' ); ?>
							<input type="hidden" name="action" value="add_inline_tracking_number">
							<input type="hidden" name="vendor_id" id="vendor_id" value="<?php esc_html_e( get_current_user_id() ); ?>">
							<input type="hidden" name="order_id" id="order_id" value="<?php esc_html_e( $order_id ); ?>">
							<input type="submit" name="Submit" value="<?php esc_html_e( 'Fulfill Items', 'ast-pro' ); ?>" class="button-primary btn_green">        
						</p>
						<p class="preview_tracking_link"><?php esc_html_e( 'Preview:', 'ast-pro' ); ?>&nbsp;<a href="" target="_blank"><?php esc_html_e( 'Track Shipment', 'ast-pro' ); ?></a></p>	
					</form>
				</div>								
			</div>
			<div class="popupclose"></div>
		</div>
		<script>
			jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field ').hide();

			jQuery( 'input#tracking_number, #tracking_provider' ).change( function() {

				var tracking  = jQuery( 'input#tracking_number' ).val();
				var provider  = jQuery( '#tracking_provider' ).val();
				var providers = jQuery.parseJSON( '<?php echo json_encode( $provider_array ); ?>' );				

				var link = '';

				if ( providers[ provider ] ) {
					link = providers[provider];
					link = link.replace( '%25number%25', tracking );					
					link = decodeURIComponent( link );

					jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).hide();
				} else {
					jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).show();

					link = jQuery( 'input#custom_tracking_link' ).val();
				}

				if ( link ) {
					jQuery( 'p.preview_tracking_link a' ).attr( 'href', link );
					jQuery( 'p.preview_tracking_link' ).show();
				} else {
					jQuery( 'p.preview_tracking_link' ).hide();
				}

			} ).change();
		</script>
		<?php			
		$html = ob_get_clean();
		$json['html'] = $html;
		wp_send_json_success( $json );
	}
	
	public function save_vendor_in_tracking_info_args( $args, $postdata, $order_id ) {
		
		if ( isset( $postdata['ASTProduct'] ) ) {
			
			$products_list = array();
				
			foreach ( $postdata[ 'ASTProduct' ] as $key => $value ) {
				
				if ( $value['qty'] > 0 ) {
					
					$product_data =  (object) array (
						'product' => $value['product'],
						'qty' => $value['qty'],
					);	
					array_push( $products_list, $product_data );								
				}
			}	
		}				
		
		if ( isset( $postdata['vendor_id'] ) && '' != $postdata['vendor_id'] ) {
			$args[ 'vendor_id' ] = $postdata['vendor_id'];
			
			$wc_ast_status_partial_shipped	= get_option( 'wc_ast_status_partial_shipped', 1 );
			$args[ 'status_shipped' ] = ( 1 == $wc_ast_status_partial_shipped ) ? 2 : 0;
			
			$tpi = AST_Tpi::get_instance();
			$args = $tpi->autocomplete_order_after_adding_all_products( $order_id, $args, $products_list );
		}
		return $args;		
	}
	
	public function save_vendor_in_tracking_item_args( $tracking_item, $args, $order_id ) {
		if ( isset( $args['vendor_id'] ) && '' != $args['vendor_id'] ) {
			$tracking_item[ 'vendor_id' ] = $args['vendor_id'];
		}
		return $tracking_item;		
	}
	
	public function ast_save_tracking_details_end( $order_id, $postdata ) {
		
		$enable_tracking_per_item = isset( $postdata[ 'enable_tracking_per_item' ] ) ? wc_clean( $postdata[ 'enable_tracking_per_item' ] ) : '';
		
		if ( 1 == $enable_tracking_per_item ) {
			if ( isset( $postdata['ASTProduct'] ) ) {	
			
				$products_list = array();
				
				foreach ( $postdata['ASTProduct'] as $product ) {				
					if ( $product['qty'] > 0 ) {						
						$item_id = $product['item_id'];
						if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
							WC_Product_Vendors_Utils::set_fulfillment_status( absint( $item_id ), 'fulfilled' );							
						}						
					}
				}																							
			}
		}		
	}
	
	public function wcpv_vendor_order_detail_order_data_column( $order ) {
		?>
		<style>
		#order_data .order_data_column {
			width: 31%;
		}
		.vendor-tracking-item .tracking-content {
			background: #efefef none repeat scroll 0 0;
			padding: 10px;
			position: relative;
			margin: 10px 0 0;
		}
		.vendor-tracking-item .tracking-content:after {
			content: "";
			display: block;
			position: absolute;
			bottom: -10px;
			left: 20px;
			width: 0;
			height: 0;
			border-width: 10px 10px 0 0;
			border-style: solid;
			border-color: #efefef transparent;
		}
		.vendor-tracking-item .tracking-content-div {
			margin-bottom: 5px;
		}
		#order_data .order_data_column .vendor-tracking-item .meta {
			font-size: 11px;
			color: #999 !important;
			padding: 10px !important;
			margin: 0;
		}
		</style>
		<?php
		$tracking_items = ast_get_tracking_items( $order->get_id() );
		
		echo '<div class="order_data_column">';
		echo '<h4>' . esc_html( 'Shipment Tracking', 'ast-pro' ) . '</h4>';
		
		echo '<p><a href="#' . esc_html( $order->get_id() ) . '" class="button button-primary btn_ast2 add_inline_tracking_vendor" type="button">' . esc_html( 'Add Tracking Info', 'ast-pro' ) . '</a></p>';
		
		if ( count( $tracking_items ) > 0 ) {
			echo '<div class="vendor-tracking-item" id="woocommerce-advanced-shipment-tracking">';
			
			foreach ( $tracking_items as $tracking_item ) {
				if ( isset( $tracking_item[ 'vendor_id' ] ) && get_current_user_id() == $tracking_item[ 'vendor_id' ] ) {
					
					break;
				}
			}
			
			foreach ( $tracking_items as $tracking_item ) {
				if ( isset( $tracking_item[ 'vendor_id' ] ) && get_current_user_id() == $tracking_item[ 'vendor_id' ] ) {
					$this->display_html_tracking_item_for_meta_box( $order->get_id(), $tracking_item );
				}
			}
			echo '</div>';
		}	
		echo '</div>';
	}
	
	/**
	 * Returns a HTML node for a tracking item for the admin meta box
	 */
	public function display_html_tracking_item_for_meta_box( $order_id, $item ) {
			
		wp_enqueue_style( 'trackshipcss' );
		wp_enqueue_script( 'trackship_script' );
		wp_enqueue_script( 'vendor_fulfillment_script' );		
		$order = wc_get_order( $order_id );					
		global $wpdb;
		$ast = AST_Pro_Actions::get_instance();
		$formatted = $ast->get_formatted_tracking_item( $order_id, $item );			
		$tracking_provider = isset( $item['tracking_provider'] ) ? $item['tracking_provider'] : $item['custom_tracking_provider'];
		$tracking_provider = apply_filters( 'convert_provider_name_to_slug', $tracking_provider );
		$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE ts_slug = %s', $this->table, $tracking_provider ) );		
		$provider_name = apply_filters( 'get_ast_provider_name', $tracking_provider, $results );
		?>
		<div class="tracking-item" id="tracking-item-<?php echo esc_attr( $item['tracking_id'] ); ?>">
			<div class="tracking-content">
				<div class="tracking-content-div">
					<strong><?php echo esc_html( $provider_name ); ?></strong>						
						<?php 
						if ( strlen( $formatted['ast_tracking_link'] ) > 0 ) { 
							?>
						- 
						<?php 							
							echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Track Shipment', 'ast-pro' ) ) . '">' . esc_html( $item['tracking_number'] ) . '</a>', esc_url( $formatted['ast_tracking_link'] ) ); 
						} else { 
							?>
						<span> - <?php esc_html_e( $item['tracking_number'] ); ?></span>
					<?php } ?>
				</div>					
				<?php 
				do_action(	'ast_after_tracking_number', $order_id, $item['tracking_id'] );
				do_action(	'ast_shipment_tracking_end', $order_id, $item ); 
				?>
			</div>
			<p class="meta">
				<?php /* translators: 1: shipping date */ ?>
				<?php echo esc_html( sprintf( __( 'Shipped on %s', 'ast-pro' ), date_i18n( get_option( 'date_format' ), $item['date_shipped'] ) ) ); ?>
				<a href="#" class="delete-vendor-tracking" rel="<?php echo esc_attr( $item['tracking_id'] ); ?>" data-orderid="<?php echo esc_attr( $order_id ); ?>" ><?php esc_html_e( 'Delete', 'woocommerce' ); ?></a>
				<input type="hidden" name="vendor_tracking_delete_nonce" id="vendor_tracking_delete_nonce" value="<?php echo esc_html( wp_create_nonce( 'delete-tracking-item' ) ); ?>" >
			</p>
		</div>	
		<?php		
	}
}	
