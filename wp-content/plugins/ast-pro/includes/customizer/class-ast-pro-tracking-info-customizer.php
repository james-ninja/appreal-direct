<?php
/**
 * Customizer Setup and Custom Controls
 *
 */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class Ast_Pro_Customizer_Settings {
	// Get our default values	
	private static $order_ids  = null;
	
	public function __construct() {
		// Get our Customizer defaults
		$this->defaults = $this->wcast_generate_defaults();		
		
		// Register our sample default controls
		add_action( 'customize_register', array( $this, 'ast_register_default_controls' ) );
		
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
	
	
	/**
	 * Add css and js for preview
	*/	
	public function enqueue_preview_scripts() {
		 wp_enqueue_script('wcast-preview-scripts', ast_pro()->plugin_dir_url() . '/assets/js/preview-scripts.js', array('jquery', 'customize-preview'), ast_pro()->version, true);
		 wp_enqueue_style('wcast-preview-styles', ast_pro()->plugin_dir_url() . 'assets/css/preview-styles.css', array(), ast_pro()->version  );
		 $preview_id     = get_theme_mod('wcast_email_preview_order_id');
		 wp_localize_script('wcast-preview-scripts', 'wcast_preview', array(
			'site_title'   => $this->get_blogname(),
			'order_number' => $preview_id,			
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
	 * Checks to see if we are opening our custom customizer preview
	 *	 
	 * @return bool
	 */
	public function is_own_preview_request() {
		return isset( $_REQUEST['wcast-tracking-preview'] ) && '1' === $_REQUEST['wcast-tracking-preview'];
	}
	
	/**
	 * Checks to see if we are opening our custom customizer controls
	 *	 
	 * @return bool
	 */
	public function is_own_customizer_request() {
		return isset( $_REQUEST['email'] ) && 'ast_tracking_general_section' === $_REQUEST['email'];
	}
	
	/**
	 * Get Customizer URL
	 *
	 */
	public function get_customizer_url( $email, $return_tab ) {	
		return add_query_arg( array(
			'wcast-customizer' => '1',
			'email' => $email,		
			//'autofocus[section]' => 'ast_tracking_general_section',	
			'url'                  => urlencode( add_query_arg( array( 'wcast-tracking-preview' => '1' ), home_url( '/' ) ) ),
			'return'               => urlencode( $this->get_email_settings_page_url($return_tab) ),
			//'autofocus[panel]' => 'ast_tracking_display_panel',
		), admin_url( 'customize.php' ) );
	}
	
	/**
	 * Get WooCommerce email settings page URL
	 *	 
	 * @return string
	 */
	public function get_email_settings_page_url( $return_tab ) {
		return admin_url( 'admin.php?page=woocommerce-advanced-shipment-tracking&tab=' . $return_tab );
	}
	
	/**
	 * Code for initialize default value for customizer
	*/	
	public function wcast_generate_defaults() {

		$customizer_defaults = array(
			'wcast_preview_order_id' => 'mockup',
			'display_tracking_info_at' => 'before_order',
			'header_text_change' => '',
			'additional_header_text' => '',
			'fluid_table_layout' => 2,
			'fluid_table_border_color' => '#e0e0e0',
			'fluid_table_border_radius' => 3,
			'fluid_table_background_color' => '#fafafa',
			'fluid_table_padding' => '15',
			'fluid_hide_provider_image' => 0,
			'fluid_hide_shipping_date'	=> 0,
			'fluid_button_text' => __( 'Track Your Order', 'ast-pro' ),
			'fluid_button_background_color' => '#005b9a',
			'fluid_button_font_color' => '#fff',
			'fluid_button_size' => 'normal',
			'fluid_button_padding' => '10',
			'fluid_button_radius' => '3',
			'fluid_button_expand' => 1,		
		);

		return apply_filters( 'ast_customizer_defaults', $customizer_defaults );
	}	
	
	/**
	 * Register our sample default controls
	 */
	public function ast_register_default_controls( $wp_customize ) {		
		/**
		* Load all our Customizer Custom Controls
		*/
		require_once ast_pro()->get_plugin_path() . '/includes/customizer/custom-controls.php';
		
		$font_size_array[ '' ] = __( 'Select', 'woocommerce' );
		for ( $i = 10; $i <= 30; $i++ ) {
			$font_size_array[ $i ] = $i . 'px';
		}
		
		$wp_customize->remove_control('tracking_info_settings[select_tracking_template]');						
		
		// Tracking Display Position
		$wp_customize->add_setting( 'tracking_info_settings[display_tracking_info_at]',
			array(
				'default' => $this->defaults['display_tracking_info_at'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[display_tracking_info_at]',
			array(
				'label' => __( 'Tracking Display Position', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'select',
				'choices' => array(					
					'before_order'		=> __( 'Before Order Details', 'ast-pro' ),
					'after_order'		=> __( 'After Order Details', 'ast-pro' ),							
				)
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[tracking_widget_header]',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new WP_Customize_Heading_Control( $wp_customize, 'tracking_info_settings[tracking_widget_header]',
			array(
				'label' => __( 'Widget Header', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',				
			)
		) );
		
		// Show track label
		$wp_customize->add_setting( 'tracking_info_settings[hide_trackig_header]',
			array(
				'default' => '',
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[hide_trackig_header]',
			array(
				'label' => __( 'Hide Tracking Header', 'ast-pro' ),
				'description' => '',
				'section' => 'ast_tracking_general_section',
				'type' => 'checkbox'
			)
		);
			
		// Header Text		
		$wp_customize->add_setting( 'tracking_info_settings[header_text_change]',
			array(
				'default' => $this->defaults['header_text_change'],
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[header_text_change]',
			array(
				'label' => __( 'Tracking Header text', 'ast-pro' ),
				'description' => '',
				'section' => 'ast_tracking_general_section',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => __( 'Tracking Information', 'ast-pro' ),
				),
				'active_callback' => array( $this, 'active_callback_for_hide_trackig_header' ),	
			)
		);
		
		// Additional text after header
		$wp_customize->add_setting( 'tracking_info_settings[additional_header_text]',
			array(
				'default' => $this->defaults['additional_header_text'],
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[additional_header_text]',
			array(
				'label' => __( 'Additional text after header', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'textarea',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' =>'',
				),
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[tracking_widget_layout]',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new WP_Customize_Heading_Control( $wp_customize, 'tracking_info_settings[tracking_widget_layout]',
			array(
				'label' => __( 'Widget Layout', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',				
			)
		) );
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_table_layout]',
			array(
				'default' => $this->defaults['fluid_table_layout'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_table_layout]',
			array(
				'label' => __( 'Select Layout', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'select',
				'choices' => array(
					'' => __( 'Select', 'woocommerce' ),
					'2'		=> '2 Columns',
					'1'		=> '1 Column',	
				),
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_hide_provider_image]',
			array(
				'default' => $this->defaults['fluid_hide_provider_image'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_hide_provider_image]',
			array(
				'label' => __( 'Hide shipping provider image', 'ast-pro' ),
				'description' => '',
				'section' => 'ast_tracking_general_section',
				'type' => 'checkbox',				
			)
		);

		$wp_customize->add_setting( 'tracking_info_settings[fluid_hide_shipping_date]',
			array(
				'default' => $this->defaults['fluid_hide_shipping_date'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_hide_shipping_date]',
			array(
				'label' => __( 'Hide the shipping date', 'ast-pro' ),
				'description' => '',
				'section' => 'ast_tracking_general_section',
				'type' => 'checkbox',				
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[tracking_widget_design]',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new WP_Customize_Heading_Control( $wp_customize, 'tracking_info_settings[tracking_widget_design]',
			array(
				'label' => __( 'Widget Design', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',				
			)
		) );
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_table_background_color]',
			array(
				'default' => $this->defaults['fluid_table_background_color'],
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_table_background_color]',
			array(
				'label' => __( 'Background color', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'color',
			)
		);						
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_table_border_color]',
			array(
				'default' => $this->defaults['fluid_table_border_color'],
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_table_border_color]',
			array(
				'label' => __( 'Border color', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'color',
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_table_border_radius]',
			array(
				'default' => $this->defaults['fluid_table_border_radius'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new AST_Slider_Custom_Control( $wp_customize, 'tracking_info_settings[fluid_table_border_radius]',
			array(
				'label' => __( 'Border radius', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'input_attrs' => array(
					'default' => $this->defaults['fluid_table_border_radius'],
					'step'  => 1,
					'min'   => 1,
					'max'   => 20,
				),
			)
		));		

		$wp_customize->add_setting( 'tracking_info_settings[fluid_table_padding]',
			array(
				'default' => $this->defaults['fluid_table_padding'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new AST_Slider_Custom_Control( $wp_customize, 'tracking_info_settings[fluid_table_padding]',
			array(
				'label' => __( 'Padding', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'input_attrs' => array(
					'default' => $this->defaults['fluid_table_padding'],
					'step'  => 1,
					'min'   => 1,
					'max'   => 20,
				),
			)
		));	
				
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_options]',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new WP_Customize_Heading_Control( $wp_customize, 'tracking_info_settings[fluid_button_options]',
			array(
				'label' => __( 'Track button', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',				
			)
		) );
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_text]',
			array(
				'default' => $this->defaults['fluid_button_text'],
				'transport' => 'refresh',				
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_button_text]',
			array(
				'label' => __( 'Track button Text', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'text',				
			)
		);

		// Add our Text Radio Button setting and Custom Control for controlling alignment of icons
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_size]',
			array(
				'default' => $this->defaults['fluid_button_size'],
				'transport' => 'refresh',
				'type' => 'option',
				'sanitize_callback' => 'ast_radio_sanitization'
			)
		);
		$wp_customize->add_control( new AST_Text_Radio_Button_Custom_Control( $wp_customize, 'tracking_info_settings[fluid_button_size]',
			array(
				'label' => __( 'Button size', 'ast-pro' ),				
				'section' => 'ast_tracking_general_section',
				'choices' => array(
					'normal' => __( 'Normal', 'ast-pro' ),
					'large' => __( 'Large', 'ast-pro'  )
				)
			)
		) );		
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_background_color]',
			array(
				'default' => $this->defaults['fluid_button_background_color'],
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_button_background_color]',
			array(
				'label' => __( 'Button color', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'color',				
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_font_color]',
			array(
				'default' => $this->defaults['fluid_button_font_color'],
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_button_font_color]',
			array(
				'label' => __( 'Button font color', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'type' => 'color',
			)
		);
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_radius]',
			array(
				'default' => $this->defaults['fluid_button_radius'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new AST_Slider_Custom_Control( $wp_customize, 'tracking_info_settings[fluid_button_radius]',
			array(
				'label' => __( 'Button radius', 'ast-pro' ),
				'section' => 'ast_tracking_general_section',
				'input_attrs' => array(
					'default' => $this->defaults['fluid_button_radius'],
					'step'  => 1,
					'min'   => 1,
					'max'   => 20,
				),
			)
		));			
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_expand]',
			array(
				'default' => $this->defaults['fluid_button_expand'],
				'transport' => 'refresh',
				'sanitize_callback' => '',
				'type' => 'option',
			)
		);
		$wp_customize->add_control( 'tracking_info_settings[fluid_button_expand]',
			array(
				'label' => __( 'Expend the Track button', 'ast-pro' ),
				'description' => '',
				'section' => 'ast_tracking_general_section',
				'type' => 'checkbox',
				'active_callback' => array( $this, 'active_callback_for_1_cl' ),				
			)
		);	
		
		$wp_customize->add_setting( 'tracking_info_settings[fluid_button_expand]',
			array(
				'default' =>  $this->defaults['fluid_button_expand'],
				'transport' => 'refresh',				
				'type' => 'option',
			)
		);
		$wp_customize->add_control( new AST_Customizer_Toggle_Control( $wp_customize, 'tracking_info_settings[fluid_button_expand]', array(
			'label'	      => esc_html__( 'Expend the Track button', 'ast-pro' ),
			'section'     => 'ast_tracking_general_section',
			'settings'    => 'tracking_info_settings[fluid_button_expand]',
			'type'        => 'ios',// light, ios, flat
			'active_callback' => array( $this, 'active_callback_for_1_cl' ),	
		) ) );
	}
	
	public function active_callback_for_hide_trackig_header() {
		$ast = AST_Pro_Actions::get_instance();		
		$hide_trackig_header = $ast->get_option_value_from_array( 'tracking_info_settings', 'hide_trackig_header', '' );
		return ( !$hide_trackig_header ) ? true : false ;			
	}
	
	public function active_callback_for_1_cl() {
		$ast = AST_Pro_Actions::get_instance();		
		$fluid_table_layout = $ast->get_option_value_from_array( 'tracking_info_settings', 'fluid_table_layout', '' );
		return ( '1' == $fluid_table_layout ) ? true : false ;		
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
		include ast_pro()->get_plugin_path() . '/includes/customizer/preview/preview.php';
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
 * Initialise our Customizer settings
 */

$ast_pro_customizer_settings = new ast_pro_customizer_settings();
