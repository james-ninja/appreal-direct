<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_Admin {
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		
		global $wpdb;
		if ( is_multisite() ) {			
			
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			
			if ( is_plugin_active_for_network( 'ast-pro/ast-pro.php' ) ) {
				$main_blog_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );			
				$this->table = $main_blog_prefix . 'woo_shippment_provider';	
			} else {
				$this->table = $wpdb->prefix . 'woo_shippment_provider';
			}
			
		} else {
			$this->table = $wpdb->prefix . 'woo_shippment_provider';	
		}
		
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return AST_Pro_Admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 10 );
		
		//Custom Woocomerce menu
		add_action( 'admin_menu', array( $this, 'register_woocommerce_menu' ), 99 );		

		//ajax save admin api settings
		add_action( 'wp_ajax_wc_ast_settings_form_update', array( $this, 'wc_ast_settings_form_update_callback' ) );				
		
		//ajax save admin api settings		
		add_action( 'wp_ajax_integrations_settings_form_update', array( $this, 'integrations_settings_form_update_callback' ) );
		
		add_action( 'wp_ajax_ptw_settings_tab_save', array( $this, 'ptw_settings_tab_save_callback' ) );
		
		add_filter( 'edit_provider_class', array( $this, 'edit_provider_class' ) );
				
		add_action( 'ast_custom_order_status_save', array( $this, 'ast_custom_order_status_save') );
		add_action( 'admin_footer', array( $this, 'footer_function'), 1 );
		
		$show_unfulfilled_filter = get_option( 'wc_ast_show_unfulfilled_filter', 0 );
		if ( 1 == $show_unfulfilled_filter ) {
			// add bulk order filter for exported / non-exported orders
			add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_fullfillment_status' ) , 20 );	
			add_filter( 'request', array( $this, 'filter_orders_by_fullfillment_status_query' ) );	
		}

		add_filter( 'ast_formated_order_id', array( $this, 'ast_get_formated_order_id' ) );		

		add_filter( 'mark_order_as_fields_data', array( $this, 'mark_order_as_fields_data' ) );		
		
		$wc_ast_enable_auto_detection = get_option( 'wc_ast_enable_auto_detection', 0 );
		if ( 1 == $wc_ast_enable_auto_detection ) {
			add_action( 'ast_tracking_form_end_meta_box', array( $this, 'autodetector_include_js_meta_box' ), 1, 1 );
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'autodetector_include_js_orders_page'), 100, 2 );		
		}
		
		$wc_ast_status_partial_shipped = get_option( 'wc_ast_status_partial_shipped', 1 );		
		if ( $wc_ast_status_partial_shipped ) {
			add_action( 'woocommerce_order_status_partial-shipped', array( $this, 'email_trigger_partial_shipped' ), 10, 2 );			
		}
	
		$wc_ast_status_updated_tracking = get_option( 'wc_ast_status_updated_tracking' );		
		if ( $wc_ast_status_updated_tracking ) {
			add_action( 'woocommerce_order_status_updated-tracking', array( $this, 'email_trigger_updated_tracking' ), 10, 2 );	
		}	
		
		$wc_ast_status_shipped = get_option( 'wc_ast_status_new_shipped' );
		if ( 1 == $wc_ast_status_shipped ) {
			add_action( 'woocommerce_order_status_shipped', array( $this, 'email_trigger_shipped' ), 10, 2 );
		}
		
		//Shipping Provider Action						
		add_action( 'wp_ajax_filter_shipiing_provider_by_status', array( $this, 'filter_shipiing_provider_by_status_fun') );
		
		add_action( 'wp_ajax_add_custom_shipment_provider', array( $this, 'add_custom_shipment_provider_fun') );
		
		add_action( 'wp_ajax_get_provider_details', array( $this, 'get_provider_details_fun') );
		
		add_action( 'wp_ajax_update_custom_shipment_provider', array( $this, 'update_custom_shipment_provider_fun') );
		
		add_action( 'wp_ajax_reset_default_provider', array( $this, 'reset_default_provider_fun') );
		
		add_action( 'wp_ajax_woocommerce_shipping_provider_delete', array( $this, 'woocommerce_shipping_provider_delete' ) );				
		
		add_action( 'wp_ajax_update_provider_status', array( $this, 'update_provider_status_fun') );				
		
		add_action( 'wp_ajax_reset_shipping_providers_database', array( $this, 'reset_shipping_providers_database_fun') );
		
		add_action( 'wp_ajax_update_shipment_status', array( $this, 'update_shipment_status_fun') );				

		add_action( 'add_more_api_provider', array( $this, 'add_more_api_provider' ) );
		
		add_action( 'wp_ajax_sync_providers', array( $this, 'sync_providers_fun' ) );				
	}
	
	/**	 
	 * Function for include css and js
	 */
	public function autodetector_include_js_meta_box() {
		wp_enqueue_script( 'ast_pro_autodetector', ast_pro()->plugin_dir_url() . 'assets/js/autodetector.js' , array( 'jquery', ), ast_pro()->version, true );
	}
	
	/**	 
	 * Function for include js on orders page
	 */
	public function autodetector_include_js_orders_page( $actions, $order ) {
		wp_enqueue_script( 'ast_pro_autodetector_orders', ast_pro()->plugin_dir_url() . 'assets/js/autodetector.js' , array( 'jquery'), ast_pro()->version, true );			
		return $actions;
	}
	
	public function admin_styles( $hook ) {
		
		if ( !isset( $_GET[ 'page' ] ) ) {
			return;
		}
		
		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		
		if ( !in_array( $page, array( 'woocommerce-advanced-shipment-tracking', 'fulfillment-dashboard', 'vendor-fulfillment-dashboard', 'wcpv-vendor-order', 'trackship-for-woocommerce', 'ast-csv-import', 'ast-license', 'ast-trackship' ) ) ) {
			return;
		}		
		
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';				

		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
		wp_enqueue_script( 'select2');
		
		wp_enqueue_style( 'ast_styles', ast_pro()->plugin_dir_url() . 'assets/css/admin.css', array(), ast_pro()->version );						
		
		wp_enqueue_script( 'woocommerce-advanced-shipment-tracking-js', ast_pro()->plugin_dir_url() . 'assets/js/admin.js', array( 'jquery' ), ast_pro()->version, true );
		wp_localize_script(
			'woocommerce-advanced-shipment-tracking-js',
			'ast_orders_params',
			array(
				'order_nonce' => wp_create_nonce( 'ast-order-list' ),
			)
		);		
		
		wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' );
		wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION );
		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		
		wp_enqueue_script( 'selectWoo' );
		wp_enqueue_script( 'wc-enhanced-select' );
		
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'wp-color-picker' );
		
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script( 'jquery-blockui' );
		wp_enqueue_script( 'wp-color-picker' );		
		wp_enqueue_script( 'jquery-ui-sortable' );		
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );		
		wp_enqueue_style( 'thickbox' );	
		wp_enqueue_style( 'trackship_styles' );			
		
		wp_enqueue_script( 'ajax-queue', ast_pro()->plugin_dir_url() . 'assets/js/jquery.ajax.queue.js', array( 'jquery' ), ast_pro()->version );
				
		wp_enqueue_script( 'ast_settings', ast_pro()->plugin_dir_url() . 'assets/js/settings.js', array( 'jquery' ), ast_pro()->version );

		wp_enqueue_script( 'ast_hip', ast_pro()->plugin_dir_url() . 'assets/js/hip.js', array( 'jquery' ), ast_pro()->version );
		
		wp_register_script( 'shipment_tracking_table_rows', ast_pro()->plugin_dir_url() . 'assets/js/shipping_row.js' , array( 'jquery', 'wp-util' ), ast_pro()->version );
		
		wp_localize_script( 'shipment_tracking_table_rows', 'shipment_tracking_table_rows', array(
			'i18n' => array(				
				'data_saved'	=> __( 'Data saved successfully', 'ast-pro' ),
				'delete_provider' => __( 'Really delete this entry? This will not be undo.', 'ast-pro' ),
				'upload_only_csv_file' => __( 'You can upload only csv file.', 'ast-pro' ),
				'browser_not_html' => __( 'This browser does not support HTML5.', 'ast-pro' ),
				'upload_valid_csv_file' => __( 'Please upload a valid CSV file.', 'ast-pro' ),
			),
			'delete_rates_nonce' => wp_create_nonce( 'delete-rate' ),
		) );
		wp_enqueue_media();	
		
		wp_enqueue_script( 'ast_pro_admin_script', ast_pro()->plugin_dir_url() . 'assets/js/admin_pro.js' , array( 'jquery', 'wp-util' ), ast_pro()->version, true );	
		wp_localize_script( 'ast_pro_admin_script', 'ast_pro_ajax_object', array( 
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'i18n' => array(				
				'data_saved'	=> __( 'Data saved successfully', 'ast-pro' ),				
			),	
		) );
	}
	
	/*
	* Admin Menu add function
	* WC sub menu
	*/
	public function register_woocommerce_menu() {
		add_submenu_page( 'woocommerce', 'Shipment Tracking', __( 'Shipment Tracking', 'ast-pro' ), 'manage_woocommerce', 'woocommerce-advanced-shipment-tracking', array( $this, 'woocommerce_advanced_shipment_tracking_page_callback' ) ); 
	}			
	
	/*
	* callback for Shipment Tracking page
	*/
	public function woocommerce_advanced_shipment_tracking_page_callback() {		  
		
		global $order, $wpdb;
		$WC_Countries = new WC_Countries();
		$countries = $WC_Countries->get_countries();				
		
		$default_shippment_providers = $wpdb->get_results( 'SELECT * FROM ' . ast_pro()->shippment_provider_table() . ' ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC' );		
		
		foreach ( $default_shippment_providers as $key => $value ) {			
			$search = array('(US)', '(UK)');
			$replace = array('', '');
			
			if ( $value->shipping_country && 'Global' != $value->shipping_country ) {
				$country = str_replace( $search, $replace, $WC_Countries->countries[ $value->shipping_country ] );
				$default_shippment_providers[ $key ]->country = $country;			
			} elseif ( $value->shipping_country && 'Global' == $value->shipping_country ) {
				$default_shippment_providers[ $key ]->country = 'Global';
			}
		}				
		
		wp_enqueue_script( 'shipment_tracking_table_rows' ); 
		?>		
		
		<div class="zorem-layout">
			<?php do_action( 'ast_settings_admin_notice' ); ?>
			<div class="zorem-layout__header">
				<h1 class="page_heading">
					<a href="javascript:void(0)"><?php esc_html_e( 'AST Fulfillment Manager', 'ast-pro' ); ?></a> <span class="dashicons dashicons-arrow-right-alt2"></span> <span class="breadcums_page_heading"><?php esc_html_e( 'Settings', 'ast-pro' ); ?></span>
				</h1>				
				<img class="zorem-layout__header-logo" src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/ast-logo.png">		
			</div>
			<div class="woocommerce zorem_admin_layout">
				<div class="ast_admin_content zorem_admin_settings">
					<?php include 'views/activity_panel.php'; ?>						
					<div class="ast_nav_div">											
						<?php 
						$this->get_html_menu_tab( $this->get_ast_tab_settings_data() );
						?>
						<div class="menu_devider"></div>
						<?php						
						require_once( 'views/admin_options_shipping_provider.php' );
						require_once( 'views/admin_options_settings.php' );										
						require_once( 'views/integrations_admin_options.php' );
						require_once( 'views/admin_options_addons.php' );
						?>
					</div>                   					
				</div>				
			</div>					
		</div>		
		<?php 	
	}		
	
	/*
	* callback for HTML function for Shipment Tracking menu
	*/
	public function get_html_menu_tab( $arrays, $tab_class = 'tab_input' ) {
		
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'settings';
		$settings = isset( $_GET['settings'] ) ? sanitize_text_field( $_GET['settings'] ) : 'general-settings';		
		
		foreach ( (array) $arrays as $id => $array ) {
			$checked = ( $tab == $array['data-tab'] || $settings == $array['data-tab'] ) ? 'checked' : '';		
			if ( $array['show'] ) {	
				if ( isset( $array['type'] ) && 'link' == $array['type'] ) {
					?>
					<a class="menu_trackship_link" href="<?php esc_html_e( esc_url( $array['link'] ) ); ?>"><?php esc_html_e( $array['title'] ); ?></a>
				<?php 
				} else { 
					?>
					<input class="<?php esc_html_e( $tab_class ); ?>" id="<?php esc_html_e( $id ); ?>" name="<?php esc_html_e( $array['name'] ); ?>" type="radio"  data-tab="<?php esc_html_e( $array['data-tab'] ); ?>" data-label="<?php esc_html_e( $array['data-label'] ); ?>"  <?php esc_html_e( $checked ); ?>/>
					<label class="<?php esc_html_e( $array['class'] ); ?>" for="<?php esc_html_e( $id ); ?>"><?php esc_html_e( $array['title'] ); ?></label>
				<?php 
				} 
			}
		}
	}			

	/*
	* get UL html of fields
	*/
	public function get_html_ul( $arrays ) { 
		?>
		<ul class="settings_ul">		
		<?php 
		foreach ( (array) $arrays as $id => $array ) {
				
			if ( $array['show'] ) { 
				
				if ( 'checkbox' == $array['type'] ) {
					$default = isset( $array['default'] ) ? $array['default'] : '';
					$checked = ( get_option( $id, $default ) ) ? 'checked' : '' ;					
					?>
					<li>
						<input type="hidden" name="<?php esc_html_e( $id ); ?>" value="0"/>
						<input class="" id="<?php esc_html_e( $id ); ?>" name="<?php esc_html_e( $id ); ?>" type="checkbox" <?php esc_html_e( $checked ); ?> value="1"/>
											
						<label class="setting_ul_checkbox_label"><?php esc_html_e( $array['title'] ); ?>
						<?php if ( isset( $array['tooltip'] ) ) { ?>
							<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
						<?php } ?>
						</label>						
					</li>	
				<?php 
				} else if ( 'tgl_checkbox' == $array['type'] ) {
					$default = isset( $array['default'] ) ? $array['default'] : '';
					$checked = ( get_option( $id, $default ) ) ? 'checked' : '' ;
					$tgl_class = isset( $array['tgl_color'] ) ? 'ast-tgl-btn-green' : '';
					$disabled = isset( $array['disabled'] ) && true == $array['disabled'] ? 'disabled' : '';					
					?>
					<li>
						<span class="ast-tgl-btn-parent">
							<input type="hidden" name="<?php esc_html_e( $id ); ?>" value="0"/>
							<input class="ast-tgl ast-tgl-flat ast-settings-toggle" id="<?php esc_html_e( $id ); ?>" name="<?php esc_html_e( $id ); ?>" type="checkbox" <?php esc_html_e( $checked ); ?> value="1" <?php esc_html_e( $disabled ); ?>/>
							<label class="ast-tgl-btn <?php esc_html_e( $tgl_class ); ?>" for="<?php esc_html_e( $id ); ?>"></label>
						</span>
											
						<div class="setting_ul_tgl_checkbox_label">
							<label><?php esc_html_e( $array['title'] ); ?></label>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
							<?php if ( isset( $array['desc'] ) ) { ?>
								<div class="tgl_checkbox_desc"><?php esc_html_e( $array['desc'] ); ?></div>
							<?php } ?>	
						</div>						
						
						<?php if ( isset( $array['customize_link'] ) ) { ?>
							<a href="<?php esc_html_e( $array['customize_link'] ); ?>" class="button-primary btn_ts_transparent btn_large ts_customizer_btn">
								<?php esc_html_e( 'Customize', 'ast-pro' ); ?>
							</a>	
						<?php 
						}
						if ( isset( $array['input_desc'] ) ) {
							if ( isset( $array['desc_url'] ) ) { 
								?>
								<span class="ast_log_setting"><?php esc_html_e( $array['input_desc'] ); ?>
								<a target="_blank" class='ptw_a' href="<?php esc_html_e( $array['desc_url'] ); ?>">Logs</a></span>
							<?php 
							} else {
								?>
								<span><?php esc_html_e( $array['input_desc'] ); ?></span>
							<?php 
							}
						}
						?>
					</li>	
				<?php 
				} else if ( 'radio' == $array['type'] ) {
					?>
					<li class="settings_radio_li">
						<label><strong><?php esc_html_e( $array['title'] ); ?></strong>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
						</label>	
						
						<?php 
						
						foreach ( (array) $array['options'] as $key => $val ) {
							$selected = ( get_option( $id, $array['default'] ) == (string) $key ) ? 'checked' : '' ; 
							?>
							<span class="radio_section">
								<label class="" for="<?php esc_html_e( $id ); ?>_<?php esc_html_e( $key ); ?>">												
									<input type="radio" id="<?php esc_html_e( $id ); ?>_<?php esc_html_e( $key ); ?>" name="<?php esc_html_e( $id ); ?>" class="<?php esc_html_e( $id ); ?>"  value="<?php esc_html_e( $key ); ?>" <?php esc_html_e( $selected ); ?> />
									<span class=""><?php esc_html_e( $val ); ?></span></br>
								</label>																		
							</span>
						<?php
						} 
						?>
					</li>					
				<?php 
				} else if ( 'multiple_select' == $array['type'] ) { 
					?>
					<li class="multiple_select_li">
						<label><?php esc_html_e( $array['title'] ); ?>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
						</label>
						<div class="multiple_select_container <?php esc_html_e( $id ); ?>">	
							<select multiple class="wc-enhanced-select" name="<?php esc_html_e( $id ); ?>[]" id="<?php esc_html_e( $id ); ?>">
								<?php
								foreach ( (array) $array['options'] as $key => $val ) { 
									$multi_checkbox_data = get_option( $id );
									$checked = isset( $multi_checkbox_data[ $key ] ) && 1 == $multi_checkbox_data[ $key ] ? 'selected' : '' ;
									?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php esc_html_e( $checked ); ?>><?php esc_html_e( $val['status'] ); ?></option>
								<?php } ?>
							</select>	
						</div>
					</li>	
				<?php 
				} else if ( 'simple_multiple_select' == $array['type'] ) {				
					?>
					<li class="multiple_select_li">
						
						<label><?php esc_html_e( $array['title'] ); ?>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
						</label>
						
						<div class="multiple_select_container">	
							<select multiple class="wc-enhanced-select" name="<?php esc_html_e( $id ); ?>[]" id="<?php esc_html_e( $id ); ?>">
							<?php
							foreach ( (array) $array['options'] as $key => $val ) { 
								$multi_checkbox_data = get_option( $id );								
								$checked = ( is_array( $multi_checkbox_data ) && in_array( $key , $multi_checkbox_data ) ) ? 'selected' : '' ;
								?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php esc_html_e( $checked ); ?>><?php esc_html_e( $val ); ?></option>
							<?php 
							} 
							?>
							</select>	
						</div>
					</li>	
					<?php 
				} else if ( 'multiple_checkbox' == $array['type'] ) { 
					?>
					<li>
						<div class="multiple_checkbox_label">
							<label for=""><strong><?php esc_html_e( $array['title'] ); ?></strong></label>
							<span class="multiple_checkbox_description"><?php esc_html_e( $array['desc'] ); ?></span>
						</div >
						<div class="multiple_checkbox_parent">
							<?php 
							$op = 1;	
							foreach ( (array) $array['options'] as $key => $val ) {
								$multi_checkbox_data = get_option($id);
								$checked = isset( $multi_checkbox_data[ $key ] ) && 1 == $multi_checkbox_data[ $key ] ? 'checked' : '' ;
								?>
								<span class="multiple_checkbox">
									<label class="" for="">
										<input type="hidden" name="<?php esc_html_e( $id ); ?>[<?php esc_html_e( $key ); ?>]" value="0"/>
										<input type="checkbox" name="<?php esc_html_e( $id ); ?>[<?php esc_html_e( $key ); ?>]" class=""  <?php esc_html_e( $checked ); ?> value="1"/>
										<span class="multiple_label"><?php esc_html_e( $val['status'] ); ?></span>
										</br>
									</label>																		
								</span>												
							<?php } ?>
						</div>						
					</li>	
				<?php 
				} else if ( 'dropdown_tpage' == $array['type'] ) { 
					?>
					<li>
						<label class="left_label"><?php esc_html_e( $array['title'] ); ?>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
						</label>						
						
						<select class="select select2 tracking_page_select" id="<?php esc_html_e( $id ); ?>" name="<?php esc_html_e( $id ); ?>">
							<?php
							foreach ( (array) $array['options'] as $page_id => $page_name ) { 
								$selected = ( get_option( $id ) == $page_id ) ? 'selected' : '' ;
								?>
								<option value="<?php esc_html_e( $page_id ); ?>" <?php esc_html_e( $selected ); ?>><?php esc_html_e( $page_name ); ?></option>
							<?php 
							}
							$selected = ( 'other' == get_option( $id ) ) ? 'selected' : '';	
							?>
							<option <?php esc_html_e( $selected ); ?> value="other"><?php esc_html_e( 'Other', 'ast-pro' ); ?></option>	
						</select>
						<?php $style = ( 'other' != get_option( $id ) ) ? 'display:none;' : ''; ?>
						<fieldset style="<?php esc_html_e( $style ); ?>" class="trackship_other_page_fieldset">
							<input type="text" name="wc_ast_trackship_other_page" id="wc_ast_trackship_other_page" value="<?php esc_html_e( get_option('wc_ast_trackship_other_page') ); ?>">
						</fieldset>
						
						<p class="tracking_page_desc"><?php esc_html_e( 'add the [wcast-track-order] shortcode in the selected page.', 'ast-pro' ); ?> 
							<a href="https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/integration/" target="blank"><?php esc_html_e( 'more info', 'ast-pro' ); ?></a>
						</p>	
						
					</li>	
				<?php 
				} else if ( 'button' == $array['type'] ) { 
					?>
					<li>
						<label class="left_label"><?php esc_html_e( $array['title'] ); ?>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
						</label>	
						<?php 
						if ( isset( $array['customize_link'] ) ) { 
							?>
							<a href="<?php esc_html_e( $array['customize_link'] ); ?>" class="button-primary btn_ts_transparent btn_large ts_customizer_btn"><?php esc_html_e( 'Customize', 'ast-pro' ); ?></a>	
						<?php } ?>	
					</li>	
				<?php 
				} elseif ( 'text' == $array['type'] ) {
					$placeholder = !empty( $array['placeholder'] ) ? $array['placeholder'] : '';
					?>
					<li class="multiple_select_li">
						<label><?php esc_html_e( $array['title'] ); ?>
							<?php if ( isset( $array['tooltip'] ) ) { ?>
								<span class="woocommerce-help-tip tipTip" data-tip="<?php esc_html_e( $array['tooltip'] ); ?>"></span>
							<?php } ?>
						</label>
						<fieldset>
							<input class="input-text regular-input " type="text" name="<?php esc_html_e( $id ); ?>" id="<?php esc_html_e( $id ); ?>" style="" value="<?php esc_html_e( get_option($id) ); ?>" placeholder="<?php esc_html_e( $placeholder ); ?>">
							<?php 
							if ( isset( $array['input_desc'] ) ) {
								if ( isset( $array['desc_url'] ) ) {
									?>
									<div><a target="_blank" href="<?php esc_html_e( $array['desc_url'] ); ?>" ><span><?php esc_html_e( $array['input_desc'] ); ?></span></a></div>
									<?php } else { ?>
										<span><?php esc_html_e( $array['input_desc'] ); ?></span>
									<?php	
									}
							}
							?>
						</fieldset>
					</li>
					<?php 
				}				
			}
		}
		?>
		</ul>	
	<?php 
	}

	/*
	* callback for Shipment Tracking menu array
	*/
	public function get_ast_tab_settings_data() {	
		
		$ast_pro_customizer_settings = new ast_pro_customizer_settings();		
		
		$setting_data = array(
			'tab2' => array(					
				'title'		=> __( 'Settings', 'ast-pro' ),
				'show'      => true,
				'class'     => 'tab_label first_label',
				'data-tab'  => 'settings',
				'data-label' => __( 'Settings', 'ast-pro' ),
				'name'  => 'tabs',
				'position'  => 1,	
			),				
			'tab1' => array(					
				'title'		=> __( 'Shipping Providers', 'ast-pro' ),
				'show'      => true,
				'class'     => 'tab_label',
				'data-tab'  => 'shipping-providers',
				'data-label' => __( 'Shipping Providers', 'ast-pro' ),
				'name'  => 'tabs',
				'position'  => 2,
			),			
			'integrations_tab' => array(					
				'title'		=> __( 'Integrations', 'ast-pro' ),
				'show'      => true,
				'class'     => 'tab_label',
				'data-tab'  => 'integrations',
				'data-label' => 'Integrations',
				'name'  => 'tabs',
			),
			/*'paypal_tracking' => array(					
				'title'		=> __( 'PayPal Tracking', 'ast-pro' ),
				'show'      => true,
				'class'     => 'tab_label',
				'data-tab'  => 'paypal-tracking',
				'data-label' => __( 'PayPal Tracking', 'ast-pro' ),
				'name'  => 'tabs',
			),*/
			'tab6' => array(					
				'title'		=> __( 'License', 'ast-pro' ),
				'show'      => true,
				'class'     => 'tab_label',
				'data-tab'  => 'license',
				'data-label' => __( 'License', 'ast-pro' ),
				'name'  => 'tabs',
			),	
		);
		return apply_filters( 'ast_menu_tab_options', $setting_data );		
	}		
	
	public function get_add_tracking_options() {
		
		$wc_ast_status_shipped = get_option( 'wc_ast_status_shipped', 1 );
		$completed_order_label = ( 1 == $wc_ast_status_shipped ) ? __( 'Shipped', 'ast-pro' ) : __( 'Completed', 'woocommerce' );
		
		$all_order_status = wc_get_order_statuses();
		
		$default_order_status = array(
			'wc-pending' => 'Pending payment',
			'wc-processing' => 'Processing',
			'wc-on-hold' => 'On hold',
			'wc-completed' => 'Completed',
			'wc-delivered' => 'Delivered',			
			'wc-cancelled' => 'Cancelled',
			'wc-refunded' => 'Refunded',
			'wc-failed' => 'Failed',
			'wc-ready-pickup' => 'Ready for Pickup',		
			'wc-pickup' => 'Picked up',	
			'wc-partial-shipped' => 'Partially Shipped',		
			'wc-updated-tracking' => 'Updated Tracking',				
		);
		
		foreach ( $default_order_status as $key => $value ) {
			unset($all_order_status[$key]);
		}
		$custom_order_status = $all_order_status;
		
		foreach ( $custom_order_status as $key => $value ) {
			unset($custom_order_status[$key]);			
			$key = str_replace( 'wc-', '', $key );		
			$custom_order_status[$key] = array(
				'status' => __( $value, '' ),
				'type' => 'custom',
			);
		}
		
		$actions_order_status = array( 
			'processing' => array(
				'status' => __( 'Processing', 'woocommerce' ),
				'type' => 'default',
			),
			'completed' => array(
				'status' => $completed_order_label,
				'type' => 'default',
			),
			'partial-shipped' => array(
				'status' => __( 'Partially Shipped', '' ),
				'type' => 'default',
				'class' => 'partially_shipped_checkbox',
			),	
			'on-hold' => array(
				'status' => __( 'On Hold', 'woocommerce' ),
				'type' => 'default',
			),
			'cancelled' => array(
				'status' => __( 'Cancelled', 'woocommerce' ),
				'type' => 'default',
			),		
			'refunded' => array(
				'status' => __( 'Refunded', 'woocommerce' ),
				'type' => 'default',
			),	
			'failed' => array(
				'status' => __( 'Failed', 'woocommerce' ),
				'type' => 'default',
			),					
		);
		
		$action_order_status_array = array_merge( $actions_order_status, $custom_order_status );
		
		$form_data = array(		
			'enable_tpi_by_default' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Tracking Per Item option by default', 'ast-pro' ),				
				//'tooltip'   => __( 'This will check the option to add tracking per item when adding tracking info to orders', 'ast-pro' ),
				'show'		=> true,
				'class'     => '',
			),
			'display_sku_for_tpi' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Show item SKU when adding tracking to orders', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),	
			'display_image_for_tpi' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Show product image when adding tracking to orders', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),	
			'wc_ast_default_mark_shipped' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Set the "mark as shipped" option checked  when adding tracking info to orders', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),
			'wc_ast_enable_auto_detection' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable Shipping Providers Auto-detection (Beta)', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),
			'wc_ast_enable_leagace_add_tracking' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable Legacy add tracking panel in edit order', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),
			'wc_ast_show_orders_actions' => array(
				'type'		=> 'multiple_select',
				'title'		=> __( 'Add Tracking Order action', 'ast-pro' ),				
				'options'   => $action_order_status_array,					
				'show'		=> true,
				'class'     => '',
			),	
		);
		return $form_data;
	}
	
	public function get_customer_view_options() {
		
		$wc_ast_status_shipped = get_option( 'wc_ast_status_shipped', 1 );
		$wc_ast_status_new_shipped = get_option( 'wc_ast_status_new_shipped', 0 );		
		$completed_order_label = ( 1 == $wc_ast_status_shipped ) ? __( 'Shipped', 'ast-pro' ) : __( 'Completed', 'woocommerce' );
		
		$all_order_status = wc_get_order_statuses();
		
		$default_order_status = array(
			'wc-pending' => 'Pending payment',
			'wc-processing' => 'Processing',
			'wc-on-hold' => 'On hold',
			'wc-completed' => 'Completed',
			'wc-delivered' => 'Delivered',			
			'wc-cancelled' => 'Cancelled',
			'wc-refunded' => 'Refunded',
			'wc-failed' => 'Failed',
			'wc-ready-pickup' => 'Ready for Pickup',		
			'wc-pickup' => 'Picked up',	
			//'wc-partial-shipped' => 'Partially Shipped',
			//'wc-shipped' => 'Shipped',				
		);
		
		foreach ( $default_order_status as $key => $value ) {
			unset($all_order_status[$key]);
		}
		
		$custom_order_status = $all_order_status;
		
		foreach ( $custom_order_status as $key => $value ) {
			unset($custom_order_status[$key]);			
			$key = str_replace( 'wc-', '', $key );		
			$custom_order_status[$key] = array(
				'status' => __( $value, '' ),
				'type' => 'custom',
			);
		}
		
		$order_status = array( 
			'processing' => array(
				'status' => __( 'Processing', 'woocommerce' ),
				'type' => 'default',
			),
			'completed' => array(
				'status' => $completed_order_label,
				'type' => 'default',
			),
			'partial-shipped' => array(
				'status' => __( 'Partially Shipped', '' ),
				'type' => 'default',
				'class' => 'partially_shipped_checkbox',				
			),
			/*'shipped' => array(
				'status' => __( 'Shipped', '' ),
				'type' => 'default',
				'class' => 'shipped_checkbox',
			),*/			
			'cancelled' => array(
				'status' => __( 'Cancelled', 'woocommerce' ),
				'type' => 'default',
			),
			'on-hold' => array(
				'status' => __( 'On Hold', 'woocommerce' ),
				'type' => 'default',
			),			
			'refunded' => array(
				'status' => __( 'Refunded', 'woocommerce' ),
				'type' => 'default',
			),			
			'failed' => array(
				'status' => __( 'Failed', 'woocommerce' ),
				'type' => 'default',
			),
			'show_in_customer_invoice' => array(
				'status' => __( 'Customer Invoice', 'woocommerce' ),
				'type' => 'default',
			),
			'show_in_customer_note' => array(
				'status' => __( 'Customer note', 'woocommerce' ),
				'type' => 'default',
			),			
		);
		
		if ( $wc_ast_status_new_shipped ) {
			$order_status['shipped'] = array(
				'status' => __( 'Shipped', '' ),
				'type' => 'default',
				'class' => 'shipped_checkbox',
			); 
		}
		
		$order_status_array = array_merge( $order_status, $custom_order_status );	
		
		$form_data = array(
			'wc_ast_unclude_tracking_info' => array(
				'type'		=> 'multiple_select',
				'title'		=> __( 'Order Emails Display', 'ast-pro' ),				
				'options'   => $order_status_array,					
				'show'		=> true,
				'class'     => '',
			),
			'display_track_in_my_account' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable Track button in orders history (actions)', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),
			'open_track_in_new_tab' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Open the Track Button link in a new tab', 'ast-pro' ),			
				'show'		=> true,
				'class'     => '',
			),
		);
		return $form_data;
	}
	
	public function get_shipment_tracking_api_options() {				
		$form_data = array(
			'autocomplete_order_tpi' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Auto-complete orders that come from the API', 'ast-pro' ),				
				'show'		=> true,
				'class'     => '',
			),	
			'wc_ast_api_date_format' => array(
				'type'		=> 'radio',
				'title'		=> __( 'API Date Format', 'ast-pro' ),				
				'desc'		=> __( 'Choose for which Order status to display', 'ast-pro' ),			
				'options'   => array(
								'd-m-Y' => 'DD/MM/YYYY',
								'm-d-Y' => 'MM/DD/YYYY',
							),
				'default'   => 'd-m-Y',				
				'show'		=> true,
				'class'     => '',
			),
		);
		return $form_data;
	}
	
	public function get_fulfillment_dashboard_options() {
			
		$wc_ast_status_shipped = get_option( 'wc_ast_status_shipped', 1 );
		$completed_order_label = ( 1 == $wc_ast_status_shipped ) ? __( 'Shipped', 'ast-pro' ) : __( 'Completed', 'woocommerce' );
		
		$all_order_status = wc_get_order_statuses();
		
		$default_order_status = array(
			'wc-pending' => 'Pending payment',
			'wc-processing' => 'Processing',
			'wc-on-hold' => 'On hold',
			'wc-completed' => 'Completed',
			'wc-delivered' => 'Delivered',			
			'wc-cancelled' => 'Cancelled',
			'wc-refunded' => 'Refunded',
			'wc-failed' => 'Failed',
			'wc-ready-pickup' => 'Ready for Pickup',		
			'wc-pickup' => 'Picked up',	
			'wc-partial-shipped' => 'Partially Shipped',		
			'wc-updated-tracking' => 'Updated Tracking',				
		);
		
		foreach ( $default_order_status as $key => $value ) {
			unset($all_order_status[$key]);
		}
		$custom_order_status = $all_order_status;
		
		foreach ( $custom_order_status as $key => $value ) {
			unset($custom_order_status[$key]);			
			$custom_order_status[$key] = __( $value, '' );
		}
		
		$actions_order_status = array( 
			'wc-processing' => __( 'Processing', 'woocommerce' ),
			'wc-completed' => $completed_order_label,
			'wc-partial-shipped' => __( 'Partially Shipped', 'ast-pro' ),		
			'wc-on-hold' => __( 'On Hold', 'woocommerce' ),
			'wc-cancelled' => __( 'Cancelled', 'woocommerce' ),		
			'wc-refunded' => __( 'Refunded', 'woocommerce' ),	
			'wc-failed' => __( 'Failed', 'woocommerce' ),		
		);
		
		$action_order_status_array = array_merge( $actions_order_status, $custom_order_status );		
		
		$form_data = array(
			'ast_order_display_in_fulfillment_dashboard' => array(
				'type'		=> 'simple_multiple_select',
				'title'		=> __( 'Order Statuses to Display', 'ast-pro' ),				
				'options'   => $action_order_status_array,					
				'show'		=> true,
				'class'     => '',
			),
		);
		
		return $form_data;
	}
	
	public function paypal_tracking_settings_options() {
		$gateways = WC()->payment_gateways->payment_gateways();
		
		foreach ( $gateways as $gateway_id => $gateway ) {
			$all_payment_gateway[$gateway_id] = array(
				'status' => __( $gateway->title, '' ),
			);
		}
		
		$settings = array(			
			'ptaa_enable' => array(
				'title'		=> __( 'Enable PayPal Tracking', 'ast-pro' ),
				'type'		=> 'tgl_checkbox',
				'default'	=> 1,
				'show'		=> true,
				'id'		=> 'ptaa_enable',
				'class'		=> '',				
			),
			'ptaa_sandbox' => array(
				'title'		=> __( 'Enable PayPal Sandbox', 'ast-pro' ),
				'type'		=> 'tgl_checkbox',
				'default'	=> 0,
				'show'		=> true,
				'id'		=> 'ptaa_sandbox',
				'class'		=> '',				
			),
			'ptaa_client_id' => array(
				'title'		=> __( 'PayPal API Client ID', 'ast-pro' ),				
				'type'		=> 'text',
				'show'		=> true,
				'id'		=> 'ptaa_client_id',
				'class'		=> 'multiple_checkbox_label ptaa_no_padding_client',
			),
			'ptaa_client_secret' => array(
				'title'		=> __( 'PayPal API Client Secret', 'ast-pro' ),				
				'type'		=> 'text',
				'show'		=> true,
				'id'		=> 'ptaa_client_secret',
				'class'		=> 'multiple_checkbox_label ptaa_no_padding',
				'input_desc' => __( 'How to get your Paypal API credentials?', 'ast-pro' ),
				'desc_url'	=>  'https://developer.paypal.com/docs/platforms/get-started/#get-api-credentials',
			),
			'ptaa_payment_methods' => array(
				'type'		=> 'multiple_select',
				'title'		=> __( 'PayPal Payment Methods', 'ast-pro' ),
				'desc'		=> __( 'Choose for which Payment method you want to sync the tracking to PayPal', 'ast-pro' ),
				'options'   => $all_payment_gateway,					
				'show'		=> true,
				'tooltip'   => __( 'Choose for which Payment method you want to sync the tracking to PayPal', 'ast-pro' ),
				'multiple'	=> 'ptaa_payment_methods[]',
				'class'     => 'multiple_select_li custome_css_select',
			),
			'ptaa_enable_buyer_notification' => array(
				'title'		=> __( 'Send tracking email to the the customer from PayPal', 'ast-pro' ),
				'type'		=> 'tgl_checkbox',
				'default'	=> 0,
				'show'		=> true,
				'id'		=> 'ptaa_enable_buyer_notification',
				'class'		=> '',				
			),
			'ptaa_enable_log' => array(
				'title'		=> __( 'Enable log', 'ast-pro' ),
				'type'		=> 'tgl_checkbox',
				'default'	=> 1,
				'show'		=> true,
				'id'		=> 'ptaa_enable_log',
				'class'		=> '',
				'input_desc'	=> __( 'Log will be added to WooCommerce > Status >', 'ast-pro' ),
				'desc_url'	=>  admin_url( 'admin.php?page=wc-status&tab=logs', 'https' ),
			),
		);
		$settings = apply_filters( 'ptaa_settings_data_array', $settings );
		return $settings;
	}
	
	/*
	* settings form save
	* save settings of all tab
	*
	* @since   1.0
	*/
	public function ptw_settings_tab_save_callback() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'ptw_settings_tab', 'ptw_settings_tab_nonce' );
		
		$data = $this->paypal_tracking_settings_options();
		foreach ( $data as $key => $val ) {
			if ( isset( $val['type'] ) && 'multiple_select' == $val['type'] ) {					
				
				if ( isset( $_POST[ $key ] ) ) {
					
					foreach ( $val['options'] as $op_status => $op_data ) {
						$_POST[ $key ][$op_status] = 0;					
					}
				
					foreach ( wc_clean( $_POST[ $key ] ) as $key1 => $status ) {
						$_POST[ $key ][$status] = 1;											
					}	
					update_option( $key, wc_clean($_POST[ $key ]) );
				} else {
					update_option( $key, '' );
				}
				
			} else {
				if ( isset( $_POST[ $key ] ) ) {						
					update_option( $key, wc_clean($_POST[ $key ]) );
				}	
			}			
		}				
		echo json_encode( array('success' => 'true') );
		die();
	}
	
	/*
	* settings form save
	*/
	public function wc_ast_settings_form_update_callback() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		if ( ! empty( $_POST ) && check_admin_referer( 'wc_ast_settings_form', 'wc_ast_settings_form_nonce' ) ) {
			
			$data = $this->get_add_tracking_options();						
			
			foreach ( $data as $key => $val ) {				
				
				if ( isset( $val['type'] ) && 'multiple_select' == $val['type'] ) {					
					
					foreach ( $val['options'] as $op_status => $op_data ) {
						$_POST[ $key ][$op_status] = 0;
					}
					
					if ( isset( $_POST[ $key ] ) ) {
						foreach ( wc_clean( $_POST[ $key ] ) as $key1 => $status ) {
							$_POST[ $key ][$status] = 1;						
						}
					}
					
					if ( isset( $_POST[ $key ] ) ) {	
						update_option( $key, wc_clean( $_POST[ $key ] ) );
					}
					
				} else {
					
					if ( isset( $_POST[ $key ] ) ) {						
						update_option( $key, wc_clean( $_POST[ $key ] ) );
					}	
				}
				
				if ( isset( $val['type'] ) && 'inline_checkbox' == $val['type'] ) {
					foreach ( (array) $val['checkbox_array'] as $key1 => $val1 ) {
						if ( isset( $_POST[ $key1 ] ) ) {						
							update_option( $key1, wc_clean( $_POST[ $key1 ] ) );
						}
					}					
				}
			}

			$data1 = $this->get_customer_view_options();						
			
			foreach ( $data1 as $key => $val ) {				
				
				if ( isset( $val['type'] ) && 'multiple_select' == $val['type'] ) {					
					
					foreach ( $val['options'] as $op_status => $op_data ) {
						$_POST[ $key ][$op_status] = 0;
					}
					
					foreach ( wc_clean( $_POST[ $key ] ) as $key1 => $status ) {
						$_POST[ $key ][$status] = 1;						
					}
					
					update_option( $key, wc_clean( $_POST[ $key ] ) );					
					
				} else {
					
					if ( isset( $_POST[ $key ] ) ) {						
						update_option( $key, wc_clean( $_POST[ $key ] ) );
					}	
				}
			}
			
			$data1 = $this->get_fulfillment_dashboard_options();						
			
			foreach ( $data1 as $key => $val ) {
				if ( isset( $_POST[ $key ] ) ) {	
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			}						

			$data2 = $this->get_shipment_tracking_api_options();						
			
			foreach ( $data2 as $key => $val ) {				
				
				if ( isset( $_POST[ $key ] ) ) {						
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			}
			
			$wc_ast_status_shipped = isset( $_POST[ 'wc_ast_status_shipped' ] ) ? wc_clean( $_POST[ 'wc_ast_status_shipped' ] ) : '';
			update_option( 'wc_ast_status_shipped', $wc_ast_status_shipped );
			
			$data = $this->get_partial_shipped_data();						
			
			foreach ( $data as $key => $val ) {				
				
				if ( 'wcast_enable_partial_shipped_email' == $key ) {						
					if ( isset( $_POST['wcast_enable_partial_shipped_email'] ) ) {						
						
						if ( 1 == $_POST['wcast_enable_partial_shipped_email'] ) {
							update_option( 'customizer_partial_shipped_order_settings_enabled', wc_clean( $_POST['wcast_enable_partial_shipped_email'] ) );
							$enabled = 'yes';
						} else {
							update_option( 'customizer_partial_shipped_order_settings_enabled', '' );
							$enabled = 'no';
						}						
						
						$wcast_enable_partial_shipped_email = get_option( 'woocommerce_customer_partial_shipped_order_settings' );
						$wcast_enable_partial_shipped_email['enabled'] = $enabled;
						update_option( 'woocommerce_customer_partial_shipped_order_settings', $wcast_enable_partial_shipped_email );	
					}	
				}										
				
				if ( isset( $_POST[ $key ] ) ) {						
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			}
			
			$data = $this->get_updated_tracking_data();						
			
			foreach ( $data as $key => $val ) {				
				
				if ( 'wcast_enable_updated_tracking_email' == $key ) {						
					if ( isset( $_POST['wcast_enable_updated_tracking_email'] ) ) {						
						if ( 1 == $_POST['wcast_enable_updated_tracking_email'] ) {
							update_option( 'customizer_updated_tracking_order_settings_enabled', wc_clean( $_POST['wcast_enable_updated_tracking_email'] ) );
							$enabled = 'yes';
						} else {
							update_option( 'customizer_updated_tracking_order_settings_enabled', '' );
							$enabled = 'no';
						}																		
						
						$wcast_enable_updated_tracking_email = get_option( 'woocommerce_customer_updated_tracking_order_settings' );
						$wcast_enable_updated_tracking_email['enabled'] = $enabled;
						update_option( 'woocommerce_customer_updated_tracking_order_settings', $wcast_enable_updated_tracking_email );	
					}	
				}										
				
				if ( isset( $_POST[ $key ] ) ) {						
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			}	
			
			$data = $this->get_shipped_data();			
			
			foreach ( $data as $key => $val ) {
				
				if ( 'wcast_enable_shipped_email' == $key ) {
					if ( isset( $_POST[ 'wcast_enable_shipped_email' ] ) ) {						
						
						if ( 1 == $_POST['wcast_enable_shipped_email'] ) {
							update_option( 'customizer_shipped_order_settings_enabled', wc_clean( $_POST[ 'wcast_enable_shipped_email' ] ) );
							$enabled = 'yes';
						} else {
							update_option( 'customizer_shipped_order_settings_enabled', '' );
							$enabled = 'no';
						}						
						
						$wcast_enable_shipped_email = get_option( 'woocommerce_customer_shipped_order_settings' );
						$wcast_enable_shipped_email['enabled'] = $enabled;
						update_option( 'woocommerce_customer_shipped_order_settings', $wcast_enable_shipped_email );	
					}	
				}										
				
				if ( isset( $_POST[ $key ] ) ) {
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			}	
			
			$data = $this->get_delivered_data();						
			foreach ( $data as $key => $val ) {				
				if ( isset( $_POST[ $key ] ) ) {						
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			}

			$data = $this->paypal_tracking_settings_options();
			foreach ( $data as $key => $val ) {
				if ( isset( $val['type'] ) && 'multiple_select' == $val['type'] ) {					
					
					if ( isset( $_POST[ $key ] ) ) {
						
						foreach ( $val['options'] as $op_status => $op_data ) {
							$_POST[ $key ][$op_status] = 0;					
						}
					
						foreach ( wc_clean( $_POST[ $key ] ) as $key1 => $status ) {
							$_POST[ $key ][$status] = 1;											
						}	
						update_option( $key, wc_clean($_POST[ $key ]) );
					} else {
						update_option( $key, '' );
					}
					
				} else {
					if ( isset( $_POST[ $key ] ) ) {						
						update_option( $key, wc_clean($_POST[ $key ]) );
					}	
				}			
			}		
		}
	}
	
	/*
	* get updated tracking status settings array data
	* return array
	*/
	public function get_updated_tracking_data() {		
		$form_data = array(			
			'wc_ast_status_updated_tracking' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable custom order status “Updated Tracking"', '' ),				
				'show'		=> true,
				'class'     => '',
			),			
			'wc_ast_status_updated_tracking_label_color' => array(
				'type'		=> 'color',
				'title'		=> __( 'Updated Tracking Label color', '' ),				
				'class'		=> 'updated_tracking_status_label_color_th',
				'show'		=> true,
			),
			'wc_ast_status_updated_tracking_label_font_color' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Updated Tracking Label font color', '' ),
				'options'   => array( 
									'' =>__( 'Select', 'woocommerce' ),
									'#fff' =>__( 'Light', '' ),
									'#000' =>__( 'Dark', '' ),
								),			
				'class'		=> 'updated_tracking_status_label_color_th',
				'show'		=> true,
			),			
			'wcast_enable_updated_tracking_email' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable the Updated Tracking order status email', '' ),
				'title_link'=> "<a class='settings_edit' href='" . ast_pro_ps_customizer()->get_customizer_url( 'custom_order_status_email' , 'updated_tracking' ) . "'>" . __( 'Edit', 'woocommerce' ) . '</a>',
				'class'		=> 'updated_tracking_status_label_color_th',
				'show'		=> true,
			),			
		);
		return $form_data;
	}

	/*
	* get Partially Shipped array data
	* return array
	*/
	public function get_partial_shipped_data() {
		$form_data = array(			
			'wc_ast_status_partial_shipped' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable custom order status “Partially Shipped"', '' ),				
				'show'		=> true,
				'class'     => '',
			),			
			'wc_ast_status_partial_shipped_label_color' => array(
				'type'		=> 'color',
				'title'		=> __( 'Partially Shipped Label color', '' ),				
				'class'		=> 'partial_shipped_status_label_color_th',
				'show'		=> true,
			),
			'wc_ast_status_partial_shipped_label_font_color' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Partially Shipped Label font color', '' ),
				'options'   => array( 
									'' =>__( 'Select', 'woocommerce' ),
									'#fff' =>__( 'Light', '' ),
									'#000' =>__( 'Dark', '' ),
								),			
				'class'		=> 'partial_shipped_status_label_color_th',
				'show'		=> true,
			),			
			'wcast_enable_partial_shipped_email' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable the Partially Shipped order status email', '' ),
				'title_link'=> "<a class='settings_edit' href='" . ast_pro_ps_customizer()->get_customizer_url( 'custom_order_status_email', 'partially_shipped' ) . "'>" . __( 'Edit', 'woocommerce' ) . '</a>',
				'class'		=> 'partial_shipped_status_label_color_th',
				'show'		=> true,
			),			
		);
		return $form_data;
	}

	/*
	* get Shipped array data
	* return array
	*/
	public function get_shipped_data() {
		$form_data = array(			
			'wc_ast_status_new_shipped' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable custom order status “Shipped"', '' ),				
				'show'		=> true,
				'class'     => '',
			),			
			'wc_ast_status_shipped_label_color' => array(
				'type'		=> 'color',
				'title'		=> __( 'Shipped Label color', '' ),				
				'class'		=> 'shipped_status_label_color_th',
				'show'		=> true,
			),
			'wc_ast_status_shipped_label_font_color' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Shipped Label font color', '' ),
				'options'   => array( 
									'' =>__( 'Select', 'woocommerce' ),
									'#fff' =>__( 'Light', '' ),
									'#000' =>__( 'Dark', '' ),
								),			
				'class'		=> 'shipped_status_label_color_th',
				'show'		=> true,
			),			
			'wcast_enable_shipped_email' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable the Shipped order status email', '' ),				
				'class'		=> 'shipped_status_label_color_th',
				'show'		=> true,
			),			
		);
		return $form_data;
	}

	/*
	* get settings tab array data
	* return array
	*/
	public function get_delivered_data() {		
		$form_data = array(			
			'wc_ast_status_delivered' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable custom order status “Delivered"', '' ),				
				'show'		=> true,
				'class'     => '',
			),			
			'wc_ast_status_label_color' => array(
				'type'		=> 'color',
				'title'		=> __( 'Delivered Label color', '' ),				
				'class'		=> 'status_label_color_th',
				'show'		=> true,
			),
			'wc_ast_status_label_font_color' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Delivered Label font color', '' ),
				'options'   => array( 
					'' =>__( 'Select', 'woocommerce' ),
					'#fff' =>__( 'Light', '' ),
					'#000' =>__( 'Dark', '' ),
				),			
				'class'		=> 'status_label_color_th',
				'show'		=> true,
			),							
		);
		return $form_data;
	}	
	
	/**
	 * Add filter on orders admin for fullfillment status
	 *
	 * @since 1.0
	 */
	public function filter_orders_by_fullfillment_status() {
		global $typenow;
		if ( 'shop_order' === $typenow ) { 
			?>
			<select name="_shop_order_fullfillment_status" id="dropdown_shop_order_fullfillment_status">
				<option value=""><?php esc_html_e( 'Filter by fullfillment status', 'ast-pro' ); ?></option>
				<option value="fullfilled"><?php esc_html_e( 'Fullfilled', 'ast-pro' ); ?></option>
				<option value="unfulfilled"><?php esc_html_e( 'Unfulfilled', 'ast-pro' ); ?></option>
			</select>
			<?php
		}
	}	
	
	/**
	 * Process bulk filter action for fullfillment status
	 *
	 * @since 1.0
	 * @param array $vars query vars without filtering
	 * @return array $vars query vars with (maybe) filtering
	 */
	public function filter_orders_by_fullfillment_status_query( $vars ) {
		global $typenow;		
		if ( 'shop_order' === $typenow && isset( $_GET['_shop_order_fullfillment_status'] ) && '' != $_GET['_shop_order_fullfillment_status'] ) {
			if ( 'fullfilled' == $_GET['_shop_order_fullfillment_status'] ) {
				$vars['meta_query'][] = array(
					'key'       => '_wc_shipment_tracking_items',
					'compare' => 'EXISTS',
				);	
			} else if ( 'unfulfilled' == $_GET['_shop_order_fullfillment_status'] ) {
				$vars['meta_query'][] = array(
					'key'       => '_wc_shipment_tracking_items',
					'compare' => 'NOT EXISTS',
				);
			}			
		}
		return $vars;
	}	
	

	/*
	* functions for add integrations options in AST settings
	*/
	public function integrations_settings_options() {
		$form_data = array(		
			'enable_ordoro_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Ordoro tracking integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the Ordoro', 'ast-pro' ),
				'img'		=> 'ordoro.png',
				'show'		=> true,
				'default'   => 1, 
				'disabled'  => false, 
				'class'     => '',
			),
			'enable_cartrover_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the CartRover tracking integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the CartRover', 'ast-pro' ),
				'img'		=> 'cart-rover.png',
				'show'		=> true,
				'default'   => 1, 
				'disabled'  => false, 
				'class'     => '',
			),
			'enable_parcelforce_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the ParcelForce tracking integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the ParcelForce', 'ast-pro' ),
				'img'		=> 'parcelfoce.png',
				'show'		=> true,
				'default'   => 1, 
				'disabled'  => false, 
				'class'     => '',
			),
			'enable_shipstation_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the ShipStation integration', 'ast-pro' ),				
				'desc'	    => __( 'Adding tracking information to your orders shipped with ShipStation and automate your workflow', 'ast-pro' ),
				'img'		=> 'shipstation.png',
				'show'		=> true,
				'default'  	=> ( is_plugin_active( 'woocommerce-shipstation-integration/woocommerce-shipstation.php' ) ) ? 1 : 0,
				'disabled'  => ( is_plugin_active( 'woocommerce-shipstation-integration/woocommerce-shipstation.php' ) ) ? false : true, 
				'class'     => '',
			),
			'enable_wc_shipping_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the WC Shipping integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with WooCommerce Shipping to the Shipment Tracking and automate your workflow', 'ast-pro' ),
				'img'		=> 'woo-shipping.png',
				'show'		=> true,
				'default'   => ( is_plugin_active( 'woocommerce-services/woocommerce-services.php' ) ) ? 1 : 0, 
				'disabled'  => ( is_plugin_active( 'woocommerce-services/woocommerce-services.php' ) ) ? false : true, 
				'class'     => '',
			),
			'enable_ups_shipping_label_pluginhive' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the WooCommerce UPS Shipping Plugin with Print Label plugin integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders when generating shipping labels using the WooCommerce UPS Shipping Plugin with Print Label plugin by PluginHive', 'ast-pro' ),
				'img'		=> 'woo-UPS-shipping.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
			),			
			'enable_quickbooks_commerce_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the QuickBooks Commerce (formerly TradeGecko) Integrations', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with QuickBooks Commerce and automate your workflow', 'ast-pro' ),
				'img'		=> 'quickbooks.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
			),
			'enable_readytoship_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the ReadyToShip integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with ReadyToShip and automate your workflow', 'ast-pro' ),
				'img'		=> 'readytoship.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false, 
				'class'     => '',
			),
			'enable_royalmail_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Royal Mail Click & Drop integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Royal Mail Click & Drop and automate your workflow', 'ast-pro' ),
				'img'		=> 'royal-mail.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),	
			'enable_customcat_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the CustomCat integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with CustomCat and automate your workflow', 'ast-pro' ),
				'img'		=> 'customcat.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),
			'enable_dear_inventory_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Dear Systems integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Dear Systems and automate your workflow', 'ast-pro' ),
				'img'		=> 'dear-system.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),
			'enable_printify_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Printify integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Printify and automate your workflow', 'ast-pro' ),
				'img'		=> 'printify.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),			
			'enable_picqer_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Picqer integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Picqer and automate your workflow', 'ast-pro' ),
				'img'		=> 'picqer.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),
			'enable_3plwinner_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the 3plwinner integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with 3plwinner and automate your workflow', 'ast-pro' ),
				'img'		=> '3plwinner.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),			
			'enable_dianxiaomi_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Dianxiaomi integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with Dianxiaomi and automate your workflow', 'ast-pro' ),
				'img'		=> 'dianxiaomi.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),
			'enable_eiz_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the EIZ integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with EIZ and automate your workflow', 'ast-pro' ),
				'img'		=> 'eiz.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),
			'enable_shippypro_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the Shippypro integration', 'ast-pro' ),				
				'desc'   	=> __( 'Adding tracking information to your orders shipped with shippypro and automate your workflow', 'ast-pro' ),
				'img'		=> 'shippypro.png',
				'show'		=> true,
				'default'   => 0, 
				'disabled'  => false,
				'class'     => '',
			),
			'enable_ali2woo_integration' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'Enable the AliExpress Dropshipping integration', 'ast-pro' ),				
				'desc'   	=> __( 'Add Tracking Information in AST meta fields when you automatically sync tracking numbers from aliexpress orders', 'ast-pro' ),
				'img'		=> 'aliexpress.png',
				'show'		=> true,
				'default'   => ( is_plugin_active( 'ali2woo-lite/ali2woo-lite.php' ) || is_plugin_active( 'ali2woo/ali2woo.php' ) ) ? 1 : 0,
				'disabled'	=> ( is_plugin_active( 'ali2woo-lite/ali2woo-lite.php' ) || is_plugin_active( 'ali2woo/ali2woo.php' ) ) ? false : true,
				'class'     => '',
			),	
			'enable_pdf_invoice_integration_ewout' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'PDF invoices integration for WooCommerce PDF Invoices & Packing Slips plugin By Ewout Fernhout', 'ast-pro' ),				
				'desc'   	=> __( 'Add tracking information in invoice PDF generate by WooCommerce PDF Invoices & Packing Slips plugin By Ewout Fernhout', 'ast-pro' ),
				'img'		=> 'WooCommerce-PDF-Invoices-Packing-Slips.png',
				'show'		=> true,
				'default'   => ( is_plugin_active( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' ) ) ? 1 : 0,
				'disabled'	=> ( is_plugin_active( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' ) ) ? false : true,
				'class'     => '',
			),
			'enable_pdf_invoice_integration_bas' => array(
				'type'		=> 'tgl_checkbox',
				'title'		=> __( 'PDF invoices integration for WooCommerce PDF Invoices plugin By Bas Elbers', 'ast-pro' ),				
				'desc'   	=> __( 'Add tracking information in invoice PDF generate by WooCommerce PDF Invoices plugin By Bas Elbers', 'ast-pro' ),
				'img'		=> 'WooCommerce-PDF-Invoices.png',
				'show'		=> true,
				'default'   => ( is_plugin_active( 'woocommerce-pdf-invoices/bootstrap.php' ) ) ? 1 : 0,
				'disabled'	=> ( is_plugin_active( 'woocommerce-pdf-invoices/bootstrap.php' ) ) ? false : true,
				'class'     => '',
			),
		);
		
		return $form_data;
	}	
	
	/*
	* get Order Status data
	* return array
	*/
	public function get_osm_data() {
		$osm_data = array(			
			'partial_shipped' => array(
				'id'		=> 'wc_ast_status_partial_shipped',
				'default'	=> 1,
				'slug'   	=> 'partial-shipped',
				'label'		=> __( 'Partially Shipped', 'ast-pro' ),				
				'label_class' => 'wc-partially-shipped',
				'option_id'	=> 'woocommerce_customer_partial_shipped_order_settings',				
				'edit_email'=> ast_pro_ps_customizer()->get_customizer_url( 'custom_partially_shipped_email', 'partially_shipped' ),
				'label_color_field' => 'wc_ast_status_partial_shipped_label_color',	
				'font_color_field' => 'wc_ast_status_partial_shipped_label_font_color',	
				'email_field' => 'wcast_enable_partial_shipped_email',					
			),	
			'shipped' => array(
				'id'				=> 'wc_ast_status_new_shipped',
				'default'			=> 0,
				'slug'   			=> 'shipped',
				'label'				=> __( 'Shipped', 'ast-pro' ),				
				'label_class' 		=> 'wc-shipped',
				'option_id'			=> 'woocommerce_customer_shipped_order_settings',				
				'edit_email'		=> ast_pro_shipped_customizer()->get_customizer_url( 'custom_shipped_email', 'shipped' ),
				'label_color_field' => 'wc_ast_status_shipped_label_color',	
				'font_color_field' 	=> 'wc_ast_status_shipped_label_font_color',	
				'email_field' 		=> 'wcast_enable_shipped_email',					
			),	
			'delivered' => array(
				'id'		=> 'wc_ast_status_delivered',
				'default'			=> 0,
				'slug'   	=> 'delivered',
				'label'		=> __( 'delivered', 'woo-advanced-shipment-tracking' ),				
				'label_class' => 'wc-delivered',
				'option_id'	=> 'woocommerce_customer_delivered_order_settings',				
				'edit_email'=> '',
				'label_color_field' => 'wc_ast_status_label_color',	
				'font_color_field' => 'wc_ast_status_label_font_color',	
				'email_field' => '',					
			),				
		);
		
		$updated_tracking_status = get_option( 'wc_ast_status_updated_tracking', 0 );
		
		if ( true == $updated_tracking_status ) {	
			$updated_tracking_data = array(			
				'updated_tracking' => array(
					'id'		=> 'wc_ast_status_updated_tracking',
					'slug'   	=> 'updated-tracking',
					'label'		=> __( 'Updated Tracking', 'ast-pro' ),				
					'label_class' => 'wc-updated-tracking',
					'option_id'	=> 'woocommerce_customer_updated_tracking_order_settings',				
					'edit_email'=> ast_pro_ps_customizer()->get_customizer_url( 'custom_order_status_email', 'updated_tracking' ),
					'label_color_field' => 'wc_ast_status_updated_tracking_label_color',	
					'font_color_field' => 'wc_ast_status_updated_tracking_label_font_color',	
					'email_field' => 'wcast_enable_updated_tracking_email',					
				),		
			);
			$osm_data = array_merge( $osm_data, $updated_tracking_data );
		}
		return apply_filters( 'ast_osm_data', $osm_data );		
	}
	
	/*
	* Integrations settings form save
	*/
	public function integrations_settings_form_update_callback() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		if ( ! empty( $_POST ) && check_admin_referer( 'integrations_settings_form', 'integrations_settings_form_nonce' ) ) {
			
			$data = $this->integrations_settings_options();						
			
			foreach ( $data as $key => $val ) {
				if ( isset( $_POST[ $key ] ) ) {						
					update_option( $key, wc_clean( $_POST[ $key ] ) );
				}
			} 						
		}
	}		
	
	public function edit_provider_class( $class ) {
		return 'edit_provider_pro';
	}	

	/*
	* change style of order label
	*/	
	public function footer_function() {
		if ( !is_plugin_active( 'woocommerce-order-status-manager/woocommerce-order-status-manager.php' ) ) {
			
			$bg_color = get_option( 'wc_ast_status_shipped_label_color', '#03a9f4' );
			$color = get_option( 'wc_ast_status_shipped_label_font_color', '#fff' );
			
			$delivered_bg_color = get_option( 'wc_ast_status_label_color', '#59c889' );
			$delivered_color = get_option( 'wc_ast_status_label_font_color', '#fff' );						
			
			$ps_bg_color = get_option( 'wc_ast_status_partial_shipped_label_color', '#1e73be' );
			$ps_color = get_option( 'wc_ast_status_partial_shipped_label_font_color', '#fff' );
			
			$ut_bg_color = get_option( 'wc_ast_status_updated_tracking_label_color', '#23a2dd' );
			$ut_color = get_option( 'wc_ast_status_updated_tracking_label_font_color', '#fff' );
			?>
			<style>
			.order-status.status-shipped,.order-status-table .order-label.wc-shipped{
				background: <?php echo esc_html( $bg_color ); ?>;
				color: <?php echo esc_html( $color ); ?>;
			}	
			.order-status.status-delivered,.order-status-table .order-label.wc-delivered{
				background: <?php echo esc_html( $delivered_bg_color ); ?>;
				color: <?php echo esc_html( $delivered_color ); ?>;
			}					
			.order-status.status-partial-shipped,.order-status-table .order-label.wc-partially-shipped{
				background: <?php echo esc_html( $ps_bg_color ); ?>;
				color: <?php echo esc_html( $ps_color ); ?>;
			}
			.order-status.status-updated-tracking,.order-status-table .order-label.wc-updated-tracking{
				background: <?php echo esc_html( $ut_bg_color ); ?>;
				color: <?php echo esc_html( $ut_color ); ?>;
			}	
			</style>
			<?php
		}		
	}

	/*
	* Function for add tracking button in order details page
	*/
	public function ast_add_tracking_btn() {
		global $post;		
		echo '<a class="button button-primary btn_ast2 btn_full add_inline_tracking" style="margin-top: 10px;text-align: center;" href="#' . esc_html( $post->ID ) . '">' . esc_html__( 'Add Tracking Info', 'ast-pro' ) . '</a>';
	}	
	
	/*
	* Return formatted order id from custom order number
	*/
	public function ast_get_formated_order_id( $order_id ) {
		
		// Compatibilty code with Custom Order Numbers for WooCommerce Pro by Tyche Softwares
		if ( class_exists( 'Alg_WC_Custom_Order_Numbers_Core' ) ) {
			$offset     = 0;
			$block_size = 512;
			while ( true ) {
				$args = array(
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
					'posts_per_page' => $block_size,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'offset'         => $offset,
					'fields'         => 'ids',
				);
				
				$loop = new WP_Query( $args );
				
				if ( ! $loop->have_posts() ) {
					break;
				}
				
				foreach ( $loop->posts as $new_order_id ) {
					$_order = wc_get_order( $new_order_id );
					$Alg_WC = new Alg_WC_Custom_Order_Numbers_Core();
					$_order_number = $Alg_WC->display_order_number( $new_order_id, $_order );
					if ( $_order_number === $order_id ) {
						$order_id = $new_order_id;
						//echo $order_id;exit;
						break;
					}
				}
				$offset += $block_size;					
			}
		}
		
		return $order_id;
	}	
	
	/*
	* Return order status array for mark order as fields
	*/
	public function mark_order_as_fields_data( $order_status_array ) {
		
		$wc_ast_status_new_shipped = get_option( 'wc_ast_status_new_shipped' );
		$wc_ast_default_mark_shipped = get_option( 'wc_ast_default_mark_shipped' );
		
		if ( !$wc_ast_status_new_shipped ) {
			return $order_status_array;
		}	
		
		$shipped_order_status = array(
			'change_order_to_custom_shipped' => array(					
				'name'		=> 'change_order_to_shipped',
				'class'		=> 'mark_shipped_checkbox',
				'label'		=> __( 'Shipped', 'ast-pro'),
				'checked'	=> ( 1 == $wc_ast_default_mark_shipped ) ? true : false,
				'show'		=> ( 1 == $wc_ast_status_new_shipped ) ? true : false,
			),			
		);
		unset( $order_status_array['change_order_to_shipped'] );
		$status_array = array_merge( $shipped_order_status, $order_status_array );
		return $status_array;
	}
	
	/**
	 * Send email when order status change to 'Partial Shipped'	 
	*/
	public function email_trigger_partial_shipped( $order_id, $order = false ) {		
		WC()->mailer()->emails['WC_Email_Customer_Partial_Shipped_Order']->trigger( $order_id, $order );
	}	
	
	/**
	 * Send email when order status change to 'Updated Tracking'	 
	*/
	public function email_trigger_updated_tracking( $order_id, $order = false ) {		
		WC()->mailer()->emails['WC_Email_Customer_Updated_Tracking_Order']->trigger( $order_id, $order );
	}
	
	/**
	 * Send email when order status change to "Shipped"
	 *
	*/
	public function email_trigger_shipped( $order_id, $order = false ) {
		WC()->mailer()->emails[ 'WC_Email_Customer_Shipped_Order' ]->trigger( $order_id, $order );
	}
	
	/*
	* Get providers list html
	*/
	public function get_provider_html( $default_shippment_providers, $status ) {
		$WC_Countries = new WC_Countries();
		$upload_dir   = wp_upload_dir();	
		$ast_directory = $upload_dir['baseurl'] . '/ast-shipping-providers/'; 
		?>
		<div class="provider_list">
			<?php 
			if ( $default_shippment_providers ) {
				if ( 'custom' == $status ) {
					?>
				</br><a href="javaScript:void(0);" class="button-primary btn_ast2 btn_large add_custom_provider" id="add-custom"><span class="dashicons dashicons-plus-alt"></span><?php esc_html_e( 'Add Custom Provider', 'ast-pro' ); ?></a>	
			<?php } ?>
			<div class="provider-grid-row grid-row">
				<?php 
				foreach ( $default_shippment_providers as $d_s_p ) {
				$provider_type = ( 1 == $d_s_p->shipping_default ) ? 'default_provider' : 'custom_provider';
				?>
				<div class="grid-item hip-item">					
					<div class="grid-top">
						<div class="grid-provider-img">
							<?php  
							$custom_thumb_id = $d_s_p->custom_thumb_id;
							if ( 1 == $d_s_p->shipping_default ) {
								if ( 0 != $custom_thumb_id ) {
									$image_attributes = wp_get_attachment_image_src( $custom_thumb_id , array( '60', '60' ) );
									$provider_image = $image_attributes[0];
								} else {
									$provider_image = $ast_directory . '' . sanitize_title( $d_s_p->provider_name ) . '.png?v=' . ast_pro()->version;
								}
								echo '<img class="provider-thumb" src="' . esc_url( $provider_image ) . '">';
							} else { 
								$image_attributes = wp_get_attachment_image_src( $custom_thumb_id , array( '60', '60' ) );
								
								if ( 0 != $custom_thumb_id ) { 
									echo '<img class="provider-thumb" src="' . esc_url( $image_attributes[0] ) . '">';
								} else { 
									echo '<img class="provider-thumb" src="' . esc_url( ast_pro()->plugin_dir_url() ) . 'assets/images/icon-default.png">';
								}  
							}
							?>							
						</div>
						<div class="grid-provider-name">
							<span class="provider_name">
								<?php 
								esc_html_e( $d_s_p->provider_name );
								
								if ( isset( $d_s_p->custom_provider_name ) && '' != $d_s_p->custom_provider_name ) { 
									esc_html_e( ' (' . $d_s_p->custom_provider_name . ')' ); 
								} 
								
								if ( isset( $d_s_p->api_provider_name ) && '' != $d_s_p->api_provider_name ) {
									
									if ( $this->isJSON( $d_s_p->api_provider_name ) && class_exists( 'ast_pro' ) ) {
										$api_count = count( json_decode( $d_s_p->api_provider_name ) );
									} else {
										$api_count = 1;
									}
									$api_text = __( 'API aliases', 'ast-pro' );
									esc_html_e( ' (' . $api_count . ' ' . $api_text . ')' );
								}
								?>
							</span>																		
							<span class="provider_country">
								<?php
								$search  = array('(US)', '(UK)');
								$replace = array('', '');
								
								if ( $d_s_p->shipping_country && 'Global' != $d_s_p->shipping_country ) {
									esc_html_e( str_replace( $search, $replace, $WC_Countries->countries[ $d_s_p->shipping_country ] ) );
								} elseif ( $d_s_p->shipping_country && 'Global' == $d_s_p->shipping_country ) {
									esc_html_e( 'Global' );
								} 
								?>
							</span>
						</div>
						<div class="grid-provider-settings">
							<?php
							if ( 0 == $d_s_p->shipping_default ) { 
								echo '<span class="dashicons dashicons-trash remove provider_actions_btn" data-pid="' . esc_html( $d_s_p->id ) . '"></span>';
							} 
							?>
							<span class="dashicons dashicons-admin-generic edit_provider provider_actions_btn" data-provider="<?php esc_html_e( $provider_type ); ?>" data-pid="<?php esc_html_e( $d_s_p->id ); ?>"></span>
						</div>
					</div>
					<div class="grid-bottom">
						<div class="grid-provider-ts">
							<?php 
							if ( 1 == $d_s_p->trackship_supported ) { 
								echo '<span class="dashicons dashicons-yes-alt"></span>'; 
							} else { 
								echo '<span class="dashicons dashicons-dismiss"></span>'; 
							} 
							?>
							<span>TrackShip</span>
						</div>
						<div class="grid-provider-enable">
							<?php $checked = ( 1 == $d_s_p->display_in_order ) ? 'checked' : ''; ?>
							<input class="ast-tgl ast-tgl-flat status_slide" id="list-switch-<?php esc_html_e( $d_s_p->id ); ?>" name="select_custom_provider[]" type="checkbox" <?php esc_html_e( $checked ); ?> value="<?php esc_html_e( $d_s_p->id ); ?>"/>
							<label class="ast-tgl-btn" for="list-switch-<?php esc_html_e( $d_s_p->id ); ?>"></label>
						</div>
					</div>						
				</div>
				<?php } ?>
								
			</div>			
			<?php 
			} else { 
				if ( 'custom' == $status ) {
					?>
				<p class="provider_message">
					<?php
					/* translators: %s: replace with status */
					printf( esc_html_e( 'You did not create any %s shipping providers yet.', 'ast-pro' ), esc_html( $status ) ); 
					?>
				</p>
				<a href="javaScript:void(0);" class="button-primary btn_ast2 btn_large add_custom_provider" id="add-custom">
					<span class="dashicons dashicons-plus-alt"></span>
					<?php esc_html_e( 'Add Custom Provider', 'ast-pro' ); ?>
				</a>	
			<?php 
				} else {
					?>
				<p class="provider_message">
					<?php
					/* translators: %s: replace with status */	
					printf( esc_html_e( "You don't have any %s shipping providers.", 'ast-pro' ), esc_html( $status ) ); 
					?>
				</p>
				<?php 
				} 
			}
			?>
		</div>	
		<?php 
	}
	
	/*
	* filter shipping providers by stats
	*/
	public function filter_shipiing_provider_by_status_fun() {		
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );
		
		$status = isset( $_POST['status'] ) ? wc_clean( $_POST['status'] ) : '';
		
		global $wpdb;		
		
		if ( 'active' == $status ) {				
			$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table WHERE display_in_order = 1" );	
		}
		
		if ( 'inactive' == $status ) {			
			$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table WHERE display_in_order = 0" );	
		}
		
		if ( 'custom' == $status ) {			
			$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table WHERE shipping_default = 0" );	
		}
		
		if ( 'all' == $status ) {
			$status = '';
			$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );	
		}
		
		$html = $this->get_provider_html( $default_shippment_providers, $status );		
		exit;		
	}
	
	/*
	* Check if valid json
	*/
	public function isJSON( $string ) {
		return is_string( $string ) && is_array( json_decode( $string, true ) ) && ( json_last_error() == JSON_ERROR_NONE ) ? true : false;
	}
	
	/*
	* Update shipment provider status
	*/
	public function update_shipment_status_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );
		
		$checked = isset( $_POST['checked'] ) ? wc_clean( $_POST['checked'] ) : '';
		$id = isset( $_POST['id'] ) ? wc_clean( $_POST['id'] ) : '';
		
		global $wpdb;
		$success = $wpdb->update( $this->table, 
			array(
				'display_in_order' => $checked,
			),	
			array( 'id' => $id )
		);
		exit;	
	}
	
	/**
	* Create slug from title
	*/
	public static function create_slug( $text ) {
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);
		
		// transliterate
		//$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		
		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);
		
		// trim
		$text = trim($text, '-');
		
		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);
		
		// lowercase
		$text = strtolower($text);
		
		$text = 'cp-' . $text;
		
		if ( empty( $text ) ) {
			return '';
		}
		
		return $text;
	}

	/**
	* Add custom shipping provider function 
	*/
	public function add_custom_shipment_provider_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'add_custom_shipment_provider', 'add_custom_shipment_provider_nonce' );
		
		global $wpdb;
				
		$tracking_url = isset( $_POST['tracking_url'] ) ? wc_clean( $_POST['tracking_url'] ) : '';
		$thumb_id = isset( $_POST['thumb_id'] ) ? wc_clean( $_POST['thumb_id'] ) : '';
		$shipping_provider = isset( $_POST['shipping_provider'] ) ? wc_clean( $_POST['shipping_provider'] ) : '';
		$shipping_display_name = isset( $_POST['shipping_display_name'] ) ? wc_clean( $_POST['shipping_display_name'] ) : '';
		$shipping_country = isset( $_POST['shipping_country'] ) ? wc_clean( $_POST['shipping_country'] ) : '';		
		
		$provider_slug = $this->create_slug( $shipping_provider );		
		
		if ( '' == $provider_slug ) {
			$provider_slug = $shipping_provider;
		}
		
		$data_array = array(
			'shipping_country' => $shipping_country,
			'provider_name' => $shipping_provider,
			'custom_provider_name' => $shipping_display_name,
			'ts_slug' => $provider_slug,
			'provider_url' => $tracking_url,
			'custom_thumb_id' => $thumb_id,			
			'display_in_order' => 1,
			'shipping_default' => 0,
		);
		
		$result = $wpdb->insert( $this->table, $data_array );
		
		$status = 'all';
		$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );		
		$html = $this->get_provider_html( $default_shippment_providers, $status );		
		exit;		
	}
	
	/*
	* Delete provide by ajax
	*/
	public function woocommerce_shipping_provider_delete() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );	

		$provider_id = isset( $_POST['provider_id'] ) ? wc_clean( $_POST['provider_id'] ) : '';
		
		if ( ! empty( $provider_id ) ) {
			global $wpdb;
			$where = array(
				'id' => $provider_id,
				'shipping_default' => 0
			);
			$wpdb->delete( $this->table, $where );
		}
		$status = 'all';
		
		$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );
		$html = $this->get_provider_html( $default_shippment_providers, $status );		
		exit;
	}
	
	/**
	* Get shipping provider details fun 
	*/
	public function get_provider_details_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );
		
		$id = isset( $_POST['provider_id'] ) ? wc_clean( $_POST['provider_id'] ) : '';		
		global $wpdb;
		
		$shippment_provider = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->table WHERE id=%d", $id ) );
		
		if ( 0 != $shippment_provider[0]->custom_thumb_id ) {
			$image = wp_get_attachment_url( $shippment_provider[0]->custom_thumb_id );	
		} else {
			$image = null;
		}
		
		$provider_name = $shippment_provider[0]->provider_name;			
		$custom_provider_name = $shippment_provider[0]->custom_provider_name;
		$api_provider_name = $shippment_provider[0]->api_provider_name;	

		$default_provider = 0;
		if ( $id == get_option( 'wc_ast_default_provider', '' ) ) {
			$default_provider = 1;	
		}
		
		echo json_encode( array('id' => $shippment_provider[0]->id,'provider_name' => $provider_name,'custom_provider_name' => $custom_provider_name,'api_provider_name' => $api_provider_name,'provider_url' => $shippment_provider[0]->provider_url,'shipping_country' => $shippment_provider[0]->shipping_country,'custom_thumb_id' => $shippment_provider[0]->custom_thumb_id,'image' => $image, 'default_provider' => $default_provider ) );
		exit;			
	}
	
	/**
	* Update custom shipping provider and returen html of it
	*/
	public function update_custom_shipment_provider_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_edit_shipping_provider', 'nonce_edit_shipping_provider' );
		
		global $wpdb;		
		
		$provider_id = isset( $_POST['provider_id'] ) ? wc_clean( $_POST['provider_id'] ) : '';
		$tracking_url = isset( $_POST['tracking_url'] ) ? wc_clean( $_POST['tracking_url'] ) : '';
		$thumb_id = isset( $_POST['thumb_id'] ) ? wc_clean( $_POST['thumb_id'] ) : '';
		$shipping_provider = isset( $_POST['shipping_provider'] ) ? wc_clean( $_POST['shipping_provider'] ) : '';
		$shipping_display_name = isset( $_POST['shipping_display_name'] ) ? wc_clean( $_POST['shipping_display_name'] ) : '';
		$shipping_country = isset( $_POST['shipping_country'] ) ? wc_clean( $_POST['shipping_country'] ) : '';
		$api_provider_name = isset( $_POST['api_provider_name'] ) ? wc_clean( $_POST['api_provider_name'] ) : '';
		$provider_type = isset( $_POST['provider_type'] ) ? wc_clean( $_POST['provider_type'] ) : '';
		$make_provider_default = isset( $_POST['make_provider_default'] ) ? wc_clean( $_POST['make_provider_default'] ) : '';
		$wc_ast_default_provider = get_option( 'wc_ast_default_provider', '' );
		
		if ( 1 == $make_provider_default ) {
			update_option( 'wc_ast_default_provider', $provider_id );
		} elseif ( $wc_ast_default_provider == $provider_id ){
			update_option( 'wc_ast_default_provider', '' );
		}
		
		if ( [] == array_filter( $api_provider_name ) ) {
			$api_provider_name = null;			
		} else {
			$api_provider_name = wc_clean( json_encode( $api_provider_name ) );
		}	
				
		if ( 'default_provider' == $provider_type ) {
			$data_array = array(				
				'custom_provider_name' => $shipping_display_name,
				'api_provider_name' => $api_provider_name,				
				'custom_thumb_id' => $thumb_id,				
			);				
		} else {
			$data_array = array(
				'shipping_country' => $shipping_country,
				'provider_name' => $shipping_provider,
				'custom_provider_name' => $shipping_display_name,
				'ts_slug' => $shipping_provider,
				'custom_thumb_id' => $thumb_id,
				'provider_url' => $tracking_url		
			);	
		}
		
		$where_array = array(
			'id' => $provider_id,			
		);
		$wpdb->update( $this->table, $data_array, $where_array );
		$status = 'active';
		$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );	
		$html = $this->get_provider_html( $default_shippment_providers, $status );		
		exit;
	}

	/**
	* Reset default provider
	*/
	public function reset_default_provider_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );
		
		update_option( 'wc_ast_default_provider', '' );
		
		global $wpdb;		
				
		$data_array = array(				
			'custom_provider_name' => null,				
			'custom_thumb_id' => null,
			'api_provider_name' => null,			
		);
		
		$provider_id = isset( $_POST['provider_id'] ) ? wc_clean( $_POST['provider_id'] ) : '';
		
		$where_array = array(
			'id' => $provider_id,			
		);
		$wpdb->update( $this->table, $data_array, $where_array );
		$status = 'active';
		$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );	
		$html = $this->get_provider_html( $default_shippment_providers, $status );		
		exit;
	}	
	
	/**
	* Update bulk status of providers to active
	*/
	public function update_provider_status_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );
		
		global $wpdb;
		
		$status = isset( $_POST['status'] ) ? wc_clean( $_POST['status'] ) : '';
		
		$data_array = array(
			'display_in_order' => $status,
		);
		
		$display_in_order = ( 1 == $status ) ? 0 : 1;
		
		$where_array = array(
			'display_in_order' => $display_in_order,			
		);
		
		$wpdb->update( $this->table, $data_array, $where_array );
		$status = 'all';
		$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );	
		$html = $this->get_provider_html( $default_shippment_providers, $status );
		exit;
	}	
	
	/*
	* get tracking provider slug (ts_slug) from database
	* 
	* return provider slug
	*/
	public function get_provider_slug_from_name( $tracking_provider_name ) {
		
		global $wpdb;
		
		$tracking_provider = $wpdb->get_var( $wpdb->prepare( "SELECT ts_slug FROM $this->table WHERE api_provider_name = '%s'", $tracking_provider_name ) );		
		
		if ( !$tracking_provider ) {			
			$query = "SELECT ts_slug FROM $this->table WHERE JSON_CONTAINS(api_provider_name, '[" . '"' . $tracking_provider_name . '"' . "]')";
			$tracking_provider = $wpdb->get_var( $query );			
		}		
		
		if ( !$tracking_provider ) {
			$tracking_provider = $wpdb->get_var( $wpdb->prepare( "SELECT ts_slug FROM $this->table WHERE provider_name = %s", $tracking_provider_name ) );
		}		
		
		if ( !$tracking_provider ) {
			$tracking_provider =  $tracking_provider_name ;
		}
		
		return $tracking_provider;
	}
	
	/*
	* function for add more provider btn
	*/
	public function add_more_api_provider() { 
		$tooltip_text = class_exists( 'ast_pro' ) ? __( 'Add API Name alias', 'ast-pro' ) : __( 'Multiple API names mapping is a pro features', 'ast-pro' ) ;
		?>
		<span class="dashicons dashicons-insert woocommerce-help-tip tipTip add_more_api_provider" data-tip="<?php esc_html_e( $tooltip_text ); ?>"></span>	
		<?php 
	}
	
	/**
	* Synch provider function 
	*/
	public function sync_providers_fun() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'nonce_shipping_provider', 'security' );
				
		$reset_checked = isset( $_POST[ 'reset_checked' ] ) ? wc_clean( $_POST[ 'reset_checked' ] ) : '' ;
		global $wpdb;		
		
		$url =	apply_filters( 'ast_sync_provider_url', 'https://trackship.info/wp-json/WCAST/v1/Provider' );
		$resp = wp_remote_get( $url );

		$upload_dir   = wp_upload_dir();	
		$ast_directory = $upload_dir['basedir'] . '/ast-shipping-providers';		
		
		if ( !is_dir( $ast_directory ) ) {
			wp_mkdir_p( $ast_directory );	
		}
		
		if ( is_array( $resp ) && ! is_wp_error( $resp ) ) {
			$providers = json_decode( $resp['body'], true );
			
			if ( 1 == $reset_checked ) {
				
				$wpdb->query( "DROP TABLE IF EXISTS {$this->table}" );
				
				$install = AST_PRO_Install::get_instance();
				$install->create_shippment_tracking_table();
				
				foreach ( $providers as $provider ) {
					$provider_name = $provider['shipping_provider'];
					$provider_url = $provider['provider_url'];
					$shipping_country = $provider['shipping_country'];
					$ts_slug = $provider['shipping_provider_slug'];	
					$img_url = $provider['img_url'];			
					$trackship_supported = $provider['trackship_supported'];							
					$img_slug = sanitize_title( $provider_name );
									
					$img = $ast_directory . '/' . $img_slug . '.png';
					
					$ch = curl_init(); 
			
					curl_setopt( $ch, CURLOPT_HEADER, 0 ); 
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
					curl_setopt( $ch, CURLOPT_URL, $img_url ); 
					
					$data = curl_exec( $ch );
					curl_close( $ch );
					
					file_put_contents( $img, $data );
								
					$data_array = array(
						'shipping_country' => sanitize_text_field( $shipping_country ),
						'provider_name' => sanitize_text_field( $provider_name ),
						'ts_slug' => $ts_slug,
						'provider_url' => sanitize_text_field( $provider_url ),			
						'display_in_order' => 1,
						'shipping_default' => 1,
						'trackship_supported' => sanitize_text_field( $trackship_supported ),
					);
					
					$data_array = apply_filters( 'ast_sync_provider_data_array', $data_array, $provider );
					
					$result = $wpdb->insert( $this->table, $data_array );
				}
				
				$status = 'active';
				$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );	
				ob_start();
				$html = $this->get_provider_html( $default_shippment_providers, $status );
				$html = ob_get_clean();	
				
				echo json_encode( array( 'html' => $html ) );
				exit;
			} else {
			
				$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );
				
				foreach ( $default_shippment_providers as $key => $val ) {
					$shippment_providers[ $val->provider_name ] = $val;						
				}
		
				foreach ( $providers as $key => $val ) {
					$providers_name[ $val['provider_name'] ] = $val;						
				}		
					
				$added = 0;
				$updated = 0;
				$deleted = 0;
				$added_html = '';
				$updated_html = '';
				$deleted_html = '';
				
				foreach ( $providers as $provider ) {
					
					$provider_name = $provider['shipping_provider'];
					$provider_url = $provider['provider_url'];
					$shipping_country = $provider['shipping_country'];
					$ts_slug = $provider['shipping_provider_slug'];
					$trackship_supported = $provider['trackship_supported'];
					
					if ( isset( $shippment_providers[ $provider_name ] ) ) {				
						$db_provider_url = $shippment_providers[ $provider_name ]->provider_url;
						$db_shipping_country = $shippment_providers[ $provider_name ]->shipping_country;
						$db_ts_slug = $shippment_providers[ $provider_name ]->ts_slug;
						$db_trackship_supported = $shippment_providers[ $provider_name ]->trackship_supported;
						
						$update_needed = apply_filters( 'ast_sync_provider_update', false, $provider, $shippment_providers );
						
						if ( $db_provider_url != $provider_url ) {
							$update_needed = true;
						} elseif ( $db_shipping_country != $shipping_country ) {
							$update_needed = true;
						} elseif ( $db_ts_slug != $ts_slug ) {
							$update_needed = true;
						} elseif ( $db_trackship_supported != $trackship_supported ) {
							$update_needed = true;
						}
						
						if ( $update_needed ) {
							
							$data_array = array(
								'ts_slug' => $ts_slug,
								'provider_url' => $provider_url,
								'shipping_country' => $shipping_country,
								'trackship_supported' => $trackship_supported,								
							);
							
							$data_array = apply_filters( 'ast_sync_provider_data_array', $data_array, $provider );
							
							$where_array = array(
								'provider_name' => $provider_name,			
							);					
							$wpdb->update( $this->table, $data_array, $where_array );
							$updated_data[ $updated ] = array( 'provider_name' => $provider_name );
							$updated++;
						}
					} else {
						$img_url = $provider['img_url'];					
						$img_slug = sanitize_title( $provider_name );
						$img = $ast_directory . '/' . $img_slug . '.png';
						
						$ch = curl_init(); 
		
						curl_setopt( $ch, CURLOPT_HEADER, 0 ); 
						curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
						curl_setopt( $ch, CURLOPT_URL, $img_url );
					
						$data = curl_exec( $ch ); 
						curl_close( $ch );
						
						file_put_contents( $img, $data );
																		
						$data_array = array(
							'shipping_country' => sanitize_text_field( $shipping_country ),
							'provider_name' => sanitize_text_field( $provider_name ),
							'ts_slug' => $ts_slug,
							'provider_url' => sanitize_text_field( $provider_url ),
							'display_in_order' => 0,
							'shipping_default' => 1,
							'trackship_supported' => sanitize_text_field( $trackship_supported ),
						);
						
						$data_array = apply_filters( 'ast_sync_provider_data_array', $data_array, $provider );
						
						$result = $wpdb->insert( $this->table, $data_array );
						$added_data[ $added ] = array( 'provider_name' => $provider_name );
						$added++;
					}		
				}
				
				foreach ( $default_shippment_providers as $db_provider ) {
					if ( !isset( $providers_name[ $db_provider->provider_name ] ) ) {			
						$where = array(
							'provider_name' => $db_provider->provider_name,
							'shipping_default' => 1
						);
						$wpdb->delete( $this->table, $where );
						$deleted_data[ $deleted ] = array( 'provider_name' => $db_provider->provider_name );
						$deleted++;		
					}
				}

				if ( $added > 0 ) {
					ob_start();
					$added_html = $this->added_html( $added_data );
					$added_html = ob_get_clean();	
				}
				
				if ( $updated > 0 ) {
					ob_start();
					$updated_html = $this->updated_html( $updated_data );
					$updated_html = ob_get_clean();	
				}
				
				if ( $deleted > 0 ) {
					ob_start();
					$deleted_html = $this->deleted_html( $deleted_data );
					$deleted_html = ob_get_clean();	
				}
				
				$status = 'active';
				$default_shippment_providers = $wpdb->get_results( "SELECT * FROM $this->table ORDER BY shipping_default ASC, display_in_order DESC, trackship_supported DESC, id ASC" );	
				ob_start();
				$html = $this->get_provider_html( $default_shippment_providers, $status );
				$html = ob_get_clean();										
				
				echo json_encode( array( 'added' => $added, 'added_html' => $added_html, 'updated' => $updated, 'updated_html' => $updated_html, 'deleted' => $deleted, 'deleted_html' => $deleted_html,'html' => $html ) );
				exit;
			}
		} else {
			echo json_encode( array( 'sync_error' => 1, 'message' => __( 'There are some issue with sync, Please Retry.', 'ast-pro') ) );
			exit;
		}	
	}
	
	/**
	* Output html of added provider from sync providers
	*/
	public function added_html( $added_data ) { 
		?>
		<ul class="updated_details" id="added_providers">
			<?php 
			foreach ( $added_data as $added ) { 
				?>
				<li><?php esc_html_e( $added['provider_name'] ); ?></li>	
			<?php } ?>
		</ul>
		<a class="view_synch_details" id="view_added_details" href="javaScript:void(0);" style="display: block;"><?php esc_html_e( 'view details', 'ast-pro' ); ?></a>
		<a class="view_synch_details" id="hide_added_details" href="javaScript:void(0);" style="display: none;"><?php esc_html_e( 'hide details', 'ast-pro' ); ?></a>
	<?php 
	}

	/**
	* Output html of updated provider from sync providers
	*/
	public function updated_html( $updated_data ) { 
		?>
		<ul class="updated_details" id="updated_providers">
			<?php 
			foreach ( $updated_data as $updated ) { 
				?>
				<li><?php esc_html_e( $updated['provider_name'] ); ?></li>	
			<?php } ?>
		</ul>
		<a class="view_synch_details" id="view_updated_details" href="javaScript:void(0);" style="display: block;"><?php esc_html_e( 'view details', 'ast-pro' ); ?></a>
		<a class="view_synch_details" id="hide_updated_details" href="javaScript:void(0);" style="display: none;"><?php esc_html_e( 'hide details', 'ast-pro' ); ?></a>
	<?php 
	}
	
	/**
	* Output html of deleted provider from sync providers
	*/
	public function deleted_html( $deleted_data ) { 
		?>
		<ul class="updated_details" id="deleted_providers">
			<?php 
			foreach ( $deleted_data as $deleted ) { 
				?>
				<li><?php esc_html_e( $deleted['provider_name'] ); ?></li>	
			<?php } ?>
		</ul>
		<a class="view_synch_details" id="view_deleted_details" href="javaScript:void(0);" style="display: block;"><?php esc_html_e( 'view details', 'ast-pro'); ?></a>
		<a class="view_synch_details" id="hide_deleted_details" href="javaScript:void(0);" style="display: none;"><?php esc_html_e( 'hide details', 'ast-pro'); ?></a>
	<?php 
	}	
	
	/*
	* Function for autocompleted order after adding all product through TPI 
	*/
	public function autocomplete_order_after_adding_all_products( $order_id, $status_shipped, $products_list ) {
	
		$order = wc_get_order( $order_id );
		$items = $order->get_items();
		$items_count = count( $items );
		
		$added_products = $this->get_all_added_product_list_with_qty( $order_id );
		
		$new_products = array();
			
		foreach ( $products_list as $in_list ) {
			
			if ( isset( $new_products[ $in_list->product ] ) ) {
				$new_products[ $in_list->product ] = (int) $new_products[ $in_list->product ] + (int) $in_list->qty;		
			} else {
				$new_products[ $in_list->product ] = $in_list->qty;	
			}			
		}
		
		$total_products_data = array();
	
		foreach ( array_keys( $new_products + $added_products ) as $products ) {
			$total_products_data[ $products ] = ( isset( $new_products[ $products ] ) ? $new_products[ $products ] : 0 ) + ( isset( $added_products[ $products ] ) ? $added_products[ $products ] : 0 );
		}			
		
		$orders_products_data = array();
		foreach ( $items as $item ) {																
			$checked = 0;
			$qty = $item->get_quantity();
			
			if ( 1 == $items_count && 1 == $qty ) {
				return $status_shipped;
			}	
			
			$variation_id = $item->get_variation_id();
			$product_id = $item->get_product_id();					
			
			if ( 0 != $variation_id ) {
				$product_id = $variation_id;
			}
			
			$orders_products_data[ $product_id ] = $qty;
		}				
		
		$change_status = 0;
		$autocomplete_order = true;				
		
		foreach ( $orders_products_data as $product_id => $qty ) {		
			if (isset( $total_products_data[ $product_id ] ) ) {
				if ( $qty > $total_products_data[ $product_id ] ) {
					$autocomplete_order = false;
					$change_status = 1;
				} else {
					$change_status = 1;
				}
			} else {
				$autocomplete_order = false;
			}
		}
		
		if ( $autocomplete_order && 1 == $change_status ) {
			$status_shipped = 1;
		}
		return $status_shipped;
	}
	
		/*
	* Function for autocompleted order after adding all product through TPI 
	*/
	public function autodetect_order_status_after_add_tracking( $order_id, $status_shipped, $products_list ) {
	
		$order = wc_get_order( $order_id );
		$items = $order->get_items();
		$items_count = count( $items );
		
		$added_products = $this->get_all_added_product_list_with_qty( $order_id );		
				
		$total_products_data = array();
		
		foreach ( array_keys( $added_products ) as $products ) {
			$total_products_data[ $products ] = ( isset( $added_products[ $products ] ) ? $added_products[ $products ] : 0 );
		}			
		
		$orders_products_data = array();
		foreach ( $items as $item ) {																
			$checked = 0;
			$qty = $item->get_quantity();
			
			if ( 1 == $items_count && 1 == $qty ) {
				return $status_shipped;
			}	
			
			$variation_id = $item->get_variation_id();
			$product_id = $item->get_product_id();					
			
			if ( 0 != $variation_id ) {
				$product_id = $variation_id;
			}
			
			$orders_products_data[ $product_id ] = $qty;
		}				
		
		$change_status = 0;
		$autocomplete_order = true;				
		
		foreach ( $orders_products_data as $product_id => $qty ) {		
			if (isset( $total_products_data[ $product_id ] ) ) {
				if ( $qty > $total_products_data[ $product_id ] ) {
					$autocomplete_order = false;
					$change_status = 1;
				} else {
					$change_status = 1;
				}
			} else {
				$autocomplete_order = false;
			}
		}
		
		if ( $autocomplete_order && 1 == $change_status ) {
			$status_shipped = 1;
		}
		return $status_shipped;
	}
	
	/*
	* Function for get already added product in TPI
	*/
	public function get_all_added_product_list_with_qty( $order_id ) {
		
		$ast = AST_Pro_Actions::get_instance();
		$tracking_items = $ast->get_tracking_items( $order_id, true );
		
		$product_list = array();			
		
		foreach ( $tracking_items as $tracking_item ) {			
			if ( isset( $tracking_item[ 'products_list' ] ) ) {
				$product_list[] = $tracking_item[ 'products_list' ];				
			}
		}
		
		$all_list = array();
		foreach ( $product_list as $list ) {			
			foreach ( $list as $in_list ) {
				if ( isset( $all_list[ $in_list->product ] ) ) {
					$all_list[ $in_list->product ] = (int) $all_list[ $in_list->product ] + (int) $in_list->qty;							
				} else {
					$all_list[ $in_list->product ] = $in_list->qty;	
				}
			}				
		}
		
		return $all_list;
	}		
	
	/**
	* Check if the value is a valid date
	*
	* @param mixed $value
	*
	* @return boolean
	*/
	public function isDate( $date, $format = 'd-m-Y' ) {
		if ( !$date ) {
			return false;
		}
			
		$d = DateTime::createFromFormat( $format, $date );
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format( $format ) === $date;
	}	
}
