<?php

if (!is_user_logged_in()) {
    $redirect = site_url() . '/login';
    wp_redirect($redirect);
    exit;
}
get_header();

wp_enqueue_style('local-awesomplete');
wp_enqueue_style('local-quick');
wp_enqueue_script('quick-v21');

the_post();

?>


<div id="global_sec">
    <?php get_template_part('templates-parts/header-banner-section'); ?>

    <div class="container">
        <div class="woocommerce-notices-wrapper"></div>

        <form id="quick-v2" method="POST" class="quick_order_form_ad quick-v2 page-order" data-action="preview" action="">

            <?php get_template_part('blocks/quick/order', 'orders') ?>

        </form>

        <?php get_template_part('blocks/quick/row') ?>

    </div>
</div>    
    <script>
        //Add to cart
        var $quick_order_form_ad = jQuery('form.quick_order_form_ad');
        $quick_order_form_ad.on('click', '.add_to_cart_quick', function(event) {
            event.preventDefault();
            var data = {
                'action': 'quick_woocommerce_ajax_add_to_cart',
                'data_form': jQuery($quick_order_form_ad).serialize(),
            };
            jQuery.ajax({
                url: custom.ajaxurl,
                data: data,
                type: 'POST',
                beforeSend: function(xhr) {
                    //jQuery('body').addClass('ajax2');
                    jQuery('.table.master').addClass('blockUI blockOverlay');
                },
                success: function(response) {
                    //jQuery('body').removeClass('ajax2');
                    jQuery('.table.master').removeClass('blockUI blockOverlay');
                    formSubmitting = true;
                    if(response['success'] == true){
                        
                        jQuery('.master .remove a.remove').trigger('click');
                        jQuery('button.add-item').trigger('click');
                        jQuery('button.add-item').trigger('click');
                        jQuery('button.add-item').trigger('click');
                        jQuery('button.add-item').trigger('click');
                        jQuery('button.add-item').trigger('click');

                        carturl = "/cart"
                        responsehtml = '<div class="woocommerce-message" role="alert">'+response['totalqty']+' products have been added to your cart. <a href="'+carturl+'">View Cart</a></div>';
                        $(".woocommerce-notices-wrapper").html(responsehtml);
                        jQuery('.add_to_cart_quick').prop('disabled', true);

                    }else{
                        responsehtml = '<div class="woocommerce-error" role="alert">Looks like there was an error. Please try again.</div>';
                        $(".woocommerce-notices-wrapper").html(responsehtml);
                    }
                    
                    $(document.body).trigger('wc_fragment_refresh');

                    setTimeout(function() {
                            jQuery('.woocommerce-message').fadeOut('slow');
                    }, 10000);
                    
                }
            });

        });

    </script>

<?php
get_footer();
?>