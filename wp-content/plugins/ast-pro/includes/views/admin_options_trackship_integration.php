<?php
/**
 * Html code for trackship tab
 */
wp_enqueue_script( 'trackship_script' );
?>
<div class="tab_inner_container" style="width: 100%;margin: 20px auto;">	
	<div class="ast_menu_container">
		<div class="trackship_landing_section">
			
			<div class="">
				<img class="ts_landing_logo" src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/trackship-logo.png">
				<h1 class="ts_landing_header">Take Control of your Post-Shipping Workflow</h1>					
				<p class="ts_landing_description">Trackship brings a fully-branded tracking experience into your store, auto-tracks all your shipments with 300+ shipping providers and enables you to further engage your customers after shipping and to offer them superb customer service and post-purchase experience.</p>	
				<h3 class="ts_landing_subheading">Start for Free. 50 Free trackers for new accounts!</h3>	
			</div>			
			
			<a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=search&s=TrackShip+For+WooCommerce&plugin-search-input=Search+Plugins' ) ); ?>" target="_blank" class="button-primary btn_green2 btn_large"><span>Install TrackShip for WooCommerce</span><span class="dashicons dashicons-arrow-right-alt2"></span></a>
			
			<a href="https://www.youtube.com/watch?v=PhnqDorKN_c" target="_blank" class="button-primary btn_ts_transparent btn_large"><span>Watch Video</span><span class="dashicons dashicons-video-alt2"></span></a>
			
			<div class="ast-row">
				<div class="as-col-6">
					<ul class="ts_features">
						<li>Branded Tracking Experience inside your WooCommerce Store</li>
						<li>Seamless integration into your fulfillment workflow</li>
						<li>Automatic Tracking with 300+ Shipping Providers</li>
						<li>Post-Shipping Automation</li>
						<li>Proactive Shipment status & Delivery Updates</li>
						<li>Custom Email Templates</li>
						<li>Tracking page on your store</li>
						<li>Display tracking widget on View order page</li>
						<li>Tracking Analytics widget</li>
						<li>White Label Tracking</li>
						<li>Map Shipping Provider</li>
						<li>SMS Notifications (requires SMS for WooCommerce)</li>
					</ul>
				</div>
				<div class="as-col-6 ts_landing_banner">
					<img src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/ts-header-banner.png">
				</div>		
			</div>				
		</div>
	</div>
</div>
