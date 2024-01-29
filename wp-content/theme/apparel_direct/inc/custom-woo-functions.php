<?php
// 1. Show field @ My Account Registration

add_action('woocommerce_register_form_start', 'ad_add_register_form_field');

function ad_add_register_form_field()
{
	echo '<div>';
	echo '<div class="step_form"><h4>Step 1: About Your Business</h4>';
	echo '<div class="step_1_reg row">';
	woocommerce_form_field(
		'business_name',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class'       => array('form-control'),
			'label'       => 'Business Name',
			'placeholder' => 'Business Name',
			// 'description' => '<label for="business_name" class="bmd-label-floating">Business Name *</label>'
		),
		(isset($_POST['business_name']) ? $_POST['business_name'] : '')
	);

	woocommerce_form_field(
		'ein',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'EIN',
			'placeholder' => 'EIN',
			// 'description' => '<label for="ein" class="bmd-label-floating">EIN *</label>'
		),
		(isset($_POST['ein']) ? $_POST['ein'] : '')
	);

	woocommerce_form_field(
		'dba',
		array(
			'type'        => 'text',
			'required'    => false,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'DBA',
			'placeholder' => 'DBA',
			// 'description' => '<label for="dba" class="bmd-label-floating">DBA</label>'
		),
		(isset($_POST['dba']) ? $_POST['dba'] : '')
	);

	woocommerce_form_field(
		'business_url',
		array(
			'type'        => 'text',
			'required'    => false,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Business Website',
			'placeholder' => 'Business Website',
			// 'description' => '<label for="business_url" class="bmd-label-floating">Business Website</label>'
		),
		(isset($_POST['business_url']) ? $_POST['business_url'] : '')
	);

	woocommerce_form_field(
		'email',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Business Email',
			'placeholder' => 'Business Email',
			// 'description' => '<label for="email" class="bmd-label-floating">Business Email *</label>'
		),
		(isset($_POST['email']) ? $_POST['email'] : '')
	);

	woocommerce_form_field(
		'business_phone',
		array(
			'type'        => 'tel',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Business Phone',
			'placeholder' => 'Business Phone',
			// 'description' => '<label for="business_phone" class="bmd-label-floating">Business Phone *</label>'
		),
		(isset($_POST['business_phone']) ? $_POST['business_phone'] : '')
	);

	woocommerce_form_field(
		'billing_address_1',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-12'),
			'input_class' => array('form-control'),
			'label' => 'Business Address',
			'placeholder' => 'Business Address',
			// 'description' => '<label for="billing_address_1" class="bmd-label-floating">Business Address *</label>'
		),
		(isset($_POST['billing_address_1']) ? $_POST['billing_address_1'] : '')
	);

	woocommerce_form_field(
		'billing_city',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'City',
			'placeholder' => 'City',
			// 'description' => '<label for="billing_city" class="bmd-label-floating">City *</label>'
		),
		(isset($_POST['billing_city']) ? $_POST['billing_city'] : '')
	);
	woocommerce_form_field(
		'billing_state',
		array(
			'type'        => 'state',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'State',
			'placeholder' => 'State'
		),
		(isset($_POST['billing_state']) ? $_POST['billing_state'] : '')
	);
	woocommerce_form_field(
		'billing_postcode',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Zip',
			'placeholder' => 'Zip',
			// 'description' => '<label for="billing_postcode" class="bmd-label-floating">Zip *</label>'
		),
		(isset($_POST['billing_postcode']) ? $_POST['billing_postcode'] : '')
	);
	woocommerce_form_field(
		'po_box',
		array(
			'type'        => 'text',
			'required'    => false,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Po Box',
			'placeholder' => 'Po Box',
			// 'description' => '<label for="po_box" class="bmd-label-floating">Po Box</label>'
		),
		(isset($_POST['po_box']) ? $_POST['po_box'] : '')
	);

	$business_type = $_POST['business_type'];
	
