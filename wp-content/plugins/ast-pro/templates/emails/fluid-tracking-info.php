<?php 

if ( $tracking_items ) : 

$ast = new AST_Pro_Actions();
$wcast_customizer_settings = new ast_pro_customizer_settings();

$hide_trackig_header = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'hide_trackig_header', '' );
$shipment_tracking_header = $ast->get_option_value_from_array( 'tracking_info_settings', 'header_text_change', 'Tracking Information' );
$shipment_tracking_header_text = $ast->get_option_value_from_array( 'tracking_info_settings', 'additional_header_text', '' );
$fluid_table_layout = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_table_layout', $wcast_customizer_settings->defaults['fluid_table_layout'] );
$border_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_table_border_color', $wcast_customizer_settings->defaults['fluid_table_border_color'] );
$border_radius = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_table_border_radius', $wcast_customizer_settings->defaults['fluid_table_border_radius'] );
$background_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_table_background_color', $wcast_customizer_settings->defaults['fluid_table_background_color'] );
$table_padding = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_table_padding', $wcast_customizer_settings->defaults['fluid_table_padding'] );
$fluid_hide_provider_image = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'fluid_hide_provider_image', $wcast_customizer_settings->defaults['fluid_hide_provider_image'] );
$fluid_hide_shipping_date = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'fluid_hide_shipping_date', $wcast_customizer_settings->defaults['fluid_hide_shipping_date'] );
$button_background_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_background_color', $wcast_customizer_settings->defaults['fluid_button_background_color'] );
$button_font_color = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_font_color', $wcast_customizer_settings->defaults['fluid_button_font_color'] );

$button_radius = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_radius', $wcast_customizer_settings->defaults['fluid_button_radius'] );
$button_expand = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'fluid_button_expand', $wcast_customizer_settings->defaults['fluid_button_expand'] );
$fluid_button_text = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_button_text', $wcast_customizer_settings->defaults['fluid_button_text'] );

$fluid_button_size = $ast->get_checkbox_option_value_from_array( 'tracking_info_settings', 'fluid_button_size', $wcast_customizer_settings->defaults['fluid_button_size'] );
$button_font_size = ( 'large' == $fluid_button_size ) ? 16 : 14 ;
$button_padding = ( 'large' == $fluid_button_size ) ? '12px 25px' : '10px 15px' ;


$order_details = wc_get_order( $order_id );

$ast_preview = ( isset( $_REQUEST['wcast-tracking-preview'] ) && '1' === $_REQUEST['wcast-tracking-preview'] ) ? true : false;
$text_align = is_rtl() ? 'right' : 'left'; 

$shipment_status = get_post_meta( $order_id, 'shipment_status', true);

	if ( !empty( $order_details ) ) {
		$order_status = $order_details->get_status();
	} else {
		$order_status = 'completed';
	}

	if ( $ast_preview ) {
		$hide_header_class = ( $hide_trackig_header ) ? 'hide' : '' ;
		?>
		<h2 class="header_text <?php esc_html_e( $hide_header_class ); ?>" style="margin: 0;text-align:<?php esc_html_e( $text_align ); ?>;">
			<?php esc_html_e( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( $shipment_tracking_header, 'ast-pro' ) ) ); ?>
		</h2>
		<?php 
	} else { 
		$hide_header = ( $hide_trackig_header ) ? 'display:none' : '' ;
		?>
		<h2 class="header_text" style="margin: 0;text-align:<?php esc_html_e( $text_align ); ?>;<?php esc_html_e( $hide_header ); ?>">
			<?php esc_html_e( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( $shipment_tracking_header, 'ast-pro' ) ) ); ?>
		</h2>
	<?php } ?>
	
<p style="margin: 0;" class="addition_header"><?php echo wp_kses_post( $shipment_tracking_header_text ); ?></p>

<?php if ( 2 == $fluid_table_layout ) { ?>
	<div class="fluid_container">
		<table class="fluid_table fluid_table_2cl">
		<?php 
		foreach ( $tracking_items as $key => $tracking_item ) { 	
			
			if ( '' != $tracking_item[ 'formatted_tracking_provider' ] ) {
				$ast_provider_title = apply_filters( 'ast_provider_title', esc_html( $tracking_item[ 'formatted_tracking_provider' ] )); 
			} else {
				$ast_provider_title = apply_filters( 'ast_provider_title', esc_html( $tracking_item[ 'tracking_provider' ] ));
			} 
			
			
			?>
				<tr class="fluid_2cl_tr">
					<td class="fluid_2cl_td_image">
						<div class="fluid_provider">
							<?php if ( !$fluid_hide_provider_image ) { ?>
								<div class="fluid_provider_img">
									<img src="<?php echo esc_url( $tracking_item['tracking_provider_image'] ); ?>"></img>
								</div>
							<?php } ?>
							<div class="provider_name">
								<div>
									<span class="tracking_provider"><?php esc_html_e( $ast_provider_title ); ?></span>
									<a class="tracking_number" href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>"><?php esc_html_e( $tracking_item['tracking_number'] ); ?></a>
								</div>								
							</div>		
							<?php if ( !$fluid_hide_shipping_date ) { ?>
								<div class="order_status <?php esc_html_e( $order_status ); ?>">
									<?php 
									esc_html_e( 'Shipped on:', 'ast-pro' ); 
									echo '<span> ' . esc_html( date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ) ) . '</span>'; 
									?>
								</div>	
							<?php } ?>							
							<?php do_action( 'ast_fluid_left_cl_end', $tracking_item, $order_id ); ?>
						</div>		
					</td>
					<td class="fluid_2cl_td_action">
						<a href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>" class="button track-button"><?php esc_html_e( $fluid_button_text ); ?></a>
					</td>
				</tr>							
			<?php
		}	 
		?>
		</table>
	</div>
