<?php

namespace Barn2\Plugin\WC_Bulk_Variations;

use Barn2\Plugin\WC_Bulk_Variations\Util\Util;

/**
 * Responsible for managing the product table columns.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Columns {

	/**
	 * @var Args The table args.
	 */
	public $args;

	public function __construct( Args $args ) {
		$this->args = $args;
	}

	public function get_all_columns() {
		return array_merge( $this->get_base_columns(), $this->get_columns(), $this->get_hidden_columns() );
	}

	public function get_base_columns() {
		return $this->args->base_columns;
	}

	public function get_columns() {
		return $this->args->attribute_columns;
	}

	public function get_hidden_columns() {
		$hidden = [];

		return $hidden;
	}

	public function column_index( $column, $incude_hidden = false ) {
		$cols  = $incude_hidden ? $this->get_all_columns() : $this->get_columns();
		$index = array_search( $column, $cols, true );
		$index = is_int( $index ) ? $index : false; // sanity check

		return $index;
	}

	public function column_indexes( $columns, $include_hidden = false ) {
		return array_map( [ $this, 'column_index' ], $columns, array_fill( 0, count( $columns ), $include_hidden ) );
	}

	public function get_column_header_class( $index, $column ) {
		$class = [ self::get_column_class( $column ) ];

		return implode( ' ', apply_filters( 'wc_bulk_variations_table_column_class_' . self::unprefix_column( $column ), $class ) );
	}

	public function get_column_heading( $index, $column ) {
		$heading        = '';
		$unprefixed_col = self::unprefix_column( $column );

		$att = $this->get_product_attribute( $column );
		if ( $att ) {
			$heading = Util::get_attribute_label( $att );
		} else {
			$heading = trim( str_replace( [ '_', '-' ], ' ', $unprefixed_col ) );
			if ( substr( $unprefixed_col, 0, 1 ) === '-' ) {
				$heading = '-' . $heading;
			}
		}

		$heading = apply_filters( 'wc_bulk_variations_table_column_heading_' . $unprefixed_col, $heading );

		return $heading;
	}

	public function is_product_attribute( $column ) {

		if ( ( $this->args->attribute_columns && in_array( $column, $this->args->attribute_columns, true ) ) || ( $this->args->attribute_rows && in_array( $column, $this->args->attribute_rows, true ) ) ) {
			return true;
		}

		return false;
	}

	public function get_product_attribute( $column ) {
		if ( $this->is_product_attribute( $column ) ) {
			return $column;
		}
		return false;
	}

	public static function unprefix_column( $column ) {
		$str = strstr( $column, ':' );
		if ( false !== $str ) {
			$column = substr( $str, 1 );
		}
		return $column;
	}

	public static function get_column_class( $column ) {
		return Util::sanitize_class_name( 'col-' . self::unprefix_column( $column ) );
	}

	public static function get_column_data_source( $column ) {
		// '.' not allowed in data source
		return str_replace( '.', '', $column );
	}
}
