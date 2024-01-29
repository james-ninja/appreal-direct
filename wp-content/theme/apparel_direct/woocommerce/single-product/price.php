<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

if(is_user_logged_in()) {
?>
<div class="price_section">
<div>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?></p>
<?php
    if($product->is_type('variable')) {
        $product_id = $product->get_id();
        $product = wc_get_product($product_id);
        $variations = $product->get_available_variations();
        $variations_id_arr = wp_list_pluck( $variations, 'variation_id' );

        $price_arr = [];
        foreach ($variations_id_arr as $variation_id) {
            $msrp = get_post_meta($variation_id, 'msrp_field', true);
            if($msrp && !in_array($msrp, $price_arr)) {
                $price_arr[] = $msrp;
            }
        }
    ?>
        <p class="retail_price_main"><span>Retail Price: </span> 
    <?php
        if(count($price_arr) > 0) {
            if(count($price_arr) == 1) {
                $price_display = get_woocommerce_currency_symbol().number_format((float)$price_arr[0], 2, '.', '');
            } else {
                $min_price = min($price_arr);
                $max_price = max($price_arr);
                $price_display = get_woocommerce_currency_symbol().number_format((float)$min_price, 2, '.', '').' - '.get_woocommerce_currency_symbol().number_format((float)$max_price, 2, '.', '');
            }
?>
        <?php echo $price_display; ?>
    <?php } ?>
        </p>
<?php } ?>
</div>

</div>
<?php } ?>