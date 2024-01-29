<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
    $login_page_link = get_field('login_page_link', 'option');

    $login_banner = get_field('login_banner', $login_page_link);
    $login_logo = get_field('login_logo', $login_page_link);

    $reset_password_title = get_field('reset_password_title', 'option');
    $reset_password_description = get_field('reset_password_description', 'option');
    $reset_password_label = get_field('reset_password_label', 'option');
    $reset_re_enter_password_label = get_field('reset_re_enter_password_label', 'option');
    $reset_password_button = get_field('reset_password_button', 'option');
?>
<section class="login_main reset_password_main">
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
                    <h1><?php echo $reset_password_title; ?></h1>
                    <?php do_action( 'woocommerce_before_reset_password_form' ); ?>
                    <form method="post" class="woocommerce-ResetPassword lost_reset_password">
                    	<?php if($reset_password_description) { ?>
							<p><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( $reset_password_description, 'woocommerce' ) ); ?></p>
						<?php } ?>
						<div class="form-group">
							<label for="password_1"><?php esc_html_e( $reset_password_label, 'woocommerce' ); ?></label><span class="star">*</span>
							<input type="password" class="form-control" name="password_1" id="password_1" autocomplete="new-password" />
						</p>
						<div class="form-group">
							<label for="password_2"><?php esc_html_e( $reset_re_enter_password_label, 'woocommerce' ); ?></label><span class="star">*</span>
							<input type="password" class="form-control" name="password_2" id="password_2" autocomplete="new-password" />
						</div>

						<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
						<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

						<div class="clear"></div>
						<?php do_action( 'woocommerce_resetpassword_form' ); ?>
						<div class="form-group">
							<div class="login_btn">
								<input type="hidden" name="wc_reset_password" value="true" />
								<button type="submit" name="reset_password" id="reset_password" class="btn-primary btn" value="<?php esc_attr_e( $reset_password_button, 'woocommerce' ); ?>"><?php esc_attr_e( $reset_password_button, 'woocommerce' ); ?></button>
							</div>
						</div>
						<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
					</form>
					<?php do_action( 'woocommerce_after_reset_password_form' ); ?>
                </div>
            </div>
        </div>
    </div>
</section>