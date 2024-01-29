<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_PRO_Fulfillment_Dashboard {
	
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
	 * @return AST_PRO_Fulfillment_Dashboard
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
		add_action( 'admin_footer', array( $this, 'order_preview_template' ) );
		//load shipments css js 
		add_action( 'admin_enqueue_scripts', array( $this, 'fulfillment_dashboard_styles' ), 1);
		
		add_action( 'wp_ajax_get_unfulfilled_orders', array($this, 'get_unfulfilled_orders') );		
		add_action( 'wp_ajax_show_fulfilled_order_items', array($this, 'show_fulfilled_order_items') );							
	}	
	
	/* 
	* Register Fulfillment Menu
	*/
	public function register_fulfillment_menu() {
		
		$fullfillment_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjQwcHgiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNDAgNDAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggaWQ9IlhNTElEXzEyXyIgZmlsbD0iI0YwRjZGQyIgZD0iTTI1LjMsMjIuNWMzLjQtMi4zLDguMy0yLDExLjQsMC44YzEuOSwxLjYsMywzLjksMy4zLDYuM3YxLjVjLTAuMyw0LjctNC4yLDguNi04LjksOC45aC0xLjMNCgljLTQtMC4zLTcuNy0zLjMtOC42LTcuMkMyMC4zLDI5LDIyLDI0LjYsMjUuMywyMi41IE0zMy42LDI3Yy0xLjUsMS4xLTIuNywyLjYtNCwzLjljLTAuNy0wLjctMS4zLTEuNC0yLjEtMg0KCWMtMS4xLTAuNi0yLjcsMC4yLTIuNywxLjVjMCwxLjMsMS4yLDIsMiwzYzEsMC44LDEuOSwyLjMsMy4zLDJjMS4xLTAuNSwxLjgtMS41LDIuNi0yLjJjMS4xLTEuMiwyLjUtMi4zLDMuNS0zLjcNCglDMzYuOSwyNy45LDM1LjEsMjYuMiwzMy42LDI3TDMzLjYsMjd6Ii8+DQo8cGF0aCBpZD0iWE1MSURfMTBfIiBmaWxsPSIjRjBGNkZDIiBkPSJNMzIsMy4yYy0wLjgtMS4zLTEuNC0zLjQtMy40LTMuMkMyMSwwLDEzLjMsMCw1LjcsMGMtMi0wLjItMi41LDEuOS0zLjQsMy4yDQoJQzEuNCw0LjktMC4yLDYuNSwwLDguNmMwLDcuMywwLDE0LjYsMCwyMS45Yy0wLjEsMiwxLjcsMy44LDMuNywzLjdjNC42LDAuMSw5LjIsMCwxMy45LDBjLTEuMy00LjcsMC0xMC4xLDMuNi0xMy41DQoJYzMuNC0zLjMsOC41LTQuNCwxMy0zLjFjMC0zLDAtNi4xLDAtOS4xQzM0LjQsNi41LDMyLjksNC45LDMyLDMuMnogTTIzLjYsMTMuNWMwLDEtMC45LDEuOS0xLjksMS45SDEzYy0xLDAtMS45LTAuOS0xLjktMS45di0wLjMNCgljMC0xLDAuOS0xLjksMS45LTEuOWg4LjdjMSwwLDEuOSwwLjksMS45LDEuOUwyMy42LDEzLjVMMjMuNiwxMy41eiBNNC4xLDcuNmMwLjgtMS4zLDEuNS0yLjUsMi4yLTMuOGM3LjIsMCwxNC40LDAsMjEuNiwwDQoJYzAuOCwxLjMsMS41LDIuNSwyLjIsMy44QzIxLjUsNy42LDEyLjgsNy42LDQuMSw3LjZ6Ii8+DQo8L3N2Zz4NCg==';				
		
		add_menu_page( __( 'Fulfillment', 'ast-pro' ), __( 'Fulfillment', 'ast-pro' ), 'manage_woocommerce', 'fulfillment-dashboard', array( $this, 'ast_pro_fulfillment_dashboard_page_callback' ), $fullfillment_icon, '55.7' );
		add_submenu_page( 'fulfillment-dashboard', __( 'Unfulfilled', 'ast-pro' ), __( 'Unfulfilled Orders', 'ast-pro' ), 'manage_woocommerce', 'fulfillment-dashboard', array( $this, 'ast_pro_fulfillment_dashboard_page_callback' ) );
		add_submenu_page( 'fulfillment-dashboard', __( 'CSV Import', 'ast-pro' ), __( 'CSV Import', 'ast-pro' ), 'manage_woocommerce', 'ast-csv-import', array( ast_pro()->ast_pro_csv_import, 'csv_import_page_callback' ) );
		
		$wc_ast_api_key = get_option('wc_ast_api_key');
		$ts4wc_installed = ( function_exists( 'trackship_for_woocommerce' ) ) ? true : false;
		$trackship_display = ( !$wc_ast_api_key && !$ts4wc_installed ) ? true : false ;
		
		if ( $trackship_display ) {
			add_submenu_page( 'fulfillment-dashboard', 'TrackShip', 'TrackShip', 'manage_woocommerce', 'ast-trackship', array( $this, 'trackship_page_callback' ));
		}
	}
	
	public function trackship_page_callback() { 
		?>
		<div class="zorem-layout">
			<?php 
			include 'views/settings_header.php'; 
			?>
			<div class="woocommerce zorem_admin_layout">
				<div class="ast_admin_content" >
					<div class="ast_nav_div">											
						<?php include 'views/admin_options_trackship_integration.php'; ?>														
					</div>                   					
				</div>				
			</div>					
		</div>	
	<?php 
	}
		
	/**
	* Load trackship styles.
	*/
	public function fulfillment_dashboard_styles( $hook ) {
		
		if ( !isset( $_GET['page'] ) ) {
			return;
		}
		
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';		
			
		wp_register_script( 'fulfillment_script', ast_pro()->plugin_dir_url() . 'assets/js/fulfillment.js', array( 'jquery' ), ast_pro()->version, true );
		wp_register_script( 'vendor_fulfillment_script', ast_pro()->plugin_dir_url() . 'assets/js/vendor_fulfillment.js', array( 'jquery' ), ast_pro()->version, true );
		wp_register_script( 'DataTable', ast_pro()->plugin_dir_url() . 'assets/js/jquery.dataTables.min.js', array ( 'jquery' ), '1.10.18', true);
		wp_register_script( 'tpi_scripts', ast_pro()->plugin_dir_url() . 'assets/js/tpi.js' , array( 'jquery', 'wp-util' ), ast_pro()->version, true );		
		wp_register_script( 'ast_pro_autodetector_orders', ast_pro()->plugin_dir_url() . 'assets/js/autodetector.js' , array( 'jquery'), ast_pro()->version, true );
		wp_register_script( 'wc-backbone-modal', WC()->plugin_url() . '/assets/js/admin/backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), ast_pro()->version );
		
		if ( 'fulfillment-dashboard' != $_GET['page'] && 'vendor-fulfillment-dashboard' != $_GET['page'] && 'wcpv-vendor-order' != $_GET['page'] && 'ast-csv-import' != $_GET['page'] ) {
			return;
		}
		
		wp_enqueue_script('jquery-ui-datepicker');
		wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), ast_pro()->version );
		wp_enqueue_style( 'jquery-ui-style' );
		
		//dataTables library
		wp_enqueue_script( 'DataTable' );
		wp_enqueue_style( 'DataTable', ast_pro()->plugin_dir_url() . 'assets/css/jquery.dataTables.min.css', array(), '1.10.18', 'all');		

		wp_enqueue_style( 'tpi_styles', ast_pro()->plugin_dir_url() . 'assets/css/tpi.css', array(), ast_pro()->version );
		wp_enqueue_script( 'tpi_scripts' );		
		
		wp_enqueue_script( 'ast_pro_autodetector_orders' );	
		
		wp_enqueue_style( 'fulfillment_styles', ast_pro()->plugin_dir_url() . 'assets/css/fulfillment.css', array(), ast_pro()->version );
		
		wp_enqueue_script( 'wc-backbone-modal' );
		wp_enqueue_script( 'fulfillment_script' );			
		wp_enqueue_script( 'vendor_fulfillment_script' );		
		wp_localize_script('fulfillment_script', 'fulfillment_script', array(
			'admin_url'   =>  admin_url(),	
			'preview_nonce' => wp_create_nonce( 'woocommerce-preview-order' ),	
		));		
	}
	
	public function fulfillment_header( $heading, $parent_label, $parent_url ) {
		include 'views/fulfillment_settings_header.php';
	}
	
	public function ast_pro_fulfillment_dashboard_page_callback() {
		?>
		<div class="zorem-layout">			
			<?php $this->fulfillment_header( 'Unfulfilled Orders', 'Fulfillment', admin_url() . 'admin.php?page=fulfillment-dashboard' ); ?>
			<div class="woocommerce zorem_admin_layout ast_fulfillment_dashboard">
				<div class="ast_menu_container">					
					<?php require_once( 'views/fulfillment_dashboard.php' ); ?>		
				</div>						
			</div>						
		</div>		
		<?php
	}		
	
	public function get_unfulfilled_orders() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_fullfillment_dashbaord', 'ajax_nonce' );						
				
		$default_order_status = array();
		$default_order_status[] = 'wc-processing';
		
		$fulfillment_filter = isset( $_POST['fulfillment_filter'] ) ? wc_clean( $_POST['fulfillment_filter'] ) : '';
		
		if ( 'unfulfilled' == $fulfillment_filter ) {
			$order_status = get_option( 'ast_order_display_in_fulfillment_dashboard', $default_order_status );
			$date_created = '';
		} else {
			$order_status[] = 'wc-completed';
			$order_status[] = 'wc-shipped';
			$order_status[] = 'wc-delivered';
			$date_created = 'AND post_date > ' . ( time() - 2592000 );
		}
		
		$order_status_string = '';
		$i = 0;
		foreach ( $order_status as $status ) {
			if ( 0 == $i ) {
				$order_status_string .= "'" . $status . "'";	
			} else {
				$order_status_string .= ",'" . $status . "'";	
			}	 		
			 $i++;
		}	
		
		$row = ( isset( $_POST['start'] ) ) ? wc_clean( $_POST['start'] ) : '' ;
		$rowperpage = ( isset( $_POST['length'] ) ) ? wc_clean( $_POST['length'] ) : '' ;
		$shipping_method = ( isset( $_POST['shipping_method_filter'] ) ) ? wc_clean( $_POST['shipping_method_filter'] ) : '' ;
		$search_input = ( isset( $_POST['fulfillment_search_input'] ) ) ? wc_clean( $_POST['fulfillment_search_input'] ) : null ;
		
		if ( null != $search_input ) {
			$order_query = "AND ( posts.ID LIKE '" . $search_input . "%' OR postmeta.meta_value LIKE '" . $search_input . "%' )";			
		} else {
			$order_query = '';
		}
		
		global $wpdb;
		$limit = 'LIMIT ' . $row . ',' . $rowperpage . '';
		$shipping_method = "AND order_item_name REGEXP '" . $shipping_method . "' )";
		
		$query = "
			SELECT 
				posts.post_status as ordr_status,
				posts.ID AS order_id
			FROM {$wpdb->posts} AS posts
			LEFT JOIN
					{$wpdb->postmeta} AS postmeta ON(posts.ID = postmeta.post_id)
			WHERE 				
				posts.post_type LIKE 'shop_order'
				AND posts.post_status IN (" . $order_status_string . ")
				$order_query
				AND posts.ID IN (SELECT " . $wpdb->prefix . "woocommerce_order_items.order_id FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_item_type = 'shipping' 
				AND order_item_name NOT LIKE 'Local Pickup%'
				$shipping_method		
				$date_created	
				GROUP BY order_id 
				ORDER BY order_id DESC				
				";				
		
		$paginated_query = $query . $limit;			
			
		$orders = $wpdb->get_results( $paginated_query );

		$total_orders = $wpdb->get_results( $query );				
		
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
			$result[$i]->order_items = $order->get_item_count();
			
			$ast = AST_Pro_Actions::get_instance();		
			$tracking_items = $ast->get_tracking_items( $order->get_id() );
			
			$shipped_items = 0;
			if ( !empty($tracking_items) ) {
				$shipped_items = $this->get_shipped_items( $tracking_items );
			}
			
			if ( $shipped_items > 0 ) {
				$order_items_html = '<a href="#' . $order->get_id() . '" class="show_fulfilled_order_items">' . $shipped_items . '/' . $order->get_item_count() . '</a>';
				$result[$i]->order_items = $order_items_html;
			}
					
		
			$result[$i]->order_date = $datetime->format('M d'). ' at '.$datetime->format('h:i a');
			$result[$i]->order_status = sprintf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ), esc_html( wc_get_order_status_name( $order->get_status() ) ) );
			$result[$i]->ship_to = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
			$result[$i]->order_view = '<a href="#" class="button wc-action-button fulfillment-order-preview" data-order-id="'.$order->get_id().'" title="Preview">Preview</a>';
			
			if ( null != $order->get_shipping_country() ) {
				$result[$i]->shipping_country = WC()->countries->countries[ $order->get_shipping_country() ];			
			} else {
				$result[$i]->shipping_country = '';
			}
			
			$result[$i]->shipping_method = $order->get_shipping_method();	
			
			$actions = $this->action_data( $order );
			//$actions = apply_filters( 'woocommerce_admin_order_actions', $actions, $order );	
			
			$result[$i]->actions_html = $this->ast_render_action_buttons( $actions );
		
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
	
	
	
	public function action_data( $order ) {
		
		$actions = array();			
			
		if ( class_exists( 'WPO_WCPDF' ) ) {
			$documents = WPO_WCPDF()->documents->get_documents();
			foreach ($documents as $document) {
				$document_title = $document->get_title();
				$icon = !empty($document->icon) ? $document->icon : WPO_WCPDF()->plugin_url() . '/assets/images/generic_document.png';
				$document = wcpdf_get_document( $document->get_type(), $order );
				if ( $document ) {
					$document_title = is_callable( array( $document, 'get_title' ) ) ? $document->get_title() : $document_title;
					$document_exists = is_callable( array( $document, 'exists' ) ) ? $document->exists() : false;
					$actions[$document->get_type()] = array(
						'url'    => wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type={$document->get_type()}&order_ids=" . $order->get_id() ), 'generate_wpo_wcpdf' ),
						'img'    => $icon,
						'name'    => 'PDF ' . $document_title,
						'exists' => $document_exists,
						'action' => $document->get_type(),	
						'class'  => apply_filters( 'wpo_wcpdf_action_button_class', $document_exists ? 'exists ' . $document->get_type() : $document->get_type(), $document ),
					);
				}
			}
		}
		
		$wc_ast_show_orders_actions = get_option( 'wc_ast_show_orders_actions' );
		$order_array = array();
		
		foreach ( $wc_ast_show_orders_actions as $order_status => $value ) {
			if ( 1 == $value ) {
				array_push($order_array, $order_status);			
			}	
		}
		
		if ( $order->get_shipping_method() != 'Local pickup' && $order->get_shipping_method() != 'Local Pickup' ) {		
			if ( $order->has_status( $order_array ) ) {			
				$actions['add_tracking'] = array(
					'url'       => '#' . $order->get_id(),
					'name'      => __( 'Add Tracking', 'woo-advanced-shipment-tracking' ),
					'icon' => '<i class="fa fa-map-marker">&nbsp;</i>',
					'action'    => 'add_inline_tracking', // keep "view" class for a clean button CSS
				);		
			}
		}
		
		return $actions;
	}
	
	public function ast_render_action_buttons( $actions ) {
		
		$actions_html = '';
		
		foreach ( $actions as $key => $action ) {
				
			if ( 'add_tracking' == $key || 'vendor_add_tracking' == $key ) {
				if ( isset( $action[ 'img'] ) ) {
					$actions_html .= sprintf( '<a class="button wc-action-img-button wc-action-button-%1$s %1$s" href="%2$s" aria-label="%3$s" title="%3$s" target="blank">%4$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( isset( $action['title'] ) ? $action['title'] : $action['name'] ), '<img src="' . $action['img'] . '" width="16">' );
				} else {
					$actions_html .= sprintf( '<a class="button wc-action-button wc-action-button-%1$s %1$s" href="%2$s" aria-label="%3$s" title="%3$s">%4$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( isset( $action['title'] ) ? $action['title'] : $action['name'] ), esc_html( $action['name'] ) );
				}	
			}

		}
		
		if ( count( $actions ) > 1 ){
			$actions_html .= '<span class="dashicons dashicons-ellipsis show_fulfillment_actions"></span><ul class="ast_fulfillment_actions_list">';
			foreach ( $actions as $key => $action ) {
				if ( 'add_tracking' != $key || 'vendor_add_tracking' != $key ) {
					$actions_html .= sprintf( '<li><a class=" wc-action-button-%1$s %1$s" href="%2$s" aria-label="%3$s" title="%3$s">%4$s</a></li>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( isset( $action['title'] ) ? $action['title'] : $action['name'] ), esc_html( $action['name'] ) );
				}
			}
			$actions_html .= '</ul>';
		}
	
		return $actions_html;
	}
	
	public function get_shipped_items( $tracking_items ) {
		$shipped_items = 0;
		
		foreach ( $tracking_items as $tracking_item ) {							
			if ( isset( $tracking_item[ 'products_list' ] ) && '' != $tracking_item[ 'products_list' ] ) {
				foreach ( $tracking_item[ 'products_list' ] as $products ) {	
					$shipped_items = $products->qty + $shipped_items;
				}
			}
		}
		return $shipped_items;	
	}
	
	public function show_fulfilled_order_items() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_fullfillment_dashbaord', 'security' );		
		
		$order_id = ( isset( $_POST['order_id'] ) ) ? wc_clean( $_POST['order_id'] ) : '' ;
		$order = wc_get_order( $order_id );
		$order_number = $order->get_order_number();
		
		$ast = AST_Pro_Actions::get_instance();
		$product_list = array();
		$tracking_items = $ast->get_tracking_items( $order_id );
		
		foreach ( $tracking_items as $tracking_item ) {			
			if ( isset( $tracking_item[ 'products_list' ] ) ) {
				$product_list[] = $tracking_item[ 'products_list' ];
			}
		}
		
		$all_list = array();		
		foreach ( $product_list as $list ) {
			if ( !empty($list ) ) {
				foreach ( (array) $list as $in_list ) {
					if ( isset( $in_list->item_id) ) {
						if ( isset( $all_list[ $in_list->item_id ] ) ) {
							$all_list[ $in_list->item_id ] = (int) $all_list[ $in_list->item_id ] + (int) $in_list->qty;							
						} else {
							$all_list[ $in_list->item_id ] = $in_list->qty;	
						}
					} else {
						if ( isset( $all_list[ $in_list->product ] ) ) {
							$all_list[ $in_list->product ] = (int) $all_list[ $in_list->product ] + (int) $in_list->qty;							
						} else {
							$all_list[ $in_list->product ] = $in_list->qty;	
						}	
					}					
				}				
			}
		}
		
		ob_start();
		?>
		<div id="" class="trackingpopup_wrapper fulfilled_order_items_popup" style="display:none;">
			<div class="trackingpopup_row">
				<div class="popup_header">
					<h3 class="popup_title"><?php esc_html_e( 'Order Items', 'ast-pro'); ?> - #<?php esc_html_e( $order_number ); ?></h2>					
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body" >
					<table class="wp-list-table widefat posts ast-product-table">
						<thead>
							<tr>
								<th><strong><?php esc_html_e( 'Fulfilled Product', 'ast-pro'); ?></strong></th>
								<th width="50"><strong><?php esc_html_e( 'Qty', 'ast-pro'); ?></strong></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach ( $tracking_items as $tracking_item ) {
								$product_list = $tracking_item[ 'products_list' ];
								if ( is_array( $product_list ) ) {
									foreach ( $product_list as $list ) { 
										$product = wc_get_product( $list->product );
										?>
										<tr>
											<td><?php esc_html_e( $product->get_title() ); ?></td>
											<td><?php esc_html_e( $list->qty ); ?></td>
										</tr>	
										<?php 
									}
								}
							}
							?>
						</tbody>
					</table>
					<table class="wp-list-table widefat fixed posts ast-product-table" style="border-top:1px solid #e0e0e0;">			
						<?php $items = $order->get_items(); ?>
						<thead>
							<tr>
								<th><strong><?php esc_html_e( 'Unfulfilled Product', 'ast-pro'); ?></strong></th>
								<th width="50"><strong><?php esc_html_e( 'Qty', 'ast-pro'); ?></strong></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$n = 0;
							$total_product = count( $items );
							
							foreach ( $items as $item_id => $item ) {												
								$product = $item->get_product();
								$checked = 0;
								$qty = $item->get_quantity();
								
								$variation_id = $item->get_variation_id();
								$product_id = $item->get_product_id();					
								
								if ( 0 != $variation_id ) {
									$product_id = $variation_id;
								}
													
								if ( array_key_exists( $product_id, $all_list ) ) {	
									if ( isset( $all_list[ $product_id ] ) ) {									  
										$qty = (int) $item->get_quantity() - (int) $all_list[ $product_id ];										
									}
								}
								
								if ( array_key_exists( $item_id, $all_list ) ) {	
									if ( isset( $all_list[ $item_id ] ) ) {									  
										$qty = (int) $item->get_quantity() - (int) $all_list[ $item_id ];										
									}
								}
								
								if ( $item->get_product_id() ) {
									$product = wc_get_product( $item->get_product_id() );																	
								}
								$qty = ( $qty < 0 ) ? 0 : $qty ;
								
								if ( 0 != $qty ) {
								?>
								<tr class="ASTProduct_row">
																
									<td><?php esc_html_e( $item->get_name() ); ?></td>
									<td style=""><?php esc_html_e( $qty ); ?></td>
								</tr>	
							<?php 
								} 
								$n++; 
							} 
							?>						
						</tbody>			
					</table>	
				</div>								
			</div>
			<div class="popupclose"></div>
		</div>
		<?php		
		$html = ob_get_clean();
		echo wp_kses_post( $html );
		exit;
	}	

	/**
	 * Template for order preview.	 
	 */
	public function order_preview_template() {
		?>
		<script type="text/template" id="tmpl-wc-modal-view-order">
			<div class="wc-backbone-modal wc-order-preview">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<mark class="order-status status-{{ data.status }}"><span>{{ data.status_name }}</span></mark>
							<?php /* translators: %s: order ID */ ?>
							<h1><?php echo esc_html( sprintf( __( 'Order #%s', 'woocommerce' ), '{{ data.order_number }}' ) ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
							</button>
						</header>
						<article>
							<?php do_action( 'woocommerce_admin_order_preview_start' ); ?>

							<div class="wc-order-preview-addresses">
								<div class="wc-order-preview-address">
									<h2><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h2>
									{{{ data.formatted_billing_address }}}

									<# if ( data.data.billing.email ) { #>
										<strong><?php esc_html_e( 'Email', 'woocommerce' ); ?></strong>
										<a href="mailto:{{ data.data.billing.email }}">{{ data.data.billing.email }}</a>
									<# } #>

									<# if ( data.data.billing.phone ) { #>
										<strong><?php esc_html_e( 'Phone', 'woocommerce' ); ?></strong>
										<a href="tel:{{ data.data.billing.phone }}">{{ data.data.billing.phone }}</a>
									<# } #>

									<# if ( data.payment_via ) { #>
										<strong><?php esc_html_e( 'Payment via', 'woocommerce' ); ?></strong>
										{{{ data.payment_via }}}
									<# } #>
								</div>
								<# if ( data.needs_shipping ) { #>
									<div class="wc-order-preview-address">
										<h2><?php esc_html_e( 'Shipping details', 'woocommerce' ); ?></h2>
										<# if ( data.ship_to_billing ) { #>
											{{{ data.formatted_billing_address }}}
										<# } else { #>
											<a href="{{ data.shipping_address_map_url }}" target="_blank">{{{ data.formatted_shipping_address }}}</a>
										<# } #>

										<# if ( data.shipping_via ) { #>
											<strong><?php esc_html_e( 'Shipping method', 'woocommerce' ); ?></strong>
											{{ data.shipping_via }}
										<# } #>
									</div>
								<# } #>

								<# if ( data.data.customer_note ) { #>
									<div class="wc-order-preview-note">
										<strong><?php esc_html_e( 'Note', 'woocommerce' ); ?></strong>
										{{ data.data.customer_note }}
									</div>
								<# } #>
							</div>

							{{{ data.item_html }}}

							<?php do_action( 'woocommerce_admin_order_preview_end' ); ?>
						</article>
						<footer>
							<div class="inner">
								{{{ data.actions_html }}}

								<a class="button button-primary button-large" aria-label="<?php esc_attr_e( 'Edit this order', 'woocommerce' ); ?>" href="<?php echo esc_url( admin_url( 'post.php?action=edit' ) ); ?>&post={{ data.data.id }}"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php
	}	
}	
