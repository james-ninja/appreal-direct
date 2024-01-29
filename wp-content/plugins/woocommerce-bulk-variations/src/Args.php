<?php

namespace Barn2\Plugin\WC_Bulk_Variations;

use Barn2\Plugin\WC_Bulk_Variations\Util\Settings;

/**
 * Responsible for storing and validating the product variations arguments.
 * Parses an array of args into the corresponding properties.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Args {

	private $args = [];

	public $include;
	public $attribute_columns;
	public $columns;
	public $base_columns;
	public $attribute_column;
	public $attribute_matrix;
	public $attribute_rows;
	public $attribute_labels;
	public $rows;
	public $enable;
	public $disable_purchasing;
	public $use_lightbox;
	public $images;
	public $variation_attribute;
	public $variation_images;
	public $single_attribute;
	public $has_images;

	/**
	 * @var array The default table parameters
	 */
	public static $default_args = [
		'include'             => 0,
		'attribute_columns'   => [],
		'columns'             => '',
		'base_columns'        => [],
		'attribute_column'    => '',
		'attribute_matrix'    => [],
		'attribute_rows'      => [],
		'attribute_labels'    => [],
		'rows'                => '',
		'enable'              => false,
		'disable_purchasing'  => false,
		'show_stock'          => false,
		'images'              => false,
		'variation_attribute' => 'vert',
		'variation_images'    => false,
		'use_lightbox'        => true,
		'single_attribute'    => false,
		'has_images'          => false,
	];

	public function __construct( array $args = [] ) {
		$this->set_args( $args );
	}

	public function get_args() {
		return $this->args;
	}

	public function set_args( array $args ) {

		// Update args
		$this->args = array_merge( $this->args, $args );

		// Parse/validate args & update properties
		$this->parse_args( $this->args );
	}

	private function parse_args( array $args ) {

		$defaults = self::get_defaults();

		// Merge in default args.
		$args = wp_parse_args( $args, $defaults );

		$args = $this->set_settings( $args );

		foreach ( $args as $arg_k => $arg_v ) {

			$this->$arg_k = $arg_v;
		}
	}

	public function set_settings( $args ) {

		$settings = get_option( 'wcbvp_variations_data' );

		if ( $args['rows'] && ! $args['columns'] ) {
			$args['variation_attribute'] = 'vert';
			$args['single_attribute']    = true;
		} elseif ( ! $args['rows'] && $args['columns'] ) {
			$args['variation_attribute'] = '';
			$args['single_attribute']    = true;
		} else {
			$args['variation_attribute'] = ( isset( $settings['variation_attribute'] ) ) ? $settings['variation_attribute'] : '';
		}

		$args = $this->set_columns( $args );

		return $args;
	}

	public function set_columns( $args ) {

		$product_id       = $args['include'];
		$hide_outof_stock = get_option( 'woocommerce_hide_out_of_stock_items', 'no' );
		$product_obj      = wc_get_product( $product_id );
		$children         = $product_obj->get_children();
		$attributes       = [];
		$attribute_lbl    = '';
		$attributes_data  = $product_obj->get_attributes();
		$attributes_lbls  = [];

		$columns_attr = '';
		$rows_attr    = '';

		foreach ( $children as $child ) {

			$child_obj = wc_get_product( $child );
			$price     = $child_obj->get_price();
			$stock     = $child_obj->get_stock_quantity();
			$image_id  = $child_obj->get_image_id();

			if ( ( $child_obj->is_in_stock() || $hide_outof_stock === 'no' ) && is_numeric( $price ) ) {
				$variation_attributes = $child_obj->get_variation_attributes();
				foreach ( $variation_attributes as $k_vas => $v_vas ) {
					if ( ! isset( $attributes[ $k_vas ] ) || ! in_array( $v_vas, $attributes[ $k_vas ], true ) ) {
						$attributes[ $k_vas ][] = $v_vas;
					}
				}
			}

			if ( $image_id ) {
				$args['has_images'] = true;
			}
		}

		foreach ( $attributes_data as $attribute_key => $attribute_data ) {

			$attribute_name = $attribute_data->get_name();

			if ( strpos( $attribute_key, 'pa_' ) !== false ) {

				$product_tax = get_taxonomy( $attribute_key );

				if ( $product_tax && isset( $product_tax->labels->singular_name ) && $product_tax->labels->singular_name ) {
					$attribute_name = $product_tax->labels->singular_name;
				}
			}

			$attributes_lbls[ $attribute_key ] = $attribute_name;
		}

		$args['attribute_labels'] = $attributes_lbls;

		foreach ( $attributes as $k_att => $v_att ) {

			$k_att_f = str_replace( 'attribute_', '', $k_att );
			$k_att_t = str_replace( 'pa_', '', $k_att_f );

			if ( isset( $attributes_data[ $k_att_t ] ) && $attributes_data[ $k_att_t ] && preg_match( '/^(?:%[0-9A-Fa-f]{2})+$/', $k_att_t, $matches ) ) {
				$k_att_f = $attributes_data[ $k_att_t ]->get_name();
				$k_att_t = $attributes_data[ $k_att_t ]->get_name();
			}

			if ( $k_att_t === strtolower( $args['columns'] ) ) {
				$columns_attr = strtolower( $k_att_f );
			} elseif ( $k_att_t === strtolower( $args['rows'] ) ) {
				$rows_attr = strtolower( $k_att_f );
			}
		}

		foreach ( $attributes as $k_att => $v_att ) {

			$k_att_f = str_replace( 'attribute_', '', $k_att );

			if ( isset( $attributes_data[ $k_att_f ] ) && $attributes_data[ $k_att_f ] && preg_match( '/^(?:%[0-9A-Fa-f]{2})+$/', $k_att_f, $matches ) ) {
				$k_att_f = $attributes_data[ $k_att_f ]->get_name();
			}

			if ( ! $columns_attr ) {
				$columns_attr = strtolower( $k_att_f );
			} elseif ( ! $rows_attr ) {
				$rows_attr = strtolower( $k_att_f );
			}
		}

		$args['single_attribute'] = ( count( $attributes ) < 2 ) ? true : false;

		if ( ! $args['single_attribute'] ) {

			$attribute_lbl .= "$columns_attr / $rows_attr";

			foreach ( $children as $child ) {

				$child_obj            = wc_get_product( $child );
				$stock                = $child_obj->get_stock_quantity();
				$variation_attributes = $child_obj->get_variation_attributes();
				$price                = $child_obj->get_price();
				$defined_attributes   = 0;

				foreach ( $variation_attributes as $attribute_key => $attribute_value ) {
					if ( $attribute_value || is_numeric( $attribute_value ) ) {
						$defined_attributes++;
					}
				}

				$column_val = '';
				$row_val    = '';

				if ( ( $child_obj->is_in_stock() || $hide_outof_stock === 'no' ) && is_numeric( $price ) && $defined_attributes === 2 && $child_obj->is_purchasable() ) {
					if ( $variation_attributes ) {
						foreach ( $variation_attributes as $attribute_key => $attribute_value ) {

							$attribute_key = str_replace( 'attribute_', '', $attribute_key );
							$attribute_key = strtolower( $attribute_key );

							if ( isset( $attributes_data[ $attribute_key ] ) && $attributes_data[ $attribute_key ] && preg_match( '/^(?:%[0-9A-Fa-f]{2})+$/', $attribute_key, $matches ) ) {
								$attribute_key = $attributes_data[ $attribute_key ]->get_name();
							}

							if ( $attribute_key === $columns_attr ) {
								$column_val = $attribute_value;
								if ( ! in_array( $attribute_value, $args['attribute_columns'], true ) ) {
									$args['attribute_columns'][] = $attribute_value;
								}
							} elseif ( $attribute_key === $rows_attr ) {
								$row_val = $attribute_value;
								if ( ! in_array( $attribute_value, $args['attribute_rows'], true ) ) {
									$args['attribute_rows'][] = $attribute_value;
								}
							}
						}
					}

					$args['attribute_matrix'][ $column_val ][ $row_val ] = $child;
				}
			}

			$args['base_columns'][]   = '';
			$args['base_columns'][]   = $attribute_lbl;
			$args['attribute_column'] = $attribute_lbl;
		} elseif ( $args['single_attribute'] && ! $args['variation_attribute'] ) {

			$attribute_lbl .= "$columns_attr";

			foreach ( $children as $child ) {

				$child_obj            = wc_get_product( $child );
				$stock                = $child_obj->get_stock_quantity();
				$variation_attributes = $child_obj->get_variation_attributes();
				$price                = $child_obj->get_price();

				$defined_attributes = 0;

				foreach ( $variation_attributes as $attribute_key => $attribute_value ) {
					if ( $attribute_value || is_numeric( $attribute_value ) ) {
						$defined_attributes++;
					}
				}

				$column_val = '';
				$row_val    = '';

				if ( ( $child_obj->is_in_stock() || $hide_outof_stock === 'no' ) && is_numeric( $price ) && $defined_attributes === 1 && $child_obj->is_purchasable() ) {
					if ( $variation_attributes ) {

						foreach ( $variation_attributes as $attribute_key => $attribute_value ) {

							$attribute_key = str_replace( 'attribute_', '', $attribute_key );
							$attribute_key = strtolower( $attribute_key );

							if ( isset( $attributes_data[ $attribute_key ] ) && $attributes_data[ $attribute_key ] && preg_match( '/^(?:%[0-9A-Fa-f]{2})+$/', $attribute_key, $matches ) ) {
								$attribute_key = $attributes_data[ $attribute_key ]->get_name();
							}

							if ( $attribute_key === $columns_attr ) {
								$column_val = $attribute_value;
								if ( ! in_array( $attribute_value, $args['attribute_columns'], true ) ) {
									$args['attribute_columns'][] = $attribute_value;
								}
							}
						}
					}

					$args['attribute_matrix'][ $column_val ][ $row_val ] = $child;
				}
			}

			$args['base_columns'][]   = $attribute_lbl;
			$args['attribute_column'] = $attribute_lbl;
		} else {
			$attribute_lbl .= "$columns_attr";

			foreach ( $children as $child ) {
				$child_obj            = wc_get_product( $child );
				$stock                = $child_obj->get_stock_quantity();
				$variation_attributes = $child_obj->get_variation_attributes();
				$price                = $child_obj->get_price();

				$defined_attributes = 0;

				foreach ( $variation_attributes as $attribute_key => $attribute_value ) {
					if ( $attribute_value || is_numeric( $attribute_value ) ) {
						$defined_attributes++;
					}
				}

				$column_val = '';
				$row_val    = '';

				if ( ( $child_obj->is_in_stock() || $hide_outof_stock === 'no' ) && is_numeric( $price ) && $defined_attributes === 1 && $child_obj->is_purchasable() ) {
					if ( $variation_attributes ) {
						foreach ( $variation_attributes as $attribute_key => $attribute_value ) {
							$attribute_key = str_replace( 'attribute_', '', $attribute_key );
							$attribute_key = strtolower( $attribute_key );

							if ( isset( $attributes_data[ $attribute_key ] ) && $attributes_data[ $attribute_key ] && preg_match( '/^(?:%[0-9A-Fa-f]{2})+$/', $attribute_key, $matches ) ) {
								$attribute_key = $attributes_data[ $attribute_key ]->get_name();
							}

							if ( $attribute_key === $columns_attr ) {
								$column_val = $attribute_value;

								if ( ! in_array( $attribute_value, $args['attribute_rows'], true ) ) {
									$args['attribute_rows'][] = $attribute_value;
								}
							}
						}
					}

					$args['attribute_matrix'][ $column_val ][ $row_val ] = $child;
				}
			}

			$args['attribute_columns']['price'] = __( 'Price', 'woocommerce' );
			$args['base_columns'][]             = $attribute_lbl;
			$args['attribute_column']           = $attribute_lbl;
		}

		$args['attribute_rows']    = $this->sort_attributes( $args['attribute_rows'], $product_obj );
		$args['attribute_columns'] = $this->sort_attributes( $args['attribute_columns'], $product_obj );

		return $args;
	}

	public function sort_attributes( $attributes, $product ) {

		$attributes_data = $product->get_attributes();
		foreach ( $attributes_data as $attribute_data ) {

			$options = $attribute_data->get_options();
			$exists  = true;
			foreach ( $attributes as $attribute ) {
				if ( ! in_array( $attribute, $options, true ) ) {

					$exists = false;
					break;
				}
			}

			if ( $exists ) {

				$new_attributes = [];
				foreach ( $attributes as $attribute ) {
					if ( in_array( $attribute, $options, true ) ) {
						$new_key                    = array_search( $attribute, $options, true );
						$new_attributes[ $new_key ] = $attribute;
					}
				}

				if ( $new_attributes ) {
					ksort( $new_attributes );
					return $new_attributes;
				}
			}
		}

		return $attributes;
	}

	public static function get_defaults() {

		$args = wp_parse_args( Settings::settings_to_variations_data_args( Settings::get_setting_variations_defaults() ), self::$default_args );

		return $args;
	}
}
