<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ast_Dokan {
	
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
	 * @return AST_PRO_Vendor_Compatibility
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
		add_action( 'dokan_order_detail_after_order_items', array( $this, 'order_details_tracking_meta_box' ), 10, 1 );
		add_action( 'dokan_enqueue_scripts', array( $this, 'dokan_enqueue_scripts' ), 10 );
		add_action( 'wp_ajax_ast_add_shipping_tracking_info', array( $this, 'add_shipping_tracking_info' ) );
		add_action( 'wp_ajax_ast_dokan_delete_item', array( $this, 'ast_dokan_delete_item' ) );
	}

	public function order_details_tracking_meta_box( $order  ) {
		global $wpdb;
		$WC_Countries = new WC_Countries();
		$ast = AST_Pro_Actions::get_instance();
		$countries = $WC_Countries->get_countries();				
		$order_id = $order->get_id();
		$shippment_countries = $wpdb->get_results( $wpdb->prepare( 'SELECT shipping_country FROM %1s WHERE display_in_order = 1 GROUP BY shipping_country', $this->table ) );		
		$shippment_providers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s', $this->table ) );
		$default_provider = get_option( 'wc_ast_default_provider' );
		
		$tracking_items = $ast->get_tracking_items( $order_id, true );
		
		$products_list = array();
		if ( count( $tracking_items ) > 0 ) {
			foreach ( $tracking_items as $tracking_item ) {				
				array_push( $products_list, $tracking_item['products_list'] );
			}
		}	
		
		$status_shipped = ast_pro()->ast_pro_admin->autocomplete_order_after_adding_all_products( $order_id, 2, $products_list );
		
		?>
		<div class="dokan-left ast-dokan-panel" style="width:100%">
			<div class="dokan-panel dokan-panel-default">
				<div class="dokan-panel-heading"><strong><?php esc_html_e( 'Shipment Tracking', 'ast-pro' ); ?></strong></div>
				<div class="dokan-panel-body">
					<div id="ast-dokan-tracking-items">
						<?php
						
						if ( count( $tracking_items ) > 0 ) {
							foreach ( $tracking_items as $tracking_item ) {				
								$this->display_html_tracking_item_for_meta_box( $order_id, $tracking_item );
							}
						}
						?>
					</div>
					<div class="clearfix dokan-form-group" style="margin-top: 10px;">
						
						<?php if ( 1 != $status_shipped ) { ?>
							<!-- Trigger the modal with a button -->
							<input type="button" id="ast-show-tracking-form" class="ast-btn" value="<?php esc_html_e( 'Add Tracking Info', 'ast-pro' ); ?>">
						<?php } ?>						

						<form id="ast-add-shipping-tracking-form" method="post" class="dokan-hide" style="margin-top: 10px;">
							
							<div class="dokan-form-group">
								<label class="dokan-control-label"><?php esc_html_e( 'Tracking number:', 'ast-pro' ); ?></label>
								<input type="text" name="tracking_number" id="tracking_number" class="dokan-form-control" value="">
							</div>

							<div class="dokan-form-group">
								<label class="dokan-control-label"><?php esc_html_e( 'Shipping Provider:', 'ast-pro' ); ?></label>                                
								<select id="tracking_provider" name="tracking_provider" class="dokan-form-control tracking_provider_dropdown" style="width:100%;">
									<option value=""><?php esc_html_e( 'Select Provider', 'ast-pro' ); ?></option>
									<?php
									foreach ( $shippment_countries as $s_c ) {
										if ( 'Global' != $s_c->shipping_country ) {
											$country_name = esc_attr( $WC_Countries->countries[$s_c->shipping_country] );
										} else {
											$country_name = 'Global';
										}
										
										echo '<optgroup label="' . esc_html( $country_name ) . '">';
										$country = $s_c->shipping_country;				
										$shippment_providers_by_country = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE shipping_country = %s AND display_in_order = 1', $this->table, $country ) );
										foreach ( $shippment_providers_by_country as $providers ) {
											$selected = ( esc_attr( $providers->id ) == $default_provider ) ? 'selected' : '';
											echo '<option value="' . esc_attr( $providers->ts_slug ) . '" ' . esc_html( $selected ) . '>' . esc_html( $providers->provider_name ) . '</option>';
										}
										echo '</optgroup>';	
									}
									?>
								</select>
							</div>

							<div class="dokan-form-group">
								<label class="dokan-control-label"><?php esc_html_e( 'Date shipped:', 'ast-pro' ); ?></label>
								<input type="text" name="legacy_date_shipped" id="legacy_date_shipped" class="dokan-form-control" value="<?php echo esc_html( date_i18n( __( 'Y-m-d', 'ast-pro' ), current_time( 'timestamp' ) ) ); ?>">
							</div>
							<?php
							do_action( 'ast_after_tracking_field', $order_id );	
							do_action( 'ast_tracking_form_between_form', $order_id, 'single_order' );
							?>
							<div class="dokan-form-group">
								<?php //$ast->mark_order_as_fields_html(); ?>
							</div>							
							<input type="hidden" name="security" id="security" value="<?php echo esc_attr( wp_create_nonce( 'ast-add-shipping-tracking-info' ) ); ?>">
							<input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr( $order->get_id() ); ?>">
							<input type="hidden" name="action" id="action" value="ast_add_shipping_tracking_info">

							<div class="dokan-form-group">
								<input id="ast-add-tracking-details" type="button" class="ast-btn" value="<?php esc_attr_e( 'Fulfill Items', 'ast-pro' ); ?>">
								<button type="button" class="ast-btn" id="ast-dokan-cancel-tracking-note"><?php esc_html_e( 'Close', 'dokan-lite' ); ?></button>
							</div>
						</form>
					</div>                    
				</div>
			</div>
		</div>
		<?php
	}

	public function display_html_tracking_item_for_meta_box( $order_id, $item ) {
		
		global $wpdb;
		$tracking_provider = isset( $item['tracking_provider'] ) ? $item['tracking_provider'] : $item['custom_tracking_provider'];
		$tracking_provider = apply_filters( 'convert_provider_name_to_slug', $tracking_provider );
		$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE ts_slug = %s', $this->table, $tracking_provider ) );		
		$provider_name = apply_filters( 'get_ast_provider_name', $tracking_provider, $results );

		?>
		<div class="tracking-item" id="tracking-item-<?php echo esc_attr( $item['tracking_id'] ); ?>">
			<div class="tracking-content">
				<div class="tracking-content-div">
					<strong><?php echo esc_html( $provider_name ); ?></strong>						
						<?php 
						if ( strlen( $item['ast_tracking_link'] ) > 0 ) { 
							?>
						- 
						<?php 							
							echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Track Shipment', 'ast-pro' ) ) . '">' . esc_html( $item['tracking_number'] ) . '</a>', esc_url( $item['ast_tracking_link'] ) ); 
						} else { 
							?>
						<span> - <?php esc_html_e( $item['tracking_number'] ); ?></span>
					<?php } ?>
				</div>					
				<?php 
				do_action( 'ast_after_tracking_number', $order_id, $item['tracking_id'] );
				do_action( 'ast_shipment_tracking_end', $order_id, $item );
				?>
			</div>
			<p class="meta">
				<?php /* translators: 1: shipping date */ ?>
				<?php echo esc_html( sprintf( __( 'Shipped on %1s', 'ast-pro' ), date_i18n( get_option( 'date_format' ), $item['date_shipped'] ) ) ); ?>
				<a href="#" class="ast-dokan-delete-tracking" rel="<?php echo esc_attr( $item['tracking_id'] ); ?>"><?php esc_html_e( 'Delete', 'woocommerce' ); ?></a>
				<input type="hidden" name="ast_dona_delete_tracking_nonce" id="ast_dona_delete_tracking_nonce" value="<?php echo esc_attr( wp_create_nonce( 'ast-dokan-delete-tracking-item' ) ); ?>">                   
			</p>
		</div>
		<?php
	}	

	public function dokan_enqueue_scripts() {
		if ( DOKAN_LOAD_SCRIPTS ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'ast_pro_dokan_script', ast_pro()->plugin_dir_url() . 'assets/js/ast_dokan.js' , array( 'jquery', 'wp-util' ), ast_pro()->version, true );
			wp_enqueue_style( 'ast_pro_dokan_style', ast_pro()->plugin_dir_url() . 'assets/css/ast_dokan.css', array(), ast_pro()->version );
			wp_enqueue_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );			
		}
	}

	 /**
	 * Add shipping tracking info via ajax
	 */
	public function add_shipping_tracking_info() {
		if ( ! isset( $_REQUEST['security'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['security'] ), 'ast-add-shipping-tracking-info' ) ) {
			die( - 1 );
		}

		if ( ! is_user_logged_in() ) {
			die( - 1 );
		}

		if ( ! current_user_can( 'dokan_manage_order_note' ) ) {
			die( - 1 );
		}

		$tracking_provider = isset( $_POST['tracking_provider'] ) ? wc_clean( $_POST['tracking_provider'] ) : '';
		$tracking_number = isset( $_POST['tracking_number'] ) ? wc_clean( $_POST['tracking_number'] ) : '';					
		
		if ( strlen( $tracking_number ) > 0 && '' != $tracking_provider ) {	
	
			$order_id = isset( $_POST['order_id'] ) ? wc_clean( $_POST['order_id'] ) : '';
				
			$date_shipped = isset( $_POST['legacy_date_shipped'] ) ? wc_clean( $_POST['legacy_date_shipped'] ) : '';
			$ASTProduct = isset( $_POST['ASTProduct'] ) ? wc_clean( $_POST['ASTProduct'] ) : array();
			
			$products_list = array();

			foreach ( $ASTProduct as $key => $product_array ) {
				$product_data =  (object) array (
					'product' => $product_array['product'],
					'qty' => $product_array['qty'],
				);	
				array_push( $products_list, $product_data );
			}

			$status_shipped = ast_pro()->ast_pro_admin->autocomplete_order_after_adding_all_products( $order_id, 2, $products_list );			
			
			$args = array(
				'tracking_provider'        => $tracking_provider,
				'tracking_number'          => $tracking_number,
				'date_shipped'             => $date_shipped,
				'status_shipped'		   => $status_shipped,	
			);
			
			$args = apply_filters( 'tracking_info_args', $args, $_POST, $order_id );
			$ast = AST_Pro_Actions::get_instance(); 
			$tracking_item = $ast->add_tracking_item( $order_id, $args );	
			
			do_action( 'ast_save_tracking_details_end', $order_id, $_POST );							
		}

		die();
	}
	
	public function ast_dokan_delete_item() {				
		
		if ( ! isset( $_REQUEST['security'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['security'] ), 'ast-dokan-delete-tracking-item' ) ) {
			die( - 1 );
		}

		if ( ! is_user_logged_in() ) {
			die( - 1 );
		}

		if ( ! current_user_can( 'dokan_manage_order_note' ) ) {
			die( - 1 );
		}

		$ast = AST_Pro_Actions::get_instance();
		$order_id = isset( $_POST['order_id'] ) ? wc_clean( $_POST['order_id'] ) : '';
		$tracking_id = isset( $_POST['tracking_id'] ) ? wc_clean( $_POST['tracking_id'] ) : '';
		$tracking_items = $ast->get_tracking_items( $order_id, true );
		
		do_action( 'delete_tracking_number_from_trackship', $tracking_items, $tracking_id, $order_id );				
		
		foreach ( $tracking_items as $tracking_item ) {
			if ( $tracking_item['tracking_id'] == $tracking_id ) {				
				$tracking_number = $tracking_item['tracking_number'];
				$tracking_provider = $tracking_item['formatted_tracking_provider'];
				$order = wc_get_order(  $order_id );
				/* translators: %1$s: replace with Shipping Provider %2$s: replace with tracking number */
				$note = sprintf( __( 'Tracking info was deleted for tracking provider %1$s with tracking number %2$s', 'ast-pro' ), $tracking_provider, $tracking_number );
				
				// Add the note
				$order->add_order_note( $note );
			}
		}
		
		$ast->delete_tracking_item( $order_id, $tracking_id );
		
		die();
	}
}
