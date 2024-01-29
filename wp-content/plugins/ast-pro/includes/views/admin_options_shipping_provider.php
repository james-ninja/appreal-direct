<?php
/**
 * Html code for shipping providers tab
 */

$wc_ast_api_key = get_option( 'wc_ast_api_key' );
$upload_dir   = wp_upload_dir();	
$ast_directory = $upload_dir['baseurl'] . '/ast-shipping-providers/';
if ( isset( $_GET['open'] ) && 'synch_providers' == $_GET['open'] ) {
	?>
	<script>
		jQuery( document ).ready(function() {	
			jQuery('.sync_provider_popup').show();
		});
	</script>
<?php 
}
?>
<section id="content1" class="tab_section">	
	<div class="tab_container_without_bg">	
		<div class="provider_top">																					
			<div class="search_section">				
				<span class="dashicons dashicons-search search-icon"></span>
				<input class="provider_search_bar" type="text" name="search_provider" id="search_provider" placeholder="<?php esc_html_e( 'Search by provider / country', 'ast-pro' ); ?>">		
			</div>
											
			<div class="provider_settings">
				<a href="javaScript:void(0);" class="add_custom_provider provider_settings_icon" id="add-custom"><span class="dashicons dashicons-plus-alt"></span></a>	
				<a href="javaScript:void(0);" class="sync_providers provider_settings_icon"><span class="dashicons dashicons-update"></span></a>
				<input class="ast-tgl ast-tgl-flat" id="reset_providers" name="reset_providers" type="checkbox" value="1"/>
				<label class="ast-tgl-btn" for="reset_providers"></label>
			</div>
		</div>		
		
		<div class="provider_list">	
			<?php 
			if ( $default_shippment_providers ) {
				echo wp_kses_post( $this->get_provider_html( $default_shippment_providers, 'all' ) );
			}
			?>
		</div>
		
		<input type="hidden" id="nonce_shipping_provider" value="<?php esc_html_e( wp_create_nonce( 'nonce_shipping_provider' ) ); ?>">
		
		<div id="" class="popupwrapper add_provider_popup" style="display:none;">
			<div class="popuprow">
				<div class="popup_header">
					<h3 class="popup_title"><?php esc_html_e( 'Add Custom Shipping Provider', 'ast-pro'); ?></h2>						
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body">
					<form id="add_provider_form" method="POST" class="add_provider_form">
						<div class="form-field form-50">
							<label><?php esc_html_e( 'Provider Name', 'ast-pro' ); ?></label>
							<input type="text" name="shipping_provider" class="shipping_provider" placeholder="<?php esc_html_e( 'Custom Provider', 'ast-pro' ); ?>">
						</div>
						<div class="form-field form-50 margin-0">
							<label><?php esc_html_e( 'Custom Display Name', 'ast-pro' ); ?> <span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( "The custom display name will show in the tracking info section on the customer order emails, my-account, and TrackShip's tracking page and email notifications", 'ast-pro' ); ?>"></span> </label>
							<input type="text" name="shipping_display_name" class="shipping_display_name" value="" placeholder="<?php esc_html_e( 'White Label Provider Name', 'ast-pro' ); ?>">
						</div>
						<div class="form-field form-50">
							<label><?php esc_html_e( 'Country', 'ast-pro' ); ?></label>
							<select class="select wcast_shipping_country shipping_country" name="shipping_country">
								<option value=""><?php esc_html_e( 'Shipping Country', 'ast-pro' ); ?></option>
								<option value="Global"><?php esc_html_e( 'Global', 'ast-pro' ); ?></option>
								<?php 
								foreach ( $countries as $key=>$val ) { 
									?>
									<option value="<?php esc_html_e( $key ); ?>" ><?php esc_html_e( $val, 'ast-pro' ); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-field form-50 margin-0">
							<label><?php esc_html_e( 'Logo image', 'ast-pro' ); ?></label>
							<input type='text' placeholder='Image' name='thumb_url' class='image_path thumb_url' value=''>
							<input type='hidden' name='thumb_id' class='image_id thumb_id' placeholder="Image" value='' style="">
							<input type="button" class="button upload_image_button" value="<?php esc_html_e( 'Upload' , 'ast-pro' ); ?>" />
						</div>
						<div class="form-field">
							<label><?php esc_html_e( 'Custom URL', 'ast-pro' ); ?></label>
							<input type="text" name="tracking_url" class="tracking_url" placeholder="<?php esc_html_e( 'My White Label Provider URL', 'ast-pro' ); ?>	 ">
						</div>
						<div class="form-field custom_provider_instruction">
							<p>
							<?php
							/* translators: %: replace with tracking number, postal_code and country_code */
							esc_html_e( 'You can use the variables %number%, %postal_code% and %country_code% in the URL, for more info, check our ', 'ast-pro' );
							?>
							<?php
							/* translators: %s: replace with Shippinh provider page url */
							echo sprintf(__('<a href="%s" target="blank">documentation</a>', 'ast-pro'), 'http://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/setting-shipping-providers/#adding-custom-shipping-provider'); 
							?>
							</p>
						</div>
						<input type="hidden" name="add_custom_shipment_provider_nonce" id="add_custom_shipment_provider_nonce" value="<?php esc_html_e( wp_create_nonce( 'add_custom_shipment_provider' ) ); ?>">
						<input type="hidden" name="action" value="add_custom_shipment_provider">
						<input type="submit" name="Submit" value="<?php esc_html_e( 'Add Custom Provider', 'ast-pro' ); ?>" class="button-primary btn_ast2">			
					</form>
				</div>						
			</div>
			<div class="popupclose"></div>
		</div>
		
		<div id="" class="popupwrapper edit_provider_popup" style="display:none;">			
			<div class="popuprow">
				<div class="popup_header">
					<h3 class="popup_title"><?php esc_html_e( 'Edit Shipping Provider', 'ast-pro'); ?></h2> - <h3 class="popup_title edit_provider_title"></h2>
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>	
				<div class="popup_body">							
					<form id="edit_provider_form" method="POST" class="edit_provider_form">
						<div class="form-field form-50 set_default_container">
							<label><input type="checkbox" name="make_provider_default" class="make_provider_default" value="1">&nbsp;&nbsp;<span><?php esc_html_e( 'Set default', 'ast-pro' ); ?></span></label>							
						</div>
						<div class="form-field form-50">
							<label><?php esc_html_e( 'Provider Name', 'ast-pro' ); ?></label>
							<input type="text" name="shipping_provider" class="shipping_provider" value="" placeholder="<?php esc_html_e( 'Shipping Provider', 'ast-pro' ); ?>">
						</div>											
						<div class="form-field form-50 margin-0">
							<label><?php esc_html_e( 'Custom display name', 'ast-pro' ); ?> <span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( "The custom display name will show in the tracking info section on the customer order emails, my-account, and TrackShip's tracking page and email notifications", 'ast-pro' ); ?>"></span> </label>
							<input type="text" name="shipping_display_name" class="shipping_display_name" value="" placeholder="<?php esc_html_e( 'White Label Provider Name', 'ast-pro' ); ?>">
						</div>
						<div class="form-field api_provider_name_container">
							<label><?php esc_html_e( 'Custom API name', 'ast-pro' ); ?> <span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( 'Add API name aliases to map Shipping providers names with the provider names that are updated in the shipment tracking API by external shipping services', 'ast-pro' ); ?>"></span></label>
							<div class="api_provider_div">									
								<input type="text" name="api_provider_name[]" class="api_provider_name" value="" placeholder="<?php esc_html_e( 'API Name', 'ast-pro' ); ?>">
								<?php do_action('add_more_api_provider'); ?>									
							</div>
						</div>	
						<div class="form-field form-50">
							<label><?php esc_html_e( 'Country', 'ast-pro' ); ?></label>
							<select class="select wcast_shipping_country shipping_country" name="shipping_country">
								<option value=""><?php esc_html_e( 'Shipping Country', 'ast-pro' ); ?></option>
								<option value="Global"><?php esc_html_e( 'Global', 'ast-pro' ); ?></option>
								<?php foreach ( $countries as $key=>$val ) { ?>
										<option value="<?php esc_html_e( $key ); ?>" ><?php esc_html_e( $val, 'ast-pro'); ?></option>
									<?php } ?>
							</select>
						</div>
						<div class="form-field form-50 margin-0">
							<label><?php esc_html_e( 'Logo image', 'ast-pro' ); ?></label>
							<input type='text' placeholder='Image' name='thumb_url' class='image_path thumb_url' value=''>
							<input type='hidden' name='thumb_id' class='image_id thumb_id' placeholder="Image" value=''>
							<input type="button" class="button upload_image_button" value="<?php esc_html_e( 'Upload' , 'ast-pro' ); ?>" />
						</div>
						<div class="form-field">
							<label><?php esc_html_e( 'Custom URL', 'ast-pro' ); ?></label>
							<input type="text" name="tracking_url" class="tracking_url" placeholder="Tracking URL">
						</div>
						<div class="form-field custom_provider_instruction">
							<p>
							<?php
								/* translators: %: replace with tracking number, postal_code and country_code */
								esc_html_e( 'You can use the variables %number%, %postal_code% and %country_code% in the URL, for more info, check our ', 'ast-pro' );
							?>
							<?php
							/* translators: %s: replace with Shipping provider page link */		
							echo sprintf(__('<a href="%s" target="blank">documentation</a>', 'ast-pro'), 'http://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/setting-shipping-providers/#adding-custom-shipping-provider'); 
							?>
							</p>
						</div>
						<input type="hidden" name="action" value="update_custom_shipment_provider">
						<input type="hidden" id="nonce_edit_shipping_provider" name="nonce_edit_shipping_provider" value="<?php esc_html_e( wp_create_nonce( 'nonce_edit_shipping_provider' ) ); ?>">
						<input type="hidden" name="provider_type" id="provider_type" value="">
						<input type="hidden" name="provider_id" id="provider_id" value="">							
						<input type="submit" name="Submit" value="<?php esc_html_e( 'Update' , 'ast-pro' ); ?>" class="button-primary btn_ast2">
						<a href="javascript:void(0);" class="reset_default_provider"><?php esc_html_e( 'Reset' , 'ast-pro' ); ?></a>
					</form>
				</div>	
			</div>
			<div class="popupclose"></div>
		</div>
		
		<div id="" class="popupwrapper sync_provider_popup" style="display:none;">
			<div class="popuprow">
				<div class="popup_header">
					<h3 class="popup_title"><?php esc_html_e( 'Sync Shipping Providers', 'ast-pro'); ?></h2>						
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>	
				<div class="popup_body">	
					<p class="sync_message"><?php esc_html_e( 'Syncing the shipping providers list add or updates the pre-set shipping providers and will not effect custom shipping providers.', 'ast-pro' ); ?></p>
					<ul class="synch_result">
						<li class="providers_added"><?php esc_html_e( 'Providers Added', 'ast-pro' ); ?> - <span></span></li>
						<li class="providers_updated"><?php esc_html_e( 'Providers Updated', 'ast-pro' ); ?> - <span></span></li>
						<li class="providers_deleted"><?php esc_html_e( 'Providers Deleted', 'ast-pro' ); ?> - <span></span></li>
					</ul>
					<p class="reset_db_message" style="display:none;"><?php esc_html_e( 'Shipping providers database reset successfully.', 'ast-pro' ); ?></p>
					<fieldset class="reset_db_fieldset">						
						<label><input type="checkbox" id="reset_tracking_providers" name="reset_tracking_providers" value="1"><?php esc_html_e( 'Reset providers database, it will reset all your shipping provider database', 'ast-pro' ); ?></label>	
					</fieldset>
					<button class="sync_providers_btn button-primary btn_ast2"><?php esc_html_e( 'Sync Shipping Providers', 'ast-pro' ); ?></button>
					<button class="close_synch_popup button-primary btn_ast2"><?php esc_html_e( 'Close', 'woocommerce' ); ?></button>
					<div class="spinner" style=""></div>
				</div>
			</div>	
			<div class="popupclose"></div>
		</div>
	</div>
</section>
