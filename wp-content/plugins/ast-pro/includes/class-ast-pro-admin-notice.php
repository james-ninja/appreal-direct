<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_PRO_Admin_Notice {

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
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return AST_PRO_Admin_Notice
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
		//add_action( 'admin_notices', array( $this, 'ast_pro_admin_notice' ) );	
		//add_action( 'admin_init', array( $this, 'ast_pro_admin_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'ast_pro_trackship_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_pro_trackship_notice_ignore' ) );

		add_action( 'before_shipping_provider_list', array( $this, 'ast_db_update_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_db_update_notice_ignore' ) );		
	}	
	
	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_pro_trackship_notice() { 		
		
		$ts4wc_installed = ( function_exists( 'trackship_for_woocommerce' ) ) ? true : false;
		if ( $ts4wc_installed ) {
			return;
		}
		
		if ( get_option('ast_pro_trackship_notice_ignore') ) {
			return;
		}	
		
		$dismissable_url = esc_url(  add_query_arg( 'ast-pro-trackship-notice', 'true' ) );
		?>		
		<style>		
		.wp-core-ui .notice.ast-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		</style>
		<div class="notice updated notice-success ast-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			

			<p>Getting many shipping inquiries? Have no idea if your shipments are delivered? <a target="blank" href="https://wordpress.org/plugins/trackship-for-woocommerce/">TrackShip</a> auto-tracks all your shipments and allows you provide your customers with Amazon style post-purchase experience!</p>
			
			<a class="button-primary ast_notice_btn" target="blank" href="https://wordpress.org/plugins/trackship-for-woocommerce/">Start for free today ></a>
			<a class="button-primary ast_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>				
		</div>	
		<?php 				
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_pro_trackship_notice_ignore() {
		if ( isset( $_GET['ast-pro-trackship-notice'] ) ) {
			update_option( 'ast_pro_trackship_notice_ignore', 'true' );
		}
	}

	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_db_update_notice() { 		
		
		if ( get_option('ast_db_update_notice_updated_ignore') ) {
			return;
		}	
		
		$dismissable_url = esc_url(  add_query_arg( 'ast-db-update-notice-updated-ignore', 'true' ) );
		$update_providers_url = esc_url( admin_url( '/admin.php?page=woocommerce-advanced-shipment-tracking&tab=shipping-providers&open=synch_providers' ) );
		?>
		<style>		
		.wp-core-ui .notice.ast-pro-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		}
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		.ast-notice{
			background: #fff;
			border: 1px solid #e0e0e0;
			margin: 0 0 25px;
			padding: 1px 12px;
			box-shadow: none;
		}
		</style>	
		<div class="ast-notice notice notice-success is-dismissible ast-pro-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<p>Shipping providers update is available, please click on update providers to update the shipping providers list.</p>
			<a class="button-primary ast_notice_btn" href="<?php esc_html_e( $update_providers_url ); ?>">Update Providers</a>			
		</div>
	<?php 		
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_db_update_notice_ignore() {
		if ( isset( $_GET['ast-db-update-notice-updated-ignore'] ) ) {
			update_option( 'ast_db_update_notice_updated_ignore', 'true' );
		}
		if ( isset( $_GET['open'] ) && 'synch_providers' == $_GET['open'] ) {
			update_option( 'ast_db_update_notice_updated_ignore', 'true' );
		}
	}		
}
