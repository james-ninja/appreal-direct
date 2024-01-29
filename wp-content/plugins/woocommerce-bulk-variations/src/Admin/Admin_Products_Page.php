<?php

namespace Barn2\Plugin\WC_Bulk_Variations\Admin;

use Barn2\Plugin\WC_Bulk_Variations\Util\Util,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings,
	Barn2\WBV_Lib\Plugin\Licensed_Plugin,
	Barn2\WBV_Lib\Registerable,
	Barn2\WBV_Lib\Service;

/**
 * Provides functions for the plugin settings page in the WordPress admin.
 *
 * Settings can be accessed at WooCommerce -> Settings -> Products -> Bulk Variations.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Products_Page implements Registerable, Service {

	private $plugin;
	private $templates_path;

	public function __construct( Licensed_Plugin $plugin, $templates_path ) {
		$this->plugin         = $plugin;
		$this->templates_path = $templates_path;
	}

	public function register() {
		// Add product tabs
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_product_tab' ] );

		// Add bulk variation fields view
		add_action( 'woocommerce_product_data_panels', [ $this, 'add_product_view' ] );

		// Save bulk variation fields
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_fields' ], 999 );

		// Product page JS
		add_action( 'admin_enqueue_scripts', [ $this, 'product_admin_scripts' ] );
	}

	public function update_fields() {
		$update   = false;
		$settings = Settings::get_setting( Settings::OPTION_VARIATIONS_DATA );
		$override = isset( $_POST[ Settings::OPTION_VARIATIONS_DATA . '_override' ] ) ? $_POST[ Settings::OPTION_VARIATIONS_DATA . '_override' ] : null; //phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data     = isset( $_POST[ Settings::OPTION_VARIATIONS_DATA ] ) ? $_POST[ Settings::OPTION_VARIATIONS_DATA ] : []; //phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( isset( $data['hide_add_to_cart'] ) && $data['hide_add_to_cart'] ) {
			$update = true;
		} else {
			if ( $settings ) {
				foreach ( $settings as $k_setting => $v_setting ) {
					switch ( $k_setting ) {
						case 'enable':
							if ( $v_setting !== $override ) {
								$update = true;
							}
							break;
						default:
							if ( isset( $data[ $k_setting ] ) && $data[ $k_setting ] !== $v_setting ) {
								$update = true;
							}
							break;
					}
				}
			} else {
				$update = true;
			}
		}

		return $update;
	}

	public function save_product_fields( $post_id ) {
		$product_obj = wc_get_product( $post_id );

		if ( $product_obj instanceof \WC_Product_Variable ) {
			$attributes = $product_obj->get_variation_attributes();
			$update     = $this->update_fields();

			if ( $update ) {
				// Save fields
				$override = isset( $_POST[ Settings::OPTION_VARIATIONS_DATA . '_override' ] ) ? $_POST[ Settings::OPTION_VARIATIONS_DATA . '_override' ] : null; //phpcs:ignore WordPress.Security.NonceVerification.Missing

				if ( ! is_null( $override ) ) {
					update_post_meta( $post_id, Settings::OPTION_VARIATIONS_DATA . '_override', esc_attr( $override ) );
				}

				$data = isset( $_POST[ Settings::OPTION_VARIATIONS_DATA ] ) ? $_POST[ Settings::OPTION_VARIATIONS_DATA ] : null; //phpcs:ignore WordPress.Security.NonceVerification.Missing

				if ( ! is_null( $data ) && is_array( $data ) ) {
					update_post_meta( $post_id, Settings::OPTION_VARIATIONS_DATA, $data );
				}
			} else {

				delete_post_meta( $post_id, Settings::OPTION_VARIATIONS_DATA . '_override' );
				delete_post_meta( $post_id, Settings::OPTION_VARIATIONS_DATA );
			}

			$single_attribute = ( count( $attributes ) === 1 ) ? true : false;

			if ( $single_attribute ) {

				$settings      = Settings::get_setting( Settings::OPTION_VARIATIONS_DATA );
				$single_layout = isset( $settings['variation_attribute'] ) ? $settings['variation_attribute'] : '';
				$structure     = isset( $_POST[ Settings::OPTION_VARIATIONS_STRUCTURE ] ) ? $_POST[ Settings::OPTION_VARIATIONS_STRUCTURE ] : null; //phpcs:ignore WordPress.Security.NonceVerification.Missing

				if ( $structure['rows'] && $single_layout ) {
					delete_post_meta( $post_id, Settings::OPTION_VARIATIONS_STRUCTURE );
				} elseif ( $structure['columns'] && ! $single_layout ) {
					delete_post_meta( $post_id, Settings::OPTION_VARIATIONS_STRUCTURE );
				} else {
					update_post_meta( $post_id, Settings::OPTION_VARIATIONS_STRUCTURE, $structure );
				}
			} else {
				$structure = isset( $_POST[ Settings::OPTION_VARIATIONS_STRUCTURE ] ) ? $_POST[ Settings::OPTION_VARIATIONS_STRUCTURE ] : null; //phpcs:ignore WordPress.Security.NonceVerification.Missing

				if ( ! is_null( $structure ) && is_array( $structure ) ) {
					update_post_meta( $post_id, Settings::OPTION_VARIATIONS_STRUCTURE, $structure );
				}
			}
		}
	}

	public function add_product_view() {
		include_once $this->templates_path . 'bulk-variation-data-panel.php';
	}

	public function product_admin_scripts() {
		global $post;
		if ( $post && $post->post_type === 'product' ) {

			$is_bulk          = false;
			$variations_count = 0;

			$product_obj = wc_get_product( $post->ID );

			if ( $product_obj && $product_obj instanceof \WC_Product_Variable ) {

				$variation_child  = $product_obj->get_children();
				$variations_count = count( $variation_child );
				if ( $variation_child && count( $variation_child ) > 0 ) {

					$attributes = $product_obj->get_variation_attributes();

					if ( $attributes && count( $attributes ) <= 2 ) {

						$is_bulk = true;
					}
				}
			}

			wp_enqueue_style( 'wc-bulk-variations-product', Util::get_asset_url( 'css/admin/wc-bulk-variations-product.css' ), [], $this->plugin->get_version() );
			wp_enqueue_script( 'wc-bulk-variations-product', Util::get_asset_url( 'js/admin/wc-bulk-variations-product.min.js' ), [ 'jquery' ], $this->plugin->get_version(), true );

			$settings = Settings::get_setting( Settings::OPTION_VARIATIONS_DATA );

			$data = [
				'variations_count'      => $variations_count,
				'is_bulk_variation'     => $is_bulk,
				'option_variation_data' => Settings::OPTION_VARIATIONS_DATA,
				'settings'              => $settings
			];

			wp_localize_script( 'wc-bulk-variations-product', 'wcbvp_data', $data );
		}
	}

	public function add_product_tab( $tabs ) {
		$tabs['bulk_variations'] = [
			'label'    => 'Bulk Variations',
			'target'   => 'bulk_variations_product_data',
			'class'    => [ 'show_if_bulk_variations' ],
			'priority' => 65
		];

		return $tabs;
	}
}
