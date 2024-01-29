<?php

/**
 * Apparel Direct functions and definitions
 */
function my_theme_setup()
{

	load_theme_textdomain('appareldirect');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');

	register_nav_menus(
		array(
			'header-menu'    => __('Header Menu', 'appareldirect'),
			'footer' => __('Footer Menu', 'appareldirect'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	// Load regular editor styles into the new block-based editor.
	add_theme_support('editor-styles');

	// Load default block styles.
	add_theme_support('wp-block-styles');

	// Add support for responsive embeds.
	add_theme_support('responsive-embeds');

	add_image_size('closeout-product-thumb', 200, 130, array('left', 'top'));
	//add_image_size('product-thumb', 168, 168, array('left', 'top'));

	add_image_size('product-recommended', 355, 395, array('left', 'top'));

	//For Main category page
	add_image_size('cat-main-thumb1', 723, 638, array('left', 'top'));
	add_image_size('cat-main-thumb2', 353, 339, array('left', 'top'));
	add_image_size('cat-main-thumb3', 353, 384, array('left', 'top'));
	add_image_size('cat-sub-thumb1', 535, 657, array('left', 'top'));
	add_image_size('cat-sub-thumb2', 535, 393, array('left', 'top'));

	$defaults = array(
		// 'height'               => 100,
		// 'width'                => 400,
		//'flex-height'          => true,
		// 'flex-width'           => true,
		// 'header-text'          => array( 'site-title', 'site-description' ),
	);

	add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', 'my_theme_setup');

add_filter('get_custom_logo', 'change_logo_class');
function change_logo_class($html)
{
	$html = str_replace('custom-logo-link', 'logo', $html);
	return $html;
}
// Changing excerpt length
function new_excerpt_length($length)
{
	return 24;
}
add_filter('excerpt_length', 'new_excerpt_length');

// Changing excerpt more
function new_excerpt_more($more)
{
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

add_action('wp_enqueue_scripts', 'ad_theme_scripts');
function ad_theme_scripts()
{

	//html
	wp_enqueue_script('select2-full-js', get_template_directory_uri() . '/assets/js/select2.full.min.js', array('jquery'), '4.0.0', '');
	// wp_enqueue_script('scroll-js', get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', array(), '1.0.0', true);
	wp_enqueue_script('owl-carousel-min-js', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array(), '1.0.0', true);
	wp_enqueue_script('popper-min-js', get_template_directory_uri() . '/assets/js/popper.min.js', array('jquery'), '4.0.0', true);
	wp_enqueue_script('bootstrap-min-js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '4.0.0', true);

	wp_enqueue_script('jquery-validate', get_template_directory_uri() . '/assets/js/jquery.validate.min.js', array(), '1.0.0', true);
	wp_enqueue_script('jquery-validate-additional-methods', get_template_directory_uri() . '/assets/js/additional-methods.min.js', array(), '1.0.0', true);
	wp_enqueue_script('cuttr-min-js', get_template_directory_uri() . '/assets/js/cuttr.min.js', array(), '1.0.0', true);
	wp_enqueue_script('custom-dev-js', get_template_directory_uri() . '/assets/js/custom-dev.js', array(), '2.5.4', true);
	wp_enqueue_script('address-autofill-js', get_template_directory_uri() . '/assets/js/address-autofill.js', array(), '1.0.0', true);
	wp_localize_script('custom-dev-js', 'custom', array(
		'ajaxurl' => admin_url('admin-ajax.php')
	));
	wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.js', array(), '1.1.0', true);
	
	
}

/**
 * Enqueues scripts and styles.
 */
if (!function_exists('ad_theme_style')) {
	function ad_theme_style()
	{

		// Load css libraries
		wp_enqueue_style('bootstrap', get_theme_file_uri('/assets/css/bootstrap.min.css'), array(), '4.0.0');
		// wp_enqueue_style('scroll', get_theme_file_uri('/assets/css/jquery.mCustomScrollbar.min.css'), array(), '4.0.0');
		wp_enqueue_style('owl-carousel-min-css', get_theme_file_uri('/assets/css/owl.carousel.min.css'), array(), '1.0.0');
		// Theme stylesheet.
		wp_enqueue_style('the-graph-group-style', get_stylesheet_uri(), array(), '1.0.0');
		wp_enqueue_style('developer', get_theme_file_uri('/assets/css/developer.css'), array(), '2.5.3');
		wp_enqueue_style('style-custom', get_theme_file_uri('/assets/css/style.css'), array(), '2.1.3');
		wp_enqueue_style('responsive-custom', get_theme_file_uri('/assets/css/responsive.css'), array(), '2.0.0');
	}
}
add_action('wp_enqueue_scripts', 'ad_theme_style');


//Quick order
//start session
/*add_action('init', 'start_session', 1);

function start_session() {
	if(!session_id()) {
		session_start();
	}
}*/
require get_template_directory() . '/include/local-enqueue.php';
require get_template_directory() . '/include/woo2-quick-v2.php';
require get_template_directory() . '/inc/PHPExcel.php';
//Quick order

/**
 * Theme Extra Functions
 */
require get_parent_theme_file_path('/inc/theme-extra-functions.php');

/**
 * Custom Woocommerce Functions
 */
require get_template_directory() . '/inc/custom-woo-functions.php';


require get_template_directory() . '/inc/aq_resizer.php';


//Admin logo update
function my_login_logo_one()
{ ?>
	<style type="text/css">
		body.login div#login h1 a {
			background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/logo.png);
			width: 140px;
			background-size: 120px;
		}
	</style>
	<?php }
add_action('login_enqueue_scripts', 'my_login_logo_one');

//Admin logo URL update
add_filter('login_headerurl', 'custom_loginlogo_url');
function custom_loginlogo_url($url)
{
	return site_url();
}
//Added Woocommerce support
add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
	add_theme_support('woocommerce');
}

function ad_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'ad_mime_types');

//Sidebar created
add_action('widgets_init', 'ad_widgets_init');
function ad_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Footer 1', 'apparel_direct'),
			'id'            => 'footer-1',
			'description'   => esc_html__('Add widgets here to appear in footer 1.', 'apparel_direct'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__('Footer 2', 'apparel_direct'),
			'id'            => 'footer-2',
			'description'   => esc_html__('Add widgets here to appear in footer 2.', 'apparel_direct'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__('Footer 3', 'apparel_direct'),
			'id'            => 'footer-3',
			'description'   => esc_html__('Add widgets here to appear in footer 3.', 'apparel_direct'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__('Footer 4', 'apparel_direct'),
			'id'            => 'footer-4',
			'description'   => esc_html__('Add widgets here to appear in footer 4.', 'apparel_direct'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__('Copyright Text', 'apparel_direct'),
			'id'            => 'copyright-bottom',
			'description'   => esc_html__('Add widgets here to appear in Copyright Area.', 'apparel_direct'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__('Shop Sidebar', 'apparel_direct'),
			'id'            => 'shop-sidebar',
			'description'   => esc_html__('Add widgets here to appear in shop sidebar.', 'apparel_direct'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

//Sidebar added on shop page
add_action('woocommerce_sidebar', 'ad_woocommerce_sidebar', 40);
function ad_woocommerce_sidebar()
{
	if (is_woocommerce()) {
		echo '<div class="shop-filters">';
		dynamic_sidebar('shop-sidebar');
		echo '</div>';
	}
}

// Register Custom Taxonomy
function create_brand_taxonomy_for_products()
{

	$labels = array(
		'name'                       => 'Brands',
		'singular_name'              => 'Brand',
		'menu_name'                  => 'Brands',
		'all_items'                  => 'All Brands',
		'parent_item'                => 'Parent Brand',
		'parent_item_colon'          => 'Parent Brand:',
		'new_item_name'              => 'New Brand Name',
		'add_new_item'               => 'Add New Brand',
		'edit_item'                  => 'Edit Brand',
		'update_item'                => 'Update Brand',
		'separate_items_with_commas' => 'Separate Brand with commas',
		'search_items'               => 'Search Brands',
		'add_or_remove_items'        => 'Add or remove Brands',
		'choose_from_most_used'      => 'Choose from the most used Brands',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest' 				 => true,
	);
	register_taxonomy('product_brand', 'product', $args);
}
add_action('init', 'create_brand_taxonomy_for_products', 0);

// Register Gender Custom Taxonomy
function create_gender_taxonomy_for_products()
{

	$labels = array(
		'name'                       => 'Gender',
		'singular_name'              => 'Gender',
		'menu_name'                  => 'Gender',
		'all_items'                  => 'All Gender',
		'parent_item'                => 'Parent Gender',
		'parent_item_colon'          => 'Parent Gender:',
		'new_item_name'              => 'New Gender Name',
		'add_new_item'               => 'Add New Gender',
		'edit_item'                  => 'Edit Gender',
		'update_item'                => 'Update Gender',
		'separate_items_with_commas' => 'Separate Gender with commas',
		'search_items'               => 'Search Gender',
		'add_or_remove_items'        => 'Add or remove Gender',
		'choose_from_most_used'      => 'Choose from the most used Gender',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest' 				 => true,
	);
	register_taxonomy('product_gender', 'product', $args);
}
add_action('init', 'create_gender_taxonomy_for_products', 0);

// Register Collection Custom Taxonomy
function create_collection_taxonomy_for_products()
{

	$labels = array(
		'name'                       => 'Collections',
		'singular_name'              => 'Collection',
		'menu_name'                  => 'Collections',
		'all_items'                  => 'All Collections',
		'parent_item'                => 'Parent Collection',
		'parent_item_colon'          => 'Parent Collection:',
		'new_item_name'              => 'New Collection Name',
		'add_new_item'               => 'Add New Collection',
		'edit_item'                  => 'Edit Collection',
		'update_item'                => 'Update Collection',
		'separate_items_with_commas' => 'Separate Collection with commas',
		'search_items'               => 'Search Collection',
		'add_or_remove_items'        => 'Add or remove Collection',
		'choose_from_most_used'      => 'Choose from the most used Collections',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest' 				 => true,
	);
	register_taxonomy('product_collection', 'product', $args);
}
add_action('init', 'create_collection_taxonomy_for_products', 0);

// Register Product Line Custom Taxonomy
function create_productline_taxonomy_for_products()
{

	$labels = array(
		'name'                       => 'Product Line',
		'singular_name'              => 'Product Line',
		'menu_name'                  => 'Product Line',
		'all_items'                  => 'All Product Line',
		'parent_item'                => 'Parent Product Line',
		'parent_item_colon'          => 'Parent Product Line:',
		'new_item_name'              => 'New Product Line Name',
		'add_new_item'               => 'Add New Product Line',
		'edit_item'                  => 'Edit Product Line',
		'update_item'                => 'Update Product Line',
		'separate_items_with_commas' => 'Separate Product Line with commas',
		'search_items'               => 'Search Product Line',
		'add_or_remove_items'        => 'Add or remove Product Line',
		'choose_from_most_used'      => 'Choose from the most used Product Line',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest' 				 => true,
	);
	register_taxonomy('product_productline', 'product', $args);
}
add_action('init', 'create_productline_taxonomy_for_products', 0);


/**
 * ACF option page
 */
if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' 	=> 'Site Options',
		'menu_title'	=> 'Site Options',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

//Header Wishlist count ajax
if (defined('YITH_WCWL') && !function_exists('yith_wcwl_ajax_update_count')) {
	function yith_wcwl_ajax_update_count()
	{
		wp_send_json(array(
			'count' => yith_wcwl_count_all_products()
		));
	}
	add_action('wp_ajax_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count');
	add_action('wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count');
}

if (defined('YITH_WCWL') && !function_exists('yith_wcwl_enqueue_custom_script')) {
	function yith_wcwl_enqueue_custom_script()
	{
		wp_add_inline_script(
			'jquery-yith-wcwl',
			"
		   jQuery( function( $ ) {
			 $( document ).on( 'added_to_wishlist removed_from_wishlist', function() {
			   $.get( yith_wcwl_l10n.ajax_url, {
				 action: 'yith_wcwl_update_wishlist_count'
			   }, function( data ) {
				 $('.yith-wcwl-items-count').html( data.count );
			   } );
			 } );
		   } );
		 "
		);
	}
	add_action('wp_enqueue_scripts', 'yith_wcwl_enqueue_custom_script', 20);
}

// WooCommerce User Login Shortcode
add_shortcode('login_form_apparel', 'apparel_separate_login_form');
function apparel_separate_login_form()
{
	if (is_admin()) return;
	if (is_user_logged_in()) return;
	ob_start();
	include 'login_form_code.php';
	return ob_get_clean();
}


// Start AJAX function for login
add_action('wp_ajax_custom_login', 'custom_login_ajax_fun');
add_action('wp_ajax_nopriv_custom_login', 'custom_login_ajax_fun');

function custom_login_ajax_fun()
{

	$response = [];

	$user_email = $_POST['user_email'];
	$user_password = $_POST['user_password'];
	$rememberme = $_POST['rememberme'];
	
	if($rememberme == 'forever'){
		$remember = true;
	}else{
		$remember = false;
	}
	if (!$user_email) {
		$response = array('status' => 'error', 'msg' => 'Enter email address');
		echo json_encode($response);
		die();
	}
	if (!$user_password) {
		$response = array('status' => 'error', 'msg' => 'Enter password');
		echo json_encode($response);
		die();
	}
	// if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
	// 	$response = array('status' => 'error', 'msg' => 'Enter valid email address');
	// 	echo json_encode($response);
	// 	die();
	// }

	//User status check
	if ($user_email) {
		$user = get_user_by('email', $user_email);
		if ($user->ID) {
			$status = get_user_meta($user->ID, 'apnu_new_user_status', true);

			if ($status == 'pending' || $status == 'disapproved') {

				$message = false;
				switch ($status) {
					case 'pending':
						$message = get_option('addify_apnu_account_pending_message');
						break;
					case 'disapproved':
						$message = get_option('addify_apnu_account_disapproved_message');
						break;
				}

				$response = array('status' => 'error', 'msg' => $message);
				echo json_encode($response);
				die();
			}
		}
	}

	$user_detail = array(
		'user_name' => $user_email,
		'user_login' => $user_email,
		'user_password' => $user_password,
		'remember'      => $remember
	);

	$user = wp_signon($user_detail, false);

	if (is_wp_error($user)) {
		$error_message = $user->get_error_message();
		$response = array('status' => 'error', 'msg' => $error_message);
		echo json_encode($response);
		die();
	} else {
		//$redirect_url = wc_get_page_permalink('myaccount');
		$redirect_url = get_home_url();
		$response = array('status' => 'success', 'msg' => 'Login successful.. Redirecting..', 'redirect' => $redirect_url);
		echo json_encode($response);
		die();
	}
}

add_action('woocommerce_register_form', 'bbloomer_add_registration_privacy_policy', 11);

function bbloomer_add_registration_privacy_policy()
{

	$privacy_page = get_permalink(get_page_by_path('privacy-policy'));
	$terms_page = get_permalink(get_page_by_path('terms-and-conditions'));

	woocommerce_form_field('privacy_policy_reg', array(
		'type'          => 'checkbox',
		'class'         => array('form-row privacy'),
		'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox privacy_policy_reg_label'),
		'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox privacy_policy_reg input-checkbox'),
		'required'      => true,
		'label'         => 'I agree to the <a target="_blank" href="' . $terms_page . '">Terms and Conditions</a> and <a target="_blank" href="' . $privacy_page . '">Privacy Policy</a>.',
	));
}

// Show error if user does not tick

add_filter('woocommerce_registration_errors', 'bbloomer_validate_privacy_registration', 10, 3);

function bbloomer_validate_privacy_registration($errors, $username, $email)
{
	if (!is_checkout()) {
		if (!(int) isset($_POST['privacy_policy_reg'])) {
			$errors->add('privacy_policy_reg_error', __('Privacy Policy consent is required!', 'woocommerce'));
		}
	}
	return $errors;
}

add_filter('woocommerce_form_field', 'elex_remove_checkout_optional_text', 10, 4);
function elex_remove_checkout_optional_text($field, $key, $args, $value)
{
	$optional = '&nbsp;<span class="optional">(' . esc_html__('optional', 'woocommerce') . ')</span>';
	$field = str_replace($optional, '', $field);
	return $field;
}

//Remove result count shop page
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

//Remove shop page product pagination
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);

function ad_filter_plugin_updates($value)
{
	if (isset($value->response['approve-new-user-registration/addify_approve_new_user.php'])) {
		unset($value->response['approve-new-user-registration/addify_approve_new_user.php']);
	}
	if (isset($value->response['multiple-shipping-address-woocommerce/oc-woo-multiple-address.php'])) {
		unset($value->response['multiple-shipping-address-woocommerce/oc-woo-multiple-address.php']);
	}
	if (isset($value->response['woocommerce-bulk-variations/woocommerce-bulk-variations.php'])) {
		unset($value->response['woocommerce-bulk-variations/woocommerce-bulk-variations.php']);
	}
	if (isset($value->response['wp-activity-log-for-woocommerce/wsal-woocommerce.php'])) {
		unset($value->response['wp-activity-log-for-woocommerce/wsal-woocommerce.php']);
	}
	if (isset($value->response['ast-pro/ast-pro.php'])) {
		unset($value->response['ast-pro/ast-pro.php']);
	}
	if (isset($value->response['back-in-stock-notifier-for-woocommerce/cwginstocknotifier.php'])) {
		unset($value->response['back-in-stock-notifier-for-woocommerce/cwginstocknotifier.php']);
	}
	return $value;
}
add_filter('site_transient_update_plugins', 'ad_filter_plugin_updates');

//Page Slug Body Class
function add_slug_body_class($classes)
{
	global $post;
	if (isset($post)) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}

	//user_level
	$userid = get_current_user_id();
	$ad_user_site_access  = get_user_meta($userid, 'ad_user_site_access', true );
	if($ad_user_site_access == 'limited_access'){
	  $classes[] = 'ad_user_limited_access';
	}
	//user_level

	return $classes;
}
add_filter('body_class', 'add_slug_body_class');

function disable_account_creation_email($email_class)
{
	remove_action('woocommerce_created_customer_notification', array($email_class, 'customer_new_account'), 10, 3);
}

add_action('woocommerce_email', 'disable_account_creation_email');

//Remove product detail page tabs
function woo_remove_product_tab($tabs)
{
	unset($tabs['description']);
	unset($tabs['reviews']);
	unset($tabs['additional_information']);
	return $tabs;
}
add_filter('woocommerce_product_tabs', 'woo_remove_product_tab', 98);

//Remove related product section from product detail page
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

//Add custom field in product detail page
function ad_add_custom_style_field_product()
{
	$args_1 = array(
		'label' => __('Style', 'woocommerce'),
		'placeholder' => __('Enter Style Here', 'woocommerce'),
		'id' => 'product_style',
		'desc_tip' => true,
		'description' => __('This Style is display on product detail page.', 'woocommerce'),
	);
	woocommerce_wp_text_input($args_1);
}
add_action('woocommerce_product_options_sku', 'ad_add_custom_style_field_product');

// Save
function ad_save_custom_meta($product)
{
	if (isset($_POST['product_style'])) {
		$product->update_meta_data('product_style', sanitize_text_field($_POST['product_style']));
	}
}
add_action('woocommerce_admin_process_product_object', 'ad_save_custom_meta', 10, 1);

/**
 * Change several of the breadcrumb defaults
 */
add_filter('woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs');
function jk_woocommerce_breadcrumbs()
{
	return array(
		'delimiter'   => ' &gt; ',
		'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
		'wrap_after'  => '</nav>',
		'before'      => '',
		'after'       => '',
		'home'        => _x('Home', 'breadcrumb', 'woocommerce'),
	);
}

/**
 * Remove the breadcrumbs 
 */
add_action('init', 'woo_remove_wc_breadcrumbs');
function woo_remove_wc_breadcrumbs()
{
	remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}

add_filter('woocommerce_get_price_suffix', 'bbloomer_add_price_suffix', 99, 4);
function bbloomer_add_price_suffix($html, $product, $price, $qty)
{
	if (is_product()) {
		$html .= ' Per Unit';
	}

	return $html;
}

/**
 * Cart page quantity
 */
add_action('wp_footer', 'cart_add_script_to_footer');

function cart_add_script_to_footer()
{
	if (!is_admin()) {
		if (!is_cart()) return;
	?>
		<script>
			jQuery(document).ready(function($) {
				$(document).on('click', '.plus', function(e) {
					$input = $(this).prev('input.qty');
					var val = parseInt($input.val());
					var step = $input.attr('step');
					step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
					$input.val(val + step).change();
				});
				$(document).on('click', '.minus',
					function(e) {
						$input = $(this).next('input.qty');
						var val = parseInt($input.val());
						var step = $input.attr('step');
						step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
						if (val > 0) {
							$input.val(val - step).change();
						}
					});

				/*var timeout;

				jQuery(function($) {
					$('.woocommerce').on('change', 'input.qty', function() {
						if (timeout !== undefined) {
							clearTimeout(timeout);
						}
						timeout = setTimeout(function() {
							$("[name='update_cart']").trigger("click");
						}, 500);

					});
				});*/
			});
		</script>
	<?php
	}
}

add_filter('woocommerce_add_to_cart_fragments', 'cpp_header_add_to_cart_fragment');
function cpp_header_add_to_cart_fragment($fragments)
{
	global $woocommerce;
	ob_start(); ?>
	<span class="cart_total_custom">
		<?php echo '<label>Subtotal</label>';?> <?php wc_cart_totals_subtotal_html(); ?>
	</span>
<?php
	$fragments['.cart_total_custom'] = ob_get_clean();
	return $fragments;
}

//Checkout Page

remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
add_action('custom_woocommerce_checkout_after_shipping', 'woocommerce_checkout_payment', 20);


add_filter('gettext', 'ad_custom_paypal_button_text', 20, 3);
function ad_custom_paypal_button_text($translated_text, $text, $domain)
{
	if ($translated_text == 'Proceed to PayPal') {
		$translated_text = 'PLACE ORDER'; // new button text is here
	}
	return $translated_text;
}

//Add Class in checkout page
add_filter('woocommerce_checkout_fields', 'addBootstrapToCheckoutFields');
function addBootstrapToCheckoutFields($fields)
{
	foreach ($fields as &$fieldset) {
		foreach ($fieldset as &$field) {
			$field['class'][] = 'form-group';
			$field['input_class'][] = 'form-control';
		}
	}
	return $fields;
}

//Change Order Notes Label
add_filter('woocommerce_checkout_fields', 'change_order_note_label');

function change_order_note_label($fields)
{
	$fields['order']['order_comments']['label'] = 'Additional Details';
	return $fields;
}

add_filter('woocommerce_shipping_package_name', 'custom_shipping_package_name');
function custom_shipping_package_name($name)
{
	return '<h3>Select Shipping Method:</h3>';
}

add_filter('woocommerce_ship_to_different_address_checked', '__return_true');

/*Remove fields in checkout page and Address in Account pages*/

add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
add_filter('woocommerce_billing_fields', 'custom_override_billing_fields');
add_filter('woocommerce_shipping_fields', 'custom_override_shipping_fields');

function custom_override_checkout_fields($fields)
{
	unset($fields['billing']['billing_address_2']);
	unset($fields['shipping']['shipping_address_2']);
	return $fields;
}

function custom_override_billing_fields($fields)
{
	unset($fields['billing']['billing_address_2']);
	unset($fields['shipping']['shipping_address_2']);
	return $fields;
}

function custom_override_shipping_fields($fields)
{
	unset($fields['billing']['billing_address_2']);
	unset($fields['shipping']['shipping_address_2']);
	return $fields;
}

function woo_dequeue_select2()
{
	if (class_exists('woocommerce')) {
		if (is_checkout()) {
			wp_dequeue_style('select2');
			wp_deregister_style('select2');

			wp_dequeue_script('selectWoo');
			wp_deregister_script('selectWoo');
		}
	}
}
add_action('wp_enqueue_scripts', 'woo_dequeue_select2', 100);

add_filter('woocommerce_update_order_review_fragments', 'price_bottom_checkout');
function price_bottom_checkout($arr)
{
	global $woocommerce;
	ob_start();
	$shipping_method_custom = wc_get_template(
		'checkout/shipping-method-custom.php',
		array(
			'checkout' => WC()->checkout(),
		)
	);
	$shipping_method_custom = ob_get_clean();
	$arr['.woocommerce-checkout-review-order-table-custom'] = $shipping_method_custom;
	return $arr;
}

//Checkout Page End

//comment becuase new layout setup
/*add_filter('wc_bulk_variations_get_table_output', function ($table, $return_type = 'html') {
	return $table . '<div class="arrow_main"><a href="javascript:void(0);" id="prev_arrow" class="prev_arrow"><</a><a href="javascript:void(0);" id="next_arrow" class="next_arrow">></a></div>';
});*/

//Save for later
add_shortcode('saveforlater_custom', 'saveforlater_custom');

function saveforlater_custom()
{
	//do_action('woocommerce_after_cart_table');
	mwb_woo_save_my_cart_product_section_custom();
}

/* WooCommerce Add To Cart Text */
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_add_to_cart_text');
function woocommerce_custom_add_to_cart_text()
{
	return __('Add Selected To Cart', 'woocommerce');
}

//Dashboard page
function woo_order_dashboard_page($atts)
{
	extract(shortcode_atts(array(
		'order_count' => -1
	), $atts));

	ob_start();
	$filterDate =  (time() - 31536000);

	$customer_orders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', array(
		'customer' => get_current_user_id(),
		'page'     => $current_page,
		'paginate' => true,
		//'date_created' => '<' . $filterDate,
		//'post_status' => array('on-hold')
		'numberposts' => 3
	)));
	wc_get_template(
		'myaccount/orders-list.php',
		array(
			'current_page'    => absint($current_page),
			'customer_orders' => $customer_orders,
			'has_orders'      => 0 < $customer_orders->total
		)
	);
?>
	<div id="modal_order_form" style="display: none;" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
				<div class="modal-body text-center">
					<?php echo do_shortcode('[contact-form-7 id="1123" title="Order Support Form"]'); ?>
				</div>
			</div>
		</div>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode('woo_order_dashboard_page', 'woo_order_dashboard_page');
//Order Page
function woo_order_page($atts)
{
	extract(shortcode_atts(array(
		'order_count' => -1
	), $atts));

	ob_start();
	$filterDate =  (time() - 31536000);

	$customer_orders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', array(
		'customer' => get_current_user_id(),
		'page'     => $current_page,
		'paginate' => true,
		//'date_created' => '<' . $filterDate,
		//'post_status' => array('on-hold')
		'numberposts' => 3
	)));
	wc_get_template(
		'myaccount/orders-list.php',
		array(
			'current_page'    => absint($current_page),
			'customer_orders' => $customer_orders,
			'has_orders'      => 0 < $customer_orders->total
		)
	);

	$allorder = wc_get_customer_order_count(get_current_user_id());
	if ($allorder > 3) {
		echo '<div class="load_more_div text-center"><button type="button" data-allorder= "' . $allorder . '" class="btn primary-btn view-more-order">View More Orders</button></div>';
	}
