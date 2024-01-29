<?php
//get save cart later content
function get_savecartlater_content()
{
	ob_start();
?>
	<!DOCTYPE html>
	<html <?php language_attributes(); ?>>

	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>Save For Later</title>
		<style>
			* {
				font-family: sans-serif;
				color: #333;
			}

			h1 {
				font-size: 20px;
				margin-bottom: 0;
			}

			h2 {
				font-size: 40px;
				text-align: center;
				margin-bottom: 50px;
			}

			table {
				width: 100%;
				border-spacing: 0;
				border-radius: 5px;
			}

			table tbody tr:nth-child(even) {
				background-color: #f9f9f9;
			}

			table tr td,
			table tr th {
				padding: 15px;
			}

			table tr th {
				border-bottom: 2px solid #ebebeb;
			}

			/*table tr {
				text-align: center;
			}*/
			.heading {
				text-align: center;
				margin-bottom: 15px;
			}

			.saveforlater_total_data {
				float: right;
				background: #ef4344;
				padding: 10px 15px;
				color: #ffffff;
				font-weight: 600;
				margin-top: 15px;
			}

			.saveforlater_total_data bdi,
			.woocommerce-Price-currencySymbol {
				color: #ffffff;
			}
		</style>
	</head>

	<body>
		<div class="heading">
			<div id="logo">
				<?php
				$logo = get_theme_mod('custom_logo');
				$image = wp_get_attachment_image_src($logo, 'full');
				$image_url = $image[0];
				if ($logo) {
				?>
					<a target="_blank" href="<?php echo home_url(); ?>"><img src="<?php echo $image_url; ?>"></a>
				<?php } ?>
			</div>
		</div>
		<?php get_save_cart_html(); ?>
	</body>

	</html>
	<?php
	return ob_get_clean();
}

