<?php
/*

 * Template Name: Login

*/

if(is_user_logged_in()) {
    wp_redirect(site_url());
    exit;
}

get_header();
?>
    <?php
        if ( have_posts() ) : 
        while ( have_posts() ) : the_post();
            $login_main_title = get_field('login_main_title');
            if($login_main_title == '') {
                $login_main_title = get_the_title();
            }
            $login_banner = get_field('login_banner');
            $login_logo = get_field('login_logo');
            $login_form_shortcode = get_field('login_form_shortcode');
    ?>
        <section class="login_main">
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
    						<h1><?php echo $login_main_title; ?></h1>
                            <?php
                                if($login_form_shortcode) {
                                    echo do_shortcode($login_form_shortcode);
                                }
                            ?>
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endwhile; endif; ?>

<?php
get_footer();