?>

	<div id="modal_order_form" style="display: none;" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
				<div class="modal-body text-center">
					<?php echo do_shortcode('[contact-form-7 id="1123" title="Order Support Form"]'); ?>
				</div>
			</div>
		</div>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode('woo_order_page', 'woo_order_page');

function order_loadmore_ajax_fun()
{

	$current_page = (isset($_POST['page'])) ? $_POST['page'] : 0;
	//$filterDate =  (time() - 31536000);

	$customer_orders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', array(
		'customer' => get_current_user_id(),
		'page'     => $current_page,
		'paginate' => true,
		//'date_created' => '<' . $filterDate,
		//'post_status' => array('on-hold')
		'numberposts' => 3
	)));
	wc_get_template(
		'myaccount/orders-data.php',
		array(
			'customer_orders' => $customer_orders,
			'has_orders'      => 0 < $customer_orders->total
		)
	);

	wp_die();
}
add_action('wp_footer', 'mycustom_wp_footer');

function mycustom_wp_footer()
{
?>
	<script type="text/javascript">
		document.addEventListener('wpcf7mailsent', function(event) {
			if ('1123' == event.detail.contactFormId) {
				jQuery('.wpcf7-response-output').show();
				jQuery('.wpcf7-form.sent .form-group, .wpcf7-form.sent p').delay(500).fadeOut('slow').hide(0);

				setTimeout(function() {
					jQuery('.wpcf7-response-output').delay(200).fadeOut('slow').hide(0);
					jQuery('#modal_order_form').modal('hide');
				}, 3000);
				setTimeout(function() {
					jQuery('.wpcf7-form.sent .form-group, .wpcf7-form.sent p').show();
				}, 3200);

			}
		}, false);
	</script>
<?php  }

