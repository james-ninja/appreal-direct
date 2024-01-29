<?php

add_action('wp_ajax_nopriv_woo2_load_product', 'woo2_quick_v2_load_product');
add_action('wp_ajax_woo2_load_product', 'woo2_quick_v2_load_product');

//add_action('save_post_product', 'woo2_quick_v2_on_update', 999);

function woo2_helper_attribute_name( $taxonomy, $slug )
{
    $term = get_term_by( 'slug', $slug, $taxonomy );
    return ( is_wp_error( $term ) || !$term ) ? $slug : $term->name;

}

function woo2_quick_v2_on_update()
{
    $option_name = '_capri_all_products';
    delete_option($option_name);
}

function woo2_quick_v2_all()
{
    $option_name = '_capri_all_products';
    //static $data = false;
    /* $data = get_option($option_name, false);
    if ($data) {
        return $data;
    }*/

    $args = array(
        'post_type' => 'product',
        'numberposts' => -1,
        'orderby' => 'menu_order name',
        'order' => 'ASC',
        'suppress_filters' => true,
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '=',
            ),
        ),
        //'fields' => 'ids',
    );

    $posts = get_posts($args);

    //$attribs = array();

    $products = array();
    foreach ($posts as $post_id) {
        //$product = wc_get_product($post_id);
        $sku = get_post_meta($post_id->ID, '_sku', true);
        $product_style = get_post_meta($post_id->ID, 'product_style', true);

        /*if ($product->is_type('variable')) {
            $a = array_flip(array_keys($product->get_variation_attributes()));
            $attribs = array_merge($attribs, $a);
        }*/

        $products[] = array(
            //'id' => $product->get_sku(),
            //'text' => $product->get_title(),
            'id' => $sku,
            'text' => $post_id->post_title,
            'product_style' => $product_style,
            //'search' => get_post_meta($post_id->ID, 'Search', true),
        );
    }
    $attribs_value = array('pa_size', 'pa_color');
    $data = array(
        'attributes' => $attribs_value,
        //'attributes' => array_keys($attribs),
        'products' => $products,
    );

    //update_option($option_name, $data);
    return $data;
}

function woo2_quick_v2_all_json()
{
    $args = array(
        'post_type' => 'product',
        'numberposts' => -1,
        'orderby' => 'menu_order name',
        'order' => 'ASC',
        'suppress_filters' => true,
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '=',
            ),
        ),
        'fields' => 'ids',
    );

    $posts = get_posts($args);

    $products = array();
    foreach ($posts as $post_id) {
        $product = wc_get_product($post_id);
        // $products[ $product->get_title() ] = strtolower( get_post_meta( $post_id, 'Search', true ) );
        $products[$product->name] = strtolower(get_post_meta($post_id, 'Search', true));
    }

    return $products;
}


