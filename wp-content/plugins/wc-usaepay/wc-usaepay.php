<?php
/*
 Plugin Name: WooCommerce USAePay Payment Gateway
 Plugin URI: https://codecanyon.net/item/woocommerce-usaepay-payment-gateway/7966550
 Description: USA ePay is an ECI Certified, Real-Time, Credit Card Processing Gateway. Secure, Fast and Reliable, the USA ePay Gateway is a vital solution to helping your merchants process Credit Card Transactions online from anywhere in the world.
 Version: 2.5.3
 Author:Ryan T.
 Author URI:https://www.ryanplugins.com/
 WC requires at least: 3.0.0
 WC tested up to: 5.2.2
 */

define( 'RPS_WC_USAEPAY', 'rps_wc_usaepay' );

function woocommerce_usaepay_init(){
	if(!class_exists('WC_Payment_Gateway')) return;

    include plugin_dir_path( __FILE__ ) . 'wc.addon.filters.php';
    include plugin_dir_path( __FILE__ ) . 'wc.transaction.api.php';
    include plugin_dir_path( __FILE__ ) . 'wc.transaction.api.addons.php';  

    add_action( 'admin_enqueue_scripts', array( 'USAePay_TRANS_WC_API' , 'order_item_script' ), 1 );
    add_action( 'wp_ajax_woocommerce_add_new_subtotal', array( 'USAePay_TRANS_WC_API', 'add_new_subtotal' ) );
    
}

add_action('plugins_loaded', 'woocommerce_usaepay_init', 0);

add_action( 'admin_enqueue_scripts', RPS_WC_USAEPAY . '_notice_dismiss' );

function rps_wc_usaepay_notice_dismiss($hook) {
    wp_enqueue_script( RPS_WC_USAEPAY . '-notice-dismiss',  plugins_url( 'assets/js/notice-dismiss.js', __FILE__ ) );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), RPS_WC_USAEPAY . '_docs_action_links' );

function rps_wc_usaepay_docs_action_links( $links ) {
  $plugin_links = array(
		'<a href="'.esc_url('https://www.ryanplugins.com/woocommerce-usaepay-payment-gateway-documentation/').'" target="_blank">' . esc_html__( 'Docs', 'woocommerce' ) . '</a>',
		'<a href="'.esc_url('https://codecanyon.net/item/woocommerce-usaepay-payment-gateway/7966550/support').'" target="_blank">' . esc_html__( 'Support', 'woocommerce' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}