?>
	<p class="form-row form-row-wide col-md-6">
		<label for="business_type" class="">Business Type <abbr class="required" title="required">*</abbr></label>
		<select name="business_type[]" class="business_type form-control" id="business_type" multiple/>
		<option value="e-commerce" <?php if ($business_type && in_array('e-commerce', $business_type)) echo 'selected="selected" '; ?>>E-Commerce</option>
		<option value="retailer-brick-mortar" <?php if ($business_type && in_array('retailer-brick-mortar', $business_type)) echo 'selected="selected" '; ?>>Retailer-Brick & Mortar</option>
		<option value="wholesaler-distributor" <?php if ($business_type && in_array('wholesaler-distributor', $business_type)) echo 'selected="selected" '; ?>>Wholesaler - Distributor</option>
		<option value="drop-shipper-direct-vendor-ship" <?php if ($business_type && in_array('drop-shipper-direct-vendor-ship', $business_type)) echo 'selected="selected" '; ?>>Drop-Shipper-Direct Vendor Ship</option>
		<option value="marketplace" <?php if ($business_type && in_array('marketplace', $business_type)) echo 'selected="selected" '; ?>>Marketplace</option>
		<option value="military-government" <?php if ($business_type && in_array('military-government', $business_type)) echo 'selected="selected" '; ?>>Military - Government</option>
		<option value="non-profit" <?php if ($business_type && in_array('non-profit', $business_type)) echo 'selected="selected" '; ?>>Non-Profit</option>
		<option value="international" <?php if ($business_type && in_array('international', $business_type)) echo 'selected="selected" '; ?>>International</option>
		</select>
	</p>
	<p class="form-row col-md-6">
		<a href="https://sbx.certexpress.com/?c=78516d436b3277686641764377344759535a504f:2b36f3b5815e50558214c58bb76130cf" type="button" target="_blank" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clicking this link will take you to a third party site where you will upload your tax certificate to Apparel Direct Distributor. Please return to this application when you are done."><span>Submit Tax Certificate</span> *</a>
	</p>
	<?php

	woocommerce_form_field(
		'business_type_ecommerce',
		array(
			'type'        => 'textarea',
			'required'    => true,
			'class'       => array('col-md-12'),
			'input_class' => array('form-control'),
			'custom_attributes'		=> array(
				'cols' => 50
			),
			'label' => 'Please list your website domain and all marketplaces you are selling on',
			'placeholder' => 'Please list your website domain and all marketplaces you are selling on',
		),
		(isset($_POST['business_type_ecommerce']) ? $_POST['business_type_ecommerce'] : '')
	);

	woocommerce_form_field(
		'about_your_business',
		array(
			'type'        => 'textarea',
			'required'    => false,
			'class'       => array('col-md-12'),
			'input_class' => array('form-control'),
			'custom_attributes'		=> array(
				'cols' => 50
			),
			'label' => 'Anything we should know about your business?',
			'placeholder' => 'Anything we should know about your business?',
			// 'description' => '<label for="about_your_business" class="bmd-label-floating">Anything we should know about your business?</label>'
		),
		(isset($_POST['about_your_business']) ? $_POST['about_your_business'] : '')
	);

	if (isset($_POST['reg_product_brand_select'])) {
		$product_brand_select = $_POST['reg_product_brand_select'];
		$product_brand_select = implode(",", $product_brand_select);
	}
	echo '<p class="form-row col-md-6">';
	echo '<label for="reg_product_brand_select" class="">Brands of Interest</label>';
	wp_dropdown_categories(array(
		'taxonomy'          => 'product_brand',
		'hierarchical'      => true,
		//'show_option_none'  => 'Select Brands of Interest',
		//'option_none_value' => '',
		//'hide_if_empty' 	=> false,
		'name'              => 'reg_product_brand_select',
		'id'                => 'product_brand_select',
		'selected'          => $product_brand_select, // e.x 86,110,786
		'multiple'          => true,
	));
	echo '</p>';
	if (isset($_POST['reg_product_cat_select'])) {
		$product_cat_selected = $_POST['reg_product_cat_select'];
		$product_cat_selected = implode(",", $product_cat_selected);
	}

	echo '<p class="form-row col-md-6">';
	// wp_dropdown_categories( array(
	// 	'taxonomy'          => 'product_cat',
	// 	'hierarchical'      => true,
	// 	//'show_option_none'  => 'Select Categories of Interest',
	// 	//'option_none_value' => '',
	// 	'name'              => 'reg_product_cat_select',
	// 	'id'                => 'product_cat_select',
	// 	'selected'          => $product_cat_selected, // e.x 86,110,786
	// 	'multiple'          => true
	// ) );
	$select_interested_categories = get_field('select_interested_categories', 'option');

	?>
	<label for="reg_product_cat_select" class="">Categories of Interest</label>
	<select name="reg_product_cat_select[]" class="postform" id="product_cat_select" multiple />
	<option value>Categories of Interest</option>
	<?php
	
	foreach ($select_interested_categories as $in_cat) {
	?>
		<option value="<?php echo $in_cat->term_id; ?>" <?php if ($product_cat_selected && in_array($in_cat->term_id, explode(",", $product_cat_selected))) echo 'selected="selected" '; ?>><?php echo $in_cat->name; ?></option>
	<?php } ?>
	</select>
<?php
	echo '</p>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '<div>';
	echo '<div class="step_form">';
	echo '<h4>Step 2: About You</h4>';

	echo '<div class="step_2_reg row">';
	woocommerce_form_field(
		'billing_first_name',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'First Name',
			'placeholder' => 'First Name',
			// 'description' => '<label for="billing_first_name" class="bmd-label-floating">First Name *</label>'
		),
		(isset($_POST['billing_first_name']) ? $_POST['billing_first_name'] : '')
	);
	woocommerce_form_field(
		'billing_last_name',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Last Name',
			'placeholder' => 'Last Name',
			// 'description' => '<label for="billing_last_name" class="bmd-label-floating">Last Name *</label>'
		),
		(isset($_POST['billing_last_name']) ? $_POST['billing_last_name'] : '')
	);

	woocommerce_form_field(
		'personal_email',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Personal Email',
			'placeholder' => 'Personal Email',
			// 'description' => '<label for="personal_email" class="bmd-label-floating">Personal Email *</label>'
		),
		(isset($_POST['personal_email']) ? $_POST['personal_email'] : '')
	);

	woocommerce_form_field(
		'personal_phone',
		array(
			'type'        => 'tel',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Personal Phone',
			'placeholder' => 'Personal Phone',
			// 'description' => '<label for="personal_phone" class="bmd-label-floating">Personal Phone *</label>'
		),
		(isset($_POST['personal_phone']) ? $_POST['personal_phone'] : '')
	);
	woocommerce_form_field(
		'home_address_1',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-12'),
			'input_class' => array('form-control'),
			'label' => 'Home Address',
			'placeholder' => 'Home Address',
			// 'description' => '<label for="home_address_1" class="bmd-label-floating">Home Address *</label>'
		),
		(isset($_POST['home_address_1']) ? $_POST['home_address_1'] : '')
	);
	woocommerce_form_field(
		'home_city',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'City',
			'placeholder' => 'City',
			// 'description' => '<label for="home_city" class="bmd-label-floating">City *</label>'
		),
		(isset($_POST['home_city']) ? $_POST['home_city'] : '')
	);
	woocommerce_form_field(
		'home_state',
		array(
			'type'        => 'state',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'State',
			'placeholder' => 'State'
		),
		(isset($_POST['home_state']) ? $_POST['home_state'] : '')
	);
	woocommerce_form_field(
		'home_postcode',
		array(
			'type'        => 'text',
			'required'    => true,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Zip',
			'placeholder' => 'Zip',
			// 'description' => '<label for="home_postcode" class="bmd-label-floating">Zip *</label>'
		),
		(isset($_POST['home_postcode']) ? $_POST['home_postcode'] : '')
	);
	woocommerce_form_field(
		'home_po_box',
		array(
			'type'        => 'text',
			'required'    => false,
			'class'       => array('col-md-6'),
			'input_class' => array('form-control'),
			'label' => 'Po Box',
			'placeholder' => 'Po Box',
			// 'description' => '<label for="home_po_box" class="bmd-label-floating">Po Box</label>'
		),
		(isset($_POST['home_po_box']) ? $_POST['home_po_box'] : '')
	);

	echo '</div>';
	echo '</div>';
	echo '</div>';
}

