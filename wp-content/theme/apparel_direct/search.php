<?php get_header();?>

<div id="global_sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 woocommerce">
                <h2 style="padding-bottom:15px;font-size:26px;">Search Results for '<i><?php echo $_GET['s'];?></i>'</h2>
                   
            	<?php if (have_posts()) : ?>   
                    <?php woocommerce_product_loop_start(); ?>         
                	
                	<?php while (have_posts()) : the_post(); ?>
                        <?php wc_get_template_part( 'content', 'product' ); ?>
                	<?php endwhile; ?>  
                    <?php woocommerce_product_loop_end(); ?> 

                    <?php // echo do_shortcode('[facetwp facet="pagination"]'); ?>
                    	<div class="pager">
    					<?php  
                        global $wp_query;
                    
                            $big = 999999999; // need an unlikely integer
                            
                            echo paginate_links( array(
                                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                'format' => '?paged=%#%',
                                'current' => max( 1, get_query_var('paged') ),
                                'total' => $wp_query->max_num_pages
                            ) );
                             wp_reset_query();
                        ?>
                        </div>               
                     	<div class="clear"></div>
                        
                <?php else: ?>    
                        
    				<div class="error"><?php _e('Not found.'); ?></div> 
                               
    			<?php endif; ?>
            </div> 
        </div>
    </div>
</div><!-- EOF : content ID -->

<?php get_footer(); ?>