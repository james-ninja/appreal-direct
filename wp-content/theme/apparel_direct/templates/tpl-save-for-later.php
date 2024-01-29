<?php
/*
 * Template Name: Save For Later Page
*/

get_header(); ?>

<div id="global_sec">
    <?php get_template_part('templates-parts/header-banner-section'); ?>
    <div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <?php $header_banner_image = get_field('header_banner_image'); ?>
                        <?php if (!is_cart() && !is_checkout() && !$header_banner_image) { ?>
                            <h2 class="title"><?php the_title(); ?></h2>
                        <?php } ?>


                        <?php the_content(); ?>

                    <?php endwhile;
                else : ?>

                    <div class="error"><?php _e('Not found.'); ?></div>

                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    global $current_user;
    $user_id = $current_user->ID;
    $mwb_woo_smc_get_save_later_product = get_user_meta($user_id, 'mwb_woo_smc_logged_in_user_data', true);
    if (!empty($mwb_woo_smc_get_save_later_product)) {
    ?>
        <div class="sticky-bottom">
            <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                        <div class="left-btn">
                            <a target="_blank" class="btn" href="<?php echo wc_get_page_permalink('shop'); ?>">Continue Shopping</a>
                        </div>
                        </div>
                        <div class="col-md-6 align-self-center text-right">
                           <div class="cart_total">
                <label>Subtotal</label>
                <span class="save_for_later_total"></span>
            </div>
            <div class="mwc_mwb_class">
                <input type="button" name="mwb_smc_add_all_product_to_cart" class="btn primary-btn mwb_smc_add_all_product_to_cart button alt" id="mwb_smc_add_all_product_to_cart" value="<?php esc_attr_e('Move All To Cart', 'save-cart-later'); ?>">
                        </div>
                    </div>
                </div>
            
            
            </div>
        </div>
    <?php } ?>
</div>
    <?php //share_list_get_pdf(); ?>
</div><!-- EOF : content ID -->

<?php get_footer(); ?>