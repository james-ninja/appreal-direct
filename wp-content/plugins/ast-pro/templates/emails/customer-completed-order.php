<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $sitepress;
if ( $sitepress ) {
	$old_lan = $sitepress->get_current_language();
	$new_lan = $order->get_meta( 'wpml_language', true );
	$sitepress->switch_lang($new_lan);
}

$ast = new AST_Pro_Actions();
$ast_customizer = Ast_Customizer::get_instance();

$display_shipping_address = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_completed_order_settings', 'display_shipping_address', $ast_customizer->defaults['display_shipping_address'] );
$display_billing_address = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_completed_order_settings', 'display_billing_address', $ast_customizer->defaults['display_billing_address'] );

$email_content = $ast->get_option_value_from_array( 'woocommerce_customer_completed_order_settings', 'wcast_completed_email_content', $ast_customizer->defaults['completed_email_content'] );
$email_content = ast_pro_email_class()->email_content( $email_content, $order->get_id(), $order );	
$email_content = html_entity_decode( $email_content );

$display_shippment_item_price = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_completed_order_settings', 'display_shippment_item_price', $ast_customizer->defaults['display_shippment_item_price'] );
$display_product_images = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_completed_order_settings', 'display_product_images', $ast_customizer->defaults['display_product_images'] );

$shipping_items_heading = $ast->get_option_value_from_array( 'woocommerce_customer_completed_order_settings', 'shipping_items_heading', $ast_customizer->defaults['shipping_items_heading'] );

$ast_preview = ( isset( $_REQUEST['action'] ) && 'ast_email_preview' === $_REQUEST['action'] ) ? true : false;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Site title */ ?>
<div class="email_content"><?php echo wp_kses_post( wpautop( wptexturize( $email_content ) ) ); ?></div>

<?php

//do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

$tracking_items = $ast->get_tracking_items( $order->get_id(), true );

if ( $order->get_id() == 1 ) {
	$tracking_items = $order->get_meta( '_wc_shipment_tracking_items', true );
}

$tpi_order = ast_pro()->ast_tpi->check_if_tpi_order( $tracking_items, $order );
		
if ( !$tpi_order ) {
	$local_template	= get_stylesheet_directory() . '/woocommerce/emails/fluid-tracking-info.php';
	if ( file_exists( $local_template ) && is_writable( $local_template ) ) {
		echo wp_kses_post( wc_get_template( 
			'emails/fluid-tracking-info.php', array( 
				'tracking_items' => $tracking_items,
				'order_id' => $order->get_id(),
			), 
			'woocommerce-advanced-shipment-tracking/',
			get_stylesheet_directory() . '/woocommerce/'
			)
		);
	} else {
		echo wp_kses_post( wc_get_template( 'emails/fluid-tracking-info.php', array( 'tracking_items' => $tracking_items, 'order_id'=> $order->get_id() ), 'woocommerce-advanced-shipment-tracking/', ast_pro()->get_plugin_path() . '/templates/' ) );
	}
}
	
if ( $tpi_order ) {
	$tpi_email_order_details	= get_stylesheet_directory() . '/woocommerce/emails/ast-pro-tpi-email-order-details.php';
	if ( file_exists( $tpi_email_order_details ) && is_writable( $tpi_email_order_details ) ) {
		echo wp_kses_post( wc_get_template(
			'emails/ast-pro-tpi-email-order-details.php', array(
				'order'         			=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 		 	=> $sent_to_admin,
				'plain_text'    			=> $plain_text,
				'email'         			=> $email,
				'tracking_items' 			=> $tracking_items,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/ast-pro-tpi-email-order-details.php', array(
				'order'         			=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 		 	=> $sent_to_admin,
				'plain_text'    			=> $plain_text,
				'email'         			=> $email,
				'tracking_items' 			=> $tracking_items,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );	
	}	
} else {
	$email_order_details = get_stylesheet_directory() . '/woocommerce/emails/ast-pro-email-order-details.php';
	if ( file_exists( $email_order_details ) && is_writable( $email_order_details ) ) {
		echo wp_kses_post( wc_get_template(
			'emails/ast-pro-email-order-details.php', array(
				'order'         		   	=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 		   	=> $sent_to_admin,
				'plain_text'    		   	=> $plain_text,
				'email'         		   	=> $email,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/ast-pro-email-order-details.php', array(
				'order'         		   	=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 		   	=> $sent_to_admin,
				'plain_text'    		   	=> $plain_text,
				'email'         		   	=> $email,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );	
	}	
}	

$shipping_email_addresses = get_stylesheet_directory() . '/woocommerce/emails/wcast-shipping-email-addresses.php';

if ( $ast_preview ) {
	$hide_shipping_address_class = ( !$display_shipping_address ) ? 'hide' : '' ;
	if ( file_exists( $shipping_email_addresses ) && is_writable( $shipping_email_addresses ) ) {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-shipping-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> $hide_shipping_address_class,
			),
			'', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-shipping-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> $hide_shipping_address_class,
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );
	}
} elseif ( $display_shipping_address ) {
	
	if ( file_exists( $shipping_email_addresses ) && is_writable( $shipping_email_addresses ) ) {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-shipping-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> '',
			),
			'', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-shipping-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> '',
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );
	}
}

$billing_email_addresses = get_stylesheet_directory() . '/woocommerce/emails/wcast-billing-email-addresses.php';

if ( $ast_preview ) {
	$hide_billing_address_class = ( !$display_billing_address ) ? 'hide' : '' ;
	if ( file_exists( $billing_email_addresses ) && is_writable( $billing_email_addresses ) ) {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-billing-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> $hide_billing_address_class,	
			),
			'', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-billing-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> $hide_billing_address_class,
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );
	} 
} elseif ( $display_billing_address ) {
	
	if ( file_exists( $billing_email_addresses ) && is_writable( $billing_email_addresses ) ) {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-billing-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> '',
			),
			'', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/wcast-billing-email-addresses.php', array(
				'order'         => $order,
				'sent_to_admin' => $sent_to_admin,
				'class'			=> '',
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );
	}
}

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

if ( $sitepress ) {
	$sitepress->switch_lang($old_lan);
}