add_action('wp_ajax_order_loadmore', 'order_loadmore_ajax_fun');
add_action('wp_ajax_nopriv_order_loadmore', 'order_loadmore_ajax_fun');

add_filter('woocommerce_my_account_my_orders_actions', function ($actions) {
	/*$actions['help'] = array(
	  "url" => home_url('/contact'),
	  "name" => "Help"
	);*/
	$actions['view']['name'] = 'View Order';
	return $actions;
}, 10, 2);

//Order Page End

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

//Product Variation Custom Field
add_action('woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3);
add_action('woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2);
add_filter('woocommerce_available_variation', 'load_variation_settings_fields');

function variation_settings_fields($loop, $variation_data, $variation)
{
	woocommerce_wp_text_input(
		array(
			'id'            => "upc_field{$loop}",
			'name'          => "upc_field[{$loop}]",
			'value'         => get_post_meta($variation->ID, 'upc_field', true),
			'label'         => __('UPC', 'woocommerce'),
			'desc_tip'      => true,
			'description'   => __('Enter UPC code.', 'woocommerce'),
			'wrapper_class' => 'form-row form-row-full',
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'            => "msrp_field{$loop}",
			'name'          => "msrp_field[{$loop}]",
			'value'         => get_post_meta($variation->ID, 'msrp_field', true),
			'label'         => __('MSRP', 'woocommerce'),
			'desc_tip'      => true,
			'description'   => __('Enter MSRP.', 'woocommerce'),
			'wrapper_class' => 'form-row form-row-full',
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'            => "fin_sku_field{$loop}",
			'name'          => "fin_sku_field[{$loop}]",
			'value'         => get_post_meta($variation->ID, 'fin_sku_field', true),
			'label'         => __('fin sku', 'woocommerce'),
			'desc_tip'      => true,
			'description'   => __('Enter fin sku.', 'woocommerce'),
			'wrapper_class' => 'form-row form-row-full',
		)
	);
}

function save_variation_settings_fields($variation_id, $loop)
{
	$upc_field = $_POST['upc_field'][$loop];
	$msrp_field = $_POST['msrp_field'][$loop];
	$fin_sku_field = $_POST['fin_sku_field'][$loop];


	if (!empty($upc_field)) {
		update_post_meta($variation_id, 'upc_field', esc_attr($upc_field));
	}else{
		update_post_meta($variation_id, 'upc_field', '');
	}
	if (!empty($msrp_field)) {
		update_post_meta($variation_id, 'msrp_field', esc_attr($msrp_field));
	}else{
		update_post_meta($variation_id, 'msrp_field', '');
	}
	if (!empty($fin_sku_field)) {
		update_post_meta($variation_id, 'fin_sku_field', esc_attr($fin_sku_field));
	}else{
		update_post_meta($variation_id, 'fin_sku_field', '');
	}
}

function load_variation_settings_fields($variation)
{
	$variation['upc_field'] = get_post_meta($variation['variation_id'], 'upc_field', true);
	$variation['msrp_field'] = get_post_meta($variation['variation_id'], 'msrp_field', true);
	$variation['fin_sku_field'] = get_post_meta($variation['variation_id'], 'fin_sku_field', true);
	return $variation;
}

add_filter('wc_bulk_variations_table_cell_output', function ($html, $product_variation) {

	$html = str_replace('Per Unit', '', $html);

	$variation_data = json_decode($product_variation);
	$variation_id = $variation_data->id;

	$taxonomy = 'pa_color';
	$meta = get_post_meta($variation_id, 'attribute_' . $taxonomy, true);
	$term = get_term_by('slug', $meta, $taxonomy);

	$upc_field = get_post_meta($variation_id, 'upc_field', true);
	$upc = ($upc_field) ? $upc_field : 'N/A';

	$price = get_woocommerce_currency_symbol() . number_format((float)$variation_data->price, 2, '.', '');

	$msrp_field = get_post_meta($variation_id, 'msrp_field', true);
	$msrp = ($msrp_field) ? get_woocommerce_currency_symbol() . number_format((float)$msrp_field, 2, '.', '') : '';

	$stock_status = $variation_data->stock_status;
	if ($stock_status == 'instock') {
		$stock_quantity = $product_variation->stock_quantity;
		$stock_html = '<span class="extra_field instock_field_span">' . $stock_quantity . '</span>';
		//$stock_html = '<span class="extra_field instock_field_span"><span>In Stock:</span> ' . $stock_quantity . '</span>';
	} else {
		$stock_html = '<span class="extra_field outofstock_field_span"><span>Out of Stock</span></span><span class="single_notifyme"><a data-varid="'.$variation_id.'" href="javascript:void(0);">Notify Me</a></span>';
	}

	if ($html) {
		if ($variation_id) {
			if(!is_user_logged_in()) {
				$login_link = '<a class="product_detail_display product_login_popup" href="javascript:void(0)">Login to see prices</a>';

				return '<span class="extra_field upc_field_span">' . $upc . ' </span><span class="extra_field upc_field_span">' . $login_link . '</span>' . $html;
			} else {
				return '<span class="extra_field upc_field_span">' . $upc . ' </span><span class="extra_field price_field_span">' . $price . '</span><span class="extra_field msrp_field_span">' . $msrp . '</span>' . $stock_html . '' . $html;
			}
		} else {
			return $html;
		}
	} else {
		return $html . '<span class="product_not_available">Not Available</span>';
	}
}, 10, 2);


add_filter('woocommerce_registration_error_email_exists', 'account_page_login_url_change', 10, 2);
function account_page_login_url_change($html, $email)
{
	$url =  home_url('/login');
	$html = 'An account is already registered with your email address.<a class="showlogin" href="' . $url . '"> Please log in.</a>';
	return $html;
}

function ad_event_data_before_log($event_data, $event_id)
{
	$user = wp_get_current_user();
	$user_name_data = $user->first_name . ' ' . $user->last_name;

	if ($event_id == 4018 || $event_id == 4017) {
		$event_data['TargetUsername'] = $user_name_data;
	}
	if ($event_id == 4015 || $event_id == 4016) {

		$event_data['TargetUsername'] = $user_name_data;

		// Product Brand
		if ($event_data['custom_field_name'] == 'reg_product_brand_select') {

			$newvalue = unserialize($event_data['new_value']);
			if ($newvalue) {
				$brand_array = array();
				foreach ($newvalue as $newvalue_item) {
					$term = get_term_by('id', $newvalue_item, 'product_brand');
					$name = $term->name;
					$brand_array[] = $name;
				}
				$event_data['new_value'] = implode(",", $brand_array);
			}


			$old_value = unserialize($event_data['old_value']);

			if ($old_value) {
				$brand_old_array = array();
				foreach ($old_value as $old_value_item) {
					$term = get_term_by('id', $old_value_item, 'product_brand');
					$name = $term->name;
					$brand_old_array[] = $name;
				}

				$event_data['old_value'] = implode(",", $brand_old_array);
			}
			$event_data['custom_field_name'] = 'Product Brand';
		}
		// Product Category
		if ($event_data['custom_field_name'] == 'reg_product_cat_select') {

			$newvalue = unserialize($event_data['new_value']);
			if ($newvalue) {
				$brand_array = array();
				foreach ($newvalue as $newvalue_item) {
					$term = get_term_by('id', $newvalue_item, 'product_cat');
					$name = $term->name;
					$brand_array[] = $name;
				}
				$event_data['new_value'] = implode(",", $brand_array);
			}


			$old_value = unserialize($event_data['old_value']);

			if ($old_value) {
				$brand_old_array = array();
				foreach ($old_value as $old_value_item) {
					$term = get_term_by('id', $old_value_item, 'product_cat');
					$name = $term->name;
					$brand_old_array[] = $name;
				}

				$event_data['old_value'] = implode(",", $brand_old_array);
			}
			$event_data['custom_field_name'] = 'Product Categories';
		}

		//business type
		if ($event_data['custom_field_name'] == 'business_type') {

			$newvalue = unserialize($event_data['new_value']);
			if ($newvalue) {
				$event_data['new_value'] = implode(",", $newvalue);
			}

			$old_value = unserialize($event_data['old_value']);

			if ($old_value) {
				$event_data['old_value'] = implode(",", $old_value);
			}
			$event_data['custom_field_name'] = 'Business Type';
		}
		if ($event_data['custom_field_name'] == 'business_name') {
			$event_data['custom_field_name'] = 'Business Name';
		}
		if ($event_data['custom_field_name'] == 'ein') {
			$event_data['custom_field_name'] = 'EIN';
		}
		if ($event_data['custom_field_name'] == 'dba') {
			$event_data['custom_field_name'] = 'DBA';
		}
		if ($event_data['custom_field_name'] == 'business_phone') {
			$event_data['custom_field_name'] = 'Business Phone';
		}
		if ($event_data['custom_field_name'] == 'business_url') {
			$event_data['custom_field_name'] = 'Business Website';
		}
		if ($event_data['custom_field_name'] == 'about_your_business') {
			$event_data['custom_field_name'] = 'About Your Business';
		}
		if ($event_data['custom_field_name'] == 'business_type_ecommerce') {
			$event_data['custom_field_name'] = 'Website Domain List';
		}

		//Personal Info

		if ($event_data['custom_field_name'] == 'home_postcode') {
			$event_data['custom_field_name'] = 'Personal Info - Zip Code';
		}
		if ($event_data['custom_field_name'] == 'home_city') {
			$event_data['custom_field_name'] = 'Personal Info - City';
		}
		if ($event_data['custom_field_name'] == 'home_state') {
			$event_data['custom_field_name'] = 'Personal Info - State';
		}
		if ($event_data['custom_field_name'] == 'home_address_1') {
			$event_data['custom_field_name'] = 'Personal Info - Address';
		}
		if ($event_data['custom_field_name'] == 'personal_email') {
			$event_data['custom_field_name'] = 'Personal Info - Email';
		}
	}

	return $event_data;
}
add_filter('wsal_event_data_before_log', 'ad_event_data_before_log', 10, 2);

add_filter('yith_wcwl_action_links', 'yith_wcwl_action_links_func');
function yith_wcwl_action_links_func($action_links)
{
	unset($action_links[1]);
	unset($action_links[2]);
	return $action_links;
}
function ad_woocommerce_image_dimensions()
{
	global $pagenow;

	if (!isset($_GET['activated']) || $pagenow != 'themes.php') {
		return;
	}
	$catalog = array(
		'width'     => '300',   // px
		'height'    => '300',   // px
		'crop'      => array('center', 'top') // New crop options to try.
	);
	/* $single = array(
        'width'     => '600',   // px
        'height'    => '600',   // px
        'crop'      => 1        // true
    );
    $thumbnail = array(
        'width'     => '120',   // px
        'height'    => '120',   // px
        'crop'      => 0        // false
    ); */
	// Image sizes
	update_option('shop_catalog_image_size', $catalog);       // Product category thumbs
	/* update_option( 'shop_single_image_size', $single );      // Single product image
    update_option( 'shop_thumbnail_image_size', $thumbnail );   // Image gallery thumbs */
}
add_action('after_switch_theme', 'ad_woocommerce_image_dimensions', 1);

//cart page
function getCartItemThumbnail($img, $cart_item)
{

	if (isset($cart_item['product_id'])) {
		$product = wc_get_product($cart_item['product_id']);
		if ($product && $product->is_type('variable')) {
			// Return variable product thumbnail instead variation.
			return $product->get_image();
		}
	}
	return $img;
}
//add_filter('woocommerce_cart_item_thumbnail', 'getCartItemThumbnail', 111, 2);

function filter_woocommerce_add_to_cart_item_name_in_quotes($item_name, $product_id)
{

	$variations = wc_get_product($product_id);
	$item_name_array =  $variations->get_variation_attributes();
	$size_name = $item_name_array['attribute_pa_size'];
	$color_name = str_replace("-", " ", $item_name_array['attribute_pa_color']);
	$item_name = ' "Size : ' . strtoupper($size_name) . ', Color : ' . ucwords($color_name) . '"';

	return $item_name;
}
//add_filter ( 'woocommerce_add_to_cart_item_name_in_quotes', 'filter_woocommerce_add_to_cart_item_name_in_quotes', 10, 2 );
add_filter('wc_add_to_cart_message_html', 'ad_custom_added_to_cart_message', 10, 3);

function ad_custom_added_to_cart_message($message, $products, $show_qty)
{
	$count  = 0;
	foreach ($products as $product_id => $qty) {
		$count  += $qty;
	}
	$message = 'products have been added to your cart.';
	return $count . ' ' . $message;
}

add_action('woocommerce_check_cart_items', 'cldws_set_min_total');
function cldws_set_min_total()
{
	// Only run in the Cart or Checkout pages
	if (is_cart() || is_checkout()) {
		global $woocommerce;
		$order_max_transaction_limit = get_field('order_max_transaction_limit', 'option');
		$total = WC()->cart->total;
		if ($order_max_transaction_limit <= $total) {
			wc_add_notice(
				sprintf(
					'<strong>A Maximum of %s %s is required before checking out.</strong>'
						. '<br />Current cart\'s total: %s %s',
					$order_max_transaction_limit,
					get_option('woocommerce_currency'),
					$total,
					get_option('woocommerce_currency')
				),
				'error'
			);
		}
	}
}

function parentIDs($sub_category_id)
{
	static $parent_ids = [];
	if ($sub_category_id != 0) {
		$category_parent = get_term($sub_category_id, 'product_cat');
		$parent_ids[] = $category_parent->term_id;
		parentIDs($category_parent->parent);
	}
	return $parent_ids;
}
//remove add to cart in shop page
//remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
function ad_shop_display_post_meta()
{

	global $product;

	// replace the custom field name with your own
	$product_style = get_post_meta($product->id, 'product_style', true);

	// Add these fields to the shop loop if set
	if (!empty($product_style)) {
		echo '<div class="product-meta"><span class="product-meta-title">Style #</span> ' . $product_style . '</div>';
	}
}
add_action('woocommerce_after_shop_loop_item', 'ad_shop_display_post_meta', 9);

add_filter('loop_shop_columns', 'loop_columns', 999);

if (!function_exists('loop_columns')) {
	function loop_columns()
	{

		if (is_search() || is_page_template('templates/tpl-closeout-page.php') || is_page_template('templates/tpl-best-seller-page.php')) {
			return 4;
		} else {
			return 3;
		}
	}
}

function hide_child_taxonomies($args, $field)
{
	if ('best_seller_category' === $field['_name']) {
		$args['parent'] = 0;
	}
	return $args;
}
//add_filter('acf/fields/taxonomy/query', 'hide_child_taxonomies', 10, 3);

add_action('admin_head', 'ad_admin_custom_css');

function ad_admin_custom_css()
{
	echo '<style>
  	.acf-field.acf-field-taxonomy.acf-field-60d9e7f9a633c {
    display: none;
	} 

	li#toplevel_page_user_view {
		display: none;
	}

	.view_user_admin td, .view_user_admin th  {
		border: 1px solid #f1f1f1;
	 }
	 .view_user_admin td.column-columnname.label-user {
		font-weight: bold;
	}
	.view_user_admin .user-title{
		font-weight: bold;
		font-size: 16px;
		background: #f1f1f1;
	
	}
	.view_user_admin .approve_status{
		margin-top:20px
	}
	body .widefat .column-loginasuser_col {
		width: auto !important;
	}
	body #mwb_smc_export_user_sc_data{
		display: none;
	}

	div.user_access_level {
		background: #ffffff;
		padding: 10px 15px;
		margin-top: 15px;
		border: 1px solid #c3c4c7;
		box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
	}
	div.user_access_level p.submit{
		margin-top : 10px;
		margin-bottom: 0px;
	}
   .status_user_access_message{
	   display:none;
   }
	div.user_access_level .update_success{
		display:block;
		font-weight: 600;
		font-size: 15px;
		color: green;
		margin-top: 0px;
	}
	div.user_access_level .update_fail{
		display:block;
		font-weight: 600;
		font-size: 15px;
		color: #f31201;
		margin-top: 0px;
	}
  </style>';
}

