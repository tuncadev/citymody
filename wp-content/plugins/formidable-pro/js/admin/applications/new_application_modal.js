/**
 * This file is loaded on pages that include the button to create a new Application.
 */
( function() {
	/** globals ajaxurl, wp, frmDom, frmCommonApplicationVars */

	if ( 'undefined' === typeof ajaxurl || 'undefined' === typeof wp || 'undefined' === typeof frmDom ) {
		return;
	}

	const __ = wp.i18n.__;
	const { tag, div, span, a, svg, img } = frmDom;
	const { doJsonFetch, doJsonPost } = frmDom.ajax;
	const { onClickPreventDefault } = frmDom.util;
	const { maybeCreateModal, footerButton } = frmDom.modal;
	const { newSearchInput } = frmDom.search;

	const state = {
		applicationName: '',
		applicationNameIsValid: false,
		newItems: {
			form: [],
			view: []
		},
		itemsPending: 0,
		redirect: ''
	};

	const elements = {
		nameInput: false,
		modal: false,
		noExistingItemsSearchResultsPlaceholder: {
			form: false,
			view: false
		}
	};

	initialize();

	function initialize() {
		document.addEventListener( 'click', handleClickEvents );
	}

	function handleClickEvents( event ) {
		if ( event.target.classList.contains( 'frm-new-application-button' ) ) {
			event.preventDefault();
			triggerNewApplicationModal();
		}
	}

	function triggerNewApplicationModal() {
		state.applicationNameIsValid = false;
		state.newItems.form = [];
		state.newItems.view = [];
		elements.modal = maybeCreateModal(
			'frm_new_application_modal',
			{
				title: __( 'Create new application', 'formidable-pro' ),
				content: getNewApplicationModalContent(),
				footer: false
			}
		);
		elements.modal.classList.add( 'frm_common_modal' );
	}

	function getNewApplicationModalContent() {
		const inputId = 'frm_new_application_modal_name_input';
		const labelText = __( 'Application name', 'formidable-pro' );
		const inputName = 'application_name';
		const inputWrapper = getLabelledInput( inputId, labelText, inputName );

		elements.nameInput = inputWrapper.querySelector( '#frm_new_application_modal_name_input' );

		const hookName = 'frm_add_application_name_validation';
		const onValidationCallback = ({ name, valid }) => {
			state.applicationName = name;
			state.applicationNameIsValid = valid;
		};
		const hookArgs = { onValidationCallback };
		wp.hooks.doAction( hookName, inputWrapper, hookArgs );

		const container = div({
			className: 'inside',
			children: [
				inputWrapper,
				getNewApplicationOption(
					/* %s: conditional "and views" text. */
					__( 'Add existing forms%s', 'formidable-pro' ).replace( '%s', getConditionalAndViewsText() ),
					/* %s: conditional "and views" text. */
					__( 'Select existing forms%s to add to the application', 'formidable-pro' ).replace( '%s', getConditionalAndViewsText() ),
					svg({ href: '#frm_folder_icon' }),
					handleAddExistingClick
				),
				getNewApplicationOption(
					__( 'Create empty application', 'formidable-pro' ),
					/* %s: conditional "and views" text. */
					__( 'Add or create forms%s later', 'formidable-pro' ).replace( '%s', getConditionalAndViewsText() ),
					svg({ href: '#frm_plus_folder_icon' }),
					handleEmptyApplicationClick
				),
				getImportApplicationOption()
			]
		});

		if ( ! frmNewApplicationModalVars.canAddViews ) {
			container.appendChild( getViewsUpsell() );
		}

		/**
		 * Trigger the Add Forms Step.
		 *
		 * @returns void
		 */
		function handleAddExistingClick() {
			if ( ! nameIsValid() ) {
				return;
			}

			elements.noExistingItemsSearchResultsPlaceholder.form = false;
			elements.modal = maybeCreateModal(
				'frm_new_application_modal',
				{
					title: __( 'Add forms to your application', 'formidable-pro' ),
					content: getAddExistingItemsContent( 'form' ),
					footer: getAddExistingFormsFooter()
				}
			);
		}

		function handleEmptyApplicationClick() {
			if ( nameIsValid() ) {
				createApplication( data => window.location.href = data.redirect );
			}
		}

		function nameIsValid() {
			const hookName = 'frm_get_application_name_validation_message';
			const hookArgs = {
				input: elements.nameInput,
				name: state.applicationName,
				isValid: state.applicationNameIsValid
			};
			const message = wp.hooks.applyFilters( hookName, '', hookArgs );

			if ( '' === message ) {
				return true;
			}

			alert( message );
			return false;
		}

		function getAddExistingViewsFooter() {
			const continueButton = footerButton({
				text: 'Continue',
				buttonType: 'primary'
			});
			onClickPreventDefault( continueButton, triggerItemSummaryStep );
			return div({
				className: 'frm-relative',
				children: [
					getBackActionTrigger( handleAddExistingClick ),
					getPageCounter( 3 ),
					continueButton
				]
			});
		}

		function triggerItemSummaryStep() {
			if ( noNewItemsWereSelected() ) {
				handleEmptyApplicationClick();
				return;
			}

			const currentPageNumber = frmNewApplicationModalVars.canAddViews ? 4 : 3;
			elements.modal = maybeCreateModal(
				'frm_new_application_modal',
				{
					/* %s: conditional "and views" text. */
					title: __( 'Embed forms%s', 'formidable-pro' ).replace( '%s', getConditionalAndViewsText() ),
					content: getAddExistingItemsSummaryContent(),
					footer: div({
						className: 'frm-relative',
						child: getPageCounter( currentPageNumber )
					})
				}
			);
		}

		function getAddExistingFormsFooter() {
			const continueButton = footerButton({
				text: 'Continue',
				buttonType: 'primary'
			});
			onClickPreventDefault( continueButton, triggerAddViewsStep );
			return div({
				className: 'frm-relative',
				children: [
					getBackActionTrigger( triggerNewApplicationModal ),
					getPageCounter( 2 ),
					continueButton
				]
			});
		}

		function triggerAddViewsStep() {
			if ( shouldSkipViewsStep() ) {
				triggerItemSummaryStep();
				return;
			}

			elements.noExistingItemsSearchResultsPlaceholder.view = false;
			elements.modal = maybeCreateModal(
				'frm_new_application_modal',
				{
					title: __( 'Add views to your application', 'formidable-pro' ),
					content: getAddExistingItemsContent( 'view' ),
					footer: getAddExistingViewsFooter()
				}
			);
		}

		return container;
	}

	function getAddExistingItemsContent( type ) {
		const container = div({ className: 'inside' });

		doJsonFetch( 'get_application_item_options&type=' + type ).then( addItemsToExistingItemModalPage );

		function addItemsToExistingItemModalPage( data ) {
			container.appendChild(
				div({ child: getExistingItemSearch( type ) })
			);
			data.options.forEach( addOptionToContainer );
			offsetModalY( elements.modal, '50px' );
		}

		function addOptionToContainer( optionData ) {
			const hookName = 'frm_application_item_option';
			const hookArgs = { optionData, handleCheckboxChange };
			wp.hooks.doAction( hookName, container, hookArgs );

			function handleCheckboxChange( event ) {
				const checkbox = event.target;

				if ( checkbox.checked ) {
					if ( ! state.newItems[ type ].find( item => item.value === optionData.value ) ) {
						state.newItems[ type ].push( optionData );
					}
				} else {
					state.newItems[ type ] = state.newItems[ type ].filter( item => item.value !== optionData.value );
				}
			}
		}

		return container;
	}

	function getExistingItemSearch( type ) {
		const id = 'frm_new_application_items_search';
		const placeholder = 'form' === type ? __( 'Search forms', 'formidable-pro' ) : __( 'Search views', 'formidable-pro' );
		const targetClassName = 'frm-application-item-option';
		const args = { handleSearchResult: args => handleExistingItemSearch( args, type ) };
		return newSearchInput( id, placeholder, targetClassName, args );
	}

	function handleExistingItemSearch({ foundSomething, notEmptySearchText }, type ) {
		let placeholder = elements.noExistingItemsSearchResultsPlaceholder[ type ];

		if ( false === placeholder ) {
			placeholder = getNoExistingItemsResultsPlaceholder( type );
			elements.noExistingItemsSearchResultsPlaceholder[ type ] = placeholder;
			const searchWrapper = document.getElementById( 'frm_new_application_items_search' ).closest( 'p.frm-search' ).parentNode;
			searchWrapper.parentNode.insertBefore( placeholder, searchWrapper.nextElementSibling );
		}

		const showNoResults = notEmptySearchText && ! foundSomething;
		placeholder.classList.toggle( 'frm_hidden', ! showNoResults );
	}

	function getNoExistingItemsResultsPlaceholder( type ) {
		let typeText;

		if ( 'form' === type ) {
			typeText = __( 'forms', 'formidable-pro' );
		} else {
			typeText = __( 'views', 'formidable-pro' );
		}

		const placeholder = div({
			/* translators: %s: type of item being searched (ie. forms, views) */
			text: __( 'No %s match your search query.', 'formidable-pro' ).replace( '%s', typeText )
		});
		placeholder.style.margin = '20px';
		return placeholder;
	}

	function getPageCounter( currentPageNumber ) {
		return span({
			className: 'frm-modal-pagecounter',
			/* translators: %1$s: current step number (ie. 2, or 3), %2$s: total number of steps (ie. 3, or 4) */
			text: __( '%1$s of %2$s', 'formidable-pro' ).replace( '%1$s', currentPageNumber ).replace( '%2$s', getNumberOfStepsForExistingItemsOption() )
		});
	}

	function getNumberOfStepsForExistingItemsOption() {
		return frmNewApplicationModalVars.canAddViews ? 4 : 3;
	}

	function getBackActionTrigger( clickHandler ) {
		const trigger = a({
			className: 'button button-secondary frm-modal-cancel',
			text: __( 'Cancel', 'formidable' )
		});
		onClickPreventDefault( trigger, clickHandler );
		return trigger;
	}

	function shouldSkipViewsStep() {
		return ! frmNewApplicationModalVars.canAddViews;
	}

	function noNewItemsWereSelected() {
		return ! state.newItems.form.length && ! state.newItems.view.length;
	}

	function getAddExistingItemsSummaryContent() {
		const container = div({ className: 'inside' });

		createApplication( addItemsAfterApplicationCreation );

		function addItemsAfterApplicationCreation( data ) {
			state.redirect = data.redirect;
			state.itemsPending = state.newItems.form.length + state.newItems.view.length;
			const termId = data.term_id;
			state.newItems.form.forEach(
				form => container.appendChild( getSummaryItem( termId, form, 'form' ) )
			);
			state.newItems.view.forEach(
				view => container.appendChild( getSummaryItem( termId, view, 'view' ) )
			);
		}

		function getSummaryItem( termId, itemData, type ) {
			const hookName = 'frm_application_summary_item';
			let { label } = itemData;
			if ( '' === label ) {
				label = __( '(no title)', 'formidable' );
			}

			const hookArgs = { label, type, objectId: itemData.value, applicationId: termId };
			const item = wp.hooks.applyFilters( hookName, document.createTextNode( '' ), hookArgs );

			const callback = () => {
				item.insertBefore( getCheckmark(), item.firstChild );
				if ( 0 === --state.itemsPending ) {
					addOpenApplicationButtonToModalFooterOnComplete();
				}
			};
			const itemId = itemData.value;
			addToApplication( termId, itemId, type, callback );
			return item;
		}

		return container;
	}

	function addOpenApplicationButtonToModalFooterOnComplete() {
		const footer = elements.modal.querySelector( '.frm_modal_footer' );
		const openButton = footerButton({
			text: __( 'Open Application', 'formidable-pro' ),
			buttonType: 'primary',
			noDismiss: true
		});
		onClickPreventDefault( openButton, () => window.location.href = state.redirect );
		footer.appendChild( openButton );
	}

	function addToApplication( termId, itemId, type, callback ) {
		const formData = new FormData();
		formData.append( 'term_id', termId );
		formData.append( 'item_id', itemId );
		formData.append( 'type', type );
		doJsonPost( 'add_to_application', formData ).then( callback );
	}

	function getCheckmark() {
		const checkmark = svg({ href: '#frm_checkmark_icon' });
		checkmark.style.color = '#0DA63C';
		return checkmark;
	}

	function getViewsUpsell() {
		return div({
			className: 'frm-views-upsell',
			children: [
				getVisualViewsIcon(),
				tag( 'p', {
					className: 'frm-m-0',
					children: [
						document.createTextNode( __( 'Build custom, drag-and-drop layouts to show any collected form entries.', 'formidable-pro' ) ),
						a({
							className: 'button frm-button-secondary',
							text: __( 'Get Views', 'formidable-pro' ),
							href: frmNewApplicationModalVars.viewsUpgradeUrl
						})
					]
				})
			]
		});
	}

	function getVisualViewsIcon() {
		return div({
			child: img({ src: frmCommonApplicationVars.proImagesUrl + 'visual-views.svg' })
		});
	}

	function getConditionalAndViewsText() {
		if ( frmNewApplicationModalVars.canAddViews ) {
			return ' ' + __( 'and views', 'formidable-pro' );
		}
		return '';
	}

	function createApplication( callback ) {
		const formData = new FormData();
		formData.append( 'application_name', state.applicationName );
		doJsonPost( 'create_application', formData )
			.then( callback )
			.catch( error => alert( error ) );
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

	function getNewApplicationOption( title, description, icon, clickHandler ) {
		const option = div({
			className: 'frm-new-application-option frm-border-info-box',
			children: [
				span({ className: 'frm-new-application-option-icon-wrapper frm-icon-wrapper', child: icon }),
				div({
					children: [
						tag( 'h3', { text: title }),
						tag( 'p', {
							text: description,
							className: 'frm-m-0'
						})
					]
				}),
				span({ className: 'caret rotate-270' })
			]
		});
		option.setAttribute( 'tabindex', 0 );
		option.setAttribute( 'role', 'button' );
		option.addEventListener( 'click', clickHandler );
		return option;
	}

	function getImportApplicationOption() {
		return tag( 'p', {
			children: [
				span( __( 'Already built your application on another server?', 'formidable-pro' ) ),
				document.createTextNode( ' ' ),
				getImportApplicationLink()
			]
		});
	}

	function getImportApplicationLink() {
		return a({
			text: __( 'Import Application', 'formidable-pro' ),
			href: frmNewApplicationModalVars.importUrl
		});
	}

	function offsetModalY( modal, amount ) {
		const position = {
			my: 'top',
			at: 'top+' + amount,
			of: window
		};
		jQuery( modal ).dialog( 'option', 'position', position );
	}
}() );
