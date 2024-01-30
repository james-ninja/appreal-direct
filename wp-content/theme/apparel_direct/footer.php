<?php

/**
 * The template for displaying the footer
 */

?>

<?php
$endpoint = WC()->query->get_current_endpoint();
if (!is_page_template('templates/tpl-login.php') && $endpoint != 'lost-password') {
?>
    <?php 
    if(!is_cart()){
    get_template_part('templates-parts/newsletter-section');
    }
    ?>
    <!-- footer start-->
    <footer>
        <div class="container">
            <?php if (is_user_logged_in()) { ?>
            <div class="row">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <!-- <div class="col-lg-12 col-md-12 col-sm-12 text-center footer-info"> -->
                    <div class="col-lg-4 col-md-4 col-sm-12 footer-info">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                <?php endif; ?>

                <?php if (is_active_sidebar('footer-2')) : ?>
                    <div class="col-lg-4 col-md-4 col-sm-12 footer-links">
                        <?php dynamic_sidebar('footer-2'); ?>
                    </div>
                <?php endif; ?>
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <div class="col-lg-4 col-md-4 col-sm-12 footer-links">
                        <?php dynamic_sidebar('footer-3'); ?>
                    </div>
                <?php endif; ?>
                <?php /* ?>  
                <?php if (is_active_sidebar('footer-4')) : ?>
                    <div class="col-lg-2 col-md-2 col-sm-12 footer-links">
                        <?php dynamic_sidebar('footer-4'); ?>
                    </div>
                <?php endif; ?>
                <?php */ ?>
            </div>
            <?php } else {
                 ?>
                <div class="row">
                <?php if (is_active_sidebar('footer-1')) : ?>
                  <div class="col-lg-12 col-md-12 col-sm-12 text-center footer-info">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                <?php endif; ?>
            </div>
           <?php } ?>
        </div>
        <div class="copyrght">
            <div class="container">
                <div class="row">
                    <?php if (is_active_sidebar('copyright-bottom')) : ?>
                        <div class="col-lg-12 col-md-12">
                            <?php dynamic_sidebar('copyright-bottom'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    <a id="button"></a>
    <!-- footer end-->
<?php } ?>

<!--menu popup -->
<div id="modal_menu" style="display: none;" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
            <div class="modal-body text-center">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-popup.png" alt="logo-popup">
                <h6>In order to browse products, you must be logged in.</h6>
                <a href="<?php echo get_site_url() ?>/login" title="LOGIN" class="btn primary-btn popup-login-btn">LOGIN</a>
                <a href="<?php echo get_site_url() ?>/my-account" title="APPLY NOW" class="btn primary-btn">APPLY NOW</a>
            </div>
        </div>
    </div>
</div>

<?php if (!is_user_logged_in()) {
    // if (is_shop() || is_product()) {
?>
        <div id="modal_login_form" style="display: none;" class="modal fade" role="dialog">
            <div class="modal-dialog login_main">
                <!-- Modal content-->
                <div class="modal-content login-form">
                    <button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
                    <div class="modal-body">
                        <?php echo do_shortcode('[login_form_apparel]'); ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    // }
} ?>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<script src="https://kit.fontawesome.com/08b23c7ec6.js"></script>
<?php
if (!is_user_logged_in()) { ?>
    <!-- <script>
        jQuery(document).ready(function() {
            // jQuery("nav a:not(.home_menu), .already-account a, .login-order-btns a, .header-links a, .about-us-link a, .search-results .post-box a").attr("href", "http://205.134.254.135/~teamh/apparel_direct/my-account/");
            jQuery("#ubermenu-main-444-header-menu-2 a:not(.home_menu), .header-links a, .about-us-link a, .search-results .post-box a").addClass('reg_popup');
            jQuery("#ubermenu-main-444-header-menu-2 a:not(.home_menu), .header-links a, .about-us-link a, .search-results .post-box a").attr("href", "#");

            jQuery(".reg_popup, .reg_popup span").on('click touchstart', function () {   
                jQuery('#modal_menu').modal('show');
            });
            setTimeout(function(){
                jQuery("#menu-item-11707 a.reg_popup").on('click touchstart', function () {   
                jQuery('#modal_menu').modal('show');
            });
             }, 1000);
        });
    </script> -->
<?php } ?>
<?php 
if(is_page('faqs')){ ?>
<style>
html {
  scroll-behavior: smooth;
}
</style>
<?php }
?>
<script>if ('serviceWorker' in navigator) {
  console.log('CLIENT: service worker registration in progress.');
  navigator.serviceWorker.register('/sw.js').then(function() {
    console.log('CLIENT: service worker registration complete.');
  }, function() {
    console.log('CLIENT: service worker registration failure.');
  });
} else {
  console.log('CLIENT: service worker is not supported.');
}</script>
<?php wp_footer(); ?>

<script src="//code.tidio.co/ffxpnegcaybmsbdqqqruwvpyjsuswihz.js"></script>
</body>

</html>