<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
	$login_page_link = get_field('login_page_link', 'option');

    $login_banner = get_field('login_banner', $login_page_link);
    $login_logo = get_field('login_logo', $login_page_link);

    $forgot_password_title = get_field('forgot_password_title', 'option');
    $forgot_password_description = get_field('forgot_password_description', 'option');
    $forgot_email_label = get_field('forgot_email_label', 'option');
    $forgot_password_button_text = get_field('forgot_password_button_text', 'option');
?>
<section class="login_main forgot_password_main">
    <div class="container-fluid">
        <div class="row">
			<div class="col-md-6 col-sm-12">
				<div class="bg-img" style="background: url(<?php echo $login_banner['url']; ?>) no-repeat center; background-size: cover;">
				</div>
			</div>
            <div class="col-md-6 col-sm-12 align-self-center">
                <div class="login-form">
                    <?php if($login_logo) { ?>
                        <div class="logo_main">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <img src="<?php echo $login_logo['url']; ?>" class="custom-logo" alt="Logo">
                            </a>
                        </div>
                    <?php } ?>
					<h1><?php echo $forgot_password_title; ?></h1>
                    <?php do_action( 'woocommerce_before_lost_password_form' ); ?>
                    <form method="post" class="woocommerce-ResetPassword lost_reset_password">
                    	<?php if($forgot_password_description) { ?>
							<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( $forgot_password_description, 'woocommerce' ) ); ?></p>
						<?php } ?>
						<div class="form-group">
							<label for="user_login"><?php esc_html_e( $forgot_email_label, 'woocommerce' ); ?></label><span class="star">*</span>
							<input type="email" name="user_login" id="user_login" class="form-control" required="required">
						</div>
						<div class="clear"></div>
						<?php do_action( 'woocommerce_lostpassword_form' ); ?>
						<div class="form-group">
							<div class="login_btn">
								<input type="hidden" name="wc_reset_password" value="true" />
								<input type="submit" name="reset_password" id="reset_password" class="btn-primary btn" value="<?php esc_attr_e( $forgot_password_button_text, 'woocommerce' ); ?>">
							</div>
						</div>
						<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
					</form>
					<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
                </div>
            </div>
        </div>
    </div>
</section>