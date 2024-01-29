<?php

add_filter( 'woocommerce_settings_tabs_array', 'dpssw_discontinued_woocommerce_settings_tabs_array_filter', 30, 1 );

/**
 * Function for `woocommerce_settings_tabs_array` filter-hook.
 * Add discontinued product setting tab in woocommerce setting page.
 *
 * @param array $settings_tabs .
 * @return array
 */
function dpssw_discontinued_woocommerce_settings_tabs_array_filter( $settings_tabs ) {
	$settings_tabs['discontinued_settings_tab'] = __( 'Discontinued Products', 'discontinued-products-stock-status' );
	return $settings_tabs;
}


add_action( 'woocommerce_sections_discontinued_settings_tab', 'dpssw_discontinued_settings_tab_sections', 10, 1 );

/**
 * Adds tabs on woocommece setting page.
 *
 * @param array $output_sections .
 * @return void
 */
function dpssw_discontinued_settings_tab_sections( $output_sections ) {
	global $current_section;
	echo '<ul class="subsubsub">';
	$sections   = array(
		''        => __( 'General', 'discontinued-products-stock-status' ),
		'restore' => __( 'Restore', 'discontinued-products-stock-status' ),
	);
	$array_keys = array_keys( $sections );
	foreach ( $sections as $id => $label ) {
		$url       = admin_url( 'admin.php?page=wc-settings&tab=discontinued_settings_tab&section=' . sanitize_title( $id ) );
		$class     = ( $current_section === $id ? 'current' : '' );
		$separator = ( end( $array_keys ) === $id ? '' : '|' );
		$text      = esc_html( $label );
		echo "<li><a href='$url' class='$class'>$text</a> $separator </li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</ul><br class="clear" />';
}


add_action( 'woocommerce_settings_discontinued_settings_tab', 'dpssw_discontinued_settings_tab_options', 10, 1 );

/**
 * To Show all setttings fields.
 */
function dpssw_discontinued_settings_tab_options() {
	$settings = dpssw_get_discontinued_settings_for_section();
	if ( ! empty( $settings ) && class_exists( 'WC_Admin_Settings' ) ) {
		WC_Admin_Settings::output_fields( $settings );
	}
}


add_action( 'woocommerce_settings_save_discontinued_settings_tab', 'dpssw_discontinued_settings_tab_options_save', 10, 1 );

/**
 * To update/save values all setttings fields.
 */
function dpssw_discontinued_settings_tab_options_save() {
	$settings = dpssw_get_discontinued_settings_for_section();
	if ( ! empty( $settings ) && class_exists( 'WC_Admin_Settings' ) ) {
		WC_Admin_Settings::save_fields( $settings );
	}
}

/**
 * Make setting page on admin.
 *
 * @return string
 */
function dpssw_get_discontinued_settings_for_section() {
	global $current_section;

	switch ( $current_section ) {
		case '':
			$settings = array(
				'section_title'                 => array(
					'name' => __( 'Discontinued products global settings', 'discontinued-products-stock-status' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'wc_discontinued_settings_tab_section_title',
				),
				'discontinued_show_in_catalog'  => array(
					'title'   => __( 'Show Discontinued products in WooCommerce catalog?', 'discontinued-products-stock-status' ),
					'id'      => 'discontinued_show_in_catalog',
					'default' => 'no',
					'type'    => 'checkbox',
					'desc'    => __( 'By default, discontinued products do not appear in WooCommerce Catalog. However if you want those products to appear in archieve page and WooCommerce shortcodes.', 'discontinued-products-stock-status' ),
				),
				'enable_custom_message'         => array(
					'title'   => __( 'Enable custom discountined message for Discontinued products?', 'discontinued-products-stock-status' ),
					'id'      => 'discontinued_enable_custom_message',
					'default' => 'no',
					'type'    => 'checkbox',
					'desc'    => __( 'Enable this if you want to set product-specific messages for particular products', 'discontinued-products-stock-status' ),
				),
				'discontinued_global_message'   => array(
					'name'        => __( 'Enter the Global Message', 'discontinued-products-stock-status' ),
					'type'        => 'text',
					'id'          => 'discontinued_global_message',
					'placeholder' => __( 'Set Custom Global Message for all Discontinued Products', 'discontinued-products-stock-status' ),
				),
				'discontinued_hide_search'      => array(
					'title'   => __( "Hide Discontinued Products showing up in your Website's search results.", 'discontinued-products-stock-status' ),
					'id'      => 'discontinued_hide_search',
					'default' => 'no',
					'type'    => 'checkbox',
					'desc'    => __( "Enable this if you want to stop discontinued products from showing up in your website's search results.", 'discontinued-products-stock-status' ),
				),
				'discontinued_greyscale_effect' => array(
					'title'   => __( 'Apply Grayscale effect on Discontinued products', 'discontinued-products-stock-status' ),
					'id'      => 'discontinued_greyscale_effect',
					'default' => 'no',
					'type'    => 'checkbox',
					'desc'    => __( "Enable this if you want to show the discontinued product's images with grayscale effect on archive page and WooCommerce shortcodes. Products with no image will use this.", 'discontinued-products-stock-status' ),
				),
				'section_end'                   => array(
					'type' => 'sectionend',
					'id'   => 'wc_discontinued_settings_tab_section_end',
				),
			);
			break;
		case 'restore':
			$settings = array(
				'section_title'                      => array(
					'name' => __( 'Restore Settings', 'discontinued-products-stock-status' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'wc_discontinued_settings_restore_tab_section_title',
				),
				'discontinued_restore_to_outofstock' => array(
					'title'    => __( 'Revert  products from "Discontinued" stock status to "Out of Stock" status?', 'discontinued-products-stock-status' ),
					'desc'     => __( 'Enabling this setting will  set all the WooCommerce products in the  "Discontinued" stock status  to "Out of Stock" stock status on deactivation of this plugin.', 'discontinued-products-stock-status' ),
					'desc_tip' => esc_html__( 'NOTE - If this setting is enabled and plugin is deactivated for any reason, all the products in the "discontinued" stock status will be updated and those changes cannot be undone.', 'discontinued-products-stock-status' ),
					'id'       => 'discontinued_restore_to_outofstock',
					'type'     => 'checkbox',
					'default'  => 'no',
					'autoload' => false,
				),
				'section_end'                        => array(
					'type' => 'sectionend',
					'id'   => 'wc_discontinued_settings_restore_tab_section_end',
				),
			);
			break;

		default:
			$settings = array();
			break;
	}
	return apply_filters( 'dpssw_discontinued_settings_tab_settings', $settings );
}


add_filter( 'plugin_action_links_' . DPSSW_DISCOUNTINUED_PLUGIN_BASENAME, 'dpssw_discontinued_plugin_setting_link', 10, 1 );

/**
 * Show 'Settings' action links on the plugin screen.
 *
 * @param mixed $links Plugin Action links.
 *
 * @return array
 */
function dpssw_discontinued_plugin_setting_link( $links ) {
	$action_links = array(
		'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=discontinued_settings_tab' ) . '" aria-label="' . esc_attr__( 'Settings', 'discontinued-products-stock-status' ) . '">' . esc_html__( 'Settings', 'discontinued-products-stock-status' ) . '</a>',
	);
	return array_merge( $action_links, $links );
}


add_filter( 'woocommerce_product_data_tabs', 'dpssw_discontinued_products_tabs', 10, 1 );

/**
 * Add custom Product data tab on Product page backend.
 *
 * @param array $tabs Return array of tabs to show.
 * @return array $tabs .
 */
function dpssw_discontinued_products_tabs( $tabs ) {
	$tabs['discontinued-products-tabs'] = array(
		'label'    => __( 'Discontinued Products', 'discontinued-products-stock-status' ),
		'class'    => array( 'show_if_simple show_if_variable show_if_grouped' ),
		'target'   => 'discontinued_tab_container',
		'priority' => 100,
	);
	return $tabs;
}


add_action( 'woocommerce_product_data_panels', 'dpssw_discontinued_product_tab_content' );

/**
 * Discontinued product tab contents on product page backend.
 */
function dpssw_discontinued_product_tab_content() {
	global $post;
	$product_id = $post->ID;

	// Note the 'id' attribute needs to match the 'target' parameter set above.
	?>
	<div id='discontinued_tab_container' class='panel woocommerce_options_panel'>
		<div class='options_group'>
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'          => '_discontinued_product',
					'label'       => __( 'Discontinued Product:', 'discontinued-products-stock-status' ),
					'description' => __( 'Check this box if you want to set the entire product as discontinued', 'discontinued-products-stock-status' ),
					'value'       => get_post_meta( $product_id, '_discontinued_product', true ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'          => 'show_specific_messsage',
					'label'       => __( 'Product Message Type', 'discontinued-products-stock-status' ),
					'type'        => 'select',
					'class'       => 'select short',
					'options'     => array(
						'global_text_message'      => __( 'Global Message', 'discontinued-products-stock-status' ),
						'product_specific_message' => __( 'Product Specific Message', 'discontinued-products-stock-status' ),
					),
					'desc_tip'    => 'true',
					'description' => __( 'Choose type of message to be displayed for the Discontinued product', 'discontinued-products-stock-status' ),
				)
			);
			$editor_id = 'custom_editor_box';
			$content   = get_post_meta( $product_id, 'custom_editor_box', true );
			wp_editor( $content, $editor_id );
			?>
		</div>
	</div>
	<?php
}


add_action( 'woocommerce_process_product_meta_simple', 'dpssw_save_discontinued_product_option_fields', 10, 1 );
add_action( 'woocommerce_process_product_meta', 'dpssw_save_discontinued_product_option_fields', 10, 1 );

/**
 * Save the custom fields from simple variable, grouped product.
 *
 * @param int $post_id .
 */
function dpssw_save_discontinued_product_option_fields( $post_id ) {
	$product = wc_get_product( $post_id ); // product object.

	// save stock status for variable and grouped product.
	if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
		$is_discontinued_product = isset( $_POST['_discontinued_product'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_discontinued_product', $is_discontinued_product );
	}

	if ( isset( $_POST['show_specific_messsage'] ) ) :
		update_post_meta( $post_id, 'show_specific_messsage', sanitize_text_field( wp_unslash( $_POST['show_specific_messsage'] ) ) );
	endif;

	/* WTYIWYG Editor Data */
	if ( isset( $_POST['custom_editor_box'] ) ) :
		update_post_meta( $post_id, 'custom_editor_box', wp_kses_post( wp_unslash( $_POST['custom_editor_box'] ) ) );
	endif;
}


add_action( 'woocommerce_variation_options_dimensions', 'dpssw_add_wc_variable_discontinued_to_variations', 10, 3 );

/**
 * To add custom field for discontinued stock status on variation level.
 *
 * @param int     $loop           Position in the loop.
 * @param array   $variation_data Variation data.
 * @param WP_Post $variation      Post data.
 */
function dpssw_add_wc_variable_discontinued_to_variations( $loop, $variation_data, $variation ) {
	?>
	<div class="variation-discontinued-div" style="display: none;">
		<?php
		$message_type = get_post_meta( $variation->ID, '_discontinued_messsage_type', true );

		woocommerce_wp_select(
			array(
				'id'          => 'wc_discontinued_messsage_type[' . $loop . ']',
				'label'       => __( 'Discontinued Message Type', 'discontinued-products-stock-status' ),
				'type'        => 'select',
				'class'       => 'select dpssw-select',
				'value'       => esc_attr( $message_type ),
				'options'     => array(
					'global_text_message'         => __( 'Global Message', 'discontinued-products-stock-status' ),
					'variations_specific_message' => __( 'Variation Specific Message', 'discontinued-products-stock-status' ),
				),
				'desc_tip'    => 'true',
				'description' => __( 'Choose type of message to be displayed for Discontinued product', 'discontinued-products-stock-status' ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'            => 'wc_variable_discontinued[' . $loop . ']',
				'class'         => 'form-field form-row-full',
				'wrapper_class' => 'dpssw-message',
				'label'         => __( 'Discontinued description', 'discontinued-products-stock-status' ),
				'value'         => get_post_meta( $variation->ID, '_variable_discontinued_textarea', true ),
			)
		);
		?>
	</div>
	<?php
}


add_action( 'woocommerce_save_product_variation', 'dpssw_save_variable_discontinued_data', 10, 2 );

/**
 * To save custom field values on product variation .
 *
 * @param int $variation_id .
 * @param int $i .
 */
function dpssw_save_variable_discontinued_data( $variation_id, $i ) {

	if ( isset( $_POST['wc_variable_discontinued'][ $i ] ) || isset( $_POST['wc_discontinued_messsage_type'][ $i ] ) ) {

		$variable_discontinued_textarea = sanitize_text_field( wp_unslash( $_POST['wc_variable_discontinued'][ $i ] ) );
		$discontinued_messsage_type     = sanitize_text_field( wp_unslash( $_POST['wc_discontinued_messsage_type'][ $i ] ) );
	}

	if ( isset( $variable_discontinued_textarea ) ) {
		update_post_meta( $variation_id, '_variable_discontinued_textarea', sanitize_text_field( wp_unslash( $variable_discontinued_textarea ) ) );
	}
	if ( isset( $discontinued_messsage_type ) ) {
		update_post_meta( $variation_id, '_discontinued_messsage_type', sanitize_text_field( wp_unslash( $discontinued_messsage_type ) ) );
	}
}


add_filter( 'woocommerce_available_variation', 'dpssw_add_wc_discontinued_variation_data', 10, 3 );

/**
 * To add custom field discountinued data in the variation object to display in product page.
 *
 * @param array  $data .
 * @param object $product .
 * @param object $variation .
 * @return array
 */
function dpssw_add_wc_discontinued_variation_data( $data, $product, $variation ) {

	if ( $variation->get_stock_status() === 'discontinued' ) {

		$variation_id = $variation->get_id();
		$message      = '';

		$message_type          = get_post_meta( $variation_id, '_discontinued_messsage_type', true );
		$custom_message_enable = get_option( 'discontinued_enable_custom_message' );

		if ( 'yes' === $custom_message_enable ) {
			$message = ! empty( get_option( 'discontinued_global_message' ) ) ? get_option( 'discontinued_global_message' ) : __( 'This product has been discontinued', 'discontinued-products-stock-status' );
			if ( 'variations_specific_message' === $message_type ) {
				$variation_message = get_post_meta( $variation_id, '_variable_discontinued_textarea', true );
				$message           = ! empty( $variation_message ) ? $variation_message : $message;
			}
		} else {
			$message = __( 'This product has been discontinued', 'discontinued-products-stock-status' );
		}

		$data['availability_html'] = apply_filters( 'dpssw_customize_variation_product_message', '<div class=discontinued_status_message>' . esc_attr( $message ) . '</div>' ); // modify the variation product message.

	}
	return $data;
}


add_action( 'woocommerce_after_single_product_summary', 'dpssw_hide_discontinued_variations_and_grouped_forms' );

/**
 * To hide all variation and group products when checkbox is checked form 'Discontinued Tab'.
 */
function dpssw_hide_discontinued_variations_and_grouped_forms() {
	global $post;
	$product_id = $post->ID;
	$product    = wc_get_product( $product_id );
	if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {

		$is_checked = get_post_meta( $product_id, '_discontinued_product', true );
		if ( 'yes' === $is_checked ) {
			?>
			<script>
				is_discontinued = '<?php echo esc_attr( $is_checked ); ?>';
				if ('yes' == is_discontinued) {
					jQuery('.variations_form , form.cart.grouped_form').hide();
				}
			</script>
			<?php
		}
	}
}


add_filter( 'woocommerce_product_stock_status_options', 'dpssw_add_woocommerce_product_stock_status_option', 10, 1 );

/**
 * Add new 'Discontinued' stock status options inside Product Inventory tab.
 *
 * @param array $status Add a new stock status called discontinued.
 * @return array $status .
 */
function dpssw_add_woocommerce_product_stock_status_option( $status ) {

	$status['discontinued'] = __( 'Discontinued', 'discontinued-products-stock-status' ); // Add new statuses.
	return $status;
}


add_action( 'woocommerce_process_product_meta_simple', 'dpssw_save_custom_stock_status', 10, 1 );

/**
 * Save Product Meta Boxes 'Discontinued' stock status.
 *
 * @param int $product_id .
 */
function dpssw_save_custom_stock_status( $product_id ) {
	$product = wc_get_product( $product_id ); // product object.

	// save stock status for simple product.
	if ( isset( $_POST['_stock_status'] ) && ! empty( $_POST['_stock_status'] ) ) {

		update_post_meta( $product_id, '_stock_status', wc_clean( sanitize_text_field( wp_unslash( $_POST['_stock_status'] ) ) ) );
		$product->set_stock_status( wc_clean( sanitize_text_field( wp_unslash( $_POST['_stock_status'] ) ) ) );
		$product->save();
	}
}


add_filter( 'woocommerce_admin_stock_html', 'dpssw_woocommerce_admin_stock_html', 100, 2 );

/**
 * Admin 'Discontinued' stock html.
 * Apply discontinued label to stock status on admin all product page.
 *
 * @param mixed $stock_html .
 * @param mixed $product  .
 */
function dpssw_woocommerce_admin_stock_html( $stock_html, $product ) {
	$pid = $product->get_id(); // gets product id.

	if ( $product->is_type( 'simple' ) ) {

		$product_stock_status = $product->get_stock_status();
		if ( 'discontinued' === $product_stock_status ) {
			$stock_html = '<mark class="discontinued">' . __( 'Discontinued', 'discontinued-products-stock-status' ) . '</mark>';
			$stock_html = apply_filters( 'dpssw_admin_discontinued_text_css', $stock_html ); // modify the stock status style of the product in the admin menu.
		}
	} elseif ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {

		$status = get_post_meta( $pid, '_discontinued_product', true );
		if ( 'yes' === $status ) {
			$stock_html = '<mark class="discontinued">' . __( 'Discontinued', 'discontinued-products-stock-status' ) . '</mark>';
			$stock_html = apply_filters( 'dpssw_admin_discontinued_text_css', $stock_html ); // modify the stock status style of the product in the admin menu.
		}
	}
	return $stock_html;
}


add_filter( 'woocommerce_product_query_meta_query', 'dpssw_custom_product_meta_query', 10, 1 );

/**
 * Hide all Discontinued Products from Catalog and Search when 'Show in Catalog' is disabled from settings Page.
 *
 * @param  array $meta_query Meta query.
 */
function dpssw_custom_product_meta_query( $meta_query ) {
	$show_in_catalog = get_option( 'discontinued_show_in_catalog' );

	if ( 'yes' !== $show_in_catalog ) {
		$meta_query_array = array(
			'key'     => '_stock_status',
			'value'   => 'discontinued',
			'compare' => '!=',
		);
		$meta_query[]     = apply_filters( 'dpssw_customize_catalog_query', $meta_query_array );
	}
	return $meta_query;
}


add_action( 'woocommerce_product_query', 'dpssw_hide_variable_group_products' );

/**
 * Hide discontinued variable & grouped product from archive, shop & category page.
 *
 * @param object $query .
 */
function dpssw_hide_variable_group_products( $query ) {
	global $wpdb;
	$show_in_catalog = get_option( 'discontinued_show_in_catalog' );

	// all discontinued product id of variable products and grouped products..
	$variable_discontinued_product_ids = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '_discontinued_product' AND `meta_value` IN ( 'yes' )" );
	// $variable_discontinued_product_ids = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` LIKE '_discontinued_product' AND `meta_value` IN ( 'yes' )" );

	// hide variable product on category, shop & archive page.
	if ( ! is_admin() ) {
		if ( 'yes' !== $show_in_catalog ) {
			$query->set( 'post__not_in', $variable_discontinued_product_ids );
		}
	}
}

/**
 * Returns discontinued product id.
 *
 * @param array $related_posts .
 * @return array
 */
function dpssw_get_discontiued_product_ids( $related_posts ) {
	$all_product_id = array(); // all discontinued product id.

	foreach ( $related_posts as $rp ) {
		$product = wc_get_product( $rp ); // product object.

		if ( $product->is_type( 'simple' ) ) {
			$product_stock_status = $product->get_stock_status();

			// gets simple discontinued product id.
			if ( 'discontinued' === $product_stock_status ) {
				array_push( $all_product_id, $rp );
			}
		} elseif ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
			$status = get_post_meta( $rp, '_discontinued_product', true );

			// gets variable & grouped discontinued product id.
			if ( 'yes' === $status ) {
				array_push( $all_product_id, $rp );
			}
		}
	}
	return $all_product_id;
}


add_filter( 'woocommerce_related_products', 'dpssw_hide_discontinued_product_related_product', 10, 3 );

/**
 * Hide discontinued product from related product .
 *
 * @param array $related_posts array of product ids.
 * @param int   $product_id  cuurent product id.
 * @param array $array  array of values of related product.
 * @return array
 */
function dpssw_hide_discontinued_product_related_product( $related_posts, $product_id, $array ) {
	$show_in_catalog         = get_option( 'discontinued_show_in_catalog' );
	$new_array               = array(); // after removed discontinued product id.
	$discontinued_product_id = dpssw_get_discontiued_product_ids( $related_posts ); // all discontinued product id.

	if ( 'yes' !== $show_in_catalog ) {
		$new_array     = array_diff( $related_posts, $discontinued_product_id ); // un discontinued product id.
		$related_posts = $new_array;
	}
	return $related_posts;
}


add_filter( 'woocommerce_shortcode_products_query_results', 'dpssw_hide_discontinued_product_from_shortcode', 50, 1 );

/**
 * Hide discontinued product from woocommerce shortcode used.
 *
 * @param object $results object of shortcode product ids.
 * @return object return modified object after removing discontinued product ids.
 */
function dpssw_hide_discontinued_product_from_shortcode( $results ) {
	$show_in_catalog         = get_option( 'discontinued_show_in_catalog' );
	$new_array               = array(); // after removed discontinued product id.
	$discontinued_product_id = dpssw_get_discontiued_product_ids( $results->ids ); // all discontinued product id.

	if ( 'yes' !== $show_in_catalog ) {
		$new_array    = array_diff( $results->ids, $discontinued_product_id ); // not discontinued product id.
		$results->ids = $new_array;
	}
	return $results;
}


add_filter( 'pre_get_posts', 'dpssw_get_discontinued_post_search_filter', 1000, 1 );

/**
 * Gets all discontinued products while filtering in all products page.
 *
 * @param object $query .
 */
function dpssw_get_discontinued_post_search_filter( $query ) {
	global $wpdb;
	$valid_instock_prod_id = array(); // all valid instock ids of all type of product.
	$var_discontinued_prod = array(); // variable and grouped discontinued product ids.

	// ony work if filter is applied while search.
	if ( isset( $_GET['filter_action'] ) ) {

		// runs only for main query.
		if ( $query->is_main_query() ) {

			// All product id from lookup table of all stock type.
			$product_id_lookup_instock = $wpdb->get_col( "SELECT product_id FROM $wpdb->wc_product_meta_lookup WHERE `stock_status` IN ( 'instock', 'onbackorder', 'outofstock', 'discontinued' )" );

			// All product id of _stock_status excluded discontinued from postmeta.
			$product_id_excude_discontinued = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '_stock_status' AND `meta_value` IN ( 'instock', 'onbackorder', 'outofstock' )" );

			// Gets all type of product id stock status from postmeta.
			$all_product_ids = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '_stock_status' AND `meta_value` IN ( 'instock', 'onbackorder', 'outofstock', 'discontinued' )" );

			// All product id of variable & grouped from postmeta of stock status yes or no.
			$product_id_gv_products = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '_discontinued_product' AND `meta_value` IN ( 'yes', 'no' )" );

			$args = array(
				'post_type'      => 'product',     // WooCommerce product post type.
				'post_status'    => 'any',         // Any post status.
				'posts_per_page' => -1,            // Retrieve all products.
			);

			$products = get_posts( $args );

			foreach ( $products as $product ) {
				$product_id = $product->ID;

				// To get Product Id of all products which are not published.
				if ( 'publish' !== get_post_status( $product_id ) ) {
					array_push( $valid_instock_prod_id, $product_id );
				}
			}

			// checks from product lookup table.
			foreach ( $product_id_lookup_instock as $pid ) {
				$product = wc_get_product( $pid ); // gets product.

				if ( gettype( $product ) === 'object' ) {

					// only work if variable type is variable or grouped.
					if ( $product->get_type() === 'variable' || $product->get_type() === 'grouped' ) {

						if ( metadata_exists( 'post', $pid, '_discontinued_product' ) ) {

							// checks the product ids from variable and group stock status.
							if ( in_array( $pid, $product_id_gv_products, true ) ) {

								// stores instock product id.
								if ( 'no' === get_post_meta( $pid, '_discontinued_product', true ) ) {
									array_push( $valid_instock_prod_id, $pid );
								} else {
									array_push( $var_discontinued_prod, $pid );
								}
							}
						} else {
							// Case for Non-discontinued Variable/Grouped product.
							array_push( $valid_instock_prod_id, $pid );
						}
					} else {

						// store instock product id excluding for variable and grouped product.
						if ( in_array( $pid, $product_id_excude_discontinued, true ) ) {
							array_push( $valid_instock_prod_id, $pid );
						}
					}
				}
			}

			if ( isset( $_GET['stock_status'] ) ) {

				// only run for discontinued is serched in filter.
				if ( 'discontinued' === $_GET['stock_status'] ) {
					$query->set( 'post__not_in', $valid_instock_prod_id ); // exclude all product id except discontinued.
					unset( $_GET['stock_status'] ); // unset the stock status.
				} elseif ( 'instock' === $_GET['stock_status'] ) {
					$query->set( 'post__not_in', $var_discontinued_prod ); // exclude all discontinued id.
				} else {
					$query->set( 'post__in', $all_product_ids ); // include all product id.
				}
			}
		}
	}
}


add_action( 'woocommerce_product_options_stock_fields', 'dpssw_woocommerce_product_options_inventory_data' );

/**
 * Function for `woocommerce_product_options_inventory_product_data` action-hook.
 */
function dpssw_woocommerce_product_options_inventory_data() {
	woocommerce_wp_checkbox(
		array(
			'id'          => '_stock_discontinued_product',
			'label'       => __( 'Mark as discontinued', 'discontinued-products-stock-status' ),
			'desc_tip'    => true,
			'description' => __( "Check this box if you want to set the stock status of this product to 'Discontinued' after stock quantity becomes zero.", 'discontinued-products-stock-status' ),
			'value'       => get_post_meta( get_the_id(), '_stock_discontinued_product', true ),
		)
	);
}


add_action( 'save_post_product', 'dpssw_product_save', 10, 2 );

/**
 * Save option for discontinued inventory zero status.
 *
 * @param int    $post_id .
 * @param object $post .
 */
function dpssw_product_save( $post_id, $post ) {
	if ( ! empty( $_POST['_stock_discontinued_product'] ) ) {
		update_post_meta( $post_id, '_stock_discontinued_product', 'yes' );
	} else {
		update_post_meta( $post_id, '_stock_discontinued_product', '' );
	}
}


add_action( 'woocommerce_variation_options_inventory', 'dpssw_woocommerce_product_options_variation', 10, 3 );

/**
 * Save option for discontinued inventory zero status for varaiable product.
 *
 * @param object $loop .
 * @param object $variation_data .
 * @param object $variation .
 */
function dpssw_woocommerce_product_options_variation( $loop, $variation_data, $variation ) {
	woocommerce_wp_checkbox(
		array(
			'id'            => '_stock_discontinued_product[' . $loop . ']',
			'label'         => __( 'Mark as discontinued', 'discontinued-products-stock-status' ),
			'desc_tip'      => true,
			'description'   => __( "Check this box if you want to set the stock status of this product to 'Discontinued' after stock quantity becomes zero.", 'discontinued-products-stock-status' ),
			'value'         => get_post_meta( $variation->ID, '_stock_discontinued_product', true ),
			'wrapper_class' => 'dpssw_discon',
		)
	);
}


add_action( 'woocommerce_save_product_variation', 'dpssw_product_save_variation', 10, 2 );

/**
 * Saves varaiation data.
 *
 * @param int $variation_id .
 * @param int $i .
 */
function dpssw_product_save_variation( $variation_id, $i ) {
	if ( isset( $_POST['_stock_discontinued_product'][ $i ] ) || isset( $_POST['_stock_discontinued_product'][ $i ] ) ) {
		$variable_discontinued_option = sanitize_text_field( wp_unslash( $_POST['_stock_discontinued_product'][ $i ] ) );
	}

	// saves variation product option data.
	if ( isset( $variable_discontinued_option ) ) {
		update_post_meta( $variation_id, '_stock_discontinued_product', 'yes' );
	} else {
		update_post_meta( $variation_id, '_stock_discontinued_product', '' );
	}
}

/**
 * This function will set all the WooCommerce products in the  'Discontinued' stock status  to 'Out of Stock' stock status on deactivation of this plugin.
 */
function dpssw_restore_to_outofstock_on_plugin_deactivate() {
	global $wpdb;

	$reset_to_outofstock = get_option( 'discontinued_restore_to_outofstock' );
	if ( 'yes' === $reset_to_outofstock ) {
		if ( function_exists( 'wc_get_product' ) ) {
			$discontinued_products_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='%s' AND meta_value='%s'", '_stock_status', 'discontinued' ) );
			if ( ! empty( $discontinued_products_ids ) ) {
				foreach ( $discontinued_products_ids as $product_id ) {
					// Get an instance of the WC_Product object.
					$product = wc_get_product( $product_id );

					// Get product stock quantity and stock status.
					$stock_quantity = $product->get_stock_quantity();
					$stock_status   = $product->get_stock_status();

					// Set product stock quantity (zero) and stock status (out of stock).
					$product->set_stock_quantity( 0 );
					$product->set_stock_status( 'outofstock' );

					// Save the data and refresh caches.
					$product->save();
				}
			}
		}
	}
}
