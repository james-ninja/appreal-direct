<?php
if (!defined('WPINC')) {
	die;
}
if (!class_exists('Addify_Approve_New_User_Admin')) {

	class Addify_Approve_New_User_Admin extends Addify_Approve_New_User
	{


		public $addify_apnu_enable_approved_email_notification;
		public $addify_apnu_approved_email_subject;
		public $addify_apnu_approved_email_heading;
		public $addify_apnu_approved_email_text;

		public $addify_apnu_enable_disapproved_email_notification;
		public $addify_apnu_disapproved_email_subject;
		public $addify_apnu_disapproved_email_heading;
		public $addify_apnu_disapproved_email_text;


		public function __construct()
		{

			if (!empty(get_option('addify_apnu_enable_approved_email_notification'))) {
				$this->addify_apnu_enable_approved_email_notification = get_option('addify_apnu_enable_approved_email_notification');
			} else {
				$this->addify_apnu_enable_approved_email_notification = '';
			}

			if (!empty(get_option('addify_apnu_approved_email_subject'))) {
				$this->addify_apnu_approved_email_subject = get_option('addify_apnu_approved_email_subject');
			} else {
				$this->addify_apnu_approved_email_subject = '';
			}

			if (!empty(get_option('addify_apnu_approved_email_heading'))) {
				$this->addify_apnu_approved_email_heading = get_option('addify_apnu_approved_email_heading');
			} else {
				$this->addify_apnu_approved_email_heading = '';
			}

			if (!empty(get_option('addify_apnu_approved_email_text'))) {
				$this->addify_apnu_approved_email_text = get_option('addify_apnu_approved_email_text');
			} else {
				$this->addify_apnu_approved_email_text = '';
			}



			if (!empty(get_option('addify_apnu_enable_disapproved_email_notification'))) {
				$this->addify_apnu_enable_disapproved_email_notification = get_option('addify_apnu_enable_disapproved_email_notification');
			} else {
				$this->addify_apnu_enable_disapproved_email_notification = '';
			}

			if (!empty(get_option('addify_apnu_disapproved_email_subject'))) {
				$this->addify_apnu_disapproved_email_subject = get_option('addify_apnu_disapproved_email_subject');
			} else {
				$this->addify_apnu_disapproved_email_subject = '';
			}

			if (!empty(get_option('addify_apnu_disapproved_email_heading'))) {
				$this->addify_apnu_disapproved_email_heading = get_option('addify_apnu_disapproved_email_heading');
			} else {
				$this->addify_apnu_disapproved_email_heading = '';
			}

			if (!empty(get_option('addify_apnu_disapproved_email_text'))) {
				$this->addify_apnu_disapproved_email_text = get_option('addify_apnu_disapproved_email_text');
			} else {
				$this->addify_apnu_disapproved_email_text = '';
			}


			add_action('admin_enqueue_scripts', array($this, 'addify_apnu_admin_assets'));
			add_action('admin_menu', array($this, 'addify_apnu_menu_item'));
			add_action('admin_init', array($this, 'addify_apnu_options'));
			add_filter('manage_users_columns', array($this, 'addify_apnu_modify_user_table'));
			add_filter('manage_users_custom_column', array($this, 'addify_apnu_modify_user_table_row'), 10, 3);
			add_filter('user_row_actions', array($this, 'addify_apnu_user_row_actions'), 10, 2);
			add_action('load-users.php', array($this, 'addify_apnu_update_action'));
			add_action('restrict_manage_users', array($this, 'addify_apnu_status_filter'), 10, 1);
			add_action('pre_user_query', array($this, 'addify_apnu_filter_user_by_status'));
			add_action('admin_footer-users.php', array($this, 'addify_apnu_admin_footer'));
			add_action('load-users.php', array($this, 'addify_apnu_bulk_action_user'));
		}


		public function addify_apnu_admin_assets()
		{


			wp_enqueue_style('addify_apnu_admin_css', plugins_url('/assets/css/addify_apnu_admin_css.css', __FILE__), false, '1.0');
			wp_enqueue_script('addify_apnu_admin_js', plugins_url('/assets/js/addify_apnu_admin_js.js', __FILE__), false, '1.0');
		}

		public function addify_apnu_menu_item()
		{

			add_menu_page(
				esc_html__('Approve New User Registration', 'addify_approve_new_user'), // page title 
				esc_html__('Approve New User Registration', 'addify_approve_new_user'), // menu title
				'manage_options', // capability
				'addify-approve-new-user',  // menu-slug
				array($this, 'addify_approve_new_user_settings'),   // function that will render its output
				plugins_url('assets/img/grey.png', __FILE__),   // link to the icon that will be displayed in the sidebar
				'10'    // position of the menu option
			);
		}


		public function addify_approve_new_user_settings()
		{

			if (isset($_GET['tab'])) {
				$active_tab = sanitize_text_field($_GET['tab']);
			} else {
				$active_tab = 'tab_one';
			}
?>
			<div class="wrap addify_apnu_main_wrap">

				<h2><?php echo esc_html__('Approve New User Registration Settings', 'addify_approve_new_user'); ?></h2>
				<?php settings_errors(); ?>

				<div class="nav-tab-wrapper">

					<a href="?page=addify-approve-new-user&tab=tab_one" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_one' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('General Settings', 'addify_approve_new_user'); ?></a>
					<a href="?page=addify-approve-new-user&tab=tab_two" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_two' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Custom Message Settings', 'addify_approve_new_user'); ?></a>
					<a href="?page=addify-approve-new-user&tab=tab_three" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_three' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Email Settings', 'addify_approve_new_user'); ?></a>

				</div>

				<form method="post" action="options.php">
					<?php
					if ('tab_one' == $active_tab) {
						settings_fields('addify-apnu-setting-group-1');
						do_settings_sections('addify-apnu-1');
					}

					if ('tab_two' == $active_tab) {
						settings_fields('addify-apnu-setting-group-2');
						do_settings_sections('addify-apnu-2');
					}

					if ('tab_three' == $active_tab) {
						settings_fields('addify-apnu-setting-group-3');
						do_settings_sections('addify-apnu-3');
					}


					?>
					<?php submit_button(); ?>
				</form>

			</div>
		<?php
		}

		public function addify_apnu_options()
		{

			//Tab 1
			add_settings_section(
				'page_1_section',
				'',
				array($this, 'addify_apnu_page_1_section_callback'),
				'addify-apnu-1'
			);

			add_settings_field(
				'addify_apnu_enable_module',
				esc_html__('Enable Approve New User', 'addify_approve_new_user'),
				array($this, 'addify_apnu_enable_module_callback'),
				'addify-apnu-1',
				'page_1_section',
				array(
					esc_html__('Enable/Disable Approve new user. When this option is enabled all new registered users will be set to Pending until admin approves.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-1',
				'addify_apnu_enable_module'
			);

			add_settings_field(
				'addify_apnu_enable_module_checkout_page',
				esc_html__('Enable Approve New User at Checkout Page', 'addify_approve_new_user'),
				array($this, 'addify_apnu_enable_module_checkout_page_callback'),
				'addify-apnu-1',
				'page_1_section',
				array(
					esc_html__('Enable/Disable Approve new user at checkout page. If checked, the customer will be automatically logged in after placing the order – as per the standard WooCommerce checkout process. However, as soon as the user logs out he/she won’t be able to login again unless the account status is approved by admin', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-1',
				'addify_apnu_enable_module_checkout_page'
			);

			add_settings_field(
				'addify_apnu_exclude_user_roles',
				esc_html__('Exclude User Roles', 'addify_approve_new_user'),
				array($this, 'addify_apnu_exclude_user_roles_callback'),
				'addify-apnu-1',
				'page_1_section',
				array(
					esc_html__('Select which user roles users you want to exclude from manual approval. These user roles users will be automatically approved. ', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-1',
				'addify_apnu_exclude_user_roles'
			);


			//Tab 2
			add_settings_section(
				'page_1_section',
				'',
				array($this, 'addify_apnu_page_2_section_callback'),
				'addify-apnu-2'
			);

			add_settings_field(
				'addify_apnu_account_creation_message',
				esc_html__('Message for Users when Account is Created', 'addify_approve_new_user'),
				array($this, 'addify_apnu_account_creation_message_callback'),
				'addify-apnu-2',
				'page_1_section',
				array(
					esc_html__('First message that will be displayed to user when he/she completes the registration process, this message will be displayed only when manual approval is required.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-2',
				'addify_apnu_account_creation_message'
			);

			add_settings_field(
				'addify_apnu_account_pending_message',
				esc_html__('Message for Users when Account is pending for approval', 'addify_approve_new_user'),
				array($this, 'addify_apnu_account_pending_message_callback'),
				'addify-apnu-2',
				'page_1_section',
				array(
					esc_html__('This will be displayed when user will attempt to login after registration and his account is still pending for admin approval.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-2',
				'addify_apnu_account_pending_message'
			);

			add_settings_field(
				'addify_apnu_account_disapproved_message',
				esc_html__('Message for Users when Account is disapproved', 'addify_approve_new_user'),
				array($this, 'addify_apnu_account_disapproved_message_callback'),
				'addify-apnu-2',
				'page_1_section',
				array(
					esc_html__('Message for Users when Account is Disapproved By Admin.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-2',
				'addify_apnu_account_disapproved_message'
			);


			//Tab 3
			add_settings_section(
				'page_1_section',
				'',
				array($this, 'addify_apnu_page_3_section_callback'),
				'addify-apnu-3'
			);

			add_settings_field(
				'addify_apnu_enable_admin_email_notification',
				esc_html__('Enable admin email notification', 'addify_approve_new_user'),
				array($this, 'addify_apnu_enable_admin_email_notification_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('Enable or Disable pending user notification to admin.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_enable_admin_email_notification'
			);

			add_settings_field(
				'addify_apnu_admin_email',
				'<div class="apnu_enable_admin_email">' . esc_html__('Admin/Shop Manager Email Address', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_admin_email_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email address will be used for sending email notification to admin from this module, if this field is empty then defualt wordpress admin email address will be used.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_admin_email'
			);

			add_settings_field(
				'addify_apnu_admin_email_subject',
				'<div class="apnu_enable_admin_email">' . esc_html__('Email Subject', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_admin_email_subject_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email subject is used when pending user notification is sent to admin. ', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_admin_email_subject'
			);

			add_settings_field(
				'addify_apnu_admin_email_heading',
				'<div class="apnu_enable_admin_email">' . esc_html__('Email Heading', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_admin_email_heading_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email heading is used when pending user notification is sent to admin.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_admin_email_heading'
			);

			add_settings_field(
				'addify_apnu_admin_email_text',
				'<div class="apnu_enable_admin_email">' . esc_html__('Email Text', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_admin_email_text_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email text is used when pending user notification is sent to admin. You can use these variables in the message. {username}, {email}, {approve_link}, {disapprove_link}', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_admin_email_text'
			);

			add_settings_field(
				'addify_apnu_enable_pending_email_notification',
				esc_html__('Enable welcome/pending user email notification', 'addify_approve_new_user'),
				array($this, 'addify_apnu_enable_pending_email_notification_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('Enable or Disable welcome/pending user email notification to user from this module.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_enable_pending_email_notification'
			);

			add_settings_field(
				'addify_apnu_pending_email_subject',
				'<div class="apnu_enable_pending_email">' . esc_html__('Email Subject', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_pending_email_subject_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email subject is used when pending user notification is sent to user. ', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_pending_email_subject'
			);

			add_settings_field(
				'addify_apnu_pending_email_heading',
				'<div class="apnu_enable_pending_email">' . esc_html__('Email Heading', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_pending_email_heading_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email heading is used when pending user notification is sent to user.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_pending_email_heading'
			);

			add_settings_field(
				'addify_apnu_pending_email_text',
				'<div class="apnu_enable_pending_email">' . esc_html__('Email Text', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_pending_email_text_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email text is used when pending user notification is sent to user. You can use these variables in the message. {username}, {email}', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_pending_email_text'
			);

			add_settings_field(
				'addify_apnu_enable_approved_email_notification',
				esc_html__('Enable approved user email notification', 'addify_approve_new_user'),
				array($this, 'addify_apnu_enable_approved_email_notification_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('Enable or Disable approved user email notification to user from this module.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_enable_approved_email_notification'
			);

			add_settings_field(
				'addify_apnu_approved_email_subject',
				'<div class="apnu_enable_approved_email">' . esc_html__('Email Subject', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_approved_email_subject_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email subject is used when approved user notification is sent to user. ', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_approved_email_subject'
			);

			add_settings_field(
				'addify_apnu_approved_email_heading',
				'<div class="apnu_enable_approved_email">' . esc_html__('Email Heading', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_approved_email_heading_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email heading is used when approved user notification is sent to user.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_approved_email_heading'
			);

			add_settings_field(
				'addify_apnu_approved_email_text',
				'<div class="apnu_enable_approved_email">' . esc_html__('Email Text', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_approved_email_text_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email text is used when approved user notification is sent to user. You can use these variables in the message. {username}, {email}', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_approved_email_text'
			);

			add_settings_field(
				'addify_apnu_enable_disapproved_email_notification',
				esc_html__('Enable disapproved user email notification', 'addify_approve_new_user'),
				array($this, 'addify_apnu_enable_disapproved_email_notification_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('Enable or Disable disapproved user email notification to user from this module.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_enable_disapproved_email_notification'
			);

			add_settings_field(
				'addify_apnu_disapproved_email_subject',
				'<div class="apnu_enable_disapproved_email">' . esc_html__('Email Subject', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_disapproved_email_subject_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email subject is used when disapproved user notification is sent to user. ', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_disapproved_email_subject'
			);

			add_settings_field(
				'addify_apnu_disapproved_email_heading',
				'<div class="apnu_enable_disapproved_email">' . esc_html__('Email Heading', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_disapproved_email_heading_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email heading is used when disapproved user notification is sent to user.', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_disapproved_email_heading'
			);

			add_settings_field(
				'addify_apnu_disapproved_email_text',
				'<div class="apnu_enable_disapproved_email">' . esc_html__('Email Text', 'addify_approve_new_user') . '</div>',
				array($this, 'addify_apnu_disapproved_email_text_callback'),
				'addify-apnu-3',
				'page_1_section',
				array(
					esc_html__('This email text is used when disapproved user notification is sent to user. You can use these variables in the message. {username}, {email}', 'addify_approve_new_user'),
				)
			);
			register_setting(
				'addify-apnu-setting-group-3',
				'addify_apnu_disapproved_email_text'
			);
		}

		//Tab 1
		public function addify_apnu_page_1_section_callback()
		{
		?>
			<p><?php echo esc_html__('Manage approve new user general settings from here.', 'addify_approve_new_user'); ?></p>
		<?php
		}

		public function addify_apnu_enable_module_callback($args)
		{
		?>
			<input type="checkbox" id="addify_apnu_enable_module" name="addify_apnu_enable_module" value="yes" <?php echo checked('yes', esc_attr(get_option('addify_apnu_enable_module'))); ?>>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_enable_module_checkout_page_callback($args)
		{
		?>
			<input type="checkbox" id="addify_apnu_enable_module_checkout_page" name="addify_apnu_enable_module_checkout_page" value="yes" <?php echo checked('yes', esc_attr(get_option('addify_apnu_enable_module_checkout_page'))); ?>>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_exclude_user_roles_callback($args)
		{
		?>

			<div class="all_user_roles">
				<ul>
					<?php

					global $wp_roles;
					$roles = $wp_roles->get_names();

					if (!empty($roles)) {

						foreach ($roles as $key => $value) {
							if ('administrator' != $key) {
					?>
								<li class="par_user_role">

									<input type="checkbox" name="addify_apnu_exclude_user_roles[]" id="addify_apnu_exclude_user_roles" value="<?php echo esc_attr($key); ?>" <?php
																																												if (!empty(get_option('addify_apnu_exclude_user_roles'))) {
																																													if (in_array($key, get_option('addify_apnu_exclude_user_roles'))) {
																																														echo 'checked';
																																													}
																																												}
																																												?> />
									<?php echo esc_attr($value); ?>

								</li>
					<?php
							}
						}
					}
					?>
				</ul>
			</div>

			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}


		//Tab 2
		public function addify_apnu_page_2_section_callback()
		{
		?>
			<p><?php echo esc_html__('Manage approve new user message settings from here.', 'addify_approve_new_user'); ?></p>
		<?php
		}

		public function addify_apnu_account_creation_message_callback($args)
		{
		?>
			<textarea name="addify_apnu_account_creation_message" id="addify_apnu_account_creation_message" rows="10" cols="70"><?php echo esc_textarea(get_option('addify_apnu_account_creation_message')); ?></textarea>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_account_pending_message_callback($args)
		{
		?>
			<textarea name="addify_apnu_account_pending_message" id="addify_apnu_account_pending_message" rows="10" cols="70"><?php echo esc_textarea(get_option('addify_apnu_account_pending_message')); ?></textarea>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_account_disapproved_message_callback($args)
		{
		?>
			<textarea name="addify_apnu_account_disapproved_message" id="addify_apnu_account_disapproved_message" rows="10" cols="70"><?php echo esc_textarea(get_option('addify_apnu_account_disapproved_message')); ?></textarea>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}


		//Tab 3
		public function addify_apnu_page_3_section_callback()
		{
		?>
			<p><?php echo esc_html__('Manage email settings.', 'addify_approve_new_user'); ?></p>
		<?php
		}

		public function addify_apnu_enable_admin_email_notification_callback($args)
		{
		?>
			<input type="checkbox" id="addify_apnu_enable_admin_email_notification" name="addify_apnu_enable_admin_email_notification" value="yes" <?php checked('yes', esc_attr(get_option('addify_apnu_enable_admin_email_notification'))); ?>>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_admin_email_callback($args)
		{
		?>
			<div class="apnu_enable_admin_email">
				<input type="email" id="addify_apnu_admin_email" class="setting_fields" name="addify_apnu_admin_email" value="<?php echo esc_attr(get_option('addify_apnu_admin_email')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_admin_email_subject_callback($args)
		{
		?>
			<div class="apnu_enable_admin_email">
				<input type="text" id="addify_apnu_admin_email_subject" class="setting_fields" name="addify_apnu_admin_email_subject" value="<?php echo esc_attr(get_option('addify_apnu_admin_email_subject')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_admin_email_heading_callback($args)
		{
		?>
			<div class="apnu_enable_admin_email">
				<input type="text" id="addify_apnu_admin_email_heading" class="setting_fields" name="addify_apnu_admin_email_heading" value="<?php echo esc_attr(get_option('addify_apnu_admin_email_heading')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_admin_email_text_callback($args)
		{
		?>
			<div class="apnu_enable_admin_email">
				<?php

				$content   = stripslashes(get_option('addify_apnu_admin_email_text'));
				$editor_id = 'addify_apnu_admin_email_text';
				$settings  = array(
					'wpautop' => false,
					'tinymce' => true,
					'textarea_rows' => 10,
					'quicktags' => array('buttons' => 'em,strong,link',),
					'quicktags' => true,
					'tinymce' => true,
				);

				wp_editor($content, $editor_id, $settings);

				?>
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_enable_pending_email_notification_callback($args)
		{
		?>
			<input type="checkbox" id="addify_apnu_enable_pending_email_notification" name="addify_apnu_enable_pending_email_notification" value="yes" <?php checked('yes', esc_attr(get_option('addify_apnu_enable_pending_email_notification'))); ?>>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_pending_email_subject_callback($args)
		{
		?>
			<div class="apnu_enable_pending_email">
				<input type="text" id="addify_apnu_pending_email_subject" class="setting_fields" name="addify_apnu_pending_email_subject" value="<?php echo esc_attr(get_option('addify_apnu_pending_email_subject')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_pending_email_heading_callback($args)
		{
		?>
			<div class="apnu_enable_pending_email">
				<input type="text" id="addify_apnu_pending_email_heading" class="setting_fields" name="addify_apnu_pending_email_heading" value="<?php echo esc_attr(get_option('addify_apnu_pending_email_heading')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_pending_email_text_callback($args)
		{
		?>
			<div class="apnu_enable_pending_email">
				<?php

				$content   = stripslashes(get_option('addify_apnu_pending_email_text'));
				$editor_id = 'addify_apnu_pending_email_text';
				$settings  = array(
					'wpautop' => false,
					'tinymce' => true,
					'textarea_rows' => 10,
					'quicktags' => array('buttons' => 'em,strong,link',),
					'quicktags' => true,
					'tinymce' => true,
				);

				wp_editor($content, $editor_id, $settings);

				?>
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_enable_approved_email_notification_callback($args)
		{
		?>
			<input type="checkbox" id="addify_apnu_enable_approved_email_notification" name="addify_apnu_enable_approved_email_notification" value="yes" <?php checked('yes', esc_attr(get_option('addify_apnu_enable_approved_email_notification'))); ?>>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_approved_email_subject_callback($args)
		{
		?>
			<div class="apnu_enable_approved_email">
				<input type="text" id="addify_apnu_approved_email_subject" class="setting_fields" name="addify_apnu_approved_email_subject" value="<?php echo esc_attr(get_option('addify_apnu_approved_email_subject')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_approved_email_heading_callback($args)
		{
		?>
			<div class="apnu_enable_approved_email">
				<input type="text" id="addify_apnu_approved_email_heading" class="setting_fields" name="addify_apnu_approved_email_heading" value="<?php echo esc_attr(get_option('addify_apnu_approved_email_heading')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_approved_email_text_callback($args)
		{
		?>
			<div class="apnu_enable_approved_email">
				<?php

				$content   = stripslashes(get_option('addify_apnu_approved_email_text'));
				$editor_id = 'addify_apnu_approved_email_text';
				$settings  = array(
					'wpautop' => false,
					'tinymce' => true,
					'textarea_rows' => 10,
					'quicktags' => array('buttons' => 'em,strong,link',),
					'quicktags' => true,
					'tinymce' => true,
				);

				wp_editor($content, $editor_id, $settings);

				?>
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_enable_disapproved_email_notification_callback($args)
		{
		?>
			<input type="checkbox" id="addify_apnu_enable_disapproved_email_notification" name="addify_apnu_enable_disapproved_email_notification" value="yes" <?php checked('yes', esc_attr(get_option('addify_apnu_enable_disapproved_email_notification'))); ?>>
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
		<?php
		}

		public function addify_apnu_disapproved_email_subject_callback($args)
		{
		?>
			<div class="apnu_enable_disapproved_email">
				<input type="text" id="addify_apnu_disapproved_email_subject" class="setting_fields" name="addify_apnu_disapproved_email_subject" value="<?php echo esc_attr(get_option('addify_apnu_disapproved_email_subject')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_disapproved_email_heading_callback($args)
		{
		?>
			<div class="apnu_enable_disapproved_email">
				<input type="text" id="addify_apnu_disapproved_email_heading" class="setting_fields" name="addify_apnu_disapproved_email_heading" value="<?php echo esc_attr(get_option('addify_apnu_disapproved_email_heading')); ?>">
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}

		public function addify_apnu_disapproved_email_text_callback($args)
		{
		?>
			<div class="apnu_enable_disapproved_email">
				<?php

				$content   = stripslashes(get_option('addify_apnu_disapproved_email_text'));
				$editor_id = 'addify_apnu_disapproved_email_text';
				$settings  = array(
					'wpautop' => false,
					'tinymce' => true,
					'textarea_rows' => 10,
					'quicktags' => array('buttons' => 'em,strong,link',),
					'quicktags' => true,
					'tinymce' => true,
				);

				wp_editor($content, $editor_id, $settings);

				?>
				<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			</div>
		<?php
		}


		public function addify_apnu_modify_user_table($column)
		{


			$column['addify_apnu_user_status'] = esc_html__('User Status', 'addify_approve_new_user');
			return $column;
		}

		public function addify_apnu_modify_user_table_row($val, $column_name, $user_id)
		{
			switch ($column_name) {

				case 'addify_apnu_user_status':
					$user_status = get_user_meta($user_id, 'apnu_new_user_status', true);
					return ucfirst($user_status);
				default:
			}
			return $val;
		}

		public function addify_apnu_user_row_actions($actions, $user)
		{

			if (get_current_user_id() == $user->ID) {
				return $actions;
			}

			if (is_super_admin($user->ID)) {
				return $actions;
			}

			$apnu_user_status = get_user_meta($user->ID, 'apnu_new_user_status', true);

			$approve_link = add_query_arg(array('action' => 'user_approve', 'user' => $user->ID));
			$approve_link = remove_query_arg(array('new_role'), $approve_link);
			$approve_link = wp_nonce_url($approve_link, 'addify-apnu');

			$deny_link = add_query_arg(array('action' => 'user_disapprove', 'user' => $user->ID));
			$deny_link = remove_query_arg(array('new_role'), $deny_link);
			$deny_link = wp_nonce_url($deny_link, 'addify-apnu');

			$approve_action = '<a href="' . esc_url($approve_link) . '">' . esc_html__('User Approve', 'addify_approve_new_user') . '</a>';
			$deny_action    = '<a href="' . esc_url($deny_link) . '">' . esc_html__('User Disapprove', 'addify_approve_new_user') . '</a>';

			if ('pending' == $apnu_user_status) {
				$actions[] = $approve_action;
				$actions[] = $deny_action;
			} elseif ('approved' == $apnu_user_status) {
				$actions[] = $deny_action;
			} elseif ('disapproved' == $apnu_user_status) {
				$actions[] = $approve_action;
			}

			return $actions;
		}

public function subscribe_user_to_mailchimp($user_email) {
    
    $email = $user_email;
    $audience_id = '8546b10d0e';
    $api_key = '1117f862cd25aae0087f57415bbca0a8-us6';
    $data_center = substr($api_key, strpos($api_key, '-')+1);
    $url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $audience_id .'/members';
    $auth = base64_encode( 'user:' . $api_key );
    $arr_data = json_encode(array(
        'email_address' => $email,
        'status' => 'subscribed', //pass 'subscribed' or 'pending'
    ));
  
    $response = wp_remote_post( $url, array(
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => "Basic $auth"
            ),
            'body' => $arr_data,
        )
    );
  
    if ( is_wp_error( $response ) ) {
       $error_message = $response->get_error_message();
       echo "Something went wrong: $error_message";
    } else {
        $status_code = wp_remote_retrieve_response_code( $response );
        switch ($status_code) {
            case '200':
                echo $status_code;
                break;
            case '400':
                $api_response = json_decode( wp_remote_retrieve_body( $response ), true );
                echo $api_response['title'];
                break;
            default:
                echo 'Something went wrong. Please try again.';
                break;
        }
    }
    //wp_die();
}

		public function addify_apnu_update_action()
		{

			//Email link approval
			if (isset($_GET['apnu_action_email']) && in_array($_GET['apnu_action_email'], array('approve', 'disapprove')) && !isset($_GET['new_role'])) {

				$sendback = remove_query_arg(array('approve', 'disapprove', 'deleted', 'ids', 'apnu-status-query-submit', 'new_role'), wp_get_referer());
				if (!$sendback) {
					$sendback = admin_url('users.php');
				}

				$wp_list_table = _get_list_table('WP_Users_List_Table');
				$pagenum       = $wp_list_table->get_pagenum();
				$sendback      = add_query_arg('paged', $pagenum, $sendback);

				if (isset($_GET['user'])) {
					$user_id = absint($_GET['user']);
				} else {
					$user_id = 0;
				}


				$apnu_user_status = get_user_meta($user_id, 'apnu_new_user_status', true);

				if (isset($_GET['apnu_action_email']) && 'approve' == $_GET['apnu_action_email']) {

					$user_status = 'approved';
					//handleUserRegistration($user_id);
					
				} elseif (isset($_GET['apnu_action_email']) && 'disapprove' == $_GET['apnu_action_email']) {

					$user_status = 'disapproved';
				} else {

					$user_status = 'pending';
				}

				update_user_meta($user_id, 'apnu_new_user_status', $user_status);

				$user = new WP_User($user_id);


				//Send email to the user to inform the status of his/her user role.
				$from_name  = get_option('woocommerce_email_from_name');
				$from_email = get_option('woocommerce_email_from_address');
				$user_login = stripslashes($user->user_login);
				$user_email = stripslashes($user->user_email);
				$first_name = stripslashes($user->first_name);
				$user_pass_custom = get_user_meta($user_id, 'user_pass_custom', true);
				$business_name = get_user_meta($user_id, 'business_name', true);
				// More headers
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type:text/html' . "\n";
				$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

				if (isset($_GET['apnu_action_email']) && 'approve' == $_GET['apnu_action_email']) {

					//User approve email
					if ('yes' == $this->addify_apnu_enable_approved_email_notification) {

						$afpnu_approve_email_message = __($this->addify_apnu_approved_email_text, 'addify_approve_new_user');

						$ad_msg = str_replace('{username}', $user_login, $afpnu_approve_email_message);
						$ad_msg = str_replace('{email}', $user_email, $ad_msg);
						$ad_msg = str_replace('{password}', $user_pass_custom, $ad_msg);
						$ad_msg = str_replace('{business_name}', $business_name, $ad_msg);

						$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

						$message1 = $this->addify_apnu_email_template($this->addify_apnu_approved_email_heading, $message);

						wp_mail($user_email, esc_html__($this->addify_apnu_approved_email_subject, 'addify_approve_new_user'), $message1, $headers);
					}
				} elseif (isset($_GET['apnu_action_email']) && 'disapprove' == $_GET['apnu_action_email']) {

					//User disapprove email
					if ('yes' == $this->addify_apnu_enable_disapproved_email_notification) {

						$afpnu_disapprove_email_message = __($this->addify_apnu_disapproved_email_text, 'addify_approve_new_user');

						$ad_msg = str_replace('{username}', $user_login, $afpnu_disapprove_email_message);
						$ad_msg = str_replace('{email}', $user_email, $ad_msg);
						$ad_msg = str_replace('{first_name}', $first_name, $ad_msg);

						$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

						$message1 = $this->addify_apnu_email_template($this->addify_apnu_disapproved_email_heading, $message);

						wp_mail($user_email, esc_html__($this->addify_apnu_disapproved_email_subject, 'addify_approve_new_user'), $message1, $headers);
					}
				}
			}

			if (isset($_GET['action']) && in_array($_GET['action'], array('user_approve', 'user_disapprove')) && !isset($_GET['new_role'])) {

				check_admin_referer('addify-apnu');

				$sendback = remove_query_arg(array('user_approve', 'user_disapprove', 'deleted', 'ids', 'afapnu-status-query-submit', 'new_role'), wp_get_referer());
				if (!$sendback) {
					$sendback = admin_url('users.php');
				}

				$wp_list_table = _get_list_table('WP_Users_List_Table');
				$pagenum       = $wp_list_table->get_pagenum();
				$sendback      = add_query_arg('paged', $pagenum, $sendback);

				if (isset($_GET['user'])) {
					$user_id = absint($_GET['user']);
				} else {
					$user_id = 0;
				}

				$user = new WP_User($user_id);

				$apnu_user_status = get_user_meta($user_id, 'apnu_new_user_status', true);

				if (isset($_GET['action']) && 'user_approve' == $_GET['action']) {

					$user_status = 'approved';
					$user_email = stripslashes($user->user_email);

					/*31-5-23 mailchimp manually approved user add */

					$new_user_data = get_user_meta($user_id);
					$email =   $new_user_data['personal_email'][0];						

					$list_id = '55c794b002';
					$api = '538f38112d52ad80a19b112ca5646618-us6';

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
									'merge_fields' => array('FNAME' => $new_user_data['first_name'][0],
																'LNAME' => $new_user_data['last_name'][0],								
																'ADDRESS' => $new_user_data['home_address_1'][0].' '.$new_user_data['billing_city'][0].' '.$new_user_data['billing_state'][0],
																'PHONE' => $new_user_data['personal_phone'][0],
																'MMERGE5' => $new_user_data['business_name'][0],
															),					
									'full_name' => $new_user_data['first_name'][0].' '.$new_user_data['last_name'][0],
									'status' => 'subscribed',
								)
							)
						)
					); 
					
					/*31-5-23 mailchimp manually user add end*/
					
				} elseif (isset($_GET['action']) && 'user_disapprove' == $_GET['action']) {

					$user_status = 'disapproved';
				} else {

					$user_status = 'pending';
				}

				update_user_meta($user_id, 'apnu_new_user_status', $user_status);


				//Send email to the user to inform the status of his/her user role.
				$from_name  = get_option('woocommerce_email_from_name');
				$from_email = get_option('woocommerce_email_from_address');
				$user_login = stripslashes($user->user_login);
				$user_email = stripslashes($user->user_email);
				$first_name = stripslashes($user->first_name);

				// More headers
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type:text/html' . "\n";
				$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

				if (isset($_GET['action']) && 'user_approve' == $_GET['action']) {

					//User approve email
					if ('yes' == $this->addify_apnu_enable_approved_email_notification) {

						$afpnu_approve_email_message = __($this->addify_apnu_approved_email_text, 'addify_approve_new_user');

						$user_pass_custom = get_user_meta($user_id, 'user_pass_custom', true);
						$business_name = get_user_meta($user_id, 'business_name', true);
						$ad_msg = str_replace('{username}', $user_login, $afpnu_approve_email_message);
						$ad_msg = str_replace('{email}', $user_email, $ad_msg);
						$ad_msg = str_replace('{password}', $user_pass_custom, $ad_msg);
						$ad_msg = str_replace('{business_name}', $business_name, $ad_msg);


						$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

						$message1 = $this->addify_apnu_email_template($this->addify_apnu_approved_email_heading, $message);

						wp_mail($user_email, esc_html__($this->addify_apnu_approved_email_subject, 'addify_approve_new_user'), $message1, $headers);
					}
				} elseif (isset($_GET['action']) && 'user_disapprove' == $_GET['action']) {

					//User disapprove email
					if ('yes' == $this->addify_apnu_enable_disapproved_email_notification) {

						$afpnu_disapprove_email_message = __($this->addify_apnu_disapproved_email_text, 'addify_approve_new_user');

						$ad_msg = str_replace('{username}', $user_login, $afpnu_disapprove_email_message);
						$ad_msg = str_replace('{email}', $user_email, $ad_msg);
						$ad_msg = str_replace('{first_name}', $first_name, $ad_msg);

						$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

						$message1 = $this->addify_apnu_email_template($this->addify_apnu_disapproved_email_heading, $message);

						wp_mail($user_email, esc_html__($this->addify_apnu_disapproved_email_subject, 'addify_approve_new_user'), $message1, $headers);
					}
				}
			}
		}




		public function addify_apnu_status_filter($s_filter)
		{


			$id = 'apnu_approve_new_user_filter-' . $s_filter;

			$f_status = $this->addify_apnu_changed_status();

		?>
			<label class="screen-reader-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html__('View all users', 'addify_approve_new_user'); ?></label>
			<select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" class="anusec">
				<option value=""><?php echo esc_html__('View all users', 'addify_approve_new_user'); ?></option>
				<?php foreach ($this->addify_apnu_get_all_statuses() as $status) { ?>
					<option value="<?php echo esc_attr($status); ?>" <?php echo selected($status, $f_status); ?>>

						<?php

						if ('disapproved' == $status) {
							echo esc_html__('Disapproved', 'addify_approve_new_user');
						} else {
							echo esc_html__(ucfirst($status));
						}


						?>

					</option>
				<?php } ?>
			</select>
			<?php
			$f_button = submit_button(esc_html__('Filter', 'addify_approve_new_user'), 'button', 'afapnu-status-query-submit', false, array('id' => 'afapnu-status-query-submit'));

			echo esc_attr(apply_filters('apnu_approve_new_user_filter_button', $f_button));
			?>

			<?php


		}

		public function addify_apnu_changed_status()
		{
			if (!empty($_REQUEST['apnu_approve_new_user_filter-top']) || !empty($_REQUEST['apnu_approve_new_user_filter-bottom'])) {
				$aa =  esc_attr((!empty($_REQUEST['apnu_approve_new_user_filter-top'])) ? sanitize_text_field($_REQUEST['apnu_approve_new_user_filter-top']) : sanitize_text_field($_REQUEST['apnu_approve_new_user_filter-bottom']));
			} else {
				$aa =  null;
			}
			return $aa;
		}

		public function addify_apnu_get_all_statuses()
		{
			return array('pending', 'approved', 'disapproved');
		}

		public function addify_apnu_filter_user_by_status($qry)
		{


			global $wpdb;

			if (!is_admin()) {
				return;
			}


			if ($this->addify_apnu_changed_status() != null) {
				$filter = $this->addify_apnu_changed_status();



				$qry->query_from .= " INNER JOIN {$wpdb->usermeta} ON ( {$wpdb->users}.ID = $wpdb->usermeta.user_id )";

				if ('approved' == $filter) {
					$qry->query_fields = "DISTINCT SQL_CALC_FOUND_ROWS {$wpdb->users}.ID";
					$where             = $qry->query_from  .= " LEFT JOIN {$wpdb->usermeta} AS mt1 ON ({$wpdb->users}.ID = mt1.user_id AND mt1.meta_key = 'apnu_new_user_status')";

					$qry->query_where .= " AND ( ( $wpdb->usermeta.meta_key = 'apnu_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = 'approved' ) OR mt1.user_id IS NULL )";
				} else {
					$qry->query_where .= " AND ( ($wpdb->usermeta.meta_key = 'apnu_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = '{$filter}') )";
				}
			}
		}

		public function addify_apnu_admin_footer()
		{
			$screen = get_current_screen();

			if ('users' == $screen->id) {
			?>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						$('<option>').val('approve').text('<?php echo esc_html__('User Approve', 'addify_approve_new_user'); ?>').appendTo("select[name='action']");
						$('<option>').val('approve').text('<?php echo esc_html__('User Approve', 'addify_approve_new_user'); ?>').appendTo("select[name='action2']");

						$('<option>').val('disapprove').text('<?php echo esc_html__('User Disapprove', 'addify_approve_new_user'); ?>').appendTo("select[name='action']");
						$('<option>').val('disapprove').text('<?php echo esc_html__('User Disapprove', 'addify_approve_new_user'); ?>').appendTo("select[name='action2']");
					});
				</script>
<?php
			}
		}

		public function addify_apnu_bulk_action_user()
		{
			$screen = get_current_screen();

			if ('users' == $screen->id) {

				// get the action
				$wp_list_table = _get_list_table('WP_Users_List_Table');
				$action        = $wp_list_table->current_action();


				$allowed_actions = array('approve', 'disapprove');
				if (!in_array($action, $allowed_actions)) {
					return;
				}


				// security check
				check_admin_referer('bulk-users');

				// make sure ids are submitted
				if (isset($_REQUEST['users'])) {
					$user_ids = array_map('intval', $_REQUEST['users']);
				}

				if (empty($user_ids)) {
					return;
				}

				$sendback = remove_query_arg(array('approve', 'disapprove', 'deleted', 'ids', 'apnu_approve_new_user_filter', 'apnu_approve_new_user_filter2', 'apnu-status-query-submit', 'new_role'), wp_get_referer());
				if (!$sendback) {
					$sendback = admin_url('users.php');
				}

				$pagenum  = $wp_list_table->get_pagenum();
				$sendback = add_query_arg('paged', $pagenum, $sendback);

				//Send email to the user to inform the status of his/her user role.
				$from_name  = get_option('woocommerce_email_from_name');
				$from_email = get_option('woocommerce_email_from_address');


				// More headers
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type:text/html' . "\n";
				$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

				switch ($action) {
					case 'approve':
						$approved = 0;
						foreach ($user_ids as $user_id) {


							//Send Message to user that their account is approved. 
							$users      = new WP_User($user_id);
							$user_login = stripslashes($users->data->user_login);
							$user_email = stripslashes($users->data->user_email);
							$user_pass_custom = get_user_meta($user_id, 'user_pass_custom', true);
							$business_name = get_user_meta($user_id, 'business_name', true);
							if ('yes' == $this->addify_apnu_enable_approved_email_notification) {

								$afpnu_approve_email_message = __($this->addify_apnu_approved_email_text, 'addify_approve_new_user');

								$ad_msg = str_replace('{username}', $user_login, $afpnu_approve_email_message);
								$ad_msg = str_replace('{email}', $user_email, $ad_msg);
								$ad_msg = str_replace('{password}', $user_pass_custom, $ad_msg);
								$ad_msg = str_replace('{business_name}', $business_name, $ad_msg);

								$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

								$message1 = $this->addify_apnu_email_template($this->addify_apnu_approved_email_heading, $message);

								wp_mail($user_email, esc_html__($this->addify_apnu_approved_email_subject, 'addify_approve_new_user'), $message1, $headers);
							}



							update_user_meta($user_id, 'apnu_new_user_status', 'approved');
							$approved++;
						}

						$sendback = add_query_arg(array('approved' => $approved, 'ids' => join(',', $user_ids)), $sendback);
						break;

					case 'disapprove':
						$disapproved = 0;
						foreach ($user_ids as $user_id) {


							//Send Message to user that their account is disapproved. 
							$users = new WP_User($user_id);

							$user_login = stripslashes($users->data->user_login);
							$user_email = stripslashes($users->data->user_email);
							$first_name = stripslashes($users->first_name);

							if ('yes' == $this->addify_apnu_enable_disapproved_email_notification) {

								$afpnu_disapprove_email_message = __($this->addify_apnu_disapproved_email_text, 'addify_approve_new_user');

								$ad_msg = str_replace('{username}', $user_login, $afpnu_disapprove_email_message);
								$ad_msg = str_replace('{email}', $user_email, $ad_msg);
								$ad_msg = str_replace('{first_name}', $first_name, $ad_msg);

								$message = '<p>' . wp_kses_post($ad_msg) . '</p>';

								$message1 = $this->addify_apnu_email_template($this->addify_apnu_disapproved_email_heading, $message);

								wp_mail($user_email, esc_html__($this->addify_apnu_disapproved_email_subject, 'addify_approve_new_user'), $message1, $headers);
							}



							update_user_meta($user_id, 'apnu_new_user_status', 'disapproved');
							$disapproved++;
						}

						$sendback = add_query_arg(array('disapproved' => $disapproved, 'ids' => join(',', $user_ids)), $sendback);
						break;

					default:
						return;
				}

				$sendback = remove_query_arg(array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view'), $sendback);

				wp_redirect($sendback);
				exit();
			}
		}


		public function addify_apnu_email_template($heading, $message)
		{

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
														<table class="customtestadmin" id="template_header" style="background-color: ' . esc_attr(get_option('woocommerce_email_base_color')) . '; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; border-radius: 3px 3px 0 0;" width="100%" cellspacing="0" cellpadding="0" border="0">
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

	new Addify_Approve_New_User_Admin();
}
