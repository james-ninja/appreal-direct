<div class="woocommerce-layout__activity-panel">
	<div class="woocommerce-layout__activity-panel-tabs">
		<button type="button" id="activity-panel-tab-help" class="components-button woocommerce-layout__activity-panel-tab">
			<span class="dashicons dashicons-menu-alt"></span>
		</button>
	</div>
	<div class="woocommerce-layout__activity-panel-wrapper">
		<div class="woocommerce-layout__activity-panel-content" id="activity-panel-true">
			<div class="woocommerce-layout__activity-panel-header">
				<div class="woocommerce-layout__inbox-title">
					<p class="css-activity-panel-Text">Documentation</p>            
				</div>								
			</div>
			<div>
				<ul class="woocommerce-list woocommerce-quick-links__list">
					<li class="woocommerce-list__item has-action">
						<?php
						$support_link = 'https://www.zorem.com/?support=1' ;
						?>
						<a href="<?php echo esc_url( $support_link ); ?>" class="woocommerce-list__item-inner" target="_blank" >
							<div class="woocommerce-list__item-before">
								<img src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/get-support-icon-20.svg">	
							</div>
							<div class="woocommerce-list__item-text">
								<span class="woocommerce-list__item-title">
									<div class="woocommerce-list-Text">Get Support</div>
								</span>
							</div>
							<div class="woocommerce-list__item-after">
								<span class="dashicons dashicons-arrow-right-alt2"></span>
							</div>
						</a>
					</li>            
					<li class="woocommerce-list__item has-action">
						<a href="https://www.zorem.com/docs/ast-pro/" class="woocommerce-list__item-inner" target="_blank">
							<div class="woocommerce-list__item-before">
								<img src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/documentation-icon-20.svg">
							</div>
							<div class="woocommerce-list__item-text">
								<span class="woocommerce-list__item-title">
									<div class="woocommerce-list-Text">Documentation</div>
								</span>
							</div>
							<div class="woocommerce-list__item-after">
								<span class="dashicons dashicons-arrow-right-alt2"></span>
							</div>
						</a>
					</li>						
				</ul>
			</div>
		</div>
	</div>
</div>