function ad_custom_admin_js() {
    ?>
	<script>
		jQuery(document).ready(function(){
			jQuery('input[type=radio][name=ad_user_site_access]').change(function() {
				var oldUrl = jQuery('.approve_status a').attr("href"); // Get current url
				var newUrl = oldUrl + '&submit_user_site_access='+this.value; // Create new url
				jQuery('.approve_status a').attr("href", newUrl); // Set herf value
			});
		});
	</script>
	<?php
}
add_action('admin_footer', 'ad_custom_admin_js');

function ad_users_column($columns)
{

	unset($columns['mwb_save_cart_column']);
	return $columns;
}
add_filter('manage_users_columns', 'ad_users_column');
/*add_action('wp_logout', 'ad_auto_redirect_after_logout');
function ad_auto_redirect_after_logout()
{
	wp_redirect(home_url());
	exit();
}*/

//add_filter('woocommerce_disable_password_change_notification', '__return_true');
//Password Change Admin Email 
function password_change_email_admin($email, $user, $blogname)
{
	ob_start();

	$header_part = get_template_part('templates-parts/email-header-custom');
?>
	<table id="template_body" width="600" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<td id="body_content" style="background-color:<?php echo esc_attr(get_option('woocommerce_email_body_background_color')) ?>" valign="top">
					<!-- Content -->
					<table width="100%" cellspacing="0" cellpadding="20" border="0">
						<tbody>
							<tr>
								<td style="padding: 48px 48px 32px;" valign="top">
									<div id="body_content_inner" style="color:<?php echo esc_attr(get_option('woocommerce_email_text_color')) ?>; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 16px; line-height: 150%; text-align: left;">
										<p style="margin:0 0 16px">Hello Admin,</p>
										<p style="margin: 0 0 16px;"><?php echo $user->user_email ?> user have changed there account password.</p>
										<p style="margin:0 0 16px"></p>
										<p style="margin:0 0 16px">This is admin notification email of password changed.</p>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<!-- End Content -->
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	$footer_part = get_template_part('templates-parts/email-footer-custom');

	$message = ob_get_clean();

	$email['message'] = $message;
	$email['headers'] = array('Content-Type: text/html; charset=UTF-8');

	return $email;
}
add_filter('wp_password_change_notification_email', 'password_change_email_admin', 10, 3);

