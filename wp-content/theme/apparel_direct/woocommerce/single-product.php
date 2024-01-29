<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop'); ?>

<!--Header Banner Section -->
<?php
$product_banner = get_field('product_banner');
if ($product_banner) {
    $product_banner_url = $product_banner['url'];
} else {
    $product_banner_url = get_template_directory_uri() . '/assets/images/inner-banner.jpg';
}
?>
<?php /* ?>
<div class="inner-banner" style="background-image: url(<?php echo $product_banner_url; ?>);">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h2 class="text-center text-uppercase"><?php the_title(); ?></h2>
                <?php woocommerce_breadcrumb(); ?>
            </div>
        </div>
    </div>
</div>
<?php */ ?>
<!--Header Banner Section End -->

<div class="container">
    <?php
    /**
     * woocommerce_before_main_content hook.
     *
     * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
     * @hooked woocommerce_breadcrumb - 20
     */
    woocommerce_breadcrumb();
    do_action('woocommerce_before_main_content');
    ?>

    <?php while (have_posts()) : ?>
        <?php the_post(); ?>

        <?php wc_get_template_part('content', 'single-product'); ?>

    <?php endwhile; // end of the loop. 
    ?>

    <?php
    /**
     * woocommerce_after_main_content hook.
     *
     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
     */
    do_action('woocommerce_after_main_content');
    ?>
</div>

<?php
global $product;
$pa_size = $product->get_attribute('pa_size');
$pa_size_arr = explode(',', $pa_size);
$attribute_count = get_field('attribute_count', 'option');

$pa_fabric = $product->get_attribute('pa_fabric');
$pa_innerpackqty = $product->get_attribute('pa_innerpackqty');
$pa_inseam = $product->get_attribute('pa_inseam');
$pa_countryoforigin = $product->get_attribute('pa_countryoforigin');

if (count($pa_size_arr) <= $attribute_count && $product->is_type('variable')) {

    $short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);
?>
    <section class="variable_pro_description">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="woocommerce-product-details__short-description">
                        <h2>Description</h2>
                        <?php
                        if ($post->post_excerpt || $post->post_content) {
                            if ($short_description) {
                                echo $short_description;
                            } else {
                                the_content();
                            }
                        }
                        ?>
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
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<!-- Related Product -->
<?php

global $product;
$curr_pro_id = $product->get_id();

$term_obj_product_collections = get_the_terms($curr_pro_id, 'product_collection');
$term_obj_product_lines = get_the_terms($curr_pro_id, 'product_productline');

// Empty array
$ids_product_collection = array();
$ids_product_line = array();
$cats_array = array();

if(is_array($term_obj_product_collections)){
    foreach ($term_obj_product_collections as $term_obj_product_collection) {
        array_push($ids_product_collection, $term_obj_product_collection->term_id);
    }
}


foreach ($term_obj_product_lines as $term_obj_product_line) {
    array_push($ids_product_line, $term_obj_product_line->term_id);
}

// get categories
$terms = wp_get_post_terms($product->id, 'product_cat');

// select only the category which doesn't have any children
foreach ($terms as $term) {
    $children = get_term_children($term->term_id, 'product_cat');
    if (!sizeof($children))
        $cats_array[] = $term->term_id;
}

$ancestors_cat = get_ancestors($cats_array[0], 'product_cat', 'taxonomy');
/*
echo '<pre>';
print_r($ids_product_collection);
echo '</pre>';
echo '<pre>';
print_r($ids_product_line);
echo '</pre>';
echo '<pre>';
print_r($cats_array);
echo '</pre>';
echo "<pre>";
print_r($ancestors);
echo "</pre>";
echo $ancestors_cat[0];*/


$args = array(
    'post_type' => 'product',
    'ignore_sticky_posts' => 1,
    'no_found_rows' => 1,
    'posts_per_page' => 5,
    'fields' => 'ids',
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => array($cats_array[0], $ancestors_cat[0]),
        ),
        array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'product_collection',
                'field' => 'id',
                'terms' => $ids_product_collection
            ),
            array(
                'taxonomy' => 'product_productline',
                'field' => 'id',
                'terms' => $ids_product_line,
            )

        ),

    ),
    'meta_query' => array(
        array(
            'key' => '_stock_status',
            'value' => 'instock'
        ),
        array(
            'key' => '_backorders',
            'value' => 'no'
        ),
    ),
    'post__not_in' => array($curr_pro_id),
);

$products_rel  = new WP_Query($args);
$products_rel_2  = new WP_Query($args);
/*echo '<pre>';
print_r($products_rel->posts);
echo '</pre>';*/
$related_pro = $products_rel->posts;
//$related_pro = wc_get_related_products($post->ID, 5);
$related_pro_2 = $products_rel_2->posts;
//$related_pro_2 = wc_get_related_products($post->ID, 5);
$related_pro_count = count($related_pro);
$related_pro_last = array_pop($related_pro);

