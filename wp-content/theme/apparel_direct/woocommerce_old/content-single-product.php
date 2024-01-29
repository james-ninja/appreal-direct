<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
<?php //added extra div for stucture setup as per layout ?>
<div class="single_gallery_section">
	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );
	?>
</div>
	<div class="single_summary_section summary entry-summary">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );
		?>
	</div>
	<?php 

/*
$available_variations = $product->get_available_variations();

$all_color_list = array();
foreach($available_variations as $available_variation){
    $all_color_list[$available_variation['variation_id']] = $available_variation['attributes']['attribute_pa_color'];
}

$all_color_list = array_unique($all_color_list);
$count = 1;
echo '<div class="color_box"><ul class="list-unstyled list-inline">';
foreach($all_color_list as $key => $all_color_list_single){
    $variationobj = new WC_Product_Variation($key);

	$image_id = $variationobj->get_image_id();
	$image_array = wp_get_attachment_image_src($image_id, 'shop_thumbnail');

    $taxonomy = 'pa_color';
    $meta = get_post_meta($key, 'attribute_'.$taxonomy, true);
    $term = get_term_by('slug', $meta, $taxonomy);
    $image_tag = $variationobj->get_image('shop_thumbnail');
    echo '<li><a data-galleryselect="'.$image_array[0].'" data-colorname="'.$all_color_list_single.'" data-count="'.$count.'" data-colorid = "color_'.$all_color_list_single.'" class="action_color" href="javascript:void(0);">'.$image_tag.'</a><span class="color_title">'.$term->name.'</span></li>';
	$count++;
}
echo '</ul></div>';
*/
?>
	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
