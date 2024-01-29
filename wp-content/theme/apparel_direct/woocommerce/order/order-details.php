<?php

/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined('ABSPATH') || exit;

$order = wc_get_order($order_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if (!$order) {
	return;
}

$order_items           = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note    = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

$order_refunds = $order->get_refunds();
$o_status =  wc_get_order_status_name($order->get_status());
?>

<?php


if ($show_downloads) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<?php
if ($show_customer_details) {
	if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
		wc_get_template('order/order-details-customer.php', array('order' => $order));
	}
}

?>
<section class="woocommerce-order-details">
	<?php do_action('woocommerce_order_details_before_order_table', $order); ?>
	<?php
	if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) { ?>

		<div class="step_form">


			<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

				<?php  ?>
				<thead>
					<tr>
						<th class="woocommerce-table__product-name product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
						<th class="woocommerce-table__product-table product-total"></th>
						<th class="woocommerce-table__product-table product-total"></th>
						<th class="woocommerce-table__product-table product-total"><?php esc_html_e('Total', 'woocommerce'); ?></th>
					</tr>
				</thead>
				<?php  ?>
				<tbody>
					<?php
					do_action('woocommerce_order_details_before_order_table_items', $order);

					foreach ($order_items as $item_id => $item) {
						$product = $item->get_product();

						wc_get_template(
							'order/order-details-item.php',
							array(
								'order'              => $order,
								'item_id'            => $item_id,
								'item'               => $item,
								'show_purchase_note' => $show_purchase_note,
								'purchase_note'      => $product ? $product->get_purchase_note() : '',
								'product'            => $product,
							)
						);
					}

					do_action('woocommerce_order_details_after_order_table_items', $order);
					?>
				</tbody>
			</table>

		</div>

	<?php } elseif ($o_status == 'Partially Shipped') {

		$ast = AST_Pro_Actions::get_instance();

		$product_list = array();
		$tracking_items = $ast->get_tracking_items($order_id);
		
		foreach ($tracking_items as $tracking_item) {
			if (isset($tracking_item['products_list'])) {
				$product_list[] = $tracking_item['products_list'];
			}
		}

		$all_list = array();
		foreach ($product_list as $list) {
			if (!empty($list)) {
				foreach ((array) $list as $in_list) {
					if (isset($in_list->item_id)) {
						if (isset($all_list[$in_list->item_id])) {
							$all_list[$in_list->item_id] = (int) $all_list[$in_list->item_id] + (int) $in_list->qty;
						} else {
							$all_list[$in_list->item_id] = $in_list->qty;
						}
					} else {
						if (isset($all_list[$in_list->product])) {
							$all_list[$in_list->product] = (int) $all_list[$in_list->product] + (int) $in_list->qty;
						} else {
							$all_list[$in_list->product] = $in_list->qty;
						}
					}
				}
			}
		}


	?>
		<div class="woocommerce_order_details_custom">
			<div class="shipment_head shipment_head_default">
				<div class="shipment_head_order">Order</div>
				<div class="shipment_head_staus">Status</div>
				<div class="shipment_head_action">Action</div>
			</div>
			<div class="step_form panel panel-default">
				<div data-toggle="collapse" data-target="#collapse_a1" class="shipment_items_head panel-title">
					<div class="shipment_num">
						Shipment </div>
					<div class="shipment_status">
						<?php
						if ($o_status == 'Partially Shipped') {
							echo 'Processing';
						} else {
							echo $o_status;
						}
						?>

					</div>
					<div class="track_shipment">

					</div>
				</div>
				<div id="collapse_a1" class="panel-collapse collapse">

					<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
						<tbody>
							<?php
							
							
							foreach ($order_items as $item_id => $item) {
			
								$product = $item->get_product();
								$qty = $item->get_quantity();

								$variation_id = $item->get_variation_id();
								$product_id = $item->get_product_id();

								if (0 != $variation_id) {
									$product_id = $variation_id;
								}

								if (array_key_exists($product_id, $all_list)) {
									if (isset($all_list[$product_id])) {
										$qty = (int) $item->get_quantity() - (int) $all_list[$product_id];
										
									}
								}

								if (array_key_exists($item_id, $all_list)) {
									if (isset($all_list[$item_id])) {
										$qty = (int) $item->get_quantity() - (int) $all_list[$item_id];
									}
								}

								$qty = ($qty < 0) ? 0 : $qty;
								$disable_row = (0 == $qty) ? 'd-none' : '';

							?>
								<tr class="ASTProduct_row <?php esc_html_e($disable_row); ?>">
									<td class="order_item_img">
										<?php
										
										if ($product) {
											
											$var_image_id = $product->get_image_id();
											
											$product_id_p = wp_get_post_parent_id($product_id);
											$var_image_url =  wp_get_attachment_url($var_image_id);

											//custom product title
											$product_title = get_the_title($product_id_p);
											$product_style = get_post_meta($product_id_p, 'product_style', true);
											
											$variation_attributes = $product->get_variation_attributes();

											$variation_color = woo2_helper_attribute_name('pa_color', $variation_attributes['attribute_pa_color']);
											$variation_size = woo2_helper_attribute_name('pa_size', $variation_attributes['attribute_pa_size']);

											$product_name_full = $product_title . ' - ' . $product_style . ' - ' . $variation_size . ' - ' . $variation_color;


											if (strpos($var_image_url, '_sw') == true) {
												$var_image_url = str_replace("_sw", "-100x100", $var_image_url);
											}

											if (strpos($var_image_url, 'EBY_') == true) {
												$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
											}

											if (@getimagesize($var_image_url)) {
												$thumbnail = '<img src="' . $var_image_url . '">';
											} else {
												$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id_p), array(100, 100));
												$thumbnail = '<img src="' . $image_array[0] . '">';
											}

											if ($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327) {
												$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id_p), array(100, 100));
												$thumbnail = '<img class="c_im" src="' . $image_coming_soon[0] . '">';
											}
											echo $thumbnail;
										} else {
											if (wc_placeholder_img_src(array(100, 100))) {
												echo '<img class="c_im" src="' . wc_placeholder_img_src(array(100, 100)) . '">';
											}
										}

										?>

									</td>
									<td>
										<?php
										if ($product_name_full) {
											echo '<span>' . $product_name_full . '</span>';
										} else {
											echo '<span>' . $item->get_name() . '</span>';
										}
										//esc_html_e($item->get_name());
										?>

									</td>
									<td>x <?php esc_html_e( $qty ); ?></td>
									<td>
										<?php
										if($item->get_variation_id()){
											$var_price = get_post_meta($item->get_variation_id(), '_regular_price', true);
										}
										echo wc_price($var_price*abs($qty));
										?>
									</td>
								</tr>
							<?php 
							} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>


		<?php } else {


		if ($o_status != 'Shipped') {
		?>
			<div class="woocommerce_order_details_custom">
				<div class="shipment_head shipment_head_default">
					<div class="shipment_head_order">Order</div>
					<div class="shipment_head_staus">Status</div>
					<div class="shipment_head_action">Action</div>
				</div>
				<div class="step_form panel panel-default">
					<div data-toggle="collapse" data-target="#collapse_a1" class="shipment_items_head panel-title">
						<div class="shipment_num">
							Shipment </div>
						<div class="shipment_status">
							<?php
							if ($o_status == 'Partially Shipped') {
								echo 'Processing';
							} else {
								echo $o_status;
							}
							?>

						</div>
						<div class="track_shipment">

						</div>
					</div>
					<div id="collapse_a1" class="panel-collapse collapse">
						<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

							<?php /* ?>
		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<?php */ ?>
							<tbody>
								<?php
								do_action('woocommerce_order_details_before_order_table_items', $order);

								foreach ($order_items as $item_id => $item) {
									$product = $item->get_product();

									wc_get_template(
										'order/order-details-item.php',
										array(
											'order'              => $order,
											'item_id'            => $item_id,
											'item'               => $item,
											'show_purchase_note' => $show_purchase_note,
											'purchase_note'      => $product ? $product->get_purchase_note() : '',
											'product'            => $product,
										)
									);
								}

								do_action('woocommerce_order_details_after_order_table_items', $order);
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
	<?php }
	}
	?>

	<?php

	$total_refund_items = 0;
	if ($order_refunds && $o_status == 'Shipped') {

		foreach ($order_refunds as $refund_check) {
			$total_refund_items += sizeof($refund_check->get_items());
		}

		if ($total_refund_items > 0) {
	?>
			<section class="woocommerce-order-details">

				<div class="step_form pt-0">

					<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

						<?php  ?>
						<thead>
							<tr>
								<th colspan="2" class="woocommerce-table__product-name product-name"><?php esc_html_e('Refund of product', 'woocommerce'); ?></th>
								<th class="woocommerce-table__product-table product-total"></th>
								<th class="woocommerce-table__product-table product-total"><?php //esc_html_e('Total', 'woocommerce'); 
																							?></th>
							</tr>
						</thead>
						<?php  ?>
						<tbody>
							<?php
							//do_action('woocommerce_order_details_before_order_table_items', $order);

							foreach ($order_refunds as $refund) {
								// Loop through the order refund line items
								foreach ($refund->get_items() as $item_id => $item) {
									$product = $item->get_product();

									wc_get_template(
										'order/order-details-item_return.php',
										array(
											'order'              => $order,
											'item_id'            => $item_id,
											'item'               => $item,
											'show_purchase_note' => $show_purchase_note,
											'purchase_note'      => $product ? $product->get_purchase_note() : '',
											'product'            => $product,
										)
									);
								}
							}
							//do_action('woocommerce_order_details_after_order_table_items', $order);
							?>
						</tbody>
					</table>

				</div>

			</section>
	<?php  }
	}
	?>

	<div class="order_detail_total_section step_form">
		<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

			<?php
			foreach ($order->get_order_item_totals() as $key => $total) {

				if ($total['value'] == 'USAePay') {
					$total['value'] = 'Credit Card';
				}
			?>
				<tr>
					<th scope="row"><?php echo esc_html($total['label']); ?></th>
					<td><?php echo ('payment_method' === $key) ? esc_html($total['value']) : wp_kses_post($total['value']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?></td>
				</tr>
			<?php
			}
			?>
			<?php if ($order->get_customer_note()) : ?>
				<tr>
					<th><?php esc_html_e('Note:', 'woocommerce'); ?></th>
					<td><?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?></td>
				</tr>
			<?php endif; ?>
		</table>
	</div>

	<?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action('woocommerce_after_order_details', $order);
