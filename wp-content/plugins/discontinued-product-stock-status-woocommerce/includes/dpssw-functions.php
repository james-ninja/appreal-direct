<?php
/**
 * Return the status after checking stock quantity and backorder.
 *
 * @param int $id .
 */
function get_stock_discontinued_status( $id ) {
	$status              = 'instock';
	$stock_level_option  = get_post_meta( $id, '_manage_stock', true ); // stock level options.
	$stock_quantity      = get_post_meta( $id, '_stock', true ); // stock quantity.
	$stock_backorder     = get_post_meta( $id, '_backorders', true ); // stock back order.
	$discon_stock_option = get_post_meta( $id, '_stock_discontinued_product', true ); // stock discontinued option.

	// Apply discontinued stock status.
	if ( 'yes' === $stock_level_option && 'yes' === $discon_stock_option ) {

		// works only if back order is not set.
		if ( 'no' === $stock_backorder ) {

			// If stock status is zero or less then zero mark as discontinied.
			if ( intval( $stock_quantity ) <= 0 ) {
				$status              = 'discontinued';
				$discon_stock_option = update_post_meta( $id, '_stock_status', $status );
			} else {
				$status              = 'instock';
				$discon_stock_option = update_post_meta( $id, '_stock_status', $status );
			}
		}
	}
}


add_filter( 'woocommerce_is_purchasable', 'dpssw_is_discontinued_product_purchasable', 10, 2 );
add_filter( 'woocommerce_variation_is_purchasable', 'dpssw_is_discontinued_product_purchasable', 10, 2 );

/**
 * Make the discontinued product purchasable to false so that they cannot be added to the cart.
 *
 * @param boolean $is_purchasable .
 * @param object  $object .
 */
function dpssw_is_discontinued_product_purchasable( $is_purchasable, $object ) {

	get_stock_discontinued_status( $object->get_parent_id() );
	$is_checked = get_post_meta( $object->get_parent_id(), '_discontinued_product', true );

	if ( $object->get_stock_status() === 'discontinued' || 'yes' === $is_checked ) {
		$is_purchasable = false;
	}
	return $is_purchasable;
}


add_action( 'woocommerce_before_shop_loop_item', 'dpssw_apply_greyscale_effect_on_discontinued_products', 10 );

/**
 * Applies the greyscale effect to the product in the product catalog page and archive page.
 */
function dpssw_apply_greyscale_effect_on_discontinued_products() {
	global $product;
	$product_id = $product->get_id(); // gets product id.

	get_stock_discontinued_status( $product_id );
	$show_in_catalog = get_option( 'discontinued_show_in_catalog' );

	if ( 'yes' === $show_in_catalog ) {
		$greyscale_effect_slider_enable = get_option( 'discontinued_greyscale_effect' );

		$stock_status = $product->get_stock_status(); // gets stock status.
		if ( 'yes' === $greyscale_effect_slider_enable ) {

			// for simple product hide discontinued price and do gray scale.
			if ( $product->is_type( 'simple' ) ) {

				if ( 'discontinued' === $stock_status ) {
					?>
					<style>
						/* Hide price */
						li.post-<?php echo intval( $product_id ); ?> span.price {
							display: none !important;
						}

						/* Apply grayscale */
						img.sft-discontinued {
							filter: grayscale(1) !important;
						}
					</style>
					<?php
				}
			} elseif ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) { // for variable product.
				$status = get_post_meta( $product_id, '_discontinued_product', true ); // get stock status.

				if ( 'yes' === $status ) {
					?>
					<style>
						/* Hide price */
						li.post-<?php echo intval( $product_id ); ?> span.price {
							display: none !important;
						}

						/* Apply grayscale */
						img.sft-discontinued{
							filter: grayscale(1) !important;
						}
					</style>
					<?php
				}
			}
		}
	}
	do_action( 'dpssw_add_grayscale_effect' );
}


add_filter( 'woocommerce_get_price_html', 'dpssw_hide_price_discontinued_products', 10, 2 );

/**
 * Remove price from single product summary for discontinued product.
 *
 * @param int    $price .
 * @param object $product .
 */
