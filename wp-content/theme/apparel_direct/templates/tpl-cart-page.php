<?php 
/*
 * Template Name: Cart Page
*/

get_header();?>

<?php 
    $endpoint = WC()->query->get_current_endpoint();
    if($endpoint == 'lost-password') {

        if (have_posts()) : while (have_posts()) : the_post();
            the_content(); 
        endwhile; else: ?>
        <div class="error"><?php _e('Not found.'); ?></div>   
    <?php endif; ?>
<?php } else { ?>
    <div id="global_sec">
    <?php  get_template_part( 'templates-parts/header-banner-section' ); ?>
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php $header_banner_image = get_field('header_banner_image'); ?>
                        <?php if(!is_cart() && !is_checkout() && !$header_banner_image){ ?>
                    	<h2 class="title"><?php the_title(); ?></h2>  
                        <?php } ?>
                        
                        
                    	<?php the_content(); ?>  
                                  
                    <?php endwhile; else: ?>    
                            
                    	<div class="error"><?php _e('Not found.'); ?></div> 
                                   
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
            $saveforlater_count = get_user_meta(get_current_user_id(), 'mwb_woo_smc_logged_in_user_data', true);

            if (empty($saveforlater_count)) {
                $saveforlater_count = 0;
            } else {
                $saveforlater_count = count($saveforlater_count);
            }

            ?>
            <div class="sticky-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                        <div class="left-btn">
                            <a class="btn" href="<?php echo wc_get_page_permalink('shop'); ?>">Continue Shopping</a>
                            <a target="_blank" class="btn" href="<?php echo home_url('/saveforlater') ?>">Saved For Later (<?php echo $saveforlater_count; ?>)</a>
                        </div>
                        </div>
                        <div class="col-md-6 align-self-center text-right">
                            <span class="cart_total_custom">
                                <?php echo '<label>Subtotal</label>';?> <?php wc_cart_totals_subtotal_html(); ?>
                            </span>
                            <a href="<?php echo wc_get_checkout_url(); ?>" class="btn primary-btn ad_checkout_btn_custom">Checkout</a>
                            <a href="javascript:void(0)" class="btn ad_updatecart_btn_custom primary-btn">Update cart</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div><!-- EOF : content ID -->
<?php } ?>

<?php get_footer(); ?>

<script>
// Check cart status when the cart is updated
$('body').on('updated_cart_totals', function() {
var carttotal = $('.carttotal').val();
$('.cart_total_custom .woocommerce-Price-amount').text(carttotal);
});
</script>