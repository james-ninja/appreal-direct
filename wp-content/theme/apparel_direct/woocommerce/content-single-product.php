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

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>
	<?php //added extra div for stucture setup as per layout 
	?>
	<div class="single_gallery_section">
		<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action('woocommerce_before_single_product_summary');
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
		do_action('woocommerce_single_product_summary');
		?>
	</div>
	<?php
	if (is_plugin_active('back-in-stock-notifier-for-woocommerce/cwginstocknotifier.php')) {
		$security = wp_create_nonce('codewoogeek-product_id-' . get_the_ID());
	?>

<div id="modal_notifyme" style="display: none;" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
            <div class="modal-body text-center">
			<section class="hpyesy cwginstock-subscribe-form">
			<div class="panel panel-primary cwginstock-panel-primary">
				<div class="panel-heading cwginstock-panel-heading">
					<h4 style="text-align: center;">
						Email when stock available </h4>
				</div>
				<div class="panel-body cwginstock-panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12">
								<div class="form-group center-block">
									<input type="email" style="width:100%; text-align:center;" class="cwgstock_email form-control" name="cwgstock_email" placeholder="Your Email Address" value="6621171@gmail.com">
								</div>
								<input type="hidden" class="cwg-phone-number" name="cwg-phone-number" value="">
								<input type="hidden" class="cwg-phone-number-meta" name="cwg-phone-number-meta" value="">
								<input type="hidden" class="cwg-product-id" name="cwg-product-id" value="<?php echo get_the_ID(); ?>">
								<input type="hidden" class="cwg-variation-id" name="cwg-variation-id" value="">
								<input type="hidden" class="cwg-security" name="cwg-security" value="<?php esc_html_e($security); ?>"/>
								<div class="form-group center-block" style="text-align:center;">
									<input type="submit" name="cwgstock_submit" class="cwgstock_button " value="Notify me when available">
								</div>

								<div class="cwgstock_output"></div>
							</div>
						</div>
					</div>

					<!-- End ROW -->

				</div>
			</div>
		</section>
            </div>
        </div>
    </div>
</div>



	<?php
		//echo do_shortcode('[cwginstock_subscribe_form product_id="'.get_the_ID().'" variation_id="84223" ]');
	}
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
	do_action('woocommerce_after_single_product_summary');
	?>
</div>

<?php do_action('woocommerce_after_single_product'); ?>