function woo2_quick_v2_load_product()
{
    $response = array(
        'success' => 1,
    );

    if (empty($_POST['sku'])) {

        $response = array(
            'error' => 1,
        );
    } else {

        $sku = sanitize_text_field($_POST['sku']);
        $sku_variation_id = sanitize_text_field($_POST['sku_variation']);

        $product_id = woo2_quick_v2_product_by_meta($sku, '_sku');

        $product_style = get_post_meta($product_id, 'product_style', true);

        $response['id'] = $product_id;
        $product = wc_get_product($product_id);

        $variation_data = array();
        $variations = $product->get_available_variations();

        foreach ($variations as $variation) {

            if (!$variation['is_purchasable']) {
                continue;
            }

            /* if ($sku_variation_id) {
                if ($variation['variation_id'] == $sku_variation_id) {
                    $attributes = array();
                    foreach ($variation['attributes'] as $key => $value) {
                        $key = preg_replace('/^attribute_/', '', $key);
                        $response['vars'][$key][$value] = woo2_helper_attribute_name($key, $value);
                        $attributes[$key] = $value;

                        ksort($response['vars'][$key]);
                    }

                    $response['variations'][implode(':', $attributes)] = $variation['variation_id'];

                    $variation_data[$variation['variation_id']] = array(
                        'variation_id' => $variation['variation_id'],
                        'variation_price' => $variation['display_price'],
                        'variation_max_qty' => $variation['max_qty'],
                        'variation_min_qty' => $variation['min_qty'],
                        'variation_upc' => $variation['upc_field'],
                        'variation_sku' => $variation['sku'],
                        'attributes' => $attributes,
                    );
                }*/
            //} else {
            $attributes = array();
            foreach ($variation['attributes'] as $key => $value) {
                $key = preg_replace('/^attribute_/', '', $key);
                $response['vars'][$key][$value] = woo2_helper_attribute_name($key, $value);
                $attributes[$key] = $value;
                ksort($response['vars'][$key]);
            }

            $response['variations'][implode(':', $attributes)] = $variation['variation_id'];

            $variations_filter[$variation['variation_id']] = array(
                'pa_size' => $attributes['pa_size'],
                'pa_color' => $attributes['pa_color']

            );

            $variation_data[$variation['variation_id']] = array(
                'variation_id' => $variation['variation_id'],
                'variation_price' => $variation['display_price'],
                'variation_max_qty' => $variation['max_qty'],
                'variation_min_qty' => $variation['min_qty'],
                'variation_upc' => $variation['upc_field'],
                'variation_sku' => $variation['sku'],
                'product_style' =>   $product_style,
                'attributes' => $attributes,
            );
            //}
        }
    }


    $termidArray = array();
    foreach ($response['vars']['pa_size'] as $key => $colum_value) {
        $term = get_term_by('slug', $key, esc_attr(str_replace('attribute_', '', 'pa_size')));
        $termidArray[] = $term->term_id;
    }

    $get_terms_size_order = get_terms(array(
        'taxonomy' => 'pa_size',
        'meta_key'   => 'order',
        'orderby'    => 'meta_value_num',
        'hide_empty' => false,
        'include' => $termidArray,
        'fields' => 'slugs'
    ));

    $sizeorder = array();

    foreach ($get_terms_size_order as $get_terms_size) {

        $sizeorder[$get_terms_size] = woo2_helper_attribute_name('pa_size', $get_terms_size);
    }

    $response['variations_filter'] =  $variations_filter;

    $response['vars']['pa_size'] = $sizeorder;

    ksort($response['variations']);
    foreach ($response['variations'] as $variation_id) {
        $response['variation_data'][$variation_id] = $variation_data[$variation_id];
    }

    wp_send_json($response);
    exit;
}


function woo2_quick_v2_product_by_meta($value, $key = '_sku')
{
    $args = array(
        'post_type' => 'product',
        'numberposts' => 1,
        'meta_key' => $key,
        'meta_value' => $value,
        'suppress_filters' => true,
        'fields' => 'ids',
    );

    $posts = get_posts($args);
    return reset($posts);
}

function woo2_is_quick_order()
{
    $order_pages = array(
        'executiveorders',
        'orders',
        'internalorders',
        'order-preview',
        2734,
    );

    return is_page($order_pages);
}

function woo2_is_quick_order_special()
{
    $order_pages = array(
        'executiveorders',
        'internalorders',
    );

    return is_page($order_pages);
}

//custom mt
function woo2_quick_v2_all_variation()
{
    /*$option_name = '_capri_all_products_variation';
    //static $data = false;
    $data = get_option($option_name, false);
    if ($data) {
        return $data;
    }*/

    $args = array(
        'post_type' => 'product_variation',
        'numberposts' => -1,
        'orderby' => 'menu_order name',
        'order' => 'ASC',
        'suppress_filters' => true,
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '=',
            ),
        )
        //'fields' => 'ids',
    );

    $posts = get_posts($args);

    foreach ($posts as $post_id) {

        $stock = get_post_meta($post_id->ID, '_stock', true);
        $sku = get_post_meta($post_id->ID, '_sku', true);
        $upc_field = get_post_meta($post_id->ID, 'upc_field', true);
        $parent_product_sku = get_post_meta($post_id->post_parent, '_sku', true);

        if ($stock > 0) {
            if ($upc_field) {
                $products2[] = array(
                    'id' =>  $post_id->ID,
                    'text' => $upc_field,
                    'parent_id' => $parent_product_sku,
                );
            }
        }
    }
    $data = array(
        'products' => $products2,
    );
    //update_option( $option_name, $data );
    return $data;
}