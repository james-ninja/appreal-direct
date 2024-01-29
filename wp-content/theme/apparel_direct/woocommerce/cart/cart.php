<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>
<p class="error empty_select_item">Please select cart items.</p>
<?php
echo '<div class="empty_cart_box">';
echo '<a href="javascript:void(0)" class="empty_cart_link_all" title="' . esc_attr('Empty Entire Cart', 'woocommerce') . '">' . esc_html('Empty Entire Cart', 'woocommerce') . '</a>';
echo '<a href="javascript:void(0)" class="empty_cart_link" title="' . esc_attr('Remove Selected Items', 'woocommerce') . '">' . esc_html('Remove Selected Items', 'woocommerce') . '</a>';
echo '</div>';
?>
<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
	<?php do_action('woocommerce_before_cart_table');
	//echo '<a href="' . esc_url( add_query_arg( 'empty_cart', 'yes' ) ) . '" class="empty_cart_link" title="' . esc_attr( 'Empty Cart', 'woocommerce' ) . '">' . esc_html( 'Empty Cart', 'woocommerce' ) . '</a>';
	//echo '<a href="javascript:void(0)" class="empty_cart_link" title="' . esc_attr( 'Empty Cart', 'woocommerce' ) . '">' . esc_html( 'Empty Cart', 'woocommerce' ) . '</a>';
	?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<?php /* ?>
				<th class="product-remove">&nbsp;</th>
				<?php */ ?>
				<th class="product-thumbnail">
					<label class="checkbox">
						<input type="checkbox" class="checkbox-input rm_items_chk_all" name="rm_items_all" value="">
						<span class="checkbox-checkmark-box">
							<span class="checkbox-checkmark"></span>
						</span>
					</label>
				</th>
				<th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
				<th class="product-data">&nbsp;</th>
				<th class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
				<th class="product-price"><?php esc_html_e('Price Per Unit', 'woocommerce'); ?></th>
				<th class="product-subtotal"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action('woocommerce_before_cart_contents'); ?>

			<?php
			$index = 1;
			$total_cart_items = count(WC()->cart->get_cart());
			$total_cart_items_more = $total_cart_items - 4;
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

				if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
					$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
			?>
					<?php if ($index > 4) {
						$hide_class = 'd-none';
					} ?>
					<tr class="<?php echo $hide_class; ?> woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
						<?php /* ?>
						<td class="product-remove">
							<?php
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
									esc_url(wc_get_cart_remove_url($cart_item_key)),
									esc_html__('Remove this item', 'woocommerce'),
									esc_attr($product_id),
									esc_attr($_product->get_sku())
								),
								$cart_item_key
							);
							?>
						</td>
						<?php */ ?>
						<td class="product-thumbnail">

							<label class="checkbox">

								<input type="checkbox" class="checkbox-input rm_items_chk" name="rm_items" value="<?php echo $cart_item['data']->get_id(); ?>">
								<span class="checkbox-checkmark-box">
									<span class="checkbox-checkmark"></span>
								</span>
							</label>

							<?php
							$variation_id = $cart_item['variation_id'];	
							 
							$variation_image = get_post_meta($variation_id,'aws_url_field', true);
						
							/*$product = new WC_product($product_id);
							$attachment_ids = $product->get_gallery_image_ids();
							    $attachment_ids = $product->get_gallery_image_ids();

								foreach( $attachment_ids as $attachment_id ) {
									echo $image_link = wp_get_attachment_url( $attachment_id );
								}*/
							if ($variation_image) {
								$resized_image = aq_resize($variation_image, 413, 362, $crop);

								
								$thumbnail = '<a href="'.$_product->get_permalink($cart_item).'"><img src="' . $variation_image. '" class="attachment-thumbnail" width="128" height="128" /></a>';
								echo $thumbnail;
							}
							else { 
							$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);


							$var_image_id = $_product->get_image_id();
							$var_image_url =  wp_get_attachment_url($var_image_id);

							if (strpos($var_image_url, '_sw') == true) {
								$var_image_url = str_replace("_sw", "-300x300", $var_image_url);
							}

							if (strpos($var_image_url, 'EBY_') == true) {
								$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
							}

							if (@getimagesize($var_image_url)) {
								$thumbnail = '<img src="' . $var_image_url . '">';
							} else {
								$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
								$thumbnail = '<img src="' . $image_array[0] . '">';
							}

							if ($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327) {
								$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
								$thumbnail = '<img src="' . $image_coming_soon[0] . '">';
							}

							if (!$product_permalink) {
								echo $thumbnail; // PHPCS: XSS ok.
							} else {
								printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
							}
							}
							?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
							<?php
							if (!$product_permalink) {
								echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
							} else {
								echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
							}
							echo '<div class="product_remove_savelater">';
							echo sprintf(
								'<div class="product-remove"><a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">Remove</a></div>',
								esc_url(wc_get_cart_remove_url($cart_item_key)),
								esc_html__('Remove this item', 'woocommerce'),
								esc_attr($product_id),
								esc_attr($_product->get_sku())
							);
							echo '<div class="save_for_later_link">';
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								'',
								$cart_item_key
							);
							echo '</div>';
							echo '</div>';

							do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

							// Meta data.
							echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

							// Backorder notification.
							if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
								echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
							}
							?>
						</td>
						<td class="product-variation-data">
							<?php
							$product_style = get_post_meta($product_id, 'product_style', true);
							if ($product_style) {
								echo '<div><span>Style:</span> ' . $product_style . '</div>';
							}
							
							if ($cart_item['variation']['attribute_pa_size']) {
								echo '<div><span>Size:</span> ' . strtoupper($cart_item['variation']['attribute_pa_size']) . '</div>';
							}

							if ($cart_item['variation']['attribute_pa_color']) {
								$pa_color = $cart_item['variation']['attribute_pa_color'];
								$pa_color = str_replace("-", " ", $pa_color);
								echo '<div><span>Color:</span> ' . ucwords($pa_color) . '</div>';
							}

							$upc_field = get_post_meta($cart_item['data']->get_id(), 'upc_field', true);
							if ($upc_field) {
								echo '<div><span>UPC:</span> ' . $upc_field . '</div>';
							}

							
							//	echo '<pre>';
							//	print_r($cart_item);
							//	echo '</pre>';

							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
							<?php
							if ($_product->is_sold_individually()) {
								$product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
							} else {
								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $_product->get_max_purchase_quantity(),
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									),
									$_product,
									false
								);
							}

							echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
							<?php
							echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
							<?php
							echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
							?>
						</td>
					</tr>
			<?php
				}
				$index++;
			}
			?>

			<?php do_action('woocommerce_cart_contents'); ?>

			<tr class="d-none cart_bottom_footer">
				<td colspan="6" class="actions">

					<?php if (wc_coupons_enabled()) { ?>
						<div class="coupon">
							<label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
							<?php do_action('woocommerce_cart_coupon'); ?>
						</div>
					<?php } ?>
					<?php 
					$hidecarttotal=WC()->cart->get_cart_subtotal(); ?>
					<input type="hidden" class="carttotal" value="<?php echo strip_tags($hidecarttotal); ?>" />
					<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

					<?php do_action('woocommerce_cart_actions'); ?>

					<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
				</td>
			</tr>

			<?php do_action('woocommerce_after_cart_contents'); ?>
		</tbody>
	</table>
	<?php if ($total_cart_items > 4) { ?>
		<div class="more-cart text-center">
			<button type="button" class="btn btn-secondary view-more-cart">View <?php echo $total_cart_items_more; ?> More Items</button>
		</div>
	<?php } ?>
	<?php do_action('woocommerce_after_cart_table'); ?>
