<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/ast-pro-tpi-email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left'; 
$margin_side = is_rtl() ? 'left' : 'right';

do_action( 'wcast_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );

$table_font_size = '';
$kt_woomail = get_option( 'kt_woomail' );

if ( !empty($kt_woomail) && isset( $kt_woomail['font_size'] ) ) {
	$table_font_size = 'font-size:' . $kt_woomail['font_size'] . 'px';
}	

$wcast_customizer_settings = new ast_pro_customizer_settings();
$ast = new AST_Pro_Actions();

$button_background_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_background_color', $wcast_customizer_settings->defaults['fluid_button_background_color'] );
$button_font_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_font_color', $wcast_customizer_settings->defaults['fluid_button_font_color'] );
$button_radius = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_radius', $wcast_customizer_settings->defaults['fluid_button_radius'] );
$fluid_button_text = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_text', $wcast_customizer_settings->defaults['fluid_button_text'] );
?>
<style>
a.button.track-button {
	background: <?php esc_html_e( $button_background_color ); ?>;
	color: <?php esc_html_e( $button_font_color ); ?> !important;
	padding: 8px 15px;
	text-decoration: none;
	border-radius: <?php esc_html_e( $button_radius ); ?>px;
	margin-top: 0;
	font-size: 90% !important;
	position: absolute !important;
	top: 6px !important;
	right: 0 important;
}
.tpi_order_details_table tr td{
	vertical-align: middle; 
	font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; 
	word-wrap:break-word;
	border-left:0;
	border:0;
	border-bottom:1px solid #e0e0e0;
	padding: 12px 6px 12px 6px !important;	
	position: relative;
}
.tpi_order_details_table tr td.last_td{
	border-bottom:0 !important;
}
.tpi_order_details_table tr td.image_id{
	padding-left: 0 !important;
}
.shipment_heading{	
	margin: 0 !important;	
}
.shipment_heading.heading_border{
	border-bottom:1px solid #e0e0e0;	
}
.shipping_items_heading{
	margin: 10px 0 0 !important;
	border-bottom: 1px solid #e0e0e0;
	padding-bottom: 3px;
}
</style>
	<?php 
	$total_trackings = count( $tracking_items );
	$layout = 'tpi_layout_2';
	foreach ( $tracking_items as $tracking_item ) {
		if ( isset( $tracking_item['products_list'] ) && !empty( $tracking_item['products_list'] ) && count( $tracking_item['products_list'] ) > 1 ) {
			$layout = 'tpi_layout_2';
			continue;
		}
	}
	//echo $layout;exit;
	if ( 'tpi_layout_2' == $layout ) {
			
		$num = 1;
		foreach ( $tracking_items as $tracking_item ) {	
			$heading_class = ( isset( $tracking_item['products_list'] ) && !empty( $tracking_item['products_list'] ) && count( $tracking_item['products_list'] ) == 1 ) ? 'heading_border' : '';
		
			if ( $total_trackings > 1 ) {
					/* translators: %1$s: search number, %2$s: search total trackings */
					echo '<p class="shipment_heading"><strong><i>' . sprintf( esc_html( 'Shipment %1$s (out of %2$s):', 'ast-pro' ), esc_html( $num ) , esc_html( $total_trackings ) ) . '</i></strong></p>';
			}
			
			$tpi_item = array();
			$tpi_item[] = $tracking_item;
			
			$local_template	= get_stylesheet_directory() . '/woocommerce/emails/fluid-tpi-tracking-info.php';
			if ( file_exists( $local_template ) && is_writable( $local_template ) ) {
				echo wp_kses_post( wc_get_template( 'emails/fluid-tpi-tracking-info.php', array( 
					'tracking_items' => $tpi_item,
					'order_id' => $order->get_id(),						
				), 'woocommerce-advanced-shipment-tracking/', get_stylesheet_directory() . '/woocommerce/' ) );
			} else {
				echo wp_kses_post( wc_get_template( 'emails/fluid-tpi-tracking-info.php', array( 
					'tracking_items' => $tpi_item, 
					'order_id'=> $order->get_id(),						
				), 'woocommerce-advanced-shipment-tracking/', ast_pro()->get_plugin_path() . '/templates/' ) );
			}
			$numItems = 0;
			if ( is_array( $tracking_item['products_list'] ) ) {
			?>
			<div style="margin:0;">
				<p class="shipping_items_heading"><?php echo esc_html( $shipping_items_heading ); ?></p>
				<table class="td tpi_order_details_table" cellspacing="0" cellpadding="6" style="background-color: transparent;width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;border:0;<?php echo esc_html( $table_font_size ); ?>" border="0">
					<tbody>
						<?php
						$numItems = count($tracking_item['products_list']);
						
						$i = 0;		
									
						foreach ( (array) $tracking_item['products_list'] as $products_list ) {
							$product_id = '';
							$product = wc_get_product( $products_list->product ); 
							$product_id = $product->get_id();
							$sku           = '';
							$purchase_note = '';
							$image         = '';
							$image_size = array( 64, 64 );
						
							if ( is_object( $product ) ) {
								$sku           = $product->get_sku();
								$purchase_note = $product->get_purchase_note();
								$image         = $product->get_image( $image_size );

								//custom
								$product_id = wp_get_post_parent_id($product->get_id());
								$var_image_id = $product->get_image_id();

								$var_image_url =  wp_get_attachment_url( $var_image_id );

								if (strpos($var_image_url, '_sw') == true) {
									$var_image_url = str_replace("_sw","-300x300", $var_image_url);
								}
								
								if (strpos($var_image_url, 'EBY_') == true) {
									$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
								}
								
								if(file_is_valid_image($var_image_url)){
									$thumbnail = '<img width="64" src="'.$var_image_url.'">';
								}else{
									$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
									$thumbnail = '<img width="64" src="'.$image_array[0].'">';
								}
								
								if($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327){
									$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
									$thumbnail = '<img width="64" src="'.$image_coming_soon[0].'">';
								}
								$image = $thumbnail;
								//custom
							}
							
							foreach ( $order->get_items() as $item_id => $item ) {
								$item_product = $item->get_product();
								$item_product_id = $item_product->get_id();
								if ( $item_product_id == $product_id ) {
									$order_item = $item;
								}
							}
							$last_child_class = '';
							if ( ++$i === $numItems ) {
								$last_child_class = 'last_td';								
							}
							?>
							<tr>
								<?php if ( $display_product_images ) { ?>
									<td class="td image_id <?php echo esc_attr( $last_child_class ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>;width: 70px;">
										<?php echo wp_kses_post( $image ); ?>
									</td>
								<?php } ?>
								<td class="td <?php echo esc_attr( $last_child_class ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
									<?php
									
									// Product name.
									echo '<div>'; 
									echo wp_kses_post( $product->get_name() ); 
									echo ' x '; 
									echo esc_html( $products_list->qty );

									//custom
									
									$product_style = get_post_meta($product_id, 'product_style', true);
									if($product_style){
										echo wp_kses_post( ' (' . $product_style . ')' );
									}
									//custom

									if ( $display_shippment_item_price ) {
										echo ' - ';
										echo wp_kses_post( $order->get_formatted_line_subtotal( $order_item ) );
									}
									echo '</div>';
									?>
								</td>	
							</tr>	
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<?php
			}
			$num++;
		}		
	} else {
		?>
		<p class="shipping_items_heading"><strong><?php echo esc_html( $shipping_items_heading ); ?></strong></p>
		<?php
		$numItems = count($tracking_items);
		$i = 0;	
		foreach ( $tracking_items as $tracking_item ) {
			$last_child_class = '';
			if ( ++$i === $numItems ) {
				$last_child_class = 'last_td';								
			}
			?>
		<div style="margin:0;">			
			<table class="td tpi_order_details_table" cellspacing="0" cellpadding="6" style="background-color: transparent;width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;border:0;<?php echo esc_html( $table_font_size ); ?>" border="0">
				<tbody>
					<?php 
					
					foreach ( $tracking_item['products_list'] as $products_list ) {
						$product_id = '';
						$product = wc_get_product( $products_list->product ); 
						$product_id = $product->get_id();
						$sku           = '';
						$purchase_note = '';
						$image         = '';
						$image_size = array( 64, 64 );
					
						if ( is_object( $product ) ) {
							$sku           = $product->get_sku();
							$purchase_note = $product->get_purchase_note();
							$image         = $product->get_image( $image_size );

							//custom
							$product_id = wp_get_post_parent_id($product->get_id());
							$var_image_id = $product->get_image_id();

							$var_image_url =  wp_get_attachment_url( $var_image_id );

							if (strpos($var_image_url, '_sw') == true) {
								$var_image_url = str_replace("_sw","-300x300", $var_image_url);
							}
							
							if (strpos($var_image_url, 'EBY_') == true) {
								$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
							}
							
							if(file_is_valid_image($var_image_url)){
								$thumbnail = '<img width="64" src="'.$var_image_url.'">';
							}else{
								$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
								$thumbnail = '<img width="64" src="'.$image_array[0].'">';
							}
							
							if($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327){
								$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
								$thumbnail = '<img width="64" src="'.$image_coming_soon[0].'">';
							}
							$image = $thumbnail;
							//custom
						}
						
						foreach ( $order->get_items() as $item_id => $item ) {
							$item_product = $item->get_product();
							$item_product_id = $item_product->get_id();
							if ( $item_product_id == $product_id ) {
								$order_item = $item;
							}
						}
						
						?>
						<tr>
							<?php if ( $display_product_images ) { ?>
								<td class="td image_id <?php echo esc_attr( $last_child_class ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>;width: 70px;">
									<?php echo wp_kses_post( $image ); ?>
								</td>
							<?php } ?>
							<td class="td <?php echo esc_attr( $last_child_class ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
								<?php
								// Product name.
								echo wp_kses_post( $product->get_name() ); 
								echo ' x '; 
								echo esc_html( $products_list->qty );
								
								//custom
								$product_style = get_post_meta($product_id, 'product_style', true);
								if($product_style){
									echo wp_kses_post( ' (' . $product_style . ')' );
								}
								//custom
								if ( $display_shippment_item_price ) {
									echo ' - ';
									echo wp_kses_post( $order->get_formatted_line_subtotal( $order_item ) );
								}
								echo '<div style="margin-top:10px;"><span style="font-size: 90%;">' . esc_html( $tracking_item['formatted_tracking_provider'] ) . '<a style="font-size: 90%;margin: 0 10px 0 5px;text-decoration: none;" href=' . esc_url( $tracking_item['ast_tracking_link'] ) . '><span>' . esc_html( $tracking_item['tracking_number'] ) . '</span></a></span> </div>';
								?>
							</td>
							<td class="td <?php echo esc_attr( $last_child_class ); ?>" style="text-align:right;">
								<?php echo '<a class="button track-button" href=' . esc_url( $tracking_item['ast_tracking_link'] ) . '><span>' . esc_html( $fluid_button_text ) . '</span></a>'; ?>
							</td>	
						</tr>	
						<?php
					}
					?>
				</tbody>
			</table>
		</div>		
		<?php	
		}
	}
	?>
</div>
<?php 
do_action( 'wcast_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
