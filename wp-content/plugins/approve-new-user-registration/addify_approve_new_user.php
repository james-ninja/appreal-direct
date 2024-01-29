<?php
/*
 * Plugin Name:       Approve New User Registration
 * Plugin URI:        https://woocommerce.com/products/approve-new-user-registration/
 * Description:       Ability to manually approve or reject users, Compatible with WordPress and WooCommerce.
 * Version:           1.4.1
 * Author:            Addify
 * Developed By:      Addify
 * Author URI:        https://www.addify.co
 * Support:           https://www.addify.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       addify_approve_new_user
 *
 * Woo: 6399878:21ba582e003705a2b4dfec14bcb2f454
 *
 * WC requires at least: 3.0.9
 * WC tested up to: 4.*.*
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Addify_Approve_New_User' ) ) {
  
	class Addify_Approve_New_User {
		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'addify_apnu_main_init' ) );
			$this->addify_apnu_constant_vars();

			if ( is_admin() ) {
				require ADDIFY_APNU_PLUGINDIR . 'addify_apnu_admin_class.php';
			} else {
				require ADDIFY_APNU_PLUGINDIR . 'addify_apnu_front_class.php';
			}
		}


		public function addify_apnu_main_init() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'addify_approve_new_user', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}
		}
	
		public function addify_apnu_constant_vars() {


			if ( !defined( 'ADDIFY_APNU_URL' ) ) {
				define( 'ADDIFY_APNU_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( !defined( 'ADDIFY_APNU_BASENAME' ) ) {
				define( 'ADDIFY_APNU_BASENAME', plugin_basename( __FILE__ ) );
			}

			if ( ! defined( 'ADDIFY_APNU_PLUGINDIR' ) ) {
				define( 'ADDIFY_APNU_PLUGINDIR', plugin_dir_path( __FILE__ ) );
			}
		}
	}

	new Addify_Approve_New_User();

}
//echo ABSPATH . 'wp-content/plugins/mailchimp-for-woocommerce/mailchimp-woocommerce.php';
require_once ABSPATH . 'wp-content/plugins/mailchimp-for-woocommerce/mailchimp-woocommerce.php';