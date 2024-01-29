<?php
$cate = get_queried_object();
$cateID = $cate->term_id;
$header_banner_image = get_field('header_banner_image', 'product_cat_' . $cateID);

$terms_cat = get_term_children($cateID, 'product_cat');
$terms_cat_all = get_term_children($cateID, 'product_cat');
/*$args = array(
	'hide_empty' => 0,
	'orderby' => 'name',
	'depth' => 1,
	'parent' => $cateID,
	'order' => 'ASC',
	'fields' => 'ids',
	'taxonomy' => 'product_cat'
);

$terms_cat = get_categories($args);
$terms_cat_all = get_categories($args);
*/

?>

<!--Header Banner Section -->
<div class="inner-banner" style="background-image: url(<?php echo $header_banner_image['url'] ?>);">
	<?php if ($cate->name) { ?>
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12">
					<h2 class="text-center text-uppercase"><?php echo $cate->name; ?></h2>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<!--Header Banner Section End -->

<?php
if (!empty($terms_cat) && is_array($terms_cat)) {
	$featured_category_ids = array();
	foreach ($terms_cat as $term_cat) {
		$featured_category = get_field('featured_category', 'product_cat_' . $term_cat);
		$featured_category_order = get_field('featured_category_order', 'product_cat_' . $term_cat);
		if ($featured_category == true) {
			if ($featured_category_order) {
				$featured_category_ids[$featured_category_order] = $term_cat;
			}
		}
	}
}
ksort($featured_category_ids);
$featured_category_ids = array_slice($featured_category_ids, 0, 3);
?>

<!-- Featured Categories Section -->
<section class="recommend-main featured_categories">
	<div class="container">
		<div class="row">
			<div class="col-xl-8 col-lg-8 col-mg-8 col-sm-8 col-xs-12">
				<div class="welcome-text text-left">
					<div class="main-title">
						<h2><span>Featured </span>Categories</h2>
					</div>
					<p><?php echo $cate->description; ?></p>
				</div>
				<?php
				if ($featured_category_ids[0]) {
					$term_obj0 = get_term($featured_category_ids[0], 'product_cat');
					$featured_category_img_0 = get_field('category_image', 'product_cat_' . $featured_category_ids[0]);
				?>
					<div class="categories-content">
						<div class="thamb-img">
							<a href="<?php echo get_term_link($featured_category_ids[0], 'product_cat'); ?>">
								<img src="<?php echo $featured_category_img_0['sizes']['cat-main-thumb1']; ?>" alt="<?php echo $term_obj0->name; ?>">
							</a>
						</div>
						<a href="<?php echo get_term_link($featured_category_ids[0], 'product_cat'); ?>" class="category-box"><?php echo strtoupper($term_obj0->name); ?></a>
					</div>
				<?php } ?>

			</div>
			<div class="col-xl-4 col-lg-4 col-mg-4 col-sm-4 col-xs-12">
				<?php
				if ($featured_category_ids[1]) {
					$term_obj1 = get_term($featured_category_ids[1], 'product_cat');
					$featured_category_img_1 = get_field('category_image', 'product_cat_' . $featured_category_ids[1]);
				?>
					<div class="categories-content">
						<div class="thamb-img">
							<a href="<?php echo get_term_link($featured_category_ids[1], 'product_cat'); ?>">
								<img src="<?php echo $featured_category_img_1['sizes']['cat-main-thumb2']; ?>" alt="<?php echo $term_obj1->name; ?>">
							</a>
						</div>
						<a href="<?php echo get_term_link($featured_category_ids[1], 'product_cat'); ?>" class="category-box"><?php echo strtoupper($term_obj1->name); ?></a>
					</div>
				<?php } ?>

				<?php
				if ($featured_category_ids[2]) {
					$term_obj2 = get_term($featured_category_ids[2], 'product_cat');
					$featured_category_img_2 = get_field('category_image', 'product_cat_' . $featured_category_ids[2]);
				?>
					<div class="categories-content">
						<div class="thamb-img">
							<a href="<?php echo get_term_link($featured_category_ids[2], 'product_cat'); ?>">
								<img src="<?php echo $featured_category_img_2['sizes']['cat-main-thumb3']; ?>" alt="<?php echo $term_obj2->name; ?>">
							</a>
						</div>
						<a href="<?php echo get_term_link($featured_category_ids[2], 'product_cat'); ?>" class="category-box"><?php echo strtoupper($term_obj2->name); ?></a>
					</div>
				<?php } ?>
			</div>
		</div>

	</div>
</section>
<!-- Featured Categories Section End -->


<?php /*
$args_brand_product = array(
	'post_type'     => 'product',
	'posts_per_page' => -1,
	'tax_query' => array(
		array(
			'taxonomy' => 'product_cat',
			'field' => 'id',
			'terms' => $cateID,
		)
	),
);
$brand_product_query = new WP_Query($args_brand_product);


$cat_ids = array();
if ($brand_product_query->have_posts()) {
	while ($brand_product_query->have_posts()) : $brand_product_query->the_post();
		//global $brand_product_query;

		$terms = get_the_terms(get_the_ID(), 'product_brand');
		foreach ($terms as $term) {
			$cat_ids[] = $term->term_id;
		}
	endwhile;
	wp_reset_query();
}

$cat_ids = array_unique($cat_ids);
$brand_all_categories = $cat_ids;*/

?>

<!-- brands Start -->
<section class="brands-main category_brands">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12">
				<div class="welcome-text text-center">
					<div class="main-title">
						<h2><span>BROWSE BY </span>BRAND</h2>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="brands-offer owl-carousel owl-theme">
				<?php
				$terms = get_terms(
					array(
						'taxonomy'   => 'product_brand',
						//'include' => $brand_all_categories,
						'hide_empty' => true,
						'exclude' => array(471)
					)
				);

				// Check if any term exists
				if (!empty($terms) && is_array($terms)) {
					foreach ($terms as $term) {
						$brand_logo = get_field('brand_logo', 'product_brand_' . $term->term_id);
						if ($brand_logo) {
				?> <a href="<?php echo wc_get_page_permalink('shop') . '?_categories=' . $cate->slug . '&_brands=' . $term->slug; ?>" title="<?php echo $term->name; ?>">
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
					<a href="<?php echo home_url('/brands') ?>" title="Browse All Brands" class="btn primary-btn">Browse All Brands</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- brands end -->
<?php
//Exclude featured_category_ids
$terms_cat_all_list = array_diff($terms_cat_all, $featured_category_ids);
$terms_cat_all = array_slice($terms_cat_all_list, 0, 4);
$terms_cat_all2 = array_slice($terms_cat_all_list, 4, 4);

?>
<!-- Browse by category Section -->
<section class="category-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12">
				<div class="welcome-text text-center">
					<div class="main-title">
						<h2><span>Browse By </span>Category</h2>
					</div>
				</div>
			</div>
		</div>
		<?php
		if (count($terms_cat_all_list) <= 4) { ?>

			<div class="row">

				<?php
				if (!empty($terms_cat_all) && is_array($terms_cat_all)) {
					foreach ($terms_cat_all as  $key => $term_cat_all) {

						$category_image = get_field('category_image', 'product_cat_' . $term_cat_all);
						$cat_light_thumbnail_image = get_field('cat_light_thumbnail_image', 'product_cat_' . $term_cat_all);
						$cat_image = $cat_light_thumbnail_image['url'];
						if ($key == 0) {
							$category_image_url = $category_image['sizes']['cat-sub-thumb1'];
							if (empty($category_image_url)) {
								$category_image_url = get_site_url() . '/wp-content/uploads/2021/07/placeholder-535x657.png';
							}
						} else {
							$category_image_url = $category_image['sizes']['cat-sub-thumb2'];
							if (empty($category_image_url)) {
								$category_image_url = get_site_url() . '/wp-content/uploads/2021/07/placeholder-535x393.png';
							}
						}


						$term_obj = get_term($term_cat_all, 'product_cat');

						if (empty($cat_light_thumbnail_image)) {
							$thumbnail_id = get_term_meta($term_cat_all, 'thumbnail_id', true);
							$cat_image = wp_get_attachment_url($thumbnail_id);
						}
				?>
						<div class="category-main col-lg-6 col-md-6 col-sm-12">
							<div class="box-img">
								<img src="<?php echo $category_image_url; ?>" alt="<?php echo $term_obj->name; ?>">
							</div>
							<div class="content">
								<?php if ($cat_image) { ?>
									<a href="<?php echo get_term_link($term_cat_all, 'product_cat'); ?>">
										<div class="icon">
											<img src="<?php echo $cat_image; ?>" alt="<?php echo $term_obj->name; ?>">
										</div>
									</a>
								<?php } ?>
								<a href="<?php echo get_term_link($term_cat_all, 'product_cat'); ?>" class="category-box"><?php echo $term_obj->name; ?></a>
							</div>
						</div>
				<?php

					}
				}
				?>
			</div>

		<?php } else { ?>

			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12">
					<?php
					if (!empty($terms_cat_all) && is_array($terms_cat_all)) {
						foreach ($terms_cat_all as  $key => $term_cat_all) {

							$category_image = get_field('category_image', 'product_cat_' . $term_cat_all);
							$cat_light_thumbnail_image = get_field('cat_light_thumbnail_image', 'product_cat_' . $term_cat_all);
							$cat_image = $cat_light_thumbnail_image['url'];
							if ($key == 0) {
								$category_image_url = $category_image['sizes']['cat-sub-thumb1'];
								if (empty($category_image_url)) {
									$category_image_url = get_site_url() . '/wp-content/uploads/2021/07/placeholder-535x657.png';
								}
							} else {
								$category_image_url = $category_image['sizes']['cat-sub-thumb2'];
								if (empty($category_image_url)) {
									$category_image_url = get_site_url() . '/wp-content/uploads/2021/07/placeholder-535x393.png';
								}
							}


							$term_obj = get_term($term_cat_all, 'product_cat');

							if (empty($cat_light_thumbnail_image)) {
								$thumbnail_id = get_term_meta($term_cat_all, 'thumbnail_id', true);
								$cat_image = wp_get_attachment_url($thumbnail_id);
							}
					?>
							<div class="category-main">
								<div class="box-img">
									<img src="<?php echo $category_image_url; ?>" alt="<?php echo $term_obj->name; ?>">
								</div>
								<div class="content">
									<?php if ($cat_image) { ?>
										<a href="<?php echo get_term_link($term_cat_all, 'product_cat'); ?>">
											<div class="icon">
												<img src="<?php echo $cat_image; ?>" alt="<?php echo $term_obj->name; ?>">
											</div>
										</a>
									<?php } ?>
									<a href="<?php echo get_term_link($term_cat_all, 'product_cat'); ?>" class="category-box"><?php echo $term_obj->name; ?></a>
								</div>
							</div>
					<?php

						}
					}
					?>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12">
					<?php
					if (!empty($terms_cat_all2) && is_array($terms_cat_all2)) {
						foreach ($terms_cat_all2 as $key => $term_cat_all) {

							$category_image = get_field('category_image', 'product_cat_' . $term_cat_all);
							$cat_light_thumbnail_image = get_field('cat_light_thumbnail_image', 'product_cat_' . $term_cat_all);
							$cat_image = $cat_light_thumbnail_image['url'];
							if ($key == 2) {
								$category_image_url = $category_image['sizes']['cat-sub-thumb1'];
								if (empty($category_image_url)) {
									$category_image_url = get_site_url() . '/wp-content/uploads/2021/07/placeholder-535x657.png';
								}
							} else {
								$category_image_url = $category_image['sizes']['cat-sub-thumb2'];
								if (empty($category_image_url)) {
									$category_image_url = get_site_url() . '/wp-content/uploads/2021/07/placeholder-535x393.png';
								}
							}
							$term_obj = get_term($term_cat_all, 'product_cat');

							if (empty($cat_light_thumbnail_image)) {
								$thumbnail_id = get_term_meta($term_cat_all, 'thumbnail_id', true);
								$cat_image = wp_get_attachment_url($thumbnail_id);
							}
					?>
							<div class="category-main">
								<div class="box-img">
									<img src="<?php echo $category_image_url; ?>" alt="<?php echo $term_obj->name; ?>">
								</div>
								<div class="content">
									<?php if ($cat_image) { ?>
										<a href="<?php echo get_term_link($term_cat_all, 'product_cat'); ?>">
											<div class="icon">
												<img src="<?php echo $cat_image; ?>" alt="<?php echo $term_obj->name; ?>">
											</div>
										</a>
									<?php } ?>
									<a href="<?php echo get_term_link($term_cat_all, 'product_cat'); ?>" class="category-box"><?php echo $term_obj->name; ?></a>
								</div>
							</div>
					<?php

						}
					}
					?>
				</div>
			</div>
		<?php }
		?>

		<div class="browse-all-new">
			<div class="row">
				<div class="col-lg-12 col-md-12 text-center">
					<a href="<?php echo wc_get_page_permalink('shop'); ?>" title="Browse All" class="btn primary-btn">Browse All</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Browse by category End -->