<?php

/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $post;

global $product;
$pa_size = $product->get_attribute('pa_size');
$pa_size_arr = explode(',', $pa_size);
$attribute_count = get_field('attribute_count', 'option');

$pa_fabric = $product->get_attribute('pa_fabric');
$pa_innerpackqty = $product->get_attribute('pa_innerpackqty');
$pa_inseam = $product->get_attribute('pa_inseam');
$pa_countryoforigin = $product->get_attribute('pa_countryoforigin');


if (count($pa_size_arr) > $attribute_count && $product->is_type('variable')) {

    $short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);
?>
    <div class="woocommerce-product-details__short-description">
        <h2>Description</h2>
        <?php if ($post->post_excerpt || $post->post_content) { ?>
            <?php
            if ($short_description) {
                echo $short_description;
            } else {
                the_content();
            }
            ?>
        <?php } ?>
        <ul class="product_attribute">
            <?php if ($pa_fabric) { ?>
                <li><span>Fabric : </span><span><?php echo $pa_fabric; ?></span></li>
            <?php } ?>

            <?php if ($pa_innerpackqty) { ?>
                <li><span>Inner pack Quantity : </span><span><?php echo $pa_innerpackqty; ?></span></li>
            <?php } ?>

            <?php if ($pa_inseam) { ?>
                <li><span>Inseam : </span><span><?php echo $pa_inseam; ?></span></li>
            <?php } ?>

            <?php if ($pa_countryoforigin) { ?>
                <li><span>Country of origin : </span><span><?php echo $pa_countryoforigin; ?></span></li>
            <?php } ?>
        </ul>
    </div>
<?php } else if (!$product->is_type('variable')) { ?>
    <?php
    $short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);
    ?>
    <div class="woocommerce-product-details__short-description">
        <h2>Description</h2>
        <?php if ($post->post_excerpt || $post->post_content) { ?>
            <?php
            if ($short_description) {
                echo $short_description;
            } else {
                the_content();
            }
            ?>
        <?php } ?>
        <ul class="product_attribute">
            <?php if ($pa_fabric) { ?>
                <li><span>Fabric : </span><span><?php echo $pa_fabric; ?></span></li>
            <?php } ?>

            <?php if ($pa_innerpackqty) { ?>
                <li><span>Inner pack Quantity : </span><span><?php echo $pa_innerpackqty; ?></span></li>
            <?php } ?>

            <?php if ($pa_inseam) { ?>
                <li><span>Inseam : </span><span><?php echo $pa_inseam; ?></span></li>
            <?php } ?>

            <?php if ($pa_countryoforigin) { ?>
                <li><span>Country of origin : </span><span><?php echo $pa_countryoforigin; ?></span></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<div class="wishlist_seeprice">
    <?php
    //echo do_shortcode('[yith_wcwl_add_to_wishlist]');
    if (!is_user_logged_in()) {
        //echo '<a class="product_detail_display" href="' . get_permalink(get_page_by_path('login')) . '">' . __('Login to see prices', 'theme_name') . '</a>';
        echo '<a class="product_detail_display product_login_popup" href="javascript:void(0)">' . __('Login to see prices', 'theme_name') . '</a>';
    }
    ?>
</div>

<?php if (count($pa_size_arr) > $attribute_count) { ?>
    </div>
<?php } ?>