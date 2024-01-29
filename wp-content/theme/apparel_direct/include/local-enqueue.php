<?php

add_action('init', 'local_enqueue_register', 1);
add_action('wp_enqueue_scripts', 'local_enqueue', 200);

function local_enqueue_register()
{

    wp_register_style('local-style', get_stylesheet_uri());
    wp_register_style('local-quick', get_template_directory_uri() . '/assets/css/quick_order/style-quick.css');

    wp_register_script('local', get_template_directory_uri() . '/assets/js/quick_order/local.js', array(
        'jquery',
    ),'4.0.0');
    wp_localize_script('local', 'local', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));

    wp_register_script('jquery-woo2', get_template_directory_uri() . '/assets/js/quick_order/woo2-variation.js', array('jquery'));
    wp_localize_script('jquery-woo2', 'woo2', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'carturl' => wc_get_cart_url(),
        'quickurl' => home_url('quick-order'),
    ));

    wp_register_script('local-select2', get_template_directory_uri() . '/3rdparty/select2/js/select2.min.js', array('jquery'));

    wp_register_style('local-awesomplete', get_template_directory_uri() . '/3rdparty/awesomplete/awesomplete.css');
    wp_register_script('local-awesomplete', get_template_directory_uri() . '/3rdparty/awesomplete/awesomplete.min.js', array('jquery'));

    wp_register_script('quick-v21', get_template_directory_uri() . '/assets/js/quick_order/quick-v21.js', array(
        'jquery',
        'jquery-woo2',
        'local-select2',
        'local-awesomplete'
    ));

    wp_localize_script('local', 'quick', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'checkout_url' => wc_get_checkout_url(),
        //'products' => woo2_quick_v2_all_json(),
    ));
}


function local_enqueue()
{
    wp_enqueue_style('local-quick');
    wp_enqueue_script('local');
    wp_enqueue_script('jquery-woo2');
}