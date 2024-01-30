<section id="integrations_content" class="tab_section">	
	<div class="accordion_container">
		<?php $ast_pro_integration = AST_Pro_Integration::get_instance(); ?>
		<form method="post" id="integrations_settings_form" action="" enctype="multipart/form-data">
			<div class="integration_list">
				<div class="provider-grid-row grid-row">
				<?php
				foreach ( $ast_pro_integration->integrations_settings_options() as $integrations_id => $array ) {
					$default = isset( $array['default'] ) ? $array['default'] : '';
					$checked = ( get_option( $integrations_id, $default ) ) ? 'checked' : '' ;
					$tgl_class = isset( $array['tgl_color'] ) ? 'ast-tgl-btn-green' : '';
					$disabled = isset( $array['disabled'] ) && true == $array['disabled'] ? 'disabled' : '';
					$settings = isset( $array['settings'] ) ? $array['settings'] : false ;	
					$documentation = isset( $array['documentation'] ) ? $array['documentation'] : null ;
				
					if ( $settings ) { 
						$settings_key_array = array_keys( $array['settings_fields'] );
						$settings_option = $settings_key_array['0'];
						$settings_option_value = get_option( $settings_option, $array['settings_fields'][$settings_option]['default'] );							
					}
					
					?>
					<div class="grid-item">					
						<div class="grid-top">
							<div class="grid-provider-img">
								<img class="provider-thumb" src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/<?php esc_html_e( $array['img'] ); ?>">
							</div>
							<div class="grid-provider-name">
								<span class="provider_name">
									<?php esc_html_e( $array['title'] ); ?>
								</span>	
							</div>	
							<div class="grid-provider-settings">
								<?php if ( $settings ) { ?>
									<span class="dashicons dashicons-admin-generic integration_settings" data-title="<?php esc_html_e( $array['title'] ); ?>" data-option="<?php esc_html_e( $settings_option ); ?>" 	data-option-value="<?php esc_html_e( $settings_option_value ); ?>"></span>				
								<?php } else { ?>
									<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( 'This integration does not require any settings. For more information check AST PRO documentation', 'ast-pro' ); ?>"></span>
								<?php } ?>
							</div>		
						</div>
						<div class="grid-bottom">
							<div class="grid-provider-more">
								<?php if ( null != $documentation ) { ?>
									<a href="<?php echo esc_url( $documentation ); ?>" class="doc-link" target="_blank"><?php esc_html_e( 'more info', 'ast-pro' ); ?></a>
								<?php } ?>	
							</div>
							<div class="grid-provider-enable">
								<input type="hidden" name="<?php esc_html_e( $integrations_id ); ?>" value="0"/>
								<input class="ast-toggle" id="<?php esc_html_e( $integrations_id ); ?>" name="<?php esc_html_e( $integrations_id ); ?>" type="checkbox" <?php esc_html_e( 	$checked ); ?> value="1" readonly <?php esc_html_e( $disabled ); ?>/>						
								<label class="ast-integration-tgl-lbl" for="<?php esc_html_e( $integrations_id ); ?>"></label>
							</div>		
						</div>	
					</div>	
					<?php
				}
				?>
				</div>
			</div>
			<?php wp_nonce_field( 'integrations_settings_form', 'integrations_settings_form_nonce' ); ?>
			<input type="hidden" name="action" value="integrations_settings_form_update">
		</form>	

		<div id="" class="popupwrapper integration_settings_popup" style="display:none;">
			<div class="popuprow">
				<div class="popup_header">
					<h3 class="popup_title integration_title"></h2>						
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body">
					<form id="integration_settings_popup_form" method="POST" class="">
						<div class="form-field">
							<input type="hidden" name="" class="integration_settings_hidden" value="0"/>
							<label><input type="checkbox" name="" class="integration_settings_checkbox" value="1">&nbsp;&nbsp;<span><?php esc_html_e( 'AutoComplete orders when they are Shipped', 'ast-pro' ); ?></span></label>							
						</div>
						<input type="submit" name="Submit" value="<?php esc_html_e( 'Save Changes', 'ast-pro' ); ?>" class="button-primary btn_ast2">
						<?php wp_nonce_field( 'integration_settings_popup_form', 'integration_settings_popup_form_nonce' ); ?>
						<input type="hidden" name="action" value="integration_settings_popup_form_update">
					</form>
				</div>	
			</div>
			<div class="popupclose"></div>
		</div>			
	</div>
</section>
