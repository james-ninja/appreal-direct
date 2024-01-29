<?php
/**
 * Plugin Name: Discontinued Product Stock Status for WooCommerce
 * Description: Discontinued Product Stock Status for WooCommerce allows you to list a product as ‘Discontinued’ in your WooCommerce catalog, optionally write a custom message to guide your buyers to newer or other products and thus helping you recover lost sales and SEO traffic in the process.
 * Author URI: https://www.saffiretech.com/
 * Author: SaffireTech
 * Text Domain: discontinued-products-stock-status
 * Domain Path: /languages
 * Stable Tag : 1.1.4
 * Requires at least: 5.0
 * Tested up to: 6.2.2
 * Requires PHP: 7.2
 * License:    GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Version: 1.1.4
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

define( 'DPSSW_DISCOUNTINUED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );


add_action( 'init', 'discontinued_product_load_required_file' );

/**
 * Load plugin files.
 */
function discontinued_product_load_required_file() {
	require_once dirname( __FILE__ ) . '/includes/dpssw-product-data-tabs.php';
	require_once dirname( __FILE__ ) . '/includes/dpssw-functions.php';
}


add_action( 'admin_enqueue_scripts', 'dpssw_discontinued_assets' );

/**
 * Include styling and js files.
 *
 * @param string $hook .
 */
function dpssw_discontinued_assets( $hook ) {
	wp_enqueue_style( 'discontinued_css', plugins_url( 'assets/css/discontinued_products.css', __FILE__ ), array(), '1.0' );
	wp_enqueue_script( 'jquery' );
	wp_register_script( 'discontinued_js', plugins_url( 'assets/js/discontinued.js', __FILE__ ), array( 'jquery', 'wp-i18n' ), '1.0', 'false' );
	wp_enqueue_script( 'discontinued_js' );
	wp_localize_script(
		'discontinued_js',
		'dpssw_custom_data',
		array(
			'nonce'      => wp_create_nonce( 'discontinued-products-stock-status' ),
			'url'        => admin_url( 'admin-ajax.php' ),
			'wc_version' => floatval( WC_VERSION ),
		)
	);
}


add_action( 'admin_init', 'dpssw_discontinued_load_textdomain_file' );

/**
 * Load Text Domain
 */
function dpssw_discontinued_load_textdomain_file() {
	load_plugin_textdomain( 'discontinued-products-stock-status', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * 'Discontinued' stock status  to 'Out of Stock' stock status on deactivation of this plugin.
 */
register_deactivation_hook( __FILE__, 'dpssw_restore_to_outofstock_on_plugin_deactivate' );

// HPOS Compatibility.
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
