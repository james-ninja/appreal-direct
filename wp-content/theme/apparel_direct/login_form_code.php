<?php
$login_page_link = get_field('login_page_link', 'option');

$login_email_label = get_field('login_email_label', $login_page_link);
$login_password_label = get_field('login_password_label', $login_page_link);
$login_login_button_text = get_field('login_login_button_text', $login_page_link);
$login_forgot_password_text = get_field('login_forgot_password_text', $login_page_link);
$login_dont_have_account_text = get_field('login_dont_have_account_text', $login_page_link);
$login_create_account_text = get_field('login_create_account_text', $login_page_link);
?>
<form name="login_form" id="login_form" action="" method="post" class="login_form">
	<div class="login_res">
	</div>
	<div class="form-group">
		<label for="user_email"><?php echo $login_email_label; ?></label><span class="star">*</span>
		<input type="text" name="user_email" id="user_email" class="form-control" required="required">
	</div>
	<div class="form-group">
		<label for="user_password"><?php echo $login_password_label; ?></label><span class="star">*</span>
		<input type="password" name="user_password" id="user_password" class="form-control" required="required">
		<i class="fa fa-eye" id="togglePassword"></i>
	</div>
	<div class="form-group">
		<div class="forgetmenot">
			<label class="checkbox">
				<input name="rememberme" class="checkbox-input" type="checkbox" id="rememberme" value="forever" />
				<span class="checkbox-checkmark-box">
					<span class="checkbox-checkmark"></span>
				</span>
				Remember Me
			</label>
		</div>
		<?php if ($login_forgot_password_text) { ?>
			<div class="forgot_password">
				<a href="<?php echo wc_lostpassword_url(); ?>"><?php echo $login_forgot_password_text; ?></a>
			</div>
		<?php } ?>
	</div>
	<div class="form-group">
		<div class="login_btn">
			<input type="hidden" name="action" value="custom_login">
			<input type="submit" name="login_submit" id="login_submit" class="btn-primary btn" value="<?php echo $login_login_button_text; ?>">
			<span class="ajax-loader"></span>
		</div>
		<?php if ($login_dont_have_account_text || $login_create_account_text) { ?>
			<div class="create_account_link">
				<p>
					<?php echo $login_dont_have_account_text; ?>
					<?php if ($login_create_account_text) { ?>
						<a href="<?php echo wc_get_page_permalink('myaccount'); ?>"><?php echo $login_create_account_text; ?></a>
					<?php } ?>
				</p>
			</div>
		<?php } ?>
	</div>
</form>