function dpssw_hide_price_discontinued_products( $price, $product ) {
	$product_id = $product->get_id(); // gets product id.
	get_stock_discontinued_status( $product_id ); // update the stock status.

	// if product found.
	if ( ! is_null( $product ) ) {
		$product_type = $product->get_type(); // get type of product.

		// removes price for discontinued product.
		if ( 'simple' === $product_type ) {
			$stock_status = $product->get_stock_status();
			if ( 'discontinued' === $stock_status ) {
				$price = '';
			}
		} elseif ( 'variable' === $product_type || 'grouped' === $product_type ) {
			$status = get_post_meta( $product_id, '_discontinued_product', true );
			if ( 'yes' === $status ) {
				$price = '';
			}
		}
	}
	return $price;
}


add_filter( 'woocommerce_show_variation_price', 'dpssw_hide_price_discontinued_variations', 10, 3 );

/**
 * To show price only for in-stock variation in product summary page
 *
 * @param mixed $condition .
 * @param mixed $product .
 * @param mixed $variation .
 */
function dpssw_hide_price_discontinued_variations( $condition, $product, $variation ) {
	if ( $variation->get_stock_status() === 'discontinued' ) {
		return false;
	} else {
		return true;
	}
}


add_filter( 'woocommerce_loop_add_to_cart_link', 'dpssw_change_add_product_link', 10, 2 );

/**
 * Removes add to cart button from discontinued products.
 *
 * @param string $link .
 * @param object $product .
 * @return string $link modified
 */
function dpssw_change_add_product_link( $link, $product ) {
	$stock_status = ''; // stores the stock status.
	$product_id   = $product->get_id();
	if ( $product->is_type( 'simple' ) || $product->is_type( 'grouped' ) ) {

		get_stock_discontinued_status( $product_id );  // update the stock status.
		$stock_status = $product->get_stock_status(); // Get stock status.
	} elseif ( $product->is_type( 'variable' ) ) {

		foreach ( $product->get_visible_children() as $variation_id ) {
			get_stock_discontinued_status( $variation_id );  // update the stock status.
			$variation    = wc_get_product( $variation_id ); // Get product.
			$stock_status = $variation->get_stock_status(); // Get stock status.
		}
	}

	// for discontinued stock status removed add to cart link.
	if ( 'discontinued' === $stock_status ) {
		$link = '';
	}
	return $link;
}


add_action( 'woocommerce_after_shop_loop_item', 'dpssw_hide_add_to_cart_buttons', 1 );

/**
 * Removes add to cart from variable & grouped product.
 */
function dpssw_hide_add_to_cart_buttons() {
	global $product;
	$product_id = $product->get_id(); // gets product id.

	if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
		get_stock_discontinued_status( $product_id );  // run function and update the stock status.

		$status = get_post_meta( $product_id, '_discontinued_product', true );

		// hide add to cart from shop, archive, category & shortcode page.
		if ( 'yes' === $status ) {
			if ( ! is_admin() ) {
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
			}
		}
	}
}


add_action( 'woocommerce_grouped_product_list_before', 'dpssw_grouped_product_page_message', 10, 3 );

/**
 * Add the custom message in the price section for the grouped product child.
 *
 * @param mixed  $grouped_product_columns .
 * @param mixed  $quantites_required .
 * @param object $product .
 */
function dpssw_grouped_product_page_message( $grouped_product_columns, $quantites_required, $product ) {
	foreach ( $grouped_product_columns as $column_id ) {
		if ( 'price' === $column_id ) {
			add_filter( 'woocommerce_grouped_product_list_column_' . $column_id, 'dpssw_grouped_discontinued_products_message_', 10, 2 );
		}
	}
}

/**
 * Add the custom message in the price section for the grouped product child
 *
 * @param int   $value price of the product.
 * @param mixed $grouped_product_child .
 */
function dpssw_grouped_discontinued_products_message_( $value, $grouped_product_child ) {
	global $post;
	if ( $grouped_product_child->get_stock_status() === 'discontinued' ) {
		$specific_messsage_dropdown = get_post_meta( $post->ID, 'show_specific_messsage', true );
		$specific_message           = get_post_meta( $post->ID, 'custom_editor_box', true );
		$custom_message_enable      = get_option( 'discontinued_enable_custom_message' );
		if ( 'yes' === $custom_message_enable ) {
			if ( 'global_text_message' === $specific_messsage_dropdown ) {
				$global_message = get_option( 'discontinued_global_message' );
				if ( empty( $global_message ) ) {
					$global_message = __( 'This product has been discontinued', 'discontinued-products-stock-status' );
				}

				$global_message_html = '<div class="discontinued_status_message">' . $global_message . '</div>';
			} else {
				$specific_message    = ! empty( $specific_message ) ? $specific_message : __( 'This product has been discontinued', 'discontinued-products-stock-status' );
				$global_message_html = '<div class="discontinued_status_message">' . $specific_message . '</div>';
			}
		} else {
			$global_message_html = '<div class="discontinued_status_message">' . __( 'This product has been discontinued', 'discontinued-products-stock-status' ) . '</div>';
		}
		$global_message_html = apply_filters( 'dpssw_customize_grouped_product_message', $global_message_html );
		return $global_message_html;
	} else {
		return $value;
	}
}


