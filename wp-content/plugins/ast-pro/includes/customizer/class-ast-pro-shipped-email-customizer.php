<?php
/**
 * Customizer Setup and Custom Controls
 *
 */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class Wcast_Shipped_Customizer_Email {
	// Get our default values	
	public function __construct() {
		// Get our Customizer defaults
		$this->defaults = $this->wcast_generate_defaults();
			
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
	
	public function wcast_order_status_email_type( $order_status ) {
		
		$shipped_status = array(
			'shipped' => __( 'Shipped', 'ast-pro' ),
		);
		$order_status = array_merge( $order_status, $shipped_status );
		return $order_status;
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
		return isset( $_REQUEST['wcast-shipped-email-customizer-preview'] ) && '1' === $_REQUEST['wcast-shipped-email-customizer-preview'];
	}
	
	/**
	 * Checks to see if we are opening our custom customizer controls
	 *	 
	 * @return bool
	 */
	public function is_own_customizer_request() {
		return isset( $_REQUEST['email'] ) && 'custom_shipped_email' === $_REQUEST['email'];
	}	

	/**
	 * Get Customizer URL
	 *
	 */
	public function get_customizer_url( $email, $order_status ) {		
		return add_query_arg( array(
			'wcast-customizer' => '1',
			'email' => $email,
			'order_status' => $order_status,
			'autofocus[section]' => 'custom_shipped_email',
			'url'                  => urlencode( add_query_arg( array( 'wcast-shipped-email-customizer-preview' => '1' ), home_url( '/' ) ) ),
			'return'               => urlencode( $this->get_email_settings_page_url() ),
		), admin_url( 'customize.php' ) );
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
			'wcast_shipped_email_subject' => __( 'Your {site_title} order is now Shipped', 'ast-pro' ),
			'wcast_shipped_email_heading' => __( 'Your Order is Shipped', 'ast-pro' ),
			'wcast_shipped_email_content' => __( "Hi there. we thought you'd like to know that your recent order from {site_title} has been shipped.", 'ast-pro' ),
			'wcast_enable_shipped_email'  => 'no',
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
		
		// Display Shipment Provider image/thumbnail
		$wp_customize->add_setting( 'customizer_shipped_order_settings_enabled',
			array(
				'default' => $this->defaults['wcast_enable_shipped_email'],
				'transport' => 'postMessage',
				'type'      => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'customizer_shipped_order_settings_enabled',
			array(
				'label' => __( 'Enable Shipped order status email', 'ast-pro' ),
				'description' => '',
				'section' => 'custom_shipped_email',
				'type' => 'checkbox',
				//'active_callback' => array( $this, 'active_callback' ),	
			)
		);

		// Header Text		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[subject]',
			array(
				'default' => $this->defaults['wcast_shipped_email_subject'],
				'transport' => 'postMessage',
				'type'  => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[subject]',
			array(
				'label' => __( 'Subject', 'woocommerce' ),
				'description' => esc_html__( 'Available variables:', 'ast-pro' ) . ' {site_title}, {order_number}',
				'section' => 'custom_shipped_email',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => $this->defaults['wcast_shipped_email_subject'],
				),
				//'active_callback' => array( $this, 'active_callback' ),	
			)
		);
		
		// Header Text		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[heading]',
			array(
				'default' => $this->defaults['wcast_shipped_email_heading'],
				'transport' => 'refresh',
				'type'  => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[heading]',
			array(
				'label' => __( 'Email heading', 'woocommerce' ),
				'description' => esc_html__( 'Available variables:', 'ast-pro' ) . ' {site_title}, {order_number}',
				'section' => 'custom_shipped_email',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => $this->defaults['wcast_shipped_email_heading'],
				),
				//'active_callback' => array( $this, 'active_callback' ),	
			)
		);
		
		
		// Test of TinyMCE control
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[wcast_shipped_email_content]',
			array(
				'default' => $this->defaults['wcast_shipped_email_content'],
				'transport' => 'refresh',
				'type'  => 'option',
				'sanitize_callback' => 'wp_kses_post'
			)
		);
		$wp_customize->add_control( new AST_TinyMCE_Custom_control( $wp_customize, 'woocommerce_customer_shipped_order_settings[wcast_shipped_email_content]',
			array(
				'label' => __( 'Email content', 'ast-pro' ),
				'description' => __( 'Available variables:', 'ast-pro' ) . ' {site_title}, {customer_email}, {customer_first_name}, {customer_last_name}, {customer_username}, {order_number}',
				'section' => 'custom_shipped_email',
				'input_attrs' => array(
					'toolbar1' => 'bold italic bullist numlist alignleft aligncenter alignright link',
					'mediaButtons' => true,
					'placeholder' => $this->defaults['wcast_shipped_email_content'],
				),
				//'active_callback' => array( $this, 'active_callback' ),	
			)
		) );						
		
		$wp_customize->add_setting( 'wcast_shipped_code_block',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( new WP_Customize_codeinfoblock_Control( $wp_customize, 'wcast_shipped_code_block',
			array(
				'label' => __( 'Available variables:', 'ast-pro' ),
				'description' => '<code>{site_title}<br>{customer_email}<br>{customer_first_name}<br>{customer_last_name}<br>{customer_company_name}<br>{customer_username}<br>{order_number}</code>',
				'section' => 'custom_shipped_email',	
				//'active_callback' => array( $this, 'active_callback' ),		
			)
		) );

		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[display_options]',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new WP_Customize_Heading_Control( $wp_customize, 'woocommerce_customer_shipped_order_settings[display_options]',
			array(
				'label' => __( 'Display Options', 'ast-pro' ),
				'section' => 'custom_shipped_email',
				//'active_callback' => array( $this, 'active_callback' ),	
			)
		) );				
		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[shipping_items_heading]',
			array(
				'default' => $this->defaults['shipping_items_heading'],
				'transport' => 'refresh',				
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[shipping_items_heading]',
			array(
				'label' => __( 'Shipping items Heading', 'ast-pro' ),
				'section' => 'custom_shipped_email',
				'type' => 'text',
				//'active_callback' => array( $this, 'active_callback' ),
			)
		);	
		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[display_shippment_item_price]',
			array(
				'default' => $this->defaults['display_shippment_item_price'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[display_shippment_item_price]',
			array(
				'label' => __( 'Display shipment item price', 'ast-pro' ),
				'description' => '',
				'section' => 'custom_shipped_email',
				'type' => 'checkbox',	
			)
		);
		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[display_product_images]',
			array(
				'default' => $this->defaults['display_product_images'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[display_product_images]',
			array(
				'label' => __( 'Display product images', 'ast-pro' ),
				'description' => '',
				'section' => 'custom_shipped_email',
				'type' => 'checkbox',
				//'active_callback' => array( $this, 'active_callback' ),	
			)
		);
		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[display_shipping_address]',
			array(
				'default' => $this->defaults['display_shipping_address'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[display_shipping_address]',
			array(
				'label' => __( 'Display Shipping Address', 'ast-pro' ),
				'description' => '',
				'section' => 'custom_shipped_email',
				'type' => 'checkbox',
				//'active_callback' => array( $this, 'active_callback' ),
			)
		);
		
		$wp_customize->add_setting( 'woocommerce_customer_shipped_order_settings[display_billing_address]',
			array(
				'default' => $this->defaults['display_billing_address'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'woocommerce_customer_shipped_order_settings[display_billing_address]',
			array(
				'label' => __( 'Display Billing Address', 'ast-pro' ),
				'description' => '',
				'section' => 'custom_shipped_email',
				'type' => 'checkbox',
				//'active_callback' => array( $this, 'active_callback' ),						
			)
		);		
	}
	
	public function active_callback() {
		return ( $this->is_own_preview_request() ) ? true : false ;	
	}
		
	/**
	 * Set up preview
	 *	 
	 * @return void
	 */
	public function set_up_preview() {		
		// Make sure this is own preview request.
		if ( ! $this->is_own_preview_request() ) {
			return;
		}	
		include ast_pro()->get_plugin_path() . '/includes/customizer/preview/shipped_preview.php';
		exit;
	}
	
	/**
	 * Code for preview of delivered order status email
	*/
	public function preview_shipped_email() {
		
		// Load WooCommerce emails.
		$wc_emails      = WC_Emails::instance();
		$emails         = $wc_emails->get_emails();		
		$preview_id     = 'mockup';						
		
		$email_type = 'WC_Email_Customer_Shipped_Order';
		
		if ( false === $email_type ) {
			return false;
		}
		
		// Reference email.
		if ( isset( $emails[ $email_type ] ) && is_object( $emails[ $email_type ] ) ) {
			$email = $emails[ $email_type ];
		}
		$order_status = 'shipped';
		
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
function ast_pro_shipped_customizer() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new wcast_shipped_customizer_email();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
ast_pro_shipped_customizer();

add_action( 'customize_save_customizer_shipped_order_settings_enabled', 'woocommerce_customer_shipped_order_settings_fun', 100, 1 ); 

/**
 * Update Delivered order email enable/disable
 *
 */
function woocommerce_customer_shipped_order_settings_fun( $data ) {
	
	if ( isset( $_POST['customize_preview_nonce'] ) && wp_verify_nonce( wc_clean( $_POST['customize_preview_nonce'] ), 'preview-customize_' . get_stylesheet() ) ) { 	
	
		$customized = isset( $_POST['customized'] ) ? wc_clean( $_POST['customized'] ) : '';
		$post_values = json_decode( wp_unslash( $customized ), true );
		$shipped_order_settings = get_option( 'woocommerce_customer_shipped_order_settings' );
		
		if ( isset( $post_values[ 'customizer_shipped_order_settings_enabled' ] ) && ( 1 == $post_values[ 'customizer_shipped_order_settings_enabled' ] ) ) {
			$shipped_order_settings['enabled'] = 'yes';
		} else {
			$shipped_order_settings['enabled'] = 'no';
		}		
		update_option( 'woocommerce_customer_shipped_order_settings', $shipped_order_settings );
	}
}
