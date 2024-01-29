<?php
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce USAePay Tranaction API Addons - Payment Gateway
 * 
 * @author Ryan IT Solutions
 * @version since 1.13.8
 */

class USAePay_TRANS_WC_API_WC_API_Addons extends USAePay_TRANS_WC_API {
	
	function __construct(){
		 parent::__construct();

		 if ( class_exists( 'WC_Subscriptions_Order' ) ) {

	      add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
	      add_action( 'woocommerce_subscription_failing_payment_method_updated_'.$this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

	      add_action( 'wcs_resubscribe_order_created', array( $this, 'delete_resubscribe_meta' ), 10 );

	      add_filter( 'woocommerce_subscription_payment_meta', array( $this, 'add_subscription_payment_meta' ), 10, 2 );
	      add_filter( 'woocommerce_subscription_validate_payment_meta', array( $this, 'validate_subscription_payment_meta' ), 10, 2 );

	    }
	    
	}

  	//* USAePay Payments process addon payment

	public function process_payment( $order_id ){

		
	
		global $woocommerce;
		
		$order  = wc_get_order( $order_id );

		$params[ "UMcommand" ] 		= $this->paymentaction; 
		$params[ "UMcard" ] 	 	= sanitize_text_field($_REQUEST[ 'usaepaytransapi-cardnumber' ]); //'4761530001111118';
		$expiry_m 					= strlen(absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]));
		$expiry_m                   = ($expiry_m <= 1 ) ? "0".absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]) : absint($_REQUEST[ 'usaepaytransapi-expiry_m' ]);
		$expiry_y 					= substr( absint($_REQUEST[ 'usaepaytransapi-expiry_y' ]), -2);
	    $params[ "UMexpir" ] 	 	= $expiry_m.$expiry_y;

	    $token_id 					= sanitize_text_field($_REQUEST[ 'wc-usaepaytransapi-payment-token' ]);

		if( !empty($token_id)){
			$token = $this->use_card_token($token_id);

			if( !empty($token) && is_array($token)){

				$params[ "UMcard" ] 	 = $token[ "UMcard" ];
				$params[ "UMexpir" ] 	 =  $token[ "UMexpir" ];
			}
		}

		
	    $params[ "UMcvv2" ] 		= sanitize_text_field($_REQUEST[ 'usaepaytransapi-card_cvc' ]);
	    //$params[ "UMamount" ]  		= $order->get_total();

	    if( $this->onedollar_auth === true ){
			$params[ "UMamount" ]  		= "1.00";
		} else {
			$params[ "UMamount" ]  		= $order->get_total();	
		}

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
	    	$params[ "UMcommand" ] = "cc:sale";
	    } else {
	    	$params[ 'UMsaveCard' ] = "false";
	    }

	    $change_payment_method = !empty($_REQUEST['change_payment_method']) ? $_REQUEST['change_payment_method'] : '';
	    $pay_for_order = (bool) $_REQUEST['pay_for_order'] === true ? true : false;

	    if( !empty($change_payment_method) && $pay_for_order === true && !empty($params['UMcard']) ){

	    	
			$card_token = $params['UMcard'];

	    	update_post_meta( $order->get_id(), '_'.$this->id.'_customer_token', $card_token );

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

				} 

	    } 
               
	    if(  $save_card === false && !is_numeric($token_id) && ( $this->order_contains_subscription( $order_id ) || ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ) ) ) ){
    		
	    		wc_add_notice( esc_html__( sprintf( "Payment Failed with message: '%s'", "Please save your card for subscription orders" ), 'woocommerce' ), "error");
		        return false;
				
    	} 

	    //* Testmode
		if( $this->testmode == 'yes' ){
			$params[ "UMtestmode" ] = 1; 	
		} else {
			$params[ "UMtestmode" ] = 0;
		}

		$params["UMisRecurring"] = "true";


	    //* Fire payment
		$result = parent::request( $params );

		if( $result['response_code'] == 200 && $result[ 'response_message' ] == 'OK' && ! empty($result['response_body'])){

			$data 		= wp_parse_args( $result['response_body'], $defaults );

			if(!empty($this->pin)){
				$isvalid 	= parent::transaction_validator($data);
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

				$card_token = sanitize_text_field($data[ 'UMcardRef' ]);
				update_post_meta( $order_id, '_' .$this->id. '_refnum', sanitize_text_field($data[ 'UMrefNum' ]) );

				if(  ( $this->order_contains_subscription( $order->id ) || ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ) ) ) && empty($card_token) ){
					wc_add_notice( 'Invalid USAePay Payment Token, Subscriptions is not Activated' , 'woocommerce');
				}
				
				 // Processing subscription
			    if ( ( $this->order_contains_subscription( $order->id ) || ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ) ) ) ) {
			    	// Set the subscriptions

			    	if( empty($card_token) && ! empty($params[ "UMcard" ])) {
			    		$card_token = $params[ "UMcard" ];
			    	} 

			      	$this->process_subscription( $order, $card_token  );
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

	
	//* process subscription

	public function process_subscription( $order,  $customer_token = ''   ){

		  WC_Subscriptions_Manager::activate_subscriptions_for_order( $order );

		  if ( $customer_token )
		    $this->save_subscription_meta( $order->id, $customer_token );

		    $redirect = $this->get_return_url($order);

		    return array(
		        'result' => 'success',
		        'redirect' => $redirect
		    );
	}
	
	//* scheduled subscription payment

	public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ){
		$result = $this->process_subscription_payment( $renewal_order, $amount_to_charge );
		if ( is_wp_error( $result ) ) {
		  $renewal_order->update_status(
		    'failed',
		    sprintf( esc_html__( 'USAePay Transaction Failed (%s)', 'woocommerce' ),
		    $result->get_error_message() )
		  );
		}
	}

	
	//* Process Subscription

	public function process_subscription_payment( $order = '', $amount = 0 ){
		global $woocommerce;

		if ( 0 == $amount ) {
		  // Payment complete
		  $order->payment_complete();
		  return true;
		}

		$ip_address = ! empty( $_SERVER['HTTP_X_FORWARD_FOR'] ) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
		
		$customer_token = get_post_meta( $order->id, '_' . $this->id . '_customer_token', true );

		self::log( 'card_token: ' . print_r( "************". substr($customer_token,-4), true ) );
		self::log( 'order: ' . print_r( $order->id, true ) );

		if ( ! $customer_token )
		  return new WP_Error( 'error', esc_html__( 'Customer token is missing.', 'woocommerce' ) );

		$currency = get_post_meta( $order->id,'_order_currency',true);

		if (!$currency || empty($currency)) $currency = get_woocommerce_currency();

			//Subscription payment here 

			$params[ "UMcommand" ] 		= $this->paymentaction; 
			
			 //* Testmode
			if( $this->testmode == 'yes' ){
				$params[ "UMtestmode" ] = 1; 	
			} else {
				$params[ "UMtestmode" ] = 0;
			}

			//$params[ "UMcommand" ] 		= $this->paymentaction; 
			$params[ "UMcard" ] 	 	= $customer_token;
			$params[ "UMexpir" ] 	 	= '0000';
		    //$params[ "UMcvv2" ] 		= '';
		    $params[ "UMamount" ]  		= $order->get_total();

		    $currency_code 				= $this->get_currency_code($currency);
		    $params[ "UMcurrency" ] 	= $currency_code;
		    $params[ "UMinvoice" ] 		= $order->get_order_number();
		    $params[ "UMorderid" ] 		= $order->get_id();
		    $params[ "UMdescription" ] 	= sanitize_text_field(sprintf( "WC ORDER #%s", $order->get_order_number()));

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
		    $params[ "UMisRecurring" ] 	= "true";

			$result = parent::request($params);

			if( $result['response_code'] == 200 && $result[ 'response_message' ] == 'OK' && ! empty($result['response_body'])){

				$data 		= wp_parse_args( $result['response_body'], $defaults );
				
				if(!empty($this->pin)){
					$isvalid 	= parent::transaction_validator($data);
				} else {
					$isvalid = (bool) true;
				}

				$order_id   = $order->get_id();

				if($data[ 'UMstatus' ] == 'Approved' && $isvalid === true ){

					$order->payment_complete( sanitize_text_field($data[ 'UMrefNum' ]) );
					$order_status = parent::virtual_order_payment_complete_order_status( $order_id );

					if( $this->default_status == 'default' ){
						if($this->paymentaction == 'cc:authonly' ){
							$order->update_status( 'on-hold' );
						} else {
							$order->update_status( $order_status );						
						}
					} else {
						$order->update_status( $this->default_status );
					}	
					
					update_post_meta( $order->get_id(), '_'. $this->id .'_refnum', sanitize_text_field($data[ 'UMrefNum' ]) );
					update_post_meta( $order->get_id(), '_transaction_id', sanitize_text_field($data[ 'UMrefNum' ]) );
					update_post_meta( $order->get_id(), '_usaepaytransapi_command', $this->paymentaction );
					$order->add_order_note(sprintf(esc_html__('USAePay Transaction API subscription payment completed (Reference Number: %s)','woocommerce'),sanitize_text_field($data[ 'UMrefNum' ])  ));

					return true;

				} else {

					$order->update_status( 'failed' );
					$order->add_order_note(
			            esc_html__( sprintf(
			                "%s Payment Failed with message: '%s'",
			                $this->method_title,
			                sanitize_text_field($data['UMerror']) 
			            ), 'woocommerce' )
			        );

			        return new WP_Error( 'error', sprintf(esc_html__('USAePay error: %s','woocommerce'), $data['UMerror'] ) );
				} 

			} else {

				return new WP_Error( 'error', sprintf(esc_html__('USAePay error: %s','woocommerce'), $result['response_body']->error_code .' - '. $result['response_body']->error ) );
			}
		
	}

	
	//* add customer to order     

	public function add_customer_to_order( $order, $customer_token = false ){
		if ( $customer_token ) {
		  $this->save_subscription_meta( $order->id, $customer_token );
		  return $result[ 'code' ] == true;
		} else {
		  return $result[ 'code' ] == false;
		}
	}

	
	//* save subscription meta 

	protected function save_subscription_meta( $order_id, $customer_token ){
		$customer_token = wc_clean( $customer_token );
		update_post_meta( $order_id, '_' . $this->id . '_customer_token', $customer_token );

		foreach( wcs_get_subscriptions_for_order( $order_id ) as $subscription ) {
		  update_post_meta( $subscription->id, '_' . $this->id . '_customer_token', $customer_token );
		}
	}

	
	//* add subscription payment meta

	public function add_subscription_payment_meta( $payment_meta, $subscription ){
		$payment_meta[ $this->id ] = array(
		  'post_meta' => array(
		    '_' . $this->id . '_customer_token' => array(
		      'value' => get_post_meta( $subscription->id, '_'.$this->id.'_customer_token', true ),
		      'label' => 'USAePay Customer Token',
		    ),
		  ),
		);
		return $payment_meta;
	}

	
	//* validate subscription payment meta

	public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {
		if ( $this->id === $payment_method_id ) {
		  if ( ! isset( $payment_meta['post_meta']['_'.$this->id.'_customer_token']['value'] ) || empty( $payment_meta['post_meta']['_'.$this->id.'_customer_token']['value'] ) ) {
		    throw new Exception( 'A "_'.$this->id.'_customer_token" value is required.' );
		  }
		}
	}

	
	//* delete subcribe meta

	public function delete_resubscribe_meta( $resubscribe_order ){
		delete_post_meta( $resubscribe_order->id, '_'.$this->id.'_customer_token' );
	}
	
	//* update failing payment method

	public function update_failing_payment_method( $subscription, $new_renewal_order ){
		update_post_meta(
		    $subscription->id, '_'.$this->id.'_customer_token',
		    get_post_meta( $new_renewal_order->id,
		    '_'.$this->id.'_customer_token', true )
		);
	}

	
	//* Check if order contains subscriptions.
	
	protected function order_contains_subscription( $order_id ) {
		return function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order_id ) || wcs_order_contains_renewal( $order_id ) );
	}
	

	public function unset_gateway_by_product( $available_gateways ){

	 	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	        $prod_variable = $prod_simple = $prod_subscription = false;
	        // Get the WC_Product object
	        $product = wc_get_product($cart_item['product_id']);
	        if($product->is_type('subscription')) $prod_subscription = true;
	    }

	    if( $prod_subscription === false ) {
	    	unset( $available_gateways[ $this->id . '_subscriptions'] ); 
	    }

	    return $available_gateways;
	 } 
	
}
