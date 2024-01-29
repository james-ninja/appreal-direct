<section id="integrations_content" class="tab_section">	
	<div class="tab_container_without_bg">		
		<form method="post" id="integrations_settings_form" action="" enctype="multipart/form-data">			
			<?php wp_nonce_field( 'integrations_settings_form', 'integrations_settings_form_nonce' ); ?>
			<div class="integration-grid-row grid-row">
				<?php
				foreach ( $this->integrations_settings_options() as $integrations_id => $array ) {
				$default = isset( $array['default'] ) ? $array['default'] : '';
				$checked = ( get_option( $integrations_id, $default ) ) ? 'checked' : '' ;
				$tgl_class = isset( $array['tgl_color'] ) ? 'ast-tgl-btn-green' : '';
				$disabled = isset( $array['disabled'] ) && true == $array['disabled'] ? 'disabled' : '';	
					?>
				<div class="grid-item">
					<div class="grid-item-wrapper">
						<img src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/<?php esc_html_e( $array['img'] ); ?>">
						<div class="grid-img-bottom">
							<span class="ast-tgl-btn-parent">
								<input type="hidden" name="<?php esc_html_e( $integrations_id ); ?>" value="0"/>
								<input class="ast-tgl ast-tgl-flat ast-settings-toggle" id="<?php esc_html_e( $integrations_id ); ?>" name="<?php esc_html_e( $integrations_id ); ?>" type="checkbox" <?php esc_html_e( $checked ); ?> value="1" readonly <?php esc_html_e( $disabled ); ?>/>
								<label class="ast-tgl-btn <?php esc_html_e( $tgl_class ); ?>" for="<?php esc_html_e( $integrations_id ); ?>"></label>
							</span>
							<a class="integration-more-info" href="https://www.zorem.com/docs/ast-pro/integrations/" target="blank"><?php esc_html_e( 'more info', 'ast-pro' ); ?></a>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>		
			<input type="hidden" name="action" value="integrations_settings_form_update">
		</form>
	</div>
</section>
