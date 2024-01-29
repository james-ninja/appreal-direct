<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>
<?php do_action('wpo_wcpdf_before_document', $this->type, $this->order); ?>

<table class="head container">
	<tr>
		<td class="header">
			<?php
			if ($this->has_header_logo()) {
				$this->header_logo();
			} else {
				echo $this->get_title();
			}
			?>
		</td>
		<td class="shop-info">
			<?php do_action('wpo_wcpdf_before_shop_name', $this->type, $this->order); ?>
			<div class="shop-name">
				<h3><?php $this->shop_name(); ?></h3>
			</div>
			<?php do_action('wpo_wcpdf_after_shop_name', $this->type, $this->order); ?>
			<?php do_action('wpo_wcpdf_before_shop_address', $this->type, $this->order); ?>
			<div class="shop-address"><?php $this->shop_address(); ?></div>
			<?php do_action('wpo_wcpdf_after_shop_address', $this->type, $this->order); ?>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
	<?php if ($this->has_header_logo()) echo $this->get_title(); ?>
</h1>

<?php do_action('wpo_wcpdf_after_document_label', $this->type, $this->order); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<!-- <h3><?php _e('Billing Address:', 'woocommerce-pdf-invoices-packing-slips'); ?></h3> -->
			<?php do_action('wpo_wcpdf_before_billing_address', $this->type, $this->order); ?>
			<?php $this->billing_address(); ?>
			<?php do_action('wpo_wcpdf_after_billing_address', $this->type, $this->order); ?>
			<?php if (isset($this->settings['display_email'])) { ?>
				<div class="billing-email"><?php $this->billing_email(); ?></div>
			<?php } ?>
			<?php if (isset($this->settings['display_phone'])) { ?>
				<div class="billing-phone"><?php $this->billing_phone(); ?></div>
			<?php } ?>
		</td>
		<td class="address shipping-address">
			<?php if (!empty($this->settings['display_shipping_address']) && ($this->ships_to_different_address() || $this->settings['display_shipping_address'] == 'always')) { ?>
				<h3><?php _e('Ship To:', 'woocommerce-pdf-invoices-packing-slips'); ?></h3>
				<?php do_action('wpo_wcpdf_before_shipping_address', $this->type, $this->order); ?>
				<?php $this->shipping_address(); ?>
				<?php do_action('wpo_wcpdf_after_shipping_address', $this->type, $this->order); ?>
			<?php } ?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action('wpo_wcpdf_before_order_data', $this->type, $this->order); ?>
				<?php if (isset($this->settings['display_number'])) { ?>
					<tr class="invoice-number">
						<th><?php echo $this->get_number_title(); ?></th>
						<td><?php $this->invoice_number(); ?></td>
					</tr>
				<?php } ?>
				<?php if (isset($this->settings['display_date'])) { ?>
					<tr class="invoice-date">
						<th><?php echo $this->get_date_title(); ?></th>
						<td><?php $this->invoice_date(); ?></td>
					</tr>
				<?php } ?>
				<tr class="order-number">
					<th><?php _e('Order Number:', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
					<td><?php $this->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e('Order Date:', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
					<td><?php $this->order_date(); ?></td>
				</tr>
				<tr class="payment-method">
					<th><?php _e('Payment Method:', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
					<td><?php
						$payment_m = get_post_meta($order->id, '_payment_method', true);
						if ($payment_m == 'usaepaytransapi') {
							echo 'Credit Card';
						} else {
							$this->payment_method();
						}
						?></td>
				</tr>
				<?php do_action('wpo_wcpdf_after_order_data', $this->type, $this->order); ?>
			</table>
		</td>
	</tr>
</table>

<?php //do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); 
?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e('Product', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
			<th class="quantity"><?php _e('Quantity', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
			<th class="price"><?php _e('Price', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $items = $this->get_order_items();
		if (sizeof($items) > 0) : foreach ($items as $item_id => $item) : ?>
				<tr class="<?php echo apply_filters('wpo_wcpdf_item_row_class', 'item-' . $item_id, $this->type, $this->order, $item_id); ?>">
					<td class="product">
						<?php $description_label = __('Description', 'woocommerce-pdf-invoices-packing-slips'); // registering alternate label translation 
						$product_style = get_post_meta($item['product_id'], 'product_style', true);
						$upc_field = get_post_meta($item['variation_id'], 'upc_field', true);
						$variation = wc_get_product($item['variation_id']);
						
						$product_title = get_the_title($item['product_id']);
						if($variation){
							$variation_attributes = $variation->get_variation_attributes();
							$variation_color = woo2_helper_attribute_name('pa_color', $variation_attributes['attribute_pa_color']);
							$variation_size = woo2_helper_attribute_name('pa_size', $variation_attributes['attribute_pa_size']);
							$product_full_name = $product_title.' - '.$product_style.' - '.$variation_size.' - '.$variation_color;
						}else{
							$product_full_name = $item['name'];
						}

						?>
						<span class="item-name"><?php 
						//echo $item['name']; 
						echo $product_full_name;
						?></span>
						<?php do_action('wpo_wcpdf_before_item_meta', $this->type, $item, $this->order); ?>
						<span class="item-meta"><?php // echo $item['meta']; ?></span>
						<dl class="meta">
							<?php $description_label = __('SKU', 'woocommerce-pdf-invoices-packing-slips'); // registering alternate label translation 
							?>
							<?php if (!empty($upc_field)) : ?><dt class="UPC"><?php _e('UPC :', 'woocommerce-pdf-invoices-packing-slips'); ?></dt>
								<dd class="UPC"><?php echo $upc_field ?></dd><?php endif; ?>
							<?php /* if( !empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; */ ?>
							<?php /* if( !empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; */ ?>
						</dl>
						<?php  ?>
						<?php do_action('wpo_wcpdf_after_item_meta', $this->type, $item, $this->order); ?>
					</td>
					<td class="quantity"><?php echo $item['quantity']; ?></td>
					<td class="price"><?php echo $item['order_price']; ?></td>
				</tr>
		<?php endforeach;
		endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="document-notes">
					<?php do_action('wpo_wcpdf_before_document_notes', $this->type, $this->order); ?>
					<?php if ($this->get_document_notes()) : ?>
						<h3><?php _e('Notes', 'woocommerce-pdf-invoices-packing-slips'); ?></h3>
						<?php $this->document_notes(); ?>
					<?php endif; ?>
					<?php do_action('wpo_wcpdf_after_document_notes', $this->type, $this->order); ?>
				</div>
				<div class="customer-notes">
					<?php do_action('wpo_wcpdf_before_customer_notes', $this->type, $this->order); ?>
					<?php if ($this->get_shipping_notes()) : ?>
						<h3><?php _e('Customer Notes', 'woocommerce-pdf-invoices-packing-slips'); ?></h3>
						<?php $this->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action('wpo_wcpdf_after_customer_notes', $this->type, $this->order); ?>
				</div>
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
					<?php
					//custom for refund
						foreach ($order->get_order_item_totals() as $key => $total) {

							if ($total['value'] == 'USAePay') {
								$total['value'] = 'Credit Card';
							}
						?>
							<tr class="<?php echo $key; ?>">
								<th scope="row"><?php echo esc_html($total['label']); ?></th>
								<td><?php echo ('payment_method' === $key) ? esc_html($total['value']) : wp_kses_post($total['value']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
									?></td>
							</tr>
						<?php
						}
						//custom for refund
					?>
						<?php /* foreach ($this->get_woocommerce_totals() as $key => $total) : ?>
							<tr class="<?php echo $key; ?>">
								<th class="description"><?php echo $total['label']; ?></th>
								<td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
							</tr>
						<?php endforeach; */?>
					</tfoot>
				</table>
			</td>
		</tr>
	</tfoot>
</table>

<div class="bottom-spacer"></div>

<?php do_action('wpo_wcpdf_after_order_details', $this->type, $this->order); ?>

<?php if ($this->get_footer()) : ?>
	<div id="footer">
		<!-- hook available: wpo_wcpdf_before_footer -->
		<?php $this->footer(); ?>
		<!-- hook available: wpo_wcpdf_after_footer -->
	</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action('wpo_wcpdf_after_document', $this->type, $this->order); ?>