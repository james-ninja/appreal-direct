<?php
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce USAePay Transaction API - Payment Gateway
 * 
 * @author Ryan IT Solutions
 * @version since 1.13.8
 */

if( !class_exists( 'WC_Payment_Gateway' )) return; 

class USAePay_TRANS_WC_API extends WC_Payment_Gateway_CC {

	public static $log_enabled = false;
    public static $log = false;
    private $source_key;

	public function __construct(){

		$this->id 					= 'usaepaytransapi';
		$this->method_title			= esc_html__( 'USAePay', 'woocommerce' );
		$this->method_description 	= esc_html__( 'USAePay Transaction API Payment Gateway Integration', 'woocommerce' );
		
		$this->has_fields           = true; 
		
		$this->supports = array(
		  'products',
		  'default_credit_card_form',
		  'tokenization',
		  'refunds',
		  'subscriptions',
		  'subscription_cancellation',
		  'subscription_reactivation',
		  'subscription_suspension',
		  'subscription_amount_changes',
		  'subscription_payment_method_change', // Subs 1.n compatibility.
		  'subscription_payment_method_change_customer',
		  'subscription_payment_method_change_admin',
		  'subscription_date_changes',
		  'multiple_subscriptions',
		  'pre-orders'
		);

		$this->init_form_fields();
		$this->init_settings();

		$this->title 			= esc_html__( $this->settings[ 'title' ], 'woocommerce' );
		$this->description 		= esc_html__( $this->settings[ 'description' ], 'woocommerce' );	
		$this->environment 		= ! empty($this->settings[ 'environment' ]) ? $this->settings[ 'environment' ] : 'sandbox' ;


		if($this->environment == 'live' ){
		    $this->source_key 	    = ! empty( $this->settings[ 'source_key1' ]) ? $this->settings[ 'source_key1' ] : '';
		    $this->pin  	 		= ! empty( $this->settings[ 'pin1' ]) ?  $this->settings[ 'pin1' ] : '';			
		} else {
			$this->source_key 	    = !empty( $this->settings[ 'source_key2' ]) ? $this->settings[ 'source_key2' ] : '';
		    $this->pin  	 		= !empty( $this->settings[ 'pin2' ])? $this->settings[ 'pin2' ] : '';
		}
		
		$this->msg['message'] 		= '';
		$this->msg['class'] 		= '';
		$this->cards 				= $this->settings[ 'cards' ];
		self::$log_enabled    		= $this->settings[ 'debug' ];

		if( !empty ( $this->settings[ 'paymentaction' ] ) ){
			$this->paymentaction = $this->settings[ 'paymentaction' ];
		} else {
			$this->paymentaction = 'cc:sale';
		}
		
		if( !empty( $this->settings['testmode'] ) ){
			$this->testmode = $this->settings['testmode'];
		} else {
			$this->testmode = 'no';
		}

		$this->hide_save_card     = 'yes' === $this->settings['hide_save_card'];
		$this->hide_card_name     = 'yes' === $this->settings['hide_card_name'];
		$this->reauth             = 'yes' === $this->settings['reauth'];
		$this->onedollar_auth     = 'yes' === $this->settings['onedollar_auth'];
		$this->o_subtotal         = 'yes' === $this->settings['o_subtotal'];
		$this->o_product          = !empty($this->settings['o_product']) ? $this->settings['o_product'] : '';

		//* USAePay Gateway URL

		if( $this->environment == 'live' ){
			$this->usaepaytransapi_url = "https://www.usaepay.com/gate";
		} else {
			$this->usaepaytransapi_url = "https://sandbox.usaepay.com/gate";
		}

		$this->default_status = !empty($this->settings['default_status']) ? $this->settings['default_status'] : 'default';

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
		add_action( 'woocommerce_credit_card_form_start', array( $this, 'before_cc_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script') ); 	
		add_action( 'woocommerce_order_item_add_action_buttons', array( $this, 'display_capture_button' ), 10, 1 );
		add_action( 'woocommerce_order_item_add_action_buttons', array( $this, 'display_reauth_capture_button' ), 10, 1 );		
		
		add_action( 'wp_footer', array( $this, 'custom_css' ) );
		add_action( 'woocommerce_admin_order_totals_after_discount', array( $this, 'admin_order_sub_total'), 10, 1);

		if(is_admin()){
			
			add_action( 'save_post', array( $this, 'process_authonly_capture' ), 10, 1 );
			add_action( 'save_post', array( $this, 'process_authonly_reauth_capture' ), 10, 1 );
			add_filter( 'save_post', array( $this, 'autocapture_payment_complete_order_status' ), 10, 1 );

		}

	}

	//* WC admin options
	