// Get share pdf link
function share_list_get_pdf()
{
	if (!class_exists('Dompdf\Dompdf')) {
		include(YITH_WCWL_DIR . 'vendor/autoload.php');
	}

	// send nocache headers.
	nocache_headers();
	//generate pdf;
	$dompdf = new Dompdf\Dompdf();
	$dompdf->loadHtml(get_savecartlater_content());
	$dompdf->set_option('isRemoteEnabled', TRUE);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$filename = uniqid(rand(), true) . '.pdf';
	$saveforlater_path = ABSPATH . 'savecartlater/' . $filename;

	$share_file_url = home_url('/savecartlater/') . $filename;
	echo esc_url($share_file_url);
	$output = $dompdf->output();
	file_put_contents($saveforlater_path, $output);
	//die();
}
function get_save_cart_html()
{

	$allowed_html = array();
	global $current_user;
	$user_id = $current_user->ID;
	if (is_user_logged_in()) {

		$mwb_woo_smc_get_save_later_product = get_user_meta($user_id, 'mwb_woo_smc_logged_in_user_data', true);
		if (empty($mwb_woo_smc_get_save_later_product)) {
			echo '<div class="product_not_found"><h4>Product Not availabe  in Save For Later.</h4></div>';
		}
	} elseif (isset($_COOKIE['mwb_woo_smc_save_guest_user_data'])) {
		$mwb_woo_smc_get_save_later_product = unserialize(base64_decode(map_deep(wp_unslash($_COOKIE['mwb_woo_smc_save_guest_user_data']), 'sanitize_text_field'))); // @codingStandardsIgnoreEnd
	}
	$days_left = 0;
	$time_left_text = '';
	if (isset($_COOKIE['mwb_woo_smc_save_guest_user_expiry'])) {
		$mwb_woo_smc_save_guest_user_expiry = map_deep(wp_unslash($_COOKIE['mwb_woo_smc_save_guest_user_expiry']), 'sanitize_text_field');
		$days_left = (int) (((int) $mwb_woo_smc_save_guest_user_expiry - time()) / 86400);
	}
	if (isset($days_left) && !empty($days_left) && '' == $user_id) {
		$time_left_text = ' ( ' . $days_left . esc_html__(' Days left', 'save-cart-later') . ' )';
	}

	if (isset($mwb_woo_smc_get_save_later_product) && is_array($mwb_woo_smc_get_save_later_product) && !empty($mwb_woo_smc_get_save_later_product)) {
	?>
		<div id="mwb_woo_smc_recover_user_cart_data">

			<table class="mwb-woo-smc-shop_table shop_table_responsive mwb-woo-smc-cart mwb-woo-smc-cart-contents table" cellspacing="0">
				<thead>
					<tr>
						<th class="mwb-woo-smc-product-thumbnail">&nbsp;</th>
						<th class="mwb-woo-smc-product-name"><?php esc_html_e('Product', 'save-cart-later'); ?></th>
						<th class="product-data">&nbsp;</th>
						<th class="mwb-woo-smc-product-quantity"><?php esc_html_e('Quantity', 'save-cart-later'); ?></th>
						<th class="mwb-woo-smc-product-price"><?php esc_html_e('Price Per Unit', 'save-cart-later'); ?></th>
						<th class="mwb-woo-smc-product-subtotal"><?php esc_html_e('Subtotal', 'save-cart-later'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total_save_for_later_sum = 0;
					foreach ($mwb_woo_smc_get_save_later_product as $saved_key => $saved_value) {

						foreach ($saved_value as $product_cart_key => $product_cart_value) {
							$mwb_attribute = array();
							if (isset($product_cart_value['variation_id']) && 0 != $product_cart_value['variation_id']) {
								$product = wc_get_product($product_cart_value['variation_id']);
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['variation_id']), 'single-post-thumbnail');
								$mwb_attribute = $product_cart_value['product_attribute'];

								// compatibility with product vendor.
								//$product_cart_value['product_meta'] = $this->mwb_woo_save_my_cart_product_vendor_compatibility( $product_cart_value['product_meta'], $product_cart_value['product_id'] );

							} else {
								$product = wc_get_product($product_cart_value['product_id']);
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['product_id']), 'single-post-thumbnail');

								// compatibility with product vendor.
								//$product_cart_value['product_meta'] = $this->mwb_woo_save_my_cart_product_vendor_compatibility( $product_cart_value['product_meta'], $product_cart_value['product_id'] );
							}
							$product_data = $product->get_data();
							$product_parent = wc_get_product($product_cart_value['product_id']);
					?>
							<tr>
								<td><?php //echo wp_kses_post($product_parent->get_image('shop_thumbnail')); 

									$var_image_id = $product->get_image_id();
									$var_image_url =  wp_get_attachment_url($var_image_id);

									if (strpos($var_image_url, '_sw') == true) {
										$var_image_url = str_replace("_sw", "-300x300", $var_image_url);
									}

									if (strpos($var_image_url, 'EBY_') == true) {
										$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
									}

									if ($var_image_url) {
										$thumbnail = '<img width="128" height="128" src="' . $var_image_url . '">';
									} else {
										$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['product_id']), 'shop_thumbnail');
										$thumbnail = '<img width="128" height="128" src="' . $image_array[0] . '">';
									}

									if($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327){
										$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['product_id']), 'shop_thumbnail');
										$thumbnail = '<img width="128" height="128" src="'.$image_coming_soon[0].'">';
									}
									echo $thumbnail;

									?></td>
								<td data-title="Product"><a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"><?php echo esc_attr($product->get_name()); ?></a>
									<?php
									if (isset($product_cart_value['product_meta']) && !empty($product_cart_value['product_meta']) && is_array($product_cart_value['product_meta'])) {
										$mwb_item_data = $product_cart_value['product_meta'];
									?>
										<dl class="variation">
											<?php foreach ($mwb_item_data as $data) : ?>
												<dt class="<?php echo sanitize_html_class('variation-' . $data['key']); ?>"><?php echo wp_kses_post($data['key']); ?>:</dt>
												<dd class="<?php echo sanitize_html_class('variation-' . $data['key']); ?>"><?php echo wp_kses_post(wpautop($data['display'])); ?></dd>
											<?php endforeach; ?>
										</dl>
									<?php
									}
									?>
								</td>
								<td>
									<?php
									if ($product_cart_value['product_attribute']['attribute_pa_size']) {
										echo '<div><span>Size:</span> ' . strtoupper($product_cart_value['product_attribute']['attribute_pa_size']) . '</div>';
									}

									if ($product_cart_value['product_attribute']['attribute_pa_color']) {
										$pa_color = $product_cart_value['product_attribute']['attribute_pa_color'];
										$pa_color = str_replace("-", " ", $pa_color);
										echo '<div><span>Color:</span> ' . ucwords($pa_color) . '</div>';
									}
									$upc_field = get_post_meta($product_cart_value['variation_id'], 'upc_field', true);
									if ($upc_field) {
										echo '<div><span>UPC:</span> ' . $upc_field . '</div>';
									}
									$price = $product->get_price();
									$new_price = str_replace('$', '', $price);
									$total_save_for_later_sum += $new_price * $product_cart_value['product_qty'];
									?>
								</td>
								<td data-title="Quantity"><label id="mwb_woo_smc_save_for_later_<?php echo esc_attr($product->get_id()); ?>" class="mwb_woo_smc_later_product_quntity"><?php echo esc_attr($product_cart_value['product_qty']); ?></label></td>
								<td data-title="Price"><?php echo wp_kses(WC()->cart->get_product_price($product), $allowed_html); ?></td>
								<td data-title="Total"><?php echo wp_kses(WC()->cart->get_product_subtotal($product, $product_cart_value['product_qty']), $allowed_html); ?></td>
							</tr>
					<?php

						}
					}
					?>
				</tbody>
			</table>
			<div class="saveforlater_total_data ">Total: <?php echo wc_price($total_save_for_later_sum); ?></div>
		</div>
	<?php
	}
}