/**
 * To validate WooCommerce registration form custom fields.
 */
add_action('woocommerce_register_post', 'ad_validate_fields', 10, 3);

function ad_validate_fields($username, $email, $errors)
{

	if (empty($_POST['business_name'])) {
		$errors->add('business_name_error', 'Business Name is required!');
	}
	if (empty($_POST['billing_first_name'])) {
		$errors->add('billing_first_name_error', 'First name is required!');
	}
	if (empty($_POST['billing_last_name'])) {
		$errors->add('billing_last_name_error', 'Last name is required!');
	}
	if (empty($_POST['business_phone'])) {
		$errors->add('business_phone_error', 'Business Phone is required!');
	}
	if (empty($_POST['billing_address_1'])) {
		$errors->add('billing_address_1_error', 'Business Address is required!');
	}
	if (empty($_POST['billing_state'])) {
		$errors->add('billing_state_error', 'State is required!');
	}
	if (empty($_POST['billing_city'])) {
		$errors->add('billing_city_error', 'City is required!');
	}
	if (empty($_POST['billing_postcode'])) {
		$errors->add('billing_postcode_error', 'Zip code is required!');
	}

	if (empty($_POST['ein'])) {
		$errors->add('ein_error', 'EIN is required!');
	}

	if (empty($_POST['business_type'])) {
		$errors->add('business_types_error', 'Business Type is required!');
	}

	if (empty($_POST['home_address_1'])) {
		$errors->add('home_address_1_error', 'Home Address is required!');
	}
	if (empty($_POST['home_state'])) {
		$errors->add('home_state_error', 'State is required!');
	}
	if (empty($_POST['home_city'])) {
		$errors->add('home_city_error', 'City is required!');
	}
	if (empty($_POST['home_postcode'])) {
		$errors->add('home_postcode_error', 'Zip code is required!');
	}
}
// 2. Save field on Customer Created action
add_action('woocommerce_created_customer', 'ad_save_register_fields');

