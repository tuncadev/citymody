( function() {
	/** globals ajaxurl, wp, frmDom */

	if ( 'undefined' === typeof ajaxurl || 'undefined' === typeof wp || 'undefined' === typeof frmDom ) {
		return;
	}

	const __ = wp.i18n.__;
	const { tag, div, span, a, svg } = frmDom;
	const { maybeCreateModal, footerButton } = frmDom.modal;
	const { onClickPreventDefault } = frmDom.util;
	const { doJsonPost, doJsonFetch } = frmDom.ajax;

	const state = {
		newApplicationName: '',
		applicationNameIsValid: false,
		applicationRequiresViews: false,
		retrievingApplicationMeta: false,
		hasMoreApplications: false
	};

	const elements = {
		customApplicationsGrid: false,
		viewAllButtonWrapper: false
	};

	addFilter( 'frm_view_application_modal_primary_action_button', changeViewApplicationModalPrimaryActionButton );
	addFilter( 'frm_application_card_control', changeCardControl );
	addAction( 'frm_application_index_card', changeCard );
	addAction( 'frm_view_application_modal_content', changeViewApplicationModalContent );
	addAction( 'frm_application_render_templates', addCustomApplicationCardsToPage );

	frmDom.bootstrap.setupBootstrapDropdowns();

	function changeViewApplicationModalPrimaryActionButton( button, { data } = {}) {
		if ( ! frmProApplicationsVars.canAddApplications ) {
			// There is already a Learn More button, so just avoid adding another button if user cannot install application.
			return document.createTextNode( '' );
		}

		if ( ! data.url ) {
			if ( data.forPurchase ) {
				button.classList.add( 'frm-get-it-now-button' );
				button.textContent = getGetItNowText();
			}
			return button;
		}

		const installButton = footerButton({
			text: __( 'Install Application', 'formidable-pro' ),
			buttonType: 'primary'
		});
		installButton.classList.remove( 'dismiss' );

		const handleInstallClick = () => {
			if ( state.applicationRequiresViews && ! frmProApplicationsVars.canAddViews ) {
				installOrActivateVisualViews();
				return;
			}

			if ( state.retrievingApplicationMeta ) {
				alert( __( 'Please wait a moment as application meta is being loaded and try again.', 'formidable-pro' ) );
				return;
			}

			promptForApplicationNameBeforeTemplateInstall( data );
		};

		onClickPreventDefault( installButton, handleInstallClick );
		return installButton;
	}

	function getGetItNowText() {
		return __( 'Get it now', 'formidable-pro' );
	}

	function installOrActivateVisualViews() {
		const action = frmProApplicationsVars.viewsIsUpdated ? 'activate_addon' : 'install_addon';
		const formData = new FormData();
		formData.append( 'plugin', frmProApplicationsVars.viewsIsUpdated ? 'formidable-views/formidable-views.php' : frmProApplicationsVars.viewsInstallUrl );
		formData.append( 'action', 'frm_' + action );
		doInstallOrActivate( formData ).then( data => alert( data ) );

		async function doInstallOrActivate( formData ) {
			formData.append( 'nonce', frmGlobal.nonce );
			const init = {
				method: 'POST',
				body: formData
			};
			const response = await fetch( ajaxurl, init );
			return await response.json();
		}
	}

	function changeCard( card, { data } = {}) {
		if ( data.termId ) {
			card.id = 'frm-application-' + data.termId;
			addLinkToCardSpan( card, data );
		}

		if ( data.icon && ! data.hasLiteThumbnail ) {
			// Set image from API if the lite plugin does not have it in-plugin.
			card.querySelector( 'img' ).setAttribute( 'src', getThumbnailUrl( data.icon ) );
		}

		if ( data.url ) {
			removeLockIconFromCard( card );
		}
	}

	function changeViewApplicationModalContent( container, { data } = {}) {
		const wrapper = container.querySelector( '.frm-application-image-wrapper' );
		if ( wrapper ) {
			wrapper.querySelector( 'img' ).src = data.icon;
		}

		if ( ! data.url ) {
			// No xml to retrieve means the user requires a license upgrade.
			// Don't remove warning, don't try to access XML.
			return;
		}

		const warning = container.querySelector( '.frm_warning_style' );
		if ( warning ) {
			warning.remove();
		}

		const formData = new FormData();
		formData.append( 'xml', data.url );

		state.applicationRequiresViews = false;
		state.retrievingApplicationMeta = true;

		setTimeout( maybeResetModalAndGetTemplateMeta, 0 );

		function maybeResetModalAndGetTemplateMeta() {
			const modal = document.getElementById( 'frm_view_application_modal' );
			if ( modal ) {
				modal.classList.remove( 'frm-has-template-meta' );
			}
			doJsonPost( 'get_application_template_meta', formData ).then( handleTemplateMeta );
		}

		function handleTemplateMeta( data ) {
			const modal = document.getElementById( 'frm_view_application_modal' );
			if ( modal ) {
				modal.classList.add( 'frm-has-template-meta' );
			}

			state.retrievingApplicationMeta = false;

			const details = container.querySelector( '.frm-application-modal-details' );

			if ( data.found.form.length ) {
				details.appendChild(
					div({
						className: 'frm-application-modal-label',
						text: __( 'Forms', 'formidable-pro' )
					})
				);
				details.appendChild( getBasicList( data.found.form ) );
			}

			if ( data.found.view.length ) {
				state.applicationRequiresViews = true;

				if ( ! frmProApplicationsVars.canAddViews && modal ) {
					modal.classList.add( 'frm-application-requires-views' );
					const installButton = modal.querySelector( '.frm_modal_footer .frm-button-primary' );

					if ( frmProApplicationsVars.viewsExists && ! frmProApplicationsVars.viewsIsUpdated ) {
						const warning = div({
							className: 'frm_warning_style',
							text: __( 'Please update Visual Views before installing this Application.', 'formidable-pro' )
						});
						container.insertBefore( warning, container.firstChild );

						if ( installButton ) {
							installButton.remove();
						}
					} else {
						const viewsButtonText = frmProApplicationsVars.viewsIsUpdated ? __( 'Activate Views', 'formidable-pro' ) : __( 'Install Views', 'formidable-pro' );

						if ( installButton ) {
							installButton.textContent = viewsButtonText;
						}

						const anchor = a( viewsButtonText );
						onClickPreventDefault( anchor, installOrActivateVisualViews );

						let warningMessage;
						if ( ! frmProApplicationsVars.viewsExists ) {
							warningMessage = __( 'This application requires Visual Views to be installed.', 'formidable-pro' );
						} else {
							warningMessage = __( 'Visual Views needs to be activated.', 'formidable-pro' );
						}

						const warning = div({
							className: 'frm_warning_style',
							children: [ span( warningMessage ), anchor ]
						});
						container.insertBefore( warning, container.firstChild );
					}
				}

				details.appendChild(
					div({
						className: 'frm-application-modal-label',
						text: __( 'Views', 'formidable-pro' )
					})
				);
				details.appendChild( getBasicList( data.found.view ) );
			}

			if ( data.found.page.length ) {
				details.appendChild(
					div({
						className: 'frm-application-modal-label',
						text: __( 'Pages', 'formidable-pro' )
					})
				);
				details.appendChild( getBasicList( data.found.page ) );
			}
		}
	}

	function getBasicList( data ) {
		return tag(
			'ul',
			{
				className: 'frm-application-item-list',
				children: data.map( text => tag( 'li', text ) )
			}
		);
	}

	function changeCardControl( control, { data } = {}) {
		if ( data.url ) {
			if ( frmProApplicationsVars.canAddApplications ) {
				control.textContent = __( 'Add', 'formidable-pro' );
				control.insertBefore( svg({ href: '#frm_plus_icon' }), control.firstChild );
			}
			return control;
		}

		if ( data.forPurchase ) {
			control.textContent = getGetItNowText();
			return control;
		}

		if ( ! data.editUrl ) {
			return control;
		}

		if ( ! frmProApplicationsVars.canAddApplications ) {
			return document.createTextNode( '' );
		}

		return getDeleteTrigger( data );
	}

	function addCustomApplicationCardsToPage( contentWrapper, { data, customTemplatesNav } = {}) {
		if ( ! customTemplatesNav ) {
			return;
		}

		const customApplicationCards = getCustomApplicationCards( data );
		contentWrapper.insertBefore( customApplicationCards, customTemplatesNav.nextElementSibling );

		if ( frmProApplicationsVars.canAddApplications ) {
			customTemplatesNav.querySelector( 'h2' ).appendChild(
				a({
					href: frmProApplicationsVars.allApplicationsUrl,
					text: __( 'View all applications', 'formidable-pro' )
				})
			);
		}
	}

	function addLinkToCardSpan( card, { name, editUrl }) {
		const span = card.firstChild.firstChild;
		const anchor = a({
			href: editUrl,
			text: name
		});

		span.innerHTML = '';
		span.appendChild( anchor );
	}

	function removeLockIconFromCard( card ) {
		const cardTitle = card.querySelector( 'h4' );
		if ( ! cardTitle ) {
			return;
		}

		card.classList.remove( 'frm-locked-application-template' );

		const svg = cardTitle.querySelector( 'svg' );
		if ( ! svg ) {
			return;
		}

		svg.remove();
	}

	function getDeleteTrigger( data ) {
		const deleteOption = a({
			className: 'frm-delete-application-trigger',
			child: svg({ href: '#frm_delete_icon' })
		});
		/* translators: %s: Application name */
		const ariaLabel = __( 'Delete Application %s', 'formidable-pro' ).replace( '%s', data.name );
		deleteOption.setAttribute( 'aria-label', ariaLabel );
		deleteOption.setAttribute( 'title', __( 'Delete Application', 'formidable-pro' ) );
		jQuery( deleteOption ).tooltip();
		onClickPreventDefault( deleteOption, () => deleteApplication( data.termId ) );
		return deleteOption;
	}

	function deleteApplication( termId ) {
		const hookName = 'frm_trigger_delete_application_modal';
		wp.hooks.doAction( hookName, termId, () => fadeAfterDelete( termId ) );
	}

	function fadeAfterDelete( termId ) {
		const card = document.getElementById( 'frm-application-' + termId );
		if ( ! card ) {
			return;
		}

		const grid = card.parentNode;
		jQuery( card ).fadeOut( 'slow', removeCardAfterFadeOut );

		function removeCardAfterFadeOut() {
			card.remove();
			deleteAllTooltips();

			if ( ! grid.children.length ) {
				addPlaceholderAfterLastCardIsDeleted();
				return;
			}

			refreshCustomApplications();
		}

		function addPlaceholderAfterLastCardIsDeleted() {
			const noApplicationsPlaceholder = getNoApplicationsPlaceholder();
			if ( false !== noApplicationsPlaceholder ) {
				grid.parentNode.insertBefore( noApplicationsPlaceholder, grid );
			}
			grid.remove();
		}
	}

	function deleteAllTooltips() {
		Array.from( document.getElementsByClassName( 'tooltip' ) ).forEach( tooltip => tooltip.remove() );
	}

	function refreshCustomApplications() {
		doJsonFetch( 'get_applications_data' ).then( renderFreshApplicationData );

		function renderFreshApplicationData( data ) {
			const container = elements.customApplicationsGrid;
			Array.from( container.children ).forEach(
				child => child.classList.contains( 'frm-application-card' ) && child.remove()
			);

			state.hasMoreApplications = data.moreApplications;
			if ( ! state.hasMoreApplications && elements.viewAllButtonWrapper ) {
				elements.viewAllButtonWrapper.remove();
				elements.viewAllButtonWrapper = false;
			}

			data.applications.forEach(
				application => container.appendChild( getCustomApplicationCard( application ) )
			);

			if ( elements.viewAllButtonWrapper ) {
				container.appendChild( elements.viewAllButtonWrapper );
			}
		}
	}

	function getNoApplicationsPlaceholder() {
		const hookName = 'frm_no_applications_placeholder';
		return wp.hooks.applyFilters( hookName, false );
	}

	function getCustomApplicationCards({ applications, moreApplications }) {
		if ( ! applications.length ) {
			const noApplicationsPlaceholder = getNoApplicationsPlaceholder();
			return false === noApplicationsPlaceholder ? [] : noApplicationsPlaceholder;
		}

		const container = div({
			id: 'frm_custom_applications_grid',
			className: 'frm_grid_container frm-application-cards-grid'
		});
		elements.customApplicationsGrid = container;

		applications.forEach(
			application => container.appendChild( getCustomApplicationCard( application ) )
		);

		if ( moreApplications && frmProApplicationsVars.canAddApplications ) {
			state.hasMoreApplications = true;
			elements.viewAllButtonWrapper = div({
				className: 'frm-view-all-applications-button-wrapper',
				child: getViewAllButton()
			});
			container.appendChild( elements.viewAllButtonWrapper );
		}

		return container;
	}

	function getViewAllButton() {
		return a({
			className: 'button-secondary frm-button-secondary',
			text: __( 'View All Applications', 'formidable-pro' ),
			href: frmProApplicationsVars.allApplicationsUrl
		});
	}

	function getCustomApplicationCard( data ) {
		let card = div();
		const hookName = 'frm_application_card';
		const args     = { data };
		card = wp.hooks.applyFilters( hookName, card, args );

		card.appendChild( getUpdatedAtTimestamp( data.updatedAt ) );

		card.addEventListener( 'mouseenter', highlightCard );
		card.addEventListener( 'mouseleave', removeCardHighlight );

		if ( frmProApplicationsVars.canAddApplications ) {
			const deleteTrigger = card.querySelector( '.frm-delete-application-trigger' );
			// If the card is clicked, and it isn't the delete trigger, trigger the link in the title to view the application.
			card.addEventListener( 'click', event => event.target !== deleteTrigger && card.querySelector( 'h4 a' ).click() );

			deleteTrigger.addEventListener( 'mouseenter', removeCardHighlight );
			deleteTrigger.addEventListener( 'mouseleave', highlightCard );
		}

		function highlightCard() {
			card.classList.add( 'frm-highlight-card' );
		}

		function removeCardHighlight() {
			card.classList.remove( 'frm-highlight-card' );
		}

		return card;
	}

	function getUpdatedAtTimestamp( updatedAt ) {
		const date = new Date( updatedAt * 1000 );
		const options = {
			year: 'numeric',
			month: 'short',
			day: 'numeric'
		};
		const dateDescription = date.toLocaleDateString( getLocale(), options );

		const className = 'frm-updated-at-timestamp';

		/** translators: %s: a date, (ie. Feb 22, 2022) */
		const text = __( 'Updated %s' ).replace( '%s', dateDescription );

		return div({ className, text });
	}

	function getLocale() {
		const [ locale ] = document.getElementsByTagName( 'html' )[0].getAttribute( 'lang' ).split( '-' );
		return locale;
	}

	function promptForApplicationNameBeforeTemplateInstall( data ) {
		state.newApplicationName = data.name;
		state.applicationNameIsValid = false;
		maybeCreateModal(
			'frm_view_application_modal',
			{
				title: __( 'Create new application', 'formidable-pro' ),
				content: getApplicationNamePromptContent( data ),
				footer: getApplicationNamePromptFooter( data )
			}
		);
	}

	function getApplicationNamePromptContent( data ) {
		const inputId = 'frm_application_prompt_name_input';
		const labelText = __( 'Application name', 'formidable-pro' );
		const inputName = 'application_name';
		const inputWrapper = getLabelledInput( inputId, labelText, inputName );

		inputWrapper.querySelector( 'label' ).style.lineHeight = '1.5';

		const input = inputWrapper.querySelector( 'input' );
		input.value = data.name;

		const hookName = 'frm_add_application_name_validation';
		const onValidationCallback = ({ name, valid }) => {
			state.newApplicationName = name;
			state.applicationNameIsValid = valid;
		};
		const hookArgs = { onValidationCallback };
		wp.hooks.doAction( hookName, inputWrapper, hookArgs );

		const container = div({
			children: [ inputWrapper ]
		});
		container.style.padding = '20px 40px';

		return container;
	}

	function getApplicationNamePromptFooter( data ) {
		const installButton = footerButton({
			text: __( 'Install Application', 'formidable-pro' ),
			buttonType: 'primary',
			noDismiss: true
		});
		onClickPreventDefault( installButton, () => installTemplate( data ) );
		return div({ child: installButton });
	}

	function getLabelledInput( inputId, labelText, inputName ) {
		const label = tag( 'label', labelText );
		label.setAttribute( 'for', inputId );

		const input = tag(
			'input',
			{
				id: inputId,
				className: 'frm_long_input'
			}
		);
		input.type = 'text';
		input.setAttribute( 'name', inputName );

		return div({ children: [ label, input ] });
	}

	function installTemplate( data ) {
		if ( ! applicationFromTemplateNameIsValid() ) {
			return;
		}

		const modal = maybeCreateModal(
			'frm_view_application_modal',
			{ content: getInstallSpinner() }
		);
		centerSpinner( modal );

		const formData = new FormData();
		formData.append( 'xml', data.url );
		formData.append( 'application_name', state.newApplicationName );
		formData.append( 'csv_files', 1 );
		doJsonPost( 'install_template', formData ).then( handleTemplateInstallSuccess );
	}

	function applicationFromTemplateNameIsValid() {
		const input = document.getElementById( 'frm_application_prompt_name_input' );

		const hookName = 'frm_get_application_name_validation_message';
		const hookArgs = {
			input,
			name: state.newApplicationName,
			isValid: state.applicationNameIsValid
		};
		const message = wp.hooks.applyFilters( hookName, '', hookArgs );

		if ( '' === message ) {
			return true;
		}

		alert( message );
		return false;
	}

	function handleTemplateInstallSuccess( response ) {
		const modal = maybeCreateModal(
			'frm_view_application_modal',
			{
				content: getInstallSuccessContent( response.applicationSummary ),
				footer: getInstallSuccessFooter( response.redirect )
			}
		);
		modal.querySelector( '.frm_modal_content' ).style.display = 'block';
	}

	function centerSpinner( modal ) {
		const modalContent = modal.querySelector( '.frm_modal_content' );
		modalContent.style.display = 'flex';
		modalContent.style.justifyContent = 'center';
	}

	function getInstallSpinner() {
		const spinner = div({ className: 'frm-wait' });
		spinner.style.transform = 'scale(3)';
		spinner.style.width = '50px';
		spinner.style.height = '50px';
		return spinner;
	}

	function getInstallSuccessContent( summary ) {
		const container = div();
		container.style.padding = '30px 40px';

		const { applicationId } = summary;
		const map = { form: summary.form, view: summary.view, page: summary.page };

		Object.keys( map ).forEach( addTypeToContainer );
		function addTypeToContainer( type ) {
			map[ type ].forEach( ({ id, name }) => container.appendChild( getSummaryItem( name, type, id, applicationId ) ) );
		}

		return container;
	}

	function getSummaryItem( label, type, objectId, applicationId ) {
		const hookName = 'frm_application_summary_item';
		const hookArgs = { label, type, objectId, applicationId, checked: true };
		return wp.hooks.applyFilters( hookName, document.createTextNode( '' ), hookArgs );
	}

	function getInstallSuccessFooter( applicationUrl ) {
		const installButton = footerButton({
			text: __( 'Open Application', 'formidable-pro' ),
			buttonType: 'primary'
		});
		installButton.classList.remove( 'dismiss' );
		installButton.setAttribute( 'href', applicationUrl );
		return div({ child: installButton });
	}

	function getThumbnailUrl( url ) {
		const iconSplit = url.split( '.' );
		const ext = iconSplit.pop();
		const filename = iconSplit.pop();
		iconSplit.push( filename + '-400x200', ext );
		return iconSplit.join( '.' );
	}

	function addFilter( hookName, callback ) {
		wp.hooks.addFilter( hookName, 'formidable', callback );
	}

	function addAction( hookName, callback ) {
		wp.hooks.addAction( hookName, 'formidable', callback );
	}
}() );
