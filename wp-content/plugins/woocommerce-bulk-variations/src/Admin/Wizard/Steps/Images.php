<?php
/**
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Bulk_Variations\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
	Barn2\Plugin\WC_Bulk_Variations\Dependencies\Barn2\Setup_Wizard\Step,
	Barn2\Plugin\WC_Bulk_Variations\Dependencies\Barn2\Setup_Wizard\Util as Wizard_Util;

class Images extends Step {

	/**
	 * Configure the step.
	 */
	public function __construct() {
		$this->set_id( 'images' );
		$this->set_name( __( 'Images', 'woocommerce-bulk-variations' ) );
		$this->set_description( __( 'Choose how to display the image of each variation in the grid', 'woocommerce-bulk-variations' ) );
		$this->set_title( __( 'Variation images', 'woocommerce-bulk-variations' ) );
		$this->set_hidden( true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$settings    = Settings::get_settings( $this->get_plugin() );
		$data_values = Settings::get_setting( Settings::OPTION_VARIATIONS_DATA );

		$fields = array_combine(
			[
				'variation_images',
				'use_lightbox',
			],
			Wizard_Util::pluck_wc_settings(
				$settings,
				[
					Settings::OPTION_VARIATIONS_DATA . '[variation_images]',
					Settings::OPTION_VARIATIONS_DATA . '[use_lightbox]',
				]
			)
		);

		$fields['variation_images']['classes'] = [ 'wcbvp-sw-select' ];
		$fields['variation_images']['value']   = array_values(
			array_filter(
				$fields['variation_images']['options'],
				function( $option ) use ( $data_values ) {
					return $option['key'] === $data_values['variation_images'];
				}
			)
		);

		$fields['use_lightbox']['value']       = filter_var( $data_values['use_lightbox'], FILTER_VALIDATE_BOOLEAN );
		$fields['use_lightbox']['label']       = $fields['use_lightbox']['description'];
		$fields['use_lightbox']['description'] = '';
		$fields['use_lightbox']['conditions']  = [
			'variation_images[0].key' => [
				'op'    => 'neq',
				'value' => 'off',
			],
		];

		return $fields;
	}

	/**
	 * Update options in the database if needed.
	 *
	 * @return void
	 */
	public function submit() {
		check_ajax_referer( 'barn2_setup_wizard_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( esc_html__( 'You are not authorized.', 'woocommerce-bulk-variations' ) );
		}

		$settings = get_option( Settings::OPTION_VARIATIONS_DATA );
		$values   = $this->get_submitted_values();

		if ( is_array( $values['variation_images'] ) && isset( $values['variation_images'][0]['key'] ) ) {
			$values['variation_images'] = $values['variation_images'][0]['key'];
		}

		$values['use_lightbox'] = filter_var( $values['use_lightbox'], FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no';

		$values = wp_parse_args(
			$values,
			$settings
		);

		update_option( Settings::OPTION_VARIATIONS_DATA, $values );

		wp_send_json_success();
	}

}
