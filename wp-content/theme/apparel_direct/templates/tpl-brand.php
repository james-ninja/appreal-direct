<?php
/*
 * Template Name: Brand Listing Page
*/
// if (!is_user_logged_in()) {
//     wp_redirect(site_url());
//     exit;
// }
get_header();

?>
<?php
$header_banner_image = get_field('header_banner_image');

$featured_brands_title = get_field('featured_brands_title');
$featured_brands_description = get_field('featured_brands_description');
$all_brands_title = get_field('all_brands_title');


?>
<!--Header Banner Section -->
<div class="inner-banner" style="background-image: url(<?php echo $header_banner_image['url'] ?>);">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h2 class="text-center text-uppercase"><?php echo get_the_title(); ?></h2>
            </div>
        </div>
    </div>
</div>
<!--Header Banner Section End -->

<!-- Featured Brands Section -->
<?php
$featured_terms = get_terms(
    array(
        'taxonomy'   => 'product_brand',
        'hide_empty' => true,
        'number' => 3,
        'meta_query' => array(
            array(
                'key'     => 'featured_brand',
                'value'   => true,
                'compare' => 'LIKE'
            )
        )
    )
);

?>

<section class="recommend-main featured_categories">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8 col-mg-8 col-sm-8 col-xs-12">
                <div class="welcome-text text-left">
                    <div class="main-title">
                        <h2><?php echo $featured_brands_title; ?></h2>
                    </div>
                    <p><?php echo $featured_brands_description; ?></p>
                </div>
                <?php
                if ($featured_terms[0]) {
                    $featured_brand_img_0 = get_field('brand_image', 'product_brand_' . $featured_terms[0]->term_id);
                    if (empty($featured_brand_img_0)) {
                        $featured_brand_img_0['sizes']['cat-main-thumb1'] = get_site_url() . '/wp-content/uploads/2021/07/placeholder-723x638.png';
                    }

                ?>
                    <div class="categories-content">
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $featured_terms[0]->slug; ?>">
                            <div class="thamb-img">
                                <img src="<?php echo $featured_brand_img_0['sizes']['cat-main-thumb1']; ?>" alt="<?php echo $featured_terms[0]->name; ?>">
                            </div>
                        </a>
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $featured_terms[0]->slug; ?>" class="category-box"><?php echo strtoupper($featured_terms[0]->name); ?></a>
                    </div>
                <?php } ?>

            </div>
            <div class="col-xl-4 col-lg-4 col-mg-4 col-sm-4 col-xs-12">
                <?php
                if ($featured_terms[1]) {
                    $featured_brand_img_1 = get_field('brand_image', 'product_brand_' . $featured_terms[1]->term_id);
                    if (empty($featured_brand_img_1)) {
                        $featured_brand_img_1['sizes']['cat-main-thumb2'] = get_site_url() . '/wp-content/uploads/2021/07/placeholder-353x339.png';
                    }
                ?>
                    <div class="categories-content">
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $featured_terms[1]->slug; ?>">
                            <div class="thamb-img">
                                <img src="<?php echo $featured_brand_img_1['sizes']['cat-main-thumb2']; ?>" alt="<?php echo $featured_terms[1]->name; ?>">
                            </div>
                        </a>
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $featured_terms[1]->slug; ?>" class="category-box"><?php echo strtoupper($featured_terms[1]->name); ?></a>
                    </div>
                <?php } ?>

                <?php
                if ($featured_terms[2]) {
                    $featured_brand_img_2 = get_field('brand_image', 'product_brand_' . $featured_terms[2]->term_id);
                    if (empty($featured_brand_img_2)) {
                        $featured_brand_img_2['sizes']['cat-main-thumb3'] = get_site_url() . '/wp-content/uploads/2021/07/placeholder-353x384.png';
                    }
                ?>
                    <div class="categories-content">
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $featured_terms[2]->slug; ?>">
                            <div class="thamb-img">
                                <img src="<?php echo $featured_brand_img_2['sizes']['cat-main-thumb3']; ?>" alt="<?php echo $featured_terms[2]->name; ?>">
                            </div>
                        </a>
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $featured_terms[2]->slug; ?>" class="category-box"><?php echo strtoupper($featured_terms[2]->name); ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>
</section>

<?php
if (!empty($featured_terms) && is_array($featured_terms)) {
    $featured_terms_ids = array();
    foreach ($featured_terms as $featured_term) {
        $featured_terms_ids[] = $featured_term->term_id;
    }
}

$all_terms = get_terms(
    array(
        'taxonomy'   => 'product_brand',
        'hide_empty' => true,
        'number' => 12,
        'exclude' => $featured_terms_ids
    )
);

?>
<!--All Brand Section -->
<section class="brand-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="welcome-text text-center">
                    <div class="main-title">
                        <h2><?php echo $all_brands_title; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            if (!empty($all_terms) && is_array($all_terms)) {
                foreach ($all_terms as $all_term) {
                    $brand_logo = get_field('brand_logo', 'product_brand_' . $all_term->term_id);
            ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <a href="<?php echo wc_get_page_permalink('shop') . '?_brands=' . $all_term->slug; ?>" title="<?php echo $all_term->name; ?>" class="category-box text-center">
                            <?php if ($brand_logo) { ?>
                                <div class="cat-img">
                                    <img src="<?php echo $brand_logo['url']; ?>" alt="<?php echo $brand_logo['alt']; ?>">
                                </div>
                            <?php } ?>
                            <div class="cat-name"><?php echo strtoupper($all_term->name); ?></div>
                        </a>
                    </div>
            <?php

                }
            }
            ?>

        </div>
        <?php $brand_browse_all_products_button = get_field('brand_browse_all_products_button'); ?>
        <?php if($brand_browse_all_products_button){?>
        <div class="browse-all-new row">
            <div class="container text-center">
                <div class="col-lg-12 col-md-12">
                    <a href="<?php echo $brand_browse_all_products_button['url']; ?>" title="<?php echo $brand_browse_all_products_button['title']; ?>" class="btn primary-btn"><?php echo $brand_browse_all_products_button['title']; ?></a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
<!--All Brand End -->
<?php
get_footer();
