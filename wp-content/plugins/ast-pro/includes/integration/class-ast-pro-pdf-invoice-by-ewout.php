<?php

add_action( 'wpo_wcpdf_before_order_details', 'tracking_display_in_invoice', 0, 4 );

/**
 * Display shipment info in PDF Invoices & Packing slips.
 *
 * @version 1.6.8
 *
 * @param WC_Order $order         Order object.
 * Plugin - https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/
 */
if ( !function_exists( 'tracking_display_in_invoice' ) ) { 
	function tracking_display_in_invoice( $template_type, $order ) {
			
		/* Remove this comment if you don't want to display tracking information in invoice PDF
			if($template_type == 'invoice' )return;
		*/
		
		if ( 'packing-slip' == $template_type ) {
			return;
		}
		
		if ( 'proforma' == $template_type ) {
			return;
		}
		
		if ( 'credit-note' == $template_type ) {
			return;
		}
		
		$order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		
		//check if AST is active
		if ( !class_exists( 'AST_Pro_Actions' ) ) {
			return;
		}	
		
		$ast = AST_Pro_Actions::get_instance();
		$tracking_items = $ast->get_tracking_items( $order_id, true );	
		
		if ( $tracking_items ) {
			?>
			<h2 class="header_text"><?php echo esc_html( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Track your order', 'ast-pro' ) ) ); ?></h2><br/>
			<table class="order-details">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Provider', 'ast-pro' ); ?></th>
						<th><?php esc_html_e( 'Tracking Number', 'ast-pro' ); ?></th>
						<th><?php esc_html_e( 'Shipped Date', 'ast-pro' ); ?></th>							
						<th><?php esc_html_e( 'Track', 'ast-pro' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $tracking_items as $tracking_item ) {
					$date_shipped = isset( $tracking_item['date_shipped'] ) ? $tracking_item['date_shipped'] : gmdate('Y-m-d'); 
					?>
					<tr class="tracking">
						<td class=""><?php echo esc_html( $tracking_item['formatted_tracking_provider'] ); ?></td>
						<td class=""><?php echo esc_html( $tracking_item['tracking_number'] ); ?></td>
						<td class="">
							<time datetime="<?php esc_html_e( gmdate( 'Y-m-d', $date_shipped ) ); ?>" title="<?php esc_html_e( gmdate( 'Y-m-d', $date_shipped ) ); ?>"><?php esc_html_e( date_i18n( get_option( 'date_format' ), $date_shipped ) ); ?></time>
						</td>						
						<td class="">
							<a href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>" target="_blank"><?php esc_html_e( 'Track', 'ast-pro' ); ?></a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>	
			<?php 
		}		
	}
}
