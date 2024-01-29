<?php
/** 
 * Plugin Name: Advanced Shipment Tracking Pro
 * Plugin URI: https://www.zorem.com/shop/tracking-per-item-ast-add-on/ 
 * Description: AST PRO fulfilment manager provides powerful features to easily add tracking info to WooCommerce orders, automate the fulfillment workflows and keep customers happy and informed.
 * Version: 1.6
 * Author: zorem
 * Author URI: https://zorem.com 
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 * Text Domain: ast-pro 
 * Domain Path: /lang/
 * WC tested up to: 5.9.0
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package zorem
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ast_Pro {
	
	/**
	 * Advanced Shipment Tracking Pro version.
	 *
	 * @var string
	 */
	public $version = '1.6';
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		
		// Add your templates to this array.
		if ( !defined('SHIPMENT_TRACKING_PATH') ) {
			define( 'SHIPMENT_TRACKING_PATH', $this->get_plugin_path() );
		}	

		add_action( 'admin_notices', array( $this, 'not_allow_admin_notice' ) );		
		
		$this->plugin_file = __FILE__;
		// Add your templates to this array.				
							
		if ( $this->is_wc_active() ) {
			
			$this->includes();
			
			// Init REST API.
			$this->init_rest_api();
			
			//start adding hooks
			$this->init();						
			
			$this->ast_tpi->init();
			$this->ast_pro_admin->init();	
			$this->ast_pro_csv_import->init();
			
			//plugin admin_notice class init
			$this->ast_pro_admin_notice->init();				
			
			//plugin install class init
			$this->ast_pro_install->init();
		}				
	}
	
	/**	 
	 * Not allow AST free plugin activate
	*/
	public function not_allow_admin_notice() {
		if ( isset( $_GET['ast-not-allow'] ) && 'true' == $_GET['ast-not-allow'] ) {
			?>
			<div class="error">
				<p><?php printf( esc_html__( "Please note, you can't activate the Advanced Shipment Tracking for WooCommerce version when the AST PRO is active.", 'ast-pro' ) ); ?></p>
			</div>
			<?php
		}
	}
	
	/**
	 * Check if WooCommerce is active
	 *
	 * @since  1.0.0
	 * @return bool
	*/
	private function is_wc_active() {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}		

		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}		
		return $is_active;
	}
	
	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		if ( isset( $this->plugin_path ) ) {
			return $this->plugin_path;
		}

		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

		return $this->plugin_path;
	}
	
	/*
	* return plugin directory URL
	*/
	public function plugin_dir_url() {
		return plugin_dir_url( __FILE__ );
	}
	
	/*
	* return shipment provider table
	*/
	public function shippment_provider_table() {
		global $wpdb;
		
		$table = $wpdb->prefix . 'woo_shippment_provider';
		
		if ( is_multisite() ) {
			
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			
			if ( is_plugin_active_for_network( 'ast-pro/ast-pro.php' ) ) {
				$main_blog_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );			
				$table = $main_blog_prefix . 'woo_shippment_provider';	
			} else {
				$table = $wpdb->prefix . 'woo_shippment_provider';
			}	
			
		} else {
			$table = $wpdb->prefix . 'woo_shippment_provider';	
		}
		
		return $table;
	}
	
	/**
	 * Init advanced shipment tracking REST API.	 
	*/
	private function init_rest_api() {
		add_action( 'rest_api_init', array( $this, 'rest_api_register_routes' ) );
	}
	
	/**
	 * Register shipment tracking routes.
	 *
	 * @since 1.5.0
	 */
	public function rest_api_register_routes() {
		
		if ( ! is_a( WC()->api, 'WC_API' ) ) {
			return;
		}
		
		if ( !class_exists( 'AST_Pro_Actions' ) ) {
			return;
		}
		
		require_once $this->get_plugin_path() . '/includes/api/class-wc-ast-pro-rest-api-controller.php';			
		
		// Register route with default namespace wc/v3.
		$ast_api_controller = new WC_AST_PRO_REST_API_Controller();
		$ast_api_controller->register_routes();				
		
		// These are all the same code but with different namespaces for compatibility reasons.
		$ast_api_controller_v1 = new WC_AST_PRO_REST_API_Controller();
		$ast_api_controller_v1->set_namespace( 'wc-ast/v3' );
		$ast_api_controller_v1->register_routes();
		
		$ast_api_controller_v1 = new WC_AST_PRO_REST_API_Controller();
		$ast_api_controller_v1->set_namespace( 'wc/v1' );
		$ast_api_controller_v1->register_routes();

		$ast_api_controller_v2 = new WC_AST_PRO_REST_API_Controller();
		$ast_api_controller_v2->set_namespace( 'wc/v2' );
		$ast_api_controller_v2->register_routes();
		
		$ast_api_controller_v3 = new WC_AST_PRO_REST_API_Controller();
		$ast_api_controller_v3->set_namespace( 'wc/v3' );
		$ast_api_controller_v3->register_routes();
		
		$shipment_api_controller_v3 = new WC_AST_PRO_REST_API_Controller();
		$shipment_api_controller_v3->set_namespace( 'wc-shipment-tracking/v3' );
		$shipment_api_controller_v3->register_routes();				
	}
	
	/**
	 * Include files
	*/
	public function includes() {
		
		require_once $this->get_plugin_path() . '/includes/class-ast-pro-install.php';
		$this->ast_pro_install = AST_PRO_Install::get_instance();
		
		require_once $this->get_plugin_path() . '/includes/class-ast-pro-admin-notice.php';
		$this->ast_pro_admin_notice = AST_PRO_Admin_notice::get_instance();
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-fulfillment-dashboard.php';	
		$this->ast_pro_fulfillment_dashboard = AST_PRO_Fulfillment_Dashboard::get_instance();
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-vendor-compatibility.php';	
		$this->ast_pro_vendor_compatibility = AST_PRO_Vendor_Compatibility::get_instance();
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-settings.php';	
		$this->ast_pro_settings = AST_PRO_Settings::get_instance();
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-admin.php';
		$this->ast_pro_admin = AST_pro_admin::get_instance();
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-csv-import.php';
		$this->ast_pro_csv_import = AST_Pro_Csv_Import::get_instance();
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-actions.php';
		$this->ast_pro_actions = AST_Pro_Actions::get_instance();				
		
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-tpi.php';
		$this->ast_tpi = AST_tpi::get_instance();

		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-paypal-tracking.php';
		$this->ast_paypal_tracking = AST_Pro_PayPal_Tracking::get_instance();
		
		//Logger
		require_once $this->get_plugin_path() . '/includes/class-ast-pro-logger.php';
		$this->logger = AST_Pro_Logger::get_instance();
		
		$enable_shipstation_default = ( is_plugin_active( 'woocommerce-shipstation-integration/woocommerce-shipstation.php' ) ) ? 1 : 0;
		$enable_shipstation = get_option( 'enable_shipstation_integration', $enable_shipstation_default );
		if ( $enable_shipstation ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-shipstation.php';
		}	

		$enable_wc_shipping_default = ( is_plugin_active( 'woocommerce-services/woocommerce-services.php' ) ) ? 1 : 0;
		$enable_wc_shipping = get_option( 'enable_wc_shipping_integration', $enable_wc_shipping_default );
		if ( $enable_wc_shipping ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-wc-services.php';
		}

		$enable_ups_shipping = get_option( 'enable_ups_shipping_label_pluginhive', 0 );
		if ( $enable_ups_shipping ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-ups-shipping-label.php';
		}
		
		$enable_quickbooks_commerce = get_option( 'enable_quickbooks_commerce_integration', 0 );
		if ( $enable_quickbooks_commerce ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-quickbooks-commerce.php';
		}		
		
		$enable_readytoship = get_option( 'enable_readytoship_integration', 0 );
		if ( $enable_readytoship ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-readytoship.php';
			$this->ast_pro_readytoship = AST_pro_readytoship::get_instance();
			$this->ast_pro_readytoship->init();	
		}
		
		$enable_royalmail = get_option( 'enable_royalmail_integration', 0 );
		if ( $enable_royalmail ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-royalmail.php';
		}	
		
		$enable_customcat = get_option( 'enable_customcat_integration', 0 );
		if ( $enable_customcat ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-customcat.php';
		}

		$enable_dear_systems = get_option( 'enable_dear_inventory_integration', 0 );
		if ( $enable_dear_systems ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-dear-inventory.php';
		}
		
		$enable_printify_integration = get_option( 'enable_printify_integration', 0 );
		if ( $enable_printify_integration ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-printify.php';
		}
		
		$enable_picqer_integration = get_option( 'enable_picqer_integration', 0 );
		if ( $enable_picqer_integration ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-picqer-integration.php';
		}
		
		$enable_3plwinner_integration = get_option( 'enable_3plwinner_integration', 0 );
		if ( $enable_3plwinner_integration ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-3plwinner-integration.php';
		}
		
		$enable_eiz_integration = get_option( 'enable_eiz_integration', 0 );
		if ( $enable_eiz_integration ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-eiz-integration.php';
		}
		
		$enable_shippypro_integration = get_option( 'enable_shippypro_integration', 0 );
		if ( $enable_shippypro_integration ) {			
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-shippypro-integration.php';
		}

		$enable_dianxiaomi_integration = get_option( 'enable_dianxiaomi_integration', 0 );
		if ( $enable_dianxiaomi_integration ) {			
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-dianxiaomi-integration.php';
			$this->ast_pro_dianxiaomi = AST_Pro_Dianxiaomi::get_instance();							
		}	
		
		$pdf_invoice_by_ewout_default = ( is_plugin_active( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' ) ) ? 1 : 0;
		$enable_pdf_invoice_by_ewout = get_option( 'enable_pdf_invoice_integration_ewout', $pdf_invoice_by_ewout_default );
		if ( $enable_pdf_invoice_by_ewout ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-pdf-invoice-by-ewout.php';
		}
		
		$pdf_invoice_by_bas_default = ( is_plugin_active( 'woocommerce-pdf-invoices/bootstrap.php' ) ) ? 1 : 0;
		$enable_pdf_invoice_by_bas = get_option( 'enable_pdf_invoice_integration_bas', $pdf_invoice_by_bas_default );
		if ( $enable_pdf_invoice_by_bas ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-pdf-invoice-by-bas.php';
		}			

		$ali2woo_default = ( is_plugin_active( 'ali2woo-lite/ali2woo-lite.php' ) ) ? 1 : 0;
		$enable_ali2woo = get_option( 'enable_ali2woo_integration', $ali2woo_default );
		if ( $enable_ali2woo ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/integration/class-ast-pro-ali2woo-integration.php';
		}					
		
		//license
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-license-manager.php';				
		$this->license = AST_Pro_License_Manager::get_instance();
		
		//update-manager	
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-pro-update-manager.php';
		new AST_Pro_Update_Manager(
			$this->version,
			'ast-pro/ast-pro.php',
			$this->license->get_item_code()
		);
		
		require_once $this->get_plugin_path() . '/includes/ast-pro-email-manager.php';
		
	}
	
	/**
	 * Display WC active notice
	 *	 
	 * @since  1.0.0
	*/
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p>
			<?php
			/* translators: %s: search WooCommerce plugin link */
			printf( esc_html__( 'Please install and activate %1$sWooCommerce%2$s for Advanced Shipment Tracking Pro!', 'ast-pro' ), '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) ) . '">', '</a>' ); 
			?>
			</p>
		</div>
		<?php
	}
	
	/*
	* init when class loaded
	*/
	public function init() {											
		
		//text domain hooks
		add_action( 'plugins_loaded', array( $this, 'ast_pro_load_textdomain'));
		register_activation_hook( __FILE__, array( $this->ast_pro_install, 'ast_pro_install' ) );
		
		//deactivate AST Addons on activate plugin
		register_activation_hook( __FILE__, array( $this,'deactivate_addons_on_plugin_activation' ) );		
		add_action( 'admin_notices', array( $this, 'ast_pro_admin_notice' ) );
		
		add_action( 'init', array( $this, 'update_database_check'));
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 100, 1 );		
		
		add_action( 'admin_footer', array( $this, 'uninstall_notice') );
		add_action( 'wp_ajax_ast_pro_reassign_order_status', array( $this, 'ast_pro_reassign_order_status' ) );		

		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ast_pro_plugin_action_links' ) );	

		$preview = ( isset( $_REQUEST['wcast-tracking-preview'] ) && '1' === $_REQUEST['wcast-tracking-preview'] ) || ( isset( $_REQUEST['wcast-partial-shipped-email-customizer-preview'] ) && '1' === $_REQUEST['wcast-partial-shipped-email-customizer-preview'] ) || ( isset( $_REQUEST['wcast-shipped-email-customizer-preview'] ) && '1' === $_REQUEST['wcast-shipped-email-customizer-preview'] ) ? true : false ;
				
		if ( !$preview ) {			
			$tracking_info_settings = get_option('tracking_info_settings');			
			if ( isset( $tracking_info_settings['display_tracking_info_at'] ) && 'after_order' == $tracking_info_settings['display_tracking_info_at'] ) {
				add_action( 'woocommerce_email_order_meta', array( $this->ast_pro_actions, 'email_display' ), 10, 4 );
			} else {
				add_action( 'woocommerce_email_before_order_table', array( $this->ast_pro_actions, 'email_display' ), 10, 4 );
			}	
		}			
	}
	
	/*** Method load Language file ***/
	public function ast_pro_load_textdomain() {
		load_plugin_textdomain( 'ast-pro', false, dirname( plugin_basename(__FILE__) ) . '/lang' );
	}

	/*
	* include file on plugin load
	*/
	public function on_plugins_loaded() {				
		
		require_once $this->get_plugin_path() . '/includes/tracking-info.php';
		
		require_once $this->get_plugin_path() . '/includes/customizer/class-ast-pro-customizer.php';
		
		require_once $this->get_plugin_path() . '/includes/customizer/class-ast-pro-completed-email-customizer.php';
		
		require_once $this->get_plugin_path() . '/includes/customizer/class-ast-pro-tracking-info-customizer.php';
		
		require_once $this->get_plugin_path() . '/includes/customizer/class-ast-pro-partial-shipped-email-customizer.php';						
																			
		require_once $this->get_plugin_path() . '/includes/customizer/class-ast-pro-shipped-email-customizer.php';
		
		$enable_leagace_add_tracking = get_option( 'wc_ast_enable_leagace_add_tracking', 0);
		if ( !$enable_leagace_add_tracking ) {
			$ast = AST_Pro_Actions::get_instance();	
			remove_action( 'ast_add_tracking_btn', array( $ast, 'ast_add_tracking_btn' ) );
			add_action( 'ast_add_tracking_btn', array( $this->ast_pro_admin, 'ast_add_tracking_btn' ) );
		}		
	}	
	
	/*
	* database update
	*/
	public function update_database_check() {				
		
		if ( is_admin() ) {			
			
			if ( version_compare( get_option( 'tracking_per_item_addon_db_version' ), '1.3.2', '<' ) ) {
				$license_key = get_option( 'ast_product_license_key', false );
				$status = get_option( 'ast_product_license_status', false );
				$instance_id = get_option( 'ast_per_product_instance_id', false );				
				$this->license->set_license_key( $license_key );
				$this->license->set_license_status( $status );
				$this->license->set_instance_id( $instance_id );
				update_option( 'tracking_per_item_addon_db_version', '1.3.2' );	
			}
			
			if ( isset( $_GET['page'] ) && 'woocommerce-advanced-shipment-tracking' == $_GET['page'] ) {
				$this->license->check_license_valid();
			}					
		}
	}
	
	/*
	* AST Addons deactivate on activation
	*/
	public function deactivate_addons_on_plugin_activation() {
		
		//Deactivate Tracking Per Item Add-on
		if ( is_plugin_active( 'ast-tracking-per-order-items/ast-tracking-per-order-items.php' ) ) {
			deactivate_plugins( 'ast-tracking-per-order-items/ast-tracking-per-order-items.php' );
			set_transient( 'ast_pro_tpi_deactivate', 'tpi_deactivate_notice' );
		}
		
		//Deactivate ShipStation Tracking Add-on for AST
		if ( is_plugin_active( 'ast-compatibility-with-wc-shipstation/ast-compatibility-with-wc-shipstation.php' ) ) {
			deactivate_plugins( 'ast-compatibility-with-wc-shipstation/ast-compatibility-with-wc-shipstation.php' );
			set_transient( 'ast_pro_shipstation_deactivate', 'ast_shipstation_deactivate_notice' );
		}
		
		//Deactivate WooCommerce Shipping Tracking Add-on for AST
		if ( is_plugin_active( 'ast-compatibility-with-wc-services/ast-compatibility-with-wc-services.php' ) ) {
			deactivate_plugins('ast-compatibility-with-wc-services/ast-compatibility-with-wc-services.php' );
			set_transient( 'ast_pro_wc_services_deactivate', 'ast_wc_services_deactivate_notice' );
		}
		
		//Deactivate ReadyToShip Tracking Add-on for AST
		if ( is_plugin_active( 'ast-compatibility-with-readytoship/ast-compatibility-with-readytoship.php' ) ) {
			deactivate_plugins('ast-compatibility-with-readytoship/ast-compatibility-with-readytoship.php' );
			set_transient( 'ast_pro_readytoship_deactivate', 'ast_readytoship_deactivate_notice' );
		}
	}

	/**
	 * AST Pro admin notice
	 *
	 * @since 1.0.0
	 */
	public function ast_pro_admin_notice() {
		
		//Display Tracking Per Item Add-on notice
		if ( 'tpi_deactivate_notice' == get_transient( 'ast_pro_tpi_deactivate' ) ) {
			?>
			<div id="message" class="updated notice is-dismissible">
				<p>We deactivated the Tracking Per Item Add-on when the AST PRO is installed, you can remove the Tracking Per Item Add-on from your store.</p>
			</div>
			<?php
			delete_transient( 'ast_pro_tpi_deactivate' );
		}
		
		//Display ShipStation Tracking Add-on for AST notice
		if ( 'ast_shipstation_deactivate_notice' == get_transient( 'ast_pro_shipstation_deactivate' ) ) {
			?>
			<div id="message" class="updated notice is-dismissible">
				<p>We deactivated the ShipStation Tracking Add-on for AST when the AST PRO is installed, you can remove the ShipStation Tracking Add-on for AST from your store.</p>
			</div>
			<?php
			delete_transient( 'ast_pro_shipstation_deactivate' );
		}
		
		//Display WooCommerce Shipping Tracking Add-on for AST notice
		if ( 'ast_wc_services_deactivate_notice' == get_transient( 'ast_pro_wc_services_deactivate' ) ) {
			?>
			<div id="message" class="updated notice is-dismissible">
				<p>We deactivated the WooCommerce Shipping Tracking Add-on for AST when the AST PRO is installed, you can remove the WooCommerce Shipping Tracking Add-on for AST from your store.</p>
			</div>
			<?php
			delete_transient( 'ast_pro_wc_services_deactivate' );
		}
		
		//Display ReadyToShip Tracking Add-on for AST notice
		if ( 'ast_readytoship_deactivate_notice' == get_transient( 'ast_pro_readytoship_deactivate' ) ) {
			?>
			<div id="message" class="updated notice is-dismissible">
				<p>We deactivated the ReadyToShip Tracking Add-on for AST when the AST PRO is installed, you can remove the ReadyToShip Tracking Add-on for AST from your store.</p>
			</div>
			<?php
			delete_transient( 'ast_pro_readytoship_deactivate' );
		}
	}

	/*
	* Plugin uninstall code 
	*/	
	public function uninstall_notice() {
		$screen = get_current_screen();
		
		if ( 'plugins.php' == $screen->parent_file ) {
			wp_enqueue_style( 'ast_styles', $this->plugin_dir_url() . 'assets/css/admin.css', array(), $this->version );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		}
		
		$shipped_count = wc_orders_count( 'shipped' );		
		$ps_count = wc_orders_count( 'partial-shipped' );
		$delivered_count = wc_orders_count( 'delivered' );		
		
		$order_statuses = wc_get_order_statuses(); 
				
		unset( $order_statuses['wc-shipped'] );		
		unset( $order_statuses['wc-partial-shipped'] );		
		unset( $order_statuses['wc-delivered'] );
		
		if ( $shipped_count > 0 || $ps_count > 0 || $delivered_count > 0 ) { 
			?>
		
		<script>
		
		jQuery(document).on("click","[data-slug='advanced-shipment-tracking-pro'] .deactivate a",function(e){			
			e.preventDefault();
			jQuery('.as_pro_uninstall_popup').show();
			var theHREF = jQuery(this).attr("href");
			jQuery(document).on("click",".ast_pro_uninstall_plugin",function(e){
				jQuery("body").block({
					message: null,
					overlayCSS: {
						background: "#fff",
						opacity: .6
					}	
				});	
				var form = jQuery('#ast_pro_order_reassign_form');
				jQuery.ajax({
					url: ajaxurl,		
					data: form.serialize(),		
					type: 'POST',		
					success: function(response) {
						jQuery("body").unblock();			
						window.location.href = theHREF;
					},
					error: function(response) {
						console.log(response);			
					}
				});				
			});			
		});
		
		jQuery(document).on("click",".popupclose",function(e){
			jQuery('.as_pro_uninstall_popup').hide();
		});
		
		jQuery(document).on("click",".ast_pro_uninstall_close",function(e){
			jQuery('.as_pro_uninstall_popup').hide();
		});

		jQuery(document).on("click",".popup_close_icon",function(e){
			jQuery('.as_pro_uninstall_popup').hide();
		});	
		</script>
		<div id="" class="popupwrapper as_pro_uninstall_popup" style="display:none;">
			<div class="popuprow">
				<div class="popup_header">
					<h3 class="popup_title">Advanced Shipment Tracking Pro</h3>					
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body">				
					<form method="post" id="ast_pro_order_reassign_form">					
						<?php 
						
						if ( $shipped_count > 0 ) { 
							?>
							<p>
							<?php 
								/* translators: %s: replace with Partially Shipped order count */
								printf( esc_html__('We detected %s orders that use the Shipped order status, You can reassign these orders to a different status', 'ast-pro'), esc_html__( $shipped_count ) ); 
							?>
							</p>	
							
							<select id="reassign_shipped_order" name="reassign_shipped_order" class="reassign_select">
								<option value=""><?php esc_html_e('Select', 'woocommerce'); ?></option>
								<?php foreach ( $order_statuses as $key => $status ) { ?>
									<option value="<?php esc_html_e( $key ); ?>"><?php esc_html_e( $status ); ?></option>
								<?php } ?>
							</select>
						
						<?php
						}
						
						if ( $ps_count > 0 ) { 
							?>
							<p>
							<?php 
								/* translators: %s: replace with Partially Shipped order count */
								printf( esc_html__('We detected %s orders that use the Partially Shipped order status, You can reassign these orders to a different status', 'ast-pro'), esc_html__( $ps_count ) ); 
							?>
							</p>	
							
							<select id="reassign_ps_order" name="reassign_ps_order" class="reassign_select">
								<option value=""><?php esc_html_e('Select', 'woocommerce'); ?></option>
								<?php foreach ( $order_statuses as $key => $status ) { ?>
									<option value="<?php esc_html_e( $key ); ?>"><?php esc_html_e( $status ); ?></option>
								<?php } ?>
							</select>
						
						<?php 
						} 
						if ( $delivered_count > 0 ) { 
							?>
							<p>
							<?php 
								/* translators: %s: replace with Partially Shipped order count */
								printf( esc_html__('We detected %s orders that use the Delivered order status, You can reassign these orders to a different status', 'ast-pro'), esc_html__( $delivered_count ) ); 
							?>
							</p>	
							
							<select id="reassign_delivered_order" name="reassign_delivered_order" class="reassign_select">
								<option value=""><?php esc_html_e('Select', 'woocommerce'); ?></option>
								<?php foreach ( $order_statuses as $key => $status ) { ?>
									<option value="<?php esc_html_e( $key ); ?>"><?php esc_html_e( $status ); ?></option>
								<?php } ?>
							</select>
						
						<?php } ?>
						<p>	
							<?php wp_nonce_field( 'ast_pro_reassign_order_status', 'ast_pro_reassign_order_status_nonce' ); ?>
							<input type="hidden" name="action" value="ast_pro_reassign_order_status">
							<input type="button" value="<?php esc_html_e( 'Deactivate' ); ?>" class="ast_pro_uninstall_plugin button-primary btn_ast2">
							<input type="button" value="<?php esc_html_e( 'Close', 'woocommerce' ); ?>" class="ast_pro_uninstall_close button-primary btn_red">				
						</p>
					</form>	
				</div>	
			</div>
			<div class="popupclose"></div>
		</div>
		<?php 
		}
	}
	
	/*
	* Functon for reassign order status on plugin deactivation
	*/
	public function ast_pro_reassign_order_status() {		
		
		check_ajax_referer( 'ast_pro_reassign_order_status', 'ast_pro_reassign_order_status_nonce' );
		
		$reassign_shipped_order = isset( $_POST['reassign_shipped_order'] ) ? wc_clean( $_POST['reassign_shipped_order'] ) : '';		
		$reassign_ps_order = isset(	$_POST['reassign_ps_order']	) ? wc_clean( $_POST['reassign_ps_order'] ) : '';
		$reassign_delivered_order = isset(	$_POST['reassign_delivered_order']	) ? wc_clean( $_POST['reassign_delivered_order'] ) : '';		
		
		if ( '' != $reassign_shipped_order ) {
			
			$args = array(
				'status' => 'shipped',
				'limit' => '-1',
			);
			
			$orders = wc_get_orders( $args );
			
			foreach ( $orders as $order ) {				
				$order_id = $order->get_id();
				$order = new WC_Order( $order_id );
				$order->update_status( $reassign_shipped_order );				
			}			
		}				
		
		if ( '' != $reassign_ps_order ) {
			
			$args = array(
				'status' => 'partial-shipped',
				'limit' => '-1',
			);
			
			$ps_orders = wc_get_orders( $args );
			
			foreach ( $ps_orders as $order ) {				
				$order_id = $order->get_id();
				$order = new WC_Order( $order_id );
				$order->update_status( $reassign_ps_order );				
			}			
		}

		if ( '' != $reassign_delivered_order ) {
			
			$args = array(
				'status' => 'delivered',
				'limit' => '-1',
			);
			
			$delivered_orders = wc_get_orders( $args );
			
			foreach ( $delivered_orders as $order ) {				
				$order_id = $order->get_id();
				$order = new WC_Order( $order_id );
				$order->update_status( $reassign_delivered_order );				
			}			
		}	
		echo 1;
		die();		
	}
	
	/**
	* Add plugin action links.
	*
	* Add a link to the settings page on the plugins.php page.
	*
	* @since 2.6.5
	*
	* @param  array  $links List of existing plugin action links.
	* @return array         List of modified plugin action links.
	*/
	public function ast_pro_plugin_action_links ( $links ) {
		return array_merge( array(
			'<a href="' . esc_url( admin_url( '/admin.php?page=woocommerce-advanced-shipment-tracking' ) ) . '">' . __( 'Settings' ) . '</a>'
		), $links );		
	}	
}

/**
 * Returns an instance of ast_pro.
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @return ast_pro
*/
function ast_pro() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new Ast_Pro();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
ast_pro();
