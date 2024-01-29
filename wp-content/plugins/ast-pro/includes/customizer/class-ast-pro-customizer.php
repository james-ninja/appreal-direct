<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_PRO_Customizer {

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
	}
	
	public function allowed_css_tags( $tags ) {
		$tags['style'] = array( 'type' => true, );
		return $tags;
	}
	
	public function safe_style_css( $styles ) {
		 $styles[] = 'display';
		return $styles;
	}
	
	/**
	 * Register the Customizer sections
	 */
	public function wcast_add_customizer_sections( $wp_customize ) {	
		
		$wp_customize->add_section( 'ast_tracking_general_section',
			array(
				'title' => __( 'Tracking Info Widget', 'ast-pro' ),
				'description' => '',
				'priority' => 1,
			)
		);				
		
		$rename_shipped_status = get_option( 'wc_ast_status_shipped', 1 );
		$custom_shipped_status = get_option( 'wc_ast_status_new_shipped', 0 );
		$wc_ast_status_partial_shipped = get_option( 'wc_ast_status_partial_shipped', 1 );
		
		if ( $rename_shipped_status ) {
			$label = __( 'Shipped Order Email(Completed)', 'ast-pro' );
		} else {
			$label = __( 'Completed Order Email', 'ast-pro' );
		}
		
		$wp_customize->add_panel( 'ast_pro_order_emails', array(
			'title' => __( 'Order Status Email Notifications', 'ast-pro' ),
			'description' => '',
			'priority' => 2,
		) );				
		
		$wp_customize->add_section( 'customer_completed_email',
			array(
				'title' => $label,
				'description' => '',
				'panel' => 'ast_pro_order_emails',
			)
		);
		
		if ( $wc_ast_status_partial_shipped ) {
			$wp_customize->add_section( 'custom_partially_shipped_email',
				array(
					'title' => __( 'Partially Shipped Order Email', 'ast-pro' ),
					'description' => '',
					'panel' => 'ast_pro_order_emails',	
				)
			);
		}
		
		if ( $custom_shipped_status ) {
			$wp_customize->add_section( 'custom_shipped_email',
				array(
					'title' => __( 'Shipped Order Email', 'ast-pro' ),
					'description' => '',				
					'panel' => 'ast_pro_order_emails',
				)
			);	
		}
	}
	
	/**
	 * Add css and js for preview
	*/
	public function enqueue_preview_scripts() {		 
		
		wp_enqueue_script('wcast-email-preview-scripts', ast_pro()->plugin_dir_url() . 'assets/js/preview-scripts.js', array('jquery', 'customize-preview'), ast_pro()->version, true);
		wp_enqueue_style('wcast-preview-styles', ast_pro()->plugin_dir_url() . 'assets/css/preview-styles.css', array(), ast_pro()->version  );
		wp_localize_script('wcast-email-preview-scripts', 'wcast_preview', array(
			'site_title'   => $this->get_blogname(),
			'order_number' => get_theme_mod('wcast_email_preview_order_id'),			
		));
	}
	
	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
	
	/**
	 * Add css and js for customizer
	*/
	public function enqueue_customizer_scripts() {
		
		if ( isset( $_REQUEST['wcast-customizer'] ) && '1' === $_REQUEST['wcast-customizer'] ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style('wcast-customizer-styles', ast_pro()->plugin_dir_url() . 'assets/css/customizer-styles.css', array(), ast_pro()->version  );			
			wp_enqueue_script('wcast-customizer-scripts', ast_pro()->plugin_dir_url() . 'assets/js/customizer-scripts.js', array('jquery', 'customize-controls','wp-color-picker'), ast_pro()->version, true);
			
			$email_type = ( isset($_REQUEST['order_status']) )  ? wc_clean( $_REQUEST['order_status'] ) : 'partially_shipped';
			$email = ( isset($_REQUEST['email']) )  ? wc_clean( $_REQUEST['email'] ) : '';			
		
			// Send variables to Javascript
			wp_localize_script('wcast-customizer-scripts', 'wcast_customizer', array(
				'customizer_nonce' 						  => wp_create_nonce( 'ast_customizer' ),
				'ajax_url'                                => admin_url('admin-ajax.php'),
				'tracking_preview_url'        			  => $this->get_tracking_preview_url(),
				'email_preview_url'        				  => $this->get_email_preview_url(),
				'partial_shipped_email_preview_url'       => $this->get_partial_shipped_email_preview_url(),
				'shipped_email_preview_url' 			  => $this->get_shipped_email_preview_url(),				
				'completed_email_preview_url' 	  		  => $this->completed_email_preview_url(),
				'email_type' 							  => $email_type,					
				'trigger_click'        					  => '#accordion-section-' . $email . ' h3',	
				'customizer_title'        				  => 'Advanced Shipment Tracking (AST)',	
				'responsive_mode'       				  => 1,
			));	

			wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
				'clear'            => __( 'Clear' ),
				'clearAriaLabel'   => __( 'Clear color' ),
				'defaultString'    => __( 'Default' ),
				'defaultAriaLabel' => __( 'Select default color' ),
				'pick'             => __( 'Select Color' ),
				'defaultLabel'     => __( 'Color value' ),
			));	
		}
	}
	
	/**
	 * Get Customizer URL
	 *
	 */
	public function get_tracking_preview_url() {		
		return add_query_arg( array(
			'wcast-tracking-preview' => '1',
		), home_url( '' ) );
	}
	
	/**
	 * Get Customizer URL
	 *
	 */
	public function get_email_preview_url() {		
		return add_query_arg( array(
			'wcast-email-customizer-preview' => '1',
		), home_url( '' ) );
	}
	
	/**
	 * Get Customizer URL
	 *
	 */
	public function get_partial_shipped_email_preview_url() {		
		return add_query_arg( array(
			'wcast-partial-shipped-email-customizer-preview' => '1',
		), home_url( '' ) );
	}
	
	/**
	 * Get Customizer URL
	 *
	 */
	public function get_shipped_email_preview_url() {
		return add_query_arg( array(
			'wcast-shipped-email-customizer-preview' => '1',
		), home_url( '' ) );
	}		
	
	/**
	 * Get Customizer URL
	 *
	 */
	public function completed_email_preview_url() {		
		return add_query_arg( array(
			'wcast-completed-email-customizer-preview' => '1',
		), home_url( '' ) );				 
	}
	
	/**
	* Remove unrelated components
	*	
	* @param array $components
	* @param object $wp_customize
	* @return array
	*/
	public function remove_unrelated_components( $components, $wp_customize ) {
		// Iterate over components
		foreach ($components as $component_key => $component) {			
			// Check if current component is own component
			if ( ! $this->is_own_component( $component ) ) {
				unset($components[$component_key]);
			}
		}
				
		// Return remaining components
		return $components;
	}

	/**
	* Remove unrelated sections
	*	
	* @param bool $active
	* @param object $section
	* @return bool
	*/
	public function remove_unrelated_sections( $active, $section ) {
		// Check if current section is own section
		if ( ! $this->is_own_section( $section->id ) ) {
			return false;
		}
		
		// We can override $active completely since this runs only on own Customizer requests
		return true;
	}

	/**
	* Check if current component is own component
	*
	* @param string $component
	* @return bool
	*/
	public function is_own_component( $component ) {
		return false;
	}

	/**
	* Check if current section is own section
	*	
	* @param string $key
	* @return bool
	*/
	public function is_own_section( $key ) {
				
		if ( 'ast_tracking_general_section' === $key || 'custom_partially_shipped_email' === $key || 'custom_shipped_email' === $key || 'customer_completed_email' === $key ) {
			return true;
		}

		// Section not found
		return false;
	}
	
	/*
	 * Unhook flatsome front end.
	 */
	public function unhook_flatsome() {
		// Unhook flatsome issue.
		wp_dequeue_style( 'flatsome-customizer-preview' );
		wp_dequeue_script( 'flatsome-customizer-frontend-js' );
	}
	
	/*
	 * Unhook Divi front end.
	 */
	public function unhook_divi() {
		// Divi Theme issue.
		remove_action( 'wp_footer', 'et_builder_get_modules_js_data' );
		remove_action( 'et_customizer_footer_preview', 'et_load_social_icons' );
	}
	
	/**
	 * Get Order Ids
	 *	 
	 * @return array
	 */
	public function get_order_ids() {		
		$order_array = array();
		$order_array['mockup'] = __( 'Mockup Order', 'ast-pro' );
		
		$orders = wc_get_orders( array(
			'limit'        => 20,
			'orderby'      => 'date',
			'order'        => 'DESC',
			'meta_key'     => '_wc_shipment_tracking_items', // The postmeta key field
			'meta_compare' => 'EXISTS', // The comparison argument
		));	
			
		foreach ( $orders as $order ) {				
			$ast = new AST_Pro_Actions();
			$tracking_items = $ast->get_tracking_items( $order->get_id(), true );
			if ( $tracking_items ) {
				$order_array[ $order->get_id() ] = $order->get_id() . ' - ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();					
			}				
		}
		return $order_array;
	}

		/**
	 * Get WooCommerce order for preview
	 *	 
	 * @param string $order_status
	 * @return object
	 */
	public function get_wc_order_for_preview( $order_status = null, $order_id = null ) {
		if ( ! empty( $order_id ) && 'mockup' != $order_id ) {
			return wc_get_order( $order_id );
		} else {			

			// Instantiate order object
			$order = new WC_Order();			
			
			// Other order properties
			$order->set_props( array(
				'id'                 => 1,
				'status'             => ( null === $order_status ? 'processing' : $order_status ),
				'shipping_first_name' => 'Sherlock',
				'shipping_last_name'  => 'Holmes',
				'shipping_company'    => 'Detectives Ltd.',
				'shipping_address_1'  => '221B Baker Street',
				'shipping_city'       => 'London',
				'shipping_postcode'   => 'NW1 6XE',
				'shipping_country'    => 'GB',
				'billing_first_name' => 'Sherlock',
				'billing_last_name'  => 'Holmes',
				'billing_company'    => 'Detectives Ltd.',
				'billing_address_1'  => '221B Baker Street',
				'billing_city'       => 'London',
				'billing_postcode'   => 'NW1 6XE',
				'billing_country'    => 'GB',
				'billing_email'      => 'sherlock@holmes.co.uk',
				'billing_phone'      => '02079304832',
				'date_created'       => gmdate( 'Y-m-d H:i:s' ),
				'total'              => 24.90,				
			) );

			// Item #1
			$order_item = new WC_Order_Item_Product();
			$order_item->set_props( array(
				'name'     => 'A Study in Scarlet',
				'subtotal' => '9.95',
				'sku'      => 'kwd_ex_1',
			) );
			$order->add_item( $order_item );

			// Item #2
			$order_item = new WC_Order_Item_Product();
			$order_item->set_props( array(
				'name'     => 'The Hound of the Baskervilles',
				'subtotal' => '14.95',
				'sku'      => 'kwd_ex_2',
			) );
			$order->add_item( $order_item );						

			// Return mockup order
			return $order;
		}
	}	
}
/**
 * Returns an instance of zorem_woocommerce_advanced_shipment_tracking.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return zorem_woocommerce_advanced_shipment_tracking
*/
function ast_pro_customizer() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new AST_PRO_Customizer();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
ast_pro_customizer();