	public function admin_options(){

		global $woocommerce;
	
		if ( $this->environment == 'live' && is_ssl() === false && get_option('woocommerce_force_ssl_checkout') == 'no' && $this->enabled == 'yes' ) : ?>
			<div class="inline error">
				<p>
					<?php echo sprintf(esc_html__('%s Sandbox testing is disabled and can perform live transactions but the force SSL option is disabled, your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'woocommerce'), $this->method_title  ); ?>
				</p>
			</div>
		<?php endif; ?>
		<h3><?php echo esc_html__(  $this->method_title .' - WooCommerce Payment Gateway', 'woocommerce' ); ?></h3>
		<?php
		 echo esc_html__( sprintf('Learn how to setup <a href="%s" target="_blank">USAePay Transaction API Payment Gateway</a>', esc_url( "https://www.ryanplugins.com/how-to-setup-woocommerce-usaepay-payment-gateway/" )), 'woocommerce' );
		?>

		<table class="form-table"><?php
		// Generate the HTML For the settings form.
		$this->generate_settings_html();
		?>
		</table>
		<!--/.form-table-->
		<?php 
			
	}


	//* WC card payment form 

	public function payment_fields(){

		if ( $this->environment == 'sandbox' ){

			$description = sprintf( __( 'TEST MODE/SANDBOX ENABLED Use a test card: %s', 'woocommerce') , '<a href="https://help.usaepay.info/developer/reference/testcards/">https://help.usaepay.info/developer/reference/testcards/</a>' );
      

			echo wp_kses_post($description);
		}

		if( $this->description ){
			echo wpautop( esc_html__(  wptexturize( $this->description  ), 'woocommerce' ));
		}

		parent::payment_fields();

	}

	//* USAePay Gateway Settings

	public function init_form_fields(){

		$products = wc_get_products(array(
        	'numberposts' => -1,
        	'post_status' => 'published',
    	));

		$p = array();

		if( !empty($products)){
			foreach ($products as $key => $value) {
				if( $value->get_catalog_visibility() == 'hidden' ){
					$p[$value->get_id()] = $value->get_name();
				}
				
			}
		}

		$this->form_fields = array(
				'enabled' 			=> array(
                    'title' 		=> esc_html__( 'Enable/Disable', 'woocommerce' ),
                    'type' 			=> 'checkbox',
                    'label' 		=> esc_html__( sprintf( "Enable %s Payment Module ", $this->method_title ), 'woocommerce' ),
                    'default' 		=> 'no'
                    ),
                'title' => array(
                    'title' 		=> esc_html__( 'Title:', 'woocommerce' ),
                    'type'			=> 'text',
                    'description' 	=> esc_html__( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default' 		=> esc_html__(  $this->method_title , 'woocommerce' )
                    ),
                'description' => array(
                    'title' 		=> esc_html__( 'Description:', 'woocommerce' ),
                    'type' 			=> 'textarea',
                    'description' 	=> esc_html__( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
                    'default' 		=> esc_html__( sprintf( "Pay with your credit card via %s.", $this->method_title ), 'woocommerce' )
                    ),
                'environment' 	=> array(
                    'title' 		=> esc_html__( 'Environment', 'woocommerce' ),
                    'type' 			=> 'select',
                    'description' 	=> '',
       				'options'     	=> array(
                    	'sandbox' 	=> esc_html__( 'Sandbox', 'woocommerce' ),
				        'live'		=> esc_html__( 'Production', 'woocommerce' )
					)
				),
				
                'source_key1' => array(
                    'title' 		=> esc_html__( 'Source Key (Live)', 'woocommerce' ),
                    'type' 			=> 'password',
                    'description' 	=> esc_html__( sprintf( "Your %s Live Merchants Source key must be generated within the console Live.", $this->method_title ), 'woocommerce' ),
                    'desc_tip'      => false,
                    ),
				'pin1' => array(
                    'title' 		=> esc_html__( 'Pin (Live)', 'woocommerce' ),
                    'type' 			=> 'password',
                    'description' 	=> esc_html__( sprintf("Your %s Live Merchants Source PIN must be generated within the console Live.", $this->method_title), 'woocommerce' ),
                    'desc_tip'      => false,
                    ),
				'source_key2' => array(
                    'title' 		=> esc_html__( 'Source Key (Test)', 'woocommerce' ),
                    'type' 			=> 'password',
                    'description' 	=> esc_html__( sprintf("Your %s Test Merchants Source key must be generated within the console Test.", $this->method_title ), 'woocommerce' ),
                    'desc_tip'      => false,
                    ),
				'pin2' => array(
                    'title' 		=> esc_html__( 'Pin (Test)', 'woocommerce' ),
                    'type' 			=> 'password',
                    'description' 	=> esc_html__( sprintf("Your %s Test Merchants Source PIN must be generated within the console Test.", $this->method_title), 'woocommerce' ),
                    'desc_tip'      => false,
                    ),
                'paymentaction' => array(
		            'title'       => esc_html__( 'Processing Commands', 'woocommerce' ),
		            'type'        => 'select',
		            'description' => esc_html__( 'Choose transaction types.', 'woocommerce' ),
		            'default'     => 'cc:sale',
		            'desc_tip'    => true,
		            'options'     => array(
		              'cc:sale'          	=> esc_html__( 'Sale', 'woocommerce' ),
		              'cc:authonly' 		=> esc_html__( 'Authonly', 'woocommerce' )
		            )
		        ),

		        'cards' => array(
						'title'             => esc_html__( 'Accepted Card Logos', 'woocommerce' ),
						'type'              => 'multiselect',
						'class'             => 'wc-enhanced-select',
						'css'               => 'width: 450px;',
						'default'           => array( 
														'visa' => 'visa' ,
													  	'mastercard' => 'mastercard', 
													  	'discover' => 'discover', 
													  	'amex' => 'amex',
													  	'jcb' => 'jcb' ) ,
						'description'       => esc_html__( 'You can dispaly your selected accepted credit cards & logos during checkout', 'woocommerce' ),
						'options'           => array( 
														'visa' => 'visa', 
														'mastercard' => 'mastercard', 
														'discover' => 'discover', 
														'amex' => 'amex',
														'jcb' => 'jcb',
														'diners' => 'diners',
														'laser' => 'laser',
														'maestro' => 'maestro'
												),
						'desc_tip'          => true,
						'custom_attributes' => array(
							'data-placeholder' => esc_html__( 'Set Accepted Card Logos', 'woocommerce' )
						)
					),

		        'hide_save_card' => array(
		          'title'             => esc_html__( 'Save to account', 'woocommerce' ),
		          'type'              => 'checkbox',
		          'label'             => esc_html__( 'Hide save card to account', 'woocommerce' ),
		          'default'           => 'no',
		          'description'       =>  esc_html__( 'Hide save to account option in checkout page' ),
		        ),

		        'hide_card_name' => array(
		          'title'             => esc_html__( 'Cardholder Name', 'woocommerce' ),
		          'type'              => 'checkbox',
		          'label'             => esc_html__( 'Hide Cardholder Name', 'woocommerce' ),
		          'default'           => 'no',
		          'description'       =>  esc_html__( 'Hide Cardholder Name option in checkout page' ),
		        ),
		        
				 'testmode' 		=> array(
                    'title' 		=> esc_html__( 'Enable Testmode', 'woocommerce' ),
                    'type' 			=> 'checkbox',
                    'label' 		=> esc_html__( 'If set to yes then the transaction will be simulated but not actually processed.', 'woocommerce' ),
                    'default' 		=> 'no'
                 ),

				 'default_status' => array(
					'title'       => esc_html__( 'Default Order Status', 'woocommerce' ),
					'type'        => 'select',
					'description' => esc_html__( 'Choose order status.', 'woocommerce' ),
					'default'     => 'default',
					'desc_tip'    => true,
					'options'     => array(
						'default'		=> esc_html__( 'Default', 'woocommerce' ),
						'on-hold'       => esc_html__( 'On Hold', 'woocommerce' ),
						'processing' 	=> esc_html__( 'Processing', 'woocommerce' ),
						'pending' 		=> esc_html__( 'Pending Payment', 'woocommerce' ),
						'completed' 	=> esc_html__( 'Completed ', 'woocommerce' )
						)
				),

                 'reauth' 		=> array(
                    'title' 		=> esc_html__( 'Enable re-auth+capture', 'woocommerce' ),
                    'type' 			=> 'checkbox',
                    'label' 		=> esc_html__( 'If set to yes then the order you can manually be re-auth and capture then automatic cancel the old auth transaction in wc orders.', 'woocommerce' ),
                    'default' 		=> 'no'
                 ),

                 'onedollar_auth' 		=> array(
                    'title' 		=> esc_html__( 'Enable $1 authorization', 'woocommerce' ),
                    'type' 			=> 'checkbox',
                    'label' 		=> esc_html__( 'If set to yes then  $1 authorization total amount will be process', 'woocommerce' ),
                    'default' 		=> 'no'
                 ),

                 'o_subtotal' 		=> array(
                    'title' 		=> esc_html__( 'Enable override subtotal', 'woocommerce' ),
                    'type' 			=> 'checkbox',
                    'label' 		=> esc_html__( 'Enable', 'woocommerce' ),
                    'default' 		=> 'no'
                 ),

                 'o_product' => array(
						'title'             => esc_html__( 'Product to add for new Subtotal', 'woocommerce' ),
						'type'              => 'select',
						'options'           => $p
					),
		       
				'debug' => array(
					'title'             => esc_html__( 'Debug Log', 'woocommerce' ),
					'type'              => 'checkbox',
					'label'             => esc_html__( 'Enable logging', 'woocommerce' ),
					'default'           => 'no',
					'description'       =>  sprintf( esc_html__( 'Log %s events, such as API requests, inside %s', 'woocommerce' ), $this->method_title, wc_get_log_file_path( $this->id ) ),
				)
		);

	}

	//* USAePay Process Payment

	public function process_payment( $order_id ){

		global $woocommerce;
			
		$order = wc_get_order( $order_id );
		
		$params[ "UMcommand" ] 		= $this->paymentaction; 
		$params[ "UMcard" ] 	 	= sanitize_text_field($_REQUEST[ 'usaepaytransapi-cardnumber' ]); //'4761530001111118';
		$expiry_m 					= strlen(absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]));
		$expiry_m                   = ($expiry_m <= 1 ) ? "0".absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]) : absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]);
		$expiry_y 					= substr( absint($_REQUEST[ 'usaepaytransapi-expiry_y' ]), -2);
	    $params[ "UMexpir" ] 	 	= $expiry_m.$expiry_y;

	    $token_id 					= sanitize_text_field($_REQUEST[ 'wc-usaepaytransapi-payment-token' ]);

		$params[ "UMcvv2" ] 		= sanitize_text_field($_REQUEST[ 'usaepaytransapi-card_cvc' ]);

		if( !empty($token_id)){
			$token = $this->use_card_token($token_id);

			if( !empty($token) && is_array($token)){

				$params[ "UMcard" ] 	 = $token[ "UMcard" ];
				$params[ "UMexpir" ] 	 =  $token[ "UMexpir" ];
				unset($params[ "UMcvv2" ]);
			}
		}

		if( $this->onedollar_auth === true ){
			
			$params[ "UMamount" ]  		= "1.00";

		} else {
			$params[ "UMamount" ]  		= $order->get_total();	
		}

		$order->add_order_note(
            esc_html__( sprintf(
                "%s - %s%s amount charge",
                $this->method_title,
                get_woocommerce_currency_symbol(),
                $params[ "UMamount" ]
            ), 'woocommerce' )
        );
	    
	    

	    $woo_currency 				= get_woocommerce_currency();
	    $currency_code 				= $this->get_currency_code($woo_currency);
	    $params[ "UMcurrency" ] 	= $currency_code;
	    $params[ "UMinvoice" ] 		= $order->get_order_number();
	    $params[ "UMorderid" ] 		= $order_id;
	    $params[ "UMdescription" ] 	= sanitize_text_field(sprintf( "WC ORDER #%s", $order->get_order_number()));

	    $card_name 					= sanitize_text_field($_REQUEST[ 'usaepaytransapi-card-name' ]);

	    if( ! empty($card_name) ){
	    	$params[ "UMname" ] 	=  esc_html__( $card_name, 'woocommerce' ) ;
	    } else {
	    	$params[ "UMname" ] 	=  esc_html__( $order->get_billing_first_name(), 'woocommerce' ) . " ".  esc_html__( $order->get_billing_last_name(), 'woocommerce' ) ;
	    }
	    
	    $params[ "UMstreet" ] 		= esc_html__( $order->get_billing_address_1() .' '. $order->get_billing_address_2(), 'woocommerce' );
	    $params[ "UMzip" ] 			= $order->get_billing_postcode();

	    //* Billing Address Fields

	    $params[ "UMbillfname" ] 	= esc_html__( $order->get_billing_first_name(), 'woocommerce' );
	    $params[ "UMbilllname" ] 	= esc_html__( $order->get_billing_last_name(), 'woocommerce' );
	    $params[ "UMbillcompany" ] 	= esc_html__( $order->get_billing_company(), 'woocommerce' );
	    $params[ "UMbillstreet" ] 	= esc_html__( $order->get_billing_address_1(), 'woocommerce' );
	    $params[ "UMbillstreet2" ] 	= esc_html__( $order->get_billing_address_2(), 'woocommerce' );
	    $params[ "UMbillcity" ] 	= esc_html__( $order->get_billing_city(), 'woocommerce' );
	    $params[ "UMbillstate" ] 	= esc_html__( $order->get_billing_state(), 'woocommerce' );
	    $params[ "UMbillzip" ] 		= $order->get_billing_postcode();
	    $params[ "UMbillcountry" ] 	= esc_html__( $order->get_billing_country(), 'woocommerce');
	    $params[ "UMbillphone" ] 	= $order->get_billing_phone();
	    $params[ "UMemail" ] 		= $order->get_billing_email();
	    
	     //* Shipping Address Fields

	    $params[ "UMshipfname" ] 	= esc_html__( $order->get_shipping_first_name(), 'woocommerce' );
	    $params[ "UMshiplname" ] 	= esc_html__( $order->get_shipping_last_name(), 'woocommerce' );
	    $params[ "UMshipcompany" ] 	= esc_html__( $order->get_shipping_company(), 'woocommerce' );
	    $params[ "UMshipstreet" ] 	= esc_html__( $order->get_shipping_address_1(), 'woocommerce' );
	    $params[ "UMshipsreet2" ] 	= esc_html__( $order->get_shipping_address_2(), 'woocommerce' );
	    $params[ "UMshipcity" ] 	= esc_html__( $order->get_shipping_city(), 'woocommerce' );
	    $params[ "UMshipstate" ] 	= esc_html__( $order->get_shipping_state(), 'woocommerce' );
	    $params[ "UMshipzip" ] 		= $order->get_shipping_postcode();
	    $params[ "UMshipcountry" ] 	= esc_html__( $order->get_shipping_country(), 'woocommerce' );
	    $params[ "UMshipphone" ] 	= $order->get_billing_phone();

	    //* save card
	    $save_card 					= (bool) sanitize_text_field($_REQUEST['wc-usaepaytransapi-new-payment-method']);

	    if( $save_card === true && !is_numeric($token_id) ){
	    	unset($params[ "UMcvv2" ]);
	    	$params[ 'UMsaveCard' ] = "true";
	    } else {
	    	$params[ 'UMsaveCard' ] = "false";
	    }

	    //* Testmode
		if( $this->testmode == 'yes' ){
			$params[ "UMtestmode" ] = 1; 	
		} else {
			$params[ "UMtestmode" ] = 0;
		}

		$params["UMisRecurring"] = "false";

		if( $this->reauth === true && $save_card === false && !is_numeric($token_id) ){
			//wc_add_notice( esc_html__( sprintf( "Payment Failed with message: '%s'", "Please save your card for re-auth order" ), 'woocommerce' ), "error");
		    //return false;
		    $params[ 'UMsaveCard' ] = "true";
		    $save_card = true;

		}

	    //* Fire payment
		$result = $this->request( $params );
		
		if( $result['response_code'] == 200 && $result[ 'response_message' ] == 'OK' && ! empty($result['response_body'])){

			$data 		= wp_parse_args( $result['response_body'], $defaults );

			if(!empty($this->pin)){
				$isvalid 	= $this->transaction_validator($data);
			} else {
				$isvalid = (bool) true;
			}

			if($data[ 'UMstatus' ] == 'Approved' && $isvalid === true ){

				$order->payment_complete( sanitize_text_field($data[ 'UMrefNum' ]) );
				$order_status = $this->virtual_order_payment_complete_order_status( $order_id );								

				if( $this->default_status == 'default' ){
					if($this->paymentaction == 'cc:authonly' ){
						$order->update_status( 'on-hold' );
					} else {
						$order->update_status( $order_status );						
					}
				} else {
					$order->update_status( $this->default_status );
				}		

				
				update_post_meta( $order_id, '_'. $this->id .'_refnum', sanitize_text_field($data[ 'UMrefNum' ]) );

				if( $this->reauth === true ){
					$cardref = !empty($data[ 'UMcardRef' ]) ? $data[ 'UMcardRef' ] : $params[ "UMcard" ];
					update_post_meta( $order_id, '_'. $this->id .'_cardref', sanitize_text_field($cardref) );
				}

				$order->add_order_note(
		            esc_html__( sprintf(
		                "%s Payment Completed with Transaction Id of '%s'",
		                $this->method_title,
		                sanitize_text_field($data[ 'UMrefNum' ])
		            ), 'woocommerce' )
		        );

		        //* save cc:authonly command

		        if($this->paymentaction == 'cc:authonly' ){
		        	update_post_meta( $order_id, '_usaepaytransapi_command', 'cc:authonly' );
		        }

		        $card_token = array();

		        //* Save token
				if( $save_card === true && !is_numeric($token_id) ){

					$card_token[ 'token' ] 				= sanitize_text_field($data[ 'UMcardRef' ]);
			        $card_token[ 'card_type' ] 			= sanitize_text_field($data[ 'UMcardType' ]);
			        $card_token[ 'set_last4' ]			= substr(sanitize_text_field($data[ 'UMmaskedCardNum' ]), -4 );
			        $card_token[ 'set_expiry_month' ]	= $expiry_m;
			        $card_token[ 'set_expiry_year' ]	= absint($_REQUEST[ 'usaepaytransapi-expiry_y' ]);

			        //* Fire save card token
			        $this->save_token($card_token);

				}
		        

		        WC()->cart->empty_cart();
				$redirect_url = $this->get_return_url( $order );

				$pay_for_order = ! empty($_REQUEST[ 'pay_for_order' ]) ? $_REQUEST[ 'pay_for_order' ] : '';

				if( $pay_for_order == 'true' ){
						ob_start();
						?>
						<script type="text/javascript">
							window.location.replace("<?php echo esc_url($redirect_url); ?>");	
						</script>
						<?php
						exit;

				} else {

				return array(
						'result' => 'success',
						'redirect' => $redirect_url
					);

				}

			} else {

				$order->update_status( 'failed' );

				$order->add_order_note(
		            esc_html__( sprintf(
		                "%s Payment Failed with message: '%s'",
		                $this->method_title,
		                sanitize_text_field($data['UMerror']) 
		            ), 'woocommerce' )
		        );

	        wc_add_notice( esc_html__( sprintf( "Payment Failed with message: '%s'", sanitize_text_field($data['UMerror'])  ), 'woocommerce' ) , "error");
	        return false;

			} 


		} else {

			$order->update_status( 'failed' );

			$order->add_order_note(
		            esc_html__( sprintf(
		                "%s Payment Failed with message: '%s'",
		                $this->method_title,
		                $result['response_body']->error_code .' - '. $result['response_body']->error 
		            ), 'woocommerce' )
		        );

	        wc_add_notice( esc_html__( sprintf( "Payment Failed with message: '%s'", $result['response_body']->error_code .' - '. $result['response_body']->error  ), 'woocommerce' ), "error");
	        return false;

		}
		

	}

	//* USAePay get saved card token

	public function use_card_token( $token_id = '' ){

		$result = array();

		if( !empty( $token_id) && is_numeric($token_id)){
			$card_token = WC_Payment_Tokens::get( $token_id );
			if( !empty($card_token->get_token())){
				$result = array(
					'UMcard' 	=> 	$card_token->get_token(),
					'UMexpir' 	=> 	'0000'
				);
			}

		}

		return $result;
	}

	//* USAePay save card token

	public function save_token( $params ){
			
		$customer_token 	= get_user_meta( get_current_user_id(), $this->id . '_customer_token', true );
		$customer_token_id 	= null;
		
		// Add New Customer Card Token
		$customer_tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), $this->id );

		if( ! empty($customer_tokens)){
			foreach ( $customer_tokens as $c_token ) {
				if ( $c_token->get_token() == $customer_token ) {
					$customer_token_id = $c_token->get_id();
					break;
				}
			}
		}

		if( $customer_token_id  == null ){
			$token = new WC_Payment_Token_CC();
		} else {
			$token = WC_Payment_Tokens::get( $customer_token_id );
		}

		$token->set_token( $params[ 'token' ] );
		$token->set_gateway_id( $this->id );
		$token->set_card_type( $params[ 'card_type' ] );
		$token->set_last4( $params[ 'set_last4' ] );
		$token->set_expiry_month(  $params['set_expiry_month'] );
		$token->set_expiry_year(  $params['set_expiry_year'] );

		if ( is_user_logged_in() ) {
			$token->set_user_id( get_current_user_id() );
		}

		//var_dump( $token->validate() ); // bool(true)
		$result = $token->save();

		if( $result == true ){
			return $token;
		} else {
			return null;
		}


	}

	//* WC check product is virtual

	public function virtual_order_payment_complete_order_status( $order_id ){
		
		$order = wc_get_order( $order_id );
	 
    	$virtual_order = null;

    	if( is_a( $order, 'WC_Order' ) ) {

    		if ( count( $order->get_items() ) > 0 ) {
 
	      		foreach( $order->get_items() as $order_item_id => $order_item ) {

	      			$product_id = $order_item['product_id'];
					$product = wc_get_product( $product_id );

					if( $product ) {
						if( $product->is_virtual() ){
							$virtual_order = true;
							break;
						}
						else{
							$virtual_order = false;
							break;
						}
					}
	        		
	      		}
	    	}

    	}
	 
	    // virtual order, mark as completed
	    if ( $virtual_order ) {
	      return 'completed';
	    }
	  	
	 
	  	// non-virtual order, return original status
	  	return $order->get_status();
	}

	//* WC payment settings message

	public function showMessage( $content ){
		$html  = '';
		$html .= '<div class="box '.$this->msg['class'].'-box">';
		$html .= esc_html__( $this->msg['message'], 'woocommerce');
		$html .= '</div>';
		$html .= $content;
			
		return $html;
	}

	//* WC request and response logger

	public static function log( $message ) {
		if ( self::$log_enabled == 'yes' ) {
			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}
			self::$log->add( 'USAePay.Transaction.API' , $message );
		}
	}

	//* WC card icons

	public function get_icon() {

		if( ! empty( $this->cards ) && is_array( $this->cards )){
			$icon = '';
			foreach ( $this->cards as $key => $value) {
				$icon  .= '<img src="' . esc_url(WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/'.$value.'.svg' )) . '" alt="'.ucwords($value).'" width="32" />';
			}

		} else {
			$icon = '';
		}
		

		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}

	//* USAePay Remote Call Requests

	public function request($params){

		$umcommand 	= $params['UMcommand'];
		$pin 		= $this->pin;
	    $umkey 		= $this->source_key;
	    //$hashseed 	= mktime();   // mktime returns the current time in seconds since epoch.
	    $hashseed 	= time();   
	    $hashdata 	= $umcommand . ":" . $pin . ":" . $params['UMamount'] . ":" . $params['UMinvoice'] . ":" . $hashseed ;
	    $hash 		= md5 ( $hashdata );   // php includes a built-in md5 function that will create the hash
	    $UMhash 	= "m/$hashseed/$hash/y";
	    $data1 		= array(
							'UMkey' 	=> $umkey,
							'UMcommand' => $umcommand,
							'UMhash' 	=> $UMhash,
							'UMip'      => WC_Geolocation::get_ip_address()
						);

		$body_params = array_merge($data1, $params);
	
		$response = wp_remote_request( $this->usaepaytransapi_url,
						array(
							'method'    => 'POST',
							'headers'   => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
							'body'      => $body_params,
							'timeout'   => 90,
							'sslverify' => true,
							'user-agent' => esc_html__( 'WooCommerce USAePay Payment Gateway.', 'woocommerce') 
						));


		$response_log  			= wp_parse_args( $response['body'], $defaults );
		$body_params['UMcard']  = '****-****-****-'.substr($body_params['UMcard'],-4);
		$body_params['UMkey']  = '***************-'.substr($body_params['UMkey'],-4);
		$body_params['UMhash']  = '***************-'.substr($body_params['UMhash'],-4);
		self::log( $this->method_title . ' Request ' . print_r( $body_params , true ) );
		self::log( $this->method_title . ' Response ' . print_r( $response_log , true ) );

		  if ( is_wp_error( $response ) ) {
				$result[ 'code' ] 				= false;
				$result[ 'message' ] 			= $response->get_error_message();
		  } else {
				$result[ 'code' ] 				= true;
				$result[ 'response_code' ] 		= $response['response']['code'];
				$result[ 'response_message' ] 	= $response['response']['message'];
				$result[ 'response_body' ] 		= $response['body'];
		  }

		 return $result;

	}

	//* USAePay Transaction validator

	public function transaction_validator($params){

		// Pin assigned to source key
	    $pin 			= $this->pin;
	    $UMresponseHash = $params[ 'UMresponseHash' ];
	    $UMrefNum 		= $params[ 'UMrefNum' ];
	    $UMresult 		= $params[ 'UMresult' ];

	    // break apart response hash
	    if(!$UMresponseHash) return false;

	    $tmp 			= explode('/', $UMresponseHash );
	    $gatewaymethod 	= $tmp[0];
	    $gatewayseed 	= $tmp[1];
	    $gatewayhash 	= $tmp[2];

	    // assembly prehash data
	    $prehash = $pin . ':' . $UMresult . ':' . $UMrefNum . ':' . $gatewayseed;

	    // calculate what we think the hash should be
	    if($gatewaymethod=='m') $myhash=md5($prehash);
	    else if($gatewaymethod=='s') $myhash=sha1($prehash);
	    else return false;

	    // Compare our hash to gateway's hash
	    if($myhash == $gatewayhash){
	       return true;
	    } else {
	       return false;
	    }

	    return false;

	}

	//* USAePay Multi-currency list

	public function get_currency_code( $currency_code ){
		
		$currencies = array(
				'AFA' => '971', // Afghan Afghani 
				'AWG' => '533', // Aruban Florin
				'AUD' => '036', // Australian Dollars 
				'ARS' => '032', // Argentine Peso 
				'AZN' => '944', // Azerbaijanian Manat
				'BSD' => '044', // Bahamian Dollar
				'BDT' => '050', // Bangladeshi Taka 
				'BBD' => '052', // Barbados Dollar 
				'BYR' => '974', // Belarussian Rouble 
				'BOB' => '068', // Bolivian Boliviano
				'BRL' => '986', // Brazilian Real
				'GBP' => '826', // British Pounds Sterling
				'BGN' => '975', // Bulgarian Lev
				'KHR' => '116', // Cambodia Riel
				'CAD' => '124', // Canadian Dollars
				'KYD' => '136', // Cayman Islands Dollar
				'CLP' => '152', // Chilean Peso
				'CNY' => '156', // Chinese Renminbi Yuan 
				'COP' => '170', // Colombian Peso
				'CRC' => '188', // Costa Rican Colon
				'HRK' => '191', // Croatia Kuna
				'CPY' => '196', // Cypriot Pounds 
				'CZK' => '203', // Czech Koruna
				'DKK' => '208', // Danish Krone
				'DOP' => '214', // Dominican Republic Peso
				'XCD' => '951', // East Caribbean Dollar
				'EGP' => '818', // Egyptian Pound
				'ERN' => '232', // Eritrean Nakfa
				'EEK' => '233', // Estonia Kroon 
				'EUR' => '978', // Euro 
				'GEL' => '981', // Georgian Lari
				'GHC' => '288', // Ghana Cedi
				'GIP' => '292', // Gibraltar Pound
				'GTQ' => '320', // Guatemala Quetzal 
				'HNL' => '340', // Honduras Lempira
				'HKD' => '344', // Hong Kong Dollars 
				'HUF' => '348', // Hungary Forint
				'ISK' => '352', // Icelandic Krona
				'INR' => '356', // Indian Rupee 
				'IDR' => '360', // Indonesia Rupiah 
				'ILS' => '376', // Israel Shekel 
				'JMD' => '388', // Jamaican Dollar
				'JPY' => '392', // Japanese yen 
				'KZT' => '368', // Kazakhstan Tenge 
				'KES' => '404', // Kenyan Shilling 
				'KWD' => '414', // Kuwaiti Dinar 
				'LVL' => '428', // Latvia Lat 
				'LBP' => '422', // Lebanese Pound 
				'LTL' => '440', // Lithuania Litas
				'MOP' => '446', // Macau Pataca
				'MKD' => '807', // Macedonian Denar
				'MGA' => '969', // Malagascy Ariary
				'MYR' => '458', // Malaysian Ringgit
				'MTL' => '470', // Maltese Lira 
				'BAM' => '977', // Marka 
				'MUR' => '480', // Mauritius Rupee
				'MXN' => '484', // Mexican Pesos
				'MZM' => '508', // Mozambique Metical
				'NPR' => '524', // Nepalese Rupee
				'ANG' => '532', // Netherlands Antilles Guilder
				'TWD' => '901', // New Taiwanese Dollars
				'NZD' => '554', // New Zealand Dollars
				'NIO' => '558', // Nicaragua Cordoba
				'NGN' => '566', // Nigeria Naira
				'KPW' => '408', // North Korean Won
				'NOK' => '578', // Norwegian Krone
				'OMR' => '512', // Omani Riyal
				'PKR' => '586', // Pakistani Rupee 
				'PYG' => '600', // Paraguay Guarani
				'PEN' => '604', // Peru New Sol
				'PHP' => '608', // Philippine Pesos
				'QAR' => '634', // Qatari Riyal
				'RON' => '946', // Romanian New Leu
				'RUB' => '643', // Russian Federation Ruble
				'SAR' => '682', // Saudi Riyal
				'CSD' => '891', // Serbian Dinar
				'SCR' => '690', // Seychelles Rupee
				'SGD' => '702', // Singapore Dollars
				'SKK' => '703', // Slovak Koruna
				'SIT' => '705', // Slovenia Tolar
				'ZAR' => '710', // South African Rand
				'KRW' => '410', // South Korean Won
				'LKR' => '144', // Sri Lankan Rupee
				'SRD' => '968', // Surinam Dollar
				'SEK' => '752', // Swedish Krona
				'CHF' => '756', // Swiss Francs
				'TZS' => '834', // Tanzanian Shilling
				'THB' => '764', // Thai Baht
				'TTD' => '780', // Trinidad and Tobago Dollar
				'TRY' => '949', // Turkish New Lira
				'AED' => '784', // UAE Dirham
				'USD' => '840', // US Dollars
				'UGX' => '800', // Ugandian Shilling
				'UAH' => '980', // Ukraine Hryvna
				'UYU' => '858', // Uruguayan Peso
				'UZS' => '860', // Uzbekistani Som
				'VEB' => '862', // Venezuela Bolivar
				'VND' => '704', // Vietnam Dong
				'AMK' => '894', // Zambian Kwacha
				'ZWD' => '716', // Zimbabwe Dollar

			);

		if( array_key_exists( $currency_code , $currencies) ){
			$code = $currencies[$currency_code];
		} else {
			$code = '';
		}

		return $code;
	}

	//* USAePay Card Name

	public function before_cc_form( $gateway_id ) {
		
		if ( $gateway_id !== $this->id ) {
			return;
		}

		if( $this->hide_card_name === true ) {
		 	return;
		}

		$customer_id = get_current_user_id();
		$customer_meta = get_user_meta( $customer_id );

		$fn = get_user_meta( $customer_id, 'billing_first_name', true );
		$ln = get_user_meta( $customer_id, 'billing_last_name', true );
			
		woocommerce_form_field( $this->id . '-card-name', array(
			'label'             => esc_html__( 'Name on Card', 'woocommerce' ),
			'required'          => true,
			'class'             => array( 'form-row-wide' ),
			'input_class'       => array( $this->id . '-card-name' ),
			'custom_attributes' => array(
				'autocomplete'  => 'off',
			),
			'default' 			=> sprintf( "%s %s", $fn, $ln),  
		) );
			
		
	}

	//* USAePay Refund Payment

	public function process_refund( $order_id, $amount = null, $reason = '' ){

		$order = wc_get_order( $order_id );
		$transaction_id = get_post_meta( $order_id, '_transaction_id', true );

		if( empty($transaction_id)){
			$transaction_id = get_post_meta( $order_id, '_'. $this->id .'_refnum', true ); 
		}
		
		if ( ! $transaction_id ) {
			return new WP_Error( $this->id . '_refund_error',
				 esc_html__( sprintf(
					'%s Credit Card Refund failed because the Transaction ID is missing.',
					ucwords($this->id)
				), 'woocommerce' )
			);
		}

		$params[ 'UMcommand' ] 		= 'refund';
		$params[ 'UMrefNum' ] 		= $transaction_id;
		$params[ 'UMamount' ] 		= $amount;
		$params[ 'UMinvoice' ]    	= $order->get_order_number();

		$response = $this->request($params);

		$data 		= wp_parse_args( $response['response_body'], $defaults );

		if(!empty($this->pin)){
			 $isvalid 	= $this->transaction_validator($data);
		} else {
			$isvalid = (bool) true;
		}

		if($data[ 'UMstatus' ] == 'Approved' && $isvalid === true ){

			$order->add_order_note(
		            esc_html__( sprintf(
		                '%1$s Refund %2$s with Transaction Id of %3$s',
		                $this->method_title,
		                sanitize_text_field($data['UMstatus']),
		                sanitize_text_field($data['UMrefNum'])
		            ), 'woocommerce' )
		        );

			return true;

		} else {

			$order->add_order_note(
		            esc_html__( sprintf(
		                '%1$s Refund %2$s with Error Code of %3$s',
		                $this->method_title,
		                sanitize_text_field($data['UMstatus']),
		                sanitize_text_field($data['UMerror']) .' - '. sanitize_text_field($data['UMerrorcode'])
		            ), 'woocommerce' )
		        );
			
			return new WP_Error( $this->id . '_refund_error', esc_html__( ucwords($this->method_title). ' ' . sanitize_text_field($data['UMerror'])  , sanitize_text_field($data['UMerrorcode']), 'woocommerce' ) );

		}
		
		return false;
		
	}

	//* USAePay Capture and Charge

	public function process_authonly_capture( $order_id ){
		

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		    return;
		}

		if ( ! current_user_can( 'edit_post', $order_id ) ) {
		    return;
		}

		$charge_nonce = !empty($_POST['usaepaytransapi-capture-charge-nonce']) ? $_POST['usaepaytransapi-capture-charge-nonce'] : '';

		if ( ! isset( $charge_nonce ) && ! wp_verify_nonce( $charge_nonce ) ) {
		    return;
		}

		$command 		= get_post_meta( $order_id, '_usaepaytransapi_command', true );
		$transaction_id = get_post_meta( $order_id, '_transaction_id', true );
		$order 			= wc_get_order( $order_id );

		if( empty($transaction_id)){
			$transaction_id = get_post_meta( $order_id, '_'. $this->id .'_refnum', true ); 
		}


		if ( ! $transaction_id ) {
			return new WP_Error( $this->id . '_capture_error',
				esc_html__( sprintf(
					'%s Capture Charge failed because the Transaction ID is missing.',
					ucwords($this->id)
				), 'woocommerce' )
			);
		}
		

		if( ! empty($_POST[ 'usaepaytransapi_capture_charge' ]) && $command == 'cc:authonly' && !empty($transaction_id) ){

			$params[ "UMcommand" ] 		= 'cc:capture'; 
			$params[ 'UMrefNum' ] 		= $transaction_id;
			$params[ 'UMinvoice' ]    	= $order->get_order_number();

			$response = $this->request($params);

			$data 		= wp_parse_args( $response['response_body'], $defaults );

			if(!empty($this->pin)){
			 $isvalid 	= $this->transaction_validator($data);
			} else {
				$isvalid = (bool) true;
			}

			if($data[ 'UMstatus' ] == 'Approved' && $isvalid === true ){

				$order->update_status( 'processing' );

				update_post_meta( $order_id, '_'. $this->id .'_refnum', sanitize_text_field($data[ 'UMrefNum' ]) );

				$order->add_order_note(
			            esc_html__( sprintf(
			                '%1$s Capture Charge %2$s with Transaction Id of %3$s',
			                $this->method_title,
			                sanitize_text_field($data['UMstatus']),
			                sanitize_text_field($data['UMrefNum'])
			            ), 'woocommerce' )
			        );

				return true;

			} else {

				$order->update_status( 'failed' );
				$order->add_order_note(
			            esc_html__( sprintf(
			                '%1$s Capture Charge %2$s with Error Code of %3$s',
			                $this->method_title,
			                sanitize_text_field($data['UMstatus']),
			                sanitize_text_field($data['UMerror']) .' - '. sanitize_text_field($data['UMerrorcode'])
			            ), 'woocommerce' )
			        );
				
				return new WP_Error( $this->id . '_capture_error', esc_html__( ucwords($this->method_title). ' ' . sanitize_text_field($data['UMerror'])  , sanitize_text_field($data['UMerrorcode']),'woocommerce' ) );

			}
			
			return false;

		}

	}

	//* reauth + capture

	public function process_authonly_reauth_capture( $order_id ){

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		    return;
		}

		if ( ! current_user_can( 'edit_post', $order_id ) ) {
		    return;
		}

		$charge_nonce = !empty($_POST['usaepaytransapi-capture-reauth-nonce']) ? $_POST['usaepaytransapi-capture-reauth-nonce'] : '';

		if ( ! isset( $charge_nonce ) && ! wp_verify_nonce( $charge_nonce ) ) {
		    return;
		}

		$command 		= get_post_meta( $order_id, '_usaepaytransapi_command', true );
		$transaction_id = get_post_meta( $order_id, '_transaction_id', true );
		$card_token     = get_post_meta( $order_id, '_usaepaytransapi_cardref', true );
		$order 			= wc_get_order( $order_id );

		if( empty($transaction_id)){
			$transaction_id = get_post_meta( $order_id, '_'. $this->id .'_refnum', true ); 
		}

		if(empty($card_token)){

			return new WP_Error( $this->id . '_capture_error',
				esc_html__( sprintf(
					'%s Re-Auth + Capture Charge failed because the Card Token is missing.',
					ucwords($this->id)
				), 'woocommerce' )
			);

		}


		if ( ! $transaction_id ) {
			return new WP_Error( $this->id . '_capture_error',
				esc_html__( sprintf(
					'%s Capture Charge failed because the Transaction ID is missing.',
					ucwords($this->id)
				), 'woocommerce' )
			);
		}

		//* void or cancel old transaction ID
		//-----------------------------------------------------------------------

		$void[ "UMcommand" ] 		= 'cc:void'; 
		$void[ 'UMrefNum' ] 		= $transaction_id;

		$response = $this->request($void);
		$data 	  = wp_parse_args( $response['response_body'], $defaults );
		
		if($data[ 'UMstatus' ] == 'Approved' ){

			$order->add_order_note(
		            esc_html__( sprintf(
		                '%1$s - Cancel %2$s with Transaction Id of %3$s',
		                $this->method_title,
		                sanitize_text_field( $data[ 'UMerror' ]),
		                sanitize_text_field($data['UMrefNum'])
		            ), 'woocommerce' )
		        );

		} else {

			$order->add_order_note(
		            esc_html__( sprintf(
		                '%1$s - Cancel %2$s with Transaction Id of %3$s',
		                $this->method_title,
		                sanitize_text_field( $data[ 'UMerror' ]),
		                sanitize_text_field($data['UMrefNum'])
		            ), 'woocommerce' )
		        );
			
		}

		if( $this->onedollar_auth === true ){

			$order->add_order_note(
	            esc_html__( sprintf(
	                "%s - %s%s amount void/cancel",
	                $this->method_title,
	                get_woocommerce_currency_symbol(),
	                "1.00"
	            ), 'woocommerce' )
	        );

		} 


		//* re-auth 
		//-----------------------------------------------------------------------

		$reauth[ "UMcommand" ] 		= $this->paymentaction; 
		$reauth[ "UMcard" ] 	 	= sanitize_text_field($card_token); //
	    $reauth[ "UMexpir" ] 	 	= "0000";
	
		$reauth[ "UMamount" ]  		= $order->get_total();

		$order->add_order_note(
            esc_html__( sprintf(
                "%s - %s%s re-auth amount ",
                $this->method_title,
                get_woocommerce_currency_symbol(),
                $reauth[ "UMamount" ]
            ), 'woocommerce' )
        );

	    $woo_currency 				= get_woocommerce_currency();
	    $currency_code 				= $this->get_currency_code($woo_currency);
	    $reauth[ "UMcurrency" ] 	= $currency_code;
	    $reauth[ "UMinvoice" ] 		= $order->get_order_number();
	    $reauth[ "UMorderid" ] 		= $order_id;
	    $reauth[ "UMdescription" ] 	= sanitize_text_field(sprintf( "WC ORDER #%s", $order->get_order_number()));

	    $reauth[ "UMname" ] 		= esc_html__( $order->get_billing_first_name(), 'woocommerce' ) . " ".  esc_html__( $order->get_billing_last_name(), 'woocommerce' ) ;
	    $reauth[ "UMstreet" ] 		= esc_html__( $order->get_billing_address_1() .' '. $order->get_billing_address_2(), 'woocommerce' );
	    $reauth[ "UMzip" ] 			= $order->get_billing_postcode();

	    //* Billing Address Fields

	    $reauth[ "UMbillfname" ] 	= esc_html__( $order->get_billing_first_name(), 'woocommerce' );
	    $reauth[ "UMbilllname" ] 	= esc_html__( $order->get_billing_last_name(), 'woocommerce' );
	    $reauth[ "UMbillcompany" ] 	= esc_html__( $order->get_billing_company(), 'woocommerce' );
	    $reauth[ "UMbillstreet" ] 	= esc_html__( $order->get_billing_address_1(), 'woocommerce' );
	    $reauth[ "UMbillstreet2" ] 	= esc_html__( $order->get_billing_address_2(), 'woocommerce' );
	    $reauth[ "UMbillcity" ] 	= esc_html__( $order->get_billing_city(), 'woocommerce' );
	    $reauth[ "UMbillstate" ] 	= esc_html__( $order->get_billing_state(), 'woocommerce' );
	    $reauth[ "UMbillzip" ] 		= $order->get_billing_postcode();
	    $reauth[ "UMbillcountry" ] 	= esc_html__( $order->get_billing_country(), 'woocommerce');
	    $reauth[ "UMbillphone" ] 	= $order->get_billing_phone();
	    $reauth[ "UMemail" ] 		= $order->get_billing_email();
	    
	     //* Shipping Address Fields

	    $reauth[ "UMshipfname" ] 	= esc_html__( $order->get_shipping_first_name(), 'woocommerce' );
	    $reauth[ "UMshiplname" ] 	= esc_html__( $order->get_shipping_last_name(), 'woocommerce' );
	    $reauth[ "UMshipcompany" ] 	= esc_html__( $order->get_shipping_company(), 'woocommerce' );
	    $reauth[ "UMshipstreet" ] 	= esc_html__( $order->get_shipping_address_1(), 'woocommerce' );
	    $reauth[ "UMshipsreet2" ] 	= esc_html__( $order->get_shipping_address_2(), 'woocommerce' );
	    $reauth[ "UMshipcity" ] 	= esc_html__( $order->get_shipping_city(), 'woocommerce' );
	    $reauth[ "UMshipstate" ] 	= esc_html__( $order->get_shipping_state(), 'woocommerce' );
	    $reauth[ "UMshipzip" ] 		= $order->get_shipping_postcode();
	    $reauth[ "UMshipcountry" ] 	= esc_html__( $order->get_shipping_country(), 'woocommerce' );
	    $reauth[ "UMshipphone" ] 	= $order->get_billing_phone();

	    $reauth[ 'UMsaveCard' ] = "false";
	    

	    //* Testmode
		if( $this->testmode == 'yes' ){
			$reauth[ "UMtestmode" ] = 1; 	
		} else {
			$reauth[ "UMtestmode" ] = 0;
		}

		$reauth["UMisRecurring"] = "false";

		$response = $this->request($reauth);
		$data 		= wp_parse_args( $response['response_body'], $defaults );

		$UMrefNum = sanitize_text_field($data[ 'UMrefNum' ]);

		update_post_meta( $order_id, '_'. $this->id .'_refnum', $UMrefNum );

		if($data[ 'UMstatus' ] == 'Approved' ){

			$order->add_order_note(
		            esc_html__( sprintf(
		                '%1$s - Re-Auth %2$s with Transaction Id of %3$s',
		                $this->method_title,
		                sanitize_text_field( $data[ 'UMerror' ]),
		                sanitize_text_field($UMrefNum)
		            ), 'woocommerce' )
		        );

		} else {

			$order->add_order_note(
		            esc_html__( sprintf(
		                '%1$s - Re-Auth %2$s with Transaction Id of %3$s',
		                $this->method_title,
		                sanitize_text_field( $data[ 'UMerror' ]),
		                sanitize_text_field($UMrefNum)
		            ), 'woocommerce' )
		        );
			
		}
		

		//* capture new
		//-----------------------------------------------------------------------

		if( ! empty($_POST[ 'usaepaytransapi_capture_reauth' ]) && $command == 'cc:authonly' && !empty($UMrefNum) ){

			$params[ "UMcommand" ] 		= 'cc:capture'; 
			$params[ 'UMrefNum' ] 		= $UMrefNum;
			$params[ 'UMinvoice' ]    	= $order->get_order_number();

			$response = $this->request($params);

			$data 		= wp_parse_args( $response['response_body'], $defaults );

			if(!empty($this->pin)){
			 $isvalid 	= $this->transaction_validator($data);
			} else {
				$isvalid = (bool) true;
			}

			if($data[ 'UMstatus' ] == 'Approved' && $isvalid === true ){

				$order->update_status( 'processing' );

				update_post_meta( $order_id, '_'. $this->id .'_refnum', sanitize_text_field($data[ 'UMrefNum' ]) );

				$order->add_order_note(
			            esc_html__( sprintf(
			                '%1$s Capture Charge %2$s with Transaction Id of %3$s',
			                $this->method_title,
			                sanitize_text_field($data['UMstatus']),
			                sanitize_text_field($data['UMrefNum'])
			            ), 'woocommerce' )
			        );


				$order->add_order_note(
		            esc_html__( sprintf(
		                "%s - %s%s new capture charge amount ",
		                $this->method_title,
		                get_woocommerce_currency_symbol(),
		                $reauth[ "UMamount" ]
		            ), 'woocommerce' )
		        );

				return true;

			} else {

				$order->update_status( 'failed' );
				$order->add_order_note(
			            esc_html__( sprintf(
			                '%1$s Capture Charge %2$s with Error Code of %3$s',
			                $this->method_title,
			                sanitize_text_field($data['UMstatus']),
			                sanitize_text_field($data['UMerror']) .' - '. sanitize_text_field($data['UMerrorcode'])
			            ), 'woocommerce' )
			        );
				
				return new WP_Error( $this->id . '_capture_error', esc_html__( ucwords($this->method_title). ' ' . sanitize_text_field($data['UMerror'])  , sanitize_text_field($data['UMerrorcode']),'woocommerce' ) );

			}
			
			return false;

		}
		
	}

	//* add payment card 

	public function add_payment_method() {
		
		$current_user   = wp_get_current_user();
		$customer_info = get_user_meta( $current_user->ID );

		if( !empty($_POST['payment_method']) && $_POST['payment_method'] == 'usaepaytransapi' && $_POST[ 'woocommerce_add_payment_method' ] == 1 ){

			$params[ "UMcard" ] 	 	= sanitize_text_field($_REQUEST[ 'usaepaytransapi-cardnumber' ]); //'4761530001111118';
			$expiry_m 					= strlen(absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]));
			$expiry_m                   = ($expiry_m <= 1 ) ? "0".absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]) : absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]);
			$expiry_y 					= substr( absint($_REQUEST[ 'usaepaytransapi-expiry_y' ]), -2);
		    $params[ "UMexpir" ] 	 	= $expiry_m.$expiry_y;

			$params[ "UMbillfname" ] 	= esc_html__( $customer_info[ 'billing_first_name' ][0], 'woocommerce' );
			$params[ "UMbilllname" ] 	= esc_html__( $customer_info[ 'billing_last_name' ][0], 'woocommerce' );
			$params[ "UMbillcompany" ] 	= esc_html__( $customer_info[ 'billing_company' ][0], 'woocommerce' );
			$params[ "UMbillstreet" ] 	= esc_html__( $customer_info[ 'billing_address_1' ][0], 'woocommerce' );
			$params[ "UMbillstreet2" ] 	= esc_html__( $customer_info[ 'billing_address_2' ][0], 'woocommerce' );
			$params[ "UMbillcity" ] 	= esc_html__( $customer_info[ 'billing_city' ][0], 'woocommerce' );
			$params[ "UMbillstate" ] 	= esc_html__( $customer_info[ 'billing_state' ][0], 'woocommerce' );
			$params[ "UMbillzip" ] 		= esc_html__( $customer_info[ 'billing_postcode' ][0], 'woocommerce' );
			$params[ "UMbillcountry" ] 	= esc_html__( $customer_info[ 'billing_country' ][0], 'woocommerce' );
			$params[ "UMbillphone" ] 	= esc_html__( $customer_info[ 'billing_phone' ][0], 'woocommerce' );
			$params[ "UMemail" ] 		= esc_html__( $customer_info[ 'billing_email' ][0], 'woocommerce' );
			$params[ 'UMsaveCard' ] 	= "true";
			$params[ "UMcommand" ] 		= "cc:save";
			
			$result   = $this->request( $params );

			if( $result['response_code'] == 200 && $result[ 'response_message' ] == 'OK' && ! empty($result['response_body'])){

			$data 		= wp_parse_args( $result['response_body'], $defaults );

			if($data[ 'UMstatus' ] == 'Approved' && !empty($data[ 'UMcardRef' ]) ){

		        $card_token = array();

		        //* Save token
				$card_token[ 'token' ] 				= sanitize_text_field($data[ 'UMcardRef' ]);
		        $card_token[ 'card_type' ] 			= sanitize_text_field($data[ 'UMcardType' ]);
		        $card_token[ 'set_last4' ]			= substr(sanitize_text_field($data[ 'UMmaskedCardNum' ]), -4 );
		        $card_token[ 'set_expiry_month' ]	= $expiry_m;
		        $card_token[ 'set_expiry_year' ]	= absint($_REQUEST[ 'usaepaytransapi-expiry_y' ]);
		        
		        //* Fire save card token
		        $this->save_token($card_token);
		       
			}

		} else {

			wc_add_notice( esc_html__( 'There was a problem adding this card.', 'woocommerce' ), 'error' );
	        return;

		}
	}

	    return array(
	      'result'   => 'success',
	      'redirect' => wc_get_endpoint_url( 'payment-methods' ),
	    );
	}

	//* Display USAePay Capture Charge Button

	public function display_capture_button( $order ){

		$command = get_post_meta( $order->get_id(), '_usaepaytransapi_command', true );
		$order_data = $order->get_data();
		$order_id  = $order->get_id();

		if( $this->reauth === false && $order_data['status'] == 'on-hold'  && $order->get_payment_method() == $this->id  && $command == 'cc:authonly' ){
			echo '<input type="submit" class="button add-items" value="'.esc_attr__('Capture', 'woocommerce' ).'" name="usaepaytransapi_capture_charge" />';
			wp_nonce_field( 'usaepaytransapi-capture-charge-save', 'usaepaytransapi-capture-charge-nonce' );
		}

	}

	//* Display re-auth + capture

	public function display_reauth_capture_button( $order ){

		$command = get_post_meta( $order->get_id(), '_usaepaytransapi_command', true );
		$order_data = $order->get_data();
		$order_id  = $order->get_id();

		if( $this->reauth === true && $order_data['status'] == 'on-hold'  && $order->get_payment_method() == $this->id  && $command == 'cc:authonly' ){
			echo '<input type="submit" class="button add-items" value="Re-auth + Capture" name="usaepaytransapi_capture_reauth" />';
			wp_nonce_field( 'usaepaytransapi-capture-reauth-save', 'usaepaytransapi-capture-reauth-nonce' );
		}

		if( $this->o_subtotal === true && $this->reauth === true && $order_data['status'] == 'on-hold'  && $order->get_payment_method() == $this->id  && $command == 'cc:authonly' ){

			$coupon = $order->get_coupon_codes();

			if( !empty($coupon)){
				$coupon = $coupon[0];
			} else {
				$coupon = '';
			}

			echo '<input type="button" class="button add-items usaepaytransapi_capture_subtotal_btn" value="Input Subtotal &uarr;" name="usaepaytransapi_capture_subtotal" data-order-id="'.$order_id.'" data-item-id="'.$this->o_product.'" data-osubtotal="'.$this->o_subtotal.'"/>';
			echo '<input type="hidden" class="usaepaytransapi_coupon_code" value="'.esc_attr($coupon).'" name="usaepaytransapi_coupon_code" id="usaepaytransapi_coupon_code" />';
			wp_nonce_field( 'usaepaytransapi-capture-subtotal-save', 'usaepaytransapi-capture-subtotal-nonce' );
		}

	}

	//* Credit Card form script 

	public function script(){
		if(is_checkout() || is_wc_endpoint_url( 'add-payment-method' ) ){
			wp_enqueue_script( RPS_WC_USAEPAY .  '-gateway', plugins_url( 'assets/js/usaepaytransapi.min.js', __FILE__ ), array( 'jquery', 'wc-credit-card-form' ), WC_VERSION, true );
		}
	}

	//* USAePay admin script

	public function admin_script(){
		if(is_admin()){
			wp_enqueue_script( RPS_WC_USAEPAY . '-admin-js', plugins_url( 'assets/js/admin-usaepaytransapi.min.js' , __FILE__ ), array( 'jquery'));
		}
	}

	public static function order_item_script(){
		
		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'edit-shop_order', 'shop_order' ) ) ) {
			wp_enqueue_script( RPS_WC_USAEPAY .'_order_items_usaepay', plugins_url( '/assets/js/admin-item-order.min.js' , __FILE__ )  );
			
			$order_line_totals = array(
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'new_subtotal_nonce'      => wp_create_nonce( 'new-subtotal' )
			 );
	
			wp_localize_script( RPS_WC_USAEPAY . '_order_items_usaepay', 'order_line_totals', $order_line_totals );
		}
	}

	public function custom_css(){
		 if( $this->hide_save_card === true  && is_checkout() === true && is_wc_endpoint_url( 'order-received' ) === false && is_wc_endpoint_url( 'order-pay' ) === false ) {

		    ?>  <style type="text/css">
		            #wc-<?php echo $this->id; ?>-new-payment-method { display: none !important; }
		            .woocommerce-SavedPaymentMethods-saveNew { display: none !important; }
		            .woocommerce-SavedPaymentMethods { display: none !important; }
		        </style>
		        <script type="text/javascript">
		          (function ( $ ) {
		              $( function () {
		                  $( "#wc-<?php echo $this->id; ?>-payment-token-new" ).click();
		              });
		           }( jQuery ) );
		        </script>
		    <?php

		    }
	}

	public function admin_order_sub_total( $order_id ){

		$order = wc_get_order( $order_id );

		if( $this->reauth === true && $order->get_payment_method() == $this->id ){

			?><tr>
				<td class="label">Subtotal:</td>
				<td width="1%"></td>
				<td class="total usaepaytransapi_subtotal"><?php echo wc_price($order->get_subtotal());?></td>
				</tr><?php

		}

	}

	public static function add_new_subtotal(){

		check_ajax_referer( 'new-subtotal', 'security' );

		$order_id 	=  isset( $_POST['orderId'] ) ? esc_attr( $_POST['orderId'] ) : null;
		$item_id 	=  isset( $_POST['itemId'] )  ? esc_attr( $_POST['itemId'] )  : null;
		$osubtotal  = isset( $_POST['osubtotal'] )  ? esc_attr( $_POST['osubtotal'] )  : null;


		if( is_admin() === true && !empty($item_id) && !empty($order_id) && $osubtotal == 1 ){

			$order 	= wc_get_order( $order_id );
			$order_data = $order->get_data();
			//$items = $order->get_items(); 

			$exist_item = array();
			foreach ( $order->get_items() as $k => $item ) {
			    // Compatibility for woocommerce 3+
			    $product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $item['product_id'] : $item->get_product_id();
			    if( $product_id == $item_id ){
			    	$exist_item[$product_id] = $product_id;
			    }
			}

			if( ! array_key_exists($item_id, $exist_item)){

				$_product = get_product( $item_id );
				// Set values
				$item = array();

				$item['product_id'] 			= $_product->id;
				$item['variation_id'] 			= isset( $_product->variation_id ) ? $_product->variation_id : '';
				$item['name'] 					= $_product->get_title();
				$item['tax_class']				= $_product->get_tax_class();
				$item['qty'] 					= 1;
				$item['line_subtotal'] 			= number_format( (double) $_product->get_price_excluding_tax(), 2, '.', '' );
				$item['line_subtotal_tax'] 		= '';
				$item['line_total'] 			= number_format( (double) $_product->get_price_excluding_tax(), 2, '.', '' );
				$item['line_tax'] 				= '';

				// Add line item
			   	$item_id = woocommerce_add_order_item( $order->get_id(), array(
			 		'order_item_name' 		=> $item['name'],
			 		'order_item_type' 		=> 'line_item'
			 	) );

			 	// Add line item meta
			 	if ( $item_id ) {
				 	woocommerce_add_order_item_meta( $item_id, '_qty', $item['qty'] );
				 	woocommerce_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
				 	woocommerce_add_order_item_meta( $item_id, '_product_id', $item['product_id'] );
				 	woocommerce_add_order_item_meta( $item_id, '_variation_id', $item['variation_id'] );
				 	woocommerce_add_order_item_meta( $item_id, '_line_subtotal', $item['line_subtotal'] );
				 	woocommerce_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['line_subtotal_tax'] );
				 	woocommerce_add_order_item_meta( $item_id, '_line_total', $item['line_total'] );
				 	woocommerce_add_order_item_meta( $item_id, '_line_tax', $item['line_tax'] );
			 	}
				
			}
			
		}
	}

	public function autocapture_payment_complete_order_status( $order_id ){

		$order 			= wc_get_order( $order_id );

		if(empty($order)){
			return;
		} 
		
		$order_data     = $order->get_data();
		$order_status   = $order->get_status();

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		    return;
		}

		if ( ! current_user_can( 'edit_post', $order_id ) ) {
		    return;
		}
		
		if ( 'completed' == $order_status && $this->paymentaction == 'cc:authonly' ){
			$this->process_auto_capture($order_id);

		}

	}

	public function process_auto_capture( $order_id ){

		$command 		= get_post_meta( $order_id, '_usaepaytransapi_command', true );
		$transaction_id = get_post_meta( $order_id, '_transaction_id', true );
		$order 			= wc_get_order( $order_id );

		if( empty($transaction_id)){
			$transaction_id = get_post_meta( $order_id, '_'. $this->id .'_refnum', true ); 
		}

		if ( ! $transaction_id ) {

			$order->add_order_note(
			            esc_html__( sprintf(
							'%s Capture Charge failed because the Transaction ID is missing.',
							ucwords($this->id)
						), 'woocommerce' )
			        );

			return;
		}

		if( $command == 'cc:authonly' && !empty($transaction_id) ){

			$params[ "UMcommand" ] 		= 'cc:capture'; 
			$params[ 'UMrefNum' ] 		= $transaction_id;
			$params[ 'UMinvoice' ]    	= $order->get_order_number();

			$response = $this->request($params);

			$data 		= wp_parse_args( $response['response_body'], $defaults );
			$isvalid 	= $this->transaction_validator($data);

			if($data[ 'UMstatus' ] == 'Approved' && $isvalid === true ){

				update_post_meta( $order_id, '_'. $this->id .'_refnum', sanitize_text_field($data[ 'UMrefNum' ]) );

				$order->add_order_note(
			            esc_html__( sprintf(
			                '%1$s Capture Charge %2$s with Transaction Id of %3$s',
			                $this->method_title,
			                sanitize_text_field($data['UMstatus']),
			                sanitize_text_field($data['UMrefNum'])
			            ), 'woocommerce' )
			        );

				return true;

			} else {

				$order->update_status( 'failed' );

				$order->add_order_note(
			            esc_html__( sprintf(
			                '%1$s Capture Charge %2$s with Error Code of %3$s',
			                $this->method_title,
			                sanitize_text_field($data['UMstatus']),
			                sanitize_text_field($data['UMerror']) .' - '. sanitize_text_field($data['UMerrorcode'])
			            ), 'woocommerce' )
			        );

			}
			
			return false;

		}

	} 

}

function rps_wc_usaepay_add_payment_method( $methods ) {

	if( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' )){
		$methods[] = 'USAePay_TRANS_WC_API_WC_API_Addons';
	} else {
		$methods[] = 'USAePay_TRANS_WC_API';
	}

	return $methods;
}

add_filter( 'woocommerce_payment_gateways', RPS_WC_USAEPAY . '_add_payment_method' );
