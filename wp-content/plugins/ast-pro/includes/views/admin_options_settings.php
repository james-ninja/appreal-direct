<?php
/**
 * Html code for settings tab
 */
?>

<section id="content2" class="tab_section">
	<form method="post" id="wc_ast_settings_form" action="" enctype="multipart/form-data">
		
		<div class="accordion_container">
		
			<div class="accordion_set">
				<div class="accordion heading add-tracking-option">
					<label>
						<?php esc_html_e( 'Add Tracking Options', 'ast-pro' ); ?>
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'ast-pro' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options add-tracking-option">
					<?php $this->get_html_ul( $this->get_add_tracking_options() ); ?>
				</div>
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading customer-view">
					<label>
						<?php esc_html_e( 'Customer View', 'ast-pro' ); ?>
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'ast-pro' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options customer-view">
					<?php $this->get_html_ul( $this->get_customer_view_options() ); ?>
				</div>
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading shipment-tracking-api">
					<label>
						<?php esc_html_e( 'Shipment Tracking API', 'ast-pro' ); ?>
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'ast-pro' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options shipment-tracking-api">
					<?php $this->get_html_ul( $this->get_shipment_tracking_api_options() ); ?>
				</div>
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading custom-order-status">
					<label>
						<?php esc_html_e( 'Order Statuses', 'ast-pro' ); ?>	
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'ast-pro' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options custom-order-status">
					<?php require_once( 'admin_options_osm.php' ); ?>
				</div>			
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading fulfillement-dashboard">
					<label>
						<?php esc_html_e( 'Fulfillement Dashboard', 'ast-pro' ); ?>			
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'ast-pro' ); ?></button>
						</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options fulfillement-dashboard">
					<?php $this->get_html_ul( $this->get_fulfillment_dashboard_options() ); ?>
				</div>
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading paypal-tracking-settings">
					<label>
						<?php esc_html_e( 'PayPal Tracking', 'ast-pro' ); ?>			
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'ast-pro' ); ?></button>
						</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options paypal-tracking-settings">
					<?php $this->get_html_ul( $this->paypal_tracking_settings_options() ); ?>
				</div>
			</div>
			
			<?php wp_nonce_field( 'wc_ast_settings_form', 'wc_ast_settings_form_nonce' ); ?>
			<input type="hidden" name="action" value="wc_ast_settings_form_update">	
		</div>	
	</form>		
</section>