</form>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

<div class="cart-collaterals">
	<?php
	/**
	 * Cart collaterals hook.
	 *
	 * @hooked woocommerce_cross_sell_display
	 * @hooked woocommerce_cart_totals - 10
	 */
	//do_action('woocommerce_cart_collaterals');
	?>

</div>

<?php do_action('woocommerce_after_cart'); ?>
<script>
	jQuery(document).ready(function() {
		jQuery('.empty_cart_link').hide();

		jQuery(".woocommerce").on("click", 'input[name="rm_items"]', function() {
			if (jQuery('input[name="rm_items"]:checkbox:checked').length > 0) {
				jQuery('.empty_cart_link').show();
			} else {
				jQuery('.empty_cart_link').hide();
			}
		});

		jQuery(".woocommerce").on("click", 'input[name="rm_items_all"]', function() {
			if (jQuery(this).is(":checked")) {
				jQuery('.empty_cart_link').show();
			} else if (jQuery(this).is(":not(:checked)")) {
				jQuery('.empty_cart_link').hide();
			}
		});

	});
	//Add to cart

	//jQuery(".empty_cart_link").on("click", function () {
	jQuery(".woocommerce").on("click", ".empty_cart_link", function() {
		var selecteditems = new Array();
		jQuery('input[name="rm_items"]:checked').each(function() {
			selecteditems.push(this.value);
		});

		if (selecteditems.length == 0) {
			jQuery('.empty_select_item').show();
			return false;
		} else {
			jQuery('.empty_select_item').hide();
		}
		console.log(selecteditems);
		event.preventDefault();
		var data = {
			'action': 'ad_woocommerce_remove_cart_items',
			'data_form': selecteditems,
		};
		jQuery.ajax({
			url: custom.ajaxurl,
			data: data,
			type: 'POST',
			beforeSend: function(xhr) {
				jQuery('.woocommerce-cart-form .shop_table').addClass('blockUI blockOverlay');
			},
			success: function(response) {
				jQuery('.woocommerce-cart-form .shop_table').removeClass('blockUI blockOverlay');
				if (response['success'] == true) {
					var carttotal = $('.carttotal').val();
					$(document.body).trigger('wc_fragment_refresh');
					jQuery("[name='update_cart']").prop("disabled", false);
					jQuery("[name='update_cart']").trigger("click");
					jQuery('.empty_cart_link').hide();
					jQuery('.woocommerce-message').remove();
					$('.cart_total_custom .woocommerce-Price-amount').text(carttotal);
					 setTimeout(function(){// wait for 5 secs(2)
					   location.reload(); // then reload the page.(3)
				  }, 3000); 
				} else {
					responsehtml = '<div class="woocommerce-error" role="alert">Looks like there was an error. Please try again.</div>';
					jQuery(".woocommerce-notices-wrapper").html(responsehtml);
				}


			}
		});

	});

	jQuery(".woocommerce").on("click", ".empty_cart_link_all", function() {

		event.preventDefault();
		var data = {
			'action': 'ad_woocommerce_remove_cart_items_all',
			'data_form': 'remove_all',
		};
		jQuery.ajax({
			url: custom.ajaxurl,
			data: data,
			type: 'POST',
			beforeSend: function(xhr) {
				jQuery('.woocommerce-cart-form .shop_table').addClass('blockUI blockOverlay');
			},
			success: function(response) {
				jQuery('.woocommerce-cart-form .shop_table').removeClass('blockUI blockOverlay');
				if (response['success'] == true) {
					var carttotal = $('.carttotal').val();
					$(document.body).trigger('wc_fragment_refresh');
					jQuery("[name='update_cart']").prop("disabled", false);
					jQuery("[name='update_cart']").trigger("click");
					jQuery('.empty_cart_link').hide();
					jQuery('.woocommerce-message').remove();
					
					$('.cart_total_custom .woocommerce-Price-amount').text(carttotal);
					setTimeout(function(){// wait for 5 secs(2)
					   location.reload(); // then reload the page.(3)
				  }, 3000); 
				} else {
					responsehtml = '<div class="woocommerce-error" role="alert">Looks like there was an error. Please try again.</div>';
					jQuery(".woocommerce-notices-wrapper").html(responsehtml);
				}

			}
		});

	});
</script>