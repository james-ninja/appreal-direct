import { __ } from '@wordpress/i18n'
import { addAction, addFilter } from '@wordpress/hooks';
import { has, get, isEmpty } from 'lodash';
import axios from 'axios';
import qs from 'qs';

import { SetupWizardSettings, getParsedAjaxError } from '../../../../../vendor/barn2/setup-wizard/resources/js/utilities';

import PluginFeatures from './plugin-features';
import WBVReady from './wbv-ready';

addFilter( 'barn2_setup_wizard_steps', 'bulk-variations', ( steps ) => {
    const customSteps = [
        { stepKey: 'features', stepClass: PluginFeatures },
        // { stepKey: 'ready', stepClass: WBVReady }
    ];

    for ( const { stepKey, stepClass } of customSteps ) {
        const index = steps.indexOf( steps.find( s => stepKey === s.key ) );
        if ( -1 !== index ) {
            steps[ index ].container = stepClass;
        }
    }

    return steps;
} );

addAction( 'barn2_wizard_on_restart', 'bulk-variations', ( wizard ) => {

    wizard.setState( { wizard_loading: true, wizard_complete: true } );

    axios.post( SetupWizardSettings.ajax, qs.stringify(
        {
            action: `barn2_wizard_${ SetupWizardSettings.plugin_slug }_on_restart`,
            nonce: SetupWizardSettings.nonce,
        }
     ) )
    .then(function (response) {

        if ( has( response, 'data.data.toggle' ) ) {
            const toToggle = response.data.data.toggle
            const frontend = toToggle.includes( 'frontend' )
            const backend  = toToggle.includes( 'backend' )

            if ( false === frontend && false === backend ) {
                wizard.setState( { wizard_loading: false } );
                return;
            }

            if ( frontend === true ) {
                wizard.showStep( 'general-settings' )
                wizard.showStep( 'images' )
            }

            wizard.setState( { wizard_loading: false } );

            wizard.setStepsCompleted( true )

        }
    })
    .catch(function (error) {
        if ( error.response ) {
            if ( ! isEmpty( getParsedAjaxError( error.response ) ) ) {
                wizard.setErrorMessage( getParsedAjaxError( error.response ) );
            } else {
                wizard.setErrorMessage( error.response.statusText );
            }
        } else if (error.request) {
            wizard.setErrorMessage( __( 'The request was made but no response was received.', 'woocommerce-bulk-variations' ) );
        } else {
            wizard.setErrorMessage( __( 'Something went wrong while making the request.', 'woocommerce-bulk-variations' ) );
        }
    });

} );