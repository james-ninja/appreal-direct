<?php get_header();?>

<div id="global_sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
               
            	<?php if (have_posts()) : ?>            
                	
                	<?php while (have_posts()) : the_post(); ?>
                        
                        <div class="post-box">
                
                			<h2 class="title"><?php the_title(); ?></h2>
                            
                            <p class="byline">Written by: <span><?php the_author_link(); ?></span> on <span><?php the_time(get_option('date_format')); ?></span></p>
                              
                			<?php the_content(); ?>  
                            
                            <div class="post-meta">Posted in a<?php the_category(',') ?> | <?php comments_popup_link(__('0 Comments'), __('1 Comment'), __('% Comments')); ?></div>
                        </div>
                        
                        <div class="post-box">
                        	
    						<?php comments_template();?>
                            
                        </div>            		
                              
                	<?php endwhile; ?>                 
                     
                <?php else: ?>    
                        
    				<div class="error"><?php _e('Not found.'); ?></div> 
                               
    			<?php endif; ?>
            </div>
        </div>
    </div>
</div><!-- EOF : content ID -->

<?php get_footer(); ?>