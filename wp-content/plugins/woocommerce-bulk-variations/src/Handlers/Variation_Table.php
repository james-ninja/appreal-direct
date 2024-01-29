<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Handlers;

use Barn2\Plugin\WC_Bulk_Variations\Args,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
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
class Variation_Table implements Registerable, Service {

	public function register() {
		// Remove not needed product fields
		add_action( 'wp', [ __CLASS__, 'remove_product_fields' ] );
	}

	public static function remove_product_fields() {

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
					add_action( 'woocommerce_before_single_product', [ __CLASS__, 'hook_into_summary_actions' ], 1 );
				}
			} else {
				$variations_data = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_DATA, true );

				if ( isset( $variations_data['hide_add_to_cart'] ) && $variations_data['hide_add_to_cart'] ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				}
			}
		}

	}

	public static function hook_into_summary_actions() {

		if ( has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' ) ) {
			$location = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', $location );
			add_action( 'woocommerce_single_product_summary', [ __CLASS__, 'print_variation_table' ], 21 );
		} elseif ( has_action( 'woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart' ) ) {
			$location = has_action( 'woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart' );
			remove_action( 'woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', $location );
			add_action( 'woocommerce_single_product_summary_single_add_to_cart', [ __CLASS__, 'print_variation_table' ] );
		} else {
			remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			add_action( 'woocommerce_variable_add_to_cart', [ __CLASS__, 'print_variation_table' ], 30 );
		}

	}

	public static function print_variation_table() {

		global $product;
		$product_id = $product ? $product->get_id() : 0;
		if ( $product_id ) {

			$variations_data      = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_DATA, true );
			$variations_structure = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_STRUCTURE, true );

			$atts = [ 'include' => $product_id ];

			if ( isset( $variations_data['variation_images'] ) ) {
				$images                   = $variations_data['variation_images'] ? true : false;
				$atts['variation_images'] = $images;
			}
			if ( isset( $variations_structure['rows'] ) ) {
				$rows         = str_replace( 'pa_', '', $variations_structure['rows'] );
				$atts['rows'] = $rows;
			}
			if ( isset( $variations_structure['columns'] ) ) {
				$columns         = str_replace( 'pa_', '', $variations_structure['columns'] );
				$atts['columns'] = $columns;
			}
			if ( isset( $variations_data['disable_purchasing'] ) ) {
				$disable                    = $variations_data['disable_purchasing'] ? true : false;
				$atts['disable_purchasing'] = $disable;
			}
			if ( isset( $variations_data['use_lightbox'] ) ) {
				$lightbox             = $variations_data['use_lightbox'] ? true : false;
				$atts['use_lightbox'] = $lightbox;
			}
			if ( isset( $variations_data['show_stock'] ) ) {
				$disable            = $variations_data['show_stock'] ? true : false;
				$atts['show_stock'] = $disable;
			}
			//custom mt
			$atts['rows'] = 'size';
			$atts['columns'] = 'color';
			//custom mt
			// Fill-in missing attributes

			$r = shortcode_atts( Args::get_defaults(), array_filter( $atts ) );

			// Return the table as HTML
			$output = apply_filters( 'wc_bulk_variations_product_output', wc_get_bulk_variations_table( $r, $atts ) );

			echo '<div class="wc-bulk-variations-table-wrapper">' . $output . '</div>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
