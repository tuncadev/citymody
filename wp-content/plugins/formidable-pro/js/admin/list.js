/**
 * This file is loaded on the forms list/index page.
 * This code is responsible for:
 *  - autopopulating the application dropdown from the new form modal
 */
( function() {
	/** globals frmDom, wp, frmAutocompleteApplicationVars */

	if ( 'undefined' === typeof frmDom || 'undefined' === typeof wp ) {
		return;
	}

	const { tag, div, span } = frmDom;
	const __ = wp.i18n.__;

	const state = {
		addedApplicationDropdown: false
	};

	addAction( 'frm_new_form_modal_form', addApplicationDropdownToNewFormModal );
	addFilter( 'frm_before_install_new_form', beforeInstallNewForm );

	function addApplicationDropdownToNewFormModal( $modal ) {
		if ( ! state.addedApplicationDropdown ) {
			$modal.addClass( 'frm_wrap' );
			$modal.find( '#frm_template_desc' ).parent().append( getApplicationDropdown( $modal ) );
			state.addedApplicationDropdown = true;
		}
	}

	function getApplicationDropdown( $modal ) {
		const dropdown = getApplicationAutocomplete();

		const label = tag(
			'label',
			{
				children: [
					document.createTextNode( __( 'Insert form into application', 'formidable-pro' ) )
				]
			}
		);
		label.setAttribute( 'for', 'frm_application_autocomplete_input' );

		const wrapper = div({
			className: 'frm_wrap',
			children: [ label, dropdown ]
		});
		wrapper.style.marginTop = '10px';

		frmDom.autocomplete.initAutocomplete( 'application', dropdown );

		jQuery( dropdown.querySelector( '.frm-application-search' ) ).autocomplete( 'option', 'appendTo', $modal );

		const urlParams = new URLSearchParams( window.location.search );
		const applicationId = urlParams.get( 'applicationId' );
		if ( applicationId && 'undefined' !== typeof frmAutocompleteApplicationVars ) {
			dropdown.querySelector( '#frm_application_autocomplete_input' ).value = frmAutocompleteApplicationVars.name;
			dropdown.querySelector( '.frm_autocomplete_value_input' ).value = applicationId;
		}

		return wrapper;
	}

	function getApplicationAutocomplete() {
		return span({
			className: 'success_action_application_box success_action_box',
			children: [
				input({
					id: 'frm_application_autocomplete_input',
					className: 'frm-application-search',
					type: 'text',
					placeholder: __( 'Select application', 'formidable-pro' )
				}),
				input({
					className: 'frm_autocomplete_value_input',
					type: 'hidden',
					name: 'application_id'
				})
			]
		});
	}

	function input( args ) {
		const output = tag( 'input', args );
		if ( 'object' === typeof args ) {
			if ( 'string' === typeof args.type ) {
				output.type = args.type;
			}
			if ( 'string' === typeof args.placeholder ) {
				output.setAttribute( 'placeholder', args.placeholder );
			}
			if ( 'string' === typeof args.name ) {
				output.setAttribute( 'name', args.name );
			}
		}
		return output;
	}

	function beforeInstallNewForm( data, { formData }) {
		data.application_id = formData.application_id;
		return data;
	}

	function addAction( hookName, callback ) {
		wp.hooks.addAction( hookName, 'formidable', callback );
	}

	function addFilter( hookName, callback ) {
		wp.hooks.addFilter( hookName, 'formidable', callback );
	}
}() );
