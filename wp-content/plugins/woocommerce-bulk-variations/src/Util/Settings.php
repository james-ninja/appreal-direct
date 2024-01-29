<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Util;

/**
 * Utility functions for the product table plugin settings.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Settings {
	/* Option names for our plugin settings (i.e. the option keys used in wp_options) */

	const OPTION_VARIATIONS_DATA      = 'wcbvp_variations_data';
	const OPTION_VARIATIONS_STRUCTURE = 'wcbvp_variations_structure';

	/* The section name within the main WooCommerce Settings */
	const SECTION_SLUG = 'bulk-variations';

	public static function get_setting_variations_defaults() {
		return self::get_setting( self::OPTION_VARIATIONS_DATA, [] );
	}

	public static function bulk_args_to_settings( $args ) {
		if ( empty( $args ) ) {
			return $args;
		}

		foreach ( $args as $key => $value ) {
			if ( is_bool( $value ) ) {
				$args[ $key ] = $value ? 'yes' : 'no';
			}
		}

		return $args;
	}

	public static function get_setting( $option_name, $default = [] ) {

		$option_value = get_option( $option_name, $default );

		if ( is_array( $option_value ) ) {
			// Merge with defaults.
			if ( is_array( $default ) ) {
				$option_value = wp_parse_args( $option_value, $default );
			}

			// Convert 'yes'/'no' options to booleans.
			$option_value = array_map( [ __CLASS__, 'array_map_yes_no_to_boolean' ], $option_value );
		}

		return $option_value;
	}

	private static function array_map_yes_no_to_boolean( $val ) {
		if ( 'yes' === $val || 1 === $val ) {
			return true;
		} elseif ( 'no' === $val || 0 === $val ) {
			return false;
		}

		return $val;
	}

	public static function settings_to_variations_data_args( $settings ) {
		return $settings;
	}

}