add_filter( 'woocommerce_grouped_price_html', 'dpssw_hide_price_discontinued_grouped', 10, 3 );

/**
 * Remove the price of grouped products from single summary page and shop page
 *
 * @param int    $price .
 * @param object $product .
 * @param int    $child_prices .
 */
function dpssw_hide_price_discontinued_grouped( $price, $product, $child_prices ) {
	$grouped_product_status = $product->get_stock_status(); // product stock status.

	if ( 'discontinued' === $grouped_product_status && ! is_admin() ) {
		$price = '';
	}
	return $price;
}


add_action( 'woocommerce_single_product_summary', 'dpssw_to_show_discontinued_product_message', 29 );

/**
 * Used to display custom message in the single product summary page and remove the add to cart button.
 * Get the messages from the Settings API and the WYSIWYG Editor.
 * Based on the condition, display the appropriate message.
 */
function dpssw_to_show_discontinued_product_message() {
	global $product;
	$product_id   = $product->get_id(); // gets product id.
	$stock_status = ''; // saves stock status.

	get_stock_discontinued_status( $product_id );  // update the stock status.

	if ( $product->is_type( 'simple' ) ) {
		$stock_status = $product->get_stock_status();
	} elseif ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {

		get_stock_discontinued_status( $product->get_id() );  // run function and update the stock status.
		$is_checked = get_post_meta( $product->get_id(), '_discontinued_product', true );

		if ( 'yes' === $is_checked ) {
			$stock_status = 'discontinued';
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
	}

	/* For discontinued products remove the add to cart button and add the custom message */
	if ( 'discontinued' === $stock_status ) {

		$specific_messsage_dropdown = get_post_meta( $product->get_id(), 'show_specific_messsage', true );

		$custom_message_enable = get_option( 'discontinued_enable_custom_message' );

		if ( 'yes' === $custom_message_enable ) {
			if ( 'global_text_message' === $specific_messsage_dropdown || '' === $specific_messsage_dropdown ) {
				$global_message = get_option( 'discontinued_global_message' );
				if ( empty( $global_message ) ) {
					$global_message = __( 'This product has been discontinued', 'discontinued-products-stock-status' );
				}
				$global_message_html = '<div class="discontinued_status_message">' . wp_kses_post( $global_message ) . '</div>';
			} else {
				$specific_message    = get_post_meta( $product->get_id(), 'custom_editor_box', true );
				$specific_message    = ! empty( $specific_message ) ? $specific_message : __( 'This product has been discontinued', 'discontinued-products-stock-status' );
				$global_message_html = '<div class="discontinued_status_message">' . wp_kses_post( $specific_message ) . '</div>';
			}
		} else {
			$global_message_html = '<div class="discontinued_status_message">' . __( 'This product has been discontinued', 'discontinued-products-stock-status' ) . '</div>';
		}
		echo wp_kses_post( $global_message_html );
		do_action( 'dpssw_messages_div' );  // add a addditional messsage div in the simple product page.
	}
}


add_filter( 'pre_get_posts', 'dpssw_search_hide_discontinued_post', 10, 1 );

/**
 * Hides discontinued products from search result.
 *
 * @param object $query all product query.
 */
function dpssw_search_hide_discontinued_post( $query ) {
	global $wpdb;
	$all_discontinued_product_id = array(); // all valid instock ids of all type of product.

	// gets option to show in catelog.
	$hide_from_search = get_option( 'discontinued_hide_search' );

	if ( 'yes' === $hide_from_search ) {

		// runs only for search query.
		if ( $query->is_search() ) {

			// All product id of instock product from postmeta.
			$product_id_discontinued = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '_stock_status' AND `meta_value` IN ( 'discontinued' )" );

			// All product id of variable & grouped from postmeta.
			$product_id_gv_products = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '_discontinued_product' AND `meta_value` IN ( 'yes' )" );

			// Combine simpe, variable and grouped discontinued product ids.
			$all_discontinued_product_id = array_merge( $product_id_discontinued, $product_id_gv_products );

			// exclude all discontinued id.
			$query->set( 'post__not_in', $all_discontinued_product_id );
		}
	}
}


add_filter( 'wp_get_attachment_image_attributes', 'dpssw_attachment_image_attributes', 10, 3 );

/**
 * Add custom class to discontinued product.
 *
 * @param string $attr class attribute.
 * @param object $attachment .
 * @param array  $size .
 * @return array attr
 */
function dpssw_attachment_image_attributes( $attr, $attachment, $size ) {
	$product_id = $attachment->post_parent; // product id.
	get_stock_discontinued_status( $product_id );  // update the stock status.

	if ( $product_id ) {

		$product = wc_get_product( $product_id ); // product object.

		if ( ! is_null( $product ) && ! is_bool( $product ) ) {

			$greyscale_effect_slider_enable = get_option( 'discontinued_greyscale_effect' ); // grayscale option.

			$stock_status = $product->get_stock_status(); // gets stock status.

			// add class to products.
			if ( 'yes' === $greyscale_effect_slider_enable ) {

				// for simple product hide discontinued price and do gray scale.
				if ( $product->is_type( 'simple' ) ) {

					// on discontinued add class.
					if ( 'discontinued' === $stock_status ) {
						$attr['class'] .= ' sft-discontinued';
					}
				} elseif ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) { // for variable product.
					$status = get_post_meta( $product_id, '_discontinued_product', true ); // get stock status.

					// on discontinued add class.
					if ( 'yes' === $status ) {
						$attr['class'] .= ' sft-discontinued';
					}
				}
			}
		}
	}
	return $attr;
}


