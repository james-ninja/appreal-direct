<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Plugin\WC_Bulk_Variations\Util\Util,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings;

global $post;

$product_obj = wc_get_product( $post->ID );
$attributes  = [];

if ( $product_obj instanceof \WC_Product_Variable ) {
	$attributes = $product_obj->get_variation_attributes();
}

$attribute_options = [ '' => __( 'Select attribute', 'woocommerce-bulk-variations' ) ];

$settings = Settings::get_setting( Settings::OPTION_VARIATIONS_DATA );
$override = ( isset( $settings['enable'] ) && $settings['enable'] ) ? 1 : 0;

$override  = metadata_exists( 'post', $post->ID, Settings::OPTION_VARIATIONS_DATA . '_override' ) ? get_post_meta( $post->ID, Settings::OPTION_VARIATIONS_DATA . '_override', true ) : $override;
$data      = get_post_meta( $post->ID, Settings::OPTION_VARIATIONS_DATA, true ) ? get_post_meta( $post->ID, Settings::OPTION_VARIATIONS_DATA, true ) : [];
$structure = get_post_meta( $post->ID, Settings::OPTION_VARIATIONS_STRUCTURE, true ) ? get_post_meta( $post->ID, Settings::OPTION_VARIATIONS_STRUCTURE, true ) : [];

$data = wp_parse_args( $data, $settings );

$rows_value      = isset( $structure['rows'] ) ? $structure['rows'] : null;
$columns_value   = isset( $structure['columns'] ) ? $structure['columns'] : null;
$hide_value      = isset( $data['hide_add_to_cart'] ) ? $data['hide_add_to_cart'] : 0;
$disable_value   = isset( $data['disable_purchasing'] ) ? $data['disable_purchasing'] : 0;
$show_stock      = isset( $data['show_stock'] ) ? $data['show_stock'] : 0;
$variation_value = isset( $data['variation_images'] ) ? $data['variation_images'] : 0;
$lightbox_value  = isset( $data['use_lightbox'] ) ? $data['use_lightbox'] : 1;

$single_attribute = ( count( $attributes ) === 1 ) ? true : false;
$single_layout    = isset( $settings['variation_attribute'] ) ? $settings['variation_attribute'] : '';

