( function() {
	/* globals frmDom, wp, jQuery, Dropzone, frmStylerFunctions, frmProStyleSettingsSVGs */
	'use strict';

	if ( 'object' !== typeof window.frmStylerFunctions ) {
		return;
	}

	const { getCardByStyleId, getStyleInputNameModalContent, trackUnsavedChange, stylerModal } = window.frmStylerFunctions;
	const isListPage = document.getElementsByClassName( 'frm-style-card' ).length > 0;

	const elements = {
		templateCssTag: false // Keep track of this so we can remove the previous tag when adding a new one.
	};
	let abortController;

	initPreview();

	if ( isListPage ) {
		initListPage();
	} else {
		initEditPage();
	}

	/**
	 * @returns {void}
	 */
	function initListPage() {
		const newStyleTrigger           = document.getElementById( 'frm_new_style_trigger' );
		const { div, a, span, success } = frmDom;
		const { footerButton }          = frmDom.modal;
		const { onClickPreventDefault } = frmDom.util;
		const { doJsonPost }            = frmDom.ajax;
		const { __ }                    = wp.i18n;
		const newStyleUrl               = newStyleTrigger.dataset.newStyleUrl;

		onClickPreventDefault( newStyleTrigger, handleNewStyleTriggerClick );
		wp.hooks.addFilter( 'frm_style_card_dropdown_options', 'formidable', addDropdownOptionsToStyleCard );
		wp.hooks.addAction( 'frm_style_card_click', 'formidable', handleCardClick );
		addApplyButtonEventListener();

		/**
		 * @returns {void}
		 */
		function handleNewStyleTriggerClick() {
			stylerModal(
				'frm_new_style_modal',
				{
					title: __( 'Create new style', 'formidable-pro' ),
					content: getStyleInputNameModalContent( 'new' ),
					footer: getNewStyleModalFooter()
				}
			);
		}

		/**
		 * Add Pro style options to the style card dropdown menu.
		 *
		 * @param {Array} options
		 * @param {Object} args {
		 *     @type {Object} data {
		 *         @type {String} styleId
		 *         @type {String} duplicateUrl
		 *         @type {boolean} isTemplate
		 *     }
		 *     @type {Function} addIconToOption
		 * }
		 * @returns {Array}
		 */
		function addDropdownOptionsToStyleCard( options, args ) {
			const { data, addIconToOption, isTemplate } = args;

			if ( isTemplate ) {
				return options;
			}

			// Swap the order a bit. Remove the reset option at the bottom of the list (defined in lite).
			// Then add it back after adding Set as Default and Duplicate.
			const resetOption = options.pop();
			const renameOption = options.pop();

			options.push(
				{ anchor: getSetAsDefaultOption( data.styleId, addIconToOption ), type: 'set-as-default' },
				renameOption,
				{ anchor: getDuplicateOption( data.duplicateUrl, data.styleId, addIconToOption ), type: 'duplicate' },
				resetOption,
				{ anchor: getDeleteOption( data.styleId, addIconToOption ), type: 'delete' }
			);

			return options;
		}

		/**
		 * @param {String} styleId
		 * @param {Function} addIconToOption
		 * @returns {HTMLElement}
		 */
		function getDeleteOption( styleId, addIconToOption ) {
			const deleteOption = a( __( 'Delete', 'formidable-pro' ) );
			addIconToOption( deleteOption, 'frm_delete_icon' );

			onClickPreventDefault(
				deleteOption,
				() => stylerModal(
					'frm_delete_style_modal',
					{
						title: __( 'Delete Style', 'formidable-pro' ),
						content: getDeleteStyleModalContent(),
						footer: getDeleteStyleModalFooter( styleId )
					}
				)
			);

			return deleteOption;
		}

		function getDeleteStyleModalContent() {
			const content = div({
				text: __( 'Permanently delete this style?', 'formidable-pro' ),
				className: 'inside'
			});
			return content;
		}

		/**
		 * @param {String} styleId
		 * @returns {HTMLElement}
		 */
		function getDeleteStyleModalFooter( styleId ) {
			const cancelButton = footerButton({ text: __( 'Cancel', 'formidable-pro' ), buttonType: 'cancel' });
			cancelButton.classList.add( 'dismiss' );

			const deleteButton = footerButton({ text: __( 'Delete Style', 'formidable-pro' ), buttonType: 'red' });
			onClickPreventDefault( deleteButton, () => deleteStyle( styleId ) );

			return div({
				children: [ cancelButton, deleteButton ]
			});
		}

		/**
		 * @param {String} styleId
		 * @returns {void}
		 */
		function deleteStyle( styleId ) {
			const formData = new FormData();
			formData.append( 'id', styleId );
			doJsonPost( 'delete_style', formData ).then(
				() => {
					fadeAndRemoveStyleCard( styleId );
					success( span( __( 'Successfully deleted style', 'formidable-pro' ) ) );
				}
			);
		}

		/**
		 * Styles are deleted with a fetch call so after the card is deleted we also need to hide the deleted style's card.
		 *
		 * @param {String} styleId
		 * @returns {void}
		 */
		function fadeAndRemoveStyleCard( styleId ) {
			const card = getCardByStyleId( styleId );
			if ( ! card ) {
				return;
			}

			if ( card.classList.contains( 'frm-active-style-card' ) ) {
				selectDefaultStyle();
			}

			jQuery( card ).fadeOut(() => {
				card.remove();
				syncCustomPagination();
				maybeDeleteCustomCardWrapper();
			});
		}

		/**
		 * Trigger a click on the default style.
		 * This is required when deleting the selected style. Otherwise nothing would be selected.
		 *
		 * @returns {void}
		 */
		function selectDefaultStyle() {
			const defaultCard = document.getElementById( 'frm_default_style_cards_wrapper' ).querySelector( '.frm-style-card' );
			if ( defaultCard ) {
				defaultCard.click();
			}
		}

		/**
		 * Adjust the pages after a card is deleted.
		 *
		 * @returns {void}
		 */
		function syncCustomPagination() {
			const cardWrapper = document.getElementById( 'frm_custom_style_cards_wrapper' );
			const pagination  = cardWrapper.querySelector( '.frm-style-card-pagination' );
			if ( ! pagination ) {
				return;
			}

			const firstHiddenCard = cardWrapper.querySelector( '.frm-style-card.frm_hidden' );
			if ( ! firstHiddenCard ) {
				return;
			}

			firstHiddenCard.classList.remove( 'frm_hidden' );

			const numberOfHiddenCards = cardWrapper.querySelectorAll( '.frm-style-card.frm_hidden' ).length;
			if ( 0 === numberOfHiddenCards ) {
				pagination.remove();
				return;
			}

			const anchor = pagination.querySelector( '.frm-show-all-styles' );
			anchor.textContent = __( 'Show all (%d)', 'formidable' ).replace( '%d', numberOfHiddenCards );
		}

		/**
		 * After the last card is deleted, we don't want to show an empty custom card wrapper so remove it.
		 *
		 * @returns {void}
		 */
		function maybeDeleteCustomCardWrapper() {
			const cardWrapper = document.getElementById( 'frm_custom_style_cards_wrapper' );
			if ( ! cardWrapper || cardWrapper.querySelector( '.frm-style-card' ) ) {
				// Either the wrapper was already removed, or there are still more cards.
				return;
			}

			if ( cardWrapper.previousElementSibling.classList.contains( 'frm_form_settings' ) ) {
				// Remove the section title before the wrapper as well.
				cardWrapper.previousElementSibling.remove();
			}

			cardWrapper.remove();
		}

		/**
		 * @param {String} duplicateUrl
		 * @param {String} styleId
		 * @param {Function} addIconToOption
		 * @returns
		 */
		function getDuplicateOption( duplicateUrl, styleId, addIconToOption ) {
			const duplicateOption = a( __( 'Duplicate', 'formidable-pro' ) );
			addIconToOption( duplicateOption, 'frm_clone_icon' );

			onClickPreventDefault(
				duplicateOption,
				() => {
					const card         = getCardByStyleId( styleId );
					const titleElement = card.querySelector( '.frm-style-card-title' );
					stylerModal(
						'frm_duplicate_style_modal',
						{
							title: __( 'Duplicate style', 'formidable-pro' ),
							content: getStyleInputNameModalContent( 'duplicate', titleElement.textContent ),
							footer: getDuplicateStyleModalFooter( duplicateUrl )
						}
					);
				}
			);

			return duplicateOption;
		}

		/**
		 * @param {String} duplicateUrl
		 * @returns {HTMLElement}
		 */
		function getDuplicateStyleModalFooter( duplicateUrl ) {
			const cancelButton = footerButton({ text: __( 'Cancel', 'formidable-pro' ), buttonType: 'cancel' });
			cancelButton.classList.add( 'dismiss' );

			const duplicateButton = footerButton({ text: __( 'Duplicate style', 'formidable-pro' ), buttonType: 'primary' });
			onClickPreventDefault(
				duplicateButton,
				() => maybeRedirectToStylerEdit( duplicateUrl, document.getElementById( 'frm_duplicate_style_name_input' ).value )
			);

			return div({
				children: [ cancelButton, duplicateButton ]
			});
		}

		/**
		 * @param {String} styleId
		 * @param {Function} addIconToOption
		 * @returns {HTMLElement}
		 */
		function getSetAsDefaultOption( styleId, addIconToOption ) {
			const setAsDefaultOption = a( __( 'Set as Default', 'formidable-pro' ) );
			addIconToOption( setAsDefaultOption, 'frm_check1_icon' );

			onClickPreventDefault(
				setAsDefaultOption,
				() => {
					const formData = new FormData();
					formData.append( 'style_id', styleId );
					doJsonPost( 'set_style_as_default', formData ).then(
						() => {
							moveDefaultStyle( styleId );
							success( span( __( 'Successfully set style as default', 'formidable-pro' ) ) );
						}
					);
				}
			);

			return setAsDefaultOption;
		}

		/**
		 * Move the default style into the default category. The old default goes into custom styles (in its old place).
		 *
		 * @param {String} styleId
		 * @returns {void}
		 */
		function moveDefaultStyle( styleId ) {
			const defaultWrapper = document.getElementById( 'frm_default_style_cards_wrapper' );
			const customWrapper  = document.getElementById( 'frm_custom_style_cards_wrapper' );
			const currentDefault = defaultWrapper.querySelector( '.frm-style-card' );
			const newDefaultCard = getCardByStyleId( styleId );

			customWrapper.insertBefore( currentDefault, newDefaultCard );
			defaultWrapper.appendChild( newDefaultCard );
		}

		/**
		 * @returns {HTMLElement}
		 */
		function getNewStyleModalFooter() {
			const createStyleButton = footerButton({
				text: __( 'Create new style', 'formidable-pro' ),
				buttonType: 'primary'
			});
			createStyleButton.setAttribute( 'disabled', 'disabled' );
			createStyleButton.classList.remove( 'dismiss' );
			onClickPreventDefault( createStyleButton, handleCreateStyleButtonClick );
			const cancelButton = footerButton({
				text: __( 'Cancel', 'formidable-pro' ),
				buttonType: 'cancel'
			});
			cancelButton.classList.add( 'dismiss' );
			return div({ children: [ cancelButton, createStyleButton ] });
		}

		/**
		 * @returns {void}
		 */
		function handleCreateStyleButtonClick() {
			maybeRedirectToStylerEdit( newStyleUrl, document.getElementById( 'frm_new_style_name_input' ).value );
		}

		/**
		 * Redirect to edit a new style.
		 *
		 * @param {String} url The url we're redirecting to. It is either a path to the new style action, or the duplicate action.
		 * @param {String} styleName The name of the new style.
		 * @returns {void}
		 */
		function maybeRedirectToStylerEdit( url, styleName ) {
			if ( '' === styleName ) {
				// Avoid redirecting with an empty name.
				// The button gets disabled on an input event when the name is empty.
				return;
			}

			window.location.href = addFormIdToRedirectUrl( addStyleNameToRedirectUrl( url, styleName ) );
		}

		/**
		 * @param {String} url
		 * @param {String} styleName
		 * @returns {String}
		 */
		function addStyleNameToRedirectUrl( url, styleName ) {
			return url + '&style_name=' + encodeURIComponent( styleName );
		}

		/**
		 * @param {String} url
		 * @returns {String}
		 */
		function addFormIdToRedirectUrl( url ) {
			const params = new URLSearchParams( document.location.search );
			const formId = params.get( 'form' );

			if ( ! formId || isNaN( formId ) ) {
				return url;
			}

			return url + '&form=' + parseInt( formId );
		}

		/**
		 * @param {Object} args
		 * @returns {void}
		 */
		function handleCardClick( args ) {
			const { card, styleIdInput }   = args;
			const isTemplate = 'undefined' !== typeof card.dataset.templateKey;

			if ( ! isTemplate ) {
				syncApplyButtonText( __( 'Apply style', 'formidable-pro' ) );
				// This uses a hook that handles custom cards as well, so exit early if the card is not a template.
				return;
			}

			syncApplyButtonText( __( 'Install and apply style', 'formidable-pro' ) );

			styleIdInput.value = card.dataset.templateKey;
			showTemplateInPreview( card.dataset.templateKey );
		}

		/**
		 * @returns {void}
		 */
		function syncApplyButtonText( text ) {
			const applyButton = document.getElementById( 'frm_apply_style' );
			if ( applyButton ) {
				applyButton.querySelector( '.frm-apply-button-text' ).textContent = text;
			}
		}

		/**
		 * @param {String} key
		 * @returns {void}
		 */
		function showTemplateInPreview( key ) {
			const formData = new FormData();
			formData.append( 'template_key', key );

			const preview = document.getElementById( 'frm_style_preview' );
			preview.classList.add( 'frm-loading-style-template' );

			if ( 'undefined' !== typeof abortController && 'function' === typeof abortController.abort && ! abortController.signal.aborted ) {
				// Abort the previous fetch request if we click to preview a template and the previous request has not finished.
				abortController.abort();
			}

			abortController = new AbortController(); // Create a new abort controller because the old one has been aborted.

			const args = { signal: abortController.signal };
			doJsonPost( 'preview_style_template', formData, args )
				.then( handleTemplatePreviewData )
				.catch( () => {} ); // .catch is triggered when aborted. We don't need to handle this as it is aborted intentionally.
		}

		/**
		 * Use the style settings from the template XML and pass them to the frm_change_styling action.
		 * This will return the CSS tag that matches those settings, which we add to the document head.
		 *
		 * @param {Object} response
		 * @returns {void}
		 */
		async function handleTemplatePreviewData( response ) {
			const formData = new FormData();
			formData.append( 'action', 'frm_change_styling' );
			formData.append( 'style_name', 'frm_style_frm_style_template' ); // All templates use the frm_style_template post_name.
			formData.append( 'nonce', frmGlobal.nonce );

			const keys = Object.keys( response.settings );
			keys.forEach( key => formData.append( 'frm_style_setting[post_content][' + key + ']', response.settings[ key ] ) );

			const init = {
				method: 'POST',
				body: formData
			};
			const cssResponse = await fetch( ajaxurl, init );
			const newCssTag   = await cssResponse.text();

			const preview = document.getElementById( 'frm_style_preview' );
			preview.classList.remove( 'frm-loading-style-template' );

			const domParser = new DOMParser();
			const doc       = domParser.parseFromString( newCssTag, 'text/html' );
			const styleTag  = doc.querySelector( 'style' );

			if ( ! styleTag ) {
				// The response is invalid to don't change the HTML.
				return;
			}

			if ( false !== elements.templateCssTag && 'function' === typeof elements.templateCssTag.remove ) {
				elements.templateCssTag.remove();
			}

			elements.templateCssTag = styleTag;
			document.head.appendChild( styleTag );
			toggleSampleFormClass( 'frm_style_frm_style_template' );
		}

		/**
		 * @returns {void}
		 */
		function addApplyButtonEventListener() {
			const applyButton = document.getElementById( 'frm_apply_style' );
			if ( ! applyButton ) {
				return;
			}

			applyButton.addEventListener(
				'click',
				() => document.getElementById( 'frm-publishing' ).querySelector( 'button' ).click()
			);
		}
	}

	/**
	 * Set up the logic required only for the edit view.
	 * This is specific to the inputs in the sidebar including background images and datepicker themes.
	 *
	 * @returns {void}
	 */
	function initEditPage() {
		function setupEventListeners() {
			jQuery( document ).on( 'change', 'input.frm_image_id[name="frm_style_setting[post_content][bg_image_id]"]', onBgImageUpload );

			const frmFieldset = document.getElementById( 'frm_fieldset' );
			if ( frmFieldset ) {
				jQuery( frmFieldset ).on( 'change', handleReset );
			}

			jQuery( 'select[name$="[theme_selector]"]' ).on( 'change', handleThemeChange ).trigger( 'change' );

			const styleIsNew = '' === document.getElementById( 'frm_styling_form' ).querySelector( 'input[name="ID"]' ).value;
			if ( styleIsNew ) {
				// Show the unsaved changes pop up on load when creating a new style and when duplicating as a style isn't created right away.
				trackUnsavedChange();
			}
		}

		function maybeAddWithBgImageClass() {
			if ( backgroundImageIsSet() ) {
				toggleSampleFormClass( 'frm_with_bg_image', true );
			}
		}

		/**
		 * @returns {boolean}
		 */
		function backgroundImageIsSet() {
			const bgImageInput = document.querySelector( 'input[name="frm_style_setting[post_content][bg_image_id]"]' );
			return bgImageInput && '' !== bgImageInput.value;
		}

		/**
		 * Update the form preview after a background image is uploaded.
		 *
		 * @returns {void}
		 */
		function onBgImageUpload() {
			trackUnsavedChange();

			const fileId = parseInt( this.value );
			const show   = 0 !== fileId;
			toggleSampleFormClass( 'frm_with_bg_image', show );
			toggleAdditionalBgImageSettings( show );
		}

		/**
		 * Show the Image Opacity option when a background image is set.
		 *
		 * @param {boolean} show
		 * @returns {void}
		 */
		function toggleAdditionalBgImageSettings( show ) {
			document.querySelectorAll( '.frm_bg_image_additional_settings' ).forEach(
				setting => setting.classList.toggle( 'frm_hidden', ! show )
			);
		}

		/**
		 * Handle a reset event (called in the edit view only).
		 * Reset the background image upload input when the style is reset.
		 *
		 * @returns {void}
		 */
		function handleReset() {
			const bgImageIdField = document.querySelector( 'input.frm_image_id[name="frm_style_setting[post_content][bg_image_id]"]' );
			if ( bgImageIdField && '' !== bgImageIdField.value ) {
				resetBackgroundImage( bgImageIdField );
			}
		}

		function resetBackgroundImage( bgImageIdField ) {
			bgImageIdField.nextElementSibling.querySelector( '.frm_remove_image_option' ).click();
			toggleAdditionalBgImageSettings( false );
			toggleSampleFormClass( 'frm_with_bg_image', false );
		}

		/**
		 * @returns {false}
		 */
		function handleThemeChange() {
			const themeVal = jQuery( this ).val();
			let css        = themeVal;

			if ( themeVal !== -1 ) {
				if ( themeVal === 'ui-lightness' && frm_admin_js.pro_url !== '' ) {
					css = frm_admin_js.pro_url + '/css/ui-lightness/jquery-ui.css';
					jQuery( '.frm_date_color' ).show();
				} else {
					css = frm_admin_js.jquery_ui_url + '/themes/' + themeVal + '/jquery-ui.css';
					jQuery( '.frm_date_color' ).hide();
				}
			}

			updateUICSS( css );
			document.getElementById( 'frm_theme_css' ).value = themeVal;
			return false;
		}

		/**
		 * Function to append a new theme stylesheet with the new style changes.
		 */
		function updateUICSS( locStr ) {
			if ( locStr == -1 ) {
				jQuery( 'link.ui-theme' ).remove();
				return false;
			}

			const $cssLink = jQuery( '<link href="' + locStr + '" type="text/css" rel="Stylesheet" class="ui-theme" />' );
			jQuery( 'head' ).append( $cssLink );

			const $link = jQuery( 'link.ui-theme' );
			if ( $link.length > 1 ) {
				$link.first().remove();
			}
		}

		maybeAddWithBgImageClass();

		if ( 'function' === typeof wp.domReady ) {
			wp.domReady( setupEventListeners );
			return;
		}
	}

	/**
	 * Initialize common functions required for the preview in both the edit and list views.
	 *
	 * @returns {void}
	 */
	function initPreview() {
		initializeDatepickerFieldsOnFocus();
		initializeInlineDatepickers();
		initializeDropzoneFields();
		initializeStarRatingFields();
		initializeSVGIcons();

		/**
		 * Initialize datepickers as formidablepro.js does not get loaded in the visual styler.
		 *
		 * @returns {void}
		 */
		function initializeDatepickerFieldsOnFocus() {
			document.querySelectorAll( '.frm_date' ).forEach(
				/**
				 * @param {HTMLElement} dateField
				 * @returns {void}
				 */
				dateField => {
					if ( dateField.classList.contains( '.frm_date_inline' ) ) {
						// Inline datepickers get initialized on load, not on focus.
						return;
					}

					dateField.addEventListener(
						'focusin',
						() => {
							initializeDatepicker( dateField );
							jQuery( dateField ).datepicker( 'show' );
						}
					);
				}
			);
		}

		/**
		 * @returns {void}
		 */
		function initializeInlineDatepickers() {
			document.querySelectorAll( '.frm_date_inline' ).forEach(
				inlineDatepicker => {
					inlineDatepicker.classList.add( 'frm-datepicker' ); // Give the inline datepicker Formidable styling.
					initializeDatepicker( inlineDatepicker );
				}
			);
		}

		/**
		 * @param {HTMLElement|String} target
		 * @returns {void}
		 */
		function initializeDatepicker( target ) {
			jQuery( target ).datepicker({
				changeMonth: true,
				changeYear: true,
				beforeShow: function( _, options ) {
					if ( options.dpDiv ) {
						options.dpDiv.addClass( 'frm-datepicker' );
					}
					return options;
				}
			});
		}

		/**
		 * Check preview for dropzoen fields and initialize them so they aren't just type="file" input fields.
		 *
		 * @returns {void}
		 */
		function initializeDropzoneFields() {
			if ( 'function' !== typeof wp.domReady ) {
				return;
			}

			wp.domReady(
				() => {
					document.getElementById( 'frm_style_preview' ).querySelectorAll( '.frm_dropzone' ).forEach(
						/**
						 * Initialize dropzone for a file field.
						 * Then immediately remove its event listeners as it's for display only.
						 *
						 * @param {HTMLElement} dropzoneField
						 * @returns {void}
						 */
						dropzoneField => {
							const url          = '/file/post'; // The url is required but it does not matter as we are disabling Dropzone.
							const dropzoneArgs = { url };
							const dropzone     = new Dropzone( 'div#' + dropzoneField.id, dropzoneArgs );

							// Calling removeEventListeners disables upload (both from click and drag).
							dropzone.removeEventListeners();
						}
					);
				}
			);
		}

		/**
		 * Make star rating fields interactive. As formidablepro.js is not loaded in the visual styler, we need to fill in that functionality for the preview.
		 *
		 * @returns {void}
		 */
		function initializeStarRatingFields() {
			const starGroups = document.querySelectorAll( '.frm-star-group' );
			if ( ! starGroups.length ) {
				return;
			}

			starGroups.forEach( initializeStarGroup );

			/**
			 * Event event listeners for a star rating field.
			 *
			 * @param {HTMLElement} starGroup
			 * @returns {void}
			 */
			function initializeStarGroup( starGroup ) {
				starGroup.querySelectorAll( 'input' ).forEach(
					input => {
						input.addEventListener( 'click', () => updateStars( input ) );
						input.addEventListener( 'mouseenter', () => updateStars( input ) );
					}
				);

				starGroup.querySelectorAll( '.star-rating' ).forEach(
					star => {
						if ( star.classList.contains( 'star-rating-readonly' ) ) {
							return;
						}

						star.addEventListener( 'mouseenter', () => updateStars( star.previousSibling ) );
						star.addEventListener( 'mouseleave', unhoverStars.bind( star ) );
					}
				);
			}

			/**
			 * @param {HTMLElement} hovered
			 * @returns {void}
			 */
			function updateStars( hovered ) {
				const starGroup = hovered.parentElement;
				const current   = parseInt( hovered.value );
				let selectLabel = false;

				starGroup.classList.add( 'frm-star-hovered' );
				Array.from( starGroup.children ).forEach(
					star => {
						if ( star.classList.contains( 'star-rating' ) ) {
							if ( selectLabel ) {
								star.classList.add( 'star-rating-hover' );
							} else {
								star.classList.remove( 'star-rating-hover', 'star-rating-on' );
							}
							return;
						}

						selectLabel = parseInt( star.value ) <= current;
					}
				);
			}

			/**
			 * @returns {void}
			 */
			function unhoverStars() {
				/*jshint validthis:true */
				const input         = this.previousSibling;
				const starGroup     = input.parentElement;
				const stars         = starGroup.children;
				const selectedInput = starGroup.querySelector( 'input:checked' );
				const selected      = selectedInput ? selectedInput.getAttribute( 'id' ) : false;
				let isSelected      = '';

				starGroup.classList.remove( 'frm-star-hovered' );

				for ( let i = stars.length - 1; i > 0; i-- ) {
					const star = stars[ i ];
					if ( ! star.classList.contains( 'star-rating' ) ) {
						continue;
					}

					star.classList.remove( 'star-rating-hover' );

					if ( isSelected === '' && star.getAttribute( 'for' ) === selected ) {
						isSelected = 'star-rating-on';
					}

					if ( isSelected !== '' ) {
						star.classList.add( isSelected );
					}
				}
			}
		} // End initializeStarRatingFields.

		function initializeSVGIcons() {
			// Collapse icon position.
			frmDom.util.documentOn( 'change', '#frm_collapse_pos', function( event ) {
				const wrapperEls = document.querySelectorAll( '.frm_section_heading .frm_trigger' );
				if ( ! wrapperEls ) {
					return;
				}

				const position = event.target.value;

				wrapperEls.forEach( wrapperEl => {
					const svgs = wrapperEl.querySelectorAll( '.frmsvg' );
					svgs.forEach( svg => {
						if ( 'before' === position ) {
							wrapperEl.prepend( ' ' );
							wrapperEl.prepend( svg );
						} else {
							wrapperEl.append( ' ' );
							wrapperEl.append( svg );
						}
					});
				});
			});

			/**
			 * Live update Repeater and Section icons.
			 */
			function changeRepeaterAndCollapseSVGIcon() {
				if ( 'object' !== typeof frmProStyleSettingsSVGs ) {
					return;
				}

				const svgIcons = frmProStyleSettingsSVGs;

				/**
				 * Changes the SVG icon when changing icon setting from Styles page.
				 *
				 * @param {String} inputSelector  CSS selector of the setting input.
				 * @param {String} svgTagSelector CSS selector of the <svg> tag of the SVG icon.
				 * @param {String} iconNameFormat Icon name format, contains `{key}`, which is replaced by the setting value.
				 */
				function changeSVGIcon( inputSelector, svgTagSelector, iconNameFormat ) {
					frmDom.util.documentOn( 'change', inputSelector, function( event ) {
						if ( ! event.target.checked ) {
							return;
						}

						const svgs = document.querySelectorAll( svgTagSelector );
						if ( ! svgs ) {
							return;
						}

						const iconKey= event.target.value && '0' !== event.target.value ? event.target.value : '';
						const newIconKey = iconNameFormat.replace( '{key}', iconKey );

						if ( ! svgIcons[ newIconKey ] ) {
							return;
						}

						svgs.forEach( svg => {
							const newSvg = createElementFromString( svgIcons[ newIconKey ] );
							newSvg.setAttribute( 'width', '1em' );
							newSvg.setAttribute( 'height', '1em' );
							svg.replaceWith( newSvg );
						});
					});
				}

				const createElementFromString = ( str ) => {
					const placeholder = document.createElement( 'div' );
					placeholder.innerHTML = str;
					return placeholder.firstElementChild;
				};

				// Add row icon.
				changeSVGIcon(
					'input[name="frm_style_setting[post_content][repeat_icon]"]',
					'.frm_repeat_buttons .frm_add_form_row svg',
					'frm_plus{key}_icon'
				);

				// Remove row icon.
				changeSVGIcon(
					'input[name="frm_style_setting[post_content][repeat_icon]"]',
					'.frm_repeat_buttons .frm_remove_form_row svg',
					'frm_minus{key}_icon'
				);

				// Collapse icon.
				changeSVGIcon(
					'input[name="frm_style_setting[post_content][collapse_icon]"]',
					'.frm_section_heading .frm_trigger svg',
					'frm_arrowdown{key}_icon'
				);

				function syncTriggerIconsWithSetting() {
					const checkedRadio = document.querySelector( 'input[name="frm_style_setting[post_content][repeat_icon]"]:checked' );
					if ( ! checkedRadio ) {
						return;
					}
					const event = new Event( 'change', {
						bubbles: true,
						cancelable: true
					});

					checkedRadio.dispatchEvent( event );
				}

				syncTriggerIconsWithSetting();
			}
			changeRepeaterAndCollapseSVGIcon();
		}
	}

	/**
	 * @param {String} className
	 * @param {boolean} toggleOn
	 * @returns {void}
	 */
	function toggleSampleFormClass( className, toggleOn ) {
		getSampleForms().forEach( formContainer => formContainer.querySelector( '.frm-show-form' ).classList.toggle( className, toggleOn ) );
	}

	/**
	 * @returns {Array<HTMLElement>}
	 */
	function getSampleForms() {
		return document.getElementById( 'frm_style_preview' ).querySelectorAll( '.frm_forms.with_frm_style' );
	}
}() );
