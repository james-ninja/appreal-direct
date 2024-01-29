<?php
/**
 * Customizer Setup and Custom Controls
 *
 */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class Wcast_Completed_Customizer_Email {
	// Get our default values	
	public function __construct() {
		// Get our Customizer defaults
		$this->defaults = $this->wcast_generate_defaults();
			
		$rename_shipped_status = get_option( 'wc_ast_status_shipped', 1 );
		$custom_shipped_status = get_option( 'wc_ast_status_new_shipped', 0 );
		
		
		// Register our sample default controls
		add_action( 'customize_register', array( $this, 'wcast_register_sample_default_controls' ) );
		
		// Only proceed if this is own request.		
		if ( ! $this->is_own_customizer_request() && ! $this->is_own_preview_request() ) {
			return;
		}	
							
		// Register our sections
		add_action( 'customize_register', array( ast_pro_customizer(), 'wcast_add_customizer_sections' ) );	
		
		// Remove unrelated components.
		add_filter( 'customize_loaded_components', array( ast_pro_customizer(), 'remove_unrelated_components' ), 99, 2 );
		
		// Remove unrelated sections.
		add_filter( 'customize_section_active', array( ast_pro_customizer(), 'remove_unrelated_sections' ), 10, 2 );	
		
		// Unhook divi front end.
		add_action( 'woomail_footer', array( ast_pro_customizer(), 'unhook_divi' ), 10 );
		
		// Unhook Flatsome js
		add_action( 'customize_preview_init', array( ast_pro_customizer(), 'unhook_flatsome' ), 50  );
		
		add_filter( 'customize_controls_enqueue_scripts', array( ast_pro_customizer(), 'enqueue_customizer_scripts' ) );				
		
		add_action( 'parse_request', array( $this, 'set_up_preview' ) );

		add_action( 'customize_preview_init', array( $this, 'enqueue_preview_scripts' ) );			
	}
	
	public function enqueue_preview_scripts() {
		wp_enqueue_style('wcast-preview-styles', ast_pro()->plugin_dir_url() . 'assets/css/preview-styles.css', array(), ast_pro()->version  );		
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
	 * Checks to see if we are opening our custom customizer preview
	 *	 
	 * @return bool
	 */
	public function is_own_preview_request() {
		return isset( $_REQUEST['wcast-completed-email-customizer-preview'] ) && '1' === $_REQUEST['wcast-completed-email-customizer-preview'];
	}
	
	/**
	 * Checks to see if we are opening our custom customizer controls
	 *	 
	 * @return bool
	 */
	public function is_own_customizer_request() {
		return isset( $_REQUEST['email'] ) && 'customer_completed_email' === $_REQUEST['email'];
	}		
	
	/**
	 * Get WooCommerce email settings page URL
	 *	 
	 * @return string
	 */
	public function get_email_settings_page_url() {
		return admin_url( 'admin.php?page=woocommerce-advanced-shipment-tracking' );
	}
	
	/**
	 * Code for initialize default value for customizer
	*/
	public function wcast_generate_defaults() {
		$customizer_defaults = array(			
			'wcast_completed_email_subject' => __( 'Your {site_title} order is now complete', 'woocommerce' ),
			'wcast_completed_email_heading' => __( 'Your order is Complete!', 'woocommerce' ),
			'wcast_completed_email_content' => __( 'We have finished processing your order.', 'woocommerce' ),			
			'display_shipping_items'  	  => 1,
			'shipping_items_heading'      => __( 'Items in this shipment', 'ast-pro' ),
			'display_shippment_item_price'=> 0, 
			'display_product_images'      => 1,
			'display_shipping_address'    => 1,
			'display_billing_address'     => 1,
		);
		return apply_filters( 'ast_customizer_defaults', $customizer_defaults );
	}

	/**
	 * Register our sample default controls
	 */
	public function wcast_register_sample_default_controls( $wp_customize ) {		
		
		/**
		* Load all our Customizer Custom Controls
		*/
		require_once ast_pro()->get_plugin_path() . '/includes/customizer/custom-controls.php';
		
		// Header Text		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[subject]',
			array(
				'default' => $this->defaults['wcast_completed_email_subject'],
				'transport' => 'postMessage',
				'type'  => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[subject]',
			array(
				'label' => __( 'Subject', 'woocommerce' ),
				'description' => esc_html__( 'Available variables:', 'ast-pro' ) . ' {site_title}, {order_number}',
				'section' => 'customer_completed_email',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => $this->defaults['wcast_completed_email_subject'],
				),
			)
		);
		
		// Header Text		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[heading]',
			array(
				'default' => $this->defaults['wcast_completed_email_heading'],
				'transport' => 'refresh',
				'type'  => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[heading]',
			array(
				'label' => __( 'Email heading', 'woocommerce' ),
				'description' => esc_html__( 'Available variables:', 'ast-pro' ) . ' {site_title}, {order_number}',
				'section' => 'customer_completed_email',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => $this->defaults['wcast_completed_email_heading'],
				),
			)
		);
		
		
		// Test of TinyMCE control
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[wcast_completed_email_content]',
			array(
				'default' => $this->defaults['wcast_completed_email_content'],
				'transport' => 'refresh',
				'type'  => 'option',
				'sanitize_callback' => 'wp_kses_post'
			)
		);
		$wp_customize->add_control( new AST_TinyMCE_Custom_control( $wp_customize, 'woocommerce_customer_completed_order_settings[wcast_completed_email_content]',
			array(
				'label' => __( 'Email content', 'ast-pro' ),
				'description' => __( 'Available variables:', 'ast-pro' ) . ' {site_title}, {customer_email}, {customer_first_name}, {customer_last_name}, {customer_username}, {order_number}',
				'section' => 'customer_completed_email',
				'input_attrs' => array(
					'toolbar1' => 'bold italic bullist numlist alignleft aligncenter alignright link',
					'mediaButtons' => true,
					'placeholder' => $this->defaults['wcast_completed_email_content'],
				),
			)
		) );						
		
		$wp_customize->add_setting( 'wcast_completed_code_block',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( new WP_Customize_codeinfoblock_Control( $wp_customize, 'wcast_completed_code_block',
			array(
				'label' => __( 'Available variables:', 'ast-pro' ),
				'description' => '<code>{site_title}<br>{customer_email}<br>{customer_first_name}<br>{customer_last_name}<br>{customer_company_name}<br>{customer_username}<br>{order_number}</code>',
				'section' => 'customer_completed_email',	
			)
		) );

		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[display_options]',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new WP_Customize_Heading_Control( $wp_customize, 'woocommerce_customer_completed_order_settings[display_options]',
			array(
				'label' => __( 'Display Options', 'ast-pro' ),
				'section' => 'customer_completed_email',
			)
		) );				
		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[shipping_items_heading]',
			array(
				'default' => $this->defaults['shipping_items_heading'],
				'transport' => 'refresh',				
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[shipping_items_heading]',
			array(
				'label' => __( 'Shipping items Heading', 'ast-pro' ),
				'section' => 'customer_completed_email',
				'type' => 'text',
			)
		);	
		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[display_shippment_item_price]',
			array(
				'default' => $this->defaults['display_shippment_item_price'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[display_shippment_item_price]',
			array(
				'label' => __( 'Display shipment item price', 'ast-pro' ),
				'description' => '',
				'section' => 'customer_completed_email',
				'type' => 'checkbox',	
			)
		);
		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[display_product_images]',
			array(
				'default' => $this->defaults['display_product_images'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[display_product_images]',
			array(
				'label' => __( 'Display product images', 'ast-pro' ),
				'description' => '',
				'section' => 'customer_completed_email',
				'type' => 'checkbox',	
			)
		);
		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[display_shipping_address]',
			array(
				'default' => $this->defaults['display_shipping_address'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[display_shipping_address]',
			array(
				'label' => __( 'Display Shipping Address', 'ast-pro' ),
				'description' => '',
				'section' => 'customer_completed_email',
				'type' => 'checkbox',
			)
		);
		
		$wp_customize->add_setting( 'woocommerce_customer_completed_order_settings[display_billing_address]',
			array(
				'default' => $this->defaults['display_billing_address'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_completed_order_settings[display_billing_address]',
			array(
				'label' => __( 'Display Billing Address', 'ast-pro' ),
				'description' => '',
				'section' => 'customer_completed_email',
				'type' => 'checkbox',						
			)
		);		
	}
		
	/**
	 * Set up preview
	 *	 
	 * @return void
	 */
	public function set_up_preview() {		
		if ( ! $this->is_own_preview_request() ) {
			return;
		}	
		include ast_pro()->get_plugin_path() . '/includes/customizer/preview/completed_preview.php';
		exit;	
	}

	/**
	 * Code for preview of tracking info in email
	*/	
	public function preview_completed_email() {
		
		$ast = AST_Pro_Actions::get_instance();				
				
		$tracking_info_settings = get_option('tracking_info_settings');					
		
		// Load WooCommerce emails.
		$wc_emails      = WC_Emails::instance();
		$emails         = $wc_emails->get_emails();
		$email_template = 'customer_completed_order';
		$preview_id     = 'mockup';
		$email_type = 'WC_Email_Customer_Completed_Order';
		
		if ( false === $email_type ) {
			return false;
		}	
		
		$order_status = 'completed';			
		
		// Reference email.
		if ( isset( $emails[ $email_type ] ) && is_object( $emails[ $email_type ] ) ) {
			$email = $emails[ $email_type ];
		}
		
		// Get an order
		$order = ast_pro_customizer()->get_wc_order_for_preview( $order_status, $preview_id );		
		
		// Make sure gateways are running in case the email needs to input content from them.
		WC()->payment_gateways();
		// Make sure shipping is running in case the email needs to input content from it.
		WC()->shipping();
			
		$email->object               = $order;
		$email->find['order-date']   = '{order_date}';
		$email->find['order-number'] = '{order_number}';
		if ( is_object( $order ) ) {
			$email->replace['order-date']   = wc_format_datetime( $email->object->get_date_created() );
			$email->replace['order-number'] = $email->object->get_order_number();
			// Other properties
			$email->recipient = $email->object->get_billing_email();
		}
		// Get email content and apply styles.
		$content = $email->get_content();
		$content = $email->style_inline( $content );
		$content = apply_filters( 'woocommerce_mail_content', $content );
		
		add_filter( 'wp_kses_allowed_html', array( ast_pro_customizer(), 'allowed_css_tags' ) );
		add_filter( 'safe_style_css', array( ast_pro_customizer(), 'safe_style_css' ), 10, 1 );
		
		if ( 'plain' === $email->email_type ) {
			$content = '<div style="padding: 35px 40px; background-color: white;">' . str_replace( "\n", '<br/>', $content ) . '</div>';
		}
		echo wp_kses_post( $content );
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
function ast_pro_completed_customizer() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new wcast_completed_customizer_email();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
ast_pro_completed_customizer();
