<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Admin;

use Barn2\Plugin\WC_Bulk_Variations\Args,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
	Barn2\Plugin\WC_Bulk_Variations\Util\Util,
	Barn2\WBV_Lib\Registerable,
	Barn2\WBV_Lib\Util as Lib_Util,
	Barn2\WBV_Lib\Plugin\Licensed_Plugin,
	Barn2\WBV_Lib\WooCommerce\Admin\Custom_Settings_Fields,
	WC_Barn2_Plugin_Promo,
	WC_Admin_Settings;

/**
 * Provides functions for the plugin settings page in the WordPress admin.
 *
 * Settings can be accessed at WooCommerce -> Settings -> Products -> Bulk variations.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Page implements Registerable {

	const SHORTCODE_DEFAULTS_SECTION_ID = 'bulk_variations_pro_shortcode_defaults';

	private $plugin;

	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function register() {
		// Register our custom settings types.
		$extra_setting_fields = new Custom_Settings_Fields();
		$extra_setting_fields->register();

		$license_setting = $this->plugin->get_license_setting();
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . $license_setting->get_license_setting_name(), [ $license_setting, 'save_license_key' ] );

		// Add plugin promo section.
		if ( class_exists( 'WC_Barn2_Plugin_Promo' ) ) {
			$plugin_promo = new WC_Barn2_Plugin_Promo( $this->plugin->get_item_id(), $this->plugin->get_file(), 'bulk-variations', false );
			$plugin_promo->register();
		}

		// Add sections & settings
		add_filter( 'woocommerce_get_sections_products', [ $this, 'add_section' ] );
		add_filter( 'woocommerce_get_settings_products', [ $this, 'add_settings' ], 12, 2 );
	}

	public function add_section( $sections ) {
		$sections[ Settings::SECTION_SLUG ] = __( 'Bulk variations', 'woocommerce-bulk-variations' );
		return $sections;
	}

	public function add_settings( $settings, $current_section ) {

		// Check we're on the correct settings section
		if ( Settings::SECTION_SLUG !== $current_section ) {
			return $settings;
		}

		$default_args = Settings::bulk_args_to_settings( Args::$default_args );

		$plugin_settings = [
			[
				'id'    => 'bulk_variations_pro_settings_start',
				'type'  => 'settings_start',
				'class' => 'bulk-variations-pro-settings barn2-settings promo'
			]
		];

		$documentation_url  = $this->plugin->get_documentation_url();
		$documentation_link = "<a target='_blank' href='$documentation_url'>" . __( 'Documentation', 'woocommerce-bulk-variations' ) . '</a>';

		// License key settings.
		$plugin_settings = array_merge(
			$plugin_settings,
			[
				[
					'title' => __( 'Bulk variations', 'woocommerce-bulk-variations' ),
					'type'  => 'title',
					'id'    => 'product_table_settings_license',
					'desc'  => '<p>' . __( 'The following options control the WooCommerce Bulk Variations extension.', 'woocommerce-bulk-variations' ) . '<p>'
					. '<p>'
					. $documentation_link . ' | '
					. Lib_Util::barn2_link( 'support-center/', __( 'Support', 'woocommerce-bulk-variations' ), true )
					. '</p>'
				],
				$this->plugin->get_license_setting()->get_license_key_setting(),
				$this->plugin->get_license_setting()->get_license_override_setting(),
			]
		);

		$plugin_settings[] = [
			'type' => 'sectionend',
			'id'   => 'bulk_variations_pro_settings_license'
		];

		$plugin_settings = array_merge(
			$plugin_settings,
			[
				[
					'title' => '',
					'type'  => 'title',
					'id'    => 'bulk_variations_pro_settings_title',
				],
				[
					'title'         => __( 'Variations grid', 'woocommerce-bulk-variations' ),
					'type'          => 'checkbox',
					'id'            => Settings::OPTION_VARIATIONS_DATA . '[enable]',
					'desc'          => __( 'Use the variations grid for all products with 1 or 2 variation attributes', 'woocommerce-bulk-variations' ),
					'default'       => $default_args['enable'],
					'checkboxgroup' => 'start'
				],
				[
					'title'         => __( 'Disable purchasing', 'woocommerce-bulk-variations' ),
					'type'          => 'checkbox',
					'id'            => Settings::OPTION_VARIATIONS_DATA . '[disable_purchasing]',
					'desc'          => __( 'Display the variations grid without quantity boxes or add to cart button', 'woocommerce-bulk-variations' ),
					'default'       => $default_args['disable_purchasing'],
					'checkboxgroup' => ''
				],
				[
					'title'         => __( 'Show stock level', 'woocommerce-bulk-variations' ),
					'type'          => 'checkbox',
					'id'            => Settings::OPTION_VARIATIONS_DATA . '[show_stock]',
					'desc'          => __( 'Display stock information in the variations grid', 'woocommerce-bulk-variations' ),
					'default'       => $default_args['show_stock'],
					'checkboxgroup' => ''
				],
				[
					'title'         => __( 'Variation images', 'woocommerce-bulk-variations' ),
					'type'          => 'checkbox',
					'id'            => Settings::OPTION_VARIATIONS_DATA . '[variation_images]',
					'desc'          => __( 'Display an image for each variation', 'woocommerce-bulk-variations' ),
					'default'       => $default_args['variation_images'],
					'checkboxgroup' => 'end'
				],
				[
					'title'   => __( 'Image lightbox', 'woocommerce-bulk-variations' ),
					'type'    => 'checkbox',
					'id'      => Settings::OPTION_VARIATIONS_DATA . '[use_lightbox]',
					'desc'    => __( 'Open variation images in a lightbox', 'woocommerce-bulk-variations' ),
					'default' => $default_args['use_lightbox'],
				],
				[
					'title'    => __( 'Single variation attribute', 'woocommerce-bulk-variations' ),
					'type'     => 'radio',
					'id'       => Settings::OPTION_VARIATIONS_DATA . '[variation_attribute]',
					'options'  => [
						''     => __( 'Display horizontally', 'woocommerce-bulk-variations' ),
						'vert' => __( 'Display vertically', 'woocommerce-bulk-variations' ),
					],
					'desc_tip' => __( 'Grid layout for products with only one variation attribute.', 'woocommerce-bulk-variations' ),
					'default'  => $default_args['variation_attribute']
				],
				[
					'type' => 'sectionend',
					'id'   => 'bulk_variations_pro_settings_content'
				]
			]
		);

		$plugin_settings[] = [
			'id'   => 'bulk_variations_pro_settings_end',
			'type' => 'settings_end'
		];

		$promo_settings = [];

		foreach ( $settings as $setting ) {
			if ( $setting['id'] === 'barn2_plugin_promo' || $setting['id'] === 'barn2_plugin_promo_content' ) {
				$promo_settings[] = $setting;
			}
		}

		return array_merge( $plugin_settings, $promo_settings );
	}
}