function redirect_after_logout($logout_url, $redirect)
{
	return $logout_url . '&amp;redirect_to=' . home_url();
}
add_filter('logout_url', 'redirect_after_logout', 10, 2);

add_shortcode('menu_brand_list', 'menu_brand_list_func');
function menu_brand_list_func()
{
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_brand',
			'hide_empty' => true,
		)
	);
	ob_start();
	if (!empty($terms) && is_array($terms)) {
		ob_start();
	?>
		<ul class="ubermenu-submenu ubermenu-submenu-type-auto ubermenu-submenu-type-stack">
			<?php
			foreach ($terms as $term) {
			?>
				<li class="ubermenu-item ubermenu-item-type-taxonomy ubermenu-item-object-product_brand ubermenu-item-auto ubermenu-item-normal">
					<a class="ubermenu-target ubermenu-item-layout-default ubermenu-item-layout-text_only" href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $term->slug; ?>">
						<?php echo $term->name; ?>
					</a>
				</li>
			<?php

			}
			?>
		</ul>
	<?php
	}
	return ob_get_clean();
}
add_filter('user_row_actions', 'add_view_author_page', 10, 2);

function add_view_author_page($actions, $user)
{
	unset($actions['view']);

	if (is_super_admin($user->ID)) {
		return $actions;
	}

	$href = admin_url('admin.php?page=user_view') . '&user_id=' . $user->ID;
	$actions['add_view_author_page'] = "<a target='_blank' href='$href'>View customer</a>";
	$user_status = get_user_meta($user->ID, 'user_status_ready_for_approval', true);

	if ($user_status != 'Yes') {
		$ready_for_approval_action = add_query_arg(array('action' => 'user_ready_for_approval', 'user' => $user->ID));

		$ready_for_approval_link    = '<a href="' . esc_url($ready_for_approval_action) . '">' . esc_html__('Ready For Approval', 'addify_approve_new_user') . '</a>';

		$actions['ready_for_approval'] = $ready_for_approval_link;
	}
	return ($actions);
}

add_action('admin_menu', 'wpse_91693_register');

function wpse_91693_register()
{
	add_menu_page(
		'User View',
		'User View',
		'manage_options',
		'user_view',
		'userview_render'
	);
}
function userview_render()
{

	$user_id = $_GET['user_id'];
	if ($user_id) {
		$default_admin_url = admin_url('users.php?afapnu-status-query-submit=addify-approve-new-user&apnu_action_email=approve&paged=1&user=' . $user_id);
		$approve_link      = wp_nonce_url($default_admin_url);

		$default_admin_url2 = admin_url('users.php?afapnu-status-query-submit=approve-new-user&apnu_action_email=disapprove&paged=1&user=' . $user_id);
		$disapprove_link    = wp_nonce_url($default_admin_url2);


		$user = get_userdata($user_id);
		$home_address_1  = get_user_meta($user_id, 'home_address_1', true);
		$home_city  = get_user_meta($user_id, 'home_city', true);
		$home_state  = get_user_meta($user_id, 'home_state', true);
		$home_postcode  = get_user_meta($user_id, 'home_postcode', true);

		$personal_email = get_user_meta($user_id, 'personal_email', true);

		$billing_address_1  = get_user_meta($user_id, 'billing_address_1', true);
		$billing_city  = get_user_meta($user_id, 'billing_city', true);
		$billing_state  = get_user_meta($user_id, 'billing_state', true);
		$billing_postcode  = get_user_meta($user_id, 'billing_postcode', true);


		$business_name = get_user_meta($user_id, 'business_name', true);
		$ein = get_user_meta($user_id, 'ein', true);
		$dba = get_user_meta($user_id, 'dba', true);
		$business_url = get_user_meta($user_id, 'business_url', true);

		$business_phone = get_user_meta($user_id, 'business_phone', true);
		$business_email = get_user_meta($user_id, 'business_email', true);
		$business_type_ecommerce = get_user_meta($user_id, 'business_type_ecommerce', true);
		$about_your_business = get_user_meta($user_id, 'about_your_business', true);

		$reg_product_cat_select = get_user_meta($user_id, 'reg_product_cat_select', true);
		if ($reg_product_cat_select) {
			$product_cat_selected = implode(",", unserialize($reg_product_cat_select));
		}

		//user_level
		if (isset($_POST['submit'])) {
			$update_site_access = update_user_meta($_POST['user_id'], 'ad_user_site_access', $_POST['ad_user_site_access']);
			if($update_site_access == true){
				$status_user_access_message = "User Access level updated successfully. ";
				$update_class = "update_success";
			}else{
				//$status_user_access_message = "Error in Update, Please try again. ";
				//$update_class = "update_fail";
				$status_user_access_message = "User Access level updated successfully. ";
				$update_class = "update_success";
			}
			
		}
		$ad_user_site_access = get_user_meta($user_id, 'ad_user_site_access', true);

		if(empty($ad_user_site_access)){
			$ad_user_site_access = 'full_access';
		}
		
		//user_level

		$newvalue = unserialize($reg_product_cat_select);

		if ($newvalue) {
			$cat_array = array();
			foreach ($newvalue as $newvalue_item) {
				$term = get_term_by('id', $newvalue_item, 'product_cat');
				$name = $term->name;
				if ($name) {
					$cat_array[] = $name;
				}
			}

			$reg_product_cat_select = implode(",", $cat_array);
		}

		$reg_product_brand_select = get_user_meta($user_id, 'reg_product_brand_select', true);
		if ($reg_product_brand_select) {
			$product_brand_select = implode(",", unserialize($reg_product_brand_select));

			$brand_list = unserialize($reg_product_brand_select);
			if ($brand_list) {
				$brand_array = array();
				foreach ($brand_list as $newvalue_item) {
					$term = get_term_by('id', $newvalue_item, 'product_brand');
					$name = $term->name;
					if ($name) {
						$brand_array[] = $name;
					}
				}
				$reg_product_brand_select = implode(",", $brand_array);
			}
		}

		$business_type = get_user_meta($user_id, 'business_type', true);
		if ($business_type) {
			$business_type =  unserialize($business_type);
		}
		$business_type = implode(",", $business_type);

	?>
		<div class="wrap view_user_admin">
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>

						<th style="width: 200px;" class="column-columnname" scope="col"> Field Name </th>
						<th class="column-columnname" scope="col">Value</th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td colspan="2" class="column-columnname user-title"><strong>Personal Info</strong></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">First name</td>
						<td class="column-columnname"><?php echo $user->first_name; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Last name</td>
						<td class="column-columnname"><?php echo $user->last_name; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Personal Email</td>
						<td class="column-columnname"><?php echo $personal_email; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Home Address</td>
						<td class="column-columnname"><?php echo $home_address_1; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">City</td>
						<td class="column-columnname"><?php echo $home_city; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">State</td>
						<td class="column-columnname"><?php echo $home_state; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Zip</td>
						<td class="column-columnname"><?php echo $home_postcode; ?></td>
					</tr>
					<tr>
						<td colspan="2" class="column-columnname user-title"><strong>Business Info</strong></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Business Name</td>
						<td class="column-columnname"><?php echo $business_name; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">EIN</td>
						<td class="column-columnname"><?php echo $ein; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">DBA</td>
						<td class="column-columnname"><?php echo $dba; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Business Website</td>
						<td class="column-columnname"><?php echo $business_url; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Business Email</td>
						<td class="column-columnname"><?php echo $business_email; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Business Phone</td>
						<td class="column-columnname"><?php echo $business_phone; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Business Address</td>
						<td class="column-columnname"><?php echo $billing_address_1; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">City</td>
						<td class="column-columnname"><?php echo $billing_city; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">State</td>
						<td class="column-columnname"><?php echo $billing_state; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Zip</td>
						<td class="column-columnname"><?php echo $billing_postcode; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Business Type</td>
						<td class="column-columnname"><?php echo $business_type; ?></td>
					</tr>
					<?php if ($business_type_ecommerce) { ?>
						<tr>
							<td class="column-columnname label-user">Website Domain List</td>
							<td class="column-columnname"><?php echo $business_type_ecommerce; ?></td>
						</tr>
					<?php } ?>
					<tr>
						<td class="column-columnname label-user">About your business</td>
						<td class="column-columnname"><?php echo $about_your_business; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Brands of Interest</td>
						<td class="column-columnname"><?php echo $reg_product_brand_select; ?></td>
					</tr>
					<tr>
						<td class="column-columnname label-user">Categories of Interest</td>
						<td class="column-columnname"><?php echo $reg_product_cat_select; ?></td>
					</tr>
				</tbody>
			</table>
			<?php //user_level ?>
			<div class="user_access_level">
			<form id="user_access_level_title" method="post"> 			
			<h3 class="user_access_level_title">Please select user site access level.</h3>
			  <input type="radio" id="full_access" name="ad_user_site_access" <?php echo ($ad_user_site_access =='full_access')? 'checked':'' ?> value="full_access">
			  <label for="html">Full Access</label><br>
			  <input type="radio" id="limited_access" name="ad_user_site_access" <?php echo ($ad_user_site_access =='limited_access')? 'checked':'' ?> value="limited_access">
			  <label for="css">Limited Access ( Not able to access Price, Add to cart, Checkout, Quick order page)</label><br>
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
			<p class="submit"><input type="submit" name="submit" id="submit_user_site_access" class="button button-primary" value="Save"></p>
			<p class="status_user_access_message <?php echo $update_class; ?>"><?php echo $status_user_access_message; ?></p>
			</form>
			</div>
			<?php //user_level ?>
			<div class="approve_status">
				<?php $apnu_user_status = get_user_meta($user->ID, 'apnu_new_user_status', true);

				$user_status = get_user_meta($user->ID, 'user_status_ready_for_approval', true);

				if ($user_status != 'Yes') {

					$ready_for_approval_link = admin_url('users.php?action=user_ready_for_approval&user=' . $user->ID);

				?>

					<a class="button button-primary" href="<?php echo $ready_for_approval_link; ?>">Ready For Approval</a>
				<?php	}

				if ('pending' == $apnu_user_status) { ?>

					<a class="button button-primary" href="<?php echo $approve_link; ?>">User Approve</a>
					<a class="button button-primary" href="<?php echo $disapprove_link; ?>">User Disapprove</a>

				<?php } elseif ('approved' == $apnu_user_status) { ?>

					<a class="button button-primary" href="<?php echo $disapprove_link; ?>">User Disapprove</a>

				<?php } elseif ('disapproved' == $apnu_user_status) { ?>

					<a class="button button-primary" href="<?php echo $approve_link; ?>">User Approve</a>

				<?php }

				?>

			</div>
		</div>
	<?php
	}
}

