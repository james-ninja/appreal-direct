<?php
/**
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Bulk_Variations\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Bulk_Variations\Dependencies\Barn2\Setup_Wizard\Step;

class Features extends Step {

	/**
	 * Configure the step.
	 */
	public function __construct() {
		$this->set_id( 'features' );
		$this->set_name( __( 'Features', 'woocommerce-bulk-variations' ) );
		$this->set_title( __( 'Managing and displaying variations', 'woocommerce-bulk-variations' ) );
		$this->set_description( __( 'Which bulk variations features do you plan to use?', 'woocommerce-bulk-variations' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		return [
			'backend'  => [
				'type'    => 'checkbox',
				'label'   => __( 'Add and edit variations in bulk', 'woocommerce-bulk-variations' ),
				'value'   => get_option( 'wcbvp_wizard_use_backend' ) === 'yes',
				'classes' => [ 'wcbvp-sw-main-checkbox' ],
			],
			'frontend' => [
				'type'    => 'checkbox',
				'label'   => __( 'Display variations in a grid layout', 'woocommerce-bulk-variations' ),
				'value'   => get_option( 'wcbvp_wizard_use_frontend' ) === 'yes',
				'classes' => [ 'wcbvp-sw-main-checkbox' ],
			],
		];
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

		$values = $this->get_submitted_values();

		if ( isset( $values['frontend'] ) && filter_var( $values['frontend'], FILTER_VALIDATE_BOOLEAN ) ) {
			update_option( 'wcbvp_wizard_use_frontend', 'yes' );
		} else {
			delete_option( 'wcbvp_wizard_use_frontend' );
		}

		if ( isset( $values['backend'] ) && filter_var( $values['backend'], FILTER_VALIDATE_BOOLEAN ) ) {
			update_option( 'wcbvp_wizard_use_backend', 'yes' );
		} else {
			delete_option( 'wcbvp_wizard_use_backend' );
		}

		wp_send_json_success();
	}

}
