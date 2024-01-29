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
		add_action( 'admin_notices', array( $this, 'ast_pro_admin_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_pro_admin_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'ast_db_update_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_db_update_notice_ignore' ) );	
		
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );		
	}

	/*
	* init on plugin loaded
	*/
	public function on_plugins_loaded() {
		
		$wc_ast_api_key = get_option( 'wc_ast_api_key' ); 
		if ( $wc_ast_api_key && !function_exists( 'trackship_for_woocommerce' ) ) {			
			add_action( 'admin_notices', array( $this, 'ast_pro_install_ts4wc' ) );
		}
	}	
	
	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_pro_admin_notice() { 		
		
		if ( get_option('ast_pro_standalone_version_admin_notice_ignore') ) {
			return;
		}	
		
		$dismissable_url = esc_url(  add_query_arg( 'ast-pro-standalone-ignore-notice', 'true' ) );
		?>		
		<style>		
		.wp-core-ui .notice.ast-pro-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		}
		</style>	
		<div class="notice notice-error is-dismissible ast-pro-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<p><strong>Please note:</strong> AST PRO is now a standalone plugin and does not require the AST Free to be installed, when you updated the AST PRO, we automatically deactivate the free AST version and you can delete it from your plugins list. <a href="https://www.zorem.com/advanced-shipment-tracking-pro-1-1/" target="_blank">Read more on the release notes</a></p>			
		</div>
	<?php 		
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_pro_admin_notice_ignore() {
		if ( isset( $_GET['ast-pro-standalone-ignore-notice'] ) ) {
			update_option( 'ast_pro_standalone_version_admin_notice_ignore', 'true' );
		}
	}

	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_db_update_notice() { 		
		
		if ( get_option('ast_db_update_notice_ignore') ) {
			return;
		}	
		
		$dismissable_url = esc_url(  add_query_arg( 'ast-db-update-notice-ignore', 'true' ) );
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
		</style>	
		<div class="notice notice-success is-dismissible ast-pro-dismissable-notice">			
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
		if ( isset( $_GET['ast-db-update-notice-ignore'] ) ) {
			update_option( 'ast_db_update_notice_ignore', 'true' );
		}
		if ( isset( $_GET['open'] ) && 'synch_providers' == $_GET['open'] ) {
			update_option( 'ast_db_update_notice_ignore', 'true' );
		}
	}	

	/*
	* Display admin notice on if Store is connected to TrackShip and TrackShip For WooCommerce plugin is not activate
	*/
	public function ast_pro_install_ts4wc() {
		?>
		<div class="notice notice-error">			
			<p>Hi, we noticed that your store is connected to TrackShip, Please install the <a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=search&s=TrackShip+For+WooCommerce&plugin-search-input=Search+Plugins' ) ); ?>" target="blank">TrackShip for WooCommerce</a> plugin (free) to continue using TrackShip on your store.	</p>
		</div>
		<?php 		
	}		
}
