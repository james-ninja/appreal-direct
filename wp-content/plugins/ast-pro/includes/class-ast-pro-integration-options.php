<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_Integration {
	
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
	 * @return AST_Pro_Admin
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
	}

	/*
	* functions for add integrations options in AST settings
	*/
	public function integrations_settings_options() {
		$form_data = array(		
			'enable_ordoro_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Ordoro', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the Ordoro', 'ast-pro' ),
				'img'		=> 'ordoro-icon.png',
				'settings'	=> false,
				'default'   => 1, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/ordoro/',
			),
			'enable_cartrover_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'CartRover', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the CartRover', 'ast-pro' ),
				'img'		=> 'cart-rover-icon.png',
				'settings'	=> false,
				'default'   => 1, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/cartrover/',
			),
			'enable_parcelforce_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'ParcelForce', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the ParcelForce', 'ast-pro' ),
				'img'		=> 'parcelfoce-icon.png',
				'settings'	=> false,
				'default'   => 1, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/parcelforce/',
			),
			'enable_shipstation_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'ShipStation', 'ast-pro' ),				
				'desc'	    => __( 'Adding tracking information to your orders shipped with ShipStation and automate your workflow', 'ast-pro' ),
				'img'		=> 'shipstation-icon.png',
				'settings'	=> true,
				'settings_fields' => array(
										'autocomplete_shipstation' => array(
											'type'		=> 'checkbox',
											'default'   => 0,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),
											'show'		=> true,										
										),
									),	
				'default'  	=> ( is_plugin_active( 'woocommerce-shipstation-integration/woocommerce-shipstation.php' ) || is_plugin_active( 'dokan-pro/dokan-pro.php' ) ) ? 1 : 0,
				'disabled'  => ( is_plugin_active( 'woocommerce-shipstation-integration/woocommerce-shipstation.php' ) || is_plugin_active( 'dokan-pro/dokan-pro.php' ) ) ? false : true, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/shipstation/',
			),
			'enable_wc_shipping_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'WC Shipping', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with WooCommerce Shipping to the Shipment Tracking and automate your workflow', 'ast-pro' ),
				'img'		=> 'woo-shipping-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_wc_shipping' => array(
											'type'		=> 'checkbox',
											'default'   => 0,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => ( is_plugin_active( 'woocommerce-services/woocommerce-services.php' ) ) ? 1 : 0, 
				'disabled'  => ( is_plugin_active( 'woocommerce-services/woocommerce-services.php' ) ) ? false : true, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/woocommerce-shipping-tracking-add-on/',
			),
			'enable_ups_shipping_label_pluginhive' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'WooCommerce UPS Shipping', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the WooCommerce UPS Shipping Plugin with Print Label plugin by PluginHive', 'ast-pro' ),
				'img'		=> 'woo-ups-shipping-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_ups_shipping' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/woocommerce-ups-shipping/',
			),
			'enable_canada_post_shipping_label_pluginhive' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'WooCommerce Canada Post Shipping', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the WooCommerce Canada Post Shipping Plugin with Print Label plugin by PluginHive', 'ast-pro' ),
				'img'		=> 'woo-ups-shipping-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_canada_post_shipping' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/woocommerce-canada-post-shipping/',
			),	
			'enable_dhl_for_woocommerce_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'DHL Shipping Germany for WooCommerce', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with DHL for WooCommerce to the Shipment Tracking and automate your workflow', 'ast-pro' ),
				'img'		=> 'dhl-for-wc.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_dhl_for_woocommerce' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/dhl-shipping-germany-for-woocommerce/',
			),		
			'enable_quickbooks_commerce_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'QuickBooks Commerce (formerly TradeGecko)', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with QuickBooks Commerce and automate your workflow', 'ast-pro' ),
				'img'		=> 'quickbooks-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_quickbooks_commerce' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/quickbooks-commerce-tracking/',
			),
			'enable_readytoship_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'ReadyToShip', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with ReadyToShip and automate your workflow', 'ast-pro' ),
				'img'		=> 'readytoship-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_readytoship' => array(
											'type'		=> 'checkbox',
											'default'   => 0,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/readytoship/',
			),
			'enable_royalmail_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Royal Mail Click & Drop', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Royal Mail Click & Drop and automate your workflow', 'ast-pro' ),
				'img'		=> 'royal-mail-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_royalmail' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/royal-mail-click-drop/',
			),	
			'enable_customcat_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'CustomCat', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with CustomCat and automate your workflow', 'ast-pro' ),
				'img'		=> 'customcat-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_customcat' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/customcat/',
			),
			'enable_dear_inventory_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Dear Systems', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Dear Systems and automate your workflow', 'ast-pro' ),
				'img'		=> 'dear-system-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_dear_inventory' => array(
											'type'		=> 'checkbox',
											'default'   => 0,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/dear-systems/',
			),
			/*'enable_printify_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Printify', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Printify and automate your workflow', 'ast-pro' ),
				'img'		=> 'printify-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_printify' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/printify/',
			),*/			
			'enable_picqer_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Picqer', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Picqer and automate your workflow', 'ast-pro' ),
				'img'		=> 'picqer-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_picqer' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/picqer/',
			),
			'enable_3plwinner_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( '3plwinner', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with 3plwinner and automate your workflow', 'ast-pro' ),
				'img'		=> '3plwinner-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_3plwinner' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/3plwinner/',
			),			
			'enable_dianxiaomi_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Dianxiaomi', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Dianxiaomi and automate your workflow', 'ast-pro' ),
				'img'		=> 'dianxiaomi-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_dianxiaomi' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/dianxiaomi/',
			),
			'enable_eiz_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'EIZ', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with EIZ and automate your workflow', 'ast-pro' ),
				'img'		=> 'eiz-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_eiz' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/ebiz/',
			),
			'enable_shippypro_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Shippypro', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with shippypro and automate your workflow', 'ast-pro' ),
				'img'		=> 'shippypro-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_shippypro' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/shippypro/',
			),
			'enable_ali2woo_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'AliExpress Dropshipping', 'ast-pro' ),				
				'desc'   	=> __( 'Add Tracking Information in AST meta fields when you automatically sync tracking numbers from aliexpress orders', 'ast-pro' ),
				'img'		=> 'aliexpress-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_ali2woo' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => ( is_plugin_active( 'ali2woo-lite/ali2woo-lite.php' ) || is_plugin_active( 'ali2woo/ali2woo.php' ) ) ? 1 : 0,
				'disabled'	=> ( is_plugin_active( 'ali2woo-lite/ali2woo-lite.php' ) || is_plugin_active( 'ali2woo/ali2woo.php' ) ) ? false : true,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/ali2woo/',
			),
			'enable_pirateship_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Pirate Ship', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with pirate Ship and automate your workflow', 'ast-pro' ),
				'img'		=> 'pirateship-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_pirateship' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/pirate-ship/',
			),	
			/*'enable_sendcloud_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Sendcloud', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with sendcloud and automate your workflow', 'ast-pro' ),
				'img'		=> 'sendcloud-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_sendcloud' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/sendcloud/',
			),*/
			'enable_shiptheory_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Shiptheory', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with shiptheory and automate your workflow', 'ast-pro' ),
				'img'		=> 'shiptheory-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_shiptheory' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/shiptheory/',
			),
			'enable_stamps_com_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Stamps.com', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with stamps.com and automate your workflow', 'ast-pro' ),
				'img'		=> 'stamps-com-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_stamps_com' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/stamps-com/',
			),
			'enable_shippo_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Shippo', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Shippo and automate your workflow', 'ast-pro' ),
				'img'		=> 'shippo-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_shippo' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/shippo/',
			),
			'enable_inventory_source_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Inventory source', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Inventory source and automate your workflow', 'ast-pro' ),
				'img'		=> 'inventory-source-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_inventory_source' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/ast-pro/integrations/inventory-source/',
			),
			'enable_gls_sell_send_italy_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'GLS Sell & Send Italy', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with GLS Sell & Send and automate your workflow', 'ast-pro' ),
				'img'		=> 'gls.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_gls_sell_send_italy' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/gls-sell-send-italy/',
			),
			'enable_gls_deliveryfrom_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Print Label and Tracking Code for GLS', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Delivery From', 'ast-pro' ),
				'img'		=> 'gls.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_gls_deliveryfrom' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => ( is_plugin_active( 'woo-gls-print-label-and-tracking-code/wp-gls-print-label.php' ) ) ? 1 : 0,
				'disabled'	=> ( is_plugin_active( 'woo-gls-print-label-and-tracking-code/wp-gls-print-label.php' ) ) ? false : true,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/print-label-and-tracking-code-for-gls/',
			),
			'enable_printful_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Printful', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Printful', 'ast-pro' ),
				'img'		=> 'printful-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_printful' => array(
											'type'		=> 'checkbox',
											'default'   => 1,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/printful/',
			),
			'enable_byrd_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Byrd Fulfillment', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Byrd', 'ast-pro' ),
				'img'		=> 'byrd-icon.png',
				'settings'		=> true,
				'settings_fields' => array(
									'autocomplete_byrd' => array(
											'type'		=> 'checkbox',
											'default'   => 0,
											'title'		=> __( 'AutoComplete orders when they are Shipped', 'ast-pro' ),										
											'show'		=> true,
										),
									),
				'default'   => 0,
				'disabled'	=> false,
				'class'     => '',
				'documentation' => 'https://docs.zorem.com/docs/ast-pro/integrations/byrd/',
			),
		);
		
		return $form_data;
	}
}