//User ready for approval:

add_filter('manage_users_columns', 'ad_add_new_user_column');

function ad_add_new_user_column($columns)
{
	$columns['ready_for_approval'] = 'Ready For Approval';
	return $columns;
}

add_filter('manage_users_custom_column', 'ad_add_new_user_column_content', 10, 3);

function ad_add_new_user_column_content($content, $column, $user_id)
{

	if ('ready_for_approval' === $column) {
		$user_status = get_user_meta($user_id, 'user_status_ready_for_approval', true);
		$content = $user_status;
	}
	return $content;
}
add_action('load-users.php', 'ad_users_page_loaded');
function ad_users_page_loaded()
{

	if (isset($_GET['action']) && $_GET['action'] === 'user_ready_for_approval') {

		$userid = $_GET['user'];
		if ($userid) {
			update_user_meta($userid, 'user_status_ready_for_approval', 'Yes');
		}
	}

	//user_level
	if (isset($_GET['submit_user_site_access'])) {
		$userid = $_GET['user'];
		update_user_meta($userid, 'ad_user_site_access', $_GET['submit_user_site_access']);
	}
	//user_level
}

add_filter('woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');
function wc_refresh_mini_cart_count($fragments)
{
	ob_start();
	$items_count = WC()->cart->get_cart_contents_count();
	?>
	<span id="mini-cart-count" class="cart-num"><?php echo $items_count ? $items_count : '0'; ?></span>
	<?php
	$fragments['#mini-cart-count'] = ob_get_clean();
	return $fragments;
}

add_filter('pa_size_row_actions', function ($actions, $tag) {
	unset($actions['view']);
	unset($actions['delete']);
	return $actions;
}, 10, 2);

add_filter('relevanssi_content_to_index', 'rlv_index_variation_skus', 10, 2);
function rlv_index_variation_skus($content, $post)
{
	if ('product' === $post->post_type) {
		$args       = array(
			'post_parent'    => $post->ID,
			'post_type'      => 'product_variation',
			'posts_per_page' => -1
		);
		$variations = get_posts($args);
		if (!empty($variations)) {
			foreach ($variations as $variation) {
				$sku      = get_post_meta($variation->ID, '_sku', true);
				$upc_field  = get_post_meta($variation->ID, 'upc_field', true);
				$fin_sku_field   = get_post_meta($variation->ID, 'fin_sku_field', true);

				$content .= " $sku";
				$content .= " $upc_field";
				$content .= " $fin_sku_field";
			}
		}
	}
	return $content;
}

add_filter('wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2);

function change_attachement_image_attributes($attr, $attachment)
{

	$parent = get_post_field('post_parent', $attachment);

	$type = get_post_field('post_type', $parent);
	if ($type != 'product') {
		return $attr;
	}

	$title = get_post_field('post_title', $parent);

	$attr['alt'] = $title;
	return $attr;
}

// start variations_table
add_filter('wc_bulk_variations_table_cell_output', function ($html, $product_variation) {

	if (empty($product_variation->attributes)) {
		$color_class = 'heading';
	} else {
		$color_class = $product_variation->attributes['pa_color'];
		$color_name = woo2_helper_attribute_name('pa_color', $product_variation->attributes['pa_color']);
		//$size_name = woo2_helper_attribute_name('pa_size', $product_variation->attributes['pa_size'] );
	}

	return '<span data-cname="' . $color_name . '" class="color_column color_' . $color_class . '">' . $html . '</span>';
}, 10, 2);
add_filter('wc_bulk_variations_table_row_attributes', function ($attributes) {
	$attributes['class'] .= 'custom-table-row-class';
	$attributes['data-csize'] .= woo2_helper_attribute_name('pa_size', str_replace('product-row-', ' ', $attributes['id']));
	return $attributes;
});

add_action('wc_bulk_variations_table_before_get_data', 'ad_bulk_variations_sw');

function ad_bulk_variations_sw()
{
	global $product;

	$available_variations = $product->get_available_variations();
	$parent_product_img_id = $product->get_image_id();
	$all_color_list = array();
	$all_color_list_stock = array();
	foreach ($available_variations as $available_variation) {
		$all_color_list[$available_variation['variation_id']] = $available_variation['attributes']['attribute_pa_color'];
		if (is_int($available_variation['max_qty'])) {
			$all_color_list_stock[$available_variation['attributes']['attribute_pa_color']] += intval($available_variation['max_qty']);
		}
		
	}

	$all_color_list = array_unique($all_color_list);
	$count = 1;
	$count_more = 0;
	echo '<div class="product_list_section show_viewmore">';
	echo '<div class="color_box">';
?>
	<?php if(is_user_logged_in()) { ?>
		<div class="product_details_msg" data-variations-count="<?php echo count($available_variations); ?>">
			<span class="div_close"><i class="fa fa-times" aria-hidden="true"></i></span>
			<h4>New! Batch "Add-to-Cart" Feature</h4>
			<p>Now, you can easily toggle between colors while selecting quantities. We'll automatically save your quantities under each color.</p>
			<p>Select "Add to Cart," and all your sizes and colors will be added to your cart at once.</p>
		</div>
	<?php } ?>
	<?php
	echo '<h5>1. Select Color: <span class="selected_color_name"></span></h4><ul class="list-unstyled list-inline s_colors_ul">';
	$total_items = count($all_color_list);
	$total_items_more = $total_items - 12;
	foreach ($all_color_list as $key => $all_color_list_single) {
		$variationobj = new WC_Product_Variation($key);
		$v_stock_class = '';
		$v_in_stock_class = '';
		$hide_class_less = '';
		//$v_stock = $variationobj->get_stock_quantity();

		if ($count_more < 12) {
			$hide_class_less = 'less_color_items';
		}

		if($all_color_list_stock[$all_color_list_single] == 0){
			$v_stock_class = 'color_out_of_stock';
		}else{
			$v_in_stock_class = 'color_in_stock';
			$count_more++;
		}
		if ($count > 12) {
			$hide_class = 'more_color_items';
		}


		$image_id = $variationobj->get_image_id();
		if ($image_id == 11668 || $image_id == 67550 || $image_id == 82327) {
			if(get_post_meta($key, 'aws_url_field', true)){
				$image_array = array(get_post_meta($key, 'aws_url_field', true));
			} else {
				$image_array = wp_get_attachment_image_src($parent_product_img_id, 'shop_thumbnail');
			}
		} else {
			if(get_post_meta($key, 'aws_url_field', true)){
				$image_array = array(get_post_meta($key, 'aws_url_field', true));
			} else {
				$image_array = wp_get_attachment_image_src($image_id, 'shop_thumbnail');
			}
		}

		if ($image_id == 11668 || $image_id == 67550 || $image_id == 82327) {
			if(get_post_meta($key, 'aws_url_field', true)){
				$image_full = get_post_meta($key, 'aws_url_field', true);
			} else {
				$image_full = wp_get_attachment_image_src($parent_product_img_id, 'full');
			}
		} else {
			if(get_post_meta($key, 'aws_url_field', true)){
				$image_full = get_post_meta($key, 'aws_url_field', true);
			} else {
				$image_full = wp_get_attachment_image_src($image_id, 'full');
			}
		}



		/*echo '<pre>';
		echo $image_id;
		print_r($image_full);
		echo '<pre>';*/

		if (strpos($image_full[0], '_sw') == true) {
			$gallery_image = str_replace("_sw", "-100x100", $image_full[0]);
		} else {
			$gallery_image = $image_array[0];
		}
		if (strpos($gallery_image, 'EBY_') == true) {
			$gallery_image = str_replace("EBY_", "HBI_", $gallery_image);
		}

		$lastElement = end(explode('/', $gallery_image));
		$parts = explode('-', $lastElement);
		if(count($parts) > 1) {
			$last = array_pop($parts);
			$final_parts = array(implode('-', $parts), $last);
			$final_image = $final_parts[0];
		} else {
			$last = $lastElement;
			$final_parts = explode('.', $last);
			$final_image = $final_parts[0];
		}

		$taxonomy = 'pa_color';
		$meta = get_post_meta($key, 'attribute_' . $taxonomy, true);
		$term = get_term_by('slug', $meta, $taxonomy);
		$image_tag = $variationobj->get_image('shop_thumbnail');
		$aws_image = get_post_meta($key, 'aws_url_field', true);
		

		if (!empty($aws_image)) {
			$image_tag ="<img src='".$aws_image."' class='attachment-shop_thumbnail size-shop_thumbnail' />";
			$final_image2 = explode('.', $lastElement); 
			$final_image = $final_image2[0];
		}
		else {
			if ($image_id == 11668 || $image_id == 67550 || $image_id == 82327) {
				$image_tag = $product->get_image('shop_thumbnail');
			} else {
				$image_tag = $variationobj->get_image('shop_thumbnail');
			}
		}

		echo '<li class="' . $hide_class .' '.$hide_class_less.' '.$v_stock_class.' '.$v_in_stock_class.'"><a data-galleryselect="' . $final_image . '" data-colorname="' . $term->name . '" data-count="' . $count . '" data-colorid = "color_' . $all_color_list_single . '" class="action_color" href="javascript:void(0);">' . $image_tag . '</a><span class="color_title">' . $term->name . '</span></li>';
		$count++;
		
	}
	echo '</ul>
	</div>';
	if ($total_items > 12) { ?>
		<div class="more-color text-center">
			<button type="button" class="btn btn-link view-more-color">View <?php //echo $total_items_more; 
																			?> More</button>
		</div>
<?php }
}

add_action('woocommerce_after_single_product_summary', 'ad_bulk_variations_sw_end');
function ad_bulk_variations_sw_end()
{
	echo '</div>';
}
//End variations_table

//Quick Order
add_action('wp_ajax_quick_woocommerce_ajax_add_to_cart', 'quick_woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_quick_woocommerce_ajax_add_to_cart', 'quick_woocommerce_ajax_add_to_cart');

function quick_woocommerce_ajax_add_to_cart()
{
	$products = array();
	parse_str($_POST['data_form'], $products);

	$success  = 0;
	$totalqty = 0;
	foreach ($products['pr'] as $product) {

		$qty          = isset($product['qty']) ? $product['qty'] : 0;
		$product_id   = $product['product_id'];
		$variation_id = isset($product['variation_id']) ? $product['variation_id'] : '';
		if (empty($qty) || empty($product_id) || (isset($product['variation_id']) && $product['variation_id'] == '')) {
			continue;
		}

		$attributes = isset($product['attributes']) ? $product['attributes'] : null;
		$status     = WC()->cart->add_to_cart($product_id, $qty, $variation_id, $attributes, null);
		if ($status) {
			$success++;
			$totalqty = $totalqty + $qty;
		}
	}

	if ($success > 0) {
		$response = array(
			'success' => true,
			'totalqty' => $totalqty,

		);
	} else {

		$response = array(
			'success' => false,
			'totalqty' => $totalqty,
		);
	}
	wp_send_json($response);
	wp_die();
}
//End Quick order

// Yith wishlist

function yith_wcwl_redirect_after_delete_wishlist_custom($redirect_url)
{
	return $redirect_url = get_site_url() . '/my-account/my-lists/';
};

add_filter('yith_wcwl_redirect_after_delete_wishlist', 'yith_wcwl_redirect_after_delete_wishlist_custom');

function yith_wcwl_create_wishlist_title_label_cutom($label)
{
	return $label = 'Create New list';
}

add_filter('yith_wcwl_create_wishlist_title_label', 'yith_wcwl_create_wishlist_title_label_cutom');

add_action('template_redirect', 'define_default_payment_gateway');
function define_default_payment_gateway()
{
	if (is_checkout() && !is_wc_endpoint_url()) {
		$default_payment_id = 'usaepaytransapi';
		WC()->session->set('chosen_payment_method', $default_payment_id);
	}
}

add_action('wp_ajax_ad_woocommerce_remove_cart_items', 'ad_woocommerce_remove_cart_items_func');
add_action('wp_ajax_ad_woocommerce_remove_cart_items', 'ad_woocommerce_remove_cart_items_func');

function ad_woocommerce_remove_cart_items_func()
{
	add_filter( 'woocommerce_cart_needs_shipping', '__return_false', 10 );
	$key_to_remove = $_POST['data_form'];
	$success  = 0;
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

		$cart_item_id = $cart_item['variation_id'];

		if (in_array($cart_item_id, $key_to_remove)) {
			WC()->cart->remove_cart_item($cart_item_key);
			$success++;
		}
	}

	if ($success > 0) {
		$response = array(
			'success' => true,
		);
	} else {
		$response = array(
			'success' => false,
		);
	}
	wp_send_json($response);
	wp_die();
}

