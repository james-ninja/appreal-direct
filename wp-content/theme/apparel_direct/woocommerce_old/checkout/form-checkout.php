<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
	exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
	return;
}

?>
<?php

$session_ship_address_id = WC()->session->get('default_shipping_address');
$session_bill_address_id = WC()->session->get('default_billing_address');

/*echo '<pre>';
print_r(WC()->session);
echo '</pre>';*/

$user_id  = get_current_user_id();
global $wpdb;
$tablename = $wpdb->prefix . 'ocwma_billingadress';

$user_bill_address = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id . " AND Defalut='1' ");
$user_bill_address_default = $user_bill_address[0]->id;

$user_shipping_address = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id . " AND Defalut='1' ");
$user_shipping_address_default = $user_shipping_address[0]->id;


if (is_numeric($session_ship_address_id)) {
	//echo 'session in';
	$user_shipping_address_default = $session_ship_address_id;
} else {
	//echo 'session out';
	$user_shipping_address_default = $user_shipping_address_default;

	if (empty($user_shipping_address_default)) {
		$user_ship_address_first = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id);
		$user_shipping_address_default = $user_ship_address_first[0]->id;
		//echo 'firstaddress';
	}
}

if (is_numeric($session_bill_address_id)) {
	$user_bill_address_default = $session_bill_address_id;
} else {
	$user_bill_address_default = $user_bill_address_default;

	if (empty($user_bill_address_default)) {
		$user_bill_address_first = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id);
		$user_bill_address_default = $user_bill_address_first[0]->id;
	}
}


?>
<script>
	jQuery(function($) {
		var defaultShipAddress = "<?php echo $user_shipping_address_default; ?>";
		setTimeout(function() {
			$('.ocwma_select_shipping').find('option[value=' + defaultShipAddress + ']').attr('selected', 'selected');
			$('.ocwma_select_shipping')
				.val(defaultShipAddress)
				.trigger('change');
		}, 500);

		var defaultBillAddress = "<?php echo $user_bill_address_default; ?>";
		setTimeout(function() {
			$('.ocwma_select').find('option[value=' + defaultBillAddress + ']').attr('selected', 'selected');
			$('.ocwma_select')
				.val(defaultBillAddress)
				.trigger('change');
		}, 500);
	});
</script>
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

	<?php if ($checkout->get_checkout_fields()) : ?>

		<?php do_action('woocommerce_checkout_before_customer_details'); ?>

		<div class="col2-set row" id="customer_details">
			<div class="col-md-8">
				<?php //do_action('woocommerce_checkout_billing'); 
				?>
				<?php do_action('woocommerce_checkout_shipping'); ?>
				<?php //do_action('woocommerce_checkout_shipping'); 
				?>

				<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

					<?php do_action('woocommerce_review_order_before_shipping'); ?>
					<div class="shop_table woocommerce-checkout-review-order-table-custom">
						<?php wc_cart_totals_shipping_html(); ?>
					</div>
					<?php do_action('woocommerce_review_order_after_shipping'); ?>

				<?php endif; ?>
				<div class="payment_section step_form">
					<h3>PAYMENT:</h3>
					<?php do_action('custom_woocommerce_checkout_after_shipping'); ?>
				</div>
				<?php do_action('woocommerce_checkout_billing'); ?>

				<?php //Additional Field 
				?>
				<div class="woocommerce-additional-fields step_form">
					<?php do_action('woocommerce_before_order_notes', $checkout); ?>

					<?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

						<?php if (!WC()->cart->needs_shipping() || wc_ship_to_billing_address_only()) : ?>

							<h3><?php esc_html_e('Additional information', 'woocommerce'); ?></h3>

						<?php endif; ?>

						<div class="woocommerce-additional-fields__field-wrapper">
							<?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
								<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
							<?php endforeach; ?>
						</div>

					<?php endif; ?>

					<?php do_action('woocommerce_after_order_notes', $checkout); ?>
				</div>
				<?php //Additional Field End 
				?>


			</div>
			<div class="col-md-4">
				<div class="sticky-top">				
				<?php // Place Order Button 
				?>
				<?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
				<?php /* ?>				
				<h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
				<?php */ ?>
				<?php do_action('woocommerce_checkout_before_order_review'); ?>
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action('woocommerce_checkout_order_review'); ?>
				</div>
				<?php do_action('woocommerce_checkout_after_order_review'); ?>
				<?php echo '<div class="place-order">'; ?>
				<?php do_action('woocommerce_review_order_before_submit'); ?>
				<?php echo '<p>Placing your order, you agree. privacy notice and conditions of use. </p>' ?>
				<?php $order_button_text = "PLACE ORDER"; ?>
				<?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine 
				?>
				<?php do_action('woocommerce_review_order_after_submit');  ?>
				
				<?php echo '</div>'; ?>
				<?php // Place Order Button End?>
				</div>			
			</div>

		</div>

		<?php do_action('woocommerce_checkout_after_customer_details'); ?>

	<?php endif; ?>
</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>