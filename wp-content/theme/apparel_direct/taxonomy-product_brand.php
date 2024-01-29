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
get_header('shop');
//wc_get_template( 'archive-product.php' );
?>
<?php 
$brand = get_queried_object();
$brand_id = $brand->term_id;
$brand_all_categories = get_field('select_product_category', 'product_brand_' . $brand_id);

$header_banner_image = get_field('header_banner_image', 'product_brand_' . $brand_id);

if(empty($header_banner_image)){
    $header_banner_image['url'] = get_site_url().'/wp-content/uploads/2021/07/inner-banner.jpg';
}

$args_brand_product = array(
    'post_type'     => 'product',
    'posts_per_page' => -1,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_brand',
            'field' => 'id',
            'terms' =>$brand_id,
        )
    ),
);
$brand_product_query = new WP_Query( $args_brand_product );

$cat_ids = array();
if ($brand_product_query->have_posts()) {
while ($brand_product_query->have_posts()) : $brand_product_query->the_post();
global $brand_product_query;

$terms = get_the_terms ( get_the_ID(), 'product_cat' );
foreach ( $terms as $term ) {
    $cat_ids[] = $term->term_id;
}
endwhile;
wp_reset_query();
}

$cat_ids = array_unique($cat_ids);

$brand_all_categories = $cat_ids;
?>
<!--Header Banner Section -->
<div class="inner-banner" style="background-image: url(<?php echo $header_banner_image['url'] ?>);">
	<?php if ($brand->name) { ?>
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12">
					<h2 class="text-center text-uppercase"><?php echo $brand->name; ?></h2>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<!--Header Banner Section End -->
<!-- category Section -->
<div id="global_sec">
    <div class="container">
        <div class="row">
            <?php
            if (!empty($brand_all_categories) && is_array($brand_all_categories)) {
                foreach ($brand_all_categories as $brand_all_category) {
                        $featured_category_img = get_field('category_image', 'product_cat_' . $brand_all_category);
                        $thumbnail_id = get_term_meta($brand_all_category, 'thumbnail_id', true);
                        $cat_image = wp_get_attachment_url($thumbnail_id);
                        $term_obj = get_term($brand_all_category, 'product_cat');
                    ?>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <a href="<?php echo get_term_link($brand_all_category, 'product_cat').'?_brands='.$brand->slug; ?>" title="<?php echo $term_obj->name; ?>" class="category-box text-center">
                                <div class="cat-img">
                                    <?php if(empty($cat_image)){
                                        $cat_image = get_site_url().'/wp-content/uploads/woocommerce-placeholder-150x150.png';
                                    } ?>
                                    <img src="<?php echo $cat_image; ?>" alt="<?php echo $term_obj->name; ?>">
                                </div>
                                <div class="cat-name"><?php echo strtoupper($term_obj->name); ?></div>
                            </a>
                        </div>
            <?php
                    
                }
            }
            ?>

        </div>
        <div class="browse-all-new row">
            <div class="container text-center">
                <div class="col-lg-12 col-md-12">
                    <a href="<?php echo wc_get_page_permalink( 'shop' ).'?_brands='.$brand->slug; ?>" title="Browse All Products" class="btn primary-btn">Browse All Products</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- category End -->


<?php get_footer('shop');
?>