function ad_save_register_fields($customer_id)
{

	//Step1 About your business
	// WooCommerce business name
	if (isset($_POST['business_name'])) {
		update_user_meta($customer_id, 'business_name', sanitize_text_field($_POST['business_name']));
	}
	// WooCommerce ein
	if (isset($_POST['ein'])) {
		update_user_meta($customer_id, 'ein', sanitize_text_field($_POST['ein']));
	}

	// WooCommerce dba
	if (isset($_POST['dba'])) {
		update_user_meta($customer_id, 'dba', sanitize_text_field($_POST['dba']));
	}

	// WooCommerce business website
	if (isset($_POST['business_url'])) {
		update_user_meta($customer_id, 'business_url', sanitize_text_field($_POST['business_url']));
	}

	// WooCommerce business email
	if (isset($_POST['email'])) {
		update_user_meta($customer_id, 'business_email', sanitize_text_field($_POST['email']));
		update_user_meta($customer_id, 'billing_email', sanitize_text_field($_POST['email']));
	}

	// WooCommerce business phone
	if (isset($_POST['business_phone'])) {
		update_user_meta($customer_id, 'business_phone', sanitize_text_field($_POST['business_phone']));
		update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['business_phone']));
	}

	//WooCommerce business type
	/*if (isset($_POST['business_type'])) {
		update_user_meta($customer_id, 'business_type', sanitize_text_field($_POST['business_type']));
	}*/
	if (isset($_POST['business_type'])) {
		update_user_meta($customer_id, 'business_type', serialize($_POST['business_type']));
	}


	// WooCommerce about your business
	if (isset($_POST['about_your_business'])) {
		update_user_meta($customer_id, 'about_your_business', sanitize_text_field($_POST['about_your_business']));
	}

	// WooCommerce business type ecommerce
	if (isset($_POST['business_type_ecommerce'])) {
		update_user_meta($customer_id, 'business_type_ecommerce', sanitize_text_field($_POST['business_type_ecommerce']));
	}

	//WooCommerce product brands
	if (isset($_POST['reg_product_brand_select'])) {
		update_user_meta($customer_id, 'reg_product_brand_select', serialize($_POST['reg_product_brand_select']));
	}
	//WooCommerce product category
	if (isset($_POST['reg_product_cat_select'])) {
		update_user_meta($customer_id, 'reg_product_cat_select', serialize($_POST['reg_product_cat_select']));
	}

	//Step2: About you
	//WooCommerce First name
	if (isset($_POST['billing_first_name'])) {
		update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']));
		update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
	}

	//WooCommerce Last name
	if (isset($_POST['billing_last_name'])) {
		update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']));
		update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
	}

	// WooCommerce personal email
	if (isset($_POST['personal_email'])) {
		update_user_meta($customer_id, 'personal_email', sanitize_text_field($_POST['personal_email']));
	}

	// WooCommerce personal phone
	if (isset($_POST['personal_phone'])) {
		update_user_meta($customer_id, 'personal_phone', sanitize_text_field($_POST['personal_phone']));
	}

	// WooCommerce home / billing address
	if (isset($_POST['billing_address_1'])) {
		update_user_meta($customer_id, 'billing_address_1', sanitize_text_field($_POST['billing_address_1']));
	}

	// WooCommerce home address
	if (isset($_POST['home_address_1'])) {
		update_user_meta($customer_id, 'home_address_1', sanitize_text_field($_POST['home_address_1']));
	}

	// WooCommerce billing state
	if (isset($_POST['billing_state'])) {
		update_user_meta($customer_id, 'billing_state', sanitize_text_field($_POST['billing_state']));
	}

	// WooCommerce billing state
	if (isset($_POST['home_state'])) {
		update_user_meta($customer_id, 'home_state', sanitize_text_field($_POST['home_state']));
	}

	// WooCommerce billing city
	if (isset($_POST['billing_city'])) {
		update_user_meta($customer_id, 'billing_city', sanitize_text_field($_POST['billing_city']));
	}

	// WooCommerce billing city
	if (isset($_POST['home_city'])) {
		update_user_meta($customer_id, 'home_city', sanitize_text_field($_POST['home_city']));
	}

	// WooCommerce billing postcode
	if (isset($_POST['billing_postcode'])) {
		update_user_meta($customer_id, 'billing_postcode', sanitize_text_field($_POST['billing_postcode']));
	}

	// WooCommerce home postcode
	if (isset($_POST['home_postcode'])) {
		update_user_meta($customer_id, 'home_postcode', sanitize_text_field($_POST['home_postcode']));
	}

	/*
	// WooCommerce po box
	if (isset($_POST['po_box'])) {
		update_user_meta($customer_id, 'po_box', sanitize_text_field($_POST['po_box']));
	}

	// WooCommerce Home po box
	if (isset($_POST['home_po_box'])) {
		update_user_meta($customer_id, 'home_po_box', sanitize_text_field($_POST['home_po_box']));
	}*/


	/*if ( isset( $_FILES['image'] ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		$attachment_id = media_handle_upload( 'image', 0 );
		
		if ( is_wp_error( $attachment_id ) ) {
		   update_user_meta( $customer_id, 'reg_document', $_FILES['image'] . ": " . $attachment_id->get_error_message() );
		} else {
		   update_user_meta( $customer_id, 'reg_document', $attachment_id );
		}
	 }*/
}

add_action('woocommerce_register_form_tag', 'bbloomer_enctype_custom_registration_forms');

function bbloomer_enctype_custom_registration_forms()
{
	echo 'enctype="multipart/form-data"';
}

// 3. Display Select Field @ User Profile (admin) and My Account Edit page (front end)

//add_action('show_user_profile', 'bbloomer_show_extra_register_select_field', 30);
//add_action('edit_user_profile', 'bbloomer_show_extra_register_select_field', 30);
add_action('woocommerce_edit_account_form', 'bbloomer_show_extra_register_select_field', 30);

