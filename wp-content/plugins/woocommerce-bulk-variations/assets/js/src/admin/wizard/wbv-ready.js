/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	Dashicon,
	Button
} from '@wordpress/components';
import { Component } from '@wordpress/element';
import { Text } from '@woocommerce/experimental';
import { isEmpty } from 'lodash';
import parse from 'html-react-parser';

import { SetupWizardSettings } from '../../../../../vendor/barn2/setup-wizard/resources/js/utilities'

export default class WBVReady extends Component {

	constructor( props ) {
		super( props );
	}

	render() {
		const {
			step,
			getValues
		} = this.props;

		const {
			frontend, backend
		} = getValues();



		return (
			<div className="woocommerce-profile-wizard__store-details">
				<div className="woocommerce-profile-wizard__step-header">
					<Dashicon icon="yes-alt" className="completed-icon" />
					<Text
						variant="title.small"
						as="h2"
						size="20"
						lineHeight="28px"
					>
						{ isEmpty( step.heading ) ? sprintf( __( 'Welcome to %s' ), SetupWizardSettings.plugin_name ) : step.heading }
					</Text>
					{ ! isEmpty( step.description ) &&
						<div className="completed-desc">
							{ parse( step.description ) }
						</div>
					}

					{ frontend && ! backend && ! isEmpty( step.fields.msg_frontend.value ) &&
						<div className="completed-desc">
							{ parse( step.fields.msg_frontend.value ) }
						</div>
					}

					{ backend && ! frontend && ! isEmpty( step.fields.msg_backend.value ) &&
						<div className="completed-desc">
							{ parse( step.fields.msg_backend.value ) }
						</div>
					}

					{ backend && frontend && ! isEmpty( step.fields.msg_both.value ) &&
						<div className="completed-desc">
							{ parse( step.fields.msg_both.value ) }
						</div>
					}

					{ frontend && 
						<Button className="completed-btn wcbvp-complete-button" href={ SetupWizardSettings.skip_url } isSecondary>{ __( 'Settings page' ) }</Button>
					}

					{ backend && 
						<Button className="completed-btn wcbvp-complete-button" href={ all_products_url } isSecondary>{ __( 'Products' ) }</Button>
					}
				</div>
			</div>
		);
	}
}
