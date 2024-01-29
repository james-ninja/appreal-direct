<?php

/**
 * Product import export
 *
 *
 * @link              https://www.webtoffee.com/
 * @since             1.0.0
 * @package           Wt_Import_Export_For_Woo
 *
 * @wordpress-plugin
 * Plugin Name:       Product Import Export for WooCommerce Add-on
 * Plugin URI:        https://www.webtoffee.com/product/import-export-woocommerce/
 * Description:       Product Import Export Add-on for WooCommerce
 * Version:           1.0.6
 * Author:            Webtoffee
 * Author URI:        https://www.webtoffee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wt-import-export-for-woo-product
 * Domain Path:       /languages
 * WC tested up to:   5.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Plugin page links */
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wt_iew_plugin_action_links_product');

function wt_iew_plugin_action_links_product($links)
{
	if(defined('WT_IEW_PLUGIN_ID')) /* main plugin is available */
	{
		$links[] = '<a href="'.admin_url('admin.php?page='.WT_IEW_PLUGIN_ID).'">'.__('Settings', 'wt-import-export-for-woo').'</a>';
	}

	$links[] = '<a href="https://www.webtoffee.com/import-export-woocommerce-products/" target="_blank">'.__('Documentation', 'wt-import-export-for-woo').'</a>';
	$links[] = '<a href="https://www.webtoffee.com/support/" target="_blank">'.__('Support', 'wt-import-export-for-woo').'</a>';
	return $links;
}

/**
* Missing plugins warning.
*/
add_action( 'admin_notices',  'wt_missing_plugins_warning');
if(!function_exists('wt_missing_plugins_warning')){
    function wt_missing_plugins_warning() {
        if (!get_option('wt_iew_is_active')) {            
            /* Display the notice*/
            $class = 'notice notice-error';                        
            $message = sprintf(__('The <b>WebToffee Import/Export wrapper plugin</b> should be activated in order to import/export any of the post types supported via <b>WebToffee add-ons(Product/Reviews, User, Order/Coupon/Subscription)</b>.
            Go to <a href="%s" target="_blank">My accounts->API Downloads</a> to download and activate the wrapper.  If already installed, activate the wrapper plugin from under <a href="%s" target="_blank">Plugins</a>.', 'wt-import-export-for-woo'),'https://www.webtoffee.com/my-account/my-api-downloads/',admin_url('plugins.php?s=Import%20Export%20for%20WooCommerce'));
            printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) ); 
                                
        }
    }
}

register_activation_hook( __FILE__, 'wt_missing_plugins_warning_on_activation_product' );
function wt_missing_plugins_warning_on_activation_product() {
    if( !get_option('wt_iew_is_active')){
        set_transient( 'wt_missing_plugins_warning_on_activation_product', true, 5 );
    }
}
add_action( 'admin_notices',  'wt_missing_plugins_warning_product',1);
function wt_missing_plugins_warning_product(){
    /* Check transient, if available display the notice on plugin activation */
    if( get_transient( 'wt_missing_plugins_warning_on_activation_product' ) ){

        $class = 'notice notice-error';  
        $post_type = 'product';
        $message = sprintf(__('<b>%s</b> has been activated. However you need to install and activate the <b>WebToffee wrapper plugin</b> also to start export/import of %s.
        Go to <a href="%s" target="_blank">My accounts->API Downloads</a> to download and activate the wrapper. If already installed activate the wrapper plugin from under <a href="%s" target="_blank">Plugins</a>.', 'wt-import-export-for-woo'), ucfirst($post_type) .' import export', $post_type.'s', 'https://www.webtoffee.com/my-account/my-api-downloads/',admin_url('plugins.php?s=Import%20Export%20for%20WooCommerce'));
        printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) );                     

        /* Delete transient, only display this notice once. */
        delete_transient( 'wt_missing_plugins_warning_on_activation_product' );
    }   
}

/* for temparary fix, wc_get_product_object is not support below WC3.9.0  */ 
if(!function_exists('wt_wc_get_product_object')){  // need change this approch, cant activate WC while product add on in active state
    function wt_wc_get_product_object( $product_type, $product_id = 0 ) {
            $classname = WC_Product_Factory::get_product_classname( $product_id, $product_type );
            return new $classname( $product_id );
    }
}


/* Checking WC is actived or not */
if(!function_exists('is_plugin_active'))
{
    include_once(ABSPATH.'wp-admin/includes/plugin.php');
}
if(!is_plugin_active('woocommerce/woocommerce.php') || !class_exists( 'WooCommerce' ))
{
    add_action( 'admin_notices', 'wt_wc_missing_warning_product' );
}
if(!function_exists('wt_wc_missing_warning_product')){
    function wt_wc_missing_warning_product(){
        
        $install_url = wp_nonce_url(add_query_arg(array('action' => 'install-plugin','plugin' => 'woocommerce',),admin_url( 'update.php' )),'install-plugin_woocommerce');              
        $class = 'notice notice-error';  
        $post_type = 'product';
        $message = sprintf(__('The <b>WooCommerce</b> plugin must be active for <b>%s Import Export for WooCommerce</b> plugin to work.  Please <a href="%s" target="_blank">install & activate WooCommerce</a>.', 'wt-import-export-for-woo'), ucfirst($post_type), esc_url( $install_url ));
        printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) );   
    }
}

add_action( 'wt_product_addon_help_content', 'wt_product_import_export_help_content' );

function wt_product_import_export_help_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID' ) ) {
		?>
			<li>
				<img src="<?php echo WT_IEW_PLUGIN_URL; ?>assets/images/sample-csv.png">
				<h3><?php _e( 'Sample Product CSV', 'wt-import-export-for-woo'); ?></h3>
				<p><?php _e( 'Familiarize yourself with the sample CSV.', 'wt-import-export-for-woo'); ?></p>
				<a target="_blank" href="https://www.webtoffee.com/wp-content/uploads/2021/03/Product_SampleCSV.csv" class="button button-primary">
				<?php _e( 'Get Product CSV', 'wt-import-export-for-woo'); ?>        
				</a>
			</li>
		<?php
	}
}


define( 'WT_PIEW_VERSION', '1.0.6' );

// Hook to licence manager
add_filter('wt_iew_add_licence_manager', 'wt_piew_add_licence_manager');

function wt_piew_add_licence_manager($products)
{
    $plugin_slug='wt-import-export-for-woo-product';
    $settings_url='';
    if(defined('WT_IEW_PLUGIN_ID')) // Main plugin is available
    {
        $settings_url = admin_url('admin.php?page='.WT_IEW_PLUGIN_ID.'#wt-licence');
    }
    $products[$plugin_slug]=array(
        'product_id'            =>  'productcsvimportexport',
        'product_edd_id'        =>  '214146',
        'plugin_settings_url'   =>  $settings_url,
        'product_version'       =>  WT_PIEW_VERSION,
        'product_name'          =>  plugin_basename(__FILE__),
        'product_slug'          =>  $plugin_slug,
        'product_display_name'  =>  'Product Import Export for WooCommerce', //plugin name, no translation needed
    );
    
    return $products;
}

