<?php
/*
 * Template Name: Closeout Page
*/
get_header(); ?>

<?php
$closeout_brand = get_field('closeout_brand', 'option');
$closeout_product_quantity = get_field('closeout_product_quantity', 'option');
?>
<?php get_template_part('templates-parts/header-banner-section'); ?>

<div id="global_sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 woocommerce">

        <?php
        $args_closeout = array(
            'post_type'     => 'product',
            'posts_per_page' => -1,
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
            woocommerce_product_loop_start();

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
                
                wc_get_template_part('content', 'product');
                $cnt++;
            } else {
                //echo 'No products availabe 1';
            }
            
        endwhile;

        if($cnt == 1){
            echo '<h4>No products availabe</h4>';
        }
        woocommerce_product_loop_end();

        } else {
        echo '<h4>No products availabe</h4>';
        } ?> 
            </div>
        </div>
    </div>        
</div><!-- EOF : content ID -->

<?php get_footer(); ?>