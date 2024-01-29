    <!-- welcome start -->
    <?php
    global $current_user;

    $offer_content = get_field('offer_content', 'option',false, false);

    ?>
    
    <?php if($offer_content){ ?>
    <div class="card offer-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-body"><?php echo $offer_content; ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <section class="welcome-sec">
        <div class="welcome-inner">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <h1>Welcome, <?php echo $current_user->user_firstname ?>!</h1>
                        <ul>
                            <li>
                                <a href="<?php echo wc_get_account_endpoint_url('orders'); ?>" title="My Orders" class="btn secondary-btn">My Orders</a>
                            </li>
                            <li>
                                <a href="<?php echo wc_get_account_endpoint_url('my-lists'); ?>" title="My Lists" class="btn secondary-btn">My Lists</a>
                            </li>
                            <li>
                                <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" title="My Account" class="btn secondary-btn">My Account</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- welcome end -->
    <!-- recommend Start -->
    <?php
    $rs_title = get_field('rs_title');
    $rs_description = get_field('rs_description');

    $user_id = get_current_user_id();  

    //Previous Order
    $customer = new WC_Customer($user_id);
    // Get the last WC_Order Object instance from current customer
    $last_order = $customer->get_last_order();  
    $postcount = 0;
    $loop = array();


    if($last_order){

        $order_id = $last_order->get_id();
        $product_cat_ids = array();

        foreach ($last_order->get_items() as $last_order_item) :
            $product_id = $last_order_item['product_id'];  


            $last_order_product_terms = get_the_terms($product_id, 'product_cat'); 

            
            foreach ($last_order_product_terms as $last_order_product_term) {
                $product_cat_ids[] = $last_order_product_term->term_id;
            }

        endforeach; 

        $product_cat_selected = array_unique($product_cat_ids);

        $args = array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        'orderby'=> 'rand',
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id', 
                'terms'    => $product_cat_selected,
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
    );

    $loop = new WP_Query($args);   
    $postcount = $loop->post_count;



    } /* end if last order exists */

    /* start if last order not exists */

    if(!$last_order || ($postcount < 6) ) {

        
        $reg_product_cat_select = get_user_meta($user_id, 'reg_product_cat_select', true);
        if ($reg_product_cat_select) {
            $product_cat_selected = unserialize($reg_product_cat_select);
        }

        $reg_product_brand_select = get_user_meta($user_id, 'reg_product_brand_select', true);
        if ($reg_product_brand_select) {
            $product_brand_selected = unserialize($reg_product_brand_select);
        }

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 6,
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'id', 
                    'terms'    => $product_cat_selected,
                ),
                array(
                    'taxonomy' => 'product_brand',
                    'field'    => 'id',
                    'terms'    => $product_brand_selected,
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
        );

        $args2 = array(
            'post_type' => 'product',
            'posts_per_page' => 6,
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',  
        );

        $loop = new WP_Query($args2);   
        $postcount = $loop->post_count;

    }
    ?>
    <section class="recommend-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="welcome-text text-center">
                        <?php if ($rs_title) { ?>
                            <div class="main-title">
                                <h2><?php echo $rs_title; ?></h2>
                            </div>
                        <?php } ?>
                        <?php echo $rs_description; ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                // $loop = new WP_Query($args);
                while ($loop->have_posts()) : $loop->the_post();
                    global $product;
                    $place_image = get_template_directory_uri() . '/assets/images/woocommerce-placeholder-353x447.png';
                    $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($loop->post->ID), 'product-recommended');
					$cdn_featured_image  = get_field('cdn_featured_image');
                    
					if($cdn_featured_image){
					$featured_image['0'] = $cdn_featured_image;
					} else if(empty($featured_image)) {
					$featured_image['0'] = $place_image;
					} else {
					$featured_image['0'] = $featured_image['0'];
					}

                ?>
                    <div class="col-lg-4 col-md-4">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="recommed-box">
                            <div class="rec-img">
                                <img src="<?php echo $featured_image[0]; ?>" data-id="<?php echo $loop->post->ID; ?>" alt="<?php the_title(); ?>">
                            </div>
                            <div class="shop-prdct"><?php the_title(); ?></div>
                        </a>
                    </div>
                <?php
                endwhile;
                wp_reset_query();
                ?>

            </div>
        </div>
    </section>
    <!-- recommend end -->

    <!-- new collection order Start -->
    <?php
    $nc_tilte = get_field('nc_tilte');
    $nc_sub_title = get_field('nc_sub_title');
    $nc_browse_all_new_button = get_field('nc_browse_all_new_button');
    $new_select_products = get_field('new_select_product');
    $new_select_products_last_id = array_pop($new_select_products);
    if ($new_select_products) {
    ?>
        <section class="new-collection">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="welcome-text text-center">
                            <div class="main-title">
                                <h3><?php echo $nc_sub_title; ?></h3>
                                <h2><?php echo $nc_tilte; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row collection-row">
                    <div class="col-lg-6 col-md-12 d-flex flex-wrap">
                        <?php
                        // Check if any term exists
                        if (!empty($new_select_products) && is_array($new_select_products)) {
                            foreach ($new_select_products as $new_select_product) {
                                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($new_select_product), 'product-thumb');
								$cdn_featured_image  = get_field('cdn_featured_image', $new_select_product);
								 $place_image = get_template_directory_uri() . '/assets/images/woocommerce-placeholder-353x447.png';

								if($cdn_featured_image){
                                        $featured_image['0'] = $cdn_featured_image;
                                    } else if(empty($featured_image)) {
                                        $featured_image['0'] = $place_image;
                                    } else {
                                        $featured_image['0'] = $featured_image['0'];
                                    }
                            
                                $product = wc_get_product($new_select_product);
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
                                                <div class="prdct-price"><?php echo $product->get_price_html(); ?></div>
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
                    $featured_image_last = wp_get_attachment_image_src(get_post_thumbnail_id($new_select_products_last_id), 'full');
                    $product_last = wc_get_product($new_select_products_last_id);

					 $cdn_featured_image  = get_field('cdn_featured_image', $new_select_products_last_id);
                    $place_image = get_template_directory_uri() . '/assets/images/woocommerce-placeholder-353x447.png';

					 if($cdn_featured_image){
                            $featured_image_last['0'] = $cdn_featured_image;
                        } else if(empty($featured_image_last)) {
                            $featured_image_last['0'] = $place_image;
                        } else {
                            $featured_image_last['0'] = $featured_image_last['0'];
                        }
                    ?>
                    <div class="col-lg-6 col-md-12 product-box-right">
                        <div class="product-box d-flex align-items-center flex-wrap">
                            <a href="<?php echo $product_last->get_permalink(); ?>" title="" class="product-img">
                                <img src="<?php echo $featured_image_last['0'] ?>" alt="">
                            </a>
                            <div class="product-info">
                                <div class="prdct-desc">
                                    <h4><?php echo $product_last->get_name(); ?></h4>
                                    <span class="prodct-brand">Brand: <?php echo strip_tags(get_the_term_list($new_select_products_last_id, 'product_brand', '', ', ')) ?></span>

                                </div>
                                <div class="prdct-price-main">
                                    <div class="prdct-price"><?php echo $product_last->get_price_html(); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($nc_browse_all_new_button) { ?>
                    <div class="browse-all-new row">
                        <div class="container text-center">
                            <div class="col-lg-12 col-md-12">
                                <a href="<?php echo $nc_browse_all_new_button['url']; ?>" title="<?php echo $nc_browse_all_new_button['title']; ?>" class="btn primary-btn"><?php echo $nc_browse_all_new_button['title']; ?></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
    <!-- new collection end -->
    <?php
    wp_reset_postdata();
   $cp_title = get_field('cp_title');
    $bcp_button = get_field('bcp_button');
    $closeout_brand = get_field('closeout_brand', 'option');
    $closeout_product_quantity = get_field('closeout_product_quantity', 'option');

     $args_closeout = array(
            'post_type'     => 'product',
            'posts_per_page' => 15,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'id',
                    'terms' => $closeout_brand,
                )
            ),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '=',
                ),
            ),
        );  

        $clousetout_query = new WP_Query($args_closeout);
        $cnt = 1;

        if ($clousetout_query->have_posts()) {
            ?>
            <section class="brands-main closeout">
            <div class="container">
                <div class="main-title text-center">
                    <h2><?php echo $cp_title; ?></h2>
                </div>
                <div class="row">
                    <div class="brands-offer owl-carousel owl-theme">
                    <?php

            while($clousetout_query->have_posts()) : $clousetout_query->the_post();

            global $post;
            $varinstock = false;
            $product = wc_get_product($post->ID);

            $variations = $product->get_children();
           
            foreach($variations as $variation){
                
                $variation_obj = new WC_Product_variation($variation);
                $stock = $variation_obj->get_stock_quantity();

                if($stock >= $closeout_product_quantity){ 
                    $varinstock = true;
                }
            }

            if($varinstock == true){ 
                $place_image = get_template_directory_uri() . '/assets/images/woocommerce-placeholder-150x150.png';
                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
				$cdn_featured_image  = get_field('cdn_featured_image', $post->ID);
                // wc_get_template_part( 'content', 'product' );
				if($cdn_featured_image){
                            $featured_image['0'] = $cdn_featured_image;
                        } else if(empty($featured_image_last)) {
                            $featured_image['0'] = $place_image;
                        } else {
                            $featured_image['0'] = $featured_image['0'];
                        }	
                if (empty($featured_image)) {
                    $featured_image['0'] = $place_image;
                }
                            ?>

                <div class="item">
                    <div class="brands-img align-items-center d-flex justify-content-center">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                            <img src="<?php echo $featured_image['0']; ?>" alt="<?php the_title(); ?>">
                        </a>
                    </div>
                </div>
                
                <?php
                $cnt++;
            } else {
                //echo 'No products availabe 1';
            }
            
        endwhile;

        if($cnt == 1){
            echo '<h4>No products availabe</h4>';
        } ?>
        </div>
                </div>
            </div>

            <?php if ($bcp_button) { ?>
                <div class="browse-all-new row">
                    <div class="container text-center">
                        <div class="col-lg-12 col-md-12">
                            <a href="<?php echo $bcp_button['url'] ?>" title="<?php echo $bcp_button['title'] ?>" class="btn primary-btn"><?php echo $bcp_button['title'] ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
        <?php
        } else {
        echo '<h4>No products availabe</h4>';
        }  ?>
        
    <!-- browse categories Start -->
    <?php
    $select_category = get_field('select_category');
    $bc_title = get_field('bc_title');
    $browse_all_products_button = get_field('browse_all_products_button');
    ?>
    <section class="browse-categories">
        <div class="container">
            <?php if ($bc_title) { ?>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="welcome-text text-center">
                            <div class="main-title">
                                <h2><?php echo $bc_title; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <?php
                // Check if any term exists
                if (!empty($select_category) && is_array($select_category)) {
                    foreach ($select_category as $term) {
                        $thumbnail_id = get_term_meta($term, 'thumbnail_id', true);
                        $cat_image = wp_get_attachment_url($thumbnail_id);
                        $term_obj = get_term($term, 'product_cat');
                ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="<?php echo get_term_link($term, 'product_cat'); ?>" title="" class="category-box text-center">
                                <div class="cat-img">
                                    <img src="<?php echo $cat_image; ?>" alt="">
                                </div>
                                <div class="cat-name"><?php echo strtoupper($term_obj->name); ?></div>
                            </a>
                        </div>
                <?php

                    }
                }
                ?>
            </div>
            <?php if ($browse_all_products_button) { ?>
                <div class="browse-all-new row">
                    <div class="container text-center">
                        <div class="col-lg-12 col-md-12">
                            <a href="<?php echo $browse_all_products_button['url']; ?>" title="<?php echo $browse_all_products_button['title']; ?>" class="btn primary-btn"><?php echo $browse_all_products_button['title']; ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
    <!-- browse categories end -->
    <?php //get_template_part('templates-parts/newsletter-section'); 
    ?>