<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Handlers;

use Barn2\Plugin\WC_Bulk_Variations\Args,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
	Barn2\WBV_Lib\Registerable,
	Barn2\WBV_Lib\Service;

/**
 * This class handles our bulk variations table shortcode.
 *
 * Example usage:
 *   [bulk_variations
 *       include="10"
 *       columns/horizontal="name"
 *       rows/vertical="t-shirts",
 *       images="false",
 *       lightbox="true"
 *       disable_purchasing="false"
 *       show_stock="true"]
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Shortcode implements Registerable, Service {

	const SHORTCODE = 'bulk_variations';

	public function register() {
		add_shortcode( self::SHORTCODE, [ __CLASS__, 'do_shortcode' ] );
	}

	/**
	 * Handles our product table shortcode.
	 *
	 * @param array $atts The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {

		if ( ! self::can_do_shortocde( $atts ) ) {
			return '';
		}

		// Pre process variations table args
		$atts = self::setup_product_data( $atts );

		// Fill-in missing attributes
		$r = shortcode_atts( Args::get_defaults(), $atts, self::SHORTCODE );

		// Return the table as HTML
		$output = apply_filters( 'wc_bulk_variations_shortcode_output', wc_get_bulk_variations_table( $r ) );

		return '<div class="wc-bulk-variations-table-wrapper">' . $output . '</div>';
	}

	private static function setup_product_data( $atts ) {

		// Handle horizontal and vertical attributes
		if ( ! isset( $atts['columns'] ) && isset( $atts['horizontal'] ) ) {
			$atts['columns'] = $atts['horizontal'];
		}
		if ( ! isset( $atts['rows'] ) && isset( $atts['vertical'] ) ) {
			$atts['rows'] = $atts['vertical'];
		}

		$product_id  = isset( $atts['include'] ) ? $atts['include'] : 0;
		$product_obj = wc_get_product( $product_id );
		if ( $product_obj ) {

			$variations_data = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_DATA, true );
			$structure_data  = get_post_meta( $product_id, Settings::OPTION_VARIATIONS_STRUCTURE, true );

			if ( isset( $variations_data['variation_images'] ) && ! isset( $atts['variation_images'] ) && ! isset( $atts['images'] ) ) {
				$images                   = $variations_data['variation_images'] ? true : false;
				$atts['variation_images'] = $images;
			}
			if ( isset( $structure_data['rows'] ) && ! isset( $atts['rows'] ) ) {
				$rows         = str_replace( 'pa_', '', $structure_data['rows'] );
				$atts['rows'] = $rows;
			}
			if ( isset( $structure_data['columns'] ) && ! isset( $atts['columns'] ) ) {
				$columns         = str_replace( 'pa_', '', $structure_data['columns'] );
				$atts['columns'] = $columns;
			}
			if ( isset( $variations_data['disable_purchasing'] ) && ! isset( $atts['disable_purchasing'] ) ) {
				$disable                    = $variations_data['disable_purchasing'] ? true : false;
				$atts['disable_purchasing'] = $disable;
			}
			if ( isset( $variations_data['use_lightbox'] ) && ! isset( $atts['lightbox'] ) ) {
				$disable          = $variations_data['use_lightbox'] ? true : false;
				$atts['lightbox'] = $disable;
			}
			if ( isset( $variations_data['show_stock'] ) && ! isset( $atts['stock'] ) ) {
				$disable       = $variations_data['show_stock'] ? true : false;
				$atts['stock'] = $disable;
			}
		}

		// Typecast true and false to boolean
		foreach ( $atts as $k_a => $v_a ) {
			if ( $v_a === 'true' ) {
				$atts[ $k_a ] = true;
			} elseif ( $v_a === 'false' ) {
				$atts[ $k_a ] = false;
			}
		}

		// Allow both images and variation_images as attributes
		if ( isset( $atts['images'] ) && ! isset( $atts['variation_images'] ) ) {
			$atts['variation_images'] = $atts['images'];
		}
		if ( isset( $atts['lightbox'] ) && ! isset( $atts['use_lightbox'] ) ) {
			$atts['use_lightbox'] = $atts['lightbox'];
		}
		if ( isset( $atts['stock'] ) && ! isset( $atts['show_stock'] ) ) {
			$atts['show_stock'] = $atts['stock'];
		}

		$settings = get_option( 'wcbvp_variations_data' );

		if ( ! isset( $atts['disable_purchasing'] ) ) {
			$atts['disable_purchasing'] = ( isset( $settings['disable_purchasing'] ) && $settings['disable_purchasing'] === 'yes' ) ? true : false;
		}
		if ( ! isset( $atts['show_stock'] ) ) {
			$atts['show_stock'] = ( isset( $settings['show_stock'] ) && $settings['show_stock'] === 'yes' ) ? true : false;
		}
		if ( ! isset( $atts['variation_images'] ) ) {
			$atts['variation_images'] = ( isset( $settings['variation_images'] ) && $settings['variation_images'] === 'yes' ) ? true : false;
		} else {
			$atts['variation_images'] = ( true === $atts['variation_images'] || $atts['variation_images'] === 'true' ) ? true : false;
		}
		if ( ! isset( $atts['use_lightbox'] ) ) {
			$atts['use_lightbox'] = ( isset( $settings['use_lightbox'] ) && $settings['use_lightbox'] === 'yes' ) ? true : false;
		} else {
			$atts['use_lightbox'] = ( $atts['use_lightbox'] === 'true' ) ? true : false;
		}

		return $atts;
	}

	private static function can_do_shortocde( $atts ) {

		$product_id  = isset( $atts['include'] ) ? $atts['include'] : 0;
		$product_obj = wc_get_product( $product_id );
		if ( $product_obj ) {

			$attributes = $product_obj->get_variation_attributes();
			if ( count( $attributes ) > 0 && count( $attributes ) <= 2 ) {
				return true;
			}
		}

		return false;
	}
}
// class WC_Product_Table_Shortcode
