<?php

if ($tracking_items) :

	//$ast = new AST_Pro_Actions();
	//$wcast_customizer_settings = new ast_pro_customizer_settings();

	//$hide_trackig_header = $ast->get_checkbox_option_value_from_array('tracking_info_settings', 'hide_trackig_header', '');
	//$shipment_tracking_header = $ast->get_option_value_from_array('tracking_info_settings', 'header_text_change', 'Shipping info');
	//$shipment_tracking_header_text = $ast->get_option_value_from_array('tracking_info_settings', 'additional_header_text', '');
	//$fluid_table_layout = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_table_layout', $wcast_customizer_settings->defaults['fluid_table_layout']);
	//$border_color = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_table_border_color', $wcast_customizer_settings->defaults['fluid_table_border_color']);
	//$border_radius = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_table_border_radius', $wcast_customizer_settings->defaults['fluid_table_border_radius']);
	//$background_color = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_table_background_color', $wcast_customizer_settings->defaults['fluid_table_background_color']);
	//$table_padding = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_table_padding', $wcast_customizer_settings->defaults['fluid_table_padding']);
	//$button_background_color = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_button_background_color', $wcast_customizer_settings->defaults['fluid_button_background_color']);
	//$button_font_color = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_button_font_color', $wcast_customizer_settings->defaults['fluid_button_font_color']);
	//$button_radius = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_button_radius', $wcast_customizer_settings->defaults['fluid_button_radius']);
	//$button_expand = $ast->get_checkbox_option_value_from_array('tracking_info_settings', 'fluid_button_expand', $wcast_customizer_settings->defaults['fluid_button_expand']);
	//$fluid_button_text = $ast->get_option_value_from_array('tracking_info_settings', 'fluid_button_text', $wcast_customizer_settings->defaults['fluid_button_text']);
	//$fluid_hide_provider_image = $ast->get_checkbox_option_value_from_array('tracking_info_settings', 'fluid_hide_provider_image', $wcast_customizer_settings->defaults['fluid_hide_provider_image']);
	//$fluid_hide_shipping_date = $ast->get_checkbox_option_value_from_array('tracking_info_settings', 'fluid_hide_shipping_date', $wcast_customizer_settings->defaults['fluid_hide_shipping_date']);

	//$fluid_button_size = $ast->get_checkbox_option_value_from_array('tracking_info_settings', 'fluid_button_size', $wcast_customizer_settings->defaults['fluid_button_size']);
	//$button_font_size = ('large' == $fluid_button_size) ? 16 : 14;
	//$button_padding = ('large' == $fluid_button_size) ? '12px 20px' : '10px 15px';

	//$order_data = wc_get_order($order_id);

	//$shipment_status = get_post_meta($order_id, 'shipment_status', true);

	/*if (!empty($order_data)) {
		$order_status = $order_data->get_status();
	} else {
		$order_status = 'completed';
	}*/

	/*if (1 != $hide_trackig_header) {
?>
		<h2><?php esc_html_e(apply_filters('woocommerce_shipment_tracking_my_orders_title', __($shipment_tracking_header, 'ast-pro'))); ?></h2>
	<?php
	}*/

	/*if ('' != $hide_trackig_header) {
	?>
		<p><?php esc_html_e($shipment_tracking_header_text); ?></p>
	<?php } */?>

	<div class="fluid_section">
		<div class="shipment_head shipment_head_tracking">
			<div class="shipment_head_order">Order</div>
			<div class="shipment_head_staus">Status</div>
			<div class="shipment_head_action">Action</div>
		</div>
		<?php
		$shipment_num = 1;
		foreach ($tracking_items as $key => $tracking_item) {

			//echo '<pre>';
			//print_r($tracking_item);
			//echo '</pre>';

			/*$ts_status = '';
			if (isset($shipment_status[$key])) {
				if (isset($shipment_status[$key]['status'])) {
					$ts_status = $shipment_status[$key]['status'];
				}
			}*/

			//$ts_tracking_page = $ast->check_ts_tracking_page_for_tracking_item($order_id, $tracking_item, $ts_status);
			

		?>
			<div class="step_form panel panel-default">
					<div data-toggle="collapse" data-target="#collapse_<?php echo $shipment_num; ?>" class="shipment_items_head panel-title">
						<div class="shipment_num">
							<?php echo 'Shipment ' . $shipment_num ?>
						</div>
						<div class="shipment_status">
							<?php echo 'Shipped'; ?>
							<?php echo 'on '.esc_html( date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ) ); ?>
						</div>

						<div class="track_shipment">
							<a target="blank" href="<?php echo esc_url($tracking_item['ast_tracking_link']); ?>" class="button track-button" data-order="<?php esc_html_e($order_id); ?>" data-tracking="<?php echo esc_html($tracking_item['tracking_number']); ?>"><?php echo $tracking_item['tracking_number'];?></a>
							<?php /* if ($ts_tracking_page) { ?>
								<a href="javascript:void(0)" class="button track-button open_tracking_lightbox" data-order="<?php esc_html_e($order_id); ?>" data-tracking="<?php echo esc_html($tracking_item['tracking_number']); ?>">Track</a>
							<?php } else { ?>
								<a target="blank" href="<?php echo esc_url($tracking_item['ast_tracking_link']); ?>" class="button track-button" data-order="<?php esc_html_e($order_id); ?>" data-tracking="<?php echo esc_html($tracking_item['tracking_number']); ?>"><?php echo $tracking_item['tracking_number'];?></a>
							<?php } */?>
						</div>
					</div>
					<div id="collapse_<?php echo $shipment_num; ?>" class="panel-collapse collapse">
					<?php

					//$tracking_items = $ast->get_tracking_items($order_id);
					/*$order = wc_get_order($order_id);

					if (!$order) {
						return;
					}*/

					//echo '<ul style="padding-left: 0px;margin: 0;margin-top: 5px;text-align: left;">';
					echo '<table class="shipment_items_table woocommerce-table woocommerce-table--order-details shop_table order_details">';
					if (isset($tracking_item['products_list'])) {
						foreach ($tracking_item['products_list'] as $products) {

							$product = wc_get_product($products->product);


							if ($product) {

								$product_id = wp_get_post_parent_id($products->product);
							
								//
								$product_title = get_the_title($product_id);
	
								$product_style = get_post_meta($product_id, 'product_style', true);
								$upc_field = get_post_meta($product->get_id(), 'upc_field', true);
	
								$variation_attributes = $product->get_variation_attributes();
								
								$variation_color = woo2_helper_attribute_name('pa_color', $variation_attributes['attribute_pa_color']);
								$variation_size = woo2_helper_attribute_name('pa_size', $variation_attributes['attribute_pa_size']);
								//
	
								$product_name_full = $product_title.' - '.$product_style.' - '.$variation_size.' - '.$variation_color;

								$product_name = $product->get_name();
								$price_totol = $product->get_price() * esc_html($products->qty);
								echo '<tr class="woocommerce-table__line-item order_item">';

								//$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $product->get_image(), $cart_item, $cart_item_key);

								$var_image_id = $product->get_image_id();
								$var_image_url =  wp_get_attachment_url($var_image_id);

								if (strpos($var_image_url, '_sw') == true) {
									$var_image_url = str_replace("_sw", "-100x100", $var_image_url);
								}

								if (strpos($var_image_url, 'EBY_') == true) {
									$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
								}
							
								
								if(@getimagesize($var_image_url)){
									$thumbnail = '<img src="' . $var_image_url . '">';
								} else {
									$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), array(100,100));
									$thumbnail = '<img src="' . $image_array[0] . '">';
								}

								if ($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327) {
									$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id),  array(100,100));
									$thumbnail = '<img class="c_im" src="' . $image_coming_soon[0] . '">';
								}
								

								echo '<td class="order_item_img">';
								echo $thumbnail;
								echo '</td>';
								//echo '<td><span>' . esc_html($product_name) . '</span></td>';
								echo '<td><span>' . esc_html($product_name_full) . '</span></td>';
								echo '<td> x ' . esc_html($products->qty) . '</td>';
								echo '<td>' . wc_price($price_totol) . '</td>';

								//echo $product->get_image();
								//echo '<li style="list-style: none;">' . esc_html( $product_name ) . ' x ' . esc_html( $products->qty ) . '</li>';
								echo '</tr>';
							}
						}
					}
					echo '</table>';
					//echo '</ul>';


					//do_action( 'ast_fluid_left_cl_end', $tracking_item, $order_id ); 
					?>
			</div>
				</div>

		<?php
			$shipment_num++;
		} ?>
	</div>
	<div id="" class="popupwrapper ts_tracking_popup" style="display:none;">
		<div class="popuprow">

		</div>
		<div class="popupclose"></div>
	</div>
	<style>
		.fluid_section {
			margin-bottom: 10px;
		}

		a.button.track-button {
			text-decoration: none;
			display: inline-block;
			margin-top: 0;
			text-align: center;
			margin-bottom: 0;
			line-height: 20px;
			text-transform: none;
		}

		.tracking_number {
			color: #03a9f4;
			text-decoration: none;
		}

	</style>
<?php
endif;
