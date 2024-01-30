<?php 

if ( $tracking_items ) : 

$ast = new AST_Pro_Actions();
$ast_customizer = Ast_Customizer::get_instance();
$ast_tpi = AST_Tpi::get_instance();


$hide_trackig_header = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'hide_trackig_header', '' );
$shipment_tracking_header = $ast->get_option_value_from_array( 'tracking_info_settings', 'header_text_change', 'Tracking Information' );
$shipment_tracking_header_text = $ast->get_option_value_from_array( 'tracking_info_settings', 'additional_header_text', '' );
$button_background_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_background_color', $ast_customizer->defaults['fluid_button_background_color'] );
$button_font_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_font_color', $ast_customizer->defaults['fluid_button_font_color'] );
$button_radius = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_radius', $ast_customizer->defaults['fluid_button_radius'] );
$fluid_button_text = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_text', $ast_customizer->defaults['fluid_button_text'] );
$fluid_hide_provider_image = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'fluid_hide_provider_image', $ast_customizer->defaults['fluid_hide_provider_image'] );

$fluid_button_size = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'fluid_button_size', $ast_customizer->defaults['fluid_button_size'] );
$button_font_size = ( 'large' == $fluid_button_size ) ? 16 : 14 ;
$button_padding = ( 'large' == $fluid_button_size ) ? '12px 20px' : '10px 15px' ;

$order_data = wc_get_order( $order_id );

$tpi_order = $ast_tpi->check_if_tpi_order( $tracking_items, $order_data );

	if ( !empty( $order_data ) ) {
	$order_status = $order_data->get_status();
	} else {
	$order_status = 'completed';
	}

	if ( 1 != $hide_trackig_header ) {
		?>
	<h2><?php esc_html_e( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', $shipment_tracking_header ) ); ?></h2>
<?php 
	}

	if ( '' != $hide_trackig_header) {
		?>
	<p><?php esc_html_e( $shipment_tracking_header_text ); ?></p>
<?php } ?>

<div class="fluid_section">
	<?php 
	foreach ( $tracking_items as $key => $tracking_item ) { 		
							
		if ( '' != $tracking_item[ 'formatted_tracking_provider' ] ) {
			$ast_provider_title = apply_filters( 'ast_provider_title', esc_html( $tracking_item[ 'formatted_tracking_provider' ] )); 
		} else {
			$ast_provider_title = apply_filters( 'ast_provider_title', esc_html( $tracking_item[ 'tracking_provider' ] ));
		}
		?>
		<div class="fluid_container">
			<div class="fluid_cl fluid_left_cl">
				<?php if ( !$fluid_hide_provider_image ) { ?>
						<div class="fluid_provider_img">
							<img src="<?php echo esc_url( $tracking_item['tracking_provider_image'] ); ?>"></img>
						</div>
					<?php } ?>
					<div class="provider_name">
						<div>
							<strong class="tracking_provider"><?php esc_html_e( $ast_provider_title ); ?></strong>
							<a class="tracking_number" href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>" target="_blank"><?php esc_html_e( $tracking_item['tracking_number'] ); ?></a>
						</div>						
						<div class="order_status <?php esc_html_e( $order_status ); ?>">
						<?php 
							esc_html_e( 'Shipped on:', 'ast-pro' ); 
							echo '<strong> ' . esc_html( date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ) ) . '</strong>'; 
						?>
						</div>
					</div>					
				<?php do_action( 'ast_fluid_left_cl_end', $tracking_item, $order_id ); ?>	
			</div>
			<div class="fluid_cl fluid_right_cl">
				<div>
					<a target="blank" href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>" class="button track-button" data-order="<?php esc_html_e( $order_id ); ?>" data-tracking="<?php echo esc_html( $tracking_item['tracking_number'] ); ?>"><?php esc_html_e( $fluid_button_text ); ?></a>
				</div>
			</div>
		</div>
		<?php if ( $tpi_order ) { ?>
			<div class="product_details">
				<h3><?php esc_html_e( 'Items in this shipment', 'ast-pro' ); ?></h3>
				<ul class="product_list">
					<?php
					if ( is_array( $tracking_item['products_list'] ) ) {
						foreach ( $tracking_item['products_list'] as $products_list ) {
							$product = wc_get_product( $products_list->product ); 
							
							if ( !is_object( $product ) ) {
								continue;
							}

							$image_size = array( 50, 50 );
							$product_id = $product->get_id();
							$image = $product->get_image( $image_size );
							
							echo '<li>' . wp_kses_post( $image ) . '<span><a target="_blank" href=' . esc_url( get_permalink( $products_list->product ) ) . '>' . esc_html( $product->get_name() ) . '</a> x ' . esc_html( $products_list->qty ) . '</span></li>';
						} 
					}
					?>
				</ul>	
			</div>	
		<?php } ?>
	<?php } ?>
</div>
<div id="" class="popupwrapper ts_tracking_popup" style="display:none;">
	<div class="popuprow">
		
	</div>	
	<div class="popupclose"></div>
</div> 
<style>
.fluid_section{
	margin-bottom: 10px;
}
.fluid_container {
	background: #fff;
	border: 1px solid #e0e0e0;	
	margin-top: 10px;
	display: flex;
	justify-content: space-between;
}
.fluid_container:after{
	content: "";
	clear: both;
	display: block;
}
.fluid_cl {	
	padding: 15px 20px;
}
.fluid_left_cl{
	flex-grow: 1;
}
.fluid_right_cl{
	margin-left: auto;
}
.fluid_cl ul li{
	margin-left: 0;
	margin-bottom: 0;
	font-size: 14px;
}
.fluid_provider_img{
	margin-right: 5px;
	width: 45px;
	display: inline-block;
	vertical-align: middle;
}
.fluid_provider_img img{
	border-radius: 5px;
	width: 100%;
}
.provider_name {
	display: inline-block;
	vertical-align: middle;
}

a.button.track-button {
	background: <?php esc_html_e( $button_background_color ); ?>;
	color: <?php esc_html_e( $button_font_color ); ?>;
	padding: <?php esc_html_e( $button_padding ); ?>;
	text-decoration: none;
	display: inline-block;
	border-radius: <?php esc_html_e( $button_radius ); ?>px;
	margin: 0;
	font-size: <?php esc_html_e( $button_font_size ); ?>px;
	text-align: center;
	margin-bottom: 0;    
	line-height: 20px;
	text-transform: none;
}
.tracking_provider{
	word-break: break-word;	
	margin-right: 5px;
}
<?php
	if ( $fluid_hide_provider_image && !$fluid_hide_shipping_date ) { 
		?>
	.tracking_provider,.tracking_number{
		display: inline-block;
	}
<?php } ?>
.tracking_number{
	color: #03a9f4;
	text-decoration: none;
}
.order_status{
	font-size: 13px;    
	margin: 0;
}
.product_details{
	padding: 15px 20px;
	background: #fff;
	border: 1px solid #e0e0e0;
	border-top: 0;
}
.product_details h3{
	font-size: 16px;
	line-height: 16px;
	font-weight: 600;
}
.product_details ul{
	list-style: none;
	margin-bottom: 0;
}
.product_details ul li{
	font-size: 14px;
	margin: 0 0 5px;
	border-bottom: 1px solid #ccc;
	padding: 0 0 5px;
}
.product_details ul li img{
	vertical-align: middle;
}
.product_details ul li span{
	margin: 0px 0px 0 10px;
	vertical-align: middle;
}
.product_details ul li:last-child {
	border-bottom: 0;
	margin: 0;
	padding: 0;
}
</style>
<?php
endif;
