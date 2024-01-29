<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Integration;

use Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
	Barn2\WBV_Lib\Registerable,
	Barn2\WBV_Lib\Service;

/**
 * This class handles our bulk variations table on the product page.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Theme_Compat implements Registerable, Service {

	public function register() {

		// Add compatibility with shopkeeper theme
		add_action( 'woocommerce_single_product_summary_single_add_to_cart', [ __CLASS__, 'shopkeeper_compat' ] );
	}

	public static function shopkeeper_compat() {

		global $post;
		if ( $post && $post->post_type === 'product' ) {

			$product_id  = $post->ID;
			$product_obj = wc_get_product( $product_id );

			$settings = get_option( Settings::OPTION_VARIATIONS_DATA, false );
			$override = ( isset( $settings['enable'] ) && $settings['enable'] === 'yes' ) ? $settings['enable'] : false;

			if ( metadata_exists( 'post', $product_id, Settings::OPTION_VARIATIONS_DATA . '_override' ) ) {
				$override = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_DATA . '_override', true );
			}

			if ( $override ) {
				$attributes_count = 0;

				if ( $product_obj instanceof \WC_Product_Variable ) {
					$attributes       = $product_obj->get_variation_attributes();
					$attributes_count = count( $attributes );
				}

				if ( $attributes_count && $attributes_count <= 2 ) {
					// Add compatibility with shopkeeper theme
					remove_action( 'woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30 );
					remove_action( 'woocommerce_single_product_summary_single_meta', 'woocommerce_template_single_meta', 40 );
					remove_action( 'woocommerce_single_product_summary_single_sharing', 'woocommerce_template_single_sharing', 50 );
				}
			} else {
				$variations_data = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_DATA, true );

				if ( isset( $variations_data['hide_add_to_cart'] ) && $variations_data['hide_add_to_cart'] ) {
					// Add compatibility with shopkeeper theme
					remove_action( 'woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30 );
					remove_action( 'woocommerce_single_product_summary_single_meta', 'woocommerce_template_single_meta', 40 );
					remove_action( 'woocommerce_single_product_summary_single_sharing', 'woocommerce_template_single_sharing', 50 );
				}
			}
		}
	}
}
