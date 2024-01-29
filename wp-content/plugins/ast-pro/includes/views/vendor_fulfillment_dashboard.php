<input type="hidden" id="nonce_fullfillment_dashbaord" value="<?php esc_html_e( wp_create_nonce( 'nonce_fullfillment_dashbaord' ) ); ?>">

<p class="fulfillment_search_box">
	<input type="search" id="fulfillment_search_input" name="fulfillment_search_input" value="" placeholder="<?php esc_html_e('Order ID', 'ast-pro'); ?>">
	<button type="submit" id="vendor_fulfillment_search_submit" class="button fulfillment_search_submit"><span class="dashicons dashicons-search"></span><span><?php esc_html_e('Search', 'woocommerce'); ?></span></button>
</p>

<select class="fulfillment_filter_select" id="vendor_fulfillment_filter">
	<option value="unfulfilled"><?php esc_html_e('Unfulfilled', 'ast-pro'); ?></option>
	<option value="recently_unfulfilled"><?php esc_html_e('Fulfilled', 'ast-pro'); ?></option>
</select>
<input type="hidden" name="unfulfilled_order_status" id="unfulfilled_order_status" value="all">
<table class="widefat fullfilments_table hover" cellspacing="0" id="vendor_fullfilments_table" style="width: 100%;">
	<thead>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Order Date', 'ast-pro'); ?></th>
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Order', 'woocommerce'); ?></th>										
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Customer', 'ast-pro'); ?></th>	
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Items', 'ast-pro'); ?></th>			
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipping Method', 'ast-pro'); ?></th>			
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipping To', 'ast-pro'); ?></th>			
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_html_e('Shipment Tracking', 'ast-pro'); ?></th>			
			<th id="columnname" class="manage-column column-columnname text-right" scope="col"><?php esc_html_e('Actions', 'ast-pro'); ?></th>
		</tr>
	</thead>
	<tbody></tbody>				
</table>
