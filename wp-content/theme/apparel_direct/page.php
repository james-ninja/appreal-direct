<?php get_header();?>

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
    </div><!-- EOF : content ID -->
<?php } ?>

<?php get_footer(); ?>