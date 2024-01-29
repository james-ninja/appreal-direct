<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
    $login_page_link = get_field('login_page_link', 'option');

    $login_banner = get_field('login_banner', $login_page_link);
    $login_logo = get_field('login_logo', $login_page_link);

    $forgot_password_confirmation_title = get_field('forgot_password_confirmation_title', 'option');
    $forgot_password_confirmation_message = get_field('forgot_password_confirmation_message', 'option');
    $forgot_password_confirmation_description = get_field('forgot_password_confirmation_description', 'option');
?>
<section class="login_main forgot_password_confirm_main">
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
                    <h1><?php echo $forgot_password_confirmation_title; ?></h1>
                    <?php wc_print_notice( esc_html__( $forgot_password_confirmation_message, 'woocommerce' ) ); ?>
                    <?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>
                    <?php if($forgot_password_confirmation_description) { ?>
                        <p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( $forgot_password_confirmation_description, 'woocommerce' ) ) ); ?></p>
                    <?php } ?>
                    <?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
                </div>
            </div>
        </div>
    </div>
</section>