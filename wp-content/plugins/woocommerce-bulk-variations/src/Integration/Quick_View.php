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
class Quick_View implements Registerable, Service {

	public function register() {

		// Integrate with WooCommerce Quick View Pro
		add_action( 'wc_quick_view_pro_before_quick_view', [ __CLASS__, 'integrate_quick_view' ], 9999 );
	}

	public static function integrate_quick_view() {
		global $post;
		if ( $post && $post->post_type === 'product' ) {

			$product_id  = $post->ID;
			$product_obj = wc_get_product( $product_id );
			if ( $product_obj && $product_obj instanceof \WC_Product_Variable ) {

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

						remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_price', 10 );
						remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_add_to_cart', 30 );
						add_action( 'wc_quick_view_pro_quick_view_product_details', [ 'Barn2\Plugin\WC_Bulk_Variations\Handlers\Variation_Table', 'print_variation_table' ], 9 );
					}
				} else {
					$variations_data = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_DATA, true );

					if ( isset( $variations_data['hide_add_to_cart'] ) && $variations_data['hide_add_to_cart'] ) {
						remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_add_to_cart', 30 );
					}
				}
			}
		}
	}
}
