<?php
/**
 * Plugin Name:          Bulk Order Form for WooCommerce
 * Plugin URI:           https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * Description:          Adds the [wcbulkorder] shortcode which allows you to display bulk order forms on any page in your site
 * Version:              3.6.2
 * Author:               WP Overnight
 * Author URI:           https://wpovernight.com/
 * License:              GPLv2 or later
 * License URI:          https://opensource.org/licenses/gpl-license.php
 * Text Domain:          woocommerce-bulk-order-form
 * Domain Path:          /languages
 * WC requires at least: 3.0
 * WC tested up to:      8.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommerce_Bulk_Order_Form' ) ):

class WooCommerce_Bulk_Order_Form {

	/**
	 * _instance
	 *
	 * @var WooCommerce_Bulk_Order_Form|null
	 */
	protected static $_instance = null;

	/**
	 * functions
	 *
	 * @var WooCommerce_Bulk_Order_Form_Functions|null
	 */
	protected static $functions = null;

	/**
	 * admin
	 *
	 * @var WooCommerce_Bulk_Order_Form_Admin|null
	 */
	protected static $admin = null;

	/**
	 * settings
	 *
	 * @var WooCommerce_Bulk_Order_Form_Settings_Framework|null
	 */
	protected static $settings = null;

	/**
	 * version
	 *
	 * @var string
	 */
	public $version = '3.6.2';

	public function __construct() {
		$this->define_constant();

		$dependency = include_once WC_BOF_INC . 'class-dependencies.php';
		if ( ! $dependency->check_dependencies() ) {
			return;
		}

		$this->load_required_files();
		$this->init_hooks();
	}

	private function define_constant(): void {
		$this->define( 'WC_BOF_FILE', plugin_basename( __FILE__ ) );
		$this->define( 'WC_BOF_PATH', plugin_dir_path( __FILE__ ) ); # Plugin DIR
		$this->define( 'WC_BOF_INC', WC_BOF_PATH . 'includes/' ); # Plugin INC Folder
		$this->define( 'WC_BOF_NAME', 'Bulk Order Form for WooCommerce' ); # Plugin Name
		$this->define( 'WC_BOF_SLUG', 'woocommerce-bulk-order-form' ); # Plugin Slug
		$this->define( 'WC_BOF_TXT', 'woocommerce-bulk-order-form' ); #plugin lang Domain
		$this->define( 'WC_BOF_DB', 'wc_bof_' );
		$this->define( 'WC_BOF_V', $this->version ); # Plugin Version
		$this->define( 'WC_BOF_LANGUAGE_PATH', WC_BOF_PATH . 'languages' ); # Plugin Language Folder
		$this->define( 'WC_BOF_ADMIN', WC_BOF_INC . 'admin/' ); # Plugin Admin Folder
		$this->define( 'WC_BOF_SETTINGS', WC_BOF_ADMIN . 'settings/' ); # Plugin Settings Folder
		$this->define( 'WC_BOF_URL', plugins_url( '', __FILE__ ) . '/' );  # Plugin URL
		$this->define( 'WC_BOF_CSS', WC_BOF_URL . 'assets/css/' ); # Plugin CSS URL
		$this->define( 'WC_BOF_IMG', WC_BOF_URL . 'assets/images/' ); # Plugin IMG URL
		$this->define( 'WC_BOF_JS', WC_BOF_URL . 'assets/js/' ); # Plugin JS URL
		$this->define( 'WC_BOF_TEMPLATE_URL', WC_BOF_URL . 'form_templates/' );  # templates URL
		$this->define( 'WC_BOF_TEMPLATE_PATH', WC_BOF_PATH . 'form_templates/' ); # templates Folder
	}

	/**
	 * Define constant if not already set
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function define( string $key, $value ): void {
		if ( ! defined( $key ) ) {
			define( $key, $value );
		}
	}

	private function load_required_files(): void {
		$this->load_files( WC_BOF_INC . 'functions.php' );
		$this->load_files( WC_BOF_INC . 'abstract-*.php' );
		$this->load_files( WC_BOF_INC . 'class-*.php' );
		$this->load_files( WC_BOF_ADMIN . 'settings_framework/class-wp-*.php' );
		$this->load_files( WC_BOF_TEMPLATE_PATH . '*' );
		$this->load_files( WC_BOF_INC . '*.php' );
		if ( is_admin() ) {
			$this->load_files( WC_BOF_ADMIN . 'class-*.php' );
		}
	}

	protected function load_files( string $path, string $type = 'require' ): void {
		foreach ( glob( $path ) as $files ) {
			if ( 'require' === $type ) {
				require_once( $files );
			} elseif ( 'include' === $type ) {
				include_once( $files );
			}
		}
	}

	public function init_hooks(): void {
		add_action( 'plugins_loaded', array( $this, 'after_plugins_loaded' ) );
		// add_filter( 'load_textdomain_mofile', array( $this, 'load_plugin_mo_files' ), 10, 2 );
		add_action( 'init', array( $this, 'init_class' ), 0 );
		// HPOS compatibility
		add_action( 'before_woocommerce_init', array( $this, 'woocommerce_hpos_compatible' ) );

		// run lifecycle methods
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			add_action( 'wp_loaded', array( $this, 'do_install' ) );
		}
	}

	public static function get_instance(): ?WooCommerce_Bulk_Order_Form {
		if ( null == self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * Cloning instances of the class is forbidden.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning instances of the class is forbidden.', 'woocommerce-bulk-order-form' ), WC_BOF_V );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * Unserializing instances of the class is forbidden.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of the class is forbidden.', 'woocommerce-bulk-order-form' ), WC_BOF_V );
	}

	public function init_class(): void {
		if ( ! function_exists( 'WC' ) ) {
			return; // WC not active - throw error message?
		}
		do_action( 'wc_bof_before_init' );

		self::$functions = new WooCommerce_Bulk_Order_Form_Functions;
		self::$settings  = new WooCommerce_Bulk_Order_Form_Settings_Framework;
		new WooCommerce_Bulk_Order_Form_ShortCode_Handler;
		new WooCommerce_Bulk_Order_Form_Ajax_FrontEnd;
		if ( is_admin() ) {
			self::$admin = new WooCommerce_Bulk_Order_Form_Admin;
		}

		do_action( 'wc_bof_init' );
	}
	
	/**
	 * Declares WooCommerce HPOS compatibility.
	 */
	public function woocommerce_hpos_compatible(): void {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_BOF_FILE, true );
		}
	}

	public function func(): ?WooCommerce_Bulk_Order_Form_Functions {
		return self::$functions;
	}

	public function settings(): ?WooCommerce_Bulk_Order_Form_Settings_Framework {
		return self::$settings;
	}

	public function admin(): ?WooCommerce_Bulk_Order_Form_Admin {
		return self::$admin;
	}

	/**
	 * Set Plugin Text Domain
	 */
	public function after_plugins_loaded(): void {
		do_action( 'wc_bof_loaded' );
		$plugin_rel_path = trailingslashit(basename(WC_BOF_PATH)) . 'languages';
		$success = load_plugin_textdomain( 'woocommerce-bulk-order-form', false, $plugin_rel_path );
	}

	/**
	 * Returns Proper MO File Location.
	 *
	 * @param $mofile
	 * @param $domain
	 *
	 * @return string
	 */
	public function load_plugin_mo_files( $mofile, string $domain ): string {
		if ( 'woocommerce-bulk-order-form' === $domain ) {
			$mo = WC_BOF_LANGUAGE_PATH . '/' . get_locale() . '.mo';
			if (file_exists($mo)) {
				return $mo;
			}
		}

		return $mofile;
	}

	/** Lifecycle methods *******************************************************
	 * Because register_activation_hook only runs when the plugin is manually
	 * activated by the user, we're checking the current version against the
	 * version stored in the database
	 ****************************************************************************/

	/**
	 * Handles version checking
	 */
	public function do_install(): void {
		$version_setting   = 'wpo_wc_bof_version';
		$installed_version = get_option( $version_setting );

		// installed version lower than plugin version?
		if ( version_compare( $installed_version, $this->version, '<' ) ) {
			if ( ! $installed_version ) {
				$this->install();
			} else {
				$this->upgrade( $installed_version );
			}

			// new version number
			update_option( $version_setting, $this->version );
		}
	}


	/**
	 * Plugin install method. Perform any installation tasks here
	 */
	protected function install(): void {
		// this may be an update rather than a fresh install if updating from pre-3.0
		$old_free_settings = get_option( 'wcbulkorderform' );
		if ( ! empty( $old_free_settings ) ) {
			$this->upgrade( 'versionless' );
			return;
		}
	}

	/**
	 * Plugin upgrade method.  Perform any required upgrades here
	 *
	 * @param string $installed_version the currently installed ('old') version
	 */
	protected function upgrade( string $installed_version ): void {

		// 3.0 update: migrate settings
		if ( $installed_version == 'versionless' ) {
			// get template
			$old_options                    = array();
			$old_options['wcbulkorderform'] = get_option( 'wcbulkorderform', array() );
			$template                       = ! empty( $old_options['wcbulkorderform']['template_style'] ) ? strtolower( $old_options['wcbulkorderform']['template_style'] ) : 'standard';

			// map new settings to old
			$settings_map = array(
				'wc_bof_general'        => array(
					'wc_bof_template_type'    => array( 'wcbulkorderform' => 'template_style' ),
					'wc_bof_no_of_rows'       => array( 'wcbulkorderform_' . $template . '_template' => 'bulkorder_row_number' ),
					'wc_bof_max_items'        => array( 'wcbulkorderform_' . $template . '_template' => 'max_items' ),
					'wc_bof_show_image'       => array( 'wcbulkorderform_' . $template . '_template' => 'display_images' ),
					'wc_bof_single_addtocart' => null,
					'wc_bof_add_rows'         => array( 'wcbulkorderform_' . $template . '_template' => 'new_row_button' ),
					'wc_bof_show_price'       => array( 'wcbulkorderform_' . $template . '_template' => 'display_price' ),
					'wc_bof_action_button'    => null,
					'wc_bof_image_width'      => null,
					'wc_bof_image_height'     => null,
				),
				'wc_bof_products'       => array(
					'wc_bof_category'                 => null,
					'wc_bof_excluded'                 => null,
					'wc_bof_included'                 => null,
					'wc_bof_search_by'                => array( 'wcbulkorderform_' . $template . '_template' => 'search_by' ),
					'wc_bof_enable_search_attributes' => null,
					'wc_bof_product_attributes'       => null,
					'wc_bof_result_format'            => array( 'wcbulkorderform_' . $template . '_template' => 'search_format' ),
					'wc_bof_result_variation_format'  => array( 'wcbulkorderform_' . $template . '_template' => 'search_format' ),
					'wc_bof_attribute_display_format' => array( 'wcbulkorderform_' . $template . '_template' => 'attribute_style' ),
				),
				'wc_bof_template_label' => array(
					'wc_bof_price_label'            => array( 'wcbulkorderform_' . $template . '_template' => 'price_field_title' ),
					'wc_bof_product_label'          => array( 'wcbulkorderform_' . $template . '_template' => 'product_field_title' ),
					'wc_bof_quantity_label'         => array( 'wcbulkorderform_' . $template . '_template' => 'quantity_field_title' ),
					'wc_bof_variation_label'        => array( 'wcbulkorderform_' . $template . '_template' => 'variation_field_title' ),
					'wc_bof_single_addtocart_label' => null,
					'wc_bof_cart_label'             => null,
					'wc_bof_checkout_label'         => null,
				),
			);

			$defaults = array(
				'wc_bof_general'        => array(
					'wc_bof_template_type'    => 'standard',
					'wc_bof_no_of_rows'       => 10,
					'wc_bof_max_items'        => 0,
					'wc_bof_single_addtocart' => true,
					'wc_bof_add_rows'         => true,
					'wc_bof_show_image'       => true,
					'wc_bof_show_price'       => true,
					'wc_bof_action_button'    => 'cart',
					'wc_bof_image_width'      => 50,
					'wc_bof_image_height'     => 50,
				),
				'wc_bof_products'       => array(
					'wc_bof_category'                 => '',
					'wc_bof_excluded'                 => '',
					'wc_bof_included'                 => '',
					'wc_bof_search_by'                => 'all',
					'wc_bof_enable_search_attributes' => '',
					'wc_bof_product_attributes'       => '',
					'wc_bof_result_format'            => 'TPS',
					'wc_bof_result_variation_format'  => 'TPS',
					'wc_bof_attribute_display_format' => 'value',
				),
				'wc_bof_template_label' => array(
					'wc_bof_price_label'            => __( 'Price', 'woocommerce-bulk-order-form' ),
					'wc_bof_product_label'          => __( 'Product', 'woocommerce-bulk-order-form' ),
					'wc_bof_quantity_label'         => __( 'Qty', 'woocommerce-bulk-order-form' ),
					'wc_bof_variation_label'        => __( 'Variation', 'woocommerce-bulk-order-form' ),
					'wc_bof_single_addtocart_label' => __( 'Add to cart', 'woocommerce-bulk-order-form' ),
					'wc_bof_cart_label'             => __( 'Cart', 'woocommerce-bulk-order-form' ),
					'wc_bof_checkout_label'         => __( 'Checkout', 'woocommerce-bulk-order-form' ),
				),
			);

			// walk through map
			foreach ( $settings_map as $new_option => $new_settings_keys ) {
				${$new_option} = array();
				foreach ( $new_settings_keys as $new_key => $old_setting ) {
					if ( empty( $old_setting ) ) {
						continue; // setting didn't exist in old version
					}

					$old_key    = reset( $old_setting );
					$old_option = key( $old_setting );

					// load old option if not already loaded
					if ( ! isset( $old_options[ $old_option ] ) ) {
						$old_options[ $old_option ] = get_option( $old_option, array() );
					}

					// migrate options, convert where necessary
					if ( ! empty( $old_options[ $old_option ][ $old_key ] ) ) {
						switch ( $old_key ) {
							case 'template_style':
								${$new_option}[ $new_key ] = strtolower( $old_options[ $old_option ][ $old_key ] );
								break;
							case 'display_images':
							case 'new_row_button':
							case 'display_price':
								// convert radio 'true'/'false' to checkbox 'on'
								if ( $old_options[ $old_option ][ $old_key ] == 'true' ) {
									${$new_option}[ $new_key ] = 'on';
								}
								break;
							case 'search_by':
								$search_by                 = $old_options[ $old_option ][ $old_key ];
								$conversion                = array(
									'1' => 'sku',
									'2' => 'id',
									'3' => 'title',
									'4' => 'all',
								);
								${$new_option}[ $new_key ] = isset( $conversion[ $search_by ] ) ? $conversion[ $search_by ] : 'all';
								break;
							case 'search_format':
								$search_format             = $old_options[ $old_option ][ $old_key ];
								$conversion                = array(
									'1' => 'STP',
									'2' => 'TPS',
									'3' => 'TP',
									'4' => 'TS',
									'5' => 'T',
								);
								${$new_option}[ $new_key ] = isset( $conversion[ $search_format ] ) ? $conversion[ $search_format ] : 'TPS';
								break;
							case 'attribute_style':
								$attribute_style           = $old_options[ $old_option ][ $old_key ];
								$conversion                = array(
									'true'  => 'value',
									'false' => 'attributes_value',
								);
								${$new_option}[ $new_key ] = isset( $conversion[ $attribute_style ] ) ? $conversion[ $attribute_style ] : 'all';
								break;
							default:
								${$new_option}[ $new_key ] = $old_options[ $old_option ][ $old_key ];
								break;
						}
					}
				}

				// merge with existing settings
				$new_option_default     = isset( $defaults[ $new_option ] ) ? $defaults[ $new_option ] : array();
				${$new_option . "_old"} = get_option( $new_option, $new_option_default ); // second argument loads new as default in case the settings did not exist yet
				${$new_option}          = (array) ${$new_option} + (array) ${$new_option . "_old"}; // duplicate options take new options as default

				// store new option values
				update_option( $new_option, ${$new_option} );
			}
		}

	}

} // end class WC_Bulk_Order_Form

endif; // end class_exists

function WooCommerce_Bulk_Order_Form(): ?WooCommerce_Bulk_Order_Form {
	return WooCommerce_Bulk_Order_Form::get_instance();
}
WooCommerce_Bulk_Order_Form();
