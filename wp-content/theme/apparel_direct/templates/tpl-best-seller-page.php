<?php
/*
 * Template Name: Best Seller Page
*/
get_header(); ?>

<?php get_template_part('templates-parts/header-banner-section'); ?>
<div id="global_sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 woocommerce">
                <?php if (have_posts()) { 
                    //echo 'no posts';
                     while (have_posts()) : the_post(); ?>
                        <?php the_content(); ?>
                    <?php endwhile;
                } else { ?>
                    <div class="error"><?php _e('Not found.'); ?></div>
                    
                <?php }  ?>

                <?php
                $best_seller_products = get_field('best_seller_products');
                
                if (count($best_seller_products) > 0) : ?>
                    <?php woocommerce_product_loop_start(); ?>
                    <?php foreach ($best_seller_products as $post) :
                        setup_postdata($post); ?>
                        <?php wc_get_template_part('content', 'product'); ?>
                    <?php endforeach; ?>
                    <?php woocommerce_product_loop_end(); ?>
                    <?php
                    wp_reset_postdata(); ?>
                <?php  
            else : 
             ?>
                <div class="error">There are currently no products in this collection
                <p class="return-to-home">
                    <a class="button wc-backward" href="https://appareldirectdistributor.com">
                         Return to home     </a>
                </p> </div>
              <?php  endif;  ?>
            </div>
        </div>
    </div>
</div><!-- EOF : content ID -->

<?php get_footer(); ?>