add_action('wp_ajax_ad_woocommerce_remove_cart_items_all', 'ad_woocommerce_remove_cart_items_all_func');
add_action('wp_ajax_ad_woocommerce_remove_cart_items_all', 'ad_woocommerce_remove_cart_items_all_func');

function ad_woocommerce_remove_cart_items_all_func()
{
	add_filter( 'woocommerce_cart_needs_shipping', '__return_false', 10 );
	$key_to_remove = $_POST['data_form'];
	$success  = 0;

	if($key_to_remove == 'remove_all'){
		global $woocommerce;
		$woocommerce->cart->empty_cart();
		$success = 1;
	}

	if ($success > 0) {
		$response = array(
			'success' => true,
		);
	} else {
		$response = array(
			'success' => false,
		);
	}
	wp_send_json($response);
	wp_die();
}

add_filter( 'https_ssl_verify', '__return_true', PHP_INT_MAX );
 
add_filter( 'http_request_args', 'http_request_force_ssl_verify', PHP_INT_MAX );
 
function http_request_force_ssl_verify( $args ) {
 
        $args[ 'sslverify' ] = true;
 
        return $args;
}

function remove_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'remove_shipping_calc_on_cart', 99 );

function filter_need_shipping ($val) {
	$prevent_after_add = WC()->cart->prevent_recalc_on_add_to_cart;
	return $val && !$prevent_after_add;
}

add_filter( 'woocommerce_cart_needs_shipping', 'filter_need_shipping' );

function mark_cart_not_to_recalc ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
	WC()->cart->prevent_recalc_on_add_to_cart = true;
}

add_action('woocommerce_add_to_cart', 'mark_cart_not_to_recalc', 10, 6);

//Remove Items form cart
function orb_check_for_out_of_stock_products() {
	if ( WC()->cart->is_empty() ) {
		return;
	}
	
	$removed_products = [];
	
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$product_obj = $cart_item['data'];
	
		if ( ! $product_obj->is_in_stock() ) {
			WC()->cart->remove_cart_item( $cart_item_key );
			$removed_products[] = $product_obj;
		}
	}
	
	if (!empty($removed_products)) {
		wc_clear_notices(); 
	
		foreach ( $removed_products as $idx => $product_obj ) {
			$product_name = $product_obj->get_title();
			$msg = sprintf( __( "The product '%s' was removed from your cart because it is now out of stock. Sorry for any inconvenience caused.", 'woocommerce' ), $product_name);
			wc_clear_notices(); 
			wc_add_notice( $msg, 'error' );
		}
	}
	
	}
add_action('woocommerce_before_cart', 'orb_check_for_out_of_stock_products');

if (!wp_next_scheduled('relevanssi_build_index')) {
	wp_schedule_event( time(), 'daily', 'relevanssi_build_index' );
}

add_filter( 'auth_cookie_expiration', 'ad_keep_me_logged_in' );

function ad_keep_me_logged_in( $expirein ) {
    return 31556926; // 1 year in seconds
}

// Filter the excel data 
function ad_filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

add_filter( 'big_image_size_threshold', '__return_false' );

function ad_formatted_shipping_address($order)
{
    return
        $order->shipping_first_name . '  ' . 
        $order->shipping_last_name . ', ' .
		$order->shipping_company . ', ' .
		$order->shipping_address_1 . ', ' . 
       // $order->shipping_address_2 . ' ' .
        $order->shipping_city      . ', ' .
        $order->shipping_state     . ' ' .
        $order->shipping_postcode;
}

function ad_formatted_billing_address($order)
{
    return
		$order->billing_first_name . '  ' . 
		$order->billing_last_name . ', ' . 
		$order->shipping_company . ', ' .
        $order->billing_address_1 . ', ' . 
       // $order->billing_address_2 . ' ' .
        $order->billing_city      . ', ' .
        $order->billing_state     . ' ' .
        $order->billing_postcode;
}

function ad_stock_info_error($error){
    global $woocommerce;
    foreach ($woocommerce->cart->cart_contents as $cart_item_key => $item) {
        $product_id = isset($item['variation_id']) ? $item['variation_id'] : $item['product_id'];
        $product = new \WC_Product_Factory();
        $product = $product->get_product($product_id);

        if ($item['quantity'] > $product->get_stock_quantity()){
			WC()->cart->set_quantity( $cart_item_key, $product->get_stock_quantity(), true );
            $name = $product->get_name();
            //$error = 'Sorry, we do not have enough "'.$name.'" in stock to fulfill your order. Please edit your cart and try again. We apologize for any inconvenience caused.';
			$error = 'Sorry, we do not have enough "'.$name.'" in stock to fulfill your order ('.$product->get_stock_quantity().' available). Quantity has been changed in cart '.$item['quantity'].' to '. $product->get_stock_quantity();
            return $error;
        }
    }
}

add_action( 'template_redirect', 'ad_callback' ); 

function ad_callback() {
  	if ( is_cart()) {
    	add_filter( 'woocommerce_add_error', 'ad_stock_info_error' );
	}
}

function ad_get_cat_count($cat_id)
{
	$args = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => array($cat_id)
			)
		),
		'fields' => 'ids',
		'posts_per_page' => -1,
		'post_type' => 'product',
		'post_status' => 'publish',
		'meta_key' => '_stock_status',
		'meta_value' => 'instock',
		'meta_compare' => '='
	);

	$wp_query = new WP_Query($args);

	wp_reset_postdata();

	return $wp_query->found_posts;
}


add_filter( 'wp_get_nav_menu_items', 'nav_remove_empty_category_menu_item',10, 3 );

function nav_remove_empty_category_menu_item ( $items, $menu, $args ) {
    if ( ! is_admin() ) {
      //global $wpdb;
      //$nopost = $wpdb->get_col( "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE count = 0" );
	  
      foreach ( $items as $key => $item ) {
       // if ( ( 'taxonomy' == $item->type ) && ( in_array( $item->object_id, $nopost ) ) ) {
		if ( ( 'taxonomy' == $item->type ) ) {
			$post_count = ad_get_cat_count($item->object_id);
				if ($post_count == 0) {
					unset( $items[$key] );
				}
			//print_r($item->object_id);
        }
      }
    }
    return $items;
}

add_filter( 'woocommerce_product_add_to_cart_text', 'ad_change_select_options_button_text', 9999, 2 );

function ad_change_select_options_button_text( $label, $product ) {
   if ( $product->is_type( 'variable' )  &&  !$product->is_in_stock() ) {
      return 'Out Of Stock';
   }
   return $label;
}

add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'ad_hide_out_of_stock_exception_page' );

function ad_hide_out_of_stock_exception_page( $hide ) {
   if ( is_single() ) {
      $hide = 'no';
   }   
   return $hide;
}


add_action('cwg_instock_mail_send_as_copy', 'ad_send_subscription_copy_to_recipients', 10, 3);

function ad_send_subscription_copy_to_recipients( $to, $subject, $message) {

	$variation_id = $_REQUEST['variation_id'];
	$product_id = $_REQUEST['product_id'];
	$product_style = get_post_meta($product_id, 'product_style', true);
	$product_title = get_the_title($variation_id);
	
	$variation = new WC_Product_Variation( $variation_id );
	$variation_attributes = $variation->get_variation_attributes();
	$variation_size = str_replace("-", " ", $variation_attributes['attribute_pa_size']);
	$variation_color = str_replace("-", " ", $variation_attributes['attribute_pa_color']);
	
	$var_image_id = $variation->get_image_id();

	$var_image_url =  wp_get_attachment_url($var_image_id);

	if (strpos($var_image_url, '_sw') == true) {
		$var_image_url = str_replace("_sw", "-300x300", $var_image_url);
	}

	if (strpos($var_image_url, 'EBY_') == true) {
		$var_image_url = str_replace("EBY_", "HBI_", $var_image_url);
	}

	if (@getimagesize($var_image_url)) {
		$thumbnail = '<img width="64" src="' . $var_image_url . '">';
	} else {
		$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
		$thumbnail = '<img width="64" src="' . $image_array[0] . '">';
	}

	if ($var_image_id == 11668 || $var_image_id == 67550 || $var_image_id == 82327) {
		$image_coming_soon = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'shop_thumbnail');
		$thumbnail = '<img width="64" src="' . $image_coming_soon[0] . '">';
	}

	$user_email = $_REQUEST['user_email'];

	$get_option = get_option('cwginstocksettings');
	
	$subject = 'Product Notify Me - Admin Notification';

	ob_start();
 	wc_get_template( 'emails/email-header.php' );
	echo $message = 'Hello,  
	<br>
	<br>
	Subscriber Email : '.$user_email.'
	<br>
	<br>
	<table style="border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0;" cellspacing="0" cellpadding="6"><tr><td>'.$thumbnail.'</td><td><div><strong>'.$product_title.'</strong></div></td></tr></table>
	<br>
	Style # : <strong>'.$product_style.'</strong>
	<br>
	Size : <strong>'.strtoupper($variation_size).'</strong>
	<br>
	Color :<strong> '.ucwords($variation_color).'</strong>
	<br>
	<br>
	The Apparel Direct Team';

	wc_get_template( 'emails/email-footer.php' );
	$message = ob_get_clean();
	if ($user_email) {
		$get_recipients = isset($get_option['subscription_copy_recipients']) && !empty($get_option['subscription_copy_recipients']) ? $get_option['subscription_copy_recipients'] : false;
		if ($get_recipients) {
			$explode_data = explode(',', $get_recipients);
			if (is_array($explode_data) && !empty($explode_data)) {
				foreach ($explode_data as $each_mail) {
					$mailer = WC()->mailer();
					$sendmail = $mailer->send($each_mail, $subject, $message);
				}
			}
		}
	}
}

