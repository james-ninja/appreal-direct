<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Data;

use Barn2\Plugin\WC_Default_Quantity;

/**
 * Factory class to get the product table data object for a given column.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Data_Factory {

	/**
	 * The full list of table args.
	 *
	 * @var Args
	 */
	private $args;

	public function __construct( $args, $id ) {
		$this->args     = $args;
		$this->table_id = $id;
	}

	public function get_default_quantity( $product ) {

		if ( $product->is_sold_individually() ) {
			return 0;
		}

		if ( class_exists( '\Barn2\Plugin\WC_Default_Quantity\Quantity_Manager' ) && $product instanceof \WC_Product_Variation ) {

			if ( $product->get_manage_stock() ) {
				$default = Barn2\Plugin\WC_Default_Quantity\Quantity_Manager::get_default_quantity( $product->get_id() );
			} else {
				$default = Barn2\Plugin\WC_Default_Quantity\Quantity_Manager::get_default_quantity( $product->get_parent_id() );
			}
			$default = apply_filters( 'woocommerce_default_quantity_value', $default, $product );
			if ( ! is_numeric( $default ) || $default < 0 ) {
				$default = 0;
			} else {
				$default = absint( $default );
			}
			return $default;
		}

		return 0;
	}

	public function insert_image( $row ) {

		$data = '';

		if ( $this->args->use_lightbox &&
			false === has_action( 'wp_footer', 'woocommerce_photoswipe' ) &&
			false === has_action( 'wp_footer', [ 'Barn2\Plugin\WC_Bulk_Variations\Frontend_Scripts', 'load_photoswipe_template' ] ) ) {
			wp_enqueue_style( 'photoswipe-default-skin' );
			wp_enqueue_script( 'photoswipe-ui-default' );
			add_action( 'wp_footer', [ 'Barn2\Plugin\WC_Bulk_Variations\Frontend_Scripts', 'load_photoswipe_template' ] );
		}

		if ( $this->args->variation_images ) {

			$has_image  = false;
			$image_prod = $row;

			foreach ( $this->args->attribute_matrix as $attribute_key => $attribute_values ) {
				foreach ( $attribute_values as $a_k => $a_v ) {

					if ( ! $this->args->single_attribute || ( $this->args->single_attribute && ! $this->args->variation_attribute ) ) {
						if ( $a_k === $row ) {
							$image_prod = $a_v;
							$has_image  = true;
						}
					} else {
						if ( $attribute_key === $row ) {
							$image_prod = $a_v;
							$has_image  = true;
						}
					}
					if ( $has_image ) {
						break;
					}
				}
				if ( $has_image ) {
					break;
				}
			}

			if ( $image_prod ) {

				$image_prod_obj = wc_get_product( $image_prod );
				if ( $image_prod_obj ) {
					$image_id = $image_prod_obj->get_image_id();
					if ( ! $image_id ) {
						$image_parent_obj = wc_get_product( $image_prod_obj->get_parent_id() );
						if ( $image_parent_obj ) {
							$image_id = $image_parent_obj->get_image_id();
						}
					}

					if ( $image_id ) {

						$attachment_image_src = wp_get_attachment_image_src( $image_id, 'full' );
						$attachment_thumb_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );

						if ( isset( $attachment_image_src[0] ) ) {

							$image_atts = [
								'title'                   => get_post_field( 'post_title', $image_id ),
								'alt'                     => trim( wp_strip_all_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ),
								'data-caption'            => get_post_field( 'post_excerpt', $image_id ),
								'data-src'                => $attachment_image_src[0],
								'data-large_image'        => $attachment_image_src[0],
								'data-large_image_width'  => $attachment_image_src[1],
								'data-large_image_height' => $attachment_image_src[2],
								'class'                   => 'product-thumbnail product-table-image',
								'style'                   => 'max-width:64px; max-height: 64px;',
							];

							// Alt fallbacks
							$image_atts['alt'] = empty( $image_atts['alt'] ) ? $image_atts['data-caption'] : $image_atts['alt'];
							$image_atts['alt'] = empty( $image_atts['alt'] ) ? $image_atts['title'] : $image_atts['alt'];

							$data = '';

							if ( $this->args->use_lightbox ) {
								$data .= sprintf(
									'<div data-thumb="%1$s" class="product-thumbnail-wrapper woocommerce-product-gallery__image"><a href="%2$s">',
									esc_url( $attachment_thumb_src[0] ),
									esc_url( $attachment_image_src[0] )
								);
							}

							$data .= wp_get_attachment_image( $image_id, [ 64, 64, true ], false, $image_atts );

							if ( $this->args->use_lightbox ) {
								$data .= '</a></div>';
							}
						}
					}
				}
			}
		}

		return $data;
	}

	public function create( $column, $row, $column_obj ) {

		$data = '';

		if ( ! empty( $this->args->attribute_matrix ) &&
			( isset( $this->args->attribute_matrix[ $column ][ $row ] ) ) ||
			( isset( $this->args->attribute_matrix[ $row ][''] ) && $column === __( 'Price', 'woocommerce' ) ) ) {

			$product_id = $this->args->attribute_matrix[ $column ][ $row ] ?? $this->args->attribute_matrix[ $row ][''];
			$product    = wc_get_product( $product_id );
			if ( $product ) {

				$stock        = $product->get_stock_quantity();
				$max          = $stock ? $stock : '';
				$max          = $product->is_sold_individually() ? 1 : $max;
				$individual   = $product->is_sold_individually() ? 1 : 0;
				$price_html   = $product->get_price_html();
				$price        = wc_get_price_to_display( $product );
				$manage_stock = $product->get_manage_stock();
				$backorders   = $product->get_backorders();
				if ( $this->args->show_stock ) {
					$stock_tag = $this->get_stock_html( $product );
				}

				if ( is_numeric( $price ) && ( $product->is_in_stock() || ! empty( $stock_tag ) ) ) {

					$default = $this->get_default_quantity( $product );

					if ( $default > $stock && $manage_stock && $backorders === 'no' ) {
						$default = $stock;
					}

					if ( $backorders !== 'no' ) {
						$max = 9999;
					}

					$default = ( $default <= $max || ! $max ) ? $default : $max;
					$default = max( 0, $default );

					if ( ! $max ) {
						$max = 9999;
					}

					$attrs = [
						'type'            => 'number',
						'input_id'        => "quantity_{$product_id}",
						'step'            => '1',
						'input_name'      => 'quantity',
						'input_value'     => $default,
						'title'           => _x( 'Qty', 'Product quantity input tooltip', 'woocommerce-bulk-variations' ),
						'size'            => 4,
						'inputmode'       => 'numeric',
						'data-individual' => $individual,
						'data-table_id'   => $this->table_id,
						'data-price'      => $price,
						'data-product_id' => $product_id,
						'min_value'       => 0,
					];

					if ( $backorders === 'no' ) {
						$attrs['max_value'] = $max;
					}

					if ( ! $product->is_in_stock() ) {
						$attrs['disabled'] = 'disabled';
						$attrs['value']    = 0;
					}

					$attrs = apply_filters( 'woocommerce_quantity_input_args', $attrs, $product );

					// the standard woocommerce_quantity_input_args filter may introduce classes we don't want, just remove them
					if ( isset( $attrs['classes'] ) ) {
						unset( $attrs['classes'] );
					}
					$attrs['class'] = 'wcbvp_quantity';
					$attrs          = $this->convert_wc_input_attrs( $attrs );

					/**
					* Hook: wc_bulk_variations_qty_input_args.
					*
					* Filters all of the input arguments before they are converted to attribute pairs. If you need
					* to add a class to the input, this is where you'd do it.
					*/
					$attrs = apply_filters( 'wc_bulk_variations_qty_input_args', $attrs, $product );

					$attribute_values = [];
					foreach ( $attrs as $key => $value ) {
						$attribute_values[] = preg_replace( '/[^a-zA-Z0-9-_]/', '', $key ) . '="' . esc_attr( $value ) . '"';
					}
					$input = sprintf( '<input %s>', implode( ' ', $attribute_values ) );

					/**
					* Hook: wc_bulk_variations_qty_input_html.
					*
					* Filters output of each individual the quantity <input> in the WBV table.
					*/
					$input = apply_filters( 'wc_bulk_variations_qty_input_html', $input, $attrs, $product );

					if ( $this->args->disable_purchasing ) {
						$data = $price_html;
					} else {
						$data = $input . $price_html;
					}

					if ( ! empty( $stock_tag ) ) {
						$data .= $stock_tag;
					}
				}
			}
		} else {

			if ( $row === 'variation-images' ) {
				$product_id = $this->args->attribute_matrix[ $column ][''];

				if ( $product_id ) {
					$data = $this->insert_image( $product_id );
				}
			} else {

				switch ( $column ) {
					case '':
						if ( ! $this->args->single_attribute ) {
							$image = $this->insert_image( $row );
							$row   = "<span class='" . ( empty( $image ) ? 'no-image' : 'with-image' ) . "'>" . $column_obj->get_column_heading( 1, $row ) . '</span>';
							$data  = $image;
						}
						break;
					case $this->args->attribute_column:
						if ( $this->args->single_attribute ) {
							$image = $this->insert_image( $row );
							$row   = "<span class='" . ( empty( $image ) ? 'no-image' : 'with-image' ) . "'>" . $column_obj->get_column_heading( 1, $row ) . '</span>';
							//$data  = $image . $row;
							$data    = $row; //custom mt
						} else {
							$image   = $this->insert_image( $row );
							$row  = "<span class='" . ( empty( $image ) ? 'no-image' : 'with-image' ) . "'>" . $column_obj->get_column_heading( 1, $row ) . '</span>';
							// $data    = $image . $row;
							$data = $row;
						}
						break;
					case 'sku':
						break;
					case 'name':
						break;
					case 'categories':
						break;
					case 'tags':
						break;
					case 'weight':
						break;
					case 'dimensions':
						break;
					case 'stock':
						break;
					case 'price':
						break;
				}
			}
		}

		/**
		* Hook: wc_bulk_variations_table_cell_output.
		*
		* Filters output of each individual <td> cell in the WBV table.
		*/
		return apply_filters( 'wc_bulk_variations_table_cell_output', $data, $product ?? null );
	}

	public function get_stock_html( $product ) {

		$availability = $product->get_availability();

		$stock_message = apply_filters_deprecated(
			'wc_bulk_variations_stock_message',
			[
				$availability['availability'],
				$product,
			],
			'1.2',
			'woocommerce_get_availability_text'
		);

		$stock_html = wc_get_stock_html( $product );

		return apply_filters_deprecated(
			'wc_bulk_variations_stock_message_html',
			[
				$stock_html,
				$stock_message,
				$product,
			],
			'1.2',
			'woocommerce_get_stock_html'
		);

	}

	//phpcs:ignore Squiz.Commenting.FunctionComment.MissingParamTag
	/**
	 * The woocommerce_quantity_input_args filter has all sorts of custom names for attributes that
	 * get hardcoded into the template, this function massages those array keypairs into their
	 * final form.
	 */
	private function convert_wc_input_attrs( $attrs ) {

		foreach ( $attrs as $key => $value ) {
			$newkey = null;
			if ( strpos( $key, 'input_' ) === 0 ) {
				$newkey = substr( $key, 6 );
			} elseif ( strrpos( $key, '_value' ) === strlen( $key ) - 6 ) {
				$newkey = substr( $key, 0, -6 );
			}
			if ( $newkey ) {
				unset( $attrs[ $key ] );
				$attrs[ $newkey ] = $value;
			}
		}

		return $attrs;

	}

}
