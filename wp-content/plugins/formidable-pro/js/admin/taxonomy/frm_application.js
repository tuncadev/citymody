/**
 * This file is loaded on the "My Application" page for managing an application.
 * This code is responsible for:
 *  - adding items to an application
 *  - listing, searching, and removing from the list of items in a table
 *  - syncing the application
 *  - exporting an application
 *  - renaming the application
 *  - jumping to other applications via a dropdown
 *  - triggering embed pop ups for forms and views
 */
( function() {
	/* globals frmApplicationTerm, frmDom, wp, frmCommonApplicationVars */

	if ( 'undefined' === typeof frmApplicationTerm || 'undefined' === typeof frmDom || 'undefined' === typeof wp ) {
		return;
	}

	const __ = wp.i18n.__;
	const { tag, div, span, a, svg, img, setAttributes } = frmDom;
	const { doJsonPost, doJsonFetch } = frmDom.ajax;
	const { maybeCreateModal, footerButton } = frmDom.modal;
	const { newSearchInput } = frmDom.search;
	const { onClickPreventDefault } = frmDom.util;
	const container = document.getElementById( 'frm_edit_application_container' );

	if ( ! container ) {
		return;
	}

	const state = {
		dataForApplication: false,
		newItems: [],
		rows: [],
		applicationNameIsValid: true,
		addedNameValidation: false,
		newPageName: '',
		pendingNewItems: 0,
		applicationName: frmApplicationTerm.name
	};
	const id = parseInt( frmApplicationTerm.id );

	const elements = {
		noTableItemSearchResultsPlaceholder: false,
		addExistingItemStepNextButton: false,
		syncSummaryModal: false,
		addItemModal: false
	};

	const table = tag(
		'table',
		{ className: 'wp-list-table widefat fixed striped toplevel_page_formidable' }
	);

	frmDom.bootstrap.setupBootstrapDropdowns();
	prepareApplicationDropdown();
	pullDataForApplication( handleApplicationData );
	addEventListeners();
	addSearchToWrapper();
	initializeSyncTooltip();

	addFilter( 'frm_before_embed_modal', getObjectIdAndKeyForEmbedModal );
	addFilter( 'frm_create_page_with_shortcode_data', addApplicationIdToCreatePageWithShortcodeData );

	function getObjectIdAndKeyForEmbedModal( _, { element }) {
		return [
			element.getAttribute( 'frm-object-id' ),
			element.getAttribute( 'frm-object-key' )
		];
	}

	function addApplicationIdToCreatePageWithShortcodeData( data ) {
		data.application_id = id;
		return data;
	}

	function prepareApplicationDropdown() {
		doJsonFetch( 'get_applications_data&view=applications&sort=alphabet' ).then( maybeReplaceApplicationHeaderWithDropdown );

		function maybeReplaceApplicationHeaderWithDropdown( data ) {
			const applications = data.applications ? data.applications.filter( application => application.termId !== id ) : [];
			if ( ! applications.length ) {
				return;
			}

			const topBar = document.getElementById( 'frm_top_bar' );
			const h1 = topBar.querySelector( 'h1' );
			const anchor = a({ className: 'frm-dropdown-toggle' });

			topBar.classList.add( 'frm-with-dropdown' );
			anchor.setAttribute( 'data-toggle', 'dropdown' );
			h1.appendChild( document.createTextNode( ' ' ) );
			h1.appendChild( svg({ href: '#frm_arrowdown4_icon' }) );

			h1.parentNode.replaceChild( anchor, h1 );
			anchor.appendChild( h1 );

			const dropdownMenu = div({
				className: 'frm-dropdown-menu frm_code_list frm-full-hover frm-inline-modal',
				children: applications.map( getApplicationDropdownItem )
			});
			const searchWrapper = div({
				className: 'dropdown-item frm-with-search',
				child: getApplicationDropdownSearch()
			});

			jQuery( anchor.parentNode ).on( 'show.bs.dropdown', autoFocusSearchOnDropdownShow );

			dropdownMenu.insertBefore( searchWrapper, dropdownMenu.firstChild );
			anchor.parentNode.insertBefore( dropdownMenu, anchor.nextElementSibling );

			function autoFocusSearchOnDropdownShow() {
				setTimeout( () => searchWrapper.querySelector( 'input' ).focus(), 0 );
			}
		}

		function getApplicationDropdownItem( application ) {
			return div({
				className: 'dropdown-item frm-dropdown-form frm-justify-evenly',
				child: a({
					children: [
						document.createTextNode( application.name ),
						span({
							text: '(ID ' + application.termId + ')'
						})
					],
					href: application.editUrl
				})
			});
		}
	}

	function handleClickEvents( event ) {
		const { classList } = event.target;
		if ( classList.contains( 'frm-applications-add-item-button' ) ) {
			event.preventDefault();
			triggerAddItemModal();
		} else if ( classList.contains( 'frm-save-application-settings' ) ) {
			saveApplicationSettings();
		} else if ( classList.contains( 'frm-sync-application-button' ) ) {
			event.preventDefault();
			syncApplication();
		} else if ( classList.contains( 'frm-export-application-button' ) ) {
			event.preventDefault();
			exportApplication();
		}
	}

	function addSearchToWrapper() {
		const searchWrapper = document.getElementById( 'frm_application_item_search_wrapper' );
		if ( searchWrapper ) {
			searchWrapper.appendChild( getItemSearch() );
		}
	}

	function initializeSyncTooltip() {
		const syncButton = document.querySelector( '.frm-sync-application-button' );
		if ( ! syncButton ) {
			return;
		}

		jQuery( syncButton ).tooltip();
	}

	function addEventListeners() {
		document.addEventListener( 'click', handleClickEvents );

		const dropdown = document.getElementById( 'frm_settings_button_wrapper' ).querySelector( '.dropdown' );
		jQuery( dropdown ).on( 'show.bs.dropdown', handleSettingsDropdownShow );
	}

	function handleApplicationData() {
		const data = state.dataForApplication;

		if ( ! data.rows ) {
			container.appendChild( getNoItemsPlaceholder() );
			return;
		}

		state.rows = data.rows;
		document.getElementById( 'frm_application_header' ).classList.remove( 'frm_hidden' );
		container.appendChild( table );
		addDataToTable( data );
	}

	function getNoItemsPlaceholder() {
		return div({
			id: 'frm_no_application_items_placeholder',
			children: [
				getNoItemsIcon(),
				tag( 'h3', __( 'This application has no items', 'formidable-pro' ) ),
				div( __( 'Add Forms, Views, and Pages in your application', 'formidable-pro' ) ),
				getAddItemButton()
			]
		});
	}

	function getNoItemsIcon() {
		return div({
			child: img({ src: getPathToImage( 'no-items.svg' ) })
		});
	}

	function getPathToImage( fileName ) {
		return frmCommonApplicationVars.proImagesUrl + fileName;
	}

	function getAddItemButton() {
		return a({
			className: 'button-primary frm-button-primary frm-applications-add-item-button',
			text: __( 'Add Item', 'formidable-pro' )
		});
	}

	function handleSettingsDropdownShow( event ) {
		const wrapper = event.target.closest( '#frm_settings_button_wrapper' );
		if ( ! wrapper ) {
			return;
		}

		const input = document.getElementById( 'frm-application-name-input' );
		input.value = state.applicationName;

		if ( ! state.addedNameValidation ) {
			const hookName = 'frm_add_application_name_validation';
			const onValidationCallback = ({ name, valid }) => {
				state.applicationName = name;
				state.applicationNameIsValid = valid;
			};
			const inputWrapper = input.parentNode;
			const hookArgs = {
				applicationId: id,
				onValidationCallback,
				validateOnInit: false
			};
			wp.hooks.doAction( hookName, inputWrapper, hookArgs );

			state.addedNameValidation = true;
		}

		setTimeout( () => input.focus(), 0 );
	}

	function triggerAddItemModal() {
		state.newItems = [];
		elements.addItemModal = maybeCreateModal(
			'frm_application_add_item_modal',
			{
				title: __( 'Add item', 'formidable-pro' ),
				content: getAddItemModalContent(),
				footer: document.createTextNode( '' )
			}
		);
		elements.addItemModal.classList.add( 'frm_common_modal' );
	}

	function getAddItemModalContent() {
		return div({
			className: 'inside',
			children: getAddItemModalOptions()
		});
	}

	function getAddItemModalOptions() {
		const formDetails = { text: getLowerCaseTextForType( 'form' ), type: 'form' };
		const pageDetails = { text: getLowerCaseTextForType( 'page' ), type: 'page' };
		const hookName = 'frm_add_application_item_options';
		const addItemOptions = wp.hooks.applyFilters(
			hookName,
			[ formDetails, pageDetails ]
		);
		return addItemOptions.map( getAddItemModalOption );
	}

	function getLowerCaseTextForType( type ) {
		switch ( type ) {
			case 'form': return __( 'form', 'formidable-pro' );
			case 'view': return __( 'view', 'formidable-pro' );
			case 'page': return __( 'page', 'formidable-pro' );
		}
		return type;
	}

	function getAddItemModalOption({ text, type }) {
		const icon = getIconForType( type );
		/* translators: %s: type (ie. form, view, page) */
		const title = __( 'Add a %s', 'formidable-pro' ).replace( '%s', text );
		const output = div({
			className: 'frm-add-item-modal-option',
			children: [
				span({ className: 'frm-application-item-icon-wrapper frm-icon-wrapper frm-' + type + '-item', child: icon }),
				tag( 'h3', title ),
				getAddItemModalOptionButtons( text, type )
			]
		});
		output.setAttribute( 'tabindex', 0 );
		output.setAttribute( 'role', 'group' );
		return output;
	}

	function getAddItemModalOptionButtons( text, type ) {
		/* translators: %s: type (ie. form, view, page) */
		const newButton = getSecondaryButton( __( 'New %s', 'formidable-pro' ).replace( '%s', text ) );

		newButton.addEventListener( 'click', createNewButtonClickHandler( type ) );

		/* translators: %s: type (ie. form, view, page) */
		const existingButton = getSecondaryButton( __( 'Existing %s', 'formidable-pro' ).replace( '%s', text ) );

		onClickPreventDefault(
			existingButton,
			() => {
				elements.addItemModal = maybeCreateModal(
					'frm_application_add_item_modal',
					{
						title: getAddExistingItemsTitle( type ),
						content: getExistingItemModalPageContent( type ),
						footer: getExistingItemModalPageFooter( type )
					}
				);
			}
		);

		return div({ children: [ newButton, existingButton ] });
	}

	function createNewButtonClickHandler( type ) {
		return function() {
			if ( 'page' === type ) {
				promptForPageName();
			} else {
				const newValue = 1;
				addToApplication( type, newValue, data => window.location.href = data.redirect );
			}
		};
	}

	function promptForPageName() {
		elements.addItemModal = maybeCreateModal(
			'frm_application_add_item_modal',
			{
				title: __( 'What will you call the new page?', 'formidable-pro' ),
				content: getPromptForPageNameContent(),
				footer: getPromptForPageNameFooter()
			}
		);
		elements.addItemModal.querySelector( 'input' ).focus();
	}

	function getPromptForPageNameContent() {
		const wrapper = div({ className: 'field-group' });
		const form = tag( 'form' );

		const createPageWithShortcode = () => {
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'frm_create_page_with_shortcode',
					object_id: state.objectId,
					type: state.type,
					name: input.value,
					nonce: frmGlobal.nonce,
					application_id: id
				},
				dataType: 'json',
				success: function( response ) {
					if ( 'object' === typeof response && 'string' === typeof response.redirect ) {
						window.location.href = response.redirect;
					}
				}
			});
		};

		form.addEventListener(
			'submit',
			function( event ) {
				event.preventDefault();
				createPageWithShortcode();
				return false;
			},
			true
		);

		const title = tag( 'label', __( 'What will you call the new page?', 'formidable' ) );
		title.setAttribute( 'for', 'frm_name_your_page' );
		form.appendChild( title );

		const input = tag(
			'input',
			{ id: 'frm_name_your_page' }
		);
		input.placeholder = __( 'Name your page', 'formidable' );
		input.type = 'text';
		input.addEventListener( 'change', () => state.newPageName = input.value );
		form.appendChild( input );

		wrapper.appendChild( form );
		return wrapper;
	}

	function getPromptForPageNameFooter() {
		const createPageButton = footerButton({
			text: __( 'Create page', 'formidable-pro' ),
			buttonType: 'primary',
			noDismiss: true
		});
		createPageButton.addEventListener(
			'click',
			() => {
				if ( '' === state.newPageName ) {
					alert( __( 'Page name is required', 'formidable-pro' ) );
					return;
				}

				const newValue = 1;
				addToApplication( 'page', newValue, data => window.location.href = data.redirect );
			}
		);
		return div({ child: createPageButton });
	}

	function addToApplication( type, newValue, callback, { itemId } = {}) {
		const formData = new FormData();
		formData.append( 'new', newValue );
		formData.append( 'type', type );
		formData.append( 'term_id', id );
		if ( 'page' === type ) {
			formData.append( 'name', state.newPageName );
		}
		if ( itemId ) {
			formData.append( 'item_id', itemId );
		}
		doJsonPost( 'add_to_application', formData ).then( callback );
	}

	function getAddExistingItemsTitle( type ) {
		let typeText = '';
		switch ( type ) {
			case 'form':
				typeText = __( 'forms', 'formidable-pro' );
				break;
			case 'view':
				typeText = __( 'views', 'formidable-pro' );
				break;
			case 'page':
				typeText = __( 'pages', 'formidable-pro' );
				break;
		}

		/* translators: %s: type of item (plural) being added to form (ie. forms, views, pages) */
		return __( 'Add %s to your application', 'formidable-pro' ).replace( '%s', typeText );
	}

	function getExistingItemModalPageContent( type ) {
		const container = div({ className: 'inside' });

		doJsonFetch( 'get_application_item_options&type=' + type ).then( addItemsToExistingItemModalPage );

		function addItemsToExistingItemModalPage( data ) {
			container.appendChild( getItemSearch( type ) );
			data.options.forEach( addOptionToContainer );
		}

		function addOptionToContainer( optionData ) {
			if ( optionIsAlreadyAssignedToApplication( optionData.value ) ) {
				return;
			}

			const hookName = 'frm_application_item_option';
			const hookArgs = { optionData, handleCheckboxChange };
			wp.hooks.doAction( hookName, container, hookArgs );

			function handleCheckboxChange( event ) {
				const checkbox = event.target;

				if ( ! checkbox.checked ) {
					// Unselect item.
					state.newItems = state.newItems.filter( item => item.value !== optionData.value );
				} else if ( ! state.newItems.find( item => item.value === optionData.value ) ) {
					state.newItems.push( optionData );
				}

				if ( false !== elements.addExistingItemStepNextButton ) {
					elements.addExistingItemStepNextButton.classList.toggle( 'frm_hidden', 0 === state.newItems.length );
				}
			}
		}

		function optionIsAlreadyAssignedToApplication( itemId ) {
			const item = state.rows.find( row => row.itemId === itemId && type === row.type );
			return undefined !== item;
		}

		return container;
	}

	function getItemSearch( modalType ) {
		const inModal = 'string' === typeof modalType;
		const id = inModal ? 'frm-application-item-search-modal-input' : 'frm-application-item-search-input';
		const placeholder = __( 'Search Items', 'formidable-pro' );
		const targetClassName = inModal ? 'frm-application-item-option' : 'frm-application-item-row';

		const args = {};
		if ( inModal ) {
			args.handleSearchResult = args => handleModalItemSearch( modalType, args );
		} else {
			args.handleSearchResult = handleItemTableSearch;
		}

		return newSearchInput( id, placeholder, targetClassName, args );
	}

	function handleModalItemSearch( modalType, { foundSomething, notEmptySearchText }) {
		const modal = elements.addItemModal;

		let placeholder = document.getElementById( 'frm_no_modal_results_placeholder' );
		if ( ! placeholder ) {
			placeholder = getNoModalResultsPlaceholder( modalType );
			modal.querySelector( '.frm-search' ).parentNode.appendChild( placeholder );
		}

		const showTable = ! notEmptySearchText || foundSomething;
		table.classList.toggle( 'frm_hidden', ! showTable );

		const showNoResults = notEmptySearchText && ! foundSomething;
		placeholder.classList.toggle( 'frm_hidden', ! showNoResults );

		setTimeout( () => centerModal( modal ), 0 );
	}

	function centerModal( modal ) {
		const position = {
			my: 'center',
			at: 'center',
			of: window
		};
		jQuery( modal ).dialog({ position });
	}

	function getNoModalResultsPlaceholder( type ) {
		const typeLabel = getLowerCaseTextForType( type );

		/* translators: %s: type (ie. form, view, page) */
		const createNewAnchor = a( __( '+ Create new %s', 'formidable-pro' ).replace( '%s', typeLabel ) );
		createNewAnchor.addEventListener( 'click', createNewButtonClickHandler( type ) );

		const placeholder = div({
			id: 'frm_no_modal_results_placeholder',
			children: [
				div({
					className: 'frm-no-results-icon-wrapper',
					child: getFontIcon( 'frm_report_problem_icon' )
				}),
				/* translators: %s: type (ie. form, view, page) */
				div( __( 'There is no %s that matches your search query', 'formidable-pro' ).replace( '%s', typeLabel ) ),
				createNewAnchor
			]
		});
		return placeholder;
	}

	function getFontIcon( iconClass ) {
		const className = 'frmfont ' + iconClass;
		const args = { className };
		return tag( 'i', args );
	}

	function handleItemTableSearch({ foundSomething, notEmptySearchText }) {
		if ( false === elements.noTableItemSearchResultsPlaceholder ) {
			elements.noTableItemSearchResultsPlaceholder = getNoResultsPlaceholder();
			document.getElementById( 'frm_edit_application_container' ).appendChild( elements.noTableItemSearchResultsPlaceholder );
		}

		const showTable = ! notEmptySearchText || foundSomething;
		table.classList.toggle( 'frm_hidden', ! showTable );

		const showNoResults = notEmptySearchText && ! foundSomething;
		elements.noTableItemSearchResultsPlaceholder.classList.toggle( 'frm_hidden', ! showNoResults );
	}

	function getNoResultsPlaceholder() {
		return div( __( 'No items match your search query.', 'formidable-pro' ) );
	}

	function getExistingItemModalPageFooter( type ) {
		const nextButton = footerButton({
			text: __( 'Next', 'formidable-pro' ),
			buttonType: 'primary',
			noDismiss: true
		});
		nextButton.classList.add( 'frm_hidden' );
		elements.addExistingItemStepNextButton = nextButton;
		nextButton.addEventListener(
			'click',
			event => {
				event.preventDefault();

				if ( ! state.newItems.length ) {
					alert( __( 'Nothing was selected to be added', 'formidable-pro' ) );
					return;
				}

				elements.addItemModal = maybeCreateModal(
					'frm_application_add_item_modal',
					{
						content: getExistingItemProgressPageContent( type ),
						footer: div()
					}
				);
			}
		);
		return div({
			className: 'frm-relative',
			children: [
				getBackActionTrigger(),
				span({
					className: 'frm-modal-pagecounter',
					text: __( '2 of 2', 'formidable-pro' )
				}),
				nextButton,
				div({ className: 'clearfix' })
			]
		});
	}

	function getBackActionTrigger() {
		const trigger = a({
			className: 'button button-secondary frm-modal-cancel',
			text: __( 'Cancel', 'formidable' )
		});
		onClickPreventDefault( trigger, triggerAddItemModal );
		return trigger;
	}

	function getExistingItemProgressPageContent( type ) {
		const content = div({ className: 'inside' });
		state.pendingNewItems = state.newItems.length;
		state.newItems.forEach(
			({ value, label }) => {
				const item = getSummaryItem( label, type, value );
				content.appendChild( item );

				const newValue = 0;
				addToApplication(
					type,
					newValue,
					() => {
						item.insertBefore( getCheckmark(), item.firstChild );
						if ( 0 === --state.pendingNewItems ) {
							refreshTableAndAddModalButtonAfterItemsAreAdded();
						}
					},
					{ itemId: value }
				);
			}
		);

		return content;
	}

	function getSummaryItem( label, type, objectId ) {
		const hookName = 'frm_application_summary_item';
		if ( '' === label ) {
			label = __( '(no title)', 'formidable' );
		}
		const hookArgs = { label, type, objectId, applicationId: id };
		return wp.hooks.applyFilters( hookName, document.createTextNode( '' ), hookArgs );
	}

	function refreshTableAndAddModalButtonAfterItemsAreAdded() {
		refreshTable( elements.addItemModal );
		addDoneButtonToModal( elements.addItemModal );

		const modalFooter = elements.addItemModal.querySelector( '.frm_modal_footer' );
		const addAnotherButton = footerButton({
			text: __( 'Add Another Item', 'formidable-pro' ),
			buttonType: 'secondary',
			noDismiss: true
		});
		onClickPreventDefault( addAnotherButton, triggerAddItemModal );
		modalFooter.insertBefore( addAnotherButton, modalFooter.querySelector( '.button' ) );
	}

	function refreshTable( activeModal ) {
		const placeholder = document.getElementById( 'frm_no_application_items_placeholder' );
		if ( placeholder ) {
			placeholder.remove();
		}

		pullDataForApplication(
			() => {
				table.innerHTML = '';
				handleApplicationData( state.dataForApplication );

				if ( 'undefined' !== typeof activeModal && 'none' !== activeModal.parentNode.style.display ) {
					centerModal( elements.addItemModal );
				}
			}
		);
	}

	function pullDataForApplication( callback ) {
		doJsonFetch( 'get_data_for_application&id=' + id ).then(
			data => {
				state.dataForApplication = data;
				callback();
			}
		);
	}

	function addDoneButtonToModal( modal ) {
		const footer = modal.querySelector( '.frm_modal_footer' );
		const openButton = footerButton({
			text: __( 'Done', 'formidable-pro' ),
			buttonType: 'primary'
		});
		footer.appendChild( openButton );
	}

	function getCheckmark() {
		const checkmark = svg({ href: '#frm_checkmark_icon' });
		checkmark.style.color = '#0DA63C';
		return checkmark;
	}

	function getSecondaryButton( textContent ) {
		return a({
			className: 'button-secondary frm-button-secondary',
			text: textContent
		});
	}

	function addDataToTable( data ) {
		addHeadersToTable();
		const children = data.rows.map( rowData => getTableRow( rowData ) );
		const tableBodyArgs = { children };
		table.appendChild( tag( 'tbody', tableBodyArgs ) );
	}

	function addHeadersToTable() {
		const headers = [
			__( 'Name', 'formidable-pro' ),
			__( 'Type', 'formidable-pro' ),
			__( 'Parent of', 'formidable-pro' ),
			__( 'Embedded in', 'formidable-pro' ),
			__( 'Embed', 'formidable-pro' )
		];
		table.appendChild(
			tag(
				'thead',
				{
					child: tr({
						children: headers.map( getHeaderElement )
					})
				}
			)
		);
	}

	function tr( args ) {
		return tag( 'tr', args );
	}

	function getHeaderElement( text ) {
		return th({ text, className: 'manage-column' });
	}

	function th( args ) {
		return tag( 'th', args );
	}

	function getTableRow( rowData ) {
		const className = 'frm-application-item-row';
		const children = [
			getNameCell( rowData ),
			td( rowData.typeLabel ),
			td({ child: getParentOfCellContents( rowData ) }),
			td({ child: getEmbeddedInCellContents( rowData ) }),
			td({ child: maybeGetEmbedButton( rowData ) })
		];
		const row = tr({ className, children });
		row.setAttribute( 'frm-search-text', rowData.name.toLowerCase() );
		return row;
	}

	function getNameCell( rowData ) {
		const text = '' === rowData.name ? __( '(no title)', 'formidable' ) : rowData.name;
		const firstRowChildren = [
			getWrappedIconForItemType( rowData.type, rowData.descriptiveType ),
			canEditItem( rowData ) ?
				a({
					text,
					href: rowData.editUrl
				}) :
				span( text )
		];

		if ( rowData.isDraft ) {
			firstRowChildren.push(
				span({ className: 'frm-is-draft', text: '— ' + __( 'Draft', 'formidable-pro' ) })
			);
		}

		return td({
			children: [
				div({
					className: 'frm-display-flex',
					children: firstRowChildren
				}),
				getRowActions( rowData )
			]
		});
	}

	function canEditItem( rowData ) {
		if ( 'repeater form' === rowData.descriptiveType ) {
			return false;
		}
		if ( 'form' === rowData.type ) {
			return frmCommonApplicationVars.canEditForms;
		}
		if ( 'view' === rowData.type ) {
			return frmCommonApplicationVars.canEditViews;
		}
		if ( 'page' === rowData.type ) {
			return frmCommonApplicationVars.canEditPages;
		}
		return true;
	}

	function td( args ) {
		return tag( 'td', args );
	}

	function getWrappedIconForItemType( type, descriptiveType ) {
		return span({
			className: 'frm-application-item-icon-wrapper frm-icon-wrapper frm-' + type + '-item',
			child: getIconForType( descriptiveType )
		});
	}

	function getIconForType( descriptiveType ) {
		const icon = frmApplicationTerm.icons[ descriptiveType ];
		if ( ! icon ) {
			return document.createTextNode( '' );
		}
		return svg({ href: '#' + icon });
	}

	function getRowActions( rowData ) {
		const editAnchor = a({
			text: __( 'Edit', 'formidable-pro' ),
			href: rowData.editUrl
		});

		const removeAnchor = a( __( 'Remove from application', 'formidable-pro' ) );
		onClickPreventDefault(
			removeAnchor,
			() => {
				const formData = new FormData();
				formData.append( 'term_id', id );
				formData.append( 'item_id', rowData.itemId );
				formData.append( 'type', rowData.type );
				doJsonPost( 'remove_from_application', formData ).then( fadeAndRemoveRowOnRemoveSuccess );

				const row = removeAnchor.closest( 'tr' );

				function fadeAndRemoveRowOnRemoveSuccess() {
					state.rows = state.rows.filter( row => row !== rowData );
					jQuery( row ).fadeOut( 'slow', removeRowAfterFade );
				}

				function removeRowAfterFade() {
					const tbody = row.parentNode;
					row.remove();

					if ( ! tbody ) {
						return;
					}

					if ( 0 === tbody.children.length ) {
						if ( container.contains( table ) ) {
							container.removeChild( table );
						}
						document.getElementById( 'frm_application_header' ).classList.add( 'frm_hidden' );
						container.appendChild( getNoItemsPlaceholder() );
					}
				}
			}
		);

		const children = [];

		if ( canEditItem( rowData ) ) {
			children.push(
				span({
					className: 'edit',
					child: editAnchor
				})
			);
			children.push( document.createTextNode( ' | ' ) );
		}

		children.push( span({ child: removeAnchor }) );

		return div({
			className: 'row-actions',
			children
		});
	}

	function getParentOfCellContents( rowData ) {
		return getRelationshipTagsForCell( 'frm-parent-of', rowData.parentOf );
	}

	function getRelationshipTagsForCell( className, data ) {
		const numberToShowInCell = 2;
		const output = div({
			className,
			children: data.slice( 0, numberToShowInCell ).map( data => getRelationshipTag( data, false ) )
		});
		if ( data.length > numberToShowInCell ) {
			const moreTag = getRelationshipTag({ label: '...' }, true );
			moreTag.classList.add( 'frm-more-relationships-tag', 'frm-dropdown-trigger' );
			moreTag.setAttribute( 'data-toggle', 'dropdown' );

			const dropdownMenu = div({
				className: 'frm-dropdown-menu',
				children: data.slice( numberToShowInCell ).map(
					data => div({
						className: 'dropdown-item',
						child: getRelationshipTag( data, true )
					})
				)
			});

			output.classList.add( 'frm-with-more-tag' );
			output.appendChild( moreTag );
			output.appendChild( dropdownMenu );
		}
		return output;
	}

	function getApplicationDropdownSearch() {
		const id = 'frm-application-dropdown-search-input';
		const placeholder = __( 'Search applications', 'formidable-pro' );
		const targetClassName = 'frm-dropdown-form';
		return newSearchInput( id, placeholder, targetClassName );
	}

	function getRelationshipTag({ type, label }, disableTooltip ) {
		const children = [];
		if ( type ) {
			children.push( span( type ) );
		}

		children.push( span(  '' === label ? __( '(no title)', 'formidable' ) : label ) );
		const className = 'frm-application-relationship-tag frm-grey-tag';
		const output = span({ className, children });

		if ( '' !== label && ! disableTooltip ) {
			output.setAttribute( 'title', label );
			output.classList.add( 'frm_bstooltip' );
			jQuery( output ).tooltip();
		}

		return output;
	}

	function getEmbeddedInCellContents( rowData ) {
		return getRelationshipTagsForCell( 'frm-embedded-in', rowData.embeddedIn );
	}

	function maybeGetEmbedButton( rowData ) {
		const { itemId, itemKey } = rowData;

		if ( ! shouldShowEmbedButton( rowData ) ) {
			return document.createTextNode( '' );
		}

		const button = a({
			text: __( 'Embed', 'formidable-pro' ),
			className: 'button frm-button-sm frm-button-secondary ' + 'frm-embed-' + rowData.type
		});
		setAttributes(
			button,
			{
				tabindex: 0,
				role: 'button',
				'frm-object-id': itemId,
				'frm-object-key': itemKey
			}
		);

		return button;
	}

	function shouldShowEmbedButton( rowData ) {
		const { type, descriptiveType, embeddedIn } = rowData;
		return 'page' !== type && 'embedded form' !== descriptiveType && 'repeater form' !== descriptiveType && 0 === embeddedIn.length;
	}

	function saveApplicationSettings() {
		if ( ! nameIsValid() ) {
			return;
		}

		const formData = new FormData();

		formData.append( 'term_id', id );
		formData.append( 'name', state.applicationName );

		doJsonPost( 'save_application_settings', formData ).then( handleApplicationSave );

		function handleApplicationSave() {
			document.getElementById( 'frm_bs_dropdown' ).querySelector( 'h1 span' ).textContent = state.applicationName;

			const toast = div( __( 'Application successfully saved!', 'formidable-pro' ) );
			toast.style.margin = '10px';
			success( toast );
		}
	}

	function nameIsValid() {
		const input = document.getElementById( 'frm-application-name-input' );

		const hookName = 'frm_get_application_name_validation_message';
		const hookArgs = {
			input,
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

	function syncApplication() {
		const formData = new FormData();
		formData.append( 'application_id', id );

		const content = div({
			child: div({ className: 'frm-wait' })
		});
		const modal = maybeCreateModal(
			'frm_sync_application_modal',
			{
				title: __( 'Syncing application', 'formidable-pro' ),
				content,
				footer: document.createTextNode( ' ' )
			}
		);
		elements.syncSummaryModal = modal;
		modal.classList.add( 'frm_common_modal' );
		doJsonPost( 'sync_application', formData ).then( handleSyncSuccess );

		function handleSyncSuccess( data ) {
			renderSyncSummary( content, data );
			refreshTable();
			addDoneButtonToModal( modal );
		}
	}

	function renderSyncSummary( container, data ) {
		container.innerHTML = '';

		const formSummary = data.summary.form;
		const viewSummary = data.summary.view;
		const pageSummary = data.summary.page;

		const pageIds = Object.keys( pageSummary );
		const viewIds = Object.keys( viewSummary );
		const formIds = Object.keys( formSummary );

		if ( ! formIds.length && ! viewIds.length && ! pageIds.length ) {
			renderEmptySyncSummary( container );
		} else {
			formIds.forEach( addSummaryForForm );
			viewIds.forEach( addSummaryForView );
			pageIds.forEach( addSummaryForPage );
		}

		centerModal( elements.syncSummaryModal );

		function addSummaryForForm( formId ) {
			addSummaryForType( formSummary[ formId ], 'form' );
		}

		function addSummaryForView( viewId ) {
			addSummaryForType( viewSummary[ viewId ], 'view' );
		}

		function addSummaryForPage( pageId ) {
			addSummaryForType( pageSummary[ pageId ], 'page' );
		}

		function addSummaryForType({ name, matches }, type ) {
			const item = getSummaryItem( name, type );
			item.insertBefore( getCheckmark(), item.firstChild );
			container.appendChild( item );

			const ul = tag( 'ul' );
			matches.forEach( getAddMatchToListFunction( ul ) );

			item.parentNode.insertBefore( ul, item.nextElementSibling );
		}

		function getAddMatchToListFunction( ul ) {
			return ({ type, objectId, description }) => {
				const name = getNameOfItem( type, objectId );
				if ( ! name ) {
					return;
				}

				const children = [
					svg({ href: '#frm_dashed_list_icon' }),
					span( description.replace( '%s', name ) )
				];
				ul.appendChild( li({ children }) );
			};
		}
	}

	function li( args ) {
		return tag( 'li', args );
	}

	function getNameOfItem( type, objectId ) {
		if ( ! state.dataForApplication.rows ) {
			return false;
		}

		const row = state.dataForApplication.rows.find( row => row.type === type && row.itemId === objectId );
		if ( ! row ) {
			return false;
		}

		return row.name;
	}

	function renderEmptySyncSummary( container ) {
		[
			img({ src: getPathToImage( 'empty-sync.svg' ) }),
			tag( 'h3', __( 'Your application is up to date', 'formidable-pro' ) ),
			span( __( 'No new items were found.', 'formidable-pro' ) )
		].forEach( child => container.appendChild( child ) );
		centerModal( elements.syncSummaryModal );
	}

	function exportApplication() {
		const form = tag( 'form' );
		form.setAttribute( 'method', 'post' );
		form.setAttribute( 'action', ajaxurl );

		addField( 'action', 'frm_export_xml' );
		addField( 'export-xml', frmApplicationTerm.exportNonce );
		addField( 'format', 'application_xml' );
		addField( 'type[]', 'applications' );
		addField( 'application_id', id );

		function addField( name, value ) {
			const field = tag( 'input' );
			field.setAttribute( 'name', name );
			field.value = value;
			form.appendChild( field );
		}

		document.body.appendChild( form );
		form.submit();
	}

	function addFilter( hookName, callback ) {
		wp.hooks.addFilter( hookName, 'formidable', callback );
	}

	function success( content ) {
		const container = document.getElementById( 'wpbody' );
		const notice = div({
			className: 'notice notice-info frm-review-notice frm_updated_message',
			child: div({
				className: 'frm-satisfied',
				child: content
			})
		});
		notice.style.borderRadius = '4px';
		notice.style.right = '10px';
		notice.style.bottom = '10px';
		container.appendChild( notice );
		setTimeout( () => jQuery( notice ).fadeOut( () => notice.remove() ), 2000 );
	}
}() );
