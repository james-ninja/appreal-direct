<?php

/**
 * User import export
 *
 *
 * @link              https://www.webtoffee.com/
 * @since             1.0.0
 * @package           Wt_Import_Export_For_Woo
 *
 * @wordpress-plugin
 * Plugin Name:       User Import Export for WooCommerce Add-on
 * Plugin URI:        https://www.webtoffee.com/product/woocommerce-import-export-suite/
 * Description:       User Import Export Add-on for WooCommerce
 * Version:           1.0.8
 * Author:            Webtoffee
 * Author URI:        https://www.webtoffee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wt-import-export-for-woo
 * Domain Path:       /languages
 * WC tested up to:   5.5
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WT_CUSTOMER_IMP_EXP_ID' ) ) {
	define('WT_CUSTOMER_IMP_EXP_ID', 'wt_iew_customer_import_export');
}

/* Plugin page links */
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wt_iew_plugin_action_links_user');

function wt_iew_plugin_action_links_user($links)
{
	if(defined('WT_IEW_PLUGIN_ID')) /* main plugin is available */
	{
		$links[] = '<a href="'.admin_url('admin.php?page='.WT_IEW_PLUGIN_ID).'">'.__('Settings', 'wt-import-export-for-woo').'</a>';
	}

	$links[] = '<a href="https://www.webtoffee.com/import-or-export-woocommerce-users-customers/" target="_blank">'.__('Documentation', 'wt-import-export-for-woo').'</a>';
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

register_activation_hook( __FILE__, 'wt_missing_plugins_warning_on_activation_user' );
function wt_missing_plugins_warning_on_activation_user() {
    if( !get_option('wt_iew_is_active')){
        set_transient( 'wt_missing_plugins_warning_on_activation_user', true, 5 );
    }
}
add_action( 'admin_notices',  'wt_missing_plugins_warning_user',1);
function wt_missing_plugins_warning_user(){
    /* Check transient, if available display the notice on plugin activation */
    if( get_transient( 'wt_missing_plugins_warning_on_activation_user' ) ){

        $class = 'notice notice-error';  
        $post_type = 'user';
        $message = sprintf(__('<b>%s</b> has been activated. However you need to install and activate the <b>WebToffee wrapper plugin</b> also to start export/import of %s.
        Go to <a href="%s" target="_blank">My accounts->API Downloads</a> to download and activate the wrapper. If already installed activate the wrapper plugin from under <a href="%s" target="_blank">Plugins</a>.', 'wt-import-export-for-woo'), ucfirst($post_type) .' import export', $post_type.'s', 'https://www.webtoffee.com/my-account/my-api-downloads/',admin_url('plugins.php?s=Import%20Export%20for%20WooCommerce'));
        printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) );                     

        /* Delete transient, only display this notice once. */
        delete_transient( 'wt_missing_plugins_warning_on_activation_user' );
    }   
}

add_action( 'wt_user_addon_help_content', 'wt_user_import_help_content' );

function wt_user_import_help_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID' ) ) {
		?>
			<li>
				<img src="<?php echo WT_IEW_PLUGIN_URL; ?>assets/images/sample-csv.png">
				<h3><?php _e( 'Sample User CSV', 'wt-import-export-for-woo' ); ?></h3>
				<p><?php _e( 'Familiarize yourself with the sample CSV.', 'wt-import-export-for-woo' ); ?></p>
				<a target="_blank" href="https://www.webtoffee.com/wp-content/uploads/2020/10/Sample_Users.csv" class="button button-primary">
				<?php _e( 'Get User CSV', 'wt-import-export-for-woo' ); ?>        
				</a>
			</li>
		<?php
	}
}


define( 'WT_UIEW_VERSION', '1.0.8' );

/* hook to licence manager */
add_filter('wt_iew_add_licence_manager', 'wt_uiew_add_licence_manager');

function wt_uiew_add_licence_manager($products)
{
    $plugin_slug='wt-import-export-for-woo-user';
    $settings_url='';
    if(defined('WT_IEW_PLUGIN_ID')) /* main plugin is available */
    {
        $settings_url = admin_url('admin.php?page='.WT_IEW_PLUGIN_ID.'#wt-licence');
    }
    $products[$plugin_slug]=array(
        'product_id'            =>  'customercsvimportexport',
        'product_edd_id'        =>  '196721',
        'plugin_settings_url'   =>  $settings_url,
        'product_version'       =>  WT_UIEW_VERSION,
        'product_name'          =>  plugin_basename(__FILE__),
        'product_slug'          =>  $plugin_slug,
        'product_display_name'  =>  'User Import Export for WooCommerce', //plugin name, no translation needed
    );
    
    return $products;
}
