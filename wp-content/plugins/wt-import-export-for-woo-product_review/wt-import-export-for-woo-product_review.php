<?php

/**
 * Product review import export
 *
 *
 * @link              https://www.webtoffee.com/
 * @since             1.0.0
 * @package           Wt_Import_Export_For_Woo
 *
 * @wordpress-plugin
 * Plugin Name:       Product review Import Export for WooCommerce Add-on
 * Plugin URI:        https://www.webtoffee.com/product/import-export-woocommerce/
 * Description:       Product review Import Export Add-on for WooCommerce
 * Version:           1.0.4
 * Author:            Webtoffee
 * Author URI:        https://www.webtoffee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wt-import-export-for-woo
 * Domain Path:       /languages
 * WC tested up to:   5.3.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Plugin page links */
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wt_iew_plugin_action_links_product_review');

function wt_iew_plugin_action_links_product_review($links)
{
	if(defined('WT_IEW_PLUGIN_ID')) /* main plugin is available */
	{
		$links[] = '<a href="'.admin_url('admin.php?page='.WT_IEW_PLUGIN_ID).'">'.__('Settings', 'wt-import-export-for-woo').'</a>';
	}

	$links[] = '<a href="https://www.webtoffee.com/import-export-woocommerce-product-reviews-and-ratings/" target="_blank">'.__('Documentation', 'wt-import-export-for-woo').'</a>';
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

register_activation_hook( __FILE__, 'wt_missing_plugins_warning_on_activation_product_review' );
function wt_missing_plugins_warning_on_activation_product_review() {
    if( !get_option('wt_iew_is_active')){
        set_transient( 'wt_missing_plugins_warning_on_activation_product_review', true, 5 );
    }
}
add_action( 'admin_notices',  'wt_missing_plugins_warning_product_review',1);
function wt_missing_plugins_warning_product_review(){
    /* Check transient, if available display the notice on plugin activation */
    if( get_transient( 'wt_missing_plugins_warning_on_activation_product_review' ) ){

        $class = 'notice notice-error';  
        $post_type = 'product review';
        $message = sprintf(__('<b>%s</b> has been activated. However you need to install and activate the <b>WebToffee wrapper plugin</b> also to start export/import of %s.
        Go to <a href="%s" target="_blank">My accounts->API Downloads</a> to download and activate the wrapper. If already installed activate the wrapper plugin from under <a href="%s" target="_blank">Plugins</a>.', 'wt-import-export-for-woo'), ucfirst($post_type) .' import export', $post_type.'s', 'https://www.webtoffee.com/my-account/my-api-downloads/',admin_url('plugins.php?s=Import%20Export%20for%20WooCommerce'));
        printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) );                     

        /* Delete transient, only display this notice once. */
        delete_transient( 'wt_missing_plugins_warning_on_activation_product_review' );
    }   
}

add_action( 'wt_review_addon_help_content', 'wt_review_import_export_help_content' );

function wt_review_import_export_help_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID' ) ) {
		?>
			<li>
				<img src="<?php echo WT_IEW_PLUGIN_URL; ?>assets/images/sample-csv.png">
				<h3><?php _e( 'Sample review CSV', 'wt-import-export-for-woo'); ?></h3>
				<p><?php _e( 'Familiarize yourself with the sample CSV.', 'wt-import-export-for-woo'); ?></p>
				<a target="_blank" href="https://www.webtoffee.com/wp-content/uploads/2021/04/product_review_SampleCSV.csv" class="button button-primary">
				<?php _e( 'Get Review CSV', 'wt-import-export-for-woo'); ?>        
				</a>
			</li>
		<?php
	}
}