function bbloomer_show_extra_register_select_field($user)
{

	$user_id = get_current_user_id();
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
	$business_type_ecommerce = get_user_meta($user_id, 'business_type_ecommerce', true);
	$about_your_business = get_user_meta($user_id, 'about_your_business', true);

	$reg_product_cat_select = get_user_meta($user_id, 'reg_product_cat_select', true);
	if ($reg_product_cat_select) {
		$product_cat_selected = implode(",", unserialize($reg_product_cat_select));
	}

	$reg_product_brand_select = get_user_meta($user_id, 'reg_product_brand_select', true);
	if ($reg_product_brand_select) {
		$product_brand_select = implode(",", unserialize($reg_product_brand_select));
	}

	$business_type = get_user_meta($user_id, 'business_type', true);
	if ($business_type) {
		$business_type =  unserialize($business_type);
	}

?>
	<!-- Second Panel -->
	<div class="step_form  panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" data-toggle="collapse" data-target="#collapseTwo"><?php echo 'Business</'; ?></h3>
		</div>

		<div id="collapseTwo" class="panel-collapse collapse">
			<div class="panel-body">
				<?php

				echo '<div class="business_section row">';
				woocommerce_form_field(
					'business_name',
					array(
						'type'        => 'text',
						'required'    => false,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label'       => 'Business Name'
					),
					($business_name)
				);

				woocommerce_form_field(
					'ein',
					array(
						'type'        => 'text',
						'required'    => false,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label'       => 'EIN'
					),
					($ein)
				);

				woocommerce_form_field(
					'dba',
					array(
						'type'        => 'text',
						'required'    => false,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label'       => 'DBA'
					),
					($dba)
				);

				woocommerce_form_field(
					'business_url',
					array(
						'type'        => 'text',
						'required'    => false,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label'       => 'Business Website'
					),
					($business_url)
				);


				woocommerce_form_field(
					'business_phone',
					array(
						'type'        => 'tel',
						'required'    => false,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'placeholder' => 'Business Phone',
						'label'       => 'Business Phone'
					),
					($business_phone)
				);

				woocommerce_form_field(
					'billing_address_1',
					array(
						'type'        => 'text',
						//'required'    => true, 
						'class'       => array('col-md-12'),
						'input_class' => array('form-control'),
						'label' => 'Business Address',
						'placeholder' => 'Business Address',
						// 'description' => '<label for="billing_address_1" class="bmd-label-floating">Business Address *</label>'
					),
					($billing_address_1)
				);

				woocommerce_form_field(
					'billing_city',
					array(
						'type'        => 'text',
						//'required'    => true, 
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label' => 'City',
						'placeholder' => 'City',
						// 'description' => '<label for="billing_city" class="bmd-label-floating">City *</label>'
					),
					($billing_city)
				);
				woocommerce_form_field(
					'billing_state',
					array(
						'type'        => 'state',
						//'required'    => true,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label' => 'State',
						'placeholder' => 'State'
					),
					($billing_state)
				);
				woocommerce_form_field(
					'billing_postcode',
					array(
						'type'        => 'text',
						//'required'    => true, 
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label' => 'Zip',
						'placeholder' => 'Zip',
						// 'description' => '<label for="billing_postcode" class="bmd-label-floating">Zip *</label>'
					),
					($billing_postcode)
				);
				
				?>
				<p class="form-row col-md-6">
					<label for=""><?php _e('Business Type', 'woocommerce'); ?> </label>
					<select name="business_type[]" class="business_type" id="business_type" multiple/>
					<option value="e-commerce" <?php if ($business_type && in_array('e-commerce', $business_type)) echo 'selected="selected" '; ?>>E-Commerce</option>
					<option value="retailer-brick-mortar" <?php if ($business_type && in_array('retailer-brick-mortar', $business_type)) echo 'selected="selected" '; ?>>Retailer-Brick & Mortar</option>
					<option value="wholesaler-distributor" <?php if ($business_type && in_array('wholesaler-distributor', $business_type)) echo 'selected="selected" '; ?>>Wholesaler - Distributor</option>
					<option value="drop-shipper-direct-vendor-ship" <?php if ($business_type && in_array('drop-shipper-direct-vendor-ship', $business_type)) echo 'selected="selected" '; ?>>Drop-Shipper-Direct Vendor Ship</option>
					<option value="marketplace" <?php if ($business_type && in_array('marketplace', $business_type)) echo 'selected="selected" '; ?>>Marketplace</option>
					<option value="military-government" <?php if ($business_type && in_array('military-government', $business_type)) echo 'selected="selected" '; ?>>Military - Government</option>
					<option value="non-profit" <?php if ($business_type && in_array('non-profit', $business_type)) echo 'selected="selected" '; ?>>Non-Profit</option>
					<option value="international" <?php if ($business_type && in_array('international', $business_type)) echo 'selected="selected" '; ?>>International</option>
					</select>
				</p>
				<?php

				woocommerce_form_field(
					'business_type_ecommerce',
					array(
						'type'        => 'textarea',
						'required'    => false,
						'class'       => array('col-md-12'),
						'input_class' => array('form-control'),
						'custom_attributes'		=> array(
							'cols' => 50
						),
						'label' => 'Please list your website domain and all marketplaces you are selling on',
						'placeholder' => 'Please list your website domain and all marketplaces you are selling on',
					),
					($business_type_ecommerce)
				);

				woocommerce_form_field(
					'about_your_business',
					array(
						'type'        => 'textarea',
						'required'    => false,
						'class'       => array('col-md-12'),
						'input_class' => array('form-control'),
						'custom_attributes'		=> array(
							'cols' => 50
						),
						'label' => 'Anything we should know about your business?',
						'placeholder' => 'Anything we should know about your business?',
						// 'description' => '<label for="about_your_business" class="bmd-label-floating">Anything we should know about your business?</label>'
					),
					($about_your_business)
				);

				echo '<p class="form-row col-md-6">';
				echo '<label for="reg_product_brand_select" class="">Brands of Interest</label>';
				wp_dropdown_categories(array(
					'taxonomy'          => 'product_brand',
					'hierarchical'      => false,
					//'show_option_none'  => 'Select Categories of Interest',
					//'option_none_value' => '',
					'hide_empty' => false,
					'name'              => 'reg_product_brand_select',
					'id'                => 'product_brand_select',
					'selected'          => $product_brand_select, // e.x 86,110,786
					'multiple'          => true
				));
				echo '</p>';

				echo '<p class="form-row col-md-6">';
				echo '<label for="reg_product_cat_select" class="">Categories of Interest</label>';
				wp_dropdown_categories(array(
					'taxonomy'          => 'product_cat',
					'hierarchical'      => false,
					//'show_option_none'  => 'Select Categories of Interest',
					//'option_none_value' => '',
					'hide_empty' => false,
					'name'              => 'reg_product_cat_select',
					'id'                => 'product_cat_select',
					'selected'          => $product_cat_selected, // e.x 86,110,786
					'multiple'          => true
				));
				echo '</p>';
				echo '</div>';
				?>
			</div>
		</div>
	</div>

	<!-- Third Panel -->
	<div class="step_form panel panel-default">
		<div class="panel-heading panel-heading-full">
			<h3 class="panel-title" data-toggle="collapse" data-target="#collapseThree"><?php echo 'Personal Info'; ?></h3>
		</div>

		<div id="collapseThree" class="panel-collapse collapse">
			<div class="panel-body">
				<?php
				echo '<div class="personal_info_section row">';
				?>
				<p class="col-md-6 woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
					<label for="account_first_name"><?php esc_html_e('First name', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr($user->first_name); ?>" />
				</p>
				<p class="col-md-6 woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
					<label for="account_last_name"><?php esc_html_e('Last name', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($user->last_name); ?>" />
				</p>

				<?php

				woocommerce_form_field(
					'personal_email',
					array(
						'type'        => 'text',
						//'required'    => true,
						'class'       => array('col-md-12'),
						'input_class' => array('form-control'),
						'label' => 'Personal Email',
						'placeholder' => 'Personal Email',
						// 'description' => '<label for="personal_email" class="bmd-label-floating">Personal Email *</label>'
					),
					($personal_email)
				);

				woocommerce_form_field(
					'home_address_1',
					array(
						'type'        => 'text',
						//'required'    => true, 
						'class'       => array('col-md-12'),
						'input_class' => array('form-control'),
						'label' => 'Home Address',
						'placeholder' => 'Home Address',
						// 'description' => '<label for="home_address_1" class="bmd-label-floating">Home Address *</label>'
					),
					($home_address_1)
				);

				woocommerce_form_field(
					'home_city',
					array(
						'type'        => 'text',
						//'required'    => true, 
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label' => 'City',
						'placeholder' => 'City',
						// 'description' => '<label for="home_city" class="bmd-label-floating">City *</label>'
					),
					($home_city)
				);

				woocommerce_form_field(
					'home_state',
					array(
						'type'        => 'state',
						//'required'    => true,
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label' => 'State',
						'placeholder' => 'State'
					),
					($home_state)
				);

				woocommerce_form_field(
					'home_postcode',
					array(
						'type'        => 'text',
						//'required'    => true, 
						'class'       => array('col-md-6'),
						'input_class' => array('form-control'),
						'label' => 'Zip',
						'placeholder' => 'Zip',
						// 'description' => '<label for="home_postcode" class="bmd-label-floating">Zip *</label>'
					),
					($home_postcode)
				);
				echo '</div>'; ?>
			</div>
		</div>
	</div>
<?php

}

// -------------------
// 4. Save User Field When Changed From the Admin/Front End Forms

add_action('personal_options_update', 'bbloomer_save_extra_register_select_field_admin');
add_action('edit_user_profile_update', 'bbloomer_save_extra_register_select_field_admin');
add_action('woocommerce_save_account_details', 'bbloomer_save_extra_register_select_field_admin');

function bbloomer_save_extra_register_select_field_admin($customer_id)
{


	//Step1 About your business
	// WooCommerce business name
	if (isset($_POST['business_name'])) {
		update_user_meta($customer_id, 'business_name', sanitize_text_field($_POST['business_name']));
	}
	// WooCommerce ein
	if (isset($_POST['ein'])) {
		update_user_meta($customer_id, 'ein', sanitize_text_field($_POST['ein']));
	}

	// WooCommerce dba
	if (isset($_POST['dba'])) {
		update_user_meta($customer_id, 'dba', sanitize_text_field($_POST['dba']));
	}

	// WooCommerce business website
	if (isset($_POST['business_url'])) {
		update_user_meta($customer_id, 'business_url', sanitize_text_field($_POST['business_url']));
	}

	// WooCommerce business email
	if (isset($_POST['email'])) {
		update_user_meta($customer_id, 'business_email', sanitize_text_field($_POST['email']));
		update_user_meta($customer_id, 'billing_email', sanitize_text_field($_POST['email']));
	}

	// WooCommerce business phone
	if (isset($_POST['business_phone'])) {
		update_user_meta($customer_id, 'business_phone', sanitize_text_field($_POST['business_phone']));
		update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['business_phone']));
	}

	//WooCommerce business type
	/*if (isset($_POST['business_type'])) {
		update_user_meta($customer_id, 'business_type', sanitize_text_field($_POST['business_type']));
	}*/
	if (isset($_POST['business_type'])) {
		update_user_meta($customer_id, 'business_type', serialize($_POST['business_type']));
	}


	// WooCommerce about your business
	if (isset($_POST['about_your_business'])) {
		update_user_meta($customer_id, 'about_your_business', sanitize_text_field($_POST['about_your_business']));
	}

	// WooCommerce business type ecommerce
	if (isset($_POST['business_type_ecommerce'])) {
		update_user_meta($customer_id, 'business_type_ecommerce', sanitize_text_field($_POST['business_type_ecommerce']));
	}

	//WooCommerce product brands
	if (isset($_POST['reg_product_brand_select'])) {
		update_user_meta($customer_id, 'reg_product_brand_select', serialize($_POST['reg_product_brand_select']));
	}
	//WooCommerce product category
	if (isset($_POST['reg_product_cat_select'])) {
		update_user_meta($customer_id, 'reg_product_cat_select', serialize($_POST['reg_product_cat_select']));
	}

	//Step2: About you
	//WooCommerce First name
	if (isset($_POST['billing_first_name'])) {
		//update_user_meta( $customer_id, 'country_to_visit', wc_clean( $_POST['country_to_visit'] ) );
		update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']));
		update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
	}

	//WooCommerce Last name
	if (isset($_POST['billing_last_name'])) {
		update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']));
		update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
	}

	// WooCommerce personal email
	if (isset($_POST['personal_email'])) {
		update_user_meta($customer_id, 'personal_email', sanitize_text_field($_POST['personal_email']));
	}

	// WooCommerce personal phone
	if (isset($_POST['personal_phone'])) {
		update_user_meta($customer_id, 'personal_phone', sanitize_text_field($_POST['personal_phone']));
	}

	// WooCommerce home / billing address
	if (isset($_POST['billing_address_1'])) {
		if (is_plugin_active('wp-security-audit-log-premium/wp-security-audit-log.php')) {
			$event_id = 4015;
			$olddata = get_user_meta($customer_id, 'billing_address_1', true);
			if ($olddata != $_POST['billing_address_1']) {
				if ($event_id === 4015) {
					$user = wp_get_current_user();
					$wsal = WpSecurityAuditLog::GetInstance();
					$user_name_data = $user->first_name . ' ' . $user->last_name;
					$wsal->alerts->Trigger(
						4015,
						array(
							'custom_field_name' => 'Business Info - Address',
							'new_value'         => $_POST['billing_address_1'],
							'old_value'         => $olddata,
							'FirstName'         => $user->user_firstname,
							'LastName'          => $user->user_lastname,
							'TargetUsername' => $user ? $user_name_data : false,
							'EditUserLink'   => add_query_arg('user_id', $customer_id, admin_url('user-edit.php')),
							'Roles'          => is_array($user->roles) ? implode(', ', $user->roles) : $user->roles,
						)
					);
				}
			}
		}

		update_user_meta($customer_id, 'billing_address_1', sanitize_text_field($_POST['billing_address_1']));
	}

	// WooCommerce home address
	if (isset($_POST['home_address_1'])) {
		update_user_meta($customer_id, 'home_address_1', sanitize_text_field($_POST['home_address_1']));
	}

	// WooCommerce billing state
	if (isset($_POST['billing_state'])) {
		if (is_plugin_active('wp-security-audit-log-premium/wp-security-audit-log.php')) {
			$event_id = 4015;
			$olddata = get_user_meta($customer_id, 'billing_state', true);
			if ($olddata != $_POST['billing_state']) {
				if ($event_id === 4015) {
					$user = wp_get_current_user();
					$wsal = WpSecurityAuditLog::GetInstance();
					$user_name_data = $user->first_name . ' ' . $user->last_name;
					$wsal->alerts->Trigger(
						4015,
						array(
							'custom_field_name' => 'Business Info - State',
							'new_value'         => $_POST['billing_state'],
							'old_value'         => $olddata,
							'FirstName'         => $user->user_firstname,
							'LastName'          => $user->user_lastname,
							'TargetUsername' => $user ? $user_name_data : false,
							'EditUserLink'   => add_query_arg('user_id', $customer_id, admin_url('user-edit.php')),
							'Roles'          => is_array($user->roles) ? implode(', ', $user->roles) : $user->roles,
						)
					);
				}
			}
		}
		update_user_meta($customer_id, 'billing_state', sanitize_text_field($_POST['billing_state']));
	}

	// WooCommerce billing state
	if (isset($_POST['home_state'])) {
		update_user_meta($customer_id, 'home_state', sanitize_text_field($_POST['home_state']));
	}

	// WooCommerce billing city
	if (isset($_POST['billing_city'])) {
		if (is_plugin_active('wp-security-audit-log-premium/wp-security-audit-log.php')) {
			$event_id = 4015;
			$olddata = get_user_meta($customer_id, 'billing_city', true);
			if ($olddata != $_POST['billing_city']) {
				if ($event_id === 4015) {
					$user = wp_get_current_user();
					$wsal = WpSecurityAuditLog::GetInstance();
					$user_name_data = $user->first_name . ' ' . $user->last_name;
					$wsal->alerts->Trigger(
						4015,
						array(
							'custom_field_name' => 'Business Info - City',
							'new_value'         => $_POST['billing_city'],
							'old_value'         => $olddata,
							'FirstName'         => $user->user_firstname,
							'LastName'          => $user->user_lastname,
							'TargetUsername' => $user ? $user_name_data : false,
							'EditUserLink'   => add_query_arg('user_id', $customer_id, admin_url('user-edit.php')),
							'Roles'          => is_array($user->roles) ? implode(', ', $user->roles) : $user->roles,
						)
					);
				}
			}
		}
		update_user_meta($customer_id, 'billing_city', sanitize_text_field($_POST['billing_city']));
	}

	// WooCommerce billing city
	if (isset($_POST['home_city'])) {
		update_user_meta($customer_id, 'home_city', sanitize_text_field($_POST['home_city']));
	}

	// WooCommerce billing postcode
	if (isset($_POST['billing_postcode'])) {
		if (is_plugin_active('wp-security-audit-log-premium/wp-security-audit-log.php')) {
			$event_id = 4015;
			$olddata = get_user_meta($customer_id, 'billing_postcode', true);
			if ($olddata != $_POST['billing_postcode']) {
				if ($event_id === 4015) {
					$user = wp_get_current_user();
					$wsal = WpSecurityAuditLog::GetInstance();
					$user_name_data = $user->first_name . ' ' . $user->last_name;
					$wsal->alerts->Trigger(
						4015,
						array(
							'custom_field_name' => 'Business Info - Zip code',
							'new_value'         => $_POST['billing_postcode'],
							'old_value'         => $olddata,
							'FirstName'         => $user->user_firstname,
							'LastName'          => $user->user_lastname,
							'TargetUsername' => $user ? $user_name_data : false,
							'EditUserLink'   => add_query_arg('user_id', $customer_id, admin_url('user-edit.php')),
							'Roles'          => is_array($user->roles) ? implode(', ', $user->roles) : $user->roles,
						)
					);
				}
			}
		}
		update_user_meta($customer_id, 'billing_postcode', sanitize_text_field($_POST['billing_postcode']));
	}

	// WooCommerce home postcode
	if (isset($_POST['home_postcode'])) {
		update_user_meta($customer_id, 'home_postcode', sanitize_text_field($_POST['home_postcode']));
	}
}

