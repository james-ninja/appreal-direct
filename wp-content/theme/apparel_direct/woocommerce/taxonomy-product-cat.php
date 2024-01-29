<?php

/**
 * The Template for displaying products in a product category. Simply includes the archive template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/taxonomy-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     4.7.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
// if(!is_user_logged_in()) {
//     wp_redirect(site_url());
//     exit;
// }

//get current category id
$cate = get_queried_object();
$cateID = $cate->term_id;

$post_count = ad_get_cat_count($cateID);

if($post_count == 0){
    wp_redirect(site_url());
    exit;
}

get_header('shop');
//wc_get_template( 'archive-product.php' );
?>
<?php
global $current_user;


$page_layout = get_field('page_layout', 'product_cat_'.$cateID);

if($page_layout == 'main-category-page'){
    get_template_part( 'woocommerce/tpl-main-category' );
}else{
    wc_get_template( 'archive-product.php' );
}

?>

<?php get_footer('shop');
?>