foreach ( $attributes as $at_k => $at_values ) {

	$taxonomy = get_taxonomy( $at_k ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	if ( $taxonomy && $taxonomy instanceof \WP_Taxonomy ) {
		$attribute_options[ $taxonomy->name ] = $taxonomy->labels->singular_name;

		if ( $single_attribute ) {

			if ( $columns_value === null && ! $single_layout ) {
				$columns_value = $at_k;
			} elseif ( $rows_value === null && $single_layout ) {
				$rows_value = $at_k;
			}
		} else {

			if ( $columns_value === null ) {
				$columns_value = $at_k;
			} elseif ( $rows_value === null ) {
				$rows_value = $at_k;
			}
		}
	} else {
		$attribute_options[ $at_k ] = $at_k;
		if ( $single_attribute ) {
			if ( $columns_value === null && ! $single_layout ) {
				$columns_value = $at_k;
			} elseif ( $rows_value === null && $single_layout ) {
				$rows_value = $at_k;
			}
		} else {

			if ( $columns_value === null ) {
				$columns_value = $at_k;
			} elseif ( $rows_value === null ) {
				$rows_value = $at_k;
			}
		}
	}
}

?>

<div id="bulk_variations_product_data" class="panel woocommerce_options_panel hidden">

	<div class="options_group">

		<?php

		woocommerce_wp_hidden_input(
			[
				'id'    => Settings::OPTION_VARIATIONS_DATA . '_override',
				'value' => '0',
			]
		);

		woocommerce_wp_checkbox(
			[
				'id'            => Settings::OPTION_VARIATIONS_DATA . '_override',
				'wrapper_class' => '',
				'label'         => __( 'Enable variations grid', 'woocommerce-bulk-variations' ),
				'description'   => __( 'Replace the default variation dropdowns with a bulk variations grid.', 'woocommerce-bulk-variations' ),
				'cbvalue'       => '1',
				'desc_tip'      => true,
				'value'         => $override
			]
		);

		echo '<div id="' . Settings::OPTION_VARIATIONS_DATA . '_hide_add_to_cart_div">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		woocommerce_wp_hidden_input(
			[
				'id'    => Settings::OPTION_VARIATIONS_DATA . '[hide_add_to_cart]',
				'value' => '0',
			]
		);

		woocommerce_wp_checkbox(
			[
				'id'            => Settings::OPTION_VARIATIONS_DATA . '[hide_add_to_cart]',
				'wrapper_class' => '',
				'label'         => __( 'Hide default add to cart', 'woocommerce-bulk-variations' ),
				'description'   => __( 'Remove the default variation dropdowns and add to cart button (recommended if you are using a shortcode to insert the variations grid manually).', 'woocommerce-bulk-variations' ),
				'cbvalue'       => '1',
				'desc_tip'      => true,
				'value'         => $hide_value
			]
		);

		echo '</div>';

		woocommerce_wp_hidden_input(
			[
				'id'    => Settings::OPTION_VARIATIONS_DATA . '[disable_purchasing]',
				'value' => '0',
			]
		);

		woocommerce_wp_checkbox(
			[
				'id'            => Settings::OPTION_VARIATIONS_DATA . '[disable_purchasing]',
				'wrapper_class' => '',
				'label'         => __( 'Disable purchasing', 'woocommerce-bulk-variations' ),
				'description'   => __( 'Display the variations grid without quantity boxes or add to cart button.', 'woocommerce-bulk-variations' ),
				'cbvalue'       => '1',
				'desc_tip'      => true,
				'value'         => $disable_value
			]
		);

		woocommerce_wp_hidden_input(
			[
				'id'    => Settings::OPTION_VARIATIONS_DATA . '[show_stock]',
				'value' => '0',
			]
		);

		woocommerce_wp_checkbox(
			[
				'id'            => Settings::OPTION_VARIATIONS_DATA . '[show_stock]',
				'wrapper_class' => '',
				'label'         => __( 'Show stock level', 'woocommerce-bulk-variations' ),
				'description'   => __( 'Display stock information in the variations grid', 'woocommerce-bulk-variations' ),
				'cbvalue'       => '1',
				'desc_tip'      => true,
				'value'         => $show_stock,
			]
		);

		woocommerce_wp_select(
			[
				'id'          => Settings::OPTION_VARIATIONS_STRUCTURE . '_columns',
				'name'        => Settings::OPTION_VARIATIONS_STRUCTURE . '[columns]',
				'label'       => __( 'Horizontal', 'woocommerce-bulk-variations' ),
				'options'     => $attribute_options,
				'description' => 'Select which attribute to use as the columns for the variations grid.',
				'desc_tip'    => true,
				'value'       => $columns_value,
			]
		);

		woocommerce_wp_select(
			[
				'id'          => Settings::OPTION_VARIATIONS_STRUCTURE . '_rows',
				'name'        => Settings::OPTION_VARIATIONS_STRUCTURE . '[rows]',
				'label'       => __( 'Vertical', 'woocommerce-bulk-variations' ),
				'options'     => $attribute_options,
				'description' => 'Select which attribute to use as the rows for the variations grid.',
				'desc_tip'    => true,
				'value'       => $rows_value
			]
		);

		woocommerce_wp_hidden_input(
			[
				'id'    => Settings::OPTION_VARIATIONS_DATA . '[variation_images]',
				'value' => '0',
			]
		);

		woocommerce_wp_checkbox(
			[
				'id'            => Settings::OPTION_VARIATIONS_DATA . '[variation_images]',
				'wrapper_class' => '',
				'label'         => __( 'Display variation images', 'woocommerce-bulk-variations' ),
				'description'   => __( 'Display an image for each variation.', 'woocommerce-bulk-variations' ),
				'cbvalue'       => '1',
				'desc_tip'      => true,
				'value'         => $variation_value
			]
		);

		woocommerce_wp_hidden_input(
			[
				'id'    => Settings::OPTION_VARIATIONS_DATA . '[use_lightbox]',
				'value' => '0',
			]
		);

		woocommerce_wp_checkbox(
			[
				'id'            => Settings::OPTION_VARIATIONS_DATA . '[use_lightbox]',
				'wrapper_class' => '',
				'label'         => __( 'Image lightbox', 'woocommerce-bulk-variations' ),
				'description'   => __( 'Open variation images in a lightbox', 'woocommerce-bulk-variations' ),
				'cbvalue'       => '1',
				'desc_tip'      => true,
				'value'         => $lightbox_value
			]
		);

		?>
	</div>
</div>
