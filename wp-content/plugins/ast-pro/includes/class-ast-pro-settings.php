<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_PRO_Settings {		
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		
		global $wpdb;
		$this->table = $wpdb->prefix . 'woo_shippment_provider';
		
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
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Get the class instance
	 *
	 * @return AST_PRO_Settings
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
		
		//rename order status +  rename bulk action + rename filter
		add_filter( 'wc_order_statuses', array( $this, 'wc_renaming_order_status' ) );		
		add_filter( 'woocommerce_register_shop_order_post_statuses', array( $this, 'filter_woocommerce_register_shop_order_post_statuses' ), 10, 1 );
		
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'modify_bulk_actions' ), 50, 1 );
		add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'modify_bulk_actions' ), 50, 1 );

		add_action( 'woocommerce_update_options_email_customer_partial_shipped_order', array( $this, 'save_partial_shipped_email' ), 100, 1 ); 		
		
		$wc_ast_status_delivered = get_option( 'wc_ast_status_delivered', 0);
		if ( true == $wc_ast_status_delivered ) {
			//register order status 
			add_action( 'init', array( $this, 'register_order_status') );
			//add status after completed
			add_filter( 'wc_order_statuses', array( $this, 'add_delivered_to_order_statuses') );
			//Custom Statuses in admin reports
			add_filter( 'woocommerce_reports_order_statuses', array( $this, 'include_custom_order_status_to_reports'), 20, 1 );
			// for automate woo to check order is paid
			add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'delivered_woocommerce_order_is_paid_statuses' ) );
			//add bulk action
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions'), 50, 1 );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions'), 50, 1 );
			//add reorder button
			add_filter( 'woocommerce_valid_order_statuses_for_order_again', array( $this, 'add_reorder_button_delivered'), 50, 1 );
			//add button in preview
			add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'additional_admin_order_preview_buttons_actions'), 5, 2 );
			//add actions in column
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_delivered_order_status_actions_button'), 100, 2 );
		}
		
		//new order status
		$updated_tracking_status = get_option( 'wc_ast_status_updated_tracking', 0 );
		if ( true == $updated_tracking_status ) {			
			//register order status 
			add_action( 'init', array( $this, 'register_updated_tracking_order_status' ) );
			//add status after completed
			add_filter( 'wc_order_statuses', array( $this, 'add_updated_tracking_to_order_statuses' ) );
			//Custom Statuses in admin reports
			add_filter( 'woocommerce_reports_order_statuses', array( $this, 'include_updated_tracking_order_status_to_reports' ), 20, 1 );
			// for automate woo to check order is paid
			add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'updated_tracking_woocommerce_order_is_paid_statuses' ) );
			add_filter('woocommerce_order_is_download_permitted', array( $this, 'add_updated_tracking_to_download_permission' ), 10, 2);
			//add bulk action
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_updated_tracking' ), 50, 1 );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions_updated_tracking'), 50, 1 );
			//add reorder button
			add_filter( 'woocommerce_valid_order_statuses_for_order_again', array( $this, 'add_reorder_button_updated_tracking' ), 50, 1 );
			add_filter( 'wcast_order_status_email_type', array( $this, 'wcast_order_status_email_type' ), 50, 1 );
		}
		
		//new order status
		$partial_shipped_status = get_option( 'wc_ast_status_partial_shipped', 1 );
		if ( true == $partial_shipped_status ) {
			//register order status 
			add_action( 'init', array( $this, 'register_partial_shipped_order_status' ) );
			//add status after completed
			add_filter( 'wc_order_statuses', array( $this, 'add_partial_shipped_to_order_statuses' ) );
			//Custom Statuses in admin reports
			add_filter( 'woocommerce_reports_order_statuses', array( $this, 'include_partial_shipped_order_status_to_reports' ), 20, 1 );
			// for automate woo to check order is paid
			add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'partial_shipped_woocommerce_order_is_paid_statuses' ) );
			add_filter('woocommerce_order_is_download_permitted', array( $this, 'add_partial_shipped_to_download_permission' ), 10, 2);
			//add bulk action
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_partial_shipped' ), 50, 1 );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions_partial_shipped'), 50, 1 );
			//add reorder button
			add_filter( 'woocommerce_valid_order_statuses_for_order_again', array( $this, 'add_reorder_button_partial_shipped' ), 50, 1 );
		}

		//new order status
		$rename_shipped_status = get_option( 'wc_ast_status_shipped', 1 );
		$custom_shipped_status = get_option( 'wc_ast_status_new_shipped', 0 );
		
		if ( true == $custom_shipped_status && false == $rename_shipped_status ) {
			//register order status 
			add_action( 'init', array( $this, 'register_shipped_order_status') );
			//add status after completed
			add_filter( 'wc_order_statuses', array( $this, 'add_shipped_to_order_statuses') );
			//Custom Statuses in admin reports
			add_filter( 'woocommerce_reports_order_statuses', array( $this, 'include_shipped_order_status_to_reports'), 20, 1 );
			// for automate woo to check order is paid
			add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'shipped_woocommerce_order_is_paid_statuses' ) );
			add_filter( 'woocommerce_order_is_download_permitted', array( $this, 'add_shipped_to_download_permission' ), 10, 2 );
			//add bulk action
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_shipped'), 50, 1 );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions_shipped'), 50, 1 );
			//add reorder button
			add_filter( 'woocommerce_valid_order_statuses_for_order_again', array( $this, 'add_reorder_button_shipped'), 50, 1 );
			//add actions in column
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_shipped_order_status_actions_button'), 100, 2 );
		}						
		
		// Ajax hook for open inline tracking form
		add_action( 'wp_ajax_ast_open_inline_tracking_form', array( $this, 'ast_open_inline_tracking_form_fun' ) );		
	
		add_action( 'wp_ajax_update_email_preview_order', array( $this, 'update_email_preview_order_fun') );	
		
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_tracking_actions_button'), 100, 2 );	
		add_filter( 'woocommerce_email_title', array( $this, 'change_completed_woocommerce_email_title'), 10, 2 );	

		// add bulk order filter for exported / non-exported orders
		add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_shipping_provider'), 20 );	
		add_action( 'woocommerce_order_list_table_restrict_manage_orders', array( $this, 'filter_listtable_orders_by_shipping_provider'), 10, 2 );	
		
		add_filter( 'request', array( $this, 'filter_orders_by_shipping_provider_query' ) );
		add_filter( 'woocommerce_shop_order_list_table_prepare_items_query_args', array( $this, 'filter_listtable_orders_by_shipping_provider_query' ) );
		
		
		// add bulk order tracking number filter for exported / non-exported orders			
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'filter_orders_by_tracking_number_query' ) );
		
		add_filter( 'cron_schedules', array( $this, 'add_cron_interval') );

		add_action( 'update_order_status_after_adding_tracking', array( $this, 'update_order_status_after_adding_tracking'), 10, 2 );
		
		$wc_ast_enable_auto_detection = get_option( 'wc_ast_enable_auto_detection', 0 );
		if ( 1 == $wc_ast_enable_auto_detection ) {
			add_action( 'ast_tracking_form_end_meta_box', array( $this, 'autodetector_include_js_meta_box' ), 1, 1 );
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'autodetector_include_js_orders_page'), 100, 2 );		
		}

		$wc_ast_status_partial_shipped = get_option( 'wc_ast_status_partial_shipped', 1 );		
		if ( $wc_ast_status_partial_shipped ) {
			add_action( 'woocommerce_order_status_partial-shipped', array( $this, 'email_trigger_partial_shipped' ), 10, 2 );			
		}
	
		$wc_ast_status_updated_tracking = get_option( 'wc_ast_status_updated_tracking' );		
		if ( $wc_ast_status_updated_tracking ) {
			add_action( 'woocommerce_order_status_updated-tracking', array( $this, 'email_trigger_updated_tracking' ), 10, 2 );	
		}	
		
		$wc_ast_status_shipped = get_option( 'wc_ast_status_new_shipped' );
		if ( 1 == $wc_ast_status_shipped ) {
			add_action( 'woocommerce_order_status_shipped', array( $this, 'email_trigger_shipped' ), 10, 2 );
		}

		add_filter( 'mark_order_as_fields_data', array( $this, 'mark_order_as_fields_data' ) );

		add_filter( 'ast_formated_order_id', array( $this, 'ast_get_formated_order_id' ) );
		
		add_action( 'admin_footer', array( $this, 'footer_function'), 1 );
	}		
	
	/** 
	* Register new status : Delivered
	**/
	public function register_order_status() {						
		register_post_status( 'wc-delivered', array(
			'label'                     => __( 'Delivered', 'ast-pro' ),
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			/* translators: %s: search number of order */
			'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', 'ast-pro' )
		) );
	}
	
	/*
	* add status after completed
	*/
	public function add_delivered_to_order_statuses( $order_statuses ) {							
		$new_order_statuses = array();
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-completed' === $key ) {
				$new_order_statuses['wc-delivered'] = __( 'Delivered', 'ast-pro' );				
			}
		}
		
		return $new_order_statuses;
	}
	
	/*
	* Adding the custom order status to the default woocommerce order statuses
	*/
	public function include_custom_order_status_to_reports( $statuses ) {
		if ( $statuses ) {
			$statuses[] = 'delivered';
		}
		return $statuses;
	}
	
	/*
	* mark status as a paid.
	*/
	public function delivered_woocommerce_order_is_paid_statuses( $statuses ) { 
		$statuses[] = 'delivered';
		return $statuses; 
	}
	
	/*
	* add bulk action
	* Change order status to delivered
	*/
	public function add_bulk_actions( $bulk_actions ) {
		$lable = wc_get_order_status_name( 'delivered' );
		/* translators: %s: search order status label */
		$bulk_actions['mark_delivered'] = sprintf( __( 'Change status to %s', 'ast-pro' ), $lable );
		return $bulk_actions;		
	}
	
	/*
	* add order again button for delivered order status	
	*/
	public function add_reorder_button_delivered( $statuses ) {
		$statuses[] = 'delivered';
		return $statuses;	
	}

	/*
	* Add delivered action button in preview order list to change order status from completed to delivered
	*/
	public function additional_admin_order_preview_buttons_actions( $actions, $order ) {
		
		$wc_ast_status_delivered = get_option( 'wc_ast_status_delivered' );
		if ( $wc_ast_status_delivered ) {
			// Below set your custom order statuses (key / label / allowed statuses) that needs a button
			$custom_statuses = array(
				'delivered' => array( // The key (slug without "wc-")
					'label'     => __( 'Delivered', 'ast-pro' ), // Label name
					'allowed'   => array( 'completed'), // Button displayed for this statuses (slugs without "wc-")
				),
			);
		
			// Loop through your custom orders Statuses
			foreach ( $custom_statuses as $status_slug => $values ) {
				if ( $order->has_status( $values['allowed'] ) ) {
					$actions[ 'status' ][ 'group' ] = __( 'Change status: ', 'woocommerce' );
					$actions[ 'status' ][ 'actions' ][ $status_slug ] = array(
						'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $status_slug . '&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
						'name'   => $values['label'],
						'title'  => __( 'Change order status to', 'ast-pro' ) . ' ' . strtolower( $values['label'] ),
						'action' => $status_slug,
					);
				}
			}
		}		
		return $actions;
	}
	
	/*
	* Add action button in order list to change order status from completed to delivered
	*/
	public function add_delivered_order_status_actions_button( $actions, $order ) {
		
		$wc_ast_status_delivered = get_option( 'wc_ast_status_delivered' );
		
		if ( $wc_ast_status_delivered ) {
			if ( $order->has_status( array( 'completed' ) ) || $order->has_status( array( 'shipped' ) ) ) {
				
				// Get Order ID (compatibility all WC versions)
				$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
				
				// Set the action button
				$actions['delivered'] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=delivered&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
					'name'      => __( 'Mark order as delivered', 'ast-pro' ),
					'icon' => '<i class="fa fa-truck">&nbsp;</i>',
					'action'    => 'delivered_icon', // keep "view" class for a clean button CSS
				);
			}	
		}
		
		return $actions;
	}

	/*
	* Add action button in order list to change order status from completed to delivered
	*/
	public function add_shipped_order_status_actions_button( $actions, $order ) {
		
		$wc_ast_status_new_shipped = get_option( 'wc_ast_status_new_shipped' );
		
		if ( $wc_ast_status_new_shipped ) {
			if ( $order->has_status( array( 'processing' ) ) ) {
				
				// Get Order ID (compatibility all WC versions)
				$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
				
				// Set the action button
				$actions['delivered'] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=shipped&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
					'name'      => __( 'Mark as Shipped', 'ast-pro' ),
					'icon' => '<i class="fa fa-truck">&nbsp;</i>',
					'action'    => 'shipped_icon', // keep "view" class for a clean button CSS
				);
			}	
		}
		
		return $actions;
	}	
	
	/** 
	 * Register new status : Updated Tracking
	**/
	public function register_updated_tracking_order_status() {
		register_post_status( 'wc-updated-tracking', array(
			'label'                     => __( 'Updated Tracking', 'ast-pro' ),
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			/* translators: %s: replace with order count */
			'label_count'               => _n_noop( 'Updated Tracking <span class="count">(%s)</span>', 'Updated Tracking <span class="count">(%s)</span>', 'ast-pro' )
		) );		
	}
	
	/** 
	 * Register new status : Partially Shipped
	**/
	public function register_partial_shipped_order_status() {
		register_post_status( 'wc-partial-shipped', array(
			'label'                     => __( 'Partially Shipped', 'ast-pro' ),
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			/* translators: %s: replace with order count */	
			'label_count'               => _n_noop( 'Partially Shipped <span class="count">(%s)</span>', 'Partially Shipped <span class="count">(%s)</span>', 'ast-pro' )
		) );		
	}			
	
	/*
	* add status after completed
	*/
	public function add_updated_tracking_to_order_statuses( $order_statuses ) {		
		$new_order_statuses = array();
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-completed' === $key ) {
				$new_order_statuses['wc-updated-tracking'] = __( 'Updated Tracking', 'ast-pro' );				
			}
		}		
		return $new_order_statuses;
	}
	
	/*
	* add status after completed
	*/
	public function add_partial_shipped_to_order_statuses( $order_statuses ) {		
		$new_order_statuses = array();
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-completed' === $key ) {
				$new_order_statuses['wc-partial-shipped'] = __( 'Partially Shipped', 'ast-pro' );				
			}
		}		
		return $new_order_statuses;
	}	
	
	/*
	* Adding the updated-tracking order status to the default woocommerce order statuses
	*/
	public function include_updated_tracking_order_status_to_reports( $statuses ) {
		if ( $statuses ) {
			$statuses[] = 'updated-tracking';
		}	
		return $statuses;
	}

	/*
	* Adding the partial-shipped order status to the default woocommerce order statuses
	*/
	public function include_partial_shipped_order_status_to_reports( $statuses ) {
		if ( $statuses ) {
			$statuses[] = 'partial-shipped';
		}	
		return $statuses;
	}	
	
	/*
	* mark status as a paid.
	*/
	public function updated_tracking_woocommerce_order_is_paid_statuses( $statuses ) { 
		$statuses[] = 'updated-tracking';		
		return $statuses; 
	}
	
	/*
	* Give download permission to updated tracking order status
	*/
	public function add_updated_tracking_to_download_permission( $data, $order ) {
		if ( $order->has_status( 'updated-tracking' ) ) { 
			return true; 
		}
		return $data;
	}

	/*
	* mark status as a paid.
	*/
	public function partial_shipped_woocommerce_order_is_paid_statuses( $statuses ) { 
		$statuses[] = 'partial-shipped';		
		return $statuses; 
	}

	/*
	* Give download permission to Partially Shipped order status
	*/
	public function add_partial_shipped_to_download_permission( $data, $order ) {
		if ( $order->has_status( 'partial-shipped' ) ) { 
			return true; 
		}
		return $data;
	}		
	
	/*
	* add bulk action
	* Change order status to Updated Tracking
	*/
	public function add_bulk_actions_updated_tracking( $bulk_actions ) {
		$lable = wc_get_order_status_name( 'updated-tracking' );
		/* translators: %s: search order status label */
		$bulk_actions['mark_updated-tracking'] = sprintf( __( 'Change status to %s', 'ast-pro' ), $lable );
		return $bulk_actions;		
	}

	/*
	* add bulk action
	* Change order status to Partially Shipped
	*/
	public function add_bulk_actions_partial_shipped( $bulk_actions ) {
		$lable = wc_get_order_status_name( 'partial-shipped' );
		/* translators: %s: search order status label */
		$bulk_actions['mark_partial-shipped'] = sprintf( __( 'Change status to %s', 'ast-pro' ), $lable );
		return $bulk_actions;		
	}

	/*
	* add order again button for Partially Shipped order status	
	*/
	public function add_reorder_button_partial_shipped( $statuses ) {
		$statuses[] = 'partial-shipped';
		return $statuses;	
	}

	/*
	* add order again button for updated tracking order status	
	*/
	public function add_reorder_button_updated_tracking( $statuses ) {
		$statuses[] = 'updated-tracking';
		return $statuses;	
	}
	
	/*
	* add Updated Tracking in order status email customizer
	*/
	public function wcast_order_status_email_type( $order_status ) {
		$updated_tracking_status = array(
			'updated_tracking' => __( 'Updated Tracking', 'ast-pro' ),
		);
		$order_status = array_merge( $order_status, $updated_tracking_status );
		return $order_status;
	}

	/** 
	 * Register new status : Shipped
	**/
	public function register_shipped_order_status() {
		register_post_status( 'wc-shipped', array(
			'label'                     => __( 'Shipped', 'ast-pro' ),
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			/* translators: %s: replace with order count */
			'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'ast-pro' )
		) );		
	}
	
	/*
	* add status after shipped
	*/
	public function add_shipped_to_order_statuses( $order_statuses ) {		
		$new_order_statuses = array();
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-completed' === $key ) {
				$new_order_statuses['wc-shipped'] = __( 'Shipped', 'ast-pro' );				
			}
		}		
		return $new_order_statuses;
	}

	/*
	* Adding the shipped order status to the default woocommerce order statuses
	*/
	public function include_shipped_order_status_to_reports( $statuses ) {
		if ( $statuses ) {
			$statuses[] = 'shipped';
		}	
		return $statuses;
	}		
	
	/*
	* mark status as a paid.
	*/
	public function shipped_woocommerce_order_is_paid_statuses( $statuses ) { 
		$statuses[] = 'shipped';		
		return $statuses; 
	}

	/*
	* Give download permission to shipped order status
	*/
	public function add_shipped_to_download_permission( $data, $order ) {
		if ( $order->has_status( 'shipped' ) ) { 
			return true; 
		}
		return $data;
	}	
	
	/*
	* add bulk action
	* Change order status to Partially Shipped
	*/
	public function add_bulk_actions_shipped( $bulk_actions ) {
		$lable = wc_get_order_status_name( 'shipped' );
		/* translators: %s: search order status label */
		$bulk_actions['mark_shipped'] = sprintf( __( 'Change status to %s', 'ast-pro' ), $lable );
		return $bulk_actions;		
	}	
	
	/*
	* add order again button for shipped order status	
	*/
	public function add_reorder_button_shipped( $statuses ) {
		$statuses[] = 'shipped';
		return $statuses;	
	}	
	
	/*
	* Rename WooCommerce Order Status
	*/
	public function wc_renaming_order_status( $order_statuses ) {
		
		$enable = get_option( 'wc_ast_status_shipped', 1 );
		if ( false == $enable ) {
			return $order_statuses;
		}	
		
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-completed' === $key ) {
				$order_statuses['wc-completed'] = esc_html__( 'Shipped', 'ast-pro' );
			}
		}		
		return $order_statuses;
	}			
	
	/*
	* define the woocommerce_register_shop_order_post_statuses callback 
	* rename filter 
	* rename from completed to shipped
	*/
	public function filter_woocommerce_register_shop_order_post_statuses( $array ) {
		
		$enable = get_option( 'wc_ast_status_shipped', 1 );
		if ( false == $enable ) {
			return $array;
		}	
		
		if ( isset( $array[ 'wc-completed' ] ) ) {
			/* translators: %s: replace with order count */
			$array[ 'wc-completed' ]['label_count'] = _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'ast-pro' );
		}
		return $array; 
	}
	
	/*
	* rename bulk action
	*/
	public function modify_bulk_actions( $bulk_actions ) {		
		$enable = get_option( 'wc_ast_status_shipped', 1 );
		if ( false == $enable ) {
			return $bulk_actions;
		}	
		
		if ( isset( $bulk_actions['mark_completed'] ) ) {
			$bulk_actions['mark_completed'] = __( 'Change status to shipped', 'ast-pro' );
		}
		return $bulk_actions;
	}		
	
	public function ast_open_inline_tracking_form_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'ast-order-list', 'security' );
		
		$order_id = ( isset($_POST['order_id']) ) ? wc_clean( $_POST['order_id'] ) : '';
		$order = wc_get_order( $order_id );
		$order_number = $order->get_order_number();
		
		global $wpdb;
		$WC_Countries = new WC_Countries();
		$countries = $WC_Countries->get_countries();
		$shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s', $this->table ) );
		$shippment_countries = $wpdb->get_results( $wpdb->prepare( 'SELECT shipping_country FROM %1s WHERE display_in_order = 1 GROUP BY shipping_country', $this->table ) );
		
		$default_provider = get_option( 'wc_ast_default_provider' );

		foreach ( $shippment_providers as $provider ) {
			$provider_array[ $provider->ts_slug ] = urlencode( $provider->provider_url );
		}
				
		ob_start();
		?>
		<div id="" class="trackingpopup_wrapper add_tracking_popup" style="display:none;">
			<div class="trackingpopup_row">
				<div class="popup_header">
					<h3 class="popup_title"><?php esc_html_e( 'Add Tracking - order	', 'ast-pro'); ?> - #<?php esc_html_e( $order_number ); ?></h3>					
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body">
					<form id="add_tracking_number_form" method="POST" class="add_tracking_number_form">	
						<?php do_action( 'ast_tracking_form_between_form', $order_id, 'inline' ); ?>
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
						<?php ast_pro()->ast_pro_actions->mark_order_as_fields_html(); ?>						
						<hr>
						<p>		
							<?php wp_nonce_field( 'wc_ast_inline_tracking_form', 'wc_ast_inline_tracking_form_nonce' ); ?>
							<input type="hidden" name="action" value="add_inline_tracking_number">
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

			jQuery( '#add_tracking_number_form input#tracking_number, #add_tracking_number_form #tracking_provider' ).change( function() {

				var tracking  = jQuery( '#add_tracking_number_form input#tracking_number' ).val();
				var provider  = jQuery( '#add_tracking_number_form #tracking_provider' ).val();
				var providers = jQuery.parseJSON( '<?php echo json_encode( $provider_array ); ?>' );				
				//console.log(providers);
				//console.log(provider);	
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
	
	/**
	* Update Partially Shipped order email enable/disable in customizer
	*/
	public function save_partial_shipped_email( $data ) {
		
		check_admin_referer( 'woocommerce-settings' );
		$enabled = ( isset( $_POST['woocommerce_customer_partial_shipped_order_enabled'] ) ? wc_clean( $_POST['woocommerce_customer_partial_shipped_order_enabled'] ) : '' );
		update_option( 'customizer_partial_shipped_order_settings_enabled', $enabled );
	}

	/*
	* update preview order id in customizer
	*/
	public function update_email_preview_order_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'ast_customizer', 'security' );
		
		$wcast_preview_order_id = isset( $_POST['wcast_preview_order_id'] ) ? wc_clean( $_POST['wcast_preview_order_id'] ) : '';
		
		set_theme_mod( 'wcast_completed_email_preview_order_id', $wcast_preview_order_id );
		set_theme_mod( 'wcast_shipped_email_preview_order_id', $wcast_preview_order_id );
		set_theme_mod( 'wcast_email_preview_order_id', $wcast_preview_order_id );
		set_theme_mod( 'wcast_preview_order_id', $wcast_preview_order_id );		
		exit;
	}			

	/*
	* Add action button in order list to change order status from completed to delivered
	*/
	public function add_tracking_actions_button( $actions, $order ) {
		
		wp_enqueue_style( 'ast_styles', ast_pro()->plugin_dir_url() . 'assets/css/admin.css', array(), ast_pro()->version );	
		wp_enqueue_script( 'woocommerce-advanced-shipment-tracking-js', ast_pro()->plugin_dir_url() . 'assets/js/admin.js', array( 'jquery' ), ast_pro()->version);
		wp_localize_script(
			'woocommerce-advanced-shipment-tracking-js',
			'ast_orders_params',
			array(
				'order_nonce' => wp_create_nonce( 'ast-order-list' ),
			)
		);		
		
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
					'name'      => __( 'Add Tracking', 'ast-pro' ),
					'icon' => '<i class="fa fa-map-marker">&nbsp;</i>',
					'action'    => 'add_inline_tracking', // keep "view" class for a clean button CSS
				);		
			}
		}
		
		$wc_ast_status_shipped = get_option( 'wc_ast_status_shipped', 1 );
		if ( $wc_ast_status_shipped ) {
			$actions['complete']['name'] = __( 'Mark as Shipped', 'ast-pro' );
			$actions['complete']['url'] = wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' );
			$actions['complete']['action'] = 'complete';				
		}
		
		return $actions;
	}

	/*
	* Change completed order email title to Shipped Order
	*/
	public function change_completed_woocommerce_email_title( $email_title, $email ) {
		
		$wc_ast_status_shipped = get_option( 'wc_ast_status_shipped', 1 );		
		
		// Only on backend Woocommerce Settings "Emails" tab
		if ( 1 == $wc_ast_status_shipped ) {
			if ( isset( $_GET['page'] ) && 'wc-settings' == $_GET['page'] && isset( $_GET['tab'] )  && 'email' == $_GET['tab'] ) {
				switch ( $email->id ) {
					case 'customer_completed_order':
						$email_title = __( 'Shipped Order', 'ast-pro' );
						break;
				}
			}
		}
		return $email_title;
	}

	/**
	 * Add bulk filter for Shipping provider in orders list
	 *
	 * @since 2.4
	 */
	public function filter_orders_by_shipping_provider() {
		global $typenow, $wpdb;		
		$default_shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC', $this->table ) );		
		if ( 'shop_order' === $typenow ) {
			?>
			<select name="_shop_order_shipping_provider" id="dropdown_shop_order_shipping_provider">
				<option value=""><?php esc_html_e( 'Filter by shipping provider', 'ast-pro' ); ?></option>
				<?php foreach ( $default_shippment_providers as $provider ) : ?>
					<option value="<?php echo esc_attr( $provider->ts_slug ); ?>" <?php echo esc_attr( isset( $_GET['_shop_order_shipping_provider'] ) ? selected( $provider->ts_slug, wc_clean( $_GET['_shop_order_shipping_provider'] ), false ) : '' ); ?>>
						<?php printf( '%1$s', esc_html( $provider->provider_name ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		<?php
		}
	}
	
	public function filter_listtable_orders_by_shipping_provider( $order_type, $which ) {
		global $wpdb;		
		$default_shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC', $this->table ) );		
		if ( 'shop_order' === $order_type ) {
			?>
		<select name="_shop_order_shipping_provider" id="dropdown_shop_order_shipping_provider">
			<option value=""><?php esc_html_e( 'Filter by shipping provider', 'ast-pro' ); ?></option>
			<?php foreach ( $default_shippment_providers as $provider ) : ?>
				<option value="<?php echo esc_attr( $provider->ts_slug ); ?>" <?php echo esc_attr( isset( $_GET['_shop_order_shipping_provider'] ) ? selected( $provider->ts_slug, wc_clean( $_GET['_shop_order_shipping_provider'] ), false ) : '' ); ?>>
					<?php printf( '%1$s', esc_html( $provider->provider_name ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
		}
	}
	
	/**
	 * Process bulk filter action for shipment status orders
	 *
	 * @since 3.0.0
	 * @param array $vars query vars without filtering
	 * @return array $vars query vars with (maybe) filtering
	 */
	public function filter_orders_by_shipping_provider_query( $vars ) {
		global $typenow;		
		if ( 'shop_order' === $typenow && isset( $_GET['_shop_order_shipping_provider'] ) && '' != $_GET['_shop_order_shipping_provider'] ) {
			$vars['meta_query'][] = array(
				'key'       => '_wc_shipment_tracking_items',
				'value'     => wc_clean( $_GET['_shop_order_shipping_provider'] ),
				'compare'   => 'LIKE'
			);						
		}

		return $vars;
	}

	public function filter_listtable_orders_by_shipping_provider_query( $args ) {
		if ( isset( $_GET['_shop_order_shipping_provider'] ) && '' != $_GET['_shop_order_shipping_provider'] ) {
			$args['meta_query'][] = array(
				'key'       => '_wc_shipment_tracking_items',
				'value'     => wc_clean( $_GET['_shop_order_shipping_provider'] ),
				'compare'   => 'LIKE'
			);						
		}
		return $args;
	}
	

	/**
	 * Process bulk filter action for shipment status orders
	 *
	 * @since 2.7.4
	 * @param array $vars query vars without filtering
	 * @return array $vars query vars with (maybe) filtering
	 */
	public function filter_orders_by_tracking_number_query( $search_fields ) {
		$search_fields[] = '_wc_shipment_tracking_items';
		return $search_fields;
	}	
	
	/*
	* add_cron_interval
	*/
	public function add_cron_interval( $schedules ) {
		
		$schedules['wc_ast_1hr'] = array(
			'interval' => 60*60,//1 hour
			'display'  => esc_html__( 'Every one hour' ),
		);
		
		$schedules['wc_ast_6hr'] = array(
			'interval' => 60*60*6,//6 hour
			'display'  => esc_html__( 'Every six hour' ),
		);
		
		$schedules['wc_ast_12hr'] = array(
			'interval' => 60*60*12,//6 hour
			'display'  => esc_html__( 'Every twelve hour' ),
		);
		
		$schedules['wc_ast_1day'] = array(
			'interval' => 60*60*24*1,//1 days
			'display'  => esc_html__( 'Every one day' ),
		);
		
		$schedules['wc_ast_2day'] = array(
			'interval' => 60*60*24*2,//2 days
			'display'  => esc_html__( 'Every two day' ),
		);
		
		$schedules['wc_ast_7day'] = array(
			'interval' => 60*60*24*7,//7 days
			'display'  => esc_html__( 'Every Seven day' ),
		);
		
		//every 5 sec for batch proccessing
		$schedules['wc_ast_2min'] = array(
			'interval' => 2*60,//1 hour
			'display'  => esc_html__( 'Every two min' ),
		);
		
		return $schedules;
	}	
	
	/*
	* Updated order status to Shipped(Completed), Partially Shipped, Updated Tracking
	*/
	public function update_order_status_after_adding_tracking( $status_shipped, $order ) {
		
		$order_id = $order->get_id();				
		$order_statuses = wc_get_order_statuses();

		if ( '' == $status_shipped ) {
			if ( 'completed' == $order->get_status() || 'shipped' == $order->get_status() ) {								
				do_action( 'send_order_to_trackship', $order_id );
				do_action( 'export_order_to_paypal', $order_id );	
			}
		}
		
		if ( 1 == $status_shipped ) {			
		
			$wc_ast_status_new_shipped = get_option( 'wc_ast_status_new_shipped' );			
			
			if ( $wc_ast_status_new_shipped && array_key_exists( 'wc-shipped', $order_statuses ) ) {			
				
				if ( 'shipped' == $order->get_status() ) {
					WC()->mailer()->emails['WC_Email_Customer_Shipped_Order']->trigger( $order_id, $order );	
					do_action( 'send_order_to_trackship', $order_id );
					do_action( 'export_order_to_paypal', $order_id );	
				} else {
					$order->update_status( 'shipped' );	
				}				
							
			} else {
				if ( 'completed' == $order->get_status() ) {
					WC()->mailer()->emails['WC_Email_Customer_Completed_Order']->trigger( $order_id, $order );								
					do_action( 'send_order_to_trackship', $order_id );	
					do_action( 'export_order_to_paypal', $order_id );
				} else {
					$order->update_status( 'completed' );
				}
			}						
		}
		
		if ( 2 == $status_shipped ) {

			$wc_ast_status_partial_shipped = get_option( 'wc_ast_status_partial_shipped', 1 );
			
			if ( $wc_ast_status_partial_shipped ) {			
				
				$previous_order_status = $order->get_status();
				
				if ( 'partial-shipped' == $previous_order_status ) {								
					WC()->mailer()->emails['WC_Email_Customer_Partial_Shipped_Order']->trigger( $order_id, $order );	
					do_action( 'send_order_to_trackship', $order_id );	
					do_action( 'export_order_to_paypal', $order_id );
				} else {
					$order->update_status('partial-shipped');
				}												
			}
		}
		
		if ( 3 == $status_shipped ) {
			
			$wc_ast_status_updated_tracking = get_option( 'wc_ast_status_updated_tracking' );
			
			if ( $wc_ast_status_updated_tracking ) {			
				
				$previous_order_status = $order->get_status();
				
				if ( 'updated-tracking' == $previous_order_status ) {								
					WC()->mailer()->emails['WC_Email_Customer_Updated_Tracking_Order']->trigger( $order_id, $order );					
				} else {
					$order->update_status( 'updated-tracking' );	
				}											
			}
		}	
	}	

	/**	 
	 * Function for include css and js
	 */
	public function autodetector_include_js_meta_box() {
		global $wpdb;
		$shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE display_in_order = 1', $this->table ) );
		foreach ( $shippment_providers as $provider ) {
			$provider_array[ $provider->ts_slug ] = urlencode( $provider->provider_url );
		}
		
		wp_enqueue_script( 'ast_pro_autodetector', ast_pro()->plugin_dir_url() . 'assets/js/autodetector.js' , array( 'jquery', ), ast_pro()->version, true );
		wp_localize_script(
			'ast_pro_autodetector',
			'autodetector_orders_params',
			array(
				'provider_array' => $provider_array,
			)
		);
	}
	
	/**	 
	 * Function for include js on orders page
	 */
	public function autodetector_include_js_orders_page( $actions, $order ) {
		global $wpdb;
		$shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE display_in_order = 1', $this->table ) );
		foreach ( $shippment_providers as $provider ) {
			$provider_array[ $provider->ts_slug ] = urlencode( $provider->provider_url );
		}
		wp_enqueue_script( 'ast_pro_autodetector_orders', ast_pro()->plugin_dir_url() . 'assets/js/autodetector.js' , array( 'jquery'), ast_pro()->version, true );	
		wp_localize_script(
			'ast_pro_autodetector_orders',
			'autodetector_orders_params',
			array(
				'provider_array' => $provider_array,
			)
		);		
		return $actions;
	}

	/**
	 * Send email when order status change to 'Partially Shipped'	 
	*/
	public function email_trigger_partial_shipped( $order_id, $order = false ) {		
		WC()->mailer()->emails['WC_Email_Customer_Partial_Shipped_Order']->trigger( $order_id, $order );
	}	
	
	/**
	 * Send email when order status change to 'Updated Tracking'	 
	*/
	public function email_trigger_updated_tracking( $order_id, $order = false ) {		
		WC()->mailer()->emails['WC_Email_Customer_Updated_Tracking_Order']->trigger( $order_id, $order );
	}
	
	/**
	 * Send email when order status change to "Shipped"
	 *
	*/
	public function email_trigger_shipped( $order_id, $order = false ) {
		WC()->mailer()->emails[ 'WC_Email_Customer_Shipped_Order' ]->trigger( $order_id, $order );
	}

	/*
	* Return order status array for mark order as fields
	*/
	public function mark_order_as_fields_data( $order_status_array ) {
		
		$wc_ast_status_new_shipped = get_option( 'wc_ast_status_new_shipped' );
		
		if ( !$wc_ast_status_new_shipped ) {
			return $order_status_array;
		}	
		
		$shipped_order_status = array(
			'change_order_to_custom_shipped' => array(					
				'name'		=> 'change_order_to_shipped',
				'class'		=> 'mark_shipped_checkbox',
				'label'		=> __( 'Shipped', 'ast-pro'),
				'checked'	=> true,
				'show'		=> ( 1 == $wc_ast_status_new_shipped ) ? true : false,
			),			
		);
		unset( $order_status_array['change_order_to_shipped'] );
		$status_array = array_merge( $shipped_order_status, $order_status_array );
		return $status_array;
	}

	/*
	* Return formatted order id from custom order number
	*/
	public function ast_get_formated_order_id( $order_id ) {
		
		// Compatibilty code with Custom Order Numbers for WooCommerce Pro by Tyche Softwares
		if ( class_exists( 'Alg_WC_Custom_Order_Numbers_Core' ) ) {
			$offset     = 0;
			$block_size = 512;
			while ( true ) {
				$args = array(
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
					'posts_per_page' => $block_size,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'offset'         => $offset,
					'fields'         => 'ids',
				);
				
				$loop = new WP_Query( $args );
				
				if ( ! $loop->have_posts() ) {
					break;
				}
				
				foreach ( $loop->posts as $new_order_id ) {
					$_order = wc_get_order( $new_order_id );
					$Alg_WC = new Alg_WC_Custom_Order_Numbers_Core();
					$_order_number = $Alg_WC->display_order_number( $new_order_id, $_order );
					if ( $_order_number === $order_id ) {
						$order_id = $new_order_id;
						//echo $order_id;exit;
						break;
					}
				}
				$offset += $block_size;					
			}
		}
		
		return $order_id;
	}

	/*
	* change style of order label
	*/	
	public function footer_function() {
		if ( !is_plugin_active( 'woocommerce-order-status-manager/woocommerce-order-status-manager.php' ) ) {
			
			$bg_color = get_option( 'wc_ast_status_shipped_label_color', '#03a9f4' );
			$color = get_option( 'wc_ast_status_shipped_label_font_color', '#fff' );
			
			$delivered_bg_color = get_option( 'wc_ast_status_label_color', '#59c889' );
			$delivered_color = get_option( 'wc_ast_status_label_font_color', '#fff' );						
			
			$ps_bg_color = get_option( 'wc_ast_status_partial_shipped_label_color', '#1e73be' );
			$ps_color = get_option( 'wc_ast_status_partial_shipped_label_font_color', '#fff' );
			
			$ut_bg_color = get_option( 'wc_ast_status_updated_tracking_label_color', '#23a2dd' );
			$ut_color = get_option( 'wc_ast_status_updated_tracking_label_font_color', '#fff' );
			?>
			<style>
			.order-status.status-shipped,.order-status-table .order-label.wc-shipped{
				background: <?php echo esc_html( $bg_color ); ?>;
				color: <?php echo esc_html( $color ); ?>;
			}	
			.order-status.status-delivered,.order-status-table .order-label.wc-delivered{
				background: <?php echo esc_html( $delivered_bg_color ); ?>;
				color: <?php echo esc_html( $delivered_color ); ?>;
			}					
			.order-status.status-partial-shipped,.order-status-table .order-label.wc-partially-shipped{
				background: <?php echo esc_html( $ps_bg_color ); ?>;
				color: <?php echo esc_html( $ps_color ); ?>;
			}
			.order-status.status-updated-tracking,.order-status-table .order-label.wc-updated-tracking{
				background: <?php echo esc_html( $ut_bg_color ); ?>;
				color: <?php echo esc_html( $ut_color ); ?>;
			}	
			</style>
			<?php
		}		
	}
}
