<div class="license_connection_section">
<?php 
$zorem_license_connected = get_option( 'zorem_license_connected', 0 );
$zorem_license_email = get_option( 'zorem_license_email', '' );
$current_url = esc_url( admin_url( '/admin.php?page=woocommerce-advanced-shipment-tracking&tab=license' ) ); 

if ( $this->get_license_status() ) {
	?>
	<h3 class="licnese-inner-heading"><?php esc_html_e( 'Status:', 'ast-pro' ); ?><span class="success">Active</span></h3>
	<p><?php esc_html_e('Want to deactivate the license for any reason?', 'ast-pro'); ?></p>
	<form method="post" id="wc_ast_pro_addons_form" class="addons_inner_container" action="" enctype="multipart/form-data">
		<input type="hidden" name="license_key" id="license_key" value="<?php esc_html_e( $this->get_license_key() ); ?>">
		<?php wp_nonce_field( 'wc_ast_pro_addons_form', 'wc_ast_pro_addons_form_nonce' ); ?>
		<input type="hidden" id="ast-pro-license-action" name="action" value="<?php echo ( $this->get_license_status() ) ? esc_html( $this->get_item_code() ) . '_license_deactivate' : esc_html( $this->get_item_code() ) . '_license_activate'; ?>" />
		<button name="save" class="button-primary btn_green2" type="submit" value="Deactivate"><?php esc_html_e( 'Deactivate', 'ast-pro' ); ?></button>
	</form>	
	<?php
} else if ( 1 == $zorem_license_connected && '' != $zorem_license_email ) {
	?>
	<h3 class="licnese-inner-heading"><?php esc_html_e( 'Activate License', 'ast-pro' ); ?></h3>
	<p><?php esc_html_e('Activate your license to receive automatic updates and premium support', 'ast-pro'); ?></p>
	<a href="https://www.zorem.com/my-account/license-activation/?product_id=<?php echo esc_html( $this->get_product_id() ); ?>&redirect_url=<?php echo urlencode( $current_url ); ?>" class="button-primary btn_ast2"><?php esc_html_e( 'Activate License', 'ast-pro' ); ?></a>
	<?php
} else {	
	?>
	<h3 class="licnese-inner-heading"><?php esc_html_e( 'Activate License', 'ast-pro' ); ?></h3>
	<p><?php esc_html_e('Activate your license to receive automatic updates and premium support.', 'ast-pro'); ?></p>
	<a href="https://www.zorem.com/my-account/license-activation/?product_id=<?php echo esc_html( $this->get_product_id() ); ?>&redirect_url=<?php echo urlencode( $current_url ); ?>" class="button-primary btn_ast2"><?php esc_html_e( 'Connect & Activate', 'ast-pro' ); ?></a>
	<?php
}
?>
</div>
<!--form method="post" id="wc_ast_pro_addons_form" class="addons_inner_container" action="" enctype="multipart/form-data">
	<table class="ast-license-form wp-list-table widefat fixed">
		<tbody>
			<tr class="wp-list-table__row is-ext-header">
				<td class="wp-list-table__ext-details">
					<div class="wp-list-table__ext-title">
						Advanced Shipment Tracking Pro	
					</div>
				</td>
				<td class="wp-list-table__ext-actions">
					<div class="wp-list-table__ext-description">
						<input class="input-text regular-input ast_licence_key" type="text" name="license_key" id="license_key" value="<?php esc_html_e( $this->get_license_key() ); ?>">
					</div>
					<div class="submit">	
						<?php														
						if ( $this->get_license_status() ) { 
							?>
							<button name="save" class="button-primary btn_green2" type="submit" value="Deactivate"><?php esc_html_e( 'Deactivate', 'ast-pro' ); ?></button>
						<?php } else { ?>                                                                                                                    
							<button name="save" class="button-primary btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Activate', 'ast-pro' ); ?></button>
						<?php 
						}
						?>
						<p class="pesan" id='ast_pro_license_message'></p>						
						<?php wp_nonce_field( 'wc_ast_pro_addons_form', 'wc_ast_pro_addons_form_nonce' ); ?>
						<input type="hidden" id="ast-pro-license-action" name="action" value="<?php echo ( $this->get_license_status() ) ? esc_html( $this->get_item_code() ) . '_license_deactivate' : esc_html( $this->get_item_code() ) . '_license_activate'; ?>" />
					</div>		
				</td>
			</tr>
		</tbody>
	</table>
</form-->
