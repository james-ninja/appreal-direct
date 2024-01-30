<section id="unfulfilled_orders_content" class="tab_section">
	<div class="accordion_container fulfillment_table_container">
		<input type="hidden" id="nonce_fullfillment_dashbaord" value="<?php esc_html_e( wp_create_nonce( 'nonce_fullfillment_dashbaord' ) ); ?>">
		<?php
		$default_order_status = array();
		$default_order_status[] = 'wc-processing';
		$order_status = get_option( 'ast_order_display_in_fulfillment_dashboard', $default_order_status );
		$shipping_methods = WC()->shipping->get_shipping_methods();
		?>

		<p class="fulfillment_search_box">
			<input type="search" id="fulfillment_search_input" class="fulfillment_search_input" name="fulfillment_search_input" value="" placeholder="<?php esc_html_e('Search', 'ast-pro'); ?>">
			<button type="submit" id="fulfillment_search_submit" class="button fulfillment_search_submit"><span class="dashicons dashicons-search"></span></button>
		</p>
		<!--select class="fulfillment_filter_select" id="shipping_method_filter">
			<option value=""><?php esc_html_e('All Shipping Methods', 'ast-pro'); ?></option>
			<?php 
			foreach ( $shipping_methods as $key => $method ) {
				if ( 'local_pickup' == $method->id ) {
					continue;
				}	
				echo '<option value="' . esc_html( $method->method_title ) . '">' . esc_html( $method->method_title ) . '</option>';
			}
			?>
		</select-->

		<input type="hidden" name="unfulfilled_order_status" id="unfulfilled_order_status" value="all">
		<table class="widefat fullfilments_table hover" cellspacing="0" id="fullfilments_table" style="width: 100%;">
			<thead>
				<tr>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Order Date', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Order', 'woocommerce'); ?></th>							
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Status', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"></th>			
					<th id="columnname" class="manage-column column-destination" scope="col"><?php esc_html_e('Customer', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Items', 'ast-pro'); ?></th>												
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipping Method', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipping To', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname text-right" scope="col"><?php esc_html_e('Actions', 'ast-pro'); ?></th>
				</tr>
			</thead>
			<tbody></tbody>				
		</table>
	</div>
</section>

<section id="recently_fulfilled_content" class="tab_section">
	<div class="accordion_container fulfillment_table_container">
		<input type="hidden" id="nonce_fullfillment_dashbaord" value="<?php esc_html_e( wp_create_nonce( 'nonce_fullfillment_dashbaord' ) ); ?>">
		<?php $shipping_methods = WC()->shipping->get_shipping_methods(); ?>

		<p class="fulfillment_search_box">
			<input type="search" id="fulfilled_search_input" class="fulfillment_search_input" name="fulfillment_search_input" value="" placeholder="<?php esc_html_e('Search', 'ast-pro'); ?>">
			<button type="submit" id="fulfilled_search_submit" class="button fulfillment_search_submit"><span class="dashicons dashicons-search"></span></button>
		</p>
		
		<!--select class="fulfillment_filter_select" id="fulfilled_shipping_method_filter">
			<option value=""><?php esc_html_e('All Shipping Methods', 'ast-pro'); ?></option>
			<?php 
			foreach ( $shipping_methods as $key => $method ) {
				if ( 'local_pickup' == $method->id ) {
					continue;
				}	
				echo '<option value="' . esc_html( $method->method_title ) . '">' . esc_html( $method->method_title ) . '</option>';
			}
			?>
		</select-->

		<input type="hidden" name="fulfilled_order_status" id="fulfilled_order_status" value="all">
		<table class="widefat fullfilments_table hover" cellspacing="0" id="fulfilled_order_table" style="width: 100%;">
			<thead>
				<tr>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Order Date', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Order', 'woocommerce'); ?></th>							
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Status', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"></th>			
					<th id="columnname" class="manage-column column-destination" scope="col"><?php esc_html_e('Customer', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Items', 'ast-pro'); ?></th>												
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipping Method', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipping To', 'ast-pro'); ?></th>
					<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipment Tracking', 'ast-pro'); ?></th>
					<!--th id="columnname" class="manage-column column-columnname text-right" scope="col"><?php esc_html_e('Actions', 'ast-pro'); ?></th-->
				</tr>
			</thead>
			<tbody></tbody>				
		</table>
	</div>		
</section>