// Replace state select field placeholder option text in checkout 
add_action('wp_footer', 'ad_custom_script_in_registration', 100, 1);
function ad_custom_script_in_registration()
{
?>
	<script type="text/javascript">
		jQuery(function($) {
			function changeStateOptionText() {
				$('select[name=billing_state] option, select[name=shipping_state] option, select[name=home_state] option').each(function() {
					if ($(this).text() == "Select an option…")
						$(this).text("Select a state");
				});
			}
			setTimeout(changeStateOptionText, 200);

			// To be sure (if shipping fields are hidden)
			$('checkbox[name=ship_to_different_address]').change(function() {
				changeStateOptionText();
			});
		});
	</script>
<?php
}

add_filter('wp_dropdown_cats', 'wp_dropdown_cats_multiple', 10, 2);

function wp_dropdown_cats_multiple($output, $r)
{

	if (isset($r['multiple']) && $r['multiple']) {

		$output = preg_replace('/^<select/i', '<select multiple', $output);

		$output = str_replace("name='{$r['name']}'", "name='{$r['name']}[]'", $output);

		foreach (array_map('trim', explode(",", $r['selected'])) as $value)
			$output = str_replace("value=\"{$value}\"", "value=\"{$value}\" selected", $output);
	}

	return $output;
}

