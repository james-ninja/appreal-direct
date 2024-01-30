<?php

/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
	return;
}
?>
<tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">
	<td class="order_item_img">
		<?php
		if ($product) {
			//$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $product->get_image(), $cart_item, $cart_item_key);

			$var_image_id = $product->get_image_id();

			$product_id = wp_get_post_parent_id($product->get_id());
			$variation_id = $item->get_variation_id();
			$var_image_url =  wp_get_attachment_url($var_image_id);

			//custom product title
			$product_title = get_the_title($product_id);
			$product_style = get_post_meta($product_id, 'product_style', true);
			$upc_field = get_post_meta($product->get_id(), 'upc_field', true);

			$variation_attributes = $product->get_variation_attributes();

			$variation_color = woo2_helper_attribute_name('pa_color', $variation_attributes['attribute_pa_color']);
			$variation_size = woo2_helper_attribute_name('pa_size', $variation_attributes['attribute_pa_size']);

			$product_name_full = $product_title . ' - ' . $product_style . ' - ' . $variation_size . ' - ' . $variation_color;

			$aws_var_image = get_post_meta($variation_id,'aws_url_field', true);

			if($aws_var_image){
				$thumbnail = '<img class="c_im" src="' . $aws_var_image . '">';
			} else {
				if (strpos($var_image_url, '_sw') == true) {
				$var_image_url = str_replace("_sw", "-100x100", $var_image_url);
				}

				if (strpos($var_image_url, 'EBY_') == true) {
					$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
				}

				if (@getimagesize($var_image_url)) {
					$thumbnail = '<img src="' . $var_image_url . '">';
				} else {
					$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), array(100, 100));
					$thumbnail = '<img src="' . $image_array[0] . '">';
				}

				if ($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327) {
					$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), array(100, 100));
					$thumbnail = '<img class="c_im" src="' . $image_coming_soon[0] . '">';
				}
			}

			
			echo $thumbnail;
		} else {
			if (wc_placeholder_img_src(array(100, 100))) {
				echo '<img class="c_im" src="' . wc_placeholder_img_src(array(100, 100)) . '">';
			}
		}

		?>

	</td>
	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);

		//echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) );
		//echo '<span>'.$item->get_name().'</span>';
		if ($product_name_full) {
			echo '<span>' . $product_name_full . '</span>';
		} else {
			echo '<span>' . $item->get_name() . '</span>';
		}
		// echo '<pre>';
		// print_r($item);
		 // echo '</pre>';

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item($item_id);

		if ($refunded_qty) {
			$qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
		} else {
			$qty_display = esc_html($qty);
		}

		//echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

		//wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$item_meta_array = array();
		foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {
			$value = wp_kses_post(make_clickable(trim(strip_tags($meta->display_value))));
			$item_meta_array[] = $value;
		}

		//print_r($item_meta_array);
		do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
		?>
	</td>
	<td>
		<?php
		echo apply_filters('woocommerce_order_item_quantity_html', ' <span class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</span>', $item);
		?>

	</td>
	<td class="woocommerce-table__product-total product-total">
		<?php echo $order->get_formatted_line_subtotal($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
		?>
	</td>

</tr>

<?php if ($show_purchase_note && $purchase_note) : ?>

	<tr class="woocommerce-table__product-purchase-note product-purchase-note">

		<td colspan="2"><?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?></td>

	</tr>

<?php endif; ?>