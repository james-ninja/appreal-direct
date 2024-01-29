<?php

/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>
<?php //echo '<h4 class="mb-5 mt-5">' . esc_html($current_user->user_firstname ) . ' Dashboard</h4>'; ?>

<?php //custom
echo '<h4>Most Recent Orders<h4>';
echo do_shortcode('[woo_order_dashboard_page]');
?>
<?php

// For logged in users only
/*
$user_id = get_current_user_id(); // The current user ID

// Get the WC_Customer instance Object for the current user
$customer = new WC_Customer($user_id);

// Get the last WC_Order Object instance from current customer
$last_order = $customer->get_last_order();
if ($last_order) {
	$order_id     = $last_order->get_id(); // Get the order id
	$order_data   = $last_order->get_data(); // Get the order unprotected data in an array
	$order_status = $last_order->get_status(); // Get the order status
	echo 'order:' . $order_id;
?>
	<div class="row last-order">
		<div class="col-md-7">
			<ul>
				<?php foreach ($last_order->get_items() as $item) : ?>
					<li><?php echo $item->get_name(); ?></li>
					<li><?php echo $item->get_quantity(); ?>Items</li>
					<li><?php echo wc_price($item->get_total()); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="col-md-4 order-status-box">
			<h6 class="status"><?php //echo esc_html(wc_get_order_status_name($order_status)); 
								?></h6>
		</div>
	</div>
<?php
}*/

?>

<p>
	<?php
	/*printf(
		wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url() )
	);
	*/ ?>
</p>

<p>
	<?php

	/*$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	if ( wc_shipping_enabled() ) {
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	}
	printf(
		wp_kses( $dashboard_desc, $allowed_html ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);*/
	?>
</p>
<?php //custom
echo '<div class="featured-list-section">';
echo '<h4>Featured Lists<h4>';
get_template_part('/woocommerce/wishlist-manage-modern-dashboard');
echo '</div>';
/*
?>
<ul class="products columns-4">
<?php 
$user_id = get_current_user_id();
$reg_product_cat_select = get_user_meta($user_id, 'reg_product_cat_select', true);
if($reg_product_cat_select){
	$product_cat_selected = unserialize($reg_product_cat_select);
}
$reg_product_brand_select = get_user_meta($user_id, 'reg_product_brand_select', true);
if($reg_product_brand_select){
	$product_brand_select = unserialize($reg_product_brand_select);
}

	$args = array(
		'post_type' => 'product',
		'posts_per_page' => 4,
		'tax_query' => array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'id',
				'terms'    => $product_cat_selected,
			),
			array(
				'taxonomy' => 'product_brand',
				'field'    => 'id',
				'terms'    => $product_brand_selec,
			),
		),
	);

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        wc_get_template_part( 'content', 'product' );
    endwhile;

    wp_reset_query();
?>
</ul>
<?php */ ?>
<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
	do_action('woocommerce_account_dashboard');

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action('woocommerce_before_my_account');

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action('woocommerce_after_my_account');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
