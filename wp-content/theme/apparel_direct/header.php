<?php

/**
 * The header for our theme
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="theme-color" content="#EF4344" />
    <link rel="manifest" href="/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php
    $endpoint = WC()->query->get_current_endpoint();
    if (!is_page_template('templates/tpl-login.php') && $endpoint != 'lost-password') {
    ?>
        <!-- header start -->
        <header>
            <div class="top-header">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-9 col-md-9">
                            <?php if (function_exists('the_custom_logo')) {
                                the_custom_logo();
                            } ?>

                            <?php if (is_user_logged_in()) { ?>
                                <div class="search-header">
                                    <div class="click-search d-block d-md-none">
                                        <a href="#" title=""><img src="<?php echo get_template_directory_uri(); ?>/assets/images/search-icon.svg" alt=""></a>
                                    </div>
                                    <form action="<?php echo get_site_url(); ?>" class="search-form">
                                        <input placeholder="Search For Products" data-swplive="true" class="form-control" name="s" type="search">
                                        <input type="submit" value="">
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-lg-3 col-md-3 d-flex align-items-center justify-content-end ">
                            <div class="header-links">
                                <?php
                                $items_count = WC()->cart->get_cart_contents_count();
                                if (is_user_logged_in()) {
                                    $current_user = wp_get_current_user();
                                ?>
                                    <ul>
                                        <li>
                                            <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" title="<?php echo $current_user->user_firstname; ?>" class="user-icon">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/user.svg" alt="My Account">
                                                <span class="header_user_fname"><?php echo $current_user->user_firstname; ?></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo wc_get_account_endpoint_url('my-lists'); ?>" title="Wishlist" class="whishlist-icon">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/whishlist.svg" alt="Wishlist">
                                                <span class="cart-num yith-wcwl-items-count"><?php echo esc_html(yith_wcwl_count_all_products()); ?></span>

                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo wc_get_cart_url(); ?>" title="<?php _e('View your shopping cart'); ?>" class="cart-icon">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.svg" alt="Cart">
                                                <span id="mini-cart-count" class="cart-num"><?php echo $items_count ? $items_count : '0'; ?></span>
                                            </a>
                                        </li>

                                    </ul>
                                <?php } else { ?>

                                    <ul>
                                        <li>
                                            <a href="<?php echo get_permalink(get_page_by_path('login')); ?>" title="My Account" class="user-icon">
                                                <img height="19" width="19" src="<?php echo get_template_directory_uri(); ?>/assets/images/user.svg" alt="My Account">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo get_permalink(get_page_by_path('login')); ?>" title="Wishlist" class="whishlist-icon">
                                                <img height="19" width="22" src="<?php echo get_template_directory_uri(); ?>/assets/images/whishlist.svg" alt="Wishlist">
                                                <span class="cart-num">0</span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="<?php echo get_permalink(get_page_by_path('login')); ?>" title="Cart" class="cart-icon">
                                                <img height="21" width="21" src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.svg" alt="Cart">
                                                <span class="cart-num">0</span>
                                            </a>
                                        </li>

                                    </ul>
                                <?php }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $log_in_button = get_field('log_in_button', 'option');
            $quick_order_button = get_field('quick_order_button', 'option');
            ?>
            <nav>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 header_menu_section">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'header-menu'
                            ));
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
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <!-- header end -->
    <?php } ?>