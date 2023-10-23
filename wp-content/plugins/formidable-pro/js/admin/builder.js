( function() {

	/** globals wp */

	const hooks = wp.hooks;

	function addEventListeners() {
		document.addEventListener( 'change', handleChangeEvent );
	}

	hooks.addFilter( 'frm_conditional_logic_field_options', 'formidable-pro', updateFieldOptions );

	function updateFieldOptions( fieldOptions, hookArgs ) {
		if ( 'scale' === hookArgs.type ) {
			fieldOptions = getScaleFieldOptions( hookArgs.fieldId );
		}
		return fieldOptions;
	}

	function getScaleFieldOptions( fieldId ) {
		let opts = [];
		const optVals = document.querySelectorAll( 'input[name^="item_meta[' + fieldId + ']"]' );

		optVals.forEach( opt => {
			opts.push( opt.value );
		});

		return opts;
	}

	function updateConditionalLogicsDependentOnThis( target ) {
		setTimeout( function() {
			let fieldId = target.closest( '.frm-single-settings' ).dataset.fid;

			if ( ! fieldId ) {
				return;
			}

			frmAdminBuild.adjustConditionalLogicOptionOrders( fieldId, 'scale' );
		}, 0 );
	}

	function handleChangeEvent( e ) {
		const target = e.target;
		if ( target.matches( '.frm_scale_opt' ) ) {
			updateConditionalLogicsDependentOnThis( target );
		}

		if ( isACurrencySetting( target ) ) {
			const settingsContainer = target.closest( '.frm-type-range' );
			syncSliderFieldAfterCurrencyChange( settingsContainer.getAttribute( 'data-fid' ) );
			return;
		}

		if ( 'INPUT' === target.nodeName && 'checkbox' === target.type ) {
			handleCheckboxToggleEvent( e );
		} else if ( target.classList.contains( 'frm_scale_opt' ) ) {
			setScaleValues( target );
		}

		validateTimeFieldRangeValue( target );
	}

	function isACurrencySetting( input ) {
		return input.closest( '.frm_custom_currency_options_wrapper' ) && input.closest( '.frm-type-range' );
	}

	function handleCheckboxToggleEvent( e ) {
		const element = e.target;
		const name = element.name;

		if ( nameMatchesCurrenyOption( name ) ) {
			const calcBox = element.closest( '[id^="frm-calc-box-"]' );
			if ( calcBox ) {
				syncCalcBoxSettingVisibility( calcBox );
			} else {
				const settings = element.closest( '.frm-single-settings' );
				if ( null !== settings && settings.classList.contains( 'frm-type-range' ) ) {
					syncSliderFormatSettingVisiblity( settings );
				}
			}
		}
	}

	function nameMatchesCurrenyOption( name ) {
		return -1 !== name.indexOf( 'field_options[calc_type_' ) ||
			-1 !== name.indexOf( 'field_options[is_currency_' ) ||
			-1 !== name.indexOf( 'field_options[custom_currency_' );
	}

	function syncCalcBoxSettingVisibility( calcBox ) {
		const typeToggle = calcBox.querySelector( '[name^="field_options[calc_type_"]' );
		const isMathType = ! typeToggle.checked;
		const decimalPlacesWrapper = calcBox.querySelector( '.frm_calc_dec' ).closest( '.frm_form_field' );
		const formatAsCurrencyOption = calcBox.querySelector( '[name^="field_options[is_currency_"]' );
		const isCurrency = formatAsCurrencyOption.checked;

		toggle( decimalPlacesWrapper, isMathType && ! isCurrency );
		syncCustomFormatSettings( calcBox, isMathType );
	}

	function syncSliderFormatSettingVisiblity( settingsContainer ) {
		syncCustomFormatSettings( settingsContainer, true );

		const fieldId = settingsContainer.getAttribute( 'data-fid' );
		syncSliderFieldAfterCurrencyChange( fieldId );
	}

	function syncSliderFieldAfterCurrencyChange( fieldId ) {
		const fieldPreview = document.getElementById( 'frm_field_id_' + fieldId );
		const range = fieldPreview.querySelector( 'input[type="range"]' );
		updateSliderFieldPreview({
			field: range,
			att: 'value',
			newValue: range.value
		});
	}

	function syncCustomFormatSettings( container, showSettings ) {
		const formatAsCurrencyOption = container.querySelector( '[name^="field_options[is_currency_"]' );
		const formatAsCurrencyWrapper = formatAsCurrencyOption.closest( '.frm_form_field' );
		const isCustomCurrencyCheckbox = container.querySelector( '[name^="field_options[custom_currency_"]' );
		const isCustomCurrency = isCustomCurrencyCheckbox.checked;
		const customCurrencyCheckboxWrapper = isCustomCurrencyCheckbox.closest( '.frm_form_field' );
		const isCurrency = formatAsCurrencyOption.checked;
		const customCurrenyOptionsWrapper = container.querySelector( '.frm_custom_currency_options_wrapper' );
		const wasCustomCurrency = ! customCurrenyOptionsWrapper.classList.contains( 'frm_hidden' );

		toggle( formatAsCurrencyWrapper, showSettings );
		toggle( customCurrencyCheckboxWrapper, showSettings && isCurrency );
		toggle( customCurrenyOptionsWrapper, showSettings && isCurrency && isCustomCurrency );

		if ( ! wasCustomCurrency && isCustomCurrency ) {
			setCustomCurrencyDefaultsToMatchDefaultCurrency( container );
		}
	}

	function setCustomCurrencyDefaultsToMatchDefaultCurrency( container ) {
		const settings = [
			'custom_decimals',
			'custom_decimal_separator',
			'custom_thousand_separator',
			'custom_symbol_left',
			'custom_symbol_right'
		];
		settings.forEach( updateCustomCurrencySettingToMatchDefault );

		function updateCustomCurrencySettingToMatchDefault( setting ) {
			container.querySelector( '[name^="field_options[' + setting + '_"]' ).value = frmProBuilderVars.currency[ setting.replace( 'custom_', '' ) ];
		}
	}

	function setScaleValues( target ) {
		const fieldID = target.id.replace( 'scale_maxnum_', '' ).replace( 'scale_minnum_', '' ).replace( 'frm_step_', '' );
		let min = document.getElementById( 'scale_minnum_' + fieldID ).value;
		let max = document.getElementById( 'scale_maxnum_' + fieldID ).value;

		updateScaleValues( parseInt( min, 10 ), parseInt( max, 10 ), fieldID );
	}

	function updateScaleValues( min, max, fieldID ) {
		const container = jQuery( '#field_' + fieldID + '_inner_container .frm_form_fields' );
		container.html( '' );
		let step = parseInt( document.getElementById( 'frm_step_' + fieldID ).value, 10 );
		if ( step < 1 ) {
			step = 1;
		}

		if ( min >= max ) {
			max = min + 1;
		}

		for ( let i = min; i <= max; i = i + step ) {
			container.append( '<div class="frm_scale"><label><input type="hidden" name="field_options[options_' + fieldID + '][' + i + ']" value="' + i + '"> <input type="radio" name="item_meta[' + fieldID + ']" value="' + i + '"> ' + i + ' </label></div>' );
		}
		container.append( '<div class="clear"></div>' );
	}

	function toggle( element, on ) {
		jQuery( element ).stop();
		element.style.opacity = 1;

		if ( on ) {
			if ( element.classList.contains( 'frm_hidden' ) ) {
				element.style.opacity = 0;
				element.classList.remove( 'frm_hidden' );
				jQuery( element ).animate({ opacity: 1 });
			}
		} else if ( ! element.classList.contains( 'frm_hidden' ) ) {
			jQuery( element ).animate({ opacity: 0 }, function() {
				element.classList.add( 'frm_hidden' );
			});
		}
	}

	hooks.addAction( 'frm_update_slider_field_preview', 'formidable-pro', updateSliderFieldPreview, 10 );

	function updateSliderFieldPreview({ field, att, newValue }) {
		if ( 'value' === att ) {
			if ( '' === newValue ) {
				newValue = getSliderMidpoint( field );
			}
			field.value = newValue;
		} else {
			field.setAttribute( att, newValue );
		}

		if ( -1 === [ 'value', 'min', 'max' ].indexOf( att ) ) {
			return;
		}

		if ( ( 'max' === att || 'min' === att ) && '' === getSliderDefaultValueInput( field.id ) ) {
			field.value = getSliderMidpoint( field );
		}

		const fieldId = field.getAttribute( 'name' ).replace( 'item_meta[', '' ).replace( ']', '' );
		const settingsContainer = document.getElementById( 'frm-single-settings-' + fieldId );
		const isCurrency = settingsContainer.querySelector( 'input[name="field_options[is_currency_' + fieldId + ']"]' ).checked;
		const sliderValueSpan = field.parentNode.querySelector( '.frm_range_value' );

		if ( ! isCurrency ) {
			sliderValueSpan.textContent = field.value;
			return;
		}

		const isCustomCurrency = settingsContainer.querySelector( 'input[name="field_options[custom_currency_' + fieldId + ']"]' ).checked;
		const currency = isCustomCurrency ? {
			decimals: parseInt( getValueFromSettingsContainerInput( 'select', 'custom_decimals' ) ),
			decimal_separator: getValueFromSettingsContainerInput( 'input', 'custom_decimal_separator' ),
			thousand_separator: getValueFromSettingsContainerInput( 'input', 'custom_thousand_separator' ),
			symbol_left: getValueFromSettingsContainerInput( 'input', 'custom_symbol_left' ),
			symbol_right: getValueFromSettingsContainerInput( 'input', 'custom_symbol_right' ),
			symbol_padding: ''
		} : frmProBuilderVars.currency;

		sliderValueSpan.textContent = formatCurrency( normalizeTotal( field.value, currency ), currency );

		function getValueFromSettingsContainerInput( type, name ) {
			let selector = type + '[name="field_options[' + name + '_' + fieldId + ']"]';
			if ( 'select' === type ) {
				selector += ' option:checked';
			}
			return settingsContainer.querySelector( selector ).value;
		}

		function getSliderDefaultValueInput( previewInputId ) {
			return document.querySelector( 'input[data-changeme="' + previewInputId + '"][data-changeatt="value"]' ).value;
		}

		function getSliderMidpoint( sliderInput ) {
			const max = parseFloat( sliderInput.getAttribute( 'max' ) );
			const min = parseFloat( sliderInput.getAttribute( 'min' ) );
			return ( max - min ) / 2 + min;
		}
	}

	function normalizeTotal( total, currency ) {
		total = currency.decimals > 0 ? round10( total, currency.decimals ) : Math.ceil( total );
		return maybeAddTrailingZeroToPrice( total, currency );
	}

	function round10( value, decimals ) {
		return Number( Math.round( value + 'e' + decimals ) + 'e-' + decimals );
	}

	function formatCurrency( total, currency ) {
		let leftSymbol, rightSymbol;

		total = maybeAddTrailingZeroToPrice( total, currency );
		total = maybeRemoveTrailingZerosFromPrice( total, currency );
		total = addThousands( total, currency );
		leftSymbol = currency.symbol_left + currency.symbol_padding;
		rightSymbol = currency.symbol_padding + currency.symbol_right;

		function maybeRemoveTrailingZerosFromPrice( total, currency ) {
			var split = total.split( currency.decimal_separator );
			if ( 2 !== split.length || split[1].length <= currency.decimals ) {
				return total;
			}
			if ( 0 === currency.decimals ) {
				return split[0];
			}
			return split[0] + currency.decimal_separator + split[1].substr( 0, currency.decimals );
		}

		function addThousands( total, currency ) {
			if ( currency.thousand_separator ) {
				total = total.toString().replace( /\B(?=(\d{3})+(?!\d))/g, currency.thousand_separator );
			}
			return total;
		}

		return leftSymbol + total + rightSymbol;
	}

	function maybeAddTrailingZeroToPrice( price, currency ) {
		if ( 'number' !== typeof price ) {
			return price;
		}

		price += ''; // first convert to string

		const pos = price.indexOf( '.' );
		if ( pos === -1 ) {
			price = price + '.00';
		} else if ( price.substring( pos + 1 ).length < 2 ) {
			price += '0';
		}

		return price.replace( '.', currency.decimal_separator );
	}

	/**
	 * Wrap rich text logic into a function and initialize.
	 * A RTE field has uses TinyMCE for the preview, and for the default value input.
	 * The RTE needs to re-initialize at various points including:
	 * - when drag-and-dropped
	 * - when added with AJA
	 * - when a new field is inserted
	 * - when a group is broken into rows
	 * - when rows are merged into a group
	 *
	 * @returns {void}
	 */
	function initRichTextFields() {
		appendModalTriggersToRtePlaceholderSettings();

		document.addEventListener(
			'click',
			function( event ) {
				const classList = event.target.classList;
				if ( classList.contains( 'frm-break-field-group' ) || classList.contains( 'frm-row-layout-option' ) || classList.contains( 'frm-save-custom-field-group-layout' ) ) {
					initializeAllWysiwygsAfterSlightDelay();
				}
			}
		);

		document.addEventListener(
			'frm_added_field',
			/**
			 * Prepare an RTE field when a new field is added.
			 *
			 * @param {Event} event
			 * @returns {void}
			 */
			event => {
				if ( 'rte' !== event.frmType || ! event.frmField ) {
					return;
				}

				prepareDefaultValueInput( event.frmField.getAttribute( 'data-fid' ) )

				const wysiwyg = event.frmField.querySelector( '.wp-editor-area' );
				if ( wysiwyg ) {
					frmDom.wysiwyg.init( wysiwyg );
				}
			}
		);

		document.addEventListener(
			'frm_ajax_loaded_field',
			/**
			 * When new fields are loaded with AJAX, check if any are RTE fields and initialize.
			 *
			 * @param {Event} event
			 * @returns {void}
			 */
			event => {
				event.frmFields.forEach(
					/**
					 * Check if a single field is an RTE and possibly initialize.
					 *
					 * @param {Object} field {
					 *     @type {String} id Numeric field ID.
					 * }
					 * @returns {void}
					 */
					field => {
						if ( 'rte' !== field.type ) {
							return;
						}

						prepareDefaultValueInput( field.id );

						const wysiwyg = document.querySelector( '#frm_field_id_' + field.id + ' .wp-editor-area' );
						if ( wysiwyg ) {
							frmDom.wysiwyg.init( wysiwyg );
						}
					}
				);
			}
		);

		let draggable;
		// frm_sync_after_drag_and_drop does not pass along information about the draggable, so hook into dropdeactivate.
		jQuery( document ).on( 'dropdeactivate', function( _, ui ) {
			draggable = ui.draggable.get( 0 );
		});
		document.addEventListener(
			'frm_sync_after_drag_and_drop',
			() => {
				if ( draggable ) {
					// Use querySelectorAll as frm_sync_after_drag_and_drop is also called for field groups.
					draggable.querySelectorAll( '.wp-editor-area' ).forEach( frmDom.wysiwyg.init );
				}
			}
		);

		function prepareDefaultValueInput( fieldId ) {
			const defaultValueWrapper = document.getElementById( 'default-value-for-' + fieldId );
			addSmartValuesTriggerToDefaultValueWrapper( defaultValueWrapper );
			copyChangemeFromWrapperToInput( defaultValueWrapper );
		}

		function initializeAllWysiwygsAfterSlightDelay() {
			setTimeout(
				() => document.querySelectorAll( '#frm-show-fields .wp-editor-area' ).forEach( frmDom.wysiwyg.init ),
				1
			);
		}

		function appendModalTriggersToRtePlaceholderSettings() {
			const rtePlaceholderDefaults = document.querySelectorAll( '.frm-single-settings.frm-type-rte .frm-default-value-wrapper' );
			if ( ! rtePlaceholderDefaults.length ) {
				return;
			}

			rtePlaceholderDefaults.forEach(
				defaultValueWrapper => {
					addSmartValuesTriggerToDefaultValueWrapper( defaultValueWrapper );
					copyChangemeFromWrapperToInput( defaultValueWrapper );
				}
			);
		}

		function copyChangemeFromWrapperToInput( defaultValueWrapper ) {
			const fieldToChangeId = defaultValueWrapper.getAttribute( 'data-changeme' );

			document.getElementById( defaultValueWrapper.getAttribute( 'data-html-id' ) ).setAttribute( 'data-changeme', fieldToChangeId );
			defaultValueWrapper.removeAttribute( 'data-changeme' );

			const field = document.getElementById( fieldToChangeId );
			if ( field ) {
				jQuery( field ).on(
					'change',
					function() {
						if ( ! tinyMCE.editors[ field.id ] || tinyMCE.editors[ field.id ].isHidden() ) {
							return;
						}
						tinyMCE.editors[ field.id ].setContent( field.value );
					}
				);
			}
		}

		function addSmartValuesTriggerToDefaultValueWrapper( defaultValueWrapper ) {
			/*global frmDom */
			const { svg } = frmDom;

			const inputID = defaultValueWrapper.getAttribute( 'data-html-id' );

			const modalTrigger = svg({ href: '#frm_more_horiz_solid_icon', classList: [ 'frm_more_horiz_solid_icon', 'frm-show-inline-modal' ] });
			modalTrigger.setAttribute( 'data-open', 'frm-smart-values-box' );
			modalTrigger.setAttribute( 'title', defaultValueWrapper.getAttribute( 'data-modal-trigger-title' ) );

			document.getElementById( inputID ).parentElement.prepend( modalTrigger );

			// The icon should be wrapped in a 'p' tag, as the modal box is appended to the 'closest' p.
			const wrapper = document.createElement( 'p' );
			wrapper.prepend( document.getElementById( 'wp-' + inputID + '-wrap' ) );
			defaultValueWrapper.appendChild( wrapper );
		}
	}

	function validateTimeFieldRangeValue( target ) {
		if ( ! ( target.id.startsWith( 'start_time' ) || target.id.startsWith( 'end_time' ) ) ) {
			return;
		}

		const timeRangeInput = target;
		let isValid          = true;

		if ( timeRangeInput.matches( '[id^=frm_step_]' ) ) {
			if ( timeRangeInput.value.match( /^\d{1,2}$/ ) ) {
				return;
			}
			frmAdminBuild.infoModal( 'Step value is invalid.' );
			isValid = false;

		} else if ( ! timeRangeInput.value.match( /^(?:\d|[01]\d|2[0-3]):[0-5]\d$/ ) ) {
			let timeRangeString;
			if ( timeRangeInput.matches( '.frm-type-time [id^=start_time]' ) ) {
				timeRangeString = 'Start time';
			} else {
				timeRangeString = 'End time';
			}
			frmAdminBuild.infoModal( `${timeRangeString} is invalid.` );
			isValid = false;
		}

		if ( ! isValid ) {
			const modalDismissers = document.querySelectorAll( '#frm_info_modal .dismiss, #frm_info_modal #frm-info-click' );
			function handleModalDismiss() {
				timeRangeInput.classList.add( 'frm_invalid_field' );
				setTimeout( () => timeRangeInput.focus(), 0 );
				modalDismissers.forEach( el => {
					el.removeEventListener( 'click', handleModalDismiss );
				});
			}

			modalDismissers.forEach( el => {
				el.addEventListener( 'click', handleModalDismiss );
			});

		} else if ( timeRangeInput.classList.contains( 'frm_invalid_field' ) ) {
			timeRangeInput.classList.remove( 'frm_invalid_field' );
		}
	}

	addEventListeners();
	initRichTextFields();

}() );
