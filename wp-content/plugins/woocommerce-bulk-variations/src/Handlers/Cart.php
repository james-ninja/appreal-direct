<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Handlers;

use Barn2\Plugin\WC_Bulk_Variations\Util\Util,
	Barn2\WBV_Lib\Registerable,
	Barn2\WBV_Lib\Service;

/**
 * This class handles caching for the product tables.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Cart implements Registerable, Service {

	public function register() {
		add_action( 'wp_loaded', [ __CLASS__, 'process_multi_cart' ], 20 );
	}

	public static function process_multi_cart() {

		// Make sure we don't process form twice when adding via AJAX.
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || 'POST' !== Util::get_server_request_method() || ! filter_input( INPUT_POST, 'multiple-add-to-cart' ) ) {
			return;
		}

		$add_to_cart = filter_input( INPUT_POST, 'multiple-add-to-cart' );
		$product_ids = explode( ',', $add_to_cart );
		$products    = [];

		foreach ( $product_ids as $product_id ) {

			if ( isset( $products[ $product_id ] ) ) {
				$products[ $product_id ]++;
			} else {
				$products[ $product_id ] = 1;
			}
		}

		if ( ! is_array( $products ) ) {
			return;
		}

		if ( empty( $products ) ) {
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( __( 'Please select one or more products.', 'woocommerce-bulk-variations' ), 'error' );
			}
			return;
		}

		$added = self::add_to_cart_multi( $products );
		if ( $added ) {
			wc_add_to_cart_message( $added, true );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}
	}

	/**
	 * Add multiple products to the cart in a single step.
	 *
	 * @param type $products - An array of products (including quantities and variation data) to add to the cart
	 * @return array An array of product IDs => quantity added
	 */
	public static function add_to_cart_multi( $products ) {
		$added_to_cart = [];

		if ( ! $products ) {
			return $added_to_cart;
		}

		foreach ( $products as $variation_id => $quantity ) {

			$product = wc_get_product( $variation_id );
			if ( $product ) {
				$quantity     = $quantity;
				$product_id   = $product->get_parent_id();
				$product_type = $product->get_type();
				if ( $product_type === 'variation' ) {

					if ( self::add_to_cart( $product_id, $quantity, $variation_id ) ) {
						if ( isset( $added_to_cart[ $variation_id ] ) ) {
							$quantity += $added_to_cart[ $variation_id ];
						}
						$added_to_cart[ $variation_id ] = $quantity;
					}
				}
			}
		}

		return $added_to_cart;
	}

	public static function add_to_cart( $product_id, $quantity = 1, $variation_id = false, $variations = [] ) {
		if ( ! $product_id ) {
			wc_add_notice( __( 'No product selected. Please try again.', 'woocommerce-bulk-variations' ), 'error' );
			return false;
		}

		$product        = wc_get_product( $product_id );
		$variable_prod  = $variation_id ? wc_get_product( $variation_id ) : null;
		$qty            = wc_stock_amount( $quantity );
		$product_status = get_post_status( $product_id );

		// Bail if no product or invalid quantity
		if ( ! $product || 'publish' !== $product_status ) {
			wc_add_notice( __( 'This product is no longer available. Please select an alternative.', 'woocommerce-bulk-variations' ), 'error' );
			return false;
		} elseif ( ! $qty ) {
			wc_add_notice( __( 'Please enter a quantity greater than 0.', 'woocommerce-bulk-variations' ), 'error' );
			return false;
		}

		$result = false;

		if ( $variable_prod ) {

			$attributes = $variable_prod->get_variation_attributes();

			foreach ( $attributes as $a_k => $a_v ) {

				if ( ! ( $a_v instanceof \WC_Product_Attribute ) ) {

					$variations[ $a_k ] = $a_v;
				}
			}
		}

		if ( $variation_id ) {

			if ( apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $variations ) ) {
				if ( false !== WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $variations ) ) {
					$result = true;
				}
			} else {
				wc_add_notice( __( 'This product cannot be purchased.', 'woocommerce-bulk-variations' ), 'error' );
			}
		}

		return $result;
	}
}
