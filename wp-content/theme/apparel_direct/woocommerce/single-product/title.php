<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce\Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

the_title( '<h1 class="product_title entry-title">', '</h1>' );
?>

<div class="pro_sku_main">
   <?php
      global $product;
      $product_style = get_post_meta( get_the_id(), 'product_style', true );
      //if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : 
      if ( wc_product_sku_enabled() && ( $product_style || $product->is_type( 'variable' ) ) ) : 
      ?>
      <span class="sku_wrapper"><?php esc_html_e( 'Style # ', 'woocommerce' ); ?><span class="sku"><?php echo ( $sku = $product_style ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
   <?php endif; ?>
   <div>
   <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
   </div>
</div>