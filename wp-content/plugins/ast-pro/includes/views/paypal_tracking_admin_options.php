<section id="paypal_tracking_content" class="tab_section">	
	<form method="post" id="paypal_tracking_settings_form" action="" enctype="multipart/form-data">		
		<div class="tab_container">	
			<h2 class="tab_section_heading botton_border"><?php esc_html_e( 'PayPal Tracking', 'ast-pro' ); ?></h2>
			<?php $this->get_html_ul( $this->paypal_tracking_settings_options() ); ?>
			<div class="settings_ul_submit">			
				<button name="save" class="button-primary woocommerce-save-button btn_ast2 btn_large" type="submit" value="Save changes"><?php esc_html_e( 'Save Changes', 'ast-pro' ); ?></button>
				<?php wp_nonce_field( 'ptw_settings_tab', 'ptw_settings_tab_nonce' ); ?>
				<input type="hidden" name="action" value="ptw_settings_tab_save">
				<div class="spinner"></div>
			</div>	
		</div>
	</form>
</section>
