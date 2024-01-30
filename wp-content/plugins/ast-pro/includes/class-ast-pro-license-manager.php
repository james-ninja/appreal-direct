<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Pro_License_Manager {
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	*/
	private static $instance;
	
	/**
	 * String store_url
	*/
	public $item_code = 'ast-pro';
	public $store_url = 'https://www.zorem.com/';
	public $default_product_id = '90229';
	
	/**
	 * Get the class instance
	 *
	 * @since  1.0
	 * @return class
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Initialize the main plugin function	 
	*/
	public function __construct() {		
		$this->init();
	}
	
	/**
	 * Return item code
	 *
	 * @since   1.0
	 * @return  string
	 *
	 */
	public function get_item_code() {
		return $this->item_code;
	}
	
	/**
	 * Set license key
	 *
	 * @since   1.0
	 * @return  Void
	 *
	 */
	public function set_license_key( $license_key ) {
		update_option( $this->get_item_code() . '_license_key', $license_key );
	}
	
	/**
	 * Return licence key
	 *
	 * @since   1.0
	 * @return  string
	 *
	 */
	public function get_license_key() {
		return get_option( $this->get_item_code() . '_license_key', false );
	}
	
	/**
	 * Set license status
	 *
	 * @since   1.0
	 * @return  Void
	 *
	 */
	public function set_license_status( $status ) {
		update_option( $this->get_item_code() . '_license_status', $status );
	}
	
	/**
	 * Return license status
	 *
	 * @since   1.0
	 * @return  Bool
	 *
	 */
	public function get_license_status() {
		return get_option( $this->get_item_code() . '_license_status', false );
	}
	
	/**
	 * Create Instance ID
	 *
	 * @since   1.0
	 * @return  string
	 *
	 */
	public function create_instance_id() {
		return md5( $this->get_item_code() . time() );
	}
	
	/**
	 * Set Instance ID
	 *
	 * @since   1.0
	 * @return  Void
	 *
	 */
	public function set_instance_id( $instance_id ) {
		update_option( $this->get_item_code() . '_instance_id', $instance_id );
	}
	
	/**
	 * Return Instance ID
	 *
	 * @since   1.0
	 * @return  string
	 *
	 */
	public function get_instance_id() {
		return get_option( $this->get_item_code() . '_instance_id', false );
	}
	
	/**
	 * Return item code
	 *
	 * @since   1.0
	 * @return  string
	 *
	 */
	public function get_product_id() {
		return $this->default_product_id;
	}
	
	/**
	 * Return cron hook
	 *
	 * @since   1.0
	 * @return  string
	 *
	 */
	public function get_license_cron_hook() {
		return $this->get_item_code() . '_license_cron_hook';
	}
	
	/*
	 * init function
	 *
	 * @since  1.0
	*/
	public function init() {
		
		add_action( 'ast_addon_license_form', array( $this, 'ast_pro_license_form' ), 10 );
		
		//cron schedule added
		add_filter( 'cron_schedules', array( $this, 'license_cron_schedule') );
		
		//ajax call for license
		add_action( 'wp_ajax_' . $this->get_item_code() . '_license_activate', array( $this, 'license_activate' ) );
		add_action( 'wp_ajax_' . $this->get_item_code() . '_license_deactivate', array( $this, 'license_deactivate' ) );
		
		//cron schedule
		add_action( 'admin_init', array( $this, 'add_cron_schedule' ) );

		//check license valid
		add_action( $this->get_license_cron_hook(), array( $this, 'check_license_valid' ) );
		if ( !$this->get_license_status() ) {
			add_action( 'admin_notices', array( $this, 'ast_pro_licence_notice' ) );			
		}	
		add_action( 'ast_addon_license_form', array( $this, 'connect_with_zorem' ), 1 );
	}
	
	public function ast_pro_license_form() { 
		require_once( 'views/ast_pro_license_form.php' );	
	}
	
	/*
	* add schedule for license check
	*
	* @since  1.0
	*
	* @return  array
	*/
	public function license_cron_schedule( $schedules ) {
		$schedules[ 'license_cron_events' ] = array(
			'interval' => 86400,
			'display'  => __( 'Every day' ),
		);
		return $schedules;
	}
	
	/*
	* license activate
	* @return  json string
	*/
	public function license_activate() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'wc_ast_pro_addons_form', 'wc_ast_pro_addons_form_nonce' );
		
		$license_key = isset( $_POST['license_key'] ) ? wc_clean( $_POST['license_key'] ) : '';

		if ( isset( $license_key ) ) {
			$instance_id = $this->create_instance_id();
			$authorize_data = $this->license_authorize_action( $license_key, 'activate', $instance_id );
			
			if ( 'true' == $authorize_data->success ) {
				$this->set_license_key( $license_key );
				$this->set_instance_id( $instance_id );
				$this->set_license_status( 1 );
				delete_transient( 'zorem_upgrade_' . $this->get_item_code() );
				delete_transient( 'zorem_subscription_status_' . $this->get_item_code() );
			} else if ( $authorize_data->error ) {
				
				$this->create_log( $authorize_data, 'license_activation_authorize_data' );

				$this->set_license_key( '' );
				$this->set_instance_id( '' );
				$this->set_license_status( 0 );
			}
			header('Content-type: application/json');
			echo json_encode( $authorize_data, JSON_PRETTY_PRINT );
			die();
		}		
	}
	
	/*
	* license deactivate
	* @return  json string
	*/
	public function license_deactivate() {
		
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( 'You are not allowed' );
		}
		
		check_ajax_referer( 'wc_ast_pro_addons_form', 'wc_ast_pro_addons_form_nonce' );
		
		$license_key = isset( $_POST['license_key'] ) ? wc_clean( $_POST['license_key'] ) : '';
		
		if ( isset( $license_key ) ) {
			$return = $this->license_authorize_action( $license_key, 'deactivate' );
			if ( 'true' == $return->success ) {
				$this->set_license_key( '' );
				$this->set_instance_id( '' );
				$this->set_license_status( 0 );
				//delete_option('zorem_license_connected');
				//delete_option('zorem_license_email');
				delete_transient( 'zorem_upgrade_' . $this->get_item_code() );
				delete_transient( 'zorem_subscription_status_' . $this->get_item_code() );
			}
			header('Content-type: application/json');
			echo json_encode( $return, JSON_PRETTY_PRINT );
			die();
		}		
	}
	
	/*
	* License authorize with server
	*/
	public function license_authorize_action( $license_key = '', $action = 'validate', $instance_id = false ) {
		
		if ( false == $instance_id ) {
			$instance_id = $this->get_instance_id();
		}	
		
		$domain = home_url();
		
		$api_params = array(
			'wc-api' => 'wc-am-api',
			'wc_am_action' => $action,
			'instance' => $instance_id,
			'object' => $domain,
			'product_id' => $this->get_product_id(),
			'api_key' => $license_key,
		);
		
		$request = add_query_arg( $api_params, $this->store_url );
		
		$response = wp_remote_get( $request, array( 'timeout' => 15, 'sslverify' => false ) );
		
		if ( is_wp_error( $response ) ) {
			$this->create_log( $response, 'license_activation_response' );
			return false;
		}	
		
		$authorize_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		if ( empty( $authorize_data ) || null == $authorize_data || false == $authorize_data ) {
			$this->create_log( $authorize_data, 'license_activation_authorize_data' );
			return false;
		}	
		
		return $authorize_data;
	}
	
	/*
	 * schedule cron event if not scheduled
	 *
	 * @since  1.0
	 *
	 * @return  null
	 */
	public function add_cron_schedule() {

		if ( ! wp_next_scheduled( $this->get_license_cron_hook() ) ) {
			wp_schedule_event( time(), 'license_cron_events', $this->get_license_cron_hook() );
		}

	}
	
	/**
	 *
	 * Check license valid
	 *
	 * @since  1.0
	 *
	 * @return  null
	 */
	public function check_license_valid() {

		if ( $this->get_license_status() ) {
			
			$authorize = $this->license_authorize_action( $this->get_license_key(), 'status' );
			
			if ( 'true' == $authorize->success ) {
				$license_status = $authorize->status_check;
				if ( isset( $authorize->status_check ) && 'inactive' == $authorize->status_check ) {
					$this->set_license_key( '' );
					$this->set_instance_id( '' );
					$this->set_license_status( 0 );
				}
			} else if ( 'false' == $authorize->success ) {
				$this->set_license_key( '' );
				$this->set_instance_id( '' );
				$this->set_license_status( 0 );
			}
			
		}
	}
	
	/*
	* License notice
	*/
	public function ast_pro_licence_notice() { 
		?>
		<div class="notice notice-warning notice-alt">
			<h3 class="notice-title">Activate the Advanced Shipment Tracking Pro</h3>	
			<p>Opps! your Advanced Shipment Tracking Pro licence key is not activated. To buy license <a href="<?php echo esc_url( admin_url( '/admin.php?page=woocommerce-advanced-shipment-tracking&tab=license' ) ); ?>" rel="noopener noreferrer">click here</a> to activate it.</p>
			<p>
				<a href="https://www.zorem.com/my-account/subscriptions/" target="_blank" rel="noopener noreferrer">
					Manage your subscription					
					<span class="dashicons dashicons-external" style="vertical-align:middle;font-size:18px;text-decoration: none;"></span>
				</a>
			</p>
		</div>
		<?php	
	}

	public function connect_with_zorem() {		
		
		$connect = isset( $_GET['connect'] ) ? sanitize_text_field( $_GET['connect'] ) : '';
		$email = isset( $_GET['email'] ) ? sanitize_text_field( $_GET['email'] ) : '';
		$license_key = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '';
		?>
		<script>
			/* zorem_snackbar jquery */
			(function( $ ){
				$.fn.ast_snackbar = function(msg) {
					if ( jQuery('.snackbar-logs').length === 0 ){
						$("body").append("<section class=snackbar-logs></section>");
					}
					var ast_snackbar = $("<article></article>").addClass('snackbar-log snackbar-log-success snackbar-log-show').text( msg );
					$(".snackbar-logs").append(ast_snackbar);
					setTimeout(function(){ ast_snackbar.remove(); }, 3000);
					return this;
				}; 
			})( jQuery );
			
			/* zorem_snackbar_warning jquery */
			(function( $ ){
				$.fn.ast_snackbar_warning = function(msg) {
					if ( jQuery('.snackbar-logs').length === 0 ){
						$("body").append("<section class=snackbar-logs></section>");
					}
					var ast_snackbar_warning = $("<article></article>").addClass( 'snackbar-log snackbar-log-error snackbar-log-show' ).html( msg );
					$(".snackbar-logs").append(ast_snackbar_warning);
					setTimeout(function(){ ast_snackbar_warning.remove(); }, 3000);
					return this;
				}; 
			})( jQuery );
		</script>
		<?php
		if ( 'true' == $connect && '' != $email ) {
			update_option( 'zorem_license_connected', 1 );
			update_option( 'zorem_license_email', $email );
			

			if ( isset( $license_key ) ) {
				$instance_id = $this->create_instance_id();
				$authorize_data = $this->license_authorize_action( $license_key, 'activate', $instance_id );
				
				if ( 'true' == $authorize_data->success ) {
					$this->set_license_key( $license_key );
					$this->set_instance_id( $instance_id );
					$this->set_license_status( 1 );
					update_option( 'zorem_license_key', $license_key );
					delete_transient( 'zorem_upgrade_' . $this->get_item_code() );
					$message = 'License successfully activated.';
					?>
					<script>
						jQuery(document).ast_snackbar( '<?php esc_html_e( $message ); ?>' );
					</script>
					<style>
					.ast-pro-license-notice {
						display: none;
					}
					</style>
					<?php
				} else if ( $authorize_data->error ) {
					
					$this->create_log( $authorize_data, 'license_activation_authorize_data' );

					if ( 'No API resources exist. Login to My Account to verify there are activations remaining, and the API Key and Product ID are correct.' == $authorize_data->error ) {
						$message = 'License not found for user ' . $email;
						?>
						<script>
							jQuery(document).ast_snackbar_warning( '<?php esc_html_e( $message ); ?>' );
						</script>
						<?php	
					} elseif ( 'Cannot activate License Key. This key is already active on another site.' == $authorize_data->error ) {
						$message = 'Cannot activate License. License is already active on another site.';
						?>
						<script>
							jQuery(document).ast_snackbar_warning( '<?php esc_html_e( $message ); ?>' );
						</script>
						<?php
					} else {
						$message = 'License not found for user ' . $email;
						?>
						<script>
							jQuery(document).ast_snackbar_warning( '<?php esc_html_e( $message ); ?>' );
						</script>
						<?php
					}
					
					$this->set_license_key( '' );
					$this->set_instance_id( '' );
					$this->set_license_status( 0 );
				}
			}	
			?>
			<script>
				
				var url = window.location.protocol + "//" + window.location.host + window.location.pathname+"?page=woocommerce-advanced-shipment-tracking&tab=license";
				window.history.pushState({path:url},'',url);
				jQuery('input#tab6').trigger('click');
				location.reload();
			</script>
			<?php
		} elseif ( 'false' == $connect && '' != $email ) {
			?>
			<script>
				var url = window.location.protocol + "//" + window.location.host + window.location.pathname+"?page=woocommerce-advanced-shipment-tracking&tab=license";
				window.history.pushState({path:url},'',url);
				jQuery('input#tab6').trigger('click');
			</script>
			<?php	
		}
	}

	public function check_subscription_status () {
		
		$license_connected = get_option( 'zorem_license_connected', 0 );
		$license_email = get_option( 'zorem_license_email', '' );
	
		$subscription = false;
		
		if ( $this->get_license_status() ) {
			return true;
		}
		
		if ( $license_connected && '' != $license_email ) {
			
			$zorem_subscription_status = get_transient( 'zorem_subscription_status_' . $this->get_item_code() );			
			
			if ( false == $zorem_subscription_status ) {
				
				$api_params = array(
					'license_email' => $license_email,			
					'product_id' => $this->get_product_id(),				
				);

				$request = add_query_arg( $api_params, $this->store_url . 'wp-json/wc-zorem-license/v3/status/subscription' );
				$response = wp_remote_get( $request, array( 'timeout' => 15, 'sslverify' => false ) );
				
				if ( is_wp_error( $response ) ) {
					return false;
				}	

				$subscription_data = json_decode( wp_remote_retrieve_body( $response ) );

				set_transient( 'zorem_subscription_status_' . $this->get_item_code(), $response, 86400 ); // 24 hours cache

				return $subscription_data->subscription;
			} else {
				$subscription_data = json_decode( wp_remote_retrieve_body( $zorem_subscription_status ) );
				$subscription = isset( $subscription_data->subscription ) ? $subscription_data->subscription : false;
				return $subscription;
			}						
		}
		
		return $subscription;
	}

	public function create_log( $content, $log_key = 'license_activation_authorize_data' ) {
		$content = print_r($content, true);
		$logger = wc_get_logger();
		$context = array( 'source' => 'ast_pro_license_activation_log' );
		$logger->info( $log_key . " \n" . $content . "\n", $context );
	}
}
