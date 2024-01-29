import { get } from 'lodash';
import WithForm from '../../../../../vendor/barn2/setup-wizard/resources/js/steps/with-form';

export default class PluginFeatures extends WithForm {

    constructor( props ) {
        super( props );
        const { getValues } = this.props


		this.initialValues = getValues();
        console.log( this.props, this.initialValues )
    }

	/**
	 * Automatically toggle steps on mount if required
	 */
     componentDidMount() {

		const step = this.props.step
		const fields = step.fields

		const frontend = get( fields.frontend, 'value', false );

        if ( true === frontend ) {
            this.props.showStep( 'general-settings' )
            this.props.showStep( 'images' )
        } else {
            this.props.hideStep( 'general-settings' )
            this.props.hideStep( 'images' )
        }

	}

    /**
     * Submit the form via ajax
     */
    onSubmit( values ) {

        // Reset the steps so we make sure the hidden ones stay hidden.
        this.props.resetSteps()

        this.setErrorMessage( false ); // reset the error message.

        const frontend = get( values, 'frontend', false );
        const backend = get( values, 'backend', false );

        // Make sure at least one option is clicked.
        if ( false === frontend && false === backend ) {
            this.setErrorMessage( 'Please select at least one option.' );
            return;
        }

        if ( true === frontend ) {
            this.props.showStep( 'general-settings' )
            this.props.showStep( 'images' )
        } else {
            this.props.hideStep( 'general-settings' )
            this.props.hideStep( 'images' )
        }

        super.onSubmit(values)

    }
}