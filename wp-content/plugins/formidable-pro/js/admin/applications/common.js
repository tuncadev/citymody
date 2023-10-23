/**
 * This file is loaded on multiple applications pages that requires it.
 */
( function() {
	/** globals ajaxurl, wp, frmDom, frmGlobal, frmCommonApplicationVars */

	if ( 'undefined' === typeof ajaxurl || 'undefined' === typeof wp || 'undefined' === typeof frmDom || 'undefined' === typeof frmGlobal ) {
		return;
	}

	const __ = wp.i18n.__;
	const { tag, div, a, img, svg, span } = frmDom;
	const { doJsonPost } = frmDom.ajax;
	const { debounce, onClickPreventDefault } = frmDom.util;
	const { maybeCreateModal, footerButton } = frmDom.modal;

	addFilter( 'frm_no_applications_placeholder', getNoApplicationsPlaceholder );
	addFilter( 'frm_get_application_name_validation_message', adjustApplicationNameValidationMessage );
	addFilter( 'frm_application_summary_item', getSummaryItemElement );
	addAction( 'frm_add_application_name_validation', addApplicationNameValidation );
	addAction( 'frm_trigger_delete_application_modal', triggerDeleteApplicationModal );
	addAction( 'frm_application_item_option', addApplicationItemOption );

	function addApplicationNameValidation( inputWrapper, { applicationId, onValidationCallback, validateOnInit = true }) {
		const validationStatus = div();
		validationStatus.style.marginTop = '5px';
		validationStatus.style.fontSize = '11px';
		validationStatus.style.height = '20px';
		inputWrapper.appendChild( validationStatus );
		inputWrapper.classList.add( 'frm-invalid-application-name' );

		const input = inputWrapper.querySelector( 'input' );

		const debouncedValidation = debounce( handleApplicationNameChange, 500 );
		const listener = () => {
			validationStatus.style.visibility = 'hidden';
			debouncedValidation();
		};

		input.addEventListener( 'input', listener );
		input.addEventListener( 'change', listener );

		const initialName = input.value.trim();
		if ( validateOnInit && initialName.length ) {
			validateApplicationName( initialName );
		}

		function handleApplicationNameChange() {
			const name = input.value.trim();

			if ( ! name.length ) {
				handleAppNameValidation( name, false );
				validationStatus.textContent = __( 'Name cannot be empty', 'formidable-pro' );
				return;
			}

			if ( name.length > 200 ) {
				handleAppNameValidation( name, false );
				validationStatus.textContent = __( 'Application name cannot exceed 200 characters', 'formidable-pro' );
				return;
			}

			validateApplicationName( name );
		}

		function handleAppNameValidation( name, valid ) {
			inputWrapper.classList.toggle( 'frm-invalid-application-name', ! valid );

			validationStatus.style.visibility = 'visible';
			validationStatus.classList.toggle( 'frm_error', ! valid );

			if ( valid ) {
				validationStatus.style.color = '#1E561F';
				validationStatus.textContent = __( 'This name is available', 'formidable-pro' );
			} else {
				validationStatus.style.color = '';
				validationStatus.textContent = __( 'This name is taken', 'formidable-pro' );
			}

			onValidationCallback({ name, valid });
		}

		function validateApplicationName( name ) {
			const formData = new FormData();
			formData.append( 'name', name );
			if ( applicationId ) {
				formData.append( 'application_id', applicationId );
			}

			doJsonPost( 'validate_application_name', formData )
				.then( () => handleAppNameValidation( name, true ) )
				.catch( () => handleAppNameValidation( name, false ) );
		}
	}

	function adjustApplicationNameValidationMessage( message, { isValid, name, input }) {
		if ( '' === input.value ) {
			return __( 'Application name can not be empty', 'formidable-pro' );
		}

		if ( input.value.length > 200 ) {
			return __( 'Application name is too long', 'formidable-pro' );
		}

		if ( input.value !== name ) {
			return __( 'Application name has not finished validating. Please wait and try again.', 'formidable-pro' );
		}

		if ( ! isValid ) {
			return __( 'Application name is not valid or taken', 'formidable-pro' );
		}

		return message;
	}

	function getNoApplicationsPlaceholder() {
		return div({
			id: 'frm_no_applications_placeholder',
			className: 'frm_placeholder_block',
			children: [
				getNoApplicationsIcon(),
				tag( 'h3', { text: __( 'You don\'t have any applications', 'formidable-pro' ) }),
				div({ text: __( 'Applications help to organize your workspace by combining forms, Views, and pages into a full solution.', 'formidable-pro' ) }),
				getCreateApplicationButton()
			]
		});
	}

	function getNoApplicationsIcon() {
		return div({
			child: img({ src: frmGlobal.url + '/images/applications/folder.svg' })
		});
	}

	function getCreateApplicationButton() {
		return a({
			className: 'button frm-button-secondary frm-new-application-button',
			text: __( 'Create Application', 'formidable-pro' )
		});
	}

	function triggerDeleteApplicationModal( termId, callback ) {
		const title = __( 'Delete Application', 'formidable-pro' );
		const content = div({
			children: [
				tag( 'p', __( 'Are you sure you want to delete this application?', 'formidable-pro' ) ),
				tag( 'p', __( 'Forms, Views, and pages will not be deleted.', 'formidable-pro' ) )
			]
		});
		content.style.padding = '20px';
		const confirmButton = footerButton({
			text: __( 'Delete Application', 'formidable-pro' ),
			buttonType: 'red'
		});
		confirmButton.addEventListener( 'click', handleDeleteConfirmation );
		const footer = div({ child: confirmButton });
		const modal = maybeCreateModal(
			'frm_delete_application_modal',
			{ title, content, footer }
		);
		modal.classList.add( 'frm_common_modal' );

		function handleDeleteConfirmation() {
			const formData = new FormData();
			formData.append( 'term_id', termId );
			doJsonPost( 'delete_application', formData ).then(
				() => callback( termId )
			);
		}
	}

	function addApplicationItemOption( container, { optionData, handleCheckboxChange }) {
		const { label, value } = optionData;

		const text = '' === label ? __( '(no title)', 'formdiable-pro' ) : label;
		const checkbox = tag(
			'input',
			{ id: 'frm-application-item-checkbox-' + value }
		);
		checkbox.value = value;
		checkbox.type = 'checkbox';
		checkbox.addEventListener(
			'change',
			event => {
				handleCheckboxChange( event );
				option.classList.toggle( 'active', checkbox.checked );
			}
		);

		const checkboxLabel = tag(
			'label',
			{
				children: [
					checkbox,
					tag( 'span', text )
				]
			}
		);

		const option = div({
			className: 'frm-application-item-option frm-select-list-item',
			children: [ checkboxLabel ]
		});
		option.addEventListener( 'click', event => event.target.classList.contains( 'frm-application-item-option' ) && option.querySelector( 'input' ).click() );
		container.appendChild( option );
	}

	function getSummaryItemElement( _, hookArgs ) {
		const { label, checked, type } = hookArgs;
		const children = [];

		if ( checked ) {
			children.push( getCheckmark() );
		} else {
			children.push( div({ className: 'frm-application-check-space' }) );
		}

		children.push(
			span({
				className: 'frm-type-tag',
				text: getTitleCaseTextForType( type )
			}),
			span({ text: label })
		);

		if ( 'page' !== type && frmCommonApplicationVars.canEditPages ) {
			// TODO only add this if it's a type that would display an embed button.
			// ('embedded form' !== descriptiveType && 0 === embeddedIn.length;)
			// See shouldShowEmbedButton in frm_application.js
			children.push( getGeneratePageOption( hookArgs ) );
		}

		return div({
			className: 'frm-application-summary-item',
			children
		});
	}

	function getTitleCaseTextForType( type ) {
		switch ( type ) {
			case 'form': return __( 'Form', 'formidable-pro' );
			case 'view': return __( 'View', 'formidable-pro' );
			case 'page': return __( 'Page', 'formidable-pro' );
		}
		return type;
	}

	function getGeneratePageOption( hookArgs ) {
		const { type, objectId, label, applicationId } = hookArgs;

		const option = a({
			className: 'frm-generate-page-option',
			text: __( '+ Create a new page', 'formidable-pro' )
		});
		onClickPreventDefault( option, handleOptionClick );

		function handleOptionClick() {
			const hookName = 'frm_create_page_with_shortcode_data';
			const data = wp.hooks.applyFilters(
				hookName,
				{
					object_id: objectId,
					type: type,
					name: label
				}
			);
			const formData = new FormData();
			formData.append( 'nonce', frmGlobal.nonce );
			formData.append( 'application_id', applicationId );
			Object.keys( data ).forEach( key => formData.append( key, data[ key ]) );

			createPageWithShortcode().then(
				() => option.replaceWith(
					span({
						className: 'frm-generate-page-option',
						text: __( 'Page created', 'formidable-pro' )
					})
				)
			);

			async function createPageWithShortcode() {
				const init = {
					method: 'POST',
					body: formData
				};
				const response = await fetch( ajaxurl + '?action=frm_create_page_with_shortcode', init );
				return await response.json();
			}
		}

		return option;
	}

	function getCheckmark() {
		const checkmark = svg({ href: '#frm_checkmark_icon' });
		checkmark.style.color = '#0DA63C';
		checkmark.style.marginRight = '10px';
		return checkmark;
	}

	function addAction( hookName, callback ) {
		wp.hooks.addAction( hookName, 'formidable', callback );
	}

	function addFilter( hookName, callback ) {
		wp.hooks.addFilter( hookName, 'formidable', callback );
	}
}() );
