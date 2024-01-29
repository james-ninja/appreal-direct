<?php
/**
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Bulk_Variations\Admin\Wizard;

use Barn2\Plugin\WC_Bulk_Variations\Dependencies\Barn2\Setup_Wizard\Interfaces\Restartable;
use Barn2\Plugin\WC_Bulk_Variations\Dependencies\Barn2\Setup_Wizard\Setup_Wizard;

/**
 * WPS Setup wizard.
 */
class Wizard extends Setup_Wizard implements Restartable {

	/**
	 * On wizard restart, detect which pages should be automatically unhidden.
	 *
	 * @return void
	 */
	public function on_restart() {
		check_ajax_referer( 'barn2_setup_wizard_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'error_message' => __( 'You are not authorized.', 'woocommerce-bulk-variations' ) ], 403 );
		}

		$toggle   = [];
		$frontend = 'yes' === get_option( 'wcbvp_wizard_use_frontend' );
		$backend  = 'yes' === get_option( 'wcbvp_wizard_use_backend' );

		if ( $frontend ) {
			$toggle[] = 'frontend';
		}

		if ( $backend ) {
			$toggle[] = 'backend';
		}

		wp_send_json_success(
			[
				'toggle' => $toggle
			]
		);

	}

}
