<?php 
if ( ! defined( 'WPINC' ) ) {
	die; 
}

if ( !class_exists( 'Addify_Approve_New_User_Front' ) ) {

	class Addify_Approve_New_User_Front extends Addify_Approve_New_User {

		public $addify_apnu_enable_module;
		public $addify_apnu_enable_module_checkout_page;
		public $addify_apnu_exclude_user_roles;

		public $addify_apnu_account_creation_message;
		public $addify_apnu_account_pending_message;
		public $addify_apnu_account_disapproved_message;

		public function __construct() {

			if (!empty(get_option('addify_apnu_enable_module'))) {
				$this->addify_apnu_enable_module = get_option( 'addify_apnu_enable_module');	
			} else {
				$this->addify_apnu_enable_module = '';
			}

			if (!empty(get_option('addify_apnu_enable_module_checkout_page'))) {
				$this->addify_apnu_enable_module_checkout_page = get_option( 'addify_apnu_enable_module_checkout_page');	
			} else {
				$this->addify_apnu_enable_module_checkout_page = '';
			}

			if (!empty(get_option('addify_apnu_exclude_user_roles'))) {
				$this->addify_apnu_exclude_user_roles = get_option( 'addify_apnu_exclude_user_roles');	
			} else {
				$this->addify_apnu_exclude_user_roles = array();
			}


			if (!empty(get_option('addify_apnu_account_creation_message'))) {
				$this->addify_apnu_account_creation_message = get_option( 'addify_apnu_account_creation_message');	
			} else {
				$this->addify_apnu_account_creation_message = '';
			}

			if (!empty(get_option('addify_apnu_account_pending_message'))) {
				$this->addify_apnu_account_pending_message = get_option( 'addify_apnu_account_pending_message');	
			} else {
				$this->addify_apnu_account_pending_message = '';
			}

			if (!empty(get_option('addify_apnu_account_disapproved_message'))) {
				$this->addify_apnu_account_disapproved_message = get_option( 'addify_apnu_account_disapproved_message');	
			} else {
				$this->addify_apnu_account_disapproved_message = '';
			}



			if (!empty(get_option('addify_apnu_enable_admin_email_notification'))) {
				$this->addify_apnu_enable_admin_email_notification = get_option( 'addify_apnu_enable_admin_email_notification');
			} else {
				$this->addify_apnu_enable_admin_email_notification = '';	
			}

			if (!empty(get_option('addify_apnu_admin_email'))) {
				$this->addify_apnu_admin_email = get_option( 'addify_apnu_admin_email');
			} else {
				$this->addify_apnu_admin_email = get_option('admin_email');	
			}

			if (!empty(get_option('addify_apnu_admin_email_subject'))) {
				$this->addify_apnu_admin_email_subject = get_option( 'addify_apnu_admin_email_subject');
			} else {
				$this->addify_apnu_admin_email_subject = '';	
			}

			if (!empty(get_option('addify_apnu_admin_email_heading'))) {
				$this->addify_apnu_admin_email_heading = get_option( 'addify_apnu_admin_email_heading');
			} else {
				$this->addify_apnu_admin_email_heading = '';	
			}

			if (!empty(get_option('addify_apnu_admin_email_text'))) {
				$this->addify_apnu_admin_email_text = get_option( 'addify_apnu_admin_email_text');
			} else {
				$this->addify_apnu_admin_email_text = '';	
			}


			if (!empty(get_option('addify_apnu_enable_pending_email_notification'))) {
				$this->addify_apnu_enable_pending_email_notification = get_option( 'addify_apnu_enable_pending_email_notification');
			} else {
				$this->addify_apnu_enable_pending_email_notification = '';	
			}

			if (!empty(get_option('addify_apnu_pending_email_subject'))) {
				$this->addify_apnu_pending_email_subject = get_option( 'addify_apnu_pending_email_subject');
			} else {
				$this->addify_apnu_pending_email_subject = '';	
			}

			if (!empty(get_option('addify_apnu_pending_email_heading'))) {
				$this->addify_apnu_pending_email_heading = get_option( 'addify_apnu_pending_email_heading');
			} else {
				$this->addify_apnu_pending_email_heading = '';	
			}

			if (!empty(get_option('addify_apnu_pending_email_text'))) {
				$this->addify_apnu_pending_email_text = get_option( 'addify_apnu_pending_email_text');
			} else {
				$this->addify_apnu_pending_email_text = '';	
			}

			add_action( 'user_register', array( $this, 'addify_apnu_status_user_woocommerce' ) );
			add_filter('wp_authenticate_user', array($this, 'addify_apnu_auth_login'));

			add_action('woocommerce_registration_redirect', array($this, 'addify_apnu_user_autologout'), 2);
		}


		public function addify_apnu_status_user_woocommerce( $customer_id ) {

			$user = new WP_User($customer_id);

			$user_login = stripslashes( $user->data->user_login );
			$user_email = stripslashes( $user->data->user_email );
			$from_name  = get_option('woocommerce_email_from_name');
			$from_email = get_option('woocommerce_email_from_address');
				
			// More headers
			$headers  = 'MIME-Version: 1.0' . "\n";
			$headers .= 'Content-type:text/html' . "\n";
			$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

			if ('yes' == $this->addify_apnu_enable_module) {


				$roles = ( array ) $user->roles;

				$default_role = $roles[0];

				if (!in_array( $default_role, $this->addify_apnu_exclude_user_roles)) {

					if ( is_checkout() && 'yes' == $this->addify_apnu_enable_module_checkout_page ) {
						update_user_meta( $customer_id, 'apnu_new_user_status', 'pending');
					} elseif ( !is_checkout() ) {
						update_user_meta( $customer_id, 'apnu_new_user_status', 'pending');
					} else {
						update_user_meta( $customer_id, 'apnu_new_user_status', 'approved');
					}


					//Send Email to admin to inform that a user is pending for approval. If admin notification is enabled.
					if ('yes' == $this->addify_apnu_enable_admin_email_notification) {

						$afapnu_admin_email_message = __($this->addify_apnu_admin_email_text, 'addify_approve_new_user');

						$default_admin_url = admin_url( 'users.php?afapnu-status-query-submit=addify-approve-new-user&apnu_action_email=approve&paged=1&user=' . $customer_id );
						$approve_link      = wp_nonce_url($default_admin_url );

						$default_admin_url2 = admin_url( 'users.php?afapnu-status-query-submit=approve-new-user&apnu_action_email=disapprove&paged=1&user=' . $customer_id );
						$disapprove_link    = wp_nonce_url($default_admin_url2 );

						$user_id = $customer_id;
						$user = get_userdata($user_id);
						$home_address_1  = $_POST['home_address_1'];
						$home_city  = $_POST['home_city'];
						$home_state  = $_POST['home_state'];
						$home_postcode  = $_POST['home_postcode'];

						$personal_email = $_POST['personal_email'];
						$business_email = $_POST['email'];

						$billing_address_1  = $_POST['billing_address_1'];
						$billing_city  = $_POST['billing_city'];
						$billing_state  = $_POST['billing_state'];
						$billing_postcode  = $_POST['billing_postcode'];


						$business_name = $_POST['business_name'];
						$ein = $_POST['ein'];
						$dba = $_POST['dba'];
						$business_url = $_POST['business_url'];

						$business_phone = $_POST['business_phone'];
						$business_type_ecommerce = $_POST['business_type_ecommerce'];
						$about_your_business = $_POST['about_your_business'];

						

						$cat_list = $_POST['reg_product_cat_select'];
						if ($cat_list) {
							$cat_array = array();
							foreach ($cat_list as $newvalue_item) {
								$term = get_term_by('id', $newvalue_item, 'product_cat');
								$name = $term->name;
								$cat_array[] = $name;
							}
							$reg_product_cat_select = implode(",", $cat_array);
						}

					
							$brand_list = $_POST['reg_product_brand_select'];
							if ($brand_list) {
								$brand_array = array();
								foreach ($brand_list as $newvalue_item) {
									$term = get_term_by('id', $newvalue_item, 'product_brand');
									$name = $term->name;
									$brand_array[] = $name;
								}
								$reg_product_brand_select = implode(",", $brand_array);
							}
						

						$business_type = $_POST['business_type'];
						
						$business_type = implode(",", $business_type);


						$user_data = '	<div class="wrap view_user_admin">
						<table class="widefat fixed" style="width: 100%; margin-bottom:20px" cellspacing="0">
							<thead>
								<tr>
					
									<th style="width: 200px;" class="column-columnname" scope="col"> Field Name </th>
									<th class="column-columnname" scope="col">Value</th>
								</tr>
							</thead>
					
							<tbody>
								<tr style="background: #f7f7f7; font-size: 20px;">
									<td colspan="2" class="column-columnname user-title"><strong>Personal Info</strong></td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>First name</strong></td>
									<td class="column-columnname">'.$_POST['billing_first_name'].'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Last name</strong></td>
									<td class="column-columnname">'.$_POST['billing_last_name'].'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Personal Email</strong></td>
									<td class="column-columnname">'.$personal_email.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Home Address</strong></td>
									<td class="column-columnname">'.$home_address_1.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>City</strong></td>
									<td class="column-columnname">'.$home_city.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>State</strong></td>
									<td class="column-columnname">'.$home_state.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Zip</strong></td>
									<td class="column-columnname">'.$home_postcode.'</td>
								</tr>
								<tr style="background: #f7f7f7; font-size: 20px;">
									<td  colspan="2" class="column-columnname user-title"><strong>Business Info</strong></td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Business Name</strong></td>
									<td class="column-columnname">'.$business_name.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>EIN</strong></td>
									<td class="column-columnname">'.$ein.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>DBA</strong></td>
									<td class="column-columnname">'.$dba.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Business Website</strong></td>
									<td class="column-columnname">'.$business_url.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Business Email</strong></td>
									<td class="column-columnname">'.$business_email.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Business Phone</strong></td>
									<td class="column-columnname">'.$business_phone.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Business Address</strong></td>
									<td class="column-columnname">'.$billing_address_1.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>City</strong></td>
									<td class="column-columnname">'.$billing_city.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>State</strong></td>
									<td class="column-columnname">'.$billing_state.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Zip</strong></td>
									<td class="column-columnname">'.$billing_postcode.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Business Type</strong></td>
									<td class="column-columnname">'.$business_type.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Website Domain List</strong></td>
									<td class="column-columnname">'.$business_type_ecommerce.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>About your business</strong></td>
									<td class="column-columnname">'.$about_your_business.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Brands of Interest</strong></td>
									<td class="column-columnname">'.$reg_product_brand_select.'</td>
								</tr>
								<tr>
									<td class="column-columnname label-user"><strong>Categories of Interest</strong></td>
									<td class="column-columnname">'.$reg_product_cat_select.'</td>
								</tr>
							</tbody>
						</table>
						</div>';


						$ad_msg = str_replace('{username}', $user_login, $afapnu_admin_email_message);
						$ad_msg = str_replace('{email}', $user_email, $ad_msg);
						$ad_msg = str_replace('{approve_link}', $approve_link, $ad_msg);
						$ad_msg = str_replace('{disapprove_link}', $disapprove_link, $ad_msg);

						$ad_msg = str_replace('{user_data}', $user_data, $ad_msg);
						

						$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

						$message1 = $this->addify_apnu_email_template($this->addify_apnu_admin_email_heading, $message);

						wp_mail( $this->addify_apnu_admin_email, esc_html__($this->addify_apnu_admin_email_subject, 'addify_approve_new_user'), $message1, $headers );

					}


					//Send Email to user to inform that a user is pending for approval. If user notification is enabled.
					if ('yes' == $this->addify_apnu_enable_pending_email_notification) {

						$afapnu_email_message = __($this->addify_apnu_pending_email_text, 'addify_approve_new_user');

						$ad_msg = str_replace('{username}', $user_login, $afapnu_email_message);
						$ad_msg = str_replace('{email}', $user_email, $ad_msg);
						

						$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

						$message1 = $this->addify_apnu_email_template($this->addify_apnu_pending_email_heading, $message);

						wp_mail( $user_email, esc_html__($this->addify_apnu_pending_email_subject, 'addify_approve_new_user'), $message1, $headers );

					}




				} else {

					update_user_meta( $customer_id, 'apnu_new_user_status', 'approved');
				}



			}
		}

		public function addify_apnu_user_autologout() {

			if ( is_user_logged_in() ) {

				if ('yes' == $this->addify_apnu_enable_module) {

					$current_user = wp_get_current_user();
					$user_id      = $current_user->ID;

					$roles = ( array ) $current_user->roles;

					$default_role = $roles[0];

					if (!in_array( $default_role, $this->addify_apnu_exclude_user_roles)) {


						$approved_status = get_user_meta($user_id, 'apnu_new_user_status', true);
						//if the user hasn't been approved yet by WP Approve User plugin, destroy the cookie to kill the session and log them out
						if ( 'approved' == $approved_status ) {
							return;

						} elseif ('pending' == $approved_status) {
							wp_logout();
							WC()->session->set( 'refresh_totals', true );
							throw new Exception( __( $this->addify_apnu_account_creation_message , 'addify_approve_new_user' ) );
							die();
						} elseif ('disapproved' == $approved_status) {

							wp_logout();
							WC()->session->set( 'refresh_totals', true );
							throw new Exception( __( $this->addify_apnu_account_disapproved_message , 'addify_approve_new_user' ) );
							die();
						} else {
							return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
						}



					} else {

						return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
					}


				} else {

					return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
				}

			}

		}


		public function addify_apnu_auth_login ( $user) {

			if ('yes' == $this->addify_apnu_enable_module) {

				$status = get_user_meta($user->ID, 'apnu_new_user_status', true);
				

				if ( empty( $status ) ) {
					// the user does not have a status so let's assume the user is good to go
					return $user;
				}

				$message = false;
				switch ( $status ) {
					case 'pending':
						$pending_message = $this->addify_apnu_account_pending_message;
						$message         = new WP_Error( 'pending_approval', __($pending_message, 'addify_approve_new_user') );
						break;
					case 'disapproved':
						$disapproved_message = $this->addify_apnu_account_disapproved_message;
						$message             = new WP_Error( 'disapproved_access', __($disapproved_message, 'addify_approve_new_user') );
						break;
					case 'approved':
						$message = $user;
						break;
				}

				return $message;

			} else {

				return $user;
			}
		}


		public function addify_apnu_email_template( $heading, $message) {

			$af_footer_data = get_option('woocommerce_email_footer_text');
			$new_footer     = str_replace('{site_address}', get_option('home'), $af_footer_data);
			$new_footer     = str_replace('{site_title}', get_option('blogname'), $new_footer);

			$new_footer = str_replace('{WooCommerce}', '<a href="https://woocommerce.com" style=" font-weight: normal; text-decoration: underline;">WooCommerce</a>', $new_footer);

			
			$html = '

			<style>
				a { color: ' . esc_attr(get_option('woocommerce_email_base_color')) . ';}
				h2 { color: ' . esc_attr(get_option('woocommerce_email_base_color')) . ';}
			</style>

			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				</head>
				<body>
					<div id="wrapper" dir="ltr" style="background-color: ' . esc_attr(get_option('woocommerce_email_background_color')) . '; margin: 0; padding: 70px 0; width: 100%; -webkit-text-size-adjust: none;">
						<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td valign="top" align="center">
										<table id="template_container" style="background-color: ' . esc_attr(get_option('woocommerce_email_body_background_color')) . '; border: 0px solid #cd3333; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;" width="600" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											<tr>
											<td><div id="template_header_image" style="text-align: center;">
											<p style="margin-top: 30px;"><a href="'.esc_url(get_site_url()).'"><img src="' . esc_url(get_option('woocommerce_email_header_image')) . '" alt="" style="border: none; display: inline-block; font-size: 14px; font-weight: bold; height: auto; outline: none; text-decoration: none; text-transform: capitalize; vertical-align: middle; max-width: 100%; margin-left: 0; margin-right: 0;"></a></p>
										</div></td>
											</tr>
												<tr style="display:none">
													<td valign="top" align="center">
													<!-- Header -->
														<table class="customtest" id="template_header" style="background-color: ' . esc_attr(get_option('woocommerce_email_base_color')) . '; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; border-radius: 3px 3px 0 0;" width="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
																<tr>
																	<td id="header_wrapper" style="padding: 36px 48px; display: block;">
																		<h1 style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #6a7d3a; color: #ffffff;">' . esc_html__($heading, 'addify_choose_userrole') . '</h1>
																	</td>
																</tr>
															</tbody>
														</table>
													<!-- End Header -->
													</td>
												</tr>
												<tr>
													<td valign="top" align="center">
													<!-- Body -->
														<table id="template_body" width="600" cellspacing="0" cellpadding="0" border="0">
															<tbody>
																<tr>
																	<td id="body_content" style="background-color: ' . esc_attr(get_option('woocommerce_email_body_background_color')) . ';" valign="top">
																	<!-- Content -->
																		<table width="100%" cellspacing="0" cellpadding="20" border="0">
																			<tbody>
																				<tr>
																					<td style="padding: 48px 48px 32px;" valign="top">
																						<div id="body_content_inner" style="color: ' . esc_attr(get_option('woocommerce_email_text_color')) . '; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 16px; line-height: 150%; text-align: left;">
																							<p style="margin: 0 0 16px;">' . $message . '</p>
																							
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
													<!-- End Body -->
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td valign="top" align="center">
									<!-- Footer -->
									<table id="template_footer" width="600" cellspacing="0" cellpadding="10" border="0">
										<tbody>
											<tr>
												<td style="padding: 0; border-radius: 6px; background: #1a1818;" valign="top">
													<table width="100%" cellspacing="0" cellpadding="10" border="0">
														<tbody>
															<tr>
																<td colspan="2" id="credit" style="border-radius: 6px; border: 0; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: center; padding: 24px 0; padding-bottom: 0px;" valign="middle">
																	<p style="margin: 0 0 16px;"><a href="http://appareldirectdistributor.com" style="color: #fff; text-decoration:none;">' . $new_footer . '</a></p>
																	<div style="text-align: center;">
<br>
<div class="contact_detail"><span style="color: #fff;">390 Cassell Street Winston-Salem, NC 27107</span></div>
<div class="contact_detail" style="color: #fff;"><span><a href="tel:3362652255" style="color: #FFFFFF;text-decoration: none;">336-265-2255</a>&nbsp;&nbsp;&nbsp; | &nbsp; <a href="mailto:customerservice@appareldirectdistributor.com" style="color: #FFFFFF;text-decoration: none;">customerservice@appareldirectdistributor.com</a></span><br><br>
	© '.date('Y').' '.get_bloginfo( 'name' ).'
</div>
<div class="contact_detail">
  &nbsp;
</div>
</div>
																</td>
															</tr>
															<tr><td style="padding:9px" class="mcnFollowBlockInner" valign="top" align="center">
															<table class="mcnFollowContentContainer" style="min-width:100%;" width="100%" cellspacing="0" cellpadding="0" border="0">
															   <tbody>
																  <tr>
																	 <td style="padding-left:9px; padding-right:9px;" align="center">
																		<table style="min-width:100%;" class="mcnFollowContent" width="100%" cellspacing="0" cellpadding="0" border="0">
																		   <tbody>
																			  <tr>
																				 <td style="padding-top:0px; padding-right:9px; padding-left:9px; padding-bottom: 20px;" valign="top" align="center">
																					<table cellspacing="0" cellpadding="0" border="0" align="center">
																					   <tbody>
																						  <tr>
																							 <td valign="top" align="center">
																										 <table style="display:block;" width="100%" cellspacing="0" cellpadding="0" border="0" align="left">
																											<tbody>
																											   <tr>
																												  <td style="padding-right:5px; padding-bottom:0px;" class="mcnFollowContentItemContainer" valign="top">
																													 <table class="mcnFollowContentItem" width="100%" cellspacing="0" cellpadding="0" border="0">
																														<tbody>
																														   <tr>
																															  <td style="padding-top:5px;  padding-bottom:5px; " valign="middle" align="left">
																																 <table width="" cellspacing="0" cellpadding="0" border="0" align="left">
																																	<tbody>
																																	   <tr>
																																		  <td class="mcnFollowIconContent" width="24" valign="middle" align="center" style=" padding-right:10px;">
																																			 <a href="#" target="_blank"><img src="'.get_template_directory_uri().'/assets/images/facebook-img.png" style="display:block;" class="" width="24" height="24"></a>
																																		  </td>
																																		  <td class="mcnFollowIconContent" width="24" valign="middle" align="center" style=" padding-right:10px;padding-left:9px;">
																																			 <a href="#" target="_blank"><img src="'.get_template_directory_uri().'/assets/images/twitter-img.png" style="display:block;" class="" width="24" height="24"></a>
																																		  </td>
																																		  <td class="mcnFollowIconContent" width="24" valign="middle" align="center" style=" padding-right:10px;padding-left:9px;">
																																			 <a href="#" target="_blank"><img src="'.get_template_directory_uri().'/assets/images/instagram-img.png" style="display:block;" class="" width="24" height="24"></a>
																																		  </td>
																																	   </tr>
																																	</tbody>
																																 </table>
																															  </td>
																														   </tr>
																														</tbody>
																													 </table>
																												  </td>
																											   </tr>
																											</tbody>
																										 </table>
																							 </td>
																						  </tr>
																					   </tbody>
																					</table>
																				 </td>
																			  </tr>
																		   </tbody>
																		</table>
																	 </td>
																  </tr>
															   </tbody>
															</table>
														 </td></tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								<!-- End Footer -->
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</body>
			</html>';

			return $html;


		}

	}

	new Addify_Approve_New_User_Front();
}
