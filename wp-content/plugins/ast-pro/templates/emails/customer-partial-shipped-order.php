<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ps_settings = new ast_pro_partial_shipped_customizer_email();
$ast = AST_Pro_Actions::get_instance();

$display_shipping_items = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'display_shipping_items', $ps_settings->defaults['display_shipping_items'] );	
$display_shipping_address = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'display_shipping_address', $ps_settings->defaults['display_shipping_address'] );
$display_billing_address = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'display_billing_address', $ps_settings->defaults['display_billing_address'] );

$email_content = $ast->get_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'wcast_partial_shipped_email_content', $ps_settings->defaults['wcast_partial_shipped_email_content'] );	
$email_content = ast_pro_email_class()->email_content( $email_content, $order->get_id(), $order );

$shipping_items_heading = $ast->get_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'shipping_items_heading', $ps_settings->defaults['shipping_items_heading'] );

$display_shippment_item_price = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'display_shippment_item_price', $ps_settings->defaults['display_shippment_item_price'] );

$display_product_images = $ast->get_checkbox_option_value_from_array( 'woocommerce_customer_partial_shipped_order_settings', 'display_product_images', $ps_settings->defaults['display_product_images'] );

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Site title */ ?>
<p class="partial_email_content"><?php echo wp_kses_post( wpautop( wptexturize( $email_content ) ) ); ?></p>

<?php
$tracking_items = $ast->get_tracking_items( $order->get_id(), true );

if ( $order->get_id() == 1 ) {
	$tracking_items = get_post_meta( $order->get_id(), '_wc_shipment_tracking_items', true );
}

$tpi_order = ast_pro()->ast_tpi->check_if_tpi_order( $tracking_items, $order );

if ( !$tpi_order || !$display_shipping_items ) {
	$local_template	= get_stylesheet_directory() . '/woocommerce/emails/tracking-info.php';			
	if ( file_exists( $local_template ) && is_writable( $local_template ) ) {				
		echo wp_kses_post( wc_get_template( 'emails/tracking-info.php', array( 
			'tracking_items' => $tracking_items,
			'order_id' => $order->get_id(),
		), 'woocommerce-advanced-shipment-tracking/', get_stylesheet_directory() . '/woocommerce/' ) );
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
				'sent_to_admin' 			=> $sent_to_admin,
				'plain_text'    			=> $plain_text,
				'email'         			=> $email,
				'tracking_items' 			=> $tracking_items,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'woocommerce-advanced-shipment-tracking/', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/ast-pro-tpi-email-order-details.php', array(
				'order'         			=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 			=> $sent_to_admin,
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
				'order'         			=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 			=> $sent_to_admin,
				'plain_text'    			=> $plain_text,
				'email'         			=> $email,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'woocommerce-advanced-shipment-tracking/', 
			get_stylesheet_directory() . '/woocommerce/'
		) );
	} else {
		echo wp_kses_post( wc_get_template(
			'emails/ast-pro-email-order-details.php', array(
				'order'         			=> $order,
				'hide_shipping_item_price' 	=> 1,
				'sent_to_admin' 			=> $sent_to_admin,
				'plain_text'    			=> $plain_text,
				'email'         			=> $email,
				'display_shippment_item_price'	=> $display_shippment_item_price,
				'display_product_images'	=> $display_product_images,
				'shipping_items_heading'	=> $shipping_items_heading,
			),
			'woocommerce-advanced-shipment-tracking/', 
			ast_pro()->get_plugin_path() . '/templates/'
		) );	
	}
}	


if ( $display_shipping_address ) {
	echo wp_kses_post( wc_get_template(
		'emails/wcast-shipping-email-addresses.php', array(
			'order'         => $order,
			'sent_to_admin' => $sent_to_admin,
		),
		'woocommerce-advanced-shipment-tracking/', 
		ast_pro()->get_plugin_path() . '/templates/'
	) );
}

if ( $display_billing_address ) {
	echo wp_kses_post( wc_get_template(
		'emails/wcast-billing-email-addresses.php', array(
			'order'         => $order,
			'sent_to_admin' => $sent_to_admin,
		),
		'woocommerce-advanced-shipment-tracking/', 
		ast_pro()->get_plugin_path() . '/templates/'
	) );
}

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