add_action( 'wp_ajax_dpssw_update', 'dpssw_ajax_update_notice' );
add_action( 'wp_ajax_nopriv_dpssw_update', 'dpssw_ajax_update_notice' );

/**
 * Update rate Notice.
 */
function dpssw_ajax_update_notice() {
	global $current_user;
	if ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'discontinued-products-stock-status' ) ) {
			wp_die( esc_html__( 'Permission Denied.', 'discontinued-products-stock-status' ) );
		}

		update_user_meta( $current_user->ID, 'dpssw_rate_notices', 'rated' );
		echo esc_url( network_admin_url() );
	}
	wp_die();
}


add_action( 'admin_notices', 'dpssw_plugin_notice' );

/**
 * Rating notice widget.
 * Save the date to display notice after 10 days.
 */
function dpssw_plugin_notice() {
	global $current_user;

	// if plugin is activated and date is not set then set the next 10 days.
	$today_date = strtotime( 'now' );

	if ( ! get_user_meta( $current_user->ID, 'dpssw_notices_time' ) ) {
		$after_10_day = strtotime( '+10 day', $today_date );
		update_user_meta( $current_user->ID, 'dpssw_notices_time', $after_10_day );
	} else {
		// gets the option of user rating status and week status.
		$rate_status = get_user_meta( $current_user->ID, 'dpssw_rate_notices', true );
		$next_w_date = get_user_meta( $current_user->ID, 'dpssw_notices_time', true );

		// show if user has not rated the plugin and it has been 1 week.
		if ( 'rated' !== $rate_status && $today_date > $next_w_date ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p><span><?php esc_html_e( "Awesome, you've been using", 'discontinued-products-stock-status' ); ?></span><span><?php echo '<strong> Discontinued Product Stock Status for WooCommerce </strong>'; ?><span><?php esc_html_e( 'for more than 1 week', 'discontinued-products-stock-status' ); ?></span></p>
				<p><?php esc_html_e( 'If you like our plugin Would you like to rate our plugin at WordPress.org ?', 'discontinued-products-stock-status' ); ?></p>
				<span><a href="https://wordpress.org/support/plugin/discontinued-product-stock-status-woocommerce/reviews/" target="_blank"><?php esc_html_e( "Yes, I'd like to rate it!", 'discontinued-products-stock-status' ); ?></a></span>&nbsp; - &nbsp;<span><a class="dpssw_hide_rate" href="#"><?php esc_html_e( 'I already did!', 'discontinued-products-stock-status' ); ?></a></span>
				<br /><br/>
			</div>
			<?php
		}
	}
}
