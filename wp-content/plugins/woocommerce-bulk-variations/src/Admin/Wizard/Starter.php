<?php
/**
 * This class handles the conditional restart of the setup wizard
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Bulk_Variations\Admin\Wizard;

use Barn2\Plugin\WC_Bulk_Variations\Dependencies\Barn2\Setup_Wizard\Starter as Setup_WizardStarter,
	Barn2\Plugin\WC_Bulk_Variations\Util\Settings;

/**
 * Class handling the wizart restart
 *
 * @return boolean
 */
class Starter extends Setup_WizardStarter {

	/**
	 * Determine if the conditions to start the wizard are met.
	 *
	 * @return boolean
	 */
	public function should_start() {
		return (
			false === get_option( 'wcbvp_wizard_use_frontend' ) &&
			false === get_option( 'wcbvp_wizard_use_backend' )
		);

	}

}
