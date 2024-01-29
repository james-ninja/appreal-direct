<form method="post" id="wc_ast_pro_addons_form" class="addons_inner_container" action="" enctype="multipart/form-data">
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
</form>