//Wishlist customization

// 1. Register new endpoint (URL) for My Account page
function ad_add_my_lists()
{
	add_rewrite_endpoint('my-lists', EP_ROOT | EP_PAGES);
}
add_action('init', 'ad_add_my_lists');

// 2. Add new query var
function ad_query_vars($vars)
{
	$vars[] = 'my-lists';
	return $vars;
}
add_filter('query_vars', 'ad_query_vars', 0);

// 3. Insert the new endpoint into the My Account menu
function ad_add_link_my_account($items)
{
	//$items['dashboard'] = 'My Account';
	$logout = $items['customer-logout'];
	$payment_methods = $items['payment-methods'];
	$edit_account = $items['edit-account'];
	$edit_address = $items['edit-address'];
	
	unset( $items['customer-logout'] );
	unset( $items['payment-methods'] );
	unset( $items['edit-account'] );
	unset( $items['edit-address'] );
	
	$items['my-lists'] = 'Lists';
	$items['edit-address'] = 'Addresses';
	$items['payment-methods'] = $payment_methods;
	$items['edit-account'] = 'Account';
	$items['customer-logout'] = $logout;
	
	return $items;
}
add_filter('woocommerce_account_menu_items', 'ad_add_link_my_account');

// 4. Add content to the new tab
function ad_my_lists_content()
{
	//echo do_shortcode('[yith_wcwl_wishlist]');
	require_once get_template_directory() . '/woocommerce/wishlist-manage-modern.php';
	do_action('yith_wcwl_wishlist_after_wishlist_content', $var);
}

