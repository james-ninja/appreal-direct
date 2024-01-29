<?php
namespace Barn2\Plugin\WC_Bulk_Variations;

use Barn2\Plugin\WC_Bulk_Variations\Admin\Admin_Controller,
	Barn2\Plugin\WC_Bulk_Variations\Admin\Admin_Products_Page,
	Barn2\Plugin\WC_Bulk_Variations\Handlers\Variation_Table,
	Barn2\Plugin\WC_Bulk_Variations\Handlers\Shortcode,
	Barn2\Plugin\WC_Bulk_Variations\Handlers\Cart,
	Barn2\Plugin\WC_Bulk_Variations\Integration\Quick_View,
	Barn2\Plugin\WC_Bulk_Variations\Integration\Theme_Compat,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
	Barn2\WBV_Lib\Plugin\Premium_Plugin,
	Barn2\WBV_Lib\Plugin\Licensed_Plugin,
	Barn2\WBV_Lib\Registerable,
	Barn2\WBV_Lib\Translatable,
	Barn2\WBV_Lib\Service_Provider,
	Barn2\WBV_Lib\Service_Container,
	Barn2\WBV_Lib\Util as Lib_Util;

/**
 * The main plugin class for WooCommerce Bulk Variations.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Premium_Plugin implements Licensed_Plugin, Registerable, Translatable, Service_Provider {

	const NAME    = 'WooCommerce Bulk Variations';
	const ITEM_ID = 194350;

	use Service_Container;

	public function __construct( $file = null, $version = null ) {
		parent::__construct(
			[
				'name'               => self::NAME,
				'item_id'            => self::ITEM_ID,
				'version'            => $version,
				'file'               => $file,
				'is_woocommerce'     => true,
				'settings_path'      => 'admin.php?page=wc-settings&tab=products&section=' . Settings::SECTION_SLUG,
				'documentation_path' => 'kb-categories/bulk-variations-kb/'
			]
		);
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		parent::register();
		add_action( 'plugins_loaded', [ $this, 'maybe_load_plugin' ] );
	}

	public function maybe_load_plugin() {
		// Don't load anything if WooCommerce not active.
		if ( ! Lib_Util::is_woocommerce_active() ) {
			$this->add_missing_woocommerce_notice();
			return;
		}

		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
		add_action( 'init', [ $this, 'register_services' ] );
	}

	public function get_services() {
		$services          = [];
		$services['admin'] = new Admin_Controller( $this );

		// Initialise plugin if valid and WC active.
		if ( $this->get_license()->is_valid() ) {
			if ( Lib_Util::is_woocommerce_active() ) {
				if ( Lib_Util::is_front_end() ) {
					$services['scripts\frontend']         = new Frontend_Scripts( $this->get_version() );
					$services['handlers\variation_table'] = new Variation_Table();
					$services['handlers\shortcode']       = new Shortcode();
					$services['handlers\cart']            = new Cart();
					$services['integration\quick_view']   = new Quick_View();
					$services['integration\theme_compat'] = new Theme_Compat();
				} elseif ( Lib_Util::is_admin() ) {
					$services['products_admin'] = new Admin_Products_Page( $this, plugin_dir_path( $this->get_file() ) . 'templates/' );
				}
			}
		}

		return $services;
	}

	private function add_missing_woocommerce_notice() {
		if ( is_admin() ) {
			$admin_notice = new \Barn2\WBV_Lib\Admin\Notices();
			$admin_notice->add(
				'wbv_woocommerce_missing',
				'',
				// translators: 1: opening anchor tag linking to WooCommerce, 2: closing anchor tag
				sprintf( __( 'Please %1$sinstall WooCommerce%2$s in order to use WooCommerce Bulk Variations.', 'woocommerce-bulk-variations' ), Lib_Util::format_link_open( 'https://woocommerce.com/', true ), '</a>' ),
				[
					'type'       => 'error',
					'capability' => 'install_plugins',
				]
			);
			$admin_notice->boot();
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-bulk-variations', false, $this->get_slug() . '/languages' );
	}

}