function mwb_woo_save_my_cart_product_section_custom()
{

	$allowed_html = array();
	global $current_user;
	$user_id = $current_user->ID;
	if (is_user_logged_in()) {
		//$user_role_enable = $this->is_save_cart_enable_for_user( $user_id );
		//if ( $user_role_enable ) {
		// @codingStandardsIgnoreStart
		$mwb_woo_smc_get_save_later_product = get_user_meta($user_id, 'mwb_woo_smc_logged_in_user_data', true);
		if (empty($mwb_woo_smc_get_save_later_product)) {
			echo '<div class="product_not_found"><h4>Product Not availabe  in Save For Later.</h4></div>';
		}
		//}
	} elseif (isset($_COOKIE['mwb_woo_smc_save_guest_user_data'])) {
		$mwb_woo_smc_get_save_later_product = unserialize(base64_decode(map_deep(wp_unslash($_COOKIE['mwb_woo_smc_save_guest_user_data']), 'sanitize_text_field'))); // @codingStandardsIgnoreEnd
	}
	$days_left = 0;
	$time_left_text = '';
	if (isset($_COOKIE['mwb_woo_smc_save_guest_user_expiry'])) {
		$mwb_woo_smc_save_guest_user_expiry = map_deep(wp_unslash($_COOKIE['mwb_woo_smc_save_guest_user_expiry']), 'sanitize_text_field');
		$days_left = (int) (((int) $mwb_woo_smc_save_guest_user_expiry - time()) / 86400);
	}
	if (isset($days_left) && !empty($days_left) && '' == $user_id) {
		$time_left_text = ' ( ' . $days_left . esc_html__(' Days left', 'save-cart-later') . ' )';
	}

	if (isset($mwb_woo_smc_get_save_later_product) && is_array($mwb_woo_smc_get_save_later_product) && !empty($mwb_woo_smc_get_save_later_product)) {
	?>
		<div id="mwb_woo_smc_recover_user_cart_data">
			<?php /* ?>
			<h3><?php esc_html_e( 'Your Saved Cart', 'save-cart-later' ); ?><?php echo esc_html( $time_left_text ); ?></h3>
			<?php */ ?>
			<span></span>
			<?php
			//if ( $this->mwb_save_my_cart_add_all_item_to_cart() ) {
			?>
			<div class="mwb_mwc_class">
				<a target="_blank" class="float-left back_to_shop" href="<?php echo wc_get_page_permalink('shop'); ?>">Back to Shopping</a>
				<a class="share-list btn primary-btn" target="_blank" href=" <?php share_list_get_pdf(); ?>">
					Share List
				</a>
			</div>
			<?php //} 
			?>
			<table class="mwb-woo-smc-shop_table shop_table_responsive mwb-woo-smc-cart mwb-woo-smc-cart-contents table" cellspacing="0">
				<thead>
					<tr>
						<?php /* ?>
						<th></th>
						<?php */  ?>
						<th class="mwb-woo-smc-product-thumbnail">&nbsp;</th>
						<th class="mwb-woo-smc-product-name"><?php esc_html_e('Product', 'save-cart-later'); ?></th>
						<th class="product-data">&nbsp;</th>
						<th class="mwb-woo-smc-product-quantity"><?php esc_html_e('Quantity', 'save-cart-later'); ?></th>
						<th class="mwb-woo-smc-product-price"><?php esc_html_e('Price Per Unit', 'save-cart-later'); ?></th>
						<th class="mwb-woo-smc-product-subtotal"><?php esc_html_e('Subtotal', 'save-cart-later'); ?></th>
						<?php /* ?>
						<th class="mwb-woo-smc-product-move"><?php esc_html_e('Action', 'save-cart-later'); ?></th>
						<?php */  ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$index = 1;
					$total_cart_items = count($mwb_woo_smc_get_save_later_product);
					$total_cart_items_more = $total_cart_items - 4;
					$total_save_for_later_sum = 0;

					foreach ($mwb_woo_smc_get_save_later_product as $saved_key => $saved_value) {

						foreach ($saved_value as $product_cart_key => $product_cart_value) {
							$mwb_attribute = array();

							$aws_var_image = get_post_meta($product_cart_value['variation_id'],'aws_url_field', true);
							
							if (isset($product_cart_value['variation_id']) && 0 != $product_cart_value['variation_id']) {
								$product = wc_get_product($product_cart_value['variation_id']);
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['variation_id']), 'single-post-thumbnail');
								$mwb_attribute = $product_cart_value['product_attribute'];

								// compatibility with product vendor.
								//$product_cart_value['product_meta'] = $this->mwb_woo_save_my_cart_product_vendor_compatibility( $product_cart_value['product_meta'], $product_cart_value['product_id'] );

							} else {
								$product = wc_get_product($product_cart_value['product_id']);
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['product_id']), 'single-post-thumbnail');

								// compatibility with product vendor.
								//$product_cart_value['product_meta'] = $this->mwb_woo_save_my_cart_product_vendor_compatibility( $product_cart_value['product_meta'], $product_cart_value['product_id'] );
							}
							$product_data = $product->get_data();
							$product_parent = wc_get_product($product_cart_value['product_id']);
					?>
							<?php if ($index > 4) {
								$hide_class = 'd-none';
							} ?>
							<tr class="<?php echo $hide_class; ?>">
								<?php /* ?>
								<td><a href="javascript:void(0)" class="mwb_woo_smc_remove_saved_item remove" data_move_prod_id="<?php echo esc_attr($product->get_id()); ?>" data-prod_qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>">x</a></td>
								<?php */  ?>
								<td><?php //echo wp_kses_post($product_parent->get_image('shop_thumbnail')); 
								
								if($aws_var_image){
										$thumbnail = '<img width="128" height="128" src="' . $aws_var_image . '">';
									}
									else { 

									$var_image_id = $product->get_image_id();
									$var_image_url =  wp_get_attachment_url($var_image_id);

									if (strpos($var_image_url, '_sw') == true) {
										$var_image_url = str_replace("_sw", "-300x300", $var_image_url);
									}

									if (strpos($var_image_url, 'EBY_') == true) {
										$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
									}

									if ($var_image_url) {
										$thumbnail = '<img width="128" height="128" src="' . $var_image_url . '">';
									} else {
										$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['product_id']), 'shop_thumbnail');
										$thumbnail = '<img width="128" height="128" src="' . $image_array[0] . '">';
									}
									}

									if($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327){
										$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_cart_value['product_id']), 'shop_thumbnail');
										$thumbnail = '<img width="128" height="128" src="'.$image_coming_soon[0].'">';
									}
									echo $thumbnail;


									/*if (!$product_permalink) {
								echo $thumbnail; // PHPCS: XSS ok.
							} else {
								printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
							}*/

									?></td>
								<td data-title="Product"><a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"><?php echo esc_attr($product->get_name()); ?></a>
									<?php
									if (isset($product_cart_value['product_meta']) && !empty($product_cart_value['product_meta']) && is_array($product_cart_value['product_meta'])) {
										$mwb_item_data = $product_cart_value['product_meta'];
									?>
										<dl class="variation">
											<?php foreach ($mwb_item_data as $data) : ?>
												<dt class="<?php echo sanitize_html_class('variation-' . $data['key']); ?>"><?php echo wp_kses_post($data['key']); ?>:</dt>
												<dd class="<?php echo sanitize_html_class('variation-' . $data['key']); ?>"><?php echo wp_kses_post(wpautop($data['display'])); ?></dd>
											<?php endforeach; ?>
										</dl>
									<?php
									}
									echo '<div>';
									?>
									<a href="javascript:void(0)" class="mwb_woo_smc_remove_saved_item remove" data_move_prod_id="<?php echo esc_attr($product->get_id()); ?>" data-prod_qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>">Remove</a>
									<?php
									if (is_array($product_data) && ('outofstock' != $product_data['stock_status'])) {
									?>
										<?php /*<input type="button" name="mwb_woo_smc_move_to_cart" class="mwb_woo_smc_move_to_cart_button button alt" id="mwb_woo_smc_move_to_cart_<?php echo esc_attr($product->get_id()); ?>" data_move_prod_id="<?php echo esc_attr($product->get_id()); ?>" value="<?php esc_attr_e('Move To Cart', 'save-cart-later'); ?>" data-prod_qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>">*/ ?>
										
										<button class="add-to-cart-button button alt" data-product-id="<?php echo esc_attr($product->get_id()); ?>" data-prod-qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>">Move To Cart</button>
										
										<?php
										$fb_share_enable = get_option('mwb_save_my_cart_fb_share_button_enable', false);
										if (isset($fb_share_enable) && 'yes' == $fb_share_enable) {
											$image_url = MWB_WOOCOMMERCE_SAVE_MY_CART_DIR_URL . '/public/images/fbshare.jpeg';
										?>
											<a href="https://www.facebook.com/sharer.php?u=<?php echo esc_url(get_permalink($product->get_id())); ?>&t=<?php echo esc_html(get_the_title($product->get_id())); ?>" class="mwb_woo_smc_button"><img src="<?php echo esc_url($image_url); ?>" class="mwb_woo_smc_img"></a>
										<?php
										}
									} else {
										?>
										<?php /*<input type="button" name="mwb_woo_smc_move_to_cart" class="mwb_woo_smc_move_to_cart_button " id="mwb_woo_smc_move_to_cart_<?php echo esc_attr($product->get_id()); ?>" data_move_prod_id="<?php echo esc_attr($product->get_id()); ?>" value="<?php esc_attr_e('Out of stock', 'save-cart-later'); ?>" data-prod_qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>" disabled="disabled" style="color: red;">*/ ?>
										
										
										<button class="add-to-cart-button button alt" data-product-id="<?php echo esc_attr($product->get_id()); ?>" data-prod-qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>" disabled="disabled" style="color: red;">Move To Cart</button>
									<?php
									}
									echo '</div>';
									?>

								</td>
								<td>
									<?php
									if ($product_cart_value['product_attribute']['attribute_pa_size']) {
										echo '<div><span>Size:</span> ' . strtoupper($product_cart_value['product_attribute']['attribute_pa_size']) . '</div>';
									}

									if ($product_cart_value['product_attribute']['attribute_pa_color']) {
										$pa_color = $product_cart_value['product_attribute']['attribute_pa_color'];
										$pa_color = str_replace("-", " ", $pa_color);
										echo '<div><span>Color:</span> ' . ucwords($pa_color) . '</div>';
									}
									$upc_field = get_post_meta($product_cart_value['variation_id'], 'upc_field', true);
									if ($upc_field) {
										echo '<div><span>UPC:</span> ' . $upc_field . '</div>';
									}
									$price = $product->get_price();
									$new_price = str_replace('$', '', $price);
									$total_save_for_later_sum += $new_price * $product_cart_value['product_qty'];
									?>
								</td>
								<td data-title="Quantity"><label id="mwb_woo_smc_save_for_later_<?php echo esc_attr($product->get_id()); ?>" class="mwb_woo_smc_later_product_quntity"><?php echo esc_attr($product_cart_value['product_qty']); ?></label></td>
								<td data-title="Price"><?php echo wp_kses(WC()->cart->get_product_price($product), $allowed_html); ?></td>
								<td data-title="Total"><?php echo wp_kses(WC()->cart->get_product_subtotal($product, $product_cart_value['product_qty']), $allowed_html); ?></td>
								<?php /* ?>	
								<td data-title="Action">
									<?php
									if (is_array($product_data) && ('outofstock' != $product_data['stock_status'])) {
									?>
										<input type="button" name="mwb_woo_smc_move_to_cart" class="mwb_woo_smc_move_to_cart_button button alt" id="mwb_woo_smc_move_to_cart_<?php echo esc_attr($product->get_id()); ?>" data_move_prod_id="<?php echo esc_attr($product->get_id()); ?>" value="<?php esc_attr_e('Move To Cart', 'save-cart-later'); ?>" data-prod_qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>">
										<?php
										$fb_share_enable = get_option('mwb_save_my_cart_fb_share_button_enable', false);
										if (isset($fb_share_enable) && 'yes' == $fb_share_enable) {
											$image_url = MWB_WOOCOMMERCE_SAVE_MY_CART_DIR_URL . '/public/images/fbshare.jpeg';
										?>
											<a href="https://www.facebook.com/sharer.php?u=<?php echo esc_url(get_permalink($product->get_id())); ?>&t=<?php echo esc_html(get_the_title($product->get_id())); ?>" class="mwb_woo_smc_button"><img src="<?php echo esc_url($image_url); ?>" class="mwb_woo_smc_img"></a>
										<?php
										}
									} else {
										?>
										<input type="button" name="mwb_woo_smc_move_to_cart" class="mwb_woo_smc_move_to_cart_button " id="mwb_woo_smc_move_to_cart_<?php echo esc_attr($product->get_id()); ?>" data_move_prod_id="<?php echo esc_attr($product->get_id()); ?>" value="<?php esc_attr_e('Out of stock', 'save-cart-later'); ?>" data-prod_qty="<?php echo esc_attr($product_cart_value['product_qty']); ?>" disabled="disabled" style="color: red;">
									<?php
									}
									?>
								</td>
								<?php */ ?>
							</tr>
					<?php
							$index++;
						}
					}
					?>
				</tbody>
			</table>
			<span class="saveforlater_total_data d-none"><?php echo wc_price($total_save_for_later_sum); ?></span>
			<?php
			if ($total_cart_items > 4) { ?>
				<div class="more-save-cart text-center">
					<button type="button" class="btn btn-secondary view-more-save-cart">View <?php echo $total_cart_items_more; ?> More Items</button>
				</div>
			<?php } ?>
		</div>