<?php } else { ?>
	<div class="fluid_container" style="margin-top:10px;">
	
		<?php 
		foreach ( $tracking_items as $key => $tracking_item ) { 	
		
			if ( '' != $tracking_item[ 'formatted_tracking_provider' ] ) {
				$ast_provider_title = apply_filters( 'ast_provider_title', esc_html( $tracking_item[ 'formatted_tracking_provider' ] )); 
			} else {
				$ast_provider_title = apply_filters( 'ast_provider_title', esc_html( $tracking_item[ 'tracking_provider' ] ));
			} 
			?>
		<table class="fluid_table fluid_table_1cl">	
			<tr>
				<td>
					<div class="fluid_provider">
						<?php if ( !$fluid_hide_provider_image ) { ?>
							<div class="fluid_provider_img">
								<img src="<?php esc_html_e( $tracking_item['tracking_provider_image'] ); ?>"></img>
							</div>
						<?php } ?>
						<div class="provider_name">
							<div>
								<span class="tracking_provider"><?php esc_html_e( $ast_provider_title ); ?></span>
								<a class="tracking_number" href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>"><?php esc_html_e( $tracking_item['tracking_number'] ); ?></a>
							</div>							
						</div>
						<?php if ( !$fluid_hide_shipping_date ) { ?>
							<div class="order_status <?php esc_html_e( $order_status ); ?>">
								<?php 
								esc_html_e( 'Shipped on:', 'ast-pro' ); 
								echo '<span> ' . esc_html( date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ) ) . '</span>'; 
								?>
							</div>	
						<?php } ?>
						<?php do_action( 'ast_fluid_left_cl_end', $tracking_item, $order_id ); ?>
					</div>		
				</td>				
			</tr>
			<tr>
				<td class="fluid_td_1_cl_action">
					<a href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>" class="button track-button"><?php esc_html_e( $fluid_button_text ); ?></a>
				</td>
			</tr>	
		</table>	
		<?php }	?>	
	</div>
<?php } ?>

<div class="clearfix"></div>

<style>
.clearfix{
	display: block;
	content: '';
	clear: both;
}
.fluid_table_2cl{
	width: 100%;	
	margin: 10px 0 !important;
	border: 1px solid <?php esc_html_e( $border_color ); ?> !important;
	border-radius: <?php esc_html_e( $border_radius ); ?>px !important;    
	background: <?php esc_html_e( $background_color ); ?> !important;	
	border-spacing: 0 !important;
}

.fluid_table_1cl{
	border-collapse: separate !important;
	border: 1px solid <?php esc_html_e( $border_color ); ?> !important;
	border-radius: <?php esc_html_e( $border_radius ); ?>px !important;
	background: <?php esc_html_e( $background_color ); ?> !important;
	margin-bottom: 10px !important;   
	width: 48% !important;		
	margin: 0 10px 0 0;
	float: left;
}
.fluid_table_2cl .fluid_2cl_tr td.fluid_2cl_td_action{	
	text-align: right;
}

.fluid_table td{
	padding: <?php esc_html_e( $table_padding ); ?>px !important;
}
@media screen and (max-width: 460px) {
	.fluid_table_1cl{
		width: 100% !important;
	}
	.fluid_table td{
		display: block;
		flex: 1;
	}
	.fluid_table_2cl .fluid_2cl_tr td.fluid_2cl_td_action{	
		text-align: left !important;
	}
}
.fluid_provider_img {    
	vertical-align: top;
	display: inline-block;
	margin-right: 5px;
	width: 14%;
}
.fluid_provider_img img{
	width:100%;
	max-width: 40px;
	border-radius: 5px;
}
.provider_name{
	display: inline-block;    
	vertical-align: top;	
}
<?php if ( !$fluid_hide_provider_image ) { ?>
	.provider_name{
		width: 80%;		
	}
<?php } ?>
.tracking_provider{
	word-break: break-word;
	margin-right: 5px;	
	font-size: 14px;
	display: block;
}
.tracking_number{
	color: #03a9f4;
	text-decoration: none;    
	font-size: 14px;
	line-height: 19px;
	display: block;
	margin-top: 4px;
}
.order_status{
	font-size: 12px;    
	margin: 8px 0 0;
	font-style: italic;
}
.fluid_table td.fluid_td_1_cl_action{
	padding-top: 0 !important;
}
a.button.track-button {
	background: <?php esc_html_e( $button_background_color ); ?>;
	color: <?php esc_html_e( $button_font_color ); ?> !important;
	padding: <?php esc_html_e( $button_padding ); ?>;
	text-decoration: none;
	display: inline-block;
	border-radius: <?php esc_html_e( $button_radius ); ?>px;
	margin-top: 0;
	font-size: <?php esc_html_e( $button_font_size ); ?>px !important;
	text-align: center;
	min-height: 10px;
	white-space: nowrap;
}
<?php 
	if ( 1 == $fluid_table_layout ) {
		?>
	.fluid_provider_img{
		width: 17%;
	}
	.provider_name{
		width: 75%;
	}
	.tracking_number{
		overflow: hidden;    
		text-overflow: ellipsis;
	}
<?php 
	}
	if ( $button_expand && 1 == $fluid_table_layout ) {
		?>
a.button.track-button {
	display: block;
}

<?php 
	}
	if ( $fluid_hide_provider_image && 2 == $fluid_table_layout && !$fluid_hide_shipping_date ) { 
		?>
	.tracking_provider,.tracking_number{
		display: inline-block;
	}
<?php } ?>
</style>

<?php
endif;
