<?php
/*
 * Template Name: FAQs Page
*/
get_header(); ?>

<?php
$endpoint = WC()->query->get_current_endpoint();
if ($endpoint == 'lost-password') {

    if (have_posts()) : while (have_posts()) : the_post();
            the_content();
        endwhile;
    else : ?>
        <div class="error"><?php _e('Not found.'); ?></div>
    <?php endif; ?>
<?php } else { ?>
    <div id="global_sec">
        <?php get_template_part('templates-parts/header-banner-section'); ?>

        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                            <?php $header_banner_image = get_field('header_banner_image'); ?>
                            <?php if (!is_cart() && !is_checkout() && !$header_banner_image) { ?>
                                <h2 class="title"><?php the_title(); ?></h2>
                            <?php } ?>


                            <?php the_content(); ?>
                            <?php if( have_rows('faqs') ): ?>
                                <ul class="faqs">
                                <?php while( have_rows('faqs') ): the_row(); 
                                    $questions = get_sub_field('questions');
                                    $answer = get_sub_field('answer');
                                    ?>
                                        <div class="step_form panel panel-default">
                                            <div class="panel-heading panel-heading-full">
                                                <h3 class="panel-title" data-toggle="collapse" data-target="#collapse_<?php echo get_row_index(); ?>"><?php echo $questions; ?></h3>
                                            </div>
                                            <div id="collapse_<?php echo get_row_index(); ?>" class="panel-collapse collapse">
                                                <div class="panel-body"><?php echo $answer; ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php endwhile; ?>
                                </ul>
                            <?php endif; ?>

                        <?php endwhile;
                    else : ?>

                        <div class="error"><?php _e('Not found.'); ?></div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!-- EOF : content ID -->
<?php } ?>

<?php get_footer(); ?>