<?php
	}
}


//Custom Move To Cart Function*/
function add_to_cart_action() {
    if (isset($_POST['product_id'])) {
		global $current_user;
		$user_id = $current_user->ID;
		$mwb_woo_smc_remove_get_user_data = get_user_meta( $user_id, 'mwb_woo_smc_logged_in_user_data', true );
		$mwb_woo_smc_logged_in_save_cart_to_atc = get_user_meta( $user_id, 'mwb_woo_smc_logged_in_save_cart_to_atc', true );
				
        $product_id = intval($_POST['product_id']);
		$prod_qty = intval($_POST['MwbWooMovedProdQty']);
		$mwb_woo_smc_remove_from_saved_item = isset( $_POST['MwbWooMoved'] ) ? sanitize_text_field( wp_unslash( $_POST['MwbWooMoved'] ) ) : '';
		
        $added_to_cart = WC()->cart->add_to_cart($product_id,$prod_qty);
		
				

        if ($added_to_cart) {
            
			if ( is_array( $mwb_woo_smc_remove_get_user_data ) && ! empty( $mwb_woo_smc_remove_get_user_data ) ) {
						foreach ( $mwb_woo_smc_remove_get_user_data as $remove_key => $remove_value ) {
							foreach ( $remove_value as $key => $value ) {
								if ( in_array( $product_id, $value ) ) {
									unset( $mwb_woo_smc_remove_get_user_data[ $remove_key ] );
									// @codingStandardsIgnoreStart
									update_user_meta( $user_id, 'mwb_woo_smc_logged_in_user_data', $mwb_woo_smc_remove_get_user_data );
									// @codingStandardsIgnoreEnd
									$remove_notice = __( 'Saved Product Remove Successfully', 'save-cart-later' );
									wc_add_notice( $remove_notice );
									echo 'Product added to cart successfully.';
								}
							}
						}
					}
        } else {
            echo 'Failed to add the product to the cart.';
        }
    }

    wp_die();
}

add_action('wp_ajax_add_to_cart_action', 'add_to_cart_action');
add_action('wp_ajax_nopriv_add_to_cart_action', 'add_to_cart_action');
