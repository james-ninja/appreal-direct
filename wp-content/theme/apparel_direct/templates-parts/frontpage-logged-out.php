    <!-- home slider start -->
    <?php
    $slider_background_image = get_field('slider_background_image');
    ?>
    <section class="home-slider-main">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <?php /* ?>
            <ol class="carousel-indicators">
                <?php if (have_rows('slider_slide')) :
                    $count = 0;
                ?>
                    <?php while (have_rows('slider_slide')) : the_row();
                        $dot_active = '';
                        if ($count == 0) {
                            $dot_active = "active";
                        }
                    ?>
                        <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $count; ?>" class="<?php echo $dot_active; ?>"></li>
                    <?php
                        $count++;
                    endwhile; ?>
                <?php endif; ?>
            </ol>
            <?php */ ?>       
            <div class="carousel-inner">
                <?php if (have_rows('slider_slide')) : ?>

                    <?php while (have_rows('slider_slide')) : the_row();
                        $slide_content = get_sub_field('slide_content');
                        $slide_image = get_sub_field('slide_image');
                        $active_class = '';
                        if (get_row_index() == 1) {
                            $active_class = 'active';
                        }
                    ?>
                        <div class="carousel-item <?php echo $active_class; ?>">
                            <img class="d-block w-100" src="<?php echo $slide_image['url'] ?>" alt="<?php echo $slide_image['alt'] ?>">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="carousel-caption slider-content">
                                            <?php the_sub_field('slide_content'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <?php /* ?>          
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            <?php */ ?>
            
        </div>
    </section>
    <!-- home slider end -->
    <!-- brands Start -->
    <section class="brands-main">
        <div class="container">
            <div class="text-center">
                <h2><?php the_field('brands_title'); ?></h2>
            </div>
            <div class="row">
                <div class="brands-offer owl-carousel owl-theme">
                    <?php
                    $terms = get_terms(
                        array(
                            'taxonomy'   => 'product_brand',
                            'hide_empty' => true,
                            'exclude' => array(471)
                        )
                    );

                    // Check if any term exists
                    if (!empty($terms) && is_array($terms)) {
                        foreach ($terms as $term) {
                            $brand_logo = get_field('brand_logo', 'product_brand_' . $term->term_id);
                            if ($brand_logo) {
                    ?>
                                <div class="item">
                                    <div class="brands-img align-items-center d-flex justify-content-center">
                                        <img src="<?php echo $brand_logo['url']; ?>" alt="<?php echo $brand_logo['alt']; ?>">
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- brands end -->
    <!-- login order Start -->
    <?php
    $lb_image = get_field('lb_image');
    $lb_banner_text = get_field('lb_banner_text');
    $lb_login_button = get_field('lb_login_button');
    $lb_create_account_button = get_field('lb_create_account_button');
    ?>
    <section class="login-order">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3">
                    <?php if($lb_image){?>
                    <div class="login-img">
                        <img src="<?php echo $lb_image['url']; ?>" alt="<?php echo $lb_image['alt']; ?>">
                    </div>
                    <?php } ?>
                </div>
                <div class="col-lg-9 col-md-9">
                    <div class="login-order-info d-flex justify-content-end align-items-center">
                        <?php echo $lb_banner_text; ?>
                        <div class="login-order-btns">
                            <ul>
                                <?php if($lb_login_button){ ?>
                                <li><a href="<?php echo $lb_login_button['url']; ?>" title="Login" class="btn secondary-btn"><?php echo $lb_login_button['title']; ?></a>
                                </li>
                                <?php } ?>
                                <?php if($lb_create_account_button){ ?>
                                <li><a href="<?php echo $lb_create_account_button['url']; ?>" title="create account" class="btn secondary-btn"><?php echo $lb_create_account_button['title']; ?></a></li>
                                <?php } ?>   
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- login order end -->
    <!-- team Start -->
    <?php
    $team_background_image = get_field('team_background_image');
    $team_content = get_field('team_content');
    ?>
    <section class="team-main">
        <?php if($team_background_image){ ?>                           
            <div class="team-img" style="background-image: url(<?php echo $team_background_image['url'] ?>);"></div>
        <?php }  ?>  

        <?php if($team_content){ ?>    
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 col-md-12">
                    <div class="team-content">
                        <?php echo $team_content; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php }  ?>  
    </section>
    <!-- team end -->
    <!-- aboutus start -->
    <?php
    $about_us_image = get_field('about_us_image');
    $about_us_content = get_field('about_us_content');
    ?>
    <section class="about-main">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-4 about-content-main">
                    <div class="about-content">
                        <?php echo $about_us_content; ?>
                    </div>
                </div>
                <?php if($about_us_image){ ?>                    
                <div class="col-lg-8 col-md-8">
                    <div class="about-img">
                        <img src="<?php echo $about_us_image['url']; ?>" alt="<?php echo $about_us_image['alt']; ?>">
                    </div>
                </div>
                 <?php } ?>   
            </div>
        </div>
    </section>
    <!-- aboutus end -->
    <?php //get_template_part('templates-parts/newsletter-section'); ?>