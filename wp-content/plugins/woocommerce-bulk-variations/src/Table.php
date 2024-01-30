<?php

namespace Barn2\Plugin\WC_Bulk_Variations;

use Barn2\Plugin\WC_Bulk_Variations\Util\Util,
	Barn2\Plugin\WC_Bulk_Variations\Data\Data_Factory;

/**
 * The main Table class.
 *
 * Responsible for creating the products table from the specified args and returning the
 * complete table.
 *
 * The main functions provided are get_table() and get_data().
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table {

	public $id;

	/**
	 * Helper classes
	 * phpcs:ignore Squiz.Commenting.VariableComment.MissingVar
	 */
	public $args;
	public $data_table;
	private $columns;
	private $data_factory;

	/**
	 * Internal flags
	 * phpcs:ignore Squiz.Commenting.VariableComment.MissingVar
	 */
	private $table_initialised = false;
	private $data_added        = false;

	const CONTROL_COLUMN_DATA_SOURCE = 'control';

	public function __construct( $id, $args = [] ) {

		$this->args         = new Args( $args );
		$this->data_factory = new Data_Factory( $this->args, $id );
		$this->id           = $id;
		$this->columns      = new Table_Columns( $this->args );
		$this->data_table   = new \Html_Data_Table();
	}

	public function should_show_table() {

		$product_id = isset( $this->args->include ) ? $this->args->include : 0;
		if ( $product_id ) {

			$product_obj = wc_get_product( $product_id );
			if ( $product_obj ) {
				return true;
			}
		}
		return false;
	}

	public function get_add_to_cart_text() {

		$product_id = isset( $this->args->include ) ? $this->args->include : 0;
		if ( $product_id ) {

			$product_obj = wc_get_product( $product_id );
			if ( $product_obj ) {

				$text = $product_obj->single_add_to_cart_text();
				return $text;
			}
		}

		return __( 'Add to cart', 'woocommerce' );
	}

	public function get_table( $output = 'object' ) {
		if ( ! $this->should_show_table() ) {
			return '';
		}

		if ( ! $this->table_initialised ) {
			// Add attriutes and table headings.
			$this->add_attributes();
			$this->add_headings();

			// Fetch the data.
			$this->fetch_data();

			do_action( 'wc_bulk_variations_table_after_get_table', $this );

			$this->table_initialised = true;
		}

		$result = $this->data_table;

		if ( 'html' === $output ) {
			$result = $this->data_table->to_html();
		} elseif ( 'array' === $output ) {
			$result = $this->data_table->to_array();
		} elseif ( 'json' === $output ) {
			$result = $this->data_table->to_json();
		}

		$result .= $this->add_footer();

		return apply_filters( 'wc_bulk_variations_get_table_output', $result, $output, $this );
	}

	private function add_headings() {

		// Add column headings
		foreach ( $this->columns->get_all_columns() as $i => $column ) {

			if ( $this->args->attribute_column === $column && $this->args->single_attribute && ! $this->args->variation_attribute ) {
				continue;
			} elseif ( ! $this->args->single_attribute && '' === $column && ( ! $this->args->variation_images || ! $this->args->has_images ) ) {
				continue;
			}

			$data_source = Table_Columns::get_column_data_source( $column );

			$column_atts = [
				'class' => $this->columns->get_column_header_class( $i, $column ),
			];

			if ( $this->args->attribute_column === $column && ! $this->args->single_attribute ) {
				$attribute_column_arr = explode( ' / ', $column );

				if ( $attribute_column_arr && isset( $attribute_column_arr[0] ) && isset( $attribute_column_arr[1] ) && $attribute_column_arr[0] && $attribute_column_arr[1] ) {
					$first_attr  = $attribute_column_arr[0];
					$second_attr = $attribute_column_arr[1];

					if ( isset( $this->args->attribute_labels[ $first_attr ] ) && $this->args->attribute_labels[ $first_attr ] ) {
						$first_attr = $this->args->attribute_labels[ $first_attr ];
					}

					if ( isset( $this->args->attribute_labels[ $second_attr ] ) && $this->args->attribute_labels[ $second_attr ] ) {
						$second_attr = $this->args->attribute_labels[ $second_attr ];
					}

					$column = "$first_attr / $second_attr";
				}
			} elseif ( isset( $this->args->attribute_labels[ $column ] ) && $this->args->attribute_labels[ $column ] ) {
				$column = $this->args->attribute_labels[ $column ];
			}

			$column = str_replace( 'pa_', '', $column );

			$this->add_heading( $this->columns->get_column_heading( $i, $column ), $column_atts, $data_source );
		}
	}

	private function fetch_data() {
		if ( $this->data_added || ! $this->can_fetch_data() ) {
			return;
		}

		// Reset the table data
		$this->data_table->reset_data();

		do_action( 'wc_bulk_variations_table_before_get_data', $this );

		// Add all rows to the table.
		$this->add_rows_to_table();

		do_action( 'wc_bulk_variations_table_after_get_data', $this );

		$this->data_added = true;
	}

	/**
	 * Add the products (array of post objects) to the table.
	 */
	private function add_rows_to_table() {

		// To make sure the post and product globals are reset, we store them here and set it back after our product loop.
		$old_global_post    = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : false;
		$old_global_product = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : false;

		// Get required columns to walk through
		$cols = $this->columns->get_all_columns();

		if ( ! empty( $cols ) ) {
			if ( $this->args->attribute_column === $cols[0] && $this->args->single_attribute && ! $this->args->variation_attribute ) {
				unset( $cols[0] );
			} elseif ( ! $this->args->single_attribute && '' === $cols[0] && ( ! $this->args->variation_images || ! $this->args->has_images ) ) {
				unset( $cols[0] );
			}
		}

		//custom mt
		$rows1 = $this->args->attribute_rows;
        
		$termidArray = array();
		foreach($rows1 as $colum_value){
			$term = get_term_by( 'slug', $colum_value, esc_attr( str_replace( 'attribute_', '', 'pa_size' ) ) );
			$termidArray[] = $term->term_id;
		}
		$get_terms_size_order = get_terms(array (
            'taxonomy' => 'pa_size', 
            'meta_key'   => 'order',
  			'orderby'    => 'meta_value_num',
            'hide_empty' => false,
            'include' => $termidArray,
            'fields' => 'slugs'
    	));
        $rows2 = $get_terms_size_order;
        
        $rowsnew = array_merge($rows1,$rows2);
        $rows = array_unique($rowsnew);
        //custom mt
		if ( $rows ) {
			foreach ( $rows as $row ) {

				$this->data_table->new_row( $this->get_row_attributes( $row ) );

				// Add the data for this product
				array_walk( $cols, [ $this, 'add_row_data' ], $row );
			}
		} else {
			if ( $this->args->single_attribute && ! $this->args->variation_attribute ) {
				if ( $this->args->variation_images || $this->args->has_images ) {
					$this->data_table->new_row( $this->get_row_attributes( 'variation-images' ) );
					array_walk( $cols, [ $this, 'add_row_data' ], 'variation-images' );
				}

				$this->data_table->new_row( $this->get_row_attributes( 'single-attribute' ) );

				// Add the data for this product
				array_walk( $cols, [ $this, 'add_row_data' ], '' );
			}
		}
	}

	private function add_footer() {

		$footer = '';

		if ( ! $this->args->disable_purchasing ) {
			$footer = '<form class="wcbvp-cart" method="post" enctype="multipart/form-data">';

			$decimals    = wc_get_price_decimals();
			$d_separator = wc_get_price_decimal_separator();

			$decimal_nbr = '';

			for ( $i = 0; $i < $decimals; $i++ ) {
				$decimal_nbr .= '0';
			}

			$price            = "0{$d_separator}$decimal_nbr";
			$base             = "<span data-table_id='{$this->id}' class='wcbvp_total_price'>$price</span>";
			$currency         = Util::set_wc_price( $base );
			$add_to_cart_text = $this->get_add_to_cart_text();

			//$footer .= "<div id='wcbvp_wrapper_{$this->id}' class='wcbvp-total-wrapper'><div class='wcbvp-total-left'>" . __( 'Items', 'woocommerce-bulk-variations' ) . ": <span data-table_id='{$this->id}' data-table_id='{$this->id}' class='wcbvp_total_quantity'>0</span><br />" . __( 'Total', 'woocommerce-bulk-variations' ) . ": $currency</div><div class='wcbvp-total-right'><button disabled class='single_add_to_cart_button button alt disabled wc-variation-selection-needed'>$add_to_cart_text</button></div><div style='clear: both;'></div><input type='hidden' name='multiple-add-to-cart' value=''></div>";
			//custom mt
			$footer .= "<div id='wcbvp_wrapper_{$this->id}' class='wcbvp-total-wrapper'><div class='wcbvp-total-left'><span class='single_selected_item selected_items_total'>" . __( 'Items', 'woocommerce-bulk-variations' ) . ": <span data-table_id='{$this->id}' data-table_id='{$this->id}' class='wcbvp_total_quantity'>0</span><i class='fas fa-chevron-down' aria-hidden='true'></i></span><br />" . __( 'Total', 'woocommerce-bulk-variations' ) . ": $currency</div><div id='wcbvp_add_to_cart' class='wcbvp-total-right'><button class='single_add_to_cart_button button alt disabled wc-variation-selection-needed'>$add_to_cart_text</button></div><div style='clear: both;'></div><input type='hidden' name='multiple-add-to-cart' value=''></div>";
			if(!is_user_logged_in()) {
				$footer = '';
			}
			//custom mt
			$footer .= '</form>';
		}

		return $footer;
	}

	private function get_row_attributes( $row ) {
		$classes = [];

		$row_attributes = [
			'id'    => 'product-row-' . $row,
			'class' => implode( ' ', apply_filters( 'wc_bulk_variations_table_row_class', $classes ) ),
		];

		return apply_filters( 'wc_bulk_variations_table_row_attributes', $row_attributes );
	}

	private function can_fetch_data() {
		return true;
	}

	private function add_heading( $heading, $attributes, $key ) {
		$this->data_table->add_header( $heading, $attributes, $key );
	}

	private function add_row_data( $column, $key, $row ) {

		$data = '';
		$atts = false;

		$data = $this->data_factory->create( $column, $row, $this->columns );

		$this->data_table->add_data( $data, $atts, Table_Columns::get_column_data_source( $column ) );
	}

	private function add_attributes() {

		// Set table attributes.
		$table_class = trim( 'wc-bulk-variations-table nowrap' . apply_filters( 'wc_bulk_variations_table_custom_class', '', $this ) );

		$this->data_table->add_attribute( 'id', $this->id );
		$this->data_table->add_attribute( 'class', $table_class );

		// This is required otherwise tables can expand beyond their container.
		$this->data_table->add_attribute( 'width', '100%' );
	}
}