//user_level
add_filter( 'woocommerce_get_price_html', 'ad_hide_price_addcart', 9999, 2 );
function ad_hide_price_addcart( $price, $product ) {

	$userid = get_current_user_id();
	$ad_user_site_access  = get_user_meta($userid, 'ad_user_site_access', true );
	if($ad_user_site_access == 'limited_access'){
      $price = '';
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
      //remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
   }
   return $price;
}

add_action('template_redirect', 'add_limited_access_user_redirect');
function add_limited_access_user_redirect() {
	$userid = get_current_user_id();
	$ad_user_site_access  = get_user_meta($userid, 'ad_user_site_access', true );
	if($ad_user_site_access == 'limited_access'){
		if( is_cart() || is_checkout() || is_page('orders')){
			wp_redirect(home_url());
			exit;
		}
		
   }
}
//user_level

add_action( 'pre_get_posts',  'set_posts_per_page'  );
function set_posts_per_page( $query ) {

  global $wp_the_query;

  if ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_search() ) ) {
    $query->set( 'posts_per_page', 12 );
    $query->set( 'meta_key', '_stock_status' );
    $query->set( 'meta_value', 'instock' );

    /*$query->set( 'tax_query', array(
    	'relation' => 'OR',
    	array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => array('outofstock'),
            'operator' => 'NOT IN'
        ),
    ) );*/
    //$query->set( 'paged', $page );
  }
  // else if(! is_admin() && !is_front_page() && !is_product()) {
  // 	$query->set( 'posts_per_page', 48);
  // }
  
  // Etc..

  return $query;
}

add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
	// $cols contains the current number of products per page based on the value stored on Options –> Reading
	// Return the number of products you wanna show per page.
	$cols = 48;
	return $cols;
}

//wp mail content type html
function wpdocs_set_html_mail_content_type() {
	return 'text/html';
}

// Check cart items conditionally displaying an error notice and avoiding checkout
add_action( 'woocommerce_check_cart_items', 'check_cart_items_conditionally' );

function check_cart_items_conditionally() {

	$prevent_checkout = false;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = $cart_item['data'];
		$product_id = $product->get_id();
		$price = get_post_meta($product_id, '_regular_price', true);
		
		if($price < 0 || $price == 0 || $price == 0.00) {
			$prevent_checkout = true;
			break;
		}
	}
	
    // if ( ( WC()->cart->get_subtotal() == 0 && WC()->cart->get_cart_contents_count() > 0 )) {
	if($prevent_checkout == true) {
    	if (!is_cart()) {

			wc_clear_notices(); 
			//wc_add_notice( printf( __(' ', 'woocommerce') ), 'error' );

    		wc_add_notice( printf( __('Sorry! There\'s an iss ue with items in your order. <br> One or more items in your cart has a price of $0.00. Please contact us for assistance.', 'woocommerce') ), 'error' );

    		if (is_checkout()) {
	    		global $current_user;
	      		get_currentuserinfo();

	    		ob_start();
				do_action( 'woocommerce_email_header', $email_heading );
				
				echo '<table width="100%" border="1" cellpadding="10" style="border-collapse: collapse;"><tbody>';
				echo '<tr><th align="left">Date & time:</th><td align="left">'.date("m-d-Y H:i:s").'</td></tr>';
				echo '<tr><th align="left">Username:</th><td align="left">'.$current_user->user_login.'</td></tr>';
				echo '<tr><th align="left">Email:</th><td align="left">'.$current_user->user_email.'</td></tr>';
				echo '<tr><th align="left">Products:</th><td align="left">';
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$item_name = $cart_item['data']->get_title();
					$product = $cart_item['data'];
					$product_id = $product->get_id();
					$price = WC()->cart->get_product_price($product);
					echo '<a href="'.get_permalink($product_id).'">'.$item_name.' ('.$price.')</a><br/>';
				}
				echo '</td></tr>';
				echo '</tbody></table>';

				do_action( 'woocommerce_email_footer', $email );
				$message = ob_get_clean();

				add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

				$to = get_option('admin_email');
			    $subject = "Apparel Direct - Email notification for 0 price order";
			     
			    $retval = wp_mail($to, $subject, $message);

				remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
			}
    	}
       
    }
}

add_filter( 'gettext', function( $translation, $text, $domain ){
if( $domain == 'woocommerce' ){    
       $translation =  str_replace('There are some issues with the items in your cart. Please go back to the cart page and resolve these issues before checking out.', '', $text);    
}   
return $translation;}, 10, 3 );

add_action('wp_head', 'my_custom_styles', 100);
function my_custom_styles() {
	if(!is_user_logged_in()) {
		echo "<style>.wcbvp-cart .wcbvp-total-wrapper{display: none;}</style>";
	}
}
function action_woocommerce_shortcode_products_loop_no_results( $attributes ) {
    echo __( ' <div class="error">There are currently no products in this collection
                <p class="return-to-home">
                    <a class="button wc-backward" href="https://appareldirectdistributor.com">
                         Return to home     </a>
                </p> </div>', 'woocommerce' );
}
add_action( 'woocommerce_shortcode_products_loop_no_results', 'action_woocommerce_shortcode_products_loop_no_results', 10, 1 );

add_action( 'user_register', 'rudr_sync_wp_users_to_mailchimp', 10, 2 );

function rudr_sync_wp_users_to_mailchimp( $user_id, $userdata ) {
	
	$all_meta_for_user = get_user_by('id', $user_id);

	$list_id = '55c794b002';
	$api = '538f38112d52ad80a19b112ca5646618-us6';
	$email =   $all_meta_for_user->user_email;
	
	$response = wp_remote_request( 
		'https://' . substr($api,strpos($api,'-')+1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($email)),
		array(
			'method' => 'PUT',
	 		'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:'. $api )
			),
			'body' => json_encode(
				array(
					'email_address' => $email,
					'merge_fields' => array('FNAME' => $_POST['billing_first_name'],
												'LNAME' => $_POST['billing_last_name'],
												'ADDRESS' => $_POST['billing_address_1'],
												'PHONE' => $_POST['business_phone'] 
											),					
					'full_name' => $_POST['billing_first_name'].' '.$_POST['billing_last_name'],
					'status' => 'subscribed',
				)
			)
		)
	);


}



/*11-08-2023*/

add_action( 'woocommerce_variation_options_pricing', 'bbloomer_add_custom_field_to_variations', 10, 3 );
 
function bbloomer_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
   woocommerce_wp_text_input( array(
'id' => 'aws_url_field[' . $loop . ']',
'name' => 'aws_url_field[' . $loop . ']',
'class' => 'short',
'description'   => __('Enter AWS Variation Image.', 'woocommerce'),
'label' => __( 'AWS Variation Image', 'woocommerce' ),
'value' => get_post_meta( $variation->ID, 'aws_url_field', true )
   ) );
}

// 2. Save custom field on product variation save
 
add_action( 'woocommerce_save_product_variation', 'bbloomer_save_custom_field_variations', 10, 2 );
 
function bbloomer_save_custom_field_variations( $variation_id, $i ) {
   $custom_field = $_POST['aws_url_field'][$i];
   if ( isset( $custom_field ) ) update_post_meta( $variation_id, 'aws_url_field', esc_attr( $custom_field ) );
}
 
// -----------------------------------------
// 3. Store custom field value into variation data
 
add_filter( 'woocommerce_available_variation', 'bbloomer_add_custom_field_variation_data' );
 
function bbloomer_add_custom_field_variation_data( $variations ) {
   $variations['aws_url_field'] = '<div class="woocommerce_custom_field">Custom Field: <span>' . get_post_meta( $variations[ 'variation_id' ], 'aws_url_field', true ) . '</span></div>';
   return $variations;
}



remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
    function woocommerce_template_loop_product_thumbnail() {
        echo woocommerce_get_product_thumbnail();
    } 
}
if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {   
    function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
        global $post, $woocommerce;
        $output = '';

        // Get the custom field value for the external image URL
    	$custom_image_url = get_post_meta($post->ID, 'cdn_featured_image', true);

    	if($custom_image_url) {
    		//$final_image_url = aq_resize($custom_image_url, '300', '300', false);
    		$output .= '<img width="300" height="300" src="'.$custom_image_url.'" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="" decoding="async" sizes="(max-width: 300px) 100vw, 300px">';
    	} else if ( has_post_thumbnail() ) {               
            $output .= get_the_post_thumbnail( $post->ID, $size );
        } else {
             $output .= wc_placeholder_img( $size );
        }                   
        return $output;
    }
}

add_filter( 'wpgs_show_featured_image_in_gallery', '__return_false', 20 );






// Define your custom function to handle the variation updated notification
/*function my_variation_updated_notification($variation_id, $product_id) {
    // Get the variation object
    $variation = wc_get_product($variation_id);

    // Get the product name
    $product_name = get_the_title($product_id->get_parent_id());
    $product_url = get_permalink($product_id->get_parent_id());
    $current_datetime = current_time('mysql');

    // Convert the date and time to the desired format
    $formatted_datetime = date('d-m-Y H:i:s', strtotime($current_datetime));

    // Initialize an array to store information about variations with price 0
    $variations_with_price_zero = [];

    // Check if the variation price is 0
    if ($variation && $variation->get_price() == 0 || $variation->get_price() == 0.0 || $variation->get_price() == 0.00 || $variation->get_price() < 1) {
        $current_user = wp_get_current_user();
        $variation_info = "Price Updated to 0 of Variation $variation_id of the product <a href='$product_url'>$product_name</a>";
        $variations_with_price_zero[] = $variation_info;
    }

    // Check if there are variations with price 0 to send an email
    if (!empty($variations_with_price_zero)) {
        // Define the admin email address
        add_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');
        $admin_email = get_field('set_email_id','option');

        // Create the email subject and message
        $subject = 'Variation Price Update Alert';
        $message_html = "<table width='100%' border='1' cellpadding='10' style='border-collapse:collapse'>
            <tbody>
                <tr>
                    <th align='left'>Date &amp; time:</th>
                    <td align='left'>$formatted_datetime</td>
                </tr>
                <tr>
                    <th align='left'>Username:</th>
                    <td align='left'>" . $current_user->user_login . "</td>
                </tr>
                <tr>
                    <th align='left'>Email:</th>
                    <td align='left'><a href='mailto:" . $current_user->user_email . "' target='_blank'>" . $current_user->user_email . "</a></td>
                </tr>
                <tr>
                    <th align='left'>Products:</th>
                    <td align='left'>" . implode("<br>", $variations_with_price_zero) . "</td>
                </tr>
            </tbody>
        </table>";

        // Send the email notification to the admin
        wp_mail($admin_email, $subject, $message_html);
    }
}

// Hook your custom function to the woocommerce_update_product_variation action
add_action('woocommerce_update_product_variation', 'my_variation_updated_notification', 10, 2);
*/





/*Product Variation 0 Price With Database*/
/*add_filter( 'cron_schedules', 'wpshout_add_cron_interval' );
function wpshout_add_cron_interval( $schedules ) {
    $schedules['daily'] = array(
            'interval'  => 43200 , // time in seconds
            'display'   => 'Once Daily'
    );
    return $schedules; 
}*/

function schedule_price_check_event() {
	if (!wp_next_scheduled('price_check_event')) {
		wp_schedule_event( strtotime('08:30:00'), 'daily', 'price_check_event' );
	}
}
add_action('init', 'schedule_price_check_event');

add_action('price_check_event', 'price_check_function');
function price_check_function() {
	require get_template_directory() . '/include/check-product-prices.php';

}

?>