add_action('woocommerce_account_my-lists_endpoint', 'ad_my_lists_content');
// Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

add_action('yith_wcwl_wishlist_before_wishlist_content', 'add_back_to_all_wishlists_link_custom', 21, 2);
function add_back_to_all_wishlists_link_custom()
{
	echo '<div class="back-to-all-wishlists-custom"><a href="' . site_url() . '/my-account/my-lists/" title="Back to all lists">‹ Back to all lists</a></div>';
}

//Wishlist customization End

//User approval
add_action('woocommerce_created_customer', 'ad_user_pass_on_registration', 10, 3);
function ad_user_pass_on_registration($customer_id, $new_customer_data, $password_generated)
{
	if ($new_customer_data['user_pass']) {
		update_user_meta($customer_id, 'user_pass_custom', $new_customer_data['user_pass']);
	}
}

add_action('user_register', 'ad_user_reg_after_popup', 10, 1);
function ad_user_reg_after_popup($user_id)
{
?>
	<div id="modal_reg_notification" style="display: none;" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
				<div class="modal-body text-center">

					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-popup.png" alt="logo-popup">
					<h2>Your account application has been submitted!</h2>
					<p>We will send you an email as soon as your account has been approved. Then you can start ordering!</p>
				</div>
			</div>
		</div>
	</div>
<?php
}
//User approval End

add_action('init', 'ad_hide_price_add_cart_not_logged_in');

function ad_hide_price_add_cart_not_logged_in()
{
	if (!is_user_logged_in()) {
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		// remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
		// remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
		remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
		add_action('woocommerce_single_product_summary', 'ad_print_login_to_see', 31);
		add_action('woocommerce_after_shop_loop_item', 'ad_print_login_to_see', 11);
	}
}

function ad_print_login_to_see()
{
	echo '<a class="global_display product_login_popup" href="javascript:void(0)">' . __('Login to see prices', 'theme_name') . '</a>';
}
add_shortcode('header_right_btn', 'header_right_btn_shortcode');
function header_right_btn_shortcode()
{
	$log_in_button = get_field('log_in_button', 'option');
	$quick_order_button = get_field('quick_order_button', 'option');
	ob_start();
?>
	<div class="quick-order">
		<?php
		if (is_user_logged_in()) { ?>
			<a href="<?php echo $quick_order_button['url']; ?>" title="<?php echo $quick_order_button['title']; ?>" class="btn primary-btn"><?php echo $quick_order_button['title']; ?></a>
		<?php } else { ?>

			<a href="<?php echo $log_in_button['url']; ?>" title="<?php echo $log_in_button['title']; ?>" class="btn primary-btn"><?php echo $log_in_button['title']; ?></a>
		<?php }
		?>

	</div>
<?php
	return ob_get_clean();
}

// Removes Order Notes Title
add_filter('woocommerce_enable_order_notes_field', '__return_false', 9999);
// Remove Order Notes Field

add_filter('woocommerce_checkout_fields', 'ad_custom_order_notes');
function ad_custom_order_notes($fields)
{
	unset($fields['order']['order_comments']);
	return $fields;
}