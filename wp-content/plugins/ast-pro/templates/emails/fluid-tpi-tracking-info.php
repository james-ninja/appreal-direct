<?php 

if ( $tracking_items ) : 

$ast = new AST_Pro_Actions();
$wcast_customizer_settings = new ast_pro_customizer_settings();

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
$button_padding = ( 'large' == $fluid_button_size ) ? '12px 25px' : '10px 20px' ;

$order_details = wc_get_order( $order_id );

$ast_preview = ( isset( $_REQUEST['wcast-tracking-preview'] ) && '1' === $_REQUEST['wcast-tracking-preview'] ) ? true : false;
$text_align = is_rtl() ? 'right' : 'left'; 
	?>

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
			<td>
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
.fluid_table_2cl .fluid_2cl_tr td.fluid_2cl_td_action{
	text-align: right;
}

.fluid_table td{
	padding: <?php esc_html_e( $table_padding ); ?>px !important;
}
@media screen and (max-width: 460px) {
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
a.button.track-button {
	background: <?php esc_html_e( $button_background_color ); ?>;
	color: <?php esc_html_e( $button_font_color ); ?> !important;
	padding: <?php esc_html_e( $button_padding ); ?>;
	text-decoration: none;
	display: inline-block;
	border-radius: <?php esc_html_e( $button_radius ); ?>px;
	margin-top: 0;
	font-size: <?php esc_html_e( $button_font_size ); ?>px;
	text-align: center;
	min-height: 10px;
	white-space: nowrap;
}
</style>

<?php
endif;
