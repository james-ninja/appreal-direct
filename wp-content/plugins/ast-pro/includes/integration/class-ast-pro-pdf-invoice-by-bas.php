<?php
add_filter( 'wpi_custom_information', 'ast_pro_wpi_custom_information_filter', 10, 2 );

/**
 * Display shipment info in PDF Invoices. 
 * Plugin - https://wordpress.org/plugins/woocommerce-pdf-invoices/
 */
if ( !function_exists( 'ast_pro_wpi_custom_information_filter' ) ) {
	function ast_pro_wpi_custom_information_filter( $blank_value, $invoice ) {
		
		$order = $invoice->order;
		$order_id = $order->get_id();
		
		if ( !class_exists( 'AST_Pro_Actions' ) ) {
			return;
		}	
		
		$wast = AST_Pro_Actions::get_instance();
		$tracking_items = $wast->get_tracking_items( $order_id, true );
		
		if ( $tracking_items ) {
			ob_start();
			?>
			<h2 class="header_text"><?php esc_html_e( 'Track your order', 'ast-pro' ); ?></h2><br/>
			<ul style="list-style: none;">
			<?php
			foreach ( $tracking_items as $tracking_item ) {
				$date_shipped = isset( $tracking_item['date_shipped'] ) ? $tracking_item['date_shipped'] : gmdate('Y-m-d');
				?>
				<li class="tracking">
					<?php echo esc_html( $tracking_item['formatted_tracking_provider'] ); ?> - 
					<a href="<?php echo esc_url( $tracking_item['ast_tracking_link'] ); ?>" target="_blank"><?php echo esc_html( $tracking_item['tracking_number'] ); ?></a>
				</li>
				<?php
			}
			?>
			</ul>			
			<?php 
			$html = ob_get_clean();	
			return $html;	
		}	
	}
}