$front_page_id = get_option('page_on_front');
$woocommerce_thumbnail_place = wc_placeholder_img_src('woocommerce_thumbnail');
if ($related_pro_2) {

?>
    <section class="new-collection">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="welcome-text text-center">
                        <div class="main-title">
                            <h2><span>Related</span> Products</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row collection-row">
                <?php if ($related_pro_count < 5) {
                ?>
                    <div class="col-lg-12 col-md-12 d-flex flex-wrap">
                        <?php
                        if (!empty($related_pro_2) && is_array($related_pro)) {
                            foreach ($related_pro_2 as $related_product) {
								 $cust_featuredimg = get_field( "cdn_featured_image", $post->ID );
                                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($related_product), 'full');
                                $product = wc_get_product($related_product);
                                if (empty($featured_image)) {
                                    $featured_image['0'] = $woocommerce_thumbnail_place;
                                }

								if($cust_featuredimg){
                                    $imgurl = $cust_featuredimg;
                                } else if($featured_image) {
                                     $imgurl = $featured_image[0];
                                 } else {
                                    $imgurl = $woocommerce_thumbnail_place;
                                 }
                        ?>
                                <div class="col-lg-3 col-md-3">
                                    <div class="product-box">
                                        <a href="<?php echo $product->get_permalink(); ?>" title="" class="product-img">
                                            <img src="<?php echo $imgurl; ?>" alt="">
                                        </a>
                                        <div class="product-info">
                                            <div class="prdct-desc">
                                                <h4><?php echo $product->get_name(); ?></h4>
                                            </div>
                                            <div class="prdct-price-main">
                                                <?php
                                                if (!is_user_logged_in()) {
                                                    echo '<div class="price_hide_section"><a class="product_detail_display product_login_popup" href="javascript:void(0)">' . __('Login to see prices', 'theme_name') . '</a></div>';
                                                } else { ?>
                                                    <div class="prdct-price"><?php echo $product->get_price_html(); ?></div>
                                                <?php }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php

                            }
                        }
                        ?>
                    </div>
                <?php } else {
                ?>
                    <div class="col-lg-6 col-md-12 d-flex flex-wrap">
                        <?php
                        if (!empty($related_pro) && is_array($related_pro)) {
                            foreach ($related_pro as $related_product) {
                                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($related_product), 'product-thumb');
                                $product = wc_get_product($related_product);
                                if (empty($featured_image)) {
                                    $featured_image['0'] = $woocommerce_thumbnail_place;
                                }
                        ?>
                                <div class="col-lg-6 col-md-6">
                                    <div class="product-box">
                                        <a href="<?php echo $product->get_permalink(); ?>" title="" class="product-img">
                                            <img src="<?php echo $featured_image['0'] ?>" alt="">
                                        </a>
                                        <div class="product-info">
                                            <div class="prdct-desc">
                                                <h4><?php echo $product->get_name(); ?></h4>
                                            </div>
                                            <div class="prdct-price-main">
                                                <?php
                                                if (!is_user_logged_in()) {
                                                    echo '<div class="price_hide_section"><a class="product_detail_display product_login_popup" href="javascript:void(0)">' . __('Login to see prices', 'theme_name') . '</a></div>';
                                                } else { ?>
                                                    <div class="prdct-price"><?php echo $product->get_price_html(); ?></div>
                                                <?php }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php

                            }
                        }
                        ?>
                    </div>
                    <?php
                    $featured_image_last = wp_get_attachment_image_src(get_post_thumbnail_id($related_pro_last), 'full');
                    if (empty($featured_image_last)) {
                        $featured_image_last['0'] = get_template_directory_uri() . '/assets/images/woocommerce-placeholder-353-482.png';
                    }
                    $product_last = wc_get_product($related_pro_last);
                    if ($product_last) {

                    ?>
                        <div class="col-lg-6 col-md-12 product-box-right">
                            <div class="product-box d-flex align-items-center flex-wrap">
                                <a href="<?php echo $product_last->get_permalink(); ?>" title="" class="product-img">
                                    <img src="<?php echo $featured_image_last['0'] ?>" alt="">
                                </a>
                                <div class="product-info">
                                    <div class="prdct-desc">
                                        <h4><?php echo $product_last->get_name(); ?></h4>
                                        <span class="prodct-brand">Brand: <?php echo strip_tags(get_the_term_list($related_pro_last, 'product_brand', '', ', ')) ?></span>

                                    </div>
                                    <div class="prdct-price-main">
                                        <?php
                                        if (!is_user_logged_in()) {
                                            echo '<div class="price_hide_section"><a class="product_detail_display product_login_popup" href="javascript:void(0)">' . __('Login to see prices', 'theme_name') . '</a></div>';
                                        } else { ?>
                                            <div class="prdct-price"><?php echo $product->get_price_html(); ?></div>
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<!-- Related Product end -->

<!-- brands Start -->
<section class="brands-main">
    <div class="container">
        <div class="text-center">
            <h2><?php the_field('brands_title', $front_page_id); ?></h2>
        </div>
        <div class="row">
            <div class="brands-offer owl-carousel owl-theme">
                <?php
                $terms = get_terms(
                    array(
                        'taxonomy'   => 'product_brand',
                        'hide_empty' => true,
                        'exclude' => array(471)
                    )
                );

                if (!empty($terms) && is_array($terms)) {
                    foreach ($terms as $term) {
                        $brand_logo = get_field('brand_logo', 'product_brand_' . $term->term_id);
                        if ($brand_logo) {
                ?> <a href="<?php echo wc_get_page_permalink( 'shop' ).'?_brands='.$term->slug; ?>" title="<?php echo $term->name; ?>">
                                <div class="item">
                                    <div class="brands-img align-items-center d-flex justify-content-center">
                                        <img src="<?php echo $brand_logo['url']; ?>" alt="<?php echo $brand_logo['alt']; ?>">
                                    </div>
                                </div>
                            </a>
                <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
        <div class="browse-all-new row">
            <div class="container text-center">
                <div class="col-lg-12 col-md-12">
                    <a href="<?php echo get_permalink(446); ?>" title="Browse all brands" class="btn primary-btn">Browse All Brands</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- brands end -->

<?php
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
