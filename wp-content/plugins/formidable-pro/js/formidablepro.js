function frmProFormJS() {
	/* globals frm_js, frmFrontForm, google */
	/* globals __frmDatepicker, __frmDropzone, __frmUniqueTimes */
	/* globals __FRMLOOKUP, __FRMCALC, __FRMRULES, __FRMCURR */
	/* globals __frmChosen, __frmHideOrShowFields, __frmDepDynamicFields */
	/* globals __frmDepLookupFields, __frmMasks, __FRMTABLES */

	'use strict';
	var currentlyAddingRow = false;
	var action = '';
	var processesRunning = 0;
	var lookupQueues = {};
	var hiddenSubmitButtons = [];
	var pendingDynamicFieldAjax = [];
	var listWrappersOriginal = {};

	function setNextPage( e ) {
		var closestButton;
		if ( this.className.indexOf( 'frm_rootline_title' ) !== -1 ) {
			closestButton = this.previousElementSibling;
			closestButton.click();
			return;
		}

		if ( this.className.indexOf( 'frm_rootline_single' ) !== -1 ) {
			this.querySelector( 'input' ).click();
			return;
		}

		/*jshint validthis:true */
		var $thisObj = jQuery( this );
		var thisType = $thisObj.attr( 'type' );
		if ( thisType !== 'submit' ) {
			e.preventDefault();
		}

		var f = $thisObj.parents( 'form' ).first(),
			v = '',
			d = '',
			thisName = this.name;

		if ( thisName === 'frm_prev_page' || this.className.indexOf( 'frm_prev_page' ) !== -1 ) {
			v = jQuery( f ).find( '.frm_next_page' ).attr( 'id' ).replace( 'frm_next_p_', '' );
		} else if ( thisName === 'frm_save_draft' || this.className.indexOf( 'frm_save_draft' ) !== -1 ) {
			d = 1;
		} else if ( this.className.indexOf( 'frm_page_skip' ) !== -1 ) {
			var goingTo = $thisObj.data( 'page' );
			var formId = jQuery( f ).find( 'input[name="form_id"]' ).val();
			var orderField = jQuery( f ).find( 'input[name="frm_page_order_' + formId + '"]' );
			jQuery( f ).append( '<input name="frm_last_page" type="hidden" value="' + orderField.val() + '" />' );

			if ( goingTo === '' ) {
				orderField.remove();
			} else {
				orderField.val( goingTo );
			}
		} else if ( this.className.indexOf( 'frm_page_back' ) !== -1 ) {
			v = $thisObj.data( 'page' );
		}

		if ( 1 === d ) {
			resetTinyMceOnDraftSave();
		} else {
			resetTinyMceOnPageTurn();
		}

		jQuery( '.frm_next_page' ).val( v );
		jQuery( '.frm_saving_draft' ).val( d );

		if ( thisType !== 'submit' ) {
			f.trigger( 'submit' );
		}
	}

	function resetTinyMceOnDraftSave() {
		jQuery( document ).one( 'frmFormComplete', function() {
			jQuery( '.wp-editor-area' ).each( function() {
				reInitializeRichText( this.id );
			});
		});
	}

	function resetTinyMceOnPageTurn() {
		var removeIds = [];
		jQuery( '.frm_form_field .wp-editor-area' ).each(
			function() {
				removeIds.push( this.id );
			}
		);
		jQuery( document ).one( 'frmPageChanged', function() {
			var removeIndex, removeId;
			// Remove tinymce from RTE fields on page update so they can initialize properly when the page becomes active again
			for ( removeIndex = 0; removeIndex < removeIds.length; ++removeIndex ) {
				removeId = removeIds[ removeIndex ];
				removeRichText( removeId );
			}
			checkConditionalLogic();
		});
	}

	function toggleSection( e ) {
		var useTag, oldIcon,
			attrNS = 'http://www.w3.org/1999/xlink';

		if ( e.key !== undefined ) {
			if ( e.key !== ' ' ) {
				return;
			}
		} else if ( e.keyCode !== undefined && e.keyCode !== 32 ) {
			return;
		}

		e.preventDefault();

		/*jshint validthis:true */
		jQuery( this ).parent().children( '.frm_toggle_container' ).slideToggle( 'fast' );

		if ( -1 !== this.className.indexOf( 'active' ) ) {
			this.className = this.className.replace( ' active', '' );
			this.setAttribute( 'aria-expanded', 'false' );

		} else {
			this.className += ' active';
			this.setAttribute( 'aria-expanded', 'true' );
		}
	}

	function loadDateFields() {
		jQuery( document ).on( 'focusin', '.frm_date', triggerDateField );
		loadUniqueTimeFields();
	}

	function triggerDateField() {
		/*jshint validthis:true */
		if ( this.className.indexOf( 'frm_custom_date' ) !== -1 || typeof __frmDatepicker === 'undefined' ) {
			return;
		}

		var dateFields = __frmDatepicker,
			id = this.id,
			idParts = id.split( '-' ),
			altID = '';

		if ( isRepeatingFieldByName( this.name ) ) {
			altID = 'input[id^="' + idParts[ 0 ] + '"]';
		} else {
			altID = 'input[id^="' + idParts.join( '-' ) + '"]';
		}

		jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ '' ]);

		var optKey = 0;
		for ( var i = 0; i < dateFields.length; i++ ) {
			if ( dateFields[ i ].triggerID === '#' + id || dateFields[ i ].triggerID == altID ) {
				optKey = i;
				break;
			}
		}

		if ( dateFields[ optKey ].options.defaultDate !== '' ) {
			dateFields[ optKey ].options.defaultDate = new Date( dateFields[ optKey ].options.defaultDate );
		}

		dateFields[ optKey ].options.beforeShow = frmProForm.addFormidableClassToDatepicker;
		dateFields[ optKey ].options.onClose = frmProForm.removeFormidableClassFromDatepicker;

		jQuery( this ).datepicker( jQuery.extend(
			{},
			jQuery.datepicker.regional[ dateFields[ optKey ].locale ],
			dateFields[ optKey ].options
		) );
	}

	function loadDropzones( repeatRow ) {
		if ( typeof __frmDropzone === 'undefined' ) {
			return;
		}

		var uploadFields = __frmDropzone;
		for ( var i = 0; i < uploadFields.length; i++ ) {
			loadDropzone( i, repeatRow );
		}
	}

	function loadDropzone( i, repeatRow ) {
		var field, max, uploadedCount, form,
			uploadFields = __frmDropzone,
			uploadField = uploadFields[ i ],
			selector = '#' + uploadField.htmlID + '_dropzone',
			fieldName = uploadField.fieldName;

		if ( typeof repeatRow !== 'undefined' && selector.indexOf( '-0_dropzone' ) !== -1 ) {
			selector = selector.replace( '-0_dropzone', '-' + repeatRow + '_dropzone' );
			fieldName = fieldName.replace( '[0]', '[' + repeatRow + ']' );
			delete uploadField.mockFiles;
		}

		field = jQuery( selector );
		if ( field.length < 1 || field.hasClass( 'dz-clickable' ) || field.hasClass( 'dz-started' ) ) {
			return;
		}

		max = uploadField.maxFiles;
		if ( typeof uploadField.mockFiles !== 'undefined' ) {
			uploadedCount = uploadField.mockFiles.length;
			if ( max > 0 ) {
				max = max - uploadedCount;
			}
		}

		form = field.closest( 'form' );
		uploadField = uploadFields[ i ];

		field.dropzone({
			url: getAjaxUrl( form.get( 0 ) ),
			headers: {
				'Frm-Dropzone': 1
			},
			addRemoveLinks: false,
			paramName: field.attr( 'id' ).replace( '_dropzone', '' ),
			maxFilesize: uploadField.maxFilesize,
			minFilesize: uploadField.minFilesize,
			maxFiles: max,
			uploadMultiple: uploadField.uploadMultiple,
			hiddenInputContainer: field.parent()[0],
			dictDefaultMessage: uploadField.defaultMessage,
			dictFallbackMessage: uploadField.fallbackMessage,
			dictFallbackText: uploadField.fallbackText,
			dictFileTooBig: uploadField.fileTooBig,
			dictFileTooSmall: uploadField.fileTooSmall,
			dictInvalidFileType: uploadField.invalidFileType,
			dictResponseError: uploadField.responseError,
			dictCancelUpload: uploadField.cancel,
			dictCancelUploadConfirmation: uploadField.cancelConfirm,
			dictRemoveFile: uploadField.remove,
			dictMaxFilesExceeded: uploadField.maxFilesExceeded,
			resizeMethod: 'contain',
			resizeWidth: uploadField.resizeWidth,
			resizeHeight: uploadField.resizeHeight,
			thumbnailWidth: 60,
			thumbnailHeight: 60,
			timeout: uploadField.timeout,
			previewTemplate: filePreviewHTML( uploadField ),
			acceptedFiles: uploadField.acceptedFiles,
			fallback: function() {
				// Force ajax submit to turn off
				jQuery( this.element ).closest( 'form' ).removeClass( 'frm_ajax_submit' );
			},
			init: function() {
				var hidden, mockFileIndex, mockFileData, mockFile;

				hidden = field.parent().find( '.dz-hidden-input' );

				if ( typeof hidden.attr( 'id' ) === 'undefined' ) {
					hidden.attr( 'id', uploadFields[ i ].label );
				}

				this.on( 'thumbnail', function( file ) {
					if ( file.size < 1024 * 1024 * ( this.options.minFilesize ) ) {
						file.rejectSize();
					}
				});

				this.on( 'sending', function( file, xhr, formData ) {

					if ( isSpam( uploadFields[ i ].parentFormID, uploadField.checkHoneypot ) ) {
						this.removeFile( file );
						alert( frm_js.file_spam );
						return false;
					} else {
						formData.append( 'action', 'frm_submit_dropzone' );
						formData.append( 'field_id', uploadFields[ i ].fieldID );
						formData.append( 'form_id', uploadFields[ i ].formID );
						formData.append( 'nonce', frm_js.nonce );

						if ( form.get( 0 ).hasAttribute( 'data-token' ) ) {
							formData.append( 'antispam_token', form.get( 0 ).getAttribute( 'data-token' ) );
						}
					}
				});

				this.on( 'success', function( file, response ) {
					var mediaIDs, m, mediaID;

					mediaIDs = JSON.parse( response );
					for ( m = 0; m < mediaIDs.length; m++ ) {
						if ( uploadFields[ i ].uploadMultiple !== true ) {
							mediaID = mediaIDs[ m ];
							jQuery( 'input[name="' + fieldName + '"]' ).val( mediaID );
						}
					}

					if ( this.options.uploadMultiple === false ) {
						this.disable();
					}
				});

				this.on( 'successmultiple', function( files, response ) {
					var mediaIDs = JSON.parse( response );
					for ( var m = 0; m < files.length; m++ ) {
						jQuery( files[ m ].previewElement ).append( getHiddenUploadHTML( uploadFields[ i ], mediaIDs[ m ], fieldName ) );
					}
				});

				this.on( 'complete', function( file ) {
					var fileName, node, img, thumbnail;

					processesRunning--;
					frmFrontForm.removeSubmitLoading( form, 'enable', processesRunning );

					if ( typeof file.mediaID === 'undefined' ) {
						return;
					}

					if ( uploadFields[ i ].uploadMultiple ) {
						jQuery( file.previewElement ).append( getHiddenUploadHTML( uploadFields[ i ], file.mediaID, fieldName ) );
					}

					// Add download link to the file
					fileName = file.previewElement.querySelectorAll( '[data-dz-name]' );
					for ( var _i = 0, _len = fileName.length; _i < _len; _i++ ) {
						node = fileName[ _i ];
						if ( file.accessible ) {
							node.innerHTML = '<a href="' + file.url + '" target="_blank" rel="noopener">' + file.name + '</a>';
						} else {
							node.innerHTML = file.name;
						}

						if ( file.ext ) {
							img = file.previewElement.querySelector( '.dz-image img' );
							if ( null !== img ) {
								thumbnail = maybeGetExtensionThumbnail( file.ext );
								if ( false !== thumbnail ) {
									img.setAttribute( 'src', thumbnail );
								}
							}
						}
					}
				});

				this.on( 'addedfile', function( file ) {
					var ext, thumbnail;
					ext = file.name.split( '.' ).pop();
					thumbnail = maybeGetExtensionThumbnail( ext );
					processesRunning++;
					frmFrontForm.showSubmitLoading( form );
					if ( false !== thumbnail ) {
						jQuery( file.previewElement ).find( '.dz-image img' ).attr( 'src', thumbnail );
					}

					clearErrorsOnUpload( file.previewElement );
				});

				function clearErrorsOnUpload( fileElement ) {
					var container = fileElement.closest( '.frm_form_field' );
					if ( ! container ) {
						return;
					}

					container.classList.remove( 'frm_blank_field', 'has-error' );
					container.querySelectorAll( '.form-field .frm_error, .frm_error_style' ).forEach(
						function( error ) {
							if ( error.parentNode ) {
								error.parentNode.removeChild( error );
							}
						}
					);
				}

				this.on( 'removedfile', function( file ) {
					var fileCount = this.files.length;

					if ( this.options.uploadMultiple === false && fileCount < 1 ) {
						this.enable();
					}

					if ( file.accepted !== false && uploadFields[ i ].uploadMultiple !== true ) {
						jQuery( 'input[name="' + fieldName + '"]' ).val( '' );
					}

					if ( file.accepted !== false && typeof file.mediaID !== 'undefined' ) {
						jQuery( file.previewElement ).remove();
						fileCount = this.files.length;
						this.options.maxFiles = uploadFields[ i ].maxFiles - fileCount;
					}
				});

				if ( typeof uploadFields[ i ].mockFiles !== 'undefined' ) {
					for ( mockFileIndex = 0; mockFileIndex < uploadFields[ i ].mockFiles.length; mockFileIndex++ ) {
						mockFileData = uploadFields[ i ].mockFiles[ mockFileIndex ];
						mockFile = {
							name: mockFileData.name,
							size: mockFileData.size,
							url: mockFileData.file_url,
							mediaID: mockFileData.id,
							accessible: mockFileData.accessible,
							ext: mockFileData.ext,
							type: mockFileData.type
						};

						this.emit( 'addedfile', mockFile );
						if ( mockFile.accessible && 'string' === typeof mockFile.type && 0 === mockFile.type.indexOf( 'image/' ) ) {
							this.emit( 'thumbnail', mockFile, mockFileData.url );
						}

						this.emit( 'complete', mockFile );
						this.files.push( mockFile );
					}
				}
			},
			accept: function( file, done ) {
				const message = this.options.dictFileTooSmall.replace( '{{minFilesize}}', this.options.minFilesize );
				file.rejectSize = function() {
					done( message );
				};

				return done();
			}
		});
	}

	function maybeGetExtensionThumbnail( ext ) {
		if ( -1 !== [ 'jpg', 'jpeg', 'png' ].indexOf( ext ) ) {
			return false;
		}

		if ( 'pdf' === ext ) {
			return getProPluginUrl() + '/images/pdf.svg';
		}

		if ( -1 !== ext.indexOf( 'xls' ) ) {
			return getProPluginUrl() + '/images/xls.svg';
		}

		return getProPluginUrl() + '/images/doc.svg';
	}

	function getProPluginUrl() {
		var freePluginUrlSplitBySlashes = frm_js.images_url.split( '/' );
		freePluginUrlSplitBySlashes.pop();
		freePluginUrlSplitBySlashes.pop();
		freePluginUrlSplitBySlashes.push( 'formidable-pro' );
		return freePluginUrlSplitBySlashes.join( '/' );
	}

	function filePreviewHTML( field ) {
		return '<div class="dz-preview dz-file-preview frm_clearfix">\n' +
		'<div class="dz-image"><img data-dz-thumbnail /></div>\n' +
		'<div class="dz-column">\n' +
		'<div class="dz-details">\n' +
		'<div class="dz-filename"><span data-dz-name></span></div>\n' +
		' ' + // add white space between file name and file size.
		'<div class="dz-size"><span data-dz-size></span></div>\n' +
		'<a class="dz-remove frm_remove_link" href="javascript:undefined;" data-dz-remove title="' + field.remove + '">' +
		'<svg width="20" height="20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M10 0a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm3.6-13L10 8.6 6.4 5 5 6.4 8.6 10 5 13.6 6.4 15l3.6-3.6 3.6 3.6 1.4-1.4-3.6-3.6L15 6.4z"/></svg>' +
		'</a>' +
		'</div>\n' +
		'<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>\n' +
		'<div class="dz-error-message"><span data-dz-errormessage></span></div>\n' +
		'</div>\n' +
		'</div>';
	}

	function getHiddenUploadHTML( field, mediaID, fieldName ) {
		return '<input name="' + fieldName + '[]" type="hidden" value="' + mediaID + '" data-frmfile="' + field.fieldID + '" />';
	}

	function removeFile() {
		/*jshint validthis:true */
		var fieldName = jQuery( this ).data( 'frm-remove' );
		fadeOut( jQuery( this ).closest( '.dz-preview' ) );
		var singleField = jQuery( 'input[name="' + fieldName + '"]' );
		if ( singleField.length ) {
			singleField.val( '' );
		}
	}

	function postToAjaxUrl( form, data, success, error, extraParams ) {
		var ajaxParams = 'object' === typeof extraParams ? extraParams : {};

		ajaxParams.type = 'POST';
		ajaxParams.url = getAjaxUrl( form );
		ajaxParams.data = data;
		ajaxParams.success = success;

		if ( 'function' === typeof error ) {
			ajaxParams.error = error;
		}

		jQuery.ajax( ajaxParams );
	}

	function getAjaxUrl( form ) {
		var ajaxUrl, action;

		ajaxUrl = frm_js.ajax_url;
		action = form.getAttribute( 'action' );

		if ( 'string' === typeof action && -1 !== action.indexOf( '?action=frm_forms_preview' ) ) {
			ajaxUrl = action.split( '?action=frm_forms_preview' )[0];
		}

		return ajaxUrl;
	}

	function isSpam( formID, checkHoneypot ) {
		if ( isHeadless() ) {
			return true;
		}
		return checkHoneypot && isHoneypotSpam( formID );
	}

	/**
	 * Check if submission is Honeypot spam
	 *
	 * @since 2.03.08
	 *
	 * @returns {boolean}
	 */
	function isHoneypotSpam( formID ) {
		var honeypotField = document.getElementById( 'frm_email_' + formID );
		if ( honeypotField === null ) {
			honeypotField = document.getElementById( 'frm_verify_' + formID );
		}
		return honeypotField !== null && honeypotField.value !== '';
	}

	function isHeadless() {
		return (
			window._phantom || window.callPhantom || //phantomjs
			window.__phantomas || //PhantomJS-based web perf metrics
			window.Buffer || //nodejs
			window.emit || //couchjs
			window.spawn  //rhino
		);
	}

	/**
	 * Show "Other" text box when Other item is checked/selected
	 * Hide and clear text box when item is unchecked/unselected
	 */
	function showOtherText() {
        /*jshint validthis:true */
        var type = this.type,
			other = false,
			select = false;

        // Dropdowns
        if ( type === 'select-one' ) {
            select = true;
            var curOpt = this.options[ this.selectedIndex ];
            if ( typeof curOpt !== 'undefined' && curOpt.className === 'frm_other_trigger' ) {
                other = true;
            }
        } else if ( type === 'select-multiple' ) {
            select = true;
            var allOpts = this.options;
            other = false;
            for ( var i = 0; i < allOpts.length; i++ ) {
                if ( allOpts[ i ].className === 'frm_other_trigger' ) {
                    if ( allOpts[ i ].selected ) {
                        other = true;
                        break;
                    }
                }
            }
        }
        if ( select ) {
			var otherField = jQuery( this ).parent().children( '.frm_other_input' );

			if ( otherField.length ) {
				if ( other ) {
					// Remove frm_pos_none
					otherField[ 0 ].className = otherField[ 0 ].className.replace( 'frm_pos_none', '' );
				} else {
					// Add frm_pos_none
					if ( otherField[ 0 ].className.indexOf( 'frm_pos_none' ) < 1 ) {
						otherField[ 0 ].className = otherField[ 0 ].className + ' frm_pos_none';
					}
					otherField[ 0 ].value = '';
				}
			}

        // Radio
        } else if ( type === 'radio' ) {
			if ( jQuery( this ).is( ':checked' ) ) {
				jQuery( this ).closest( '.frm_radio' ).children( '.frm_other_input' ).removeClass( 'frm_pos_none' );
				jQuery( this ).closest( '.frm_radio' ).siblings().children( '.frm_other_input' ).addClass( 'frm_pos_none' ).val( '' );
			}
        // Checkboxes
        } else if ( type === 'checkbox' ) {
            if ( this.checked ) {
                jQuery( this ).closest( '.frm_checkbox' ).children( '.frm_other_input' ).removeClass( 'frm_pos_none' );
            } else {
                jQuery( this ).closest( '.frm_checkbox' ).children( '.frm_other_input' ).addClass( 'frm_pos_none' ).val( '' );
            }
        }
	}

	function setToggleAriaChecked() {
		this.nextElementSibling.setAttribute( 'aria-checked', this.checked ? 'true' : 'false' );
	}

	function maybeCheckDependent( event, field, fieldId, e ) {
		/*jshint validthis:true */
		var $field = jQuery( field );
		checkFieldsWithConditionalLogicDependentOnThis( fieldId, $field );

		var originalEvent = getOriginalEvent( e );
		checkFieldsWatchingLookup( fieldId, $field, originalEvent );
		doCalculation( fieldId, $field );
	}

	function getOriginalEvent( e ) {
		var originalEvent;
		if ( typeof e.originalEvent !== 'undefined' || e.currentTarget.className.indexOf( 'frm_chzn' ) > -1 ) {
			originalEvent = 'value changed';
		} else {
			originalEvent = 'other';
		}
		return originalEvent;
	}

	/*****************************************************
	 Conditional Logic Functions
	 ******************************************************/

	// Check if a changed field has other fields depending on it
	function checkFieldsWithConditionalLogicDependentOnThis( fieldId, changedInput ) {
		if ( typeof __FRMRULES  === 'undefined' ||
			typeof __FRMRULES[ fieldId ] === 'undefined' ||
			__FRMRULES[ fieldId ].dependents.length < 1 ||
			changedInput === null ||
			typeof changedInput === 'undefined'
		) {
			return;
		}

		var triggerFieldArgs = __FRMRULES[ fieldId ];
		var repeatArgs = getRepeatArgsFromFieldName( changedInput[ 0 ].name );

		pendingDynamicFieldAjax = [];

		for ( var i = 0, l = triggerFieldArgs.dependents.length; i < l; i++ ) {
			hideOrShowFieldById( triggerFieldArgs.dependents[ i ], repeatArgs );
		}

		processPendingAjax();
	}

	function processPendingAjax() {
		var fieldsToProcess, postData, data, formId;

		if ( ! pendingDynamicFieldAjax.length ) {
			return;
		}

		// Copy pendingDynamicFieldAjax as it gets reset to [] every time checkFieldsWithConditionalLogicDependentOnThis is called.
		fieldsToProcess = pendingDynamicFieldAjax.slice();
		postData        = [];

		for ( data in fieldsToProcess ) {
			postData.push( fieldsToProcess[ data ].data );
		}

		formId = fieldsToProcess[0].args.depFieldArgs.formId;

		function processDynamicField( html, depFieldArgs, onCurrentPage ) {
			var $fieldDiv, $optContainer, $listInputs, listVal;

			if ( onCurrentPage ) {
				$fieldDiv = jQuery( '#' + depFieldArgs.containerId );
				addLoadingIcon( $fieldDiv );

				$optContainer = $fieldDiv.find( '.frm_opt_container, .frm_data_container' );
				$optContainer.html( html );
				$listInputs = $optContainer.children( 'input' );
				listVal = $listInputs.val();
				removeLoadingIcon( $optContainer );
				if ( '' === html || '' === listVal ) {
					hideDynamicField( depFieldArgs );
				} else {
					showDynamicField( depFieldArgs, $fieldDiv, $listInputs, true );
				}
			} else {
				updateHiddenDynamicListField( depFieldArgs, html );
			}
		}

		function ajaxHandler( response ) {
			var i;
			for ( i = 0; i < fieldsToProcess.length; i++ ) {
				processDynamicField(
					'undefined' === typeof response[ i ] ? '' : response[ i ],
					fieldsToProcess[ i ].args.depFieldArgs,
					fieldsToProcess[ i ].args.onCurrentPage
				);
			}
		}

		postToAjaxUrl(
			getFormById( formId ),
			{
				action: 'frm_fields_ajax_get_data_arr',
				postData: postData
			},
			ajaxHandler,
			function( response ) {
				console.error( response );
			},
			{
				dataType: 'json'
			}
		);

	}

	/**
	 * Hide or show all instances of a field using the field ID
	 *
	 * @param fieldId
	 * @param triggerFieldRepeatArgs
     */
	function hideOrShowFieldById( fieldId, triggerFieldRepeatArgs ) {
		var depFieldArgs = getRulesForSingleField( fieldId );

		if ( depFieldArgs === false || depFieldArgs.conditions.length < 1 ) {
			// If field has no logic on it, stop now
			return;
		}

		var childFieldDivIds = getAllFieldDivIds( depFieldArgs, triggerFieldRepeatArgs );

		var childFieldNum = childFieldDivIds.length;
		for ( var i = 0; i < childFieldNum; i++ ) {
			depFieldArgs.containerId = childFieldDivIds[ i ];
			addRepeatRow( depFieldArgs, childFieldDivIds[ i ]);
			hideOrShowSingleField( depFieldArgs );
		}
	}

	/**
	 * Get all the field divs that should be hidden or shown, regardless of whether they're on the current page
	 *
	 * @param {Object} depFieldArgs
	 * @param {bool} depFieldArgs.isRepeating
	 * @param {string} depFieldArgs.fieldId
	 * @param {object} triggerFieldArgs
	 * @param {string} triggerFieldArgs.repeatingSection
	 * @param {string} triggerFieldArgs.repeatRow
	 * @param {string} depFieldArgs.inEmbedForm
	 * @returns {Array}
     */
	function getAllFieldDivIds( depFieldArgs, triggerFieldArgs ) {
		var childFieldDivs = [];

		if ( depFieldArgs.isRepeating ) {
			if ( triggerFieldArgs.repeatingSection !== '' ) {
				// If trigger field is repeating/embedded, use its section row in selector
				var container = 'frm_field_' + depFieldArgs.fieldId + '-';
				container += triggerFieldArgs.repeatingSection + '-' + triggerFieldArgs.repeatRow + '_container';
				childFieldDivs.push( container );
			} else {
				// If trigger field is not repeating/embedded, get all repeating/embedded field divs
				childFieldDivs = getAllRepeatingFieldDivIds( depFieldArgs );
			}
		} else if ( depFieldArgs.fieldType === 'submit' ) {
			childFieldDivs.push( getSubmitButtonContainerID( depFieldArgs ) );
		} else {
			childFieldDivs.push( 'frm_field_' + depFieldArgs.fieldId + '_container' );
		}

		return childFieldDivs;
	}

	/**
	 * Return selector for submit button for use in Conditional Logic.
	 *
	 * @param depFieldArgs
	 * @returns {string}
	 */
	function getSubmitButtonContainerID( depFieldArgs ) {
		return 'frm_form_' + depFieldArgs.formId + '_container .frm_final_submit';
	}

	/**
	 * Get all instances of a repeating field
	 *
	 * @since 2.01.0
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldId
     */
	function getAllRepeatingFieldDivIds( depFieldArgs ) {
		var childFieldDivs = [],
			containerFieldId = getContainerFieldId( depFieldArgs );

		// TODO: what if section is inside embedded form?

		if ( isFieldDivOnPage( 'frm_field_' + containerFieldId + '_container' ) ) {
			childFieldDivs = getRepeatingFieldDivIdsOnCurrentPage( depFieldArgs.fieldId );
		} else {
			childFieldDivs = getRepeatingFieldDivIdsAcrossPage( depFieldArgs );
		}

		return childFieldDivs;
	}

	/**
	 * Get all repeating field divs on the current page
	 *
	 * @since 2.02.06
	 * @param string fieldId
	 * @returns {Array}
     */
	function getRepeatingFieldDivIdsOnCurrentPage( fieldId ) {
		var childFieldDivs = [],
			childFields = document.querySelectorAll( '.frm_field_' + fieldId + '_container' );

		for ( var i = 0, l = childFields.length; i < l; i++ ) {
			childFieldDivs.push( childFields[ i ].id );
		}

		return childFieldDivs;
	}

	/**
	 * Get the field divs for repeating fields across a page
	 *
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldId
	 * @returns {Array}
	 */
	function getRepeatingFieldDivIdsAcrossPage( depFieldArgs ) {
		var childFieldDivs = [],
			containerFieldId = getContainerFieldId( depFieldArgs ),
			fieldDiv = 'frm_field_' + depFieldArgs.fieldId + '-' + containerFieldId + '-',
			allRows = document.querySelectorAll( '[name="item_meta[' + containerFieldId + '][row_ids][]"]' );

        for ( var i = 0, l = allRows.length; i < l; i++ ) {
            if ( allRows[ i ].value !== '' ) {
                childFieldDivs.push( fieldDiv + allRows[ i ].value + '_container' );
            }
        }

        if ( childFieldDivs.length < 1 ) {
			childFieldDivs.push( fieldDiv + '0_container' );
		}

		return childFieldDivs;
	}

	/**
	 *
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.inSection
	 * @param {string} depFieldArgs.inEmbedForm
	 * @returns {string}
     */
	function getContainerFieldId( depFieldArgs ) {
		var containerFieldId = '';
		if ( depFieldArgs.inEmbedForm !== '0' ) {
			containerFieldId = depFieldArgs.inEmbedForm;
		} else if ( depFieldArgs.inSection !== '0' ) {
			containerFieldId = depFieldArgs.inSection;
		}

		return containerFieldId;
	}

	/**
	 *
	 * @param depFieldArgs
	 * @param {bool} depFieldArgs.isRepeating
	 * @param childFieldDivId
     */
	function addRepeatRow( depFieldArgs, childFieldDivId ) {
		if ( depFieldArgs.isRepeating ) {
			var divParts = childFieldDivId.replace( '_container', '' ).split( '-' );
			depFieldArgs.repeatRow = divParts[ 2 ];
		} else {
			depFieldArgs.repeatRow = '';
		}
	}

	function hideOrShowSingleField( depFieldArgs ) {
		var add,
			logicOutcomes = [],
			len = depFieldArgs.conditions.length;

		for ( var i = 0; i < len; i++ ) {
			add = checkLogicCondition( depFieldArgs.conditions[ i ], depFieldArgs );
			if ( add !== null ) {
				// Prevent fields not on the page from being hidden without a chance.
				logicOutcomes.push( add );
			}
		}

		if ( logicOutcomes.length ) {
			// Only continue if the field was found on the page.
			routeToHideOrShowField( depFieldArgs, logicOutcomes );
		}
	}

	function getRulesForSingleField( fieldId ) {
		if ( typeof __FRMRULES  === 'undefined' || typeof __FRMRULES[ fieldId ] === 'undefined' ) {
			return false;
		}

		return __FRMRULES[ fieldId ];
	}

	/**
	 * @param {Array} logicCondition
	 * @param {string} logicCondition.fieldId
	 * @param depFieldArgs
	 * @returns {*}
     */
	function checkLogicCondition( logicCondition, depFieldArgs ) {
		var fieldId = logicCondition.fieldId,
			logicFieldArgs = getRulesForSingleField( fieldId ),
			fieldValue = getFieldValue( logicFieldArgs, depFieldArgs );

		if ( fieldValue === null ) {
			// The field wasn't found.
			return null;
		}

		return getLogicConditionOutcome( logicCondition, fieldValue, depFieldArgs, logicFieldArgs );
	}

	/**
	 * Get the value from any field
	 *
	 * @param logicFieldArgs
	 * @param {string} logicFieldArgs.inputType
	 * @param depFieldArgs
	 * @returns {string}
     */
	function getFieldValue( logicFieldArgs, depFieldArgs ) {
		var fieldValue = '';

		if ( logicFieldArgs.inputType === 'radio' || logicFieldArgs.inputType === 'checkbox' || logicFieldArgs.inputType === 'toggle' ) {
			fieldValue = getValueFromRadioOrCheckbox( logicFieldArgs, depFieldArgs );
		} else {
			fieldValue = getValueFromTextOrDropdown( logicFieldArgs, depFieldArgs );
		}

		fieldValue = cleanFinalFieldValue( fieldValue );

		return fieldValue;
	}

	/**
	 * Get the value from a Text or Dropdown field
	 *
	 * @param logicFieldArgs
	 * @param {string} logicFieldArgs.fieldKey
	 * @param {bool} logicFieldArgs.isRepeating
	 * @param {bool} logicFieldArgs.isMultiSelect
	 * @param depFieldArgs
	 * @param {string} depFieldArgs.repeatRow
 	 */
	function getValueFromTextOrDropdown( logicFieldArgs, depFieldArgs ) {
		var logicFieldValue = '';

		if ( logicFieldArgs.isMultiSelect === true ) {
			return getValueFromMultiSelectDropdown( logicFieldArgs, depFieldArgs );
		}

		var fieldCall = 'field_' + logicFieldArgs.fieldKey;
		if ( logicFieldArgs.isRepeating ) {
			// If trigger field is repeating, dependent field is repeating too
			fieldCall += '-' + depFieldArgs.repeatRow;
		}

		var logicFieldInput = document.getElementById( fieldCall );

		if ( logicFieldInput === null ) {
			logicFieldValue = parseTimeValue( logicFieldArgs, fieldCall );
			if ( logicFieldValue === '' ) {
				// Check for hidden fields.
				logicFieldValue = getValueFromMultiSelectDropdown( logicFieldArgs, depFieldArgs );
			}
		} else {
			logicFieldValue = logicFieldInput.value;
		}

		return logicFieldValue;
	}

	function parseTimeValue( logicFieldArgs, fieldCall ) {
		var logicFieldValue = '';
		if ( logicFieldArgs.fieldType === 'time' ) {
			var hour = document.getElementById( fieldCall + '_H' );
			if ( hour !== null ) {
				var minute = document.getElementById( fieldCall + '_m' );
				logicFieldValue = hour.value + ':' + minute.value;

				var pm = document.getElementById( fieldCall + '_A' );
				if ( logicFieldValue == ':' ) {
					logicFieldValue = '';
				} else if ( pm !== null ) {
					logicFieldValue += ' ' + pm.value;
				}
			}
		}
		return logicFieldValue;
	}

	function getValueFromMultiSelectDropdown( logicFieldArgs, depFieldArgs ) {
		var inputName = buildLogicFieldInputName( logicFieldArgs, depFieldArgs ),
			logicFieldInputs = document.querySelectorAll( '[name^="' + inputName + '"]' ),
			selectedVals = [];

		// TODO: What about if it's read-only?

		if ( logicFieldInputs.length == 1 && logicFieldInputs[ 0 ].type !== 'hidden' ) {
			selectedVals = jQuery( '[name^="' + inputName + '"]' ).val();
			if ( selectedVals === null ) {
				selectedVals = '';
			}
		} else {
			selectedVals = getValuesFromCheckboxInputs( logicFieldInputs );
		}

		return selectedVals;
	}

	/**
	 * Get the value from a Radio or Checkbox field trigger field
	 *
	 * @param {Object} logicFieldArgs
	 * @param {string} logicFieldArgs.inputType
	 * @param {Object} depFieldArgs
	 * @returns {String|Array}
     */
	function getValueFromRadioOrCheckbox( logicFieldArgs, depFieldArgs ) {
		var logicFieldValue,
			inputName = buildLogicFieldInputName( logicFieldArgs, depFieldArgs ),
			logicFieldInputs = document.querySelectorAll( 'input[name^="' + inputName + '"]' );

		if ( logicFieldInputs.length === 0 ) {
			// Don't continue if the field doesn't exist.
			return null;
		}

		if ( logicFieldArgs.inputType === 'checkbox' || logicFieldArgs.inputType === 'toggle' ) {
			logicFieldValue = getValuesFromCheckboxInputs( logicFieldInputs );
		} else {
			logicFieldValue = getValueFromRadioInputs( logicFieldInputs );
		}

		return logicFieldValue;
	}

	/**
	 * Build a logic field's input name
	 * Does not include full name for checkbox, address, or multi-select fields
	 *
	 * @param {object} logicFieldArgs
	 * @param {boolean} logicFieldArgs.isRepeating
	 * @param {string} logicFieldArgs.fieldId
	 * @param {object} depFieldArgs
	 * @param {string} depFieldArgs.inEmbedForm
	 * @param {string} depFieldArgs.inSection
	 * @param {string} depFieldArgs.repeatRow
	 * @returns {string}
     */
	function buildLogicFieldInputName( logicFieldArgs, depFieldArgs ) {
		var inputName = '';

		if ( logicFieldArgs.isRepeating ) {
			// If the trigger field is repeating, the child must be repeating as well
			var sectionId = '';
			if ( depFieldArgs.inEmbedForm !== '0' ) {
				sectionId = depFieldArgs.inEmbedForm;
			} else {
				sectionId = depFieldArgs.inSection;
			}
			var rowId = depFieldArgs.repeatRow;
			inputName = 'item_meta[' + sectionId + '][' + rowId + '][' + logicFieldArgs.fieldId + ']';
		} else {
			inputName = 'item_meta[' + logicFieldArgs.fieldId + ']';
		}

		return inputName;
	}

	function getValuesFromCheckboxInputs( inputs ) {
		var checkedVals = [];

		for ( var i = 0, l = inputs.length; i < l; i++ ) {
			if ( inputs[ i ].type === 'hidden' || inputs[ i ].checked ) {
				checkedVals.push( inputs[ i ].value );
			} else if ( typeof inputs[ i ].dataset.off !== 'undefined' ) {
				checkedVals.push( inputs[ i ].dataset.off );
			}
		}

		if ( checkedVals.length === 0 ) {
			checkedVals = false;
		}

		return checkedVals;
	}

	function cleanFinalFieldValue( fieldValue ) {
		if ( typeof fieldValue === 'undefined' ) {
			fieldValue = '';
		} else if ( typeof fieldValue === 'string' ) {
			fieldValue = fieldValue.trim();
		}

		return fieldValue;
	}

	/**
	 * Check whether a particular conditional logic condition is true or false
	 *
	 * @param {Array} logicCondition
	 * @param {operator:string} logicCondition.operator
	 * @param {value:string} logicCondition.value
	 * @param {String|Array} fieldValue
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldType
	 * @param {Object} logicFieldArgs
	 * @param {fieldType:string} logicFieldArgs.fieldType
     * @returns {Boolean}
     */
	function getLogicConditionOutcome( logicCondition, fieldValue, depFieldArgs, logicFieldArgs ) {
		var outcome;

		if ( depFieldArgs.fieldType === 'data' && logicFieldArgs.fieldType === 'data' ) {
			// If dep field is Dynamic and logic field is Dynamic
			outcome = getDynamicFieldLogicOutcome( logicCondition, fieldValue, depFieldArgs );
		} else {
			outcome = operators( logicCondition.operator, logicCondition.value, fieldValue );
		}

		return outcome;
	}

	/**
	 * @param {Array} logicCondition
	 * @param {string} logicCondition.operator
	 * @param {string} logicCondition.value
	 * @param {string|Array} fieldValue
	 * @param {object} depFieldArgs
	 * @param {Array} depFieldArgs.dataLogic
	 * @returns {boolean}
     */
	function getDynamicFieldLogicOutcome( logicCondition, fieldValue, depFieldArgs ) {
		var outcome = false;
		if ( logicCondition.value === '' ) {
			// Logic: "Dynamic field is equal to/not equal to anything"

			if ( fieldValue === '' || ( fieldValue.length == 1 && fieldValue[ 0 ] === '' ) ) {
				outcome = false;
			} else {
				outcome = true;
			}
		} else {
			// Logic: "Dynamic field is equal to/not equal to specific option"
			outcome = operators( logicCondition.operator, logicCondition.value, fieldValue );
		}
		depFieldArgs.dataLogic = logicCondition;
		depFieldArgs.dataLogic.actualValue = fieldValue;

		return outcome;
	}

	function operators( op, a, b ) {
		var theOperators;

		a = prepareLogicValueForComparison( a );
		b = prepareEnteredValueForComparison( a, b );

		if ( typeof a === 'string' && a.indexOf( '&quot;' ) != '-1' && operators( op, a.replace( '&quot;', '"' ), b ) ) {
			return true;
		}

		theOperators = {
			'==': function( c, d ) {
				return c === d;
			},
			'!=': function( c, d ) {
				return c !== d;
			},
			'<': function( c, d ) {
				return c > d;
			},
			'<=': function( c, d ) {
				return c >= d;
			},
			'>': function( c, d ) {
				return c < d;
			},
			'>=': function( c, d ) {
				return c <= d;
			},
			'LIKE': function( c, d ) {
				if ( ! d ) {
					/* If no value, then assume no match */
					return false;
				}

				c = prepareLogicValueForLikeComparison( c );
				d = prepareEnteredValueForLikeComparison( c, d );

				return d.indexOf( c ) != -1;
			},
			'not LIKE': function( c, d ) {
				if ( ! d ) {
					/* If no value, then assume no match */
					return true;
				}

				c = prepareLogicValueForLikeComparison( c );
				d = prepareEnteredValueForLikeComparison( c, d );

				return d.indexOf( c ) == -1;
			}
		};

		return theOperators[ op ]( a, b );
	}

	function prepareLogicValueForComparison( a ) {
		if ( String( a ).search( /^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/ ) !== -1 ) {
			a = parseFloat( a );
		} else if ( typeof a === 'string' ) {
			a = a.trim();
		}

		return a;
	}

	function prepareEnteredValueForComparison( a, b ) {
		if ( typeof b === 'undefined' || b === null || b === false ) {
			b = '';
		}

		if ( Array.isArray( b ) && jQuery.inArray( String( a ), b ) > -1 ) {
			b = a;
		}

		if ( typeof a === 'number' && typeof b === 'string' ) {
			b = parseFloat( b );
		}

		if ( typeof b === 'string' ) {
			b = b.trim();
		}

		return b;
	}

	function prepareLogicValueForLikeComparison( val ) {
		return prepareValueForLikeComparison( val );
	}

	function prepareEnteredValueForLikeComparison( logicValue, enteredValue ) {
		enteredValue = prepareValueForLikeComparison( enteredValue );

		var currentValue = '';
		if ( Array.isArray( enteredValue ) ) {
			for ( var i = 0, l = enteredValue.length; i < l; i++ ) {
				currentValue = enteredValue[ i ].toLowerCase();
				if ( currentValue.indexOf( logicValue ) > -1 ) {
					enteredValue = logicValue;
					break;
				}
			}
		}

		return enteredValue;
	}

	function prepareValueForLikeComparison( val ) {
		if ( typeof val === 'string' ) {
			val = val.toLowerCase();
		} else if ( typeof val === 'number' ) {
			val = val.toString();
		}
		return val;
	}

	/**
	 *
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.containerId
	 * @param {string} depFieldArgs.fieldType
	 * @param logicOutcomes
     */
	function routeToHideOrShowField( depFieldArgs, logicOutcomes ) {
		var onCurrentPage,
			action = getHideOrShowAction( depFieldArgs, logicOutcomes );

		if ( depFieldArgs.fieldType === 'submit' ) {
			onCurrentPage = isSubmitButtonOnPage( depFieldArgs.containerId );
		} else {
			onCurrentPage = isFieldDivOnPage( depFieldArgs.containerId );
		}

		if ( action == 'show' ) {
			if ( depFieldArgs.fieldType === 'data' && depFieldArgs.hasOwnProperty( 'dataLogic' ) ) {
				// Only update dynamic field options/value if it is dependent on another Dynamic field
				updateDynamicField( depFieldArgs, onCurrentPage );
			} else {
				showFieldAndSetValue( depFieldArgs, onCurrentPage );
			}
		} else {
			hideFieldAndClearValue( depFieldArgs, onCurrentPage );
		}
	}

	function isFieldDivOnPage( containerId ) {
		var fieldDiv = document.getElementById( containerId );

		return fieldDiv !== null;
	}

	/**
	 * Checks if specified submit button is on the current page.
	 *
	 * @param depFieldArgs
	 * @returns {boolean}
	 */
	function isSubmitButtonOnPage( container ) {
		var submitButton = document.querySelector( '#' + container );

		return submitButton != null;
	}

	/**
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.anyAll
	 * @param {string} depFieldArgs.showHide
	 * @param {Array} logicOutcomes
	 * @returns {string}
     */
	function getHideOrShowAction( depFieldArgs, logicOutcomes ) {
		if ( depFieldArgs.anyAll === 'any' ) {
			// If any of the following match logic
			if ( logicOutcomes.indexOf( true ) > -1 ) {
				action = depFieldArgs.showHide;
			} else {
				action = reverseAction( depFieldArgs.showHide );
			}
		} else {
			// If all of the following match logic
			if ( logicOutcomes.indexOf( false ) > -1 ) {
				action = reverseAction( depFieldArgs.showHide );
			} else {
				action = depFieldArgs.showHide;
			}
		}

		return action;
	}

	function reverseAction( action ) {
		if ( action === 'show' ) {
			action = 'hide';
		} else {
			action = 'show';
		}
		return action;
	}

	/**
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.containerId
	 * @param {string} depFieldArgs.formId
	 * @param {bool} onCurrentPage
     */
	function showFieldAndSetValue( depFieldArgs, onCurrentPage ) {
		if ( isFieldCurrentlyShown( depFieldArgs.containerId, depFieldArgs.formId ) ) {
			return;
		}

		removeFromHideFields( depFieldArgs.containerId, depFieldArgs.formId );

		if ( depFieldArgs.fieldType === 'submit' ) {
			if ( onCurrentPage ) {
				showOrEnableSubmitButton( depFieldArgs );
			}
			return;
		}
		if ( onCurrentPage ) {
			// Set value, then show field
			setValuesInsideFieldOnPage( depFieldArgs.containerId, depFieldArgs );
			showFieldContainer( depFieldArgs.containerId );
			triggerEvent( document, 'frmShowField' );
			if ( depFieldArgs.inputType === 'rte' ) {
				reInitializeRichText( 'field_' + depFieldArgs.fieldKey );
			}
		} else {
			setValuesInsideFieldAcrossPage( depFieldArgs );
		}
	}

	/**
	 * @param {string} fieldId
	 */
	function reInitializeRichText( fieldId ) {
		var isVisible = 'undefined' !== typeof tinyMCE.editors[ fieldId ] && ! tinyMCE.editors[ fieldId ].isHidden();
		if ( ! isVisible ) {
			return;
		}
		removeRichText( fieldId );
		initRichText( fieldId );
	}

	/**
	 * Show or enable submit button, as appropriate based on submit Conditional Logic.
	 *
	 * @param depFieldArgs
	 */
	function showOrEnableSubmitButton( depFieldArgs ) {
		if ( depFieldArgs.hideDisable && depFieldArgs.hideDisable === 'disable' ) {
			enableButton( '#' + depFieldArgs.containerId );
		} else {
			showFieldContainer( depFieldArgs.containerId );
		}

		removeSubmitButtonFromHiddenList( depFieldArgs );
	}

	/**
	 * Remove submit button from list of hidden submit buttons on page
	 *
	 * @param depFieldArgs
	 */
	function removeSubmitButtonFromHiddenList( depFieldArgs ) {
		hiddenSubmitButtons = hiddenSubmitButtons.filter( function( button ) {
			return button !== depFieldArgs.formKey;
		});
	}

	/**
	 * Enable button with given selector.
	 *
	 * @param buttonSelector
	 */
	function enableButton( buttonSelector ) {
		jQuery( buttonSelector ).prop( 'disabled', false );
	}

	/**
	 * Set the value for all inputs inside of a field div on the current page
	 *
	 * @param {string} container
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldType
	 * @param {string} depFieldArgs.formId
 	 */
	function setValuesInsideFieldOnPage( container, depFieldArgs ) {
		var inputs = getInputsInFieldOnPage( container ),
			inContainer = ( depFieldArgs.fieldType === 'divider' || depFieldArgs.fieldType === 'form' );

		setValueForInputs( inputs, inContainer, depFieldArgs.formId, 'required' );
	}

	/**
	 * Set the value for all inputs inside of a field across a page
	 *
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldType
	 * @param {string} depFieldArgs.formId
	 */
	function setValuesInsideFieldAcrossPage( depFieldArgs ) {
		var inputs = getInputsInFieldAcrossPage( depFieldArgs ),
			inContainer = ( depFieldArgs.fieldType === 'divider' || depFieldArgs.fieldType === 'form' );

		setValueForInputs( inputs, inContainer, depFieldArgs.formId );
	}

	/**
	 * Gets inputs in field on page.
	 *
	 * @since 5.4 Accepted HTML element as param.
	 *
	 * @param {String|HTMLElement} containerId ID of container or container element.
	 * @return {Array}
	 */
	function getInputsInFieldOnPage( containerId ) {
		var container = 'string' === typeof containerId ? document.getElementById( containerId ) : containerId;
		return container.querySelectorAll( 'select[name^="item_meta"], textarea[name^="item_meta"], input[name^="item_meta"]' );
	}

	/**
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldType
	 * @returns {Array}
     */
	function getInputsInFieldAcrossPage( depFieldArgs ) {
		var inputs = [];

		if ( depFieldArgs.fieldType === 'divider' ) {
			inputs = getInputsInHiddenSection( depFieldArgs );
		} else if ( depFieldArgs.fieldType === 'form' ) {
			inputs = getInputsInHiddenEmbeddedForm( depFieldArgs );
		} else {
			inputs = getHiddenInputs( depFieldArgs );
		}

		return inputs;
	}

	/**
	 * Get the inputs for a non-repeating field that is type=hidden
	 * @param {object} depFieldArgs
	 * @param {bool} depFieldArgs.isRepeating
	 * @param {string} depFieldArgs.inSection
	 * @param {string} depFieldArgs.repeatRow
	 * @param {string} depFieldArgs.fieldId
	 * @returns {NodeList}
     */
	function getHiddenInputs( depFieldArgs ) {
		var name = '';
		if ( depFieldArgs.isRepeating ) {
			//item_meta[section-id][row-id][field-id]
			var containerFieldId = getContainerFieldId( depFieldArgs );
			name = 'item_meta[' + containerFieldId + '][' + depFieldArgs.repeatRow + '][' + depFieldArgs.fieldId + ']';
		} else {
			// item_meta[field-id]
			name = 'item_meta[' + depFieldArgs.fieldId + ']';
		}
		return document.querySelectorAll( '[name^="' + name + '"]' );
	}

	function setValueForInputs( inputs, inContainer, formId, setRequired ) {
		var input, prevInput, i;

		if ( ! inputs.length ) {
			return;
		}

		for ( i = 0; i < inputs.length; i++ ) {
			input = inputs[ i ];

			// Don't set the value if the field is in a section and it's conditionally hidden
			if ( inContainer && isChildInputConditionallyHidden( input, formId ) ) {
				continue;
			}

			if ( setRequired === 'required' ) {
				maybeAddRequiredTag( input );
			}

			if ( skipSetValue( i, prevInput, inputs ) ) {
				continue;
			}

			setDefaultValue( input, inContainer );
			maybeSetWatchingFieldValue( input );
			setShownProduct( input );
			maybeDoCalcForSingleField( input );

			prevInput = input;
		}
	}

	/**
	 * When a field is shown with logic, add the html and aria
	 * required attributes if the field is required.
	 */
	function maybeAddRequiredTag( input ) {
		var isRequired, isOptional;

		if ( input.type === 'checkbox' || input.type === 'radio' || input.type === 'file' ) {
			return;
		}

		isRequired = input.parentElement.className.indexOf( 'frm_required_field' );
		isOptional = input.className.indexOf( 'frm_optional' );

		if ( isRequired > -1 && isOptional === -1 ) {
			input.setAttribute( 'aria-required', true );
		}
	}

	/**
	 * Don't loop through every input in a radio/checkbox/other field
	 * TODO: Improve this for checkboxes and address fields
	 */
	function skipSetValue( i, prevInput, inputs ) {
		var typeArray = [ 'checkbox', 'radio' ];

		if ( i < 1 || typeof prevInput === 'undefined' ) {
			return false;
		}

		if ( null !== inputs[ i ].getAttribute( 'data-frmprice' ) ) {
			// never skip calculating a product price.
			return false;
		}

		var isOther = inputs[ i ].className.indexOf( 'frm_other_input' ) !== -1;
		return isOther || ( prevInput.name == inputs[ i ].name && typeArray.indexOf( prevInput.type ) > -1 );
	}

	// Check if a field input inside of a section or embedded form is conditionally hidden
	function isChildInputConditionallyHidden( input, formId ) {
		var fieldDivPart = frmFrontForm.getFieldId( input, true ),
			fieldDivId = 'frm_field_' + fieldDivPart + '_container';

		return isFieldConditionallyHidden( fieldDivId, formId );
	}

	function showFieldContainer( containerId ) {
		var $container = jQuery( '#' + containerId ).show();
		if ( $container.hasClass( 'frm_inside_container' ) && null === $container.find( 'select' ).val() ) {
			$container.find( 'select' ).val( '' ).trigger( 'change' );
		}
	}

	/**
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.containerId
	 * @param {string} depFieldArgs.formId
	 * @param onCurrentPage
     */
	function hideFieldAndClearValue( depFieldArgs, onCurrentPage ) {
		if ( isFieldConditionallyHidden( depFieldArgs.containerId, depFieldArgs.formId ) ) {
			return;
		}

		addToHideFields( depFieldArgs.containerId, depFieldArgs.formId );

		if ( depFieldArgs.fieldType === 'submit' ) {
			if ( onCurrentPage ) {
				hideOrDisableSubmitButton( depFieldArgs );
			}
			return;
		}

		if ( onCurrentPage ) {
			hideFieldContainer( depFieldArgs.containerId );
			clearInputsInFieldOnPage( depFieldArgs.containerId );
		} else {
			clearInputsInFieldAcrossPage( depFieldArgs );
		}
	}

	/**
	 * Hide or disable given submit button as appropriate, based on submit Conditional Logic
	 *
	 * @param depFieldArgs
	 */
	function hideOrDisableSubmitButton( depFieldArgs ) {
		if ( depFieldArgs.containerId == undefined ) {
			depFieldArgs.containerId = getSubmitButtonContainerID( depFieldArgs );
		}

		addSubmitButtonToHiddenList( depFieldArgs );

		if ( depFieldArgs.hideDisable && depFieldArgs.hideDisable === 'disable' ) {
			disableButton( '#' + depFieldArgs.containerId );
		} else {
			hideFieldContainer( depFieldArgs.containerId );
		}
	}

	/**
	 * Add submit button to list of hidden submit buttons on page
	 *
	 * @param depFieldArgs
	 */
	function addSubmitButtonToHiddenList( depFieldArgs ) {
		hiddenSubmitButtons.push( depFieldArgs.formKey );
	}

	/**
	 * Checks if a particular submit button on the current page is hidden
	 *
	 * @param {string} formKey
	 * @returns {boolean}
	 */
	function isOnPageSubmitButtonHidden( formKey ) {
		return hiddenSubmitButtons.indexOf( formKey ) !== -1;
	}

	/**
	 * Hide submit button with specified container ID that was previously hidden.
	 *
	 * @param submitContainerID
	 */
	function hidePreviouslyHiddenSubmitButton( submitContainerID ) {
		var formId = submitContainerID.replace( 'frm_form_', '' );
		formId = formId.replace( '_container .frm_final_submit', '' );

		var depFieldArgs = getRulesForSingleField( 'submit_' + formId );
		if ( depFieldArgs ) {
			hideOrDisableSubmitButton( depFieldArgs );
		}
	}

	/**
	 * Get the form key (e.g. t3cq5) from the form element id (e.g. form_t3cq5)
	 *
	 * @param elementId
	 * @returns {string} form key
	 */
	function getFormKeyFromFormElementID( elementId ) {
		return elementId.replace( 'form_', '' );
	}

	function hideFieldContainer( containerId ) {
		jQuery( '#' + containerId ).hide();
	}

	/**
	 * Disable button with specified selector
	 *
	 * @param buttonSelector
	 */
	function disableButton( buttonSelector ) {
		jQuery( buttonSelector ).prop( 'disabled', true );
	}

	function jsonParse( str ) {
		try {
			var obj = JSON.parse( str );
			return obj;
		} catch ( e ) {
			return false;
		}
	}

	function clearInputsInFieldOnPage( containerId ) {
		var inputs = getInputsInFieldOnPage( containerId );
		clearValueForInputs( inputs, 'required' );
	}

	function clearInputsInFieldAcrossPage( depFieldArgs ) {
		var inputs = getInputsInFieldAcrossPage( depFieldArgs );
		clearValueForInputs( inputs );
	}

	/**
	 * Get all the child inputs in a hidden section (across a page)
	 *
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldType
	 * @param {string} depFieldArgs.fieldId
 	 */
	function getInputsInHiddenSection( depFieldArgs ) {
		// If a section, get all inputs with data attribute
		var inputs = [];
		if ( depFieldArgs.fieldType === 'divider' ) {
			inputs = document.querySelectorAll( '[data-sectionid="' + depFieldArgs.fieldId + '"]' );
		}

		return inputs;
	}

	// Get all the child inputs in a hidden embedded form (across a page)
	function getInputsInHiddenEmbeddedForm( depFieldArgs ) {
		// TODO: maybe remove form and [0]?
		// TODO: what if someone is using embeddedformfieldkey-moretext as their field key? Add data attribute for this
		return document.querySelectorAll( '[id^="field_' + depFieldArgs.fieldKey + '-"]' );
	}

	/**
	 * Clears value of field inputs.
	 *
	 * @since 5.4 Added the third param.
	 *
	 * @param {Array}   inputs         Array of inputs.
	 * @param {String}  required       Required string.
	 * @param {Boolean} resetToDefault Is `true` if reset to default value. Otherwise, reset to empty value.
	 */
	function clearValueForInputs( inputs, required, resetToDefault ) {
		var prevInput, blankSelect, valueChanged, l, i, input, defaultVal, reset;

		if ( inputs.length < 1 ) {
			return;
		}

		valueChanged = true;

		l = inputs.length;
		for ( i = 0; i < l; i++ ) {
			input = inputs[ i ];

			defaultVal = input.getAttribute( 'data-frmval' );
			reset      = resetToDefault && defaultVal;

			// Don't remove values from some fields.
			if ( input.className.indexOf( 'frm_dnc' ) > -1 || input.name.indexOf( '[row_ids]' ) > -1 ) {
				prevInput = input;
				continue;
			}

			if ( i > 0 && prevInput.name != input.name && valueChanged === true ) {
				// Only trigger a change after all inputs in a field are cleared
				triggerChange( jQuery( prevInput ) );
			}

			valueChanged = true;

			if ( input.type === 'radio' || input.type === 'checkbox' ) {
				if ( ! reset ) {
					input.checked = false;
				} else if ( 'radio' === input.type ) {
					input.checked = defaultVal === input.value;
				} else {
					resetCheckboxInputToValue( input, defaultVal );
				}

				maybeClearStarRatingInput( input );
			} else if ( input.tagName === 'SELECT' ) {
				if ( ! reset ) {
					blankSelect = input.selectedIndex === 0 && input.options[ 0 ].text.trim() === '';
					if ( blankSelect || ( input.selectedIndex === -1 ) ) {
						valueChanged = false;
					} else {
						input.selectedIndex = -1;
					}
				} else {
					valueChanged = resetSelectInputToValue( input, defaultVal );
				}

				var chosenId = input.id.replace( /[^\w]/g, '_' ); // match what the script is doing
				var autocomplete = document.getElementById( chosenId + '_chosen' );
				if ( autocomplete !== null ) {
					jQuery( input ).trigger( 'chosen:updated' );
				}
			} else if ( input.type === 'range' ) {
				if ( ! reset ) {
					input.value = 0;
				} else {
					input.value = defaultVal;
				}
			} else if ( input.getAttribute( 'data-frmprice' ) !== null ) {
				setHiddenProduct( input );
			} else {
				if ( ! reset ) {
					input.value = '';
				} else {
					input.value = defaultVal;
				}

				if ( null !== input.getAttribute( 'data-frmfile' ) ) {
					clearDropzoneFiles( input );
				}
			}

			if ( required === 'required' ) {
				input.required = false;
				input.setAttribute( 'aria-required', false );
			}

			prevInput = inputs[ i ];
		}

		// trigger a change for the final input in the loop
		if ( valueChanged === true ) {
			triggerChange( jQuery( prevInput ) );
		}
	}

	/**
	 * Maybe clear star rating.
	 *
	 * @since 5.4
	 *
	 * @param {HTMLElement} input This should be the last input in star rating.
	 */
	function maybeClearStarRatingInput( input ) {
		var starGroup, checkedInput;
		if ( 'radio' !== input.type || ! input.matches( '.frm-star-group input:last-of-type' ) ) {
			return;
		}

		starGroup    = input.closest( '.frm-star-group' );
		checkedInput = starGroup.querySelector( 'input:checked' );
		if ( checkedInput ) {
			updateStars( checkedInput );
		} else {
			clearStars( starGroup, true );
		}
	}

	/**
	 * Resets checkbox input to a value.
	 *
	 * @since 5.4
	 *
	 * @param {HTMLElement} input Checkbox element.
	 * @param {Object}      val   The value.
	 */
	function resetCheckboxInputToValue( input, val ) {
		var i;

		val = jsonParse( val );

		if ( ! val ) {
			return;
		}

		for ( i in val ) {
			if ( val[ i ] === input.value ) {
				input.checked = true;
				return;
			}
		}

		input.checked = false;
	}

	/**
	 * Resets select input to a value.
	 *
	 * @since 5.4
	 *
	 * @param {HTMLElement}   input Select element.
	 * @param {String|Object} val   The value. Is object if multiselect.
	 * @return {Boolean}            Return `true` if value is changed.
	 */
	function resetSelectInputToValue( input, val ) {
		if ( input.multiple ) {
			return resetMultiSelectInputToValue( input, val );
		}

		var i,
			valueChanged = false,
			options = input.querySelectorAll( 'option' );

		for ( i = 0; i < options.length; i++ ) {
			if ( val === options[ i ].value && ! options[ i ].selected ) {
				options[ i ].selected = true;
				valueChanged = true;
				continue;
			}

			if ( val !== options[ i ].value && options[ i ].selected ) {
				options[ i ].selected = false;
				valueChanged = true;
			}
		}

		return valueChanged;
	}

	/**
	 * Resets multiselect input to a value.
	 *
	 * @since 5.4
	 *
	 * @param {HTMLElement} input Select element.
	 * @param {Object}      val   The value.
	 * @return {Boolean}          Return `true` if value is changed.
	 */
	function resetMultiSelectInputToValue( input, val ) {
		val = jsonParse( val );

		if ( ! val ) {
			return false;
		}

		var i, contained,
			valueChanged = false,
			options = input.querySelectorAll( 'option' );

		for ( i = 0; i < options.length; i++ ) {
			contained = objectContainValue( val, options[ i ].value );
			if ( contained && ! options[ i ].selected ) {
				options[ i ].selected = true;
				valueChanged = true;
				continue;
			}

			if ( ! contained && options[ i ].selected ) {
				options[ i ].selected = false;
				valueChanged = true;
			}
		}

		return valueChanged;
	}

	/**
	 * Checks if object contains a value.
	 *
	 * @since 5.4
	 *
	 * @param {Object} obj Object.
	 * @param {Mixed}  val Value.
	 * @return {Boolean}
	 */
	function objectContainValue( obj, val ) {
		var x;

		for ( x in obj ) {
			if ( obj[ x ] === val ) {
				return true;
			}
		}

		return false;
	}

	function clearDropzoneFiles( hiddenFileIdField ) {
		var dropzoneElement = hiddenFileIdField.nextElementSibling;
		if ( dropzoneElement && -1 !== dropzoneElement.className.indexOf( 'frm_dropzone' ) && 'object' === typeof dropzoneElement.dropzone && 'function' === typeof dropzoneElement.dropzone.removeAllFiles ) {
			dropzoneElement.dropzone.removeAllFiles( true );
		}
	}

	function isFieldCurrentlyShown( containerId, formId ) {
		return isFieldConditionallyHidden( containerId, formId ) === false;
	}

	function isFieldConditionallyHidden( containerId, formId ) {
		var hidden = false,
			hiddenFields = getHiddenFields( formId );

		if ( hiddenFields.indexOf( containerId ) > -1 ) {
			hidden = true;
		}

		return hidden;
	}

	function clearHideFields() {
		var hideFieldInputs = document.querySelectorAll( '[id^="frm_hide_fields_"]' );
		clearValueForInputs( hideFieldInputs );
	}

	function addToHideFields( htmlFieldId, formId ) { //TODO: why is this run on submit?
		// Get all currently hidden fields
		var hiddenFields = getHiddenFields( formId );

		if ( hiddenFields.indexOf( htmlFieldId ) > -1 ) {
			// If field id is already in the array, move on
		} else {
			// Add new conditionally hidden field to array
			hiddenFields.push( htmlFieldId );

			// Set the hiddenFields value in the frm_hide_field_formID input
			hiddenFields = JSON.stringify( hiddenFields );
			var frmHideFieldsInput = document.getElementById( 'frm_hide_fields_' + formId );
			if ( frmHideFieldsInput !== null ) {
				frmHideFieldsInput.value = hiddenFields;
			}
		}
	}

	function getAllHiddenFields() {
		var formId, i,
			hiddenFields = [],
			hideFieldInputs = document.querySelectorAll( '*[id^="frm_hide_fields_"]' ),
			formTotal = hideFieldInputs.length;

		for ( i = 0; i < formTotal; i++ ) {
			formId = hideFieldInputs[ i ].id.replace( 'frm_hide_fields_', '' );
			hiddenFields = hiddenFields.concat( getHiddenFields( formId ) );
		}

		return hiddenFields;
	}

	function getHiddenFields( formId ) {
		var hiddenFields = [];

		// Fetch the hidden fields from the frm_hide_fields_formId input
		var frmHideFieldsInput = document.getElementById( 'frm_hide_fields_' + formId );
		if ( frmHideFieldsInput === null ) {
			return hiddenFields;
		}

		hiddenFields = frmHideFieldsInput.value;
		if ( hiddenFields ) {
			hiddenFields = JSON.parse( hiddenFields );
		} else {
			hiddenFields = [];
		}

		return hiddenFields;
	}

	function setDefaultValue( input, inContainer ) {
		var placeholder,
			$input = jQuery( input ),
			defaultValue = $input.data( 'frmval' );

		if ( typeof defaultValue === 'undefined' && input.classList.contains( 'wp-editor-area' ) ) {
			// set value in rich text
			var defaultField = document.getElementById( input.id + '-frmval' );
			if ( defaultField !== null ) {
				defaultValue = defaultField.value;
				var targetTinyMceEditor = tinymce.get( input.id );
				if ( null !== targetTinyMceEditor ) {
					targetTinyMceEditor.setContent( defaultValue );
				}
			}
		} else if ( typeof defaultValue === 'undefined' && input.type === 'hidden' ) {
			//with read only select input, value is in sibling select
			var $select = $input.next( 'select[disabled]' );
			if ( $select.length > 0 ) {
				defaultValue = $select.data( 'frmval' );
			}
		}

		placeholder = defaultValue;
		defaultValue = setDropdownPlaceholder( defaultValue, input );
		if ( placeholder !== defaultValue ) {
			placeholder = true;
		}

		if ( typeof defaultValue !== 'undefined' ) {
			var numericKey = new RegExp( /\[\d*\]$/i );

			if ( input.type === 'checkbox' || input.type === 'radio' ) {
				setCheckboxOrRadioDefaultValue( input.name, defaultValue );

			} else if ( input.type === 'hidden' && input.name.indexOf( '[]' ) > -1 ) {
				setHiddenCheckboxDefaultValue( input.name, defaultValue );

			// Set for hidden checkbox fields that aren't on the current page. Skip hidden fields in a repeater.
			} else if ( ! inContainer && input.type === 'hidden' && input.name.indexOf( '][' ) > -1 && numericKey.test( input.name ) ) {
				setHiddenCheckboxDefaultValue( input.name.replace( numericKey, '' ), defaultValue );

			} else {
				if ( defaultValue.constructor === Object ) {
					var addressType = input.getAttribute( 'name' ).split( '[' ).slice( -1 )[ 0 ];
					if ( addressType !== null ) {
						addressType = addressType.replace( ']', '' );
						defaultValue = defaultValue[ addressType ];
						if ( typeof defaultValue == 'undefined' ) {
							defaultValue = '';
						}
					}
				}

				if ( typeof defaultValue === 'object' ) {
					// Don't replace a field shortcode with the id.
					defaultValue = '[' + defaultValue + ']';
				}

				input.value = defaultValue;
			}

			if ( ! placeholder && input.tagName === 'SELECT' ) {
				maybeUpdateChosenOptions( input );
				if ( input.value === '' ) {
					setOtherSelectValue( input, defaultValue );
				}
			}

			triggerChange( $input );
		}
	}

	/**
	 * Select the option with placeholder if applicable.
	 */
	function setDropdownPlaceholder( defaultValue, input ) {
		var placeholder;
		if ( typeof defaultValue === 'undefined' && input.tagName === 'SELECT' ) {
			placeholder = input.getAttribute( 'data-placeholder' );
			if ( placeholder !== null ) {
				defaultValue = '';
			}
		}
		return defaultValue;
	}

	function setCheckboxOrRadioDefaultValue( inputName, defaultValue ) {
		// Get all checkbox/radio inputs for this field
		var radioInputs = document.getElementsByName( inputName ),
			isSet = false,
			firstInput = false;

		if ( typeof defaultValue === 'object' ) {
			// Convert the object to an array.
			defaultValue = Object.keys( defaultValue ).map(
				function( key ) {
					return defaultValue[ key ];
				}
			);
		}

		// Loop through options and set the default value
		for ( var i = 0, l = radioInputs.length; i < l; i++ ) {
			if ( firstInput === false ) {
				firstInput = radioInputs[ i ];
			}

			if ( radioInputs[ i ].type === 'hidden' ) {
				// If field is read-only and there is a hidden input
				if ( Array.isArray( defaultValue ) && defaultValue[ i ] !== null ) {
					radioInputs[ i ].value = defaultValue[ i ];
				} else {
					radioInputs[ i ].value = defaultValue;
				}
				isSet = true;
			} else if ( radioInputs[ i ].value == defaultValue ||
				( Array.isArray( defaultValue ) && defaultValue.indexOf( radioInputs[ i ].value ) > -1 ) ) {
				// If input's value matches the default value, set checked to true

				radioInputs[ i ].checked = true;
				isSet = true;
				if ( radioInputs[ i ].type === 'radio' ) {
					break;
				}
			}
		}

		if ( ! isSet && firstInput !== false ) {
			setOtherValueLimited( firstInput, defaultValue );
		}
	}

	// Set the default value for hidden checkbox or multi-select dropdown fields
	function setHiddenCheckboxDefaultValue( inputName, defaultValue ) {
		// Get all the hidden inputs with the same name
		var hiddenInputs = jQuery( 'input[name^="' + inputName + '"]' ).get();

		if ( typeof defaultValue === 'object' ) {
			// Convert the object to an array.
			defaultValue = Object.keys( defaultValue ).map(
				function( key ) {
					return defaultValue[ key ];
				}
			);
		}
		if ( Array.isArray( defaultValue ) ) {
			for ( var i = 0, l = defaultValue.length; i < l; i++ ) {
				if ( i in hiddenInputs ) {
					hiddenInputs[ i ].value = defaultValue[ i ];
				} else {
					// TODO: accommodate for when there are multiple default values but the user has removed some
				}
			}
		} else if ( hiddenInputs[ 0 ] !== null && typeof hiddenInputs[ 0 ] !== 'undefined' ) {
			hiddenInputs[ 0 ].value = defaultValue;
		}
	}

	function removeFromHideFields( htmlFieldId, formId ) {
		// Get all currently hidden fields
		var hiddenFields = getHiddenFields( formId );

		// If field id is in the array, delete it
		var itemIndex = hiddenFields.indexOf( htmlFieldId );
		if ( itemIndex > -1 ) {
			// Remove field from the hiddenFields array
			hiddenFields.splice( itemIndex, 1 );

			// Update frm_hide_fields_formId input
			hiddenFields = JSON.stringify( hiddenFields );
			var frmHideFieldsInput = document.getElementById( 'frm_hide_fields_' + formId );
			frmHideFieldsInput.value = hiddenFields;
		}
	}

	/*****************************************************
	 * Lookup Field Functions
	 ******************************************************/

	/**
	 * Check all fields that are "watching" a lookup field that changed
 	 */
	function checkFieldsWatchingLookup( fieldId, changedInput, originalEvent ) {
		if ( typeof __FRMLOOKUP  === 'undefined' ||
			typeof __FRMLOOKUP[ fieldId ] === 'undefined' ||
			__FRMLOOKUP[ fieldId ].dependents.length < 1 ||
			changedInput === null ||
			typeof changedInput === 'undefined'
		) {
			return;
		}

		var triggerFieldArgs = __FRMLOOKUP[ fieldId ];

		var parentRepeatArgs = getRepeatArgsFromFieldName( changedInput[ 0 ].name );

		for ( var i = 0, l = triggerFieldArgs.dependents.length; i < l; i++ ) {
			updateWatchingFieldById( triggerFieldArgs.dependents[ i ], parentRepeatArgs, originalEvent );
		}
	}

	/**
	 * Update all instances of a "watching" field
	 *
	 * @since 2.01.0
	 * @param {string} fieldId
	 * @param {Object} parentRepeatArgs
	 * @param {string} originalEvent
     */
	function updateWatchingFieldById( fieldId, parentRepeatArgs, originalEvent ) {
		var childFieldArgs = getLookupArgsForSingleField( fieldId );

		// If lookup field has no parents, no need to update this field
		if ( childFieldArgs === false || childFieldArgs.parents.length < 1 ) {
			return;
		}

		if ( childFieldArgs.fieldType == 'lookup' ) {
			updateLookupFieldOptions( childFieldArgs, parentRepeatArgs );
		} else {
			// If the original event was NOT triggered from a direct value change to the Lookup field,
			// do not update the text field value
			if ( originalEvent === 'value changed' ) {
				updateWatchingFieldValue( childFieldArgs, parentRepeatArgs );
			}
		}
	}

	/**
	 * Update a Lookup field's options
	 *
	 * @param {Object} childFieldArgs
	 * @param {Object} parentRepeatArgs
	 * @param {String} parentRepeatArgs.repeatRow
     */
	function updateLookupFieldOptions( childFieldArgs, parentRepeatArgs ) {
		var childFieldElements = [];
		if ( parentRepeatArgs.repeatRow !== '' ) {
			childFieldElements = getRepeatingFieldDivOnCurrentPage( childFieldArgs, parentRepeatArgs );
		} else {
			childFieldElements = getAllFieldDivsOnCurrentPage( childFieldArgs );
		}

		for ( var i = 0, l = childFieldElements.length; i < l; i++ ) {
			addRepeatRow( childFieldArgs, childFieldElements[ i ].id );
			updateSingleLookupField( childFieldArgs, childFieldElements[ i ]);
		}
	}

	/**
	 * Get the div for a repeating field on the current page
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.fieldId
	 * @param {Object} parentRepeatArgs
	 * @param {string} parentRepeatArgs.repeatingSection
	 * @param {string} parentRepeatArgs.repeatRow
	 * @returns {Array}
     */
	function getRepeatingFieldDivOnCurrentPage( childFieldArgs, parentRepeatArgs ) {
		var childFieldDivs = [],
			selector = 'frm_field_' + childFieldArgs.fieldId + '-';

		selector += parentRepeatArgs.repeatingSection + '-' + parentRepeatArgs.repeatRow + '_container';
		var container = document.getElementById( selector );
		if ( container !== null ) {
			childFieldDivs.push( container );
		}

		return childFieldDivs;
	}

	function updateWatchingFieldValue( childFieldArgs, parentRepeatArgs ) {
		var childFieldElements = getAllTextFieldInputs( childFieldArgs, parentRepeatArgs );

		for ( var i = 0, l = childFieldElements.length; i < l; i++ ) {
			addRepeatRowForInput( childFieldElements[ i ].name, childFieldArgs );
			updateSingleWatchingField( childFieldArgs, childFieldElements[ i ]);
		}
	}

	/**
	 * Get the Lookup Args for a field ID
	 *
	 * @param {string} fieldId
	 * @return {boolean|Object}
     */
	function getLookupArgsForSingleField( fieldId ) {
		if ( typeof __FRMLOOKUP  === 'undefined' || typeof __FRMLOOKUP[ fieldId ] === 'undefined' ) {
			return false;
		}

		return __FRMLOOKUP[ fieldId ];
	}

	/**
	 * Update a single Lookup field
	 *
	 * @since 2.01.0
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.inputType
	 * @param {object} childElement
     */
	function updateSingleLookupField( childFieldArgs, childElement ) {
		childFieldArgs.parentVals = getParentLookupFieldVals( childFieldArgs );

		if ( childFieldArgs.inputType === 'select' ) {
			maybeReplaceSelectLookupFieldOptions( childFieldArgs, childElement );
		} else if ( childFieldArgs.inputType === 'radio' || childFieldArgs.inputType === 'checkbox' ) {
			maybeReplaceCbRadioLookupOptions( childFieldArgs, childElement );
		} else if ( childFieldArgs.inputType === 'data' ) {
			maybeReplaceLookupList( childFieldArgs, childElement );
		}
	}

	/**
	 * Update a standard field that is "watching" a Lookup
	 *
	 * @since 2.01.0
	 * @param {Object} childFieldArgs
	 * @param {object} childElement
	 */
	function updateSingleWatchingField( childFieldArgs, childElement ) {
		childFieldArgs.parentVals = getParentLookupFieldVals( childFieldArgs );

		if ( currentLookupHasQueue( childElement.id ) ) {
			addLookupToQueueOfTwo( childFieldArgs, childElement );
			return;
		}

		addLookupToQueueOfTwo( childFieldArgs, childElement );

		maybeInsertValueInFieldWatchingLookup( childFieldArgs, childElement );
	}

	/**
	 * Get all the occurences of a specific Text field
	 *
	 * @since 2.01.0
	 * @param {Object} childFieldArgs
	 * @param {boolean} childFieldArgs.isRepeating
	 * @param {string} childFieldArgs.fieldKey
	 * @param {Object} parentRepeatArgs
	 * @param {string} parentRepeatArgs.repeatingSection
	 * @param {string} parentRepeatArgs.repeatRow
	 * @return {NodeList}
 	 */
	function getAllTextFieldInputs( childFieldArgs, parentRepeatArgs ) {
		var selector = 'field_' + childFieldArgs.fieldKey;
		if ( childFieldArgs.isRepeating ) {
			if ( parentRepeatArgs.repeatingSection !== '' ) {
				// If trigger field is repeating/embedded, use its section row in selector
				selector = '[id="' + selector + '-' + parentRepeatArgs.repeatRow + '"]';
			} else {
				// If trigger field is not repeating/embedded, get all repeating field inputs
				selector = '[id^="' + selector + '-"]';
			}
		} else {
			selector = '[id="' + selector + '"]';
		}

		return document.querySelectorAll( selector );
	}

	// Set the value in a regular field that is watching a lookup field when it is conditionally shown
	function maybeSetWatchingFieldValue( input ) {
		var fieldId = frmFrontForm.getFieldId( input, false ),
			childFieldArgs = getLookupArgsForSingleField( fieldId );

		// If lookup field has no parents, no need to update this field
		if ( childFieldArgs === false || childFieldArgs.fieldType === 'lookup' ) {
			return;
		}

		updateSingleWatchingField( childFieldArgs, input, 'value changed' );
	}

	/**
	 * Get all divs on the current page for a given field
	 *
	 * @since 2.01.0
	 * @param {Object} childFieldArgs
	 * @param {boolean} childFieldArgs.isRepeating
	 * @param {string} childFieldArgs.fieldId
	 * @returns {Array}
     */
	function getAllFieldDivsOnCurrentPage( childFieldArgs ) {
		var childFieldDivs = [];

		if ( childFieldArgs.isRepeating ) {
			childFieldDivs = document.querySelectorAll( '.frm_field_' + childFieldArgs.fieldId + '_container' );
		} else {
			var container = document.getElementById( 'frm_field_' + childFieldArgs.fieldId + '_container' );
			if ( container !== null ) {
				childFieldDivs.push( container );
			}
		}

		return childFieldDivs;
	}

	// Get the field values from all parents
	function getParentLookupFieldVals( childFieldArgs ) {
		var parentFieldArgs,
			parentVals = [],
			parentIds = childFieldArgs.parents,
			parentValue = false;

		for ( var i = 0, l = parentIds.length; i < l; i++ ) {
			parentFieldArgs = getLookupArgsForSingleField( parentIds[ i ]);
			parentValue = getFieldValue( parentFieldArgs, childFieldArgs );

			// If any parents have blank values, don't waste time looking for values
			if ( parentValue === '' || parentValue === false ) {
				parentVals = false;
				break;
			}

			parentVals[ i ] = parentValue;
		}

		return parentVals;
	}

	// Get the value from array of radio inputs (could be type="hidden" or type="radio")
	function getValueFromRadioInputs( radioInputs ) {
		var radioValue = false,
			l = radioInputs.length;

		for ( var i = 0; i < l; i++ ) {
			if ( radioInputs[ i ].type === 'hidden' || radioInputs[ i ].checked ) {
				radioValue = radioInputs[ i ].value;
				break;
			}
		}

		return radioValue;
	}

	/**
	 * Maybe replace the options in a Select Lookup field
	 *
	 * @since 2.01.0
	 * @param {Object} childFieldArgs
	 * @param {Array} childFieldArgs.parents
	 * @param {Array} childFieldArgs.parentVals
	 * @param {string} childFieldArgs.fieldId
	 * @param {string} childFieldArgs.fieldKey
	 * @param {string} childFieldArgs.formId
	 * @param {object} childDiv
	 */
	function maybeReplaceSelectLookupFieldOptions( childFieldArgs, childDiv ) {
		// Get select within childDiv
		var childSelect = childDiv.getElementsByTagName( 'SELECT' )[ 0 ];
		if ( childSelect === null ) {
			return;
		}

		var currentValue = childSelect.value;

		if ( childFieldArgs.parentVals === false  ) {
			// If any parents have blank values, don't waste time looking for values
			childSelect.options.length = 1;
			childSelect.value = '';
			maybeUpdateChosenOptions( childSelect );

			if ( currentValue !== '' ) {
				triggerChange( jQuery( childSelect ), childFieldArgs.fieldKey );
			}

		} else {
			disableLookup( childSelect );
			disableFormPreLookup( childFieldArgs.formId );

			getLookupValues(
				childFieldArgs,
				function( newOptions ) {
					replaceSelectLookupFieldOptions( childFieldArgs, childSelect, newOptions );
					triggerLookupOptionsLoaded( jQuery( childDiv ) );
					enableFormAfterLookup( childFieldArgs.formId );
				}
			);
		}
	}

	// Update chosen options if autocomplete is enabled
	function maybeUpdateChosenOptions( childSelect ) {
		if ( childSelect.className.indexOf( 'frm_chzn' ) > -1 && jQuery().chosen ) {
			jQuery( childSelect ).trigger( 'chosen:updated' );
		}
	}

	/**
	 * Disable a Select Lookup field and add loading image
	 *
	 * @since 2.02.11
	 * @param {object} childSelect
	 */
	function disableLookup( childSelect ) {
		childSelect.className = childSelect.className + ' frm_loading_lookup';
		childSelect.disabled = true;
		maybeUpdateChosenOptions( childSelect );
	}

	/**
	 * Disable a form prior to a Lookup field's Ajax request
	 *
	 * @since 2.03.02
	 * @param {String} formId
     */
	function disableFormPreLookup( formId ) {
		processesRunning++;

		if ( processesRunning === 1 ) {

			var form = getFormById( formId );
			if ( form !== null ) {
				frmFrontForm.showSubmitLoading( jQuery( form ) );
			}
		}
	}

	/**
	 * Enable a form if all Lookup field requests are completed
	 *
	 * @since 2.03.02
	 * @param {String} formId
	 */
	function enableFormAfterLookup( formId ) {
		processesRunning--;

		if ( processesRunning <= 0 ) {

			var form = getFormById( formId );
			if ( form !== null ) {
				frmFrontForm.removeSubmitLoading( jQuery( form ), 'enable', processesRunning );
			}
		}
	}

	/**
	 * Get a form element by the ID number
	 *
	 * @since 2.03.02
	 * @param {string} formId
	 * @returns {Element}
     */
	function getFormById( formId ) {
		var form = document.querySelector( '#frm_form_' + formId + '_container form' );
		if ( form === null ) {
			// The order is switched on the back end.
			form = document.getElementById( 'frm_form_' + formId + '_container' );
			if ( form !== null ) {
				form = form.closest( 'form' );
			}
		}
		return form;
	}

	/**
	 * Enable a Select Lookup field and remove loading image
	 *
	 * @since 2.02.11
	 * @param {object} childSelect
	 * @pparam {boolean} isReadOnly
	 */
	function enableLookup( childSelect, isReadOnly ) {
		if ( isReadOnly === false ) {
			childSelect.disabled = false;
		}
		childSelect.className = childSelect.className.replace( ' frm_loading_lookup', '' );
	}

	/**
	 * Replace the options in a Select Lookup field
	 *
	 * @since 2.01.0
	 * @param {Object} fieldArgs
	 * @param {string} fieldArgs.fieldKey
	 * @param {boolean} fieldArgs.isReadOnly
	 * @param {object} childSelect
	 * @param {Array} newOptions
	 */
	function replaceSelectLookupFieldOptions( fieldArgs, childSelect, newOptions ) {
		var origVal = childSelect.value;

		// Remove old options
		for ( var i = childSelect.options.length; i > 0; i-- ) {
			childSelect.remove( i );
		}

		// Add new options
		var optsLength = newOptions.length;
		for ( i = 0; i < optsLength; i++ ) {
			childSelect.options[ i + 1 ] = new Option( newOptions[ i ], newOptions[ i ], false, false );
		}

		setSelectLookupVal( childSelect, origVal );

		enableLookup( childSelect, fieldArgs.isReadOnly );

		maybeUpdateChosenOptions( childSelect );

		// Trigger a change if the new value is different from the old value
		if ( childSelect.value != origVal ) {
			triggerChange( jQuery( childSelect ), fieldArgs.fieldKey );
		}
	}

	// Set the value in a refreshed Lookup Field
	function setSelectLookupVal( childSelect, origVal ) {
		// Try setting the dropdown to the original value
		childSelect.value = origVal;
		if ( childSelect.value === '' ) {
			// If the original value is no longer present, try setting to default value
			var defaultValue = childSelect.getAttribute( 'data-frmval' );
			if ( defaultValue !== null ) {
				childSelect.value = defaultValue;
			}
		}
	}

	/**
	 * Either hide checkbox/radio Lookup field or update its options
	 *
	 * @since 2.01.01
	 * @param {Object} childFieldArgs
	 * @param {Array} childFieldArgs.parentVals
	 * @param {object} childDiv
     */
	function maybeReplaceCbRadioLookupOptions( childFieldArgs, childDiv ) {
		if ( childFieldArgs.parentVals === false  ) {
			// If any parents have blank values, don't waste time looking for values

			var inputs = childDiv.getElementsByTagName( 'input' );
			maybeHideRadioLookup( childFieldArgs, childDiv );
			clearValueForInputs( inputs );

		} else {
			replaceCbRadioLookupOptions( childFieldArgs, childDiv );
		}
	}

	/**
	 * Update the options in a checkbox/radio lookup field
	 *
	 * @since 2.01.01
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.inputType
	 * @param {Array} childFieldArgs.parents
	 * @param {Array} childFieldArgs.parentVals
	 * @param {string} childFieldArgs.fieldId
	 * @param {string} childFieldArgs.repeatRow
	 * @param {string} childFieldArgs.fieldKey
	 * @param {string} childFieldArgs.formId
	 * @param {object} childDiv
     */
	function replaceCbRadioLookupOptions( childFieldArgs, childDiv ) {
		var optContainer, inputs, currentValue, defaultValue, form, data, success;

		optContainer = childDiv.getElementsByClassName( 'frm_opt_container' )[ 0 ];
		inputs = optContainer.getElementsByTagName( 'input' );
		currentValue = '';

		addLoadingIconJS( childDiv, optContainer );

		if ( childFieldArgs.inputType == 'radio' ) {
			currentValue = getValueFromRadioInputs( inputs );
		} else {
			currentValue = getValuesFromCheckboxInputs( inputs );
		}

		defaultValue = jQuery( inputs[ 0 ]).data( 'frmval' );
		disableFormPreLookup( childFieldArgs.formId );

		form = getFormById( childFieldArgs.formId );

		data = {
			action: 'frm_replace_cb_radio_lookup_options',
			parent_fields: childFieldArgs.parents,
			parent_vals: childFieldArgs.parentVals,
			field_id: childFieldArgs.fieldId,
			container_field_id: getContainerFieldId( childFieldArgs ),
			row_index: childFieldArgs.repeatRow,
			current_value: currentValue,
			default_value: defaultValue,
			nonce: frm_js.nonce
		};

		success = function( newHtml ) {
			var input;
			optContainer.innerHTML = newHtml;

			removeLoadingIconJS( childDiv, optContainer );

			if ( inputs.length == 1 && inputs[ 0 ].value === '' ) {
				maybeHideRadioLookup( childFieldArgs, childDiv );
			} else {
				maybeShowRadioLookup( childFieldArgs, childDiv );
				maybeSetDefaultCbRadioValue( childFieldArgs, inputs, defaultValue );
			}

			input = inputs[0];
			triggerChange( jQuery( input ), childFieldArgs.fieldKey );
			triggerLookupOptionsLoaded( jQuery( childDiv ) );

			enableFormAfterLookup( childFieldArgs.formId );
		};

		postToAjaxUrl( form, data, success );
	}

	/**
	 * @since 4.0.03
	 */
	function maybeReplaceLookupList( childFieldArgs, childDiv ) {
		var inputs = childDiv.getElementsByTagName( 'input' ),
			content = inputs[0].previousElementSibling;

		if ( childFieldArgs.parentVals === false  ) {
			// If any parents have blank values, don't waste time looking for values
			maybeHideRadioLookup( childFieldArgs, childDiv );
			if ( typeof content !== 'undefined' ) {
				content.innerHTML = '';
			}
		} else {
			getLookupValues( childFieldArgs, function( response ) {
				content.innerHTML = response.join( ', ' );
				inputs[0].value = response;

				maybeShowRadioLookup( childFieldArgs, childDiv );
				triggerLookupOptionsLoaded( jQuery( childDiv ) );
			});
		}
	}

	/**
	 * Get the lookup options in an array.
	 * @since 4.0.03
	 */
	function getLookupValues( childFieldArgs, callback ) {
		disableFormPreLookup( childFieldArgs.formId );


		postToAjaxUrl(
			getFormById( childFieldArgs.formId ),
			{
				action: 'frm_replace_lookup_field_options',
				parent_fields: childFieldArgs.parents,
				parent_vals: childFieldArgs.parentVals,
				field_id: childFieldArgs.fieldId,
				nonce: frm_js.nonce
			},
			function( newOptions ) {
				enableFormAfterLookup( childFieldArgs.formId );
				callback( newOptions );
			},
			false,
			{
				dataType: 'json'
			}
		);
	}

	/**
	 * Trigger the frm_lookup_options_loaded event on the field div
	 *
	 * @since 2.03.05
	 *
	 * @param {Object} $fieldDiv
	 */
	function triggerLookupOptionsLoaded( $fieldDiv ) {
		$fieldDiv.trigger( 'frmLookupOptionsLoaded' );
	}

	/**
	 * Select the defatul value in a radio/checkbox field if no value is selected
	 *
	 * @since 2.02.11
	 *
	 * @param {Object} inputs
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.inputType
	 * @param {(string|Array)} defaultValue
     */
	function maybeSetDefaultCbRadioValue( childFieldArgs, inputs, defaultValue ) {
		if ( defaultValue === undefined ) {
			return;
		}

		var currentValue = false;
		if ( childFieldArgs.inputType === 'radio' ) {
			currentValue = getValueFromRadioInputs( inputs );
		} else {
			currentValue = getValuesFromCheckboxInputs( inputs );
		}

		if ( currentValue !== false || inputs.length < 1 ) {
			return;
		}

		var inputName = inputs[ 0 ].name;
		setCheckboxOrRadioDefaultValue( inputName, defaultValue );
	}

	/**
	 * Hide a Radio Lookup field if it doesn't have any options
	 *
	 * @since 2.01.01
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.formId
	 * @param {object} childDiv
	 */
	function maybeHideRadioLookup( childFieldArgs, childDiv ) {
		if ( isFieldConditionallyHidden( childDiv.id, childFieldArgs.formId ) ) {
			return;
		}

		hideFieldContainer( childDiv.id );
		addToHideFields( childDiv.id, childFieldArgs.formId );
	}

	/**
	 * Show a radio Lookup field if it has options and conditional logic says it should be shown
	 *
	 * @since 2.01.01
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.formId
	 * @param {string} childFieldArgs.fieldId
	 * @param {string} childFieldArgs.repeatRow
	 * @param {object} childDiv
	 */
	function maybeShowRadioLookup( childFieldArgs, childDiv ) {
		if ( isFieldCurrentlyShown( childDiv.id, childFieldArgs.formId ) ) {
			return;
		}

		var logicArgs = getRulesForSingleField( childFieldArgs.fieldId );
		if ( logicArgs === false || logicArgs.conditions.length < 1 ) {
			removeFromHideFields( childDiv.id, childFieldArgs.formId );
			showFieldContainer( childDiv.id );
		} else {
			logicArgs.containerId = childDiv.id;
			logicArgs.repeatRow = childFieldArgs.repeatRow;
			hideOrShowSingleField( logicArgs );
		}
	}

	/**
	 * Get new value for a text field if all Lookup Field parents have a value
	 *
	 * @since 2.01.0
	 * @param {Object} childFieldArgs
	 * @param {string} childFieldArgs.formId
	 * @param {Array} childFieldArgs.parents
	 * @param {Array} childFieldArgs.parentVals
	 * @param {string} childFieldArgs.fieldKey
	 * @param {string} childFieldArgs.fieldId
	 * @param {object} childInput
     */
	function maybeInsertValueInFieldWatchingLookup( childFieldArgs, childInput ) {
		if ( isChildInputConditionallyHidden( childInput, childFieldArgs.formId ) ) {
			// TODO: What if field is in conditionally hidden section?
			checkQueueAfterLookupCompleted( childInput.id );
			return;
		}

		if ( childFieldArgs.parentVals === false  ) {
			// If any parents have blank values, set the field value to the default value
			var newValue = childInput.getAttribute( 'data-frmval' );
			if ( newValue === null ) {
				newValue = '';
			}
			insertValueInFieldWatchingLookup( childFieldArgs, childInput, newValue );
			checkQueueAfterLookupCompleted( childInput.id );
		} else {
			// If all parents have values, check for a new value

			disableFormPreLookup( childFieldArgs.formId );

			postToAjaxUrl(
				getFormById( childFieldArgs.formId ),
				{
					action: 'frm_get_lookup_text_value',
					parent_fields: childFieldArgs.parents,
					parent_vals: childFieldArgs.parentVals,
					field_id: childFieldArgs.fieldId,
					nonce: frm_js.nonce
				},
				function( newValue ) {
					if ( ! isChildInputConditionallyHidden( childInput, childFieldArgs.formId ) && childInput.value != newValue ) {
						insertValueInFieldWatchingLookup( childFieldArgs.fieldKey, childInput, newValue );
					}

					enableFormAfterLookup( childFieldArgs.formId );
					checkQueueAfterLookupCompleted( childInput.id );
				}
			);
		}
	}

	/**
	 * Check if the current Lookup watcher field has a queue
	 *
	 * @since 2.03.05
	 *
	 * @param {string} elementId
	 * @returns {boolean}
     */
	function currentLookupHasQueue( elementId ) {
		return ( elementId in lookupQueues && lookupQueues[ elementId ].length > 0 );
	}

	/**
	 * Add the current Lookup watcher to a queue of size two
	 *
	 * @since 2.03.05
	 *
	 * @param {Object} childFieldArgs
	 * @param {Object} childInput
     */
	function addLookupToQueueOfTwo( childFieldArgs, childInput ) {
		var elementId = childInput.id;

		if ( elementId in lookupQueues ) {
			if ( lookupQueues[ elementId ].length >= 2 ) {
				lookupQueues[ elementId ] = lookupQueues[ elementId ].slice( 0, 1 );
			}
		} else {
			lookupQueues[ elementId ] = [];
		}

		lookupQueues[ elementId ].push({ childFieldArgs: childFieldArgs, childInput: childInput });
	}

	/**
	 * Check the lookupQueue after a value lookup is completed
	 *
	 * @since 2.03.05
	 *
	 * @param {string} elementId
     */
	function checkQueueAfterLookupCompleted( elementId ) {
		removeLookupFromQueue( elementId );
		doNextItemInLookupQueue( elementId );
	}

	/**
	 * Remove a Lookup from the queue
	 *
	 * @since 2.03.05
	 *
	 * @param {string} elementId
	 */
	function removeLookupFromQueue( elementId ) {
		lookupQueues[ elementId ].shift();
	}

	/**
	 * Check the current Lookup queue
	 *
	 * @since 2.03.05
	 *
	 * @param {string} elementId
	 */
	function doNextItemInLookupQueue( elementId ) {
		if ( currentLookupHasQueue( elementId ) ) {
			var childFieldArgs = lookupQueues[ elementId ][ 0 ].childFieldArgs,
				childInput = lookupQueues[ elementId ][ 0 ].childInput;

			maybeInsertValueInFieldWatchingLookup( childFieldArgs, childInput );
		}
	}

	/**
	 * Convert html entities back into their character values
	 *
	 * @param {string} string
	 * @returns {string}
	 */
	function decodeEntities( string ) {
		var decoded = string.replace( /&amp;/g, '&' )
			.replace( /&lt;/g, '<' )
			.replace( /&gt;/g, '>' )
			.replace( /&quot;/g, '"' )
			.replace( /&#039;/g, '\'' );
		return decoded;
	}

	/**
	 * Insert a new text field Lookup value
	 *
	 * @since 2.01.0
	 * @param {string} fieldKey
	 * @param {object} childInput
	 * @param {string} newValue
 	 */
	function insertValueInFieldWatchingLookup( fieldKey, childInput, newValue ) {
		childInput.value = decodeEntities( newValue );
		triggerChange( jQuery( childInput ), fieldKey );
	}

	/**
	 * Add the repeat Row to the child field args
	 *
	 * @since 2.01.0
	 * @param {string} fieldName
	 * @param {Object} childFieldArgs
     */
	function addRepeatRowForInput( fieldName, childFieldArgs ) {
		var repeatArgs = getRepeatArgsFromFieldName( fieldName );

		if ( repeatArgs.repeatRow !== '' ) {
			childFieldArgs.repeatRow = repeatArgs.repeatRow;
		} else {
			childFieldArgs.repeatRow = '';
		}
	}

	/*******************************************************
	 Dynamic Field Functions
	 *******************************************************/

	// Update a Dynamic field's data or options
	function updateDynamicField( depFieldArgs, onCurrentPage ) {
		var depFieldArgsCopy = cloneObjectForDynamicFields( depFieldArgs );

		if ( depFieldArgsCopy.inputType === 'data' ) {
			updateDynamicListData( depFieldArgsCopy, onCurrentPage );
		} else if ( onCurrentPage ) {
			// Only update the options if field is on the current page
			updateDynamicFieldOptions( depFieldArgsCopy );
		}
	}

	/**
	 * Clone the depFieldArgs object for use in ajax requests
	 *
	 * @since 2.01.0
	 * @param {Object} depFieldArgs
	 * @param {string|Array} depFieldArgs.dataLogic.actualValue
	 * @param {string} depFieldArgs.fieldId
	 * @param {string} depFieldArgs.fieldKey
	 * @param {string} depFieldArgs.formId
	 * @param {string} depFieldArgs.containerId
	 * @param {string} depFieldArgs.repeatRow
	 * @param {string} depFieldArgs.inputType
	 * @return {Object} dynamicFieldArgs
	 */
	function cloneObjectForDynamicFields( depFieldArgs ) {
		var dataLogic = {
			actualValue: depFieldArgs.dataLogic.actualValue,
			fieldId: depFieldArgs.dataLogic.fieldId
		};

		var dynamicFieldArgs = {
			fieldId: depFieldArgs.fieldId,
			fieldKey: depFieldArgs.fieldKey,
			formId: depFieldArgs.formId,
			containerId: depFieldArgs.containerId,
			repeatRow: depFieldArgs.repeatRow,
			dataLogic: dataLogic,
			children: '',
			inputType: depFieldArgs.inputType
		};

		return dynamicFieldArgs;
	}

	pendingDynamicFieldAjax = [];

	/**
	 * Update a Dynamic List field
	 *
	 * @since 2.01
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.containerId
	 * @param {string|Array} depFieldArgs.dataLogic.actualValue
	 * @param {string} depFieldArgs.fieldId
 	 */
	function updateDynamicListData( depFieldArgs, onCurrentPage ) {
		var $fieldDiv;

		if ( onCurrentPage ) {
			$fieldDiv = jQuery( '#' + depFieldArgs.containerId );
			addLoadingIcon( $fieldDiv );
		}

		pendingDynamicFieldAjax.push({
			args: {
				depFieldArgs: depFieldArgs,
				onCurrentPage: onCurrentPage
			},
			data: {
				entry_id: depFieldArgs.dataLogic.actualValue,
				current_field: depFieldArgs.fieldId,
				hide_id: depFieldArgs.containerId,
				on_current_page: onCurrentPage,
				nonce: frm_js.nonce
			}
		});
	}

	/**
	 * Update a Dynamic dropdown, radio, or checkbox options
	 *
	 * @since 2.01
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.containerId
	 * @param {string} depFieldArgs.dataLogic.fieldId
	 * @param {string|Array} depFieldArgs.dataLogic.actualValue
	 * @param {string} depFieldArgs.fieldId
	 */
	function updateDynamicFieldOptions( depFieldArgs ) {
		var $fieldDiv = jQuery( '#' + depFieldArgs.containerId ),
			$fieldInputs = $fieldDiv.find( 'select[name^="item_meta"], input[name^="item_meta"]' ),
			prevValue = getFieldValueFromInputs( $fieldInputs ),
			defaultVal = $fieldInputs.data( 'frmval' ),
			editingEntry = $fieldDiv.closest( 'form' ).find( 'input[name="id"]' ).val();

		addLoadingIcon( $fieldDiv );

		postToAjaxUrl(
			getFormById( depFieldArgs.formId ),
			{
				action: 'frm_fields_ajax_data_options',
				trigger_field_id: depFieldArgs.dataLogic.fieldId,
				entry_id: depFieldArgs.dataLogic.actualValue,
				field_id: depFieldArgs.fieldId,
				default_value: defaultVal,
				container_id: depFieldArgs.containerId,
				editing_entry: editingEntry,
				prev_val: prevValue,
				nonce: frm_js.nonce
			},
			function( html ) {
				var $optContainer = $fieldDiv.find( '.frm_opt_container, .frm_data_container' );
				$optContainer.html( html );
				var $dynamicFieldInputs = $optContainer.find( 'select, input[type="checkbox"], input[type="radio"]' );

				removeLoadingIcon( $optContainer );

				if ( html === '' || $dynamicFieldInputs.length < 1 ) {
					hideDynamicField( depFieldArgs );
				} else {
					var valueChanged = dynamicFieldValueChanged( depFieldArgs, $dynamicFieldInputs, prevValue );
					showDynamicField( depFieldArgs, $fieldDiv, $dynamicFieldInputs, valueChanged );
				}
			}
		);
	}

	function dynamicFieldValueChanged( depFieldArgs, $dynamicFieldInputs, prevValue ) {
		var newValue = getFieldValueFromInputs( $dynamicFieldInputs );
		return ( prevValue !== newValue );
	}

	/**
	 * Update the value in a hidden Dynamic List field
	 *
	 * @since 2.01.01
	 * @param {Object} depFieldArgs
	 * @param {string} depFieldArgs.fieldKey
	 * @param {string} depFieldArgs.repeatRow
	 * @param {string} depFieldArgs.containerId
	 * @param {string} depFieldArgs.formId
     */
	function updateHiddenDynamicListField( depFieldArgs, newValue ) {
		// Get the Dynamic List input
		var inputId = 'field_' + depFieldArgs.fieldKey;
		if ( depFieldArgs.repeatRow !== '' ) {
			inputId += '-' + depFieldArgs.repeatRow;
		}
		var listInput = document.getElementById( inputId );

		if ( listInput === null ) {
			return;
		}

		// Set the new value
		listInput.value = newValue;

		// Remove field from hidden field list
		if ( isFieldConditionallyHidden( depFieldArgs.containerId, depFieldArgs.formId ) ) {
			removeFromHideFields( depFieldArgs.containerId, depFieldArgs.formId );
		}

		triggerChange( jQuery( listInput ) );
	}

	// Add the loading icon with jQuery
	function addLoadingIcon( $fieldDiv ) {
		var currentHTML = $fieldDiv.html();

		if ( currentHTML.indexOf( 'frm-loading-img' ) > -1 ) {
			// Loading image already present
		} else {
			var loadingIcon = '<span class="frm-loading-img"></span>';
			$fieldDiv.html( currentHTML + loadingIcon );

			var $optContainer = $fieldDiv.find( '.frm_opt_container, .frm_data_container' );
			$optContainer.hide();
		}
	}

	// Add the loading icon with JavaScript
	function addLoadingIconJS( fieldDiv, optContainer ) {
		var currentHTML = fieldDiv.innerHTML;

		if ( currentHTML.indexOf( 'frm-loading-img' ) > -1 ) {
			// Loading image already present
		} else {
			optContainer.classList.add( 'frm_hidden' );

			var loadingIcon = document.createElement( 'span' );
			loadingIcon.setAttribute( 'class', 'frm-loading-img' );
			fieldDiv.insertBefore( loadingIcon, optContainer.nextSibling );
		}
	}

	// Remove the loading icon with jQuery
	function removeLoadingIcon( $optContainer ) {
		$optContainer.parent().children( '.frm-loading-img' ).remove();
		$optContainer.show();
	}

	// Remove the loading icon with JavaScript
	function removeLoadingIconJS( fieldDiv, optContainer ) {
		var loadingIcon = fieldDiv.getElementsByClassName( 'frm-loading-img' )[ 0 ];
		if ( loadingIcon !== null && loadingIcon !== undefined ) {
			loadingIcon.parentNode.removeChild( loadingIcon );
		}

		optContainer.classList.remove( 'frm_hidden' );
	}

	// Get the field value from all the inputs
	function getFieldValueFromInputs( $inputs ) {
		var fieldValue = [],
			currentValue = '';

		$inputs.each(
			function() {
				currentValue = this.value;
				if ( this.type === 'radio' || this.type === 'checkbox' ) {
					if ( this.checked === true ) {
						fieldValue.push( currentValue );
					}
				} else if ( currentValue !== '' ) {
					fieldValue.push( currentValue );
				}
			}
		);

		if ( fieldValue.length === 0 ) {
			fieldValue = '';
		}

		return fieldValue;
	}

	// Hide and clear a Dynamic Field
	function hideDynamicField( depFieldArgs ) {
		hideFieldAndClearValue( depFieldArgs, true );
	}

	// Show Dynamic field
	function showDynamicField( depFieldArgs, $fieldDiv, $fieldInputs, valueChanged ) {
		if ( isFieldConditionallyHidden( depFieldArgs.containerId, depFieldArgs.formId ) ) {
			removeFromHideFields( depFieldArgs.containerId, depFieldArgs.formId );
			$fieldDiv.show();
		}

		if ( $fieldInputs.hasClass( 'frm_chzn' ) ) {
			loadChosen();
		}

		if ( valueChanged === true ) {
			triggerChange( $fieldInputs );
		}
	}

	/*************************************************
	 Calculations
	 ************************************************/

	function triggerCalc() {
		if ( typeof __FRMCALC === 'undefined' ) {
			// there are no calculations on this page
			return;
		}

		var triggers = __FRMCALC.triggers;
		if ( triggers ) {
			jQuery( triggers.join() ).trigger({ type: 'change', selfTriggered: true });
		}

		triggerCalcWithoutFields();
	}

	function triggerCalcWithoutFields() {
		var calcs = __FRMCALC.calc,
			vals = [];

		for ( var fieldKey in calcs ) {
			if ( calcs[ fieldKey ].fields.length < 1 ) {
				var totalField = document.getElementById( 'field_' + fieldKey );
				if ( totalField !== null && ! isChildInputConditionallyHidden( totalField, calcs[ fieldKey ].form_id ) ) {
					// if field is not hidden, do calculation
					doSingleCalculation( __FRMCALC, fieldKey, vals );
				}
			}
		}
	}

	function doCalculation( fieldId, triggerField ) {
		if ( typeof __FRMCALC === 'undefined' ) {
			// there are no calculations on this page
			return;
		}

		var allCalcs = __FRMCALC,
			calc = allCalcs.fields[ fieldId ],
			vals = [];

		if ( typeof calc === 'undefined' ) {
			// this field is not used in a calculation
			return;
		}

		var keys = calc.total;
		var len = keys.length;
		var pages = getStartEndPage( allCalcs.calc[ keys[ 0 ] ]);

		// loop through each calculation this field is used in
		for ( var i = 0, l = len; i < l; i++ ) {
			var totalOnPage = isTotalFieldOnPage( allCalcs.calc[ keys[ i ] ], pages );

			// Proceed with calculation if total field is not conditionally hidden
			if ( totalOnPage && isTotalFieldConditionallyHidden( allCalcs.calc[ keys[ i ] ], triggerField.attr( 'name' ) ) === false ) {
				doSingleCalculation( allCalcs, keys[ i ], vals, triggerField );
			}
		}
	}

	/**
	 * @param thisField
	 * @since 2.05.06
	 */
	function getStartEndPage( thisField ) {
		var formId = thisField.form_id,
			formContainer = document.getElementById( 'frm_form_' + formId + '_container' );

		if ( formContainer === null && thisField.in_section ) {
			var fieldContainer = document.getElementById( 'frm_field_' + thisField.in_section + '_container' );

			if ( fieldContainer === null ) {
				// bail now and run the calculation if we can't determine if it's on the page
				return [];
			}

			formContainer = closest( fieldContainer, function( el ) {
				return el.tagName === 'FORM';
			});

			formId = formContainer.elements.namedItem( 'form_id' ).value;
		}

		var hasPreviousPage = formContainer.getElementsByClassName( 'frm_next_page' );
		var hasAnotherPage = document.getElementById( 'frm_page_order_' + formId );

		var pages = [];
		if ( hasPreviousPage.length > 0 ) {
			pages.start = hasPreviousPage[ 0 ];
		}
		if ( hasAnotherPage !== null ) {
			pages.end = hasAnotherPage;
		}

		return pages;
	}

	function closest( el, fn ) {
		return el && ( fn( el ) ? el : closest( el.parentNode, fn ) );
	}

	/**
	 * If the total field is not on the current page, don't trigger the calculation
	 *
	 * @param calcDetails
	 * @param pages
	 * @since 2.05.06
	 */
	function isTotalFieldOnPage( calcDetails, pages ) {
		if ( typeof pages.start !== 'undefined' || typeof pages.end !== 'undefined' ) {
			// the form has pages
			var hiddenTotalField = jQuery( 'input[type=hidden][name*="[' + calcDetails.field_id + ']"]' );
			if ( hiddenTotalField.length ) {
				// the total field is a hidden field
				return isHiddenTotalOnPage( hiddenTotalField, pages );
			}
		}

		return true;
	}

	function isHiddenTotalOnPage( hiddenTotalField, pages ) {
		var onPage,
			hiddenParent = hiddenTotalField.closest( '.frm_form_field' );

		if ( hiddenParent.length ) {
			// the field is in a section
			return true;
		}

		var totalPos = hiddenTotalField.index();
		var isAfterStart = true;
		var isBeforeEnd = true;
		if ( typeof pages.start !== 'undefined' ) {
			isAfterStart = jQuery( pages.start ).index() < totalPos;
		}
		if ( typeof pages.end !== 'undefined' ) {
			isBeforeEnd = jQuery( pages.end ).index() > totalPos;
		}

		onPage = ( isAfterStart && isBeforeEnd );
		if ( ! onPage ) {
			onPage = hiddenTotalField.closest( '.do-calculation' ).length > 0;
		}

		return onPage;
	}

	/**
	 * Check if a total field is conditionally hidden
	 * @param {Object} calcDetails
	 * @param {string} calcDetails.field_id
	 * @param {string} calcDetails.form_id
	 * @param {string} calcDetails.inSection
	 * @param {string} calcDetails.inEmbedForm
	 * @param {string} triggerFieldName
	 * @returns {boolean}
     */
	function isTotalFieldConditionallyHidden( calcDetails, triggerFieldName ) {
		var hidden = false,
			fieldId = calcDetails.field_id,
			formId = calcDetails.form_id,
			hiddenFields = getHiddenFields( formId );

		// Check if there are any conditionally hidden fields
		if ( hiddenFields.length < 1 ) {
			return hidden;
		}

		if ( calcDetails.inSection === '0' && calcDetails.inEmbedForm === '0' ) {
			// Field is not in a section or embedded form
			hidden = isNonRepeatingFieldConditionallyHidden( fieldId, hiddenFields );
		} else {
			// Field is in a section or embedded form
			var repeatArgs = getRepeatArgsFromFieldName( triggerFieldName );

			if ( isNonRepeatingFieldConditionallyHidden( fieldId, hiddenFields ) ) {
				// Check standard field
				hidden = true;
			} else if ( isRepeatingFieldConditionallyHidden( fieldId, repeatArgs, hiddenFields ) ) {
				// Check repeating field
				hidden = true;
			} else if ( calcDetails.inSection !== '0' && calcDetails.inEmbedForm !== '0' ) {
				// Check section in embedded form
				hidden = isRepeatingFieldConditionallyHidden( calcDetails.inSection, repeatArgs, hiddenFields );
			} else if ( calcDetails.inSection !== '0' ) {
				// Check section
				hidden = isNonRepeatingFieldConditionallyHidden( calcDetails.inSection, hiddenFields );
			} else if ( calcDetails.inEmbedForm !== '0' ) {
				// Check embedded form
				hidden = isNonRepeatingFieldConditionallyHidden( calcDetails.inEmbedForm, hiddenFields );
			}
		}

		return hidden;
	}

	// Check if a non-repeating field is conditionally hidden
	function isNonRepeatingFieldConditionallyHidden( fieldId, hiddenFields ) {
		var htmlID = 'frm_field_' + fieldId + '_container';
		return ( hiddenFields.indexOf( htmlID ) > -1 );
	}

	// Check if a repeating field is conditionally hidden
	function isRepeatingFieldConditionallyHidden( fieldId, repeatArgs, hiddenFields ) {
		var hidden = false;

		if ( repeatArgs.repeatingSection ) {
			var fieldRepeatId = 'frm_field_' + fieldId + '-' + repeatArgs.repeatingSection;
			fieldRepeatId += '-' + repeatArgs.repeatRow + '_container';
			hidden = ( hiddenFields.indexOf( fieldRepeatId ) > -1 );
		}

		return hidden;
	}

	function maybeShowCalculationsErrorAlert( err, fieldKey, thisFullCalc ) {
		var alertMessage = '';

		if ( ( ! jQuery( 'form' ).hasClass( 'frm-admin-viewing' ) ) ) {
			return;
		}

		alertMessage += frm_js.calc_error + ' ' + fieldKey + ':\n\n';
		alertMessage += thisFullCalc + '\n\n';

		if ( err.message ) {
			alertMessage += err.message + '\n\n';
		}
		alert( alertMessage );
	}

	/**
	 * Adjust a Date for timezone offset
	 *
	 * @param {Date} date
	 * @returns {Date}
	 */
	function treatAsUTC( date ) {
		var copy = new Date( date.valueOf() );
		copy.setMinutes( copy.getMinutes() - copy.getTimezoneOffset() );
		return copy;
	}

	/**
	 * Try to change date strings and ints to Date objects before calculations
	 *
	 * @param {mixed} date
	 * @returns {mixed}
	 */
	function normalizeDate( date ) {
		switch ( typeof date ) {
			case 'number': return new Date( date * 86400000 ); // 1000 * 60 * 60 * 24 (milliseconds per day)
			case 'string': return new Date( date );
			default:       return date;
		}
	}

	/**
	 * Calculate the difference between two dates
	 *
	 * @param {mixed} a date
	 * @param {mixed} b date
	 * @param {string} format
	 * @returns {int}
	 */
	function calculateDateDifference( a, b, format ) {
		a = normalizeDate( a );
		b = normalizeDate( b );

		switch ( format ) {
			case 'days': {
				return Math.floor( ( treatAsUTC( b ) - treatAsUTC( a ) ) / 86400000 ); // 1000 * 60 * 60 * 24 (milliseconds per day)
			}

			case 'years': default: {
				var years = b.getFullYear() - a.getFullYear();
				if ( b.getMonth() < a.getMonth() || b.getMonth() === a.getMonth() && b.getDate() < a.getDate() ) {
					years--;
				}

				return years;
			}
		}
	}

	function doSingleCalculation( allCalcs, fieldKey, vals, triggerField ) {
		var currency, total, dec, updatedTotal,
			thisCalc = allCalcs.calc[ fieldKey ],
			thisFullCalc = thisCalc.calc,
			totalField = jQuery( document.getElementById( 'field_' + fieldKey ) ),
			fieldInfo = {
				triggerField: triggerField,
				inSection: false,
				thisFieldCall: 'input[id^="field_' + fieldKey + '-"]'
			};

		// exit early for sliders as there is no calculation for such types and avoid errors that might rise due to formatting the value
		if ( totalField.attr( 'type' ) === 'range' ) {
			return;
		}

		// TODO: update this to work more like conditional logic
		if ( totalField.length < 1 && typeof triggerField !== 'undefined' ) {
			// check if the total field is inside of a repeating/embedded form
			fieldInfo.inSection = true;
			fieldInfo.thisFieldId = objectSearch( allCalcs.fieldsWithCalc, fieldKey );
			totalField = getSiblingField( fieldInfo );
		}

		if ( totalField === null || totalField.length < 1 ) {
			return;
		}

		// loop through the fields in this calculation
		thisFullCalc = getValsForSingleCalc( thisCalc, thisFullCalc, allCalcs, vals, fieldInfo );

		total = '';
		dec = '';

		if ( 'function' === typeof window[ 'frmProGetCalcTotal' + thisCalc.calc_type ]) {
			total = window[ 'frmProGetCalcTotal' + thisCalc.calc_type ].call( thisCalc, thisFullCalc );
		} else if ( thisCalc.calc_type === 'text' ) {
			total = thisFullCalc;
		} else {
			// Set the number of decimal places
			dec = thisCalc.calc_dec;

			// allow .toFixed for reverse compatibility
			if ( thisFullCalc.indexOf( ').toFixed(' ) > -1 ) {
				var calcParts = thisFullCalc.split( ').toFixed(' );
				if ( isNumeric( calcParts[ 1 ]) ) {
					dec = calcParts[ 1 ];
					thisFullCalc = thisFullCalc.replace( ').toFixed(' + dec, '' );
				}
			}

			thisFullCalc = trimNumericCalculation( thisFullCalc );

			if ( thisFullCalc !== '' ) {
				try {
					total = parseFloat( eval( thisFullCalc ) );
				} catch ( err ) {
					maybeShowCalculationsErrorAlert( err, fieldKey, thisFullCalc );
				}
			}

			if ( typeof total === 'undefined' || isNaN( total ) ) {
				total = 0;
			}

			// Set decimal points
			if ( isNumeric( dec ) && total !== '' ) {
				total = total.toFixed( dec );
			}
		}

		if ( thisCalc.is_currency === true && isNumeric( total ) ) {
			currency = getCurrencyFromCalcRule( thisCalc );
			if ( currency.decimals > 0 ) {
				total = Math.round10( total, currency.decimals );
				total = maybeAddTrailingZeroToPrice( total, currency );
				dec = currency.decimals;
			}
		}

		if ( totalField.val() === total ) {
			setDisplayedTotal( totalField, total, currency );
			return;
		}

		updatedTotal = false;
		if ( ( isNumeric( dec ) || thisCalc.is_currency ) && [ 'number', 'text' ].indexOf( totalField.attr( 'type' ) ) > -1 ) {
			if ( total.toString().slice( -1 ) == '0' && navigator.userAgent.toLowerCase().indexOf( 'firefox' ) > -1 ) {
				// Change the input to text in Firefox. Otherwise, trailing decimals will fail.
				totalField[0].setAttribute( 'type', 'text' );
			}

			if ( totalField.parent().is( '.frm_input_group.frm_with_box.frm_hidden' ) && 'string' === typeof total ) {
				updatedTotal = true;
				totalField.val( total.replace( ',', '.' ) );
			}
		}

		if ( ! updatedTotal ) {
			totalField.val( total );
		}

		triggerEvent( document, 'frmCalcUpdatedTotal', {
			totalField: totalField,
			total: total
		});

		if ( triggerField === null || typeof triggerField === 'undefined' || totalField.attr( 'name' ) != triggerField.attr( 'name' ) ) {
			triggerChange( totalField, fieldKey );
		}

		setDisplayedTotal( totalField, total, currency );
	}

	/**
	 * Show the total for frm_total fields.
	 */
	function setDisplayedTotal( totalField, total, currency ) {
		var prepend, append,
			showTotal = totalField.parent().prev();

		if ( ! showTotal.hasClass( 'frm_total_formatted' ) ) {
			return;
		}

		prepend = showTotal.data( 'prepend' );
		append = showTotal.data( 'append' );
		if ( typeof prepend === 'undefined' ) {
			prepend = '';
		}
		if ( typeof append === 'undefined' ) {
			append = '';
		}

		if ( typeof currency === 'object' ) {
			total = formatCurrency( total, currency );
			if ( currency.symbol_left === prepend ) {
				prepend = '';
			}
			if ( currency.symbol_right === append ) {
				append = '';
			}
		}

		if ( prepend !== '' ) {
			prepend = '<span class="frm_inline_pre">' + prepend + '</span>';
		}

		if ( append !== '' ) {
			append = '<span class="frm_inline_pre">' + append + '</span>';
		}

		showTotal.html( prepend + '<span class="frm_inline_total">' + total + '</span>' + append );
	}

	function getValsForSingleCalc( thisCalc, thisFullCalc, allCalcs, vals, fieldInfo ) {
		var fCount, f, field, date, findVar;

		fCount = thisCalc.fields.length;

		for ( f = 0; f < fCount; f++ ) {
			field = {
				triggerField: fieldInfo.triggerField,
				thisFieldId: thisCalc.fields[ f ],
				inSection: fieldInfo.inSection,
				valKey: fieldInfo.inSection + '' + thisCalc.fields[ f ],
				thisField: allCalcs.fields[ thisCalc.fields[ f ] ],
				thisFieldCall: 'input' + allCalcs.fieldKeys[ thisCalc.fields[ f ] ],
				formID: thisCalc.form_id
			};

			field = getCallForField( field, allCalcs );

			if ( ! thisCalc.calc_type ) {
				// Math calc type.
				field.valKey = 'num' + field.valKey;
				vals = getCalcFieldId( field, allCalcs, vals );

				if ( typeof vals[ field.valKey ] === 'undefined' || isNaN( vals[ field.valKey ]) ) {
					vals[ field.valKey ] = 0;

					if ( field.thisField.type === 'date' ) {
						date = tryToGetDateValue( field );
						if ( null !== date ) {
							vals[ field.valKey ] = Math.floor( date.getTime() / 86400000 );
						} else {
							thisFullCalc = '';
						}
					}
				} else if ( 0 === vals[ field.valKey ] && field.thisField.type === 'date' && dateValueShouldBeClearedForDateCalculation( field, fieldInfo ) ) {
					thisFullCalc = '';
				}
			} else {
				// Text calc type.
				field.valKey = 'text' + field.valKey;
				vals = getTextCalcFieldId( field, vals );
				if ( typeof vals[ field.valKey ] === 'undefined' ) {
					vals[ field.valKey ] = '';
				}
			}

			findVar = '[' + field.thisFieldId + ' show=label]';
			if ( thisCalc.calc_type === 'text' && -1 !== thisFullCalc.indexOf( findVar ) ) {
				vals[ field.valKey + 'label' ] = getOptionLabelsFromValues( vals[ field.valKey ], field );
				findVar = findVar.replace( /([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1' );
				thisFullCalc = thisFullCalc.replace( new RegExp( findVar, 'g' ), vals[ field.valKey + 'label' ]);
			}

			findVar = '[' + field.thisFieldId + ']';
			findVar = findVar.replace( /([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1' );
			thisFullCalc = thisFullCalc.replace( new RegExp( findVar, 'g' ), vals[ field.valKey ]);
		}

		return thisFullCalc;
	}

	function tryToGetDateValue( field ) {
		var $element = jQuery( field.thisField.key );
		return $element.hasClass( 'hasDatepicker' ) ? $element.datepicker( 'getDate' ) : null;
	}

	/**
	 * Check if a date value is ready for use in a calculation.
	 */
	function dateValueShouldBeClearedForDateCalculation( field, fieldInfo ) {
		if ( fieldInfo.triggerField !== null ) {
			if ( fieldInfo.triggerField.is( 'input' ) ) {
				if ( datepickerFieldShouldBeClearedForDateCalculation( fieldInfo.triggerField ) ) {
					return fieldShouldBeClearedForDateCalculation( field.thisFieldCall, field.thisField.key );
				}
				return '' === fieldInfo.triggerField.val();
			}
			return fieldInfo.triggerField.is( 'div' ) && fieldInfo.triggerField.end().is( 'input' ) && '' === fieldInfo.triggerField.end().val();
		} else if ( fieldShouldBeClearedForDateCalculation( field.thisFieldCall, field.thisField.key ) ) {
			return true;
		}
		return false;
	}

	function datepickerFieldShouldBeClearedForDateCalculation( field ) {
		var dateValue = field.hasClass( 'hasDatepicker' ) ? field.datepicker( 'getDate' ) : null;
		return null !== dateValue && -72000000 !== dateValue.getTime();
	}

	function fieldShouldBeClearedForDateCalculation( fieldCall, fieldKey ) {
		return 0 === fieldCall.indexOf( 'input' ) && 0 === fieldKey.indexOf( '[id=' ) && '' === jQuery( fieldKey ).val();
	}

	function getOptionLabelsFromValues( value, field ) {
		var fieldId, options, split, labels, length, index;

		fieldId = field.thisFieldId;
		if ( 'undefined' === typeof __FRMCALC.options || 'undefined' === typeof __FRMCALC.options[ fieldId ]) {
			return value;
		}

		options = __FRMCALC.options[ fieldId ];

		if ( 'checkbox' === field.thisField.type ) {
			split = value.split( ', ' );
			labels = [];
			length = split.length;
			for ( index = 0; index < length; ++index ) {
				if ( 'undefined' !== typeof options[ split[ index ] ]) {
					labels.push( options[ split[ index ] ]);
				}
			}
			return labels.join( ', ' );
		}

		return 'undefined' !== typeof options[ value ] ? options[ value ] : '';
	}

	/**
	 * Trim non-numeric characters from the end of a numeric calculation
	 *
	 * @since 2.03.02
	 * @param {String} numericCalc
	 * @returns {String}
	 */
	function trimNumericCalculation( numericCalc ) {
		var lastChar = numericCalc.charAt( numericCalc.length - 1 );
		if ( lastChar === '+' || lastChar === '-' ) {
			numericCalc = numericCalc.substr( 0, numericCalc.length - 1 );
		}

		return numericCalc;
	}

	function getCallForField( field, allCalcs ) {
		if ( field.thisField.type === 'checkbox' || field.thisField.type === 'radio' || field.thisField.type === 'scale' || field.thisField.type === 'star' ) {
			field.thisFieldCall = field.thisFieldCall + ':checked,' + field.thisFieldCall + '[type=hidden]';
		} else if ( field.thisField.type === 'select' || field.thisField.type === 'time' ) {
			field.thisFieldCall = 'select' + allCalcs.fieldKeys[ field.thisFieldId ] + ' option:selected,' + field.thisFieldCall + '[type=hidden]';
		} else if ( field.thisField.type === 'textarea' ) {
			field.thisFieldCall = field.thisFieldCall + ',textarea' + allCalcs.fieldKeys[ field.thisFieldId ];
		}
		return field;
	}

	function maybeDoCalcForSingleField( fieldInput ) {
		if ( typeof __FRMCALC === 'undefined' ) {
			// there are no calculations on this page
			return;
		}

		// Exit now if field is a type that can't do calculations
		if ( ! fieldCanDoCalc( fieldInput.type ) ) {
			return;
		}

		var allCalcs = __FRMCALC,
			fieldKey = getFieldKey( fieldInput.id, fieldInput.name ),
			triggerField = maybeGetTriggerField( fieldInput ),
			vals = [];

		if ( allCalcs.calc[ fieldKey ] === undefined ) {
			// This field doesn't have any calculations
			return;
		}

		doSingleCalculation( allCalcs, fieldKey, vals, triggerField );
	}

	function fieldCanDoCalc( fieldType ) {
		return -1 !== [ 'text', 'hidden', 'number', 'textarea' ].indexOf( fieldType );
	}

	function getFieldKey( fieldHtmlId, fieldName ) {
		var fieldKey = fieldHtmlId.replace( 'field_', '' ),
			newFieldKey = '';

		if ( isRepeatingFieldByName( fieldName ) ) {
			var fieldKeyParts = fieldKey.split( '-' );
			for ( var i = 0; i < fieldKeyParts.length - 1; i++ ) {
				if ( newFieldKey === '' ) {
					newFieldKey = fieldKeyParts[ i ];
				} else {
					newFieldKey = newFieldKey + '-' + fieldKeyParts[ i ];
				}
			}
			fieldKey = newFieldKey;
		}

		return fieldKey;
	}

	function maybeGetTriggerField( fieldInput ) {
		var triggerField = null;
		if ( isRepeatingFieldByName( fieldInput.name ) ) {
			if ( fieldInput.type !== 'hidden' ) {
				triggerField = jQuery( fieldInput ).closest( '.frm_form_field' );
			} else {
				triggerField = jQuery( fieldInput );
			}
		}

		return triggerField;
	}

	function isRepeatingFieldByName( fieldName ) {
		var fieldNameParts = fieldName.split( '][' );
		return fieldNameParts.length >= 3;
	}

	function getCalcFieldId( field, allCalcs, vals ) {
		if ( typeof vals[ field.valKey ] !== 'undefined' && vals[ field.valKey ] !== 0 ) {
			return vals;
		}

		vals[ field.valKey ] = 0;

		var currency,
			calcField = getCalcField( field );

		if ( calcField === false ) {
			return vals;
		}

		calcField.each( function() {
			var thisVal = getOptionValue( field.thisField, this );

			if ( field.thisField.type === 'date' ) {
				var d = getDateFieldValue( allCalcs.date, thisVal );
                if ( d !== null ) {
					vals[ field.valKey ] = Math.ceil( d / ( 1000 * 60 * 60 * 24 ) );
                }
			} else if ( 'data' === field.thisField.type ) {
				vals[ field.valKey ] = 0;
				if ( '' !== thisVal ) {
					if ( 'SELECT' === this.tagName ) {
						vals[ field.valKey ] = parseFloat( this.querySelector( 'option[value="' + thisVal + '"]' ).textContent );
					} else if ( null !== this.closest( '.frm_checkbox' ) ) {
						vals[ field.valKey ] = 0;
						jQuery( this.closest( '.frm_opt_container' ).querySelectorAll( 'input:checked' ) ).each(
							function() {
								vals[ field.valKey ] += parseFloat( this.parentNode.textContent );
							}
						);
					} else {
						vals[ field.valKey ] = parseFloat( this.parentNode.textContent );
					}
				}
			} else if ( this.hasAttribute( 'data-frmprice' ) || field.thisField.type === 'total' ) {
				// data-frmprice means product field.
				currency = getCurrency( field.formID );
				vals[ field.valKey ] += parseFloat( ! currency ? thisVal : preparePrice( thisVal, currency ) );
			} else {
				var n = thisVal;

				if ( n !== '' && n !== 0 ) {
					n = n.trim();
					n = parseFloat( n.replace( /,/g, '' ).match( /-?[\d\.]+$/ ) );
				}

				if ( typeof n === 'undefined' || isNaN( n ) || n === '' ) {
					n = 0;
				}
				vals[ field.valKey ] += n;
			}
		});

		return vals;
	}

	function getTextCalcFieldId( field, vals ) {
		if ( typeof vals[ field.valKey ] !== 'undefined' && vals[ field.valKey ] !== '' ) {
			return vals;
		}

		vals[ field.valKey ] = '';

		var calcField = getCalcField( field );
		if ( calcField === false ) {
			return vals;
		}

		var count = 0;
		var sep = '';

		calcField.each( function() {
			var thisVal = getOptionValue( field.thisField, this );
			thisVal = thisVal.trim();

			sep = getCalcSep( field, count );

			if ( thisVal !== '' ) {
				vals[ field.valKey ] += sep + thisVal;
				count++;
			}
		});

		return vals;
	}

	function getCalcSep( field, count ) {
		var sep = '';
		if ( count > 0 ) {
			if ( field.thisField.type === 'time' ) {
				if ( count == 1 ) {
					sep = ':';
				} else if ( count == 2 ) {
					sep = ' ';
				}
			} else {
				sep = ', ';
			}

			var customSep = jQuery( document ).triggerHandler( 'frmCalSeparation', [ field.thisField, count ]);
			if ( typeof customSep !== 'undefined' ) {
				sep = customSep;
			}
		}
		return sep;
	}

	function getCalcField( field ) {
		var calcField;
		if ( field.inSection === false ) {
			calcField = jQuery( field.thisFieldCall );
			if ( ! calcField.length && -1 !== [ 'date', 'data' ].indexOf( field.thisField.type ) ) {
				calcField = jQuery( field.thisField.key );
				if ( ! calcField.length && 'data' === field.thisField.type ) {
					// radio and checkbox fields have additional hyphenated values in their ids.
					calcField = jQuery( field.thisField.key.replace( '="', '^=' ).replace( '"]', '-' ) + ']:checked' );
				}
			}
		} else {
			calcField = getSiblingField( field );
		}

		if ( calcField === null || typeof calcField === 'undefined' || calcField.length < 1 ) {
			calcField = false;
		}

		return calcField;
	}

	/**
	* Get the value from a date field regardless of whether datepicker is defined for it
	* Limitations: If using a format with a 2-digit date, '20' will be added to the front if the year is prior to 70
	*/
	function getDateFieldValue( dateFormat, thisVal ) {
		var d = 0;

		if ( ! thisVal ) {
			// If no value was selected in date field, use 0
		} else if ( typeof jQuery.datepicker === 'undefined' ) {
			// If date field is not on the current page

			var splitAt = '-';
			if ( dateFormat.indexOf( '/' ) > -1 ) {
				splitAt = '/';
			}

			var year = '',
				month = '',
				day = '',
				formatPieces = dateFormat.split( splitAt ),
				datePieces = thisVal.split( splitAt );

			for ( var i = 0; i < formatPieces.length; i++ ) {
				if ( formatPieces[ i ] === 'y' ) {
					var currentYear = new Date().getFullYear() + 15;
					var currentYearPlusFifteen = currentYear.toString().substr( 2, 2 );

					if ( datePieces[ i ] > currentYearPlusFifteen ) {
						year = '19' + datePieces[ i ];
					} else {
						year = '20' + datePieces[ i ];
					}
				} else if ( formatPieces[ i ] === 'yy' ) {
					year = datePieces[ i ];
				} else if ( formatPieces[ i ] === 'm' || formatPieces[ i ] === 'mm' ) {
					month = datePieces[ i ];
					if ( month.length < 2 ) {
						month = '0' + month;
					}
				} else if ( formatPieces[ i ] === 'd' || formatPieces[ i ] === 'dd' ) {
					day = datePieces[ i ];
					if ( day.length < 2 ) {
						day = '0' + day;
					}
				}
			}

			d = Date.parse( year + '-' + month + '-' + day );
		} else {
			d = jQuery.datepicker.parseDate( dateFormat, thisVal );
		}
		return d;
	}

	function getSiblingField( field ) {
		if ( typeof field.triggerField === 'undefined' ) {
			return null;
		}

		var fields = null,
			container = field.triggerField.closest( '.frm_repeat_sec, .frm_repeat_inline, .frm_repeat_grid' ),
			repeatArgs = getRepeatArgsFromFieldName( field.triggerField.attr( 'name' ) ),
			siblingFieldCall = field.thisFieldCall.replace( '[id=', '[id^=' ).replace( /-"]/g, '-' + repeatArgs.repeatRow + '"]' );

		if ( container.length || repeatArgs.repeatRow !== '' ) {
			if ( container.length ) {
				fields = container.find( siblingFieldCall );
			} else {
				fields = jQuery( siblingFieldCall );
			}

			if ( fields === null || typeof fields === 'undefined' || fields.length < 1 ) {
				fields = uncheckedSiblingOrOutsideSection( field, container, siblingFieldCall );
			}
		} else {
			// the trigger is not in the repeating section
			fields = getNonSiblingField( field );
		}

		return fields;
	}

	function uncheckedSiblingOrOutsideSection( field, container, siblingFieldCall ) {
		var fields = null;
		if ( siblingFieldCall.indexOf( ':checked' ) ) {
			// check if the field has nothing selected, or is not inside the section
			var inSection = container.find( siblingFieldCall.replace( ':checked', '' ) );
			if ( inSection.length < 1 ) {
				fields = getNonSiblingField( field );
			}
		} else {
			// the field holding the value is outside of the section
			fields = getNonSiblingField( field );
		}
		return fields;
	}

	function getNonSiblingField( field ) {
		var nonSiblingField = jQuery( field.thisFieldCall );
		if ( ! nonSiblingField.length && 'input[' === field.thisFieldCall.substr( 0, 6 ) ) {
			if ( 'undefined' !== typeof field.triggerField && field.triggerField.is( 'div' ) && field.triggerField.hasClass( 'frm_form_field' ) ) {
				nonSiblingField = field.triggerField.find( field.thisFieldCall.replace( 'input[', 'textarea[' ) );
			} else {
				nonSiblingField = jQuery( field.thisFieldCall.replace( 'input[', 'textarea[' ) );
			}
		}
		return nonSiblingField;
	}

	function getOptionValue( thisField, currentOpt ) {
		var thisVal;

		// If current option is an other option, get other value
		if ( isOtherOption( thisField, currentOpt ) ) {
			thisVal = getOtherValueAnyField( thisField, currentOpt );
		} else if ( currentOpt.type === 'checkbox' || currentOpt.type === 'radio' ) {

			if ( currentOpt.checked ) {
				// hasAttribute( 'data-frmprice' ) is a product field.
				thisVal = currentOpt.hasAttribute( 'data-frmprice' ) ? currentOpt.dataset.frmprice : currentOpt.value;
			} else {
				thisVal = currentOpt.dataset.off;
			}
		} else {
			thisVal = currentOpt.hasAttribute( 'data-frmprice' ) ? currentOpt.dataset.frmprice : jQuery( currentOpt ).val();
		}

		if ( typeof thisVal === 'undefined' ) {
			thisVal = '';
		}
		return thisVal;
	}

	/* Check if current option is an "Other" option (not an Other text field) */
	function isOtherOption( thisField, currentOpt ) {
		var isOtherOpt = false;

		// If hidden, check for a value
		if ( currentOpt.type === 'hidden' ) {
			if ( getOtherValueLimited( currentOpt ) !== '' ) {
				isOtherOpt = true;
			}
		} else if ( thisField.type === 'select' ) {
			// If a visible dropdown field
			var optClass = currentOpt.className;
			if ( optClass && optClass.indexOf( 'frm_other_trigger' ) > -1 ) {
				isOtherOpt = true;
			}
		} else if ( thisField.type === 'checkbox' || thisField.type === 'radio' ) {
			// If visible checkbox/radio field
			if ( currentOpt.id.indexOf( '-other_' ) > -1 && currentOpt.id.indexOf( '-otext' ) < 0 ) {
				isOtherOpt = true;
			}
		}

		return isOtherOpt;
	}

	/* Get the value from an "Other" text field */
	/* Does NOT work for visible select fields */
	function getOtherValueLimited( currentOpt ) {
		var otherVal = '',
			otherText = document.getElementById( currentOpt.id + '-otext' );

		if ( otherText !== null && otherText.value !== '' ) {
			otherVal = otherText.value;
		}
		return otherVal;
	}

	/* Get value from Other text field */
	function getOtherValueAnyField( thisField, currentOpt ) {
		var otherVal = 0;

		if ( thisField.type === 'select' ) {
			if ( currentOpt.type === 'hidden' ) {
				if ( isCurrentOptRepeating( currentOpt ) ) {
					// Do nothing because regular doCalculation code takes care of it
				} else {
					otherVal = getOtherValueLimited( currentOpt );
				}
			} else {
				otherVal = getOtherSelectValue( currentOpt );
			}
		} else if ( thisField.type === 'checkbox' || thisField.type === 'radio' ) {
			if ( currentOpt.type === 'hidden' ) {
				// Do nothing because regular doCalculation code takes care of it
			} else {
				otherVal = getOtherValueLimited( currentOpt );
			}
		}

		return otherVal;
	}

	/* Check if current option is in a repeating section */
	function isCurrentOptRepeating( currentOpt ) {
		var isRepeating = false,
			parts = currentOpt.name.split( '[' );

		if ( parts.length > 2 ) {
			isRepeating = true;
		}
		return isRepeating;
	}

	/* Get value from Other text field in a visible dropdown field */
	function getOtherSelectValue( currentOpt ) {
		var fields = getOtherSelects( currentOpt );
		return fields.val();
	}

	/**
	 * Fill in the other text, and select the other option from a dropdown
	 */
	function setOtherSelectValue( thisField, value ) {
		var i,
			fields = getOtherSelects( thisField );

		if ( fields.length < 1 ) {
			return;
		}

		fields.val( value );

		for ( i = 0; i < thisField.options.length; i++ ) {
			if ( thisField.options[ i ].className.indexOf( 'frm_other_trigger' ) !== -1 ) {
				thisField.options[ i ].selected = true;
			}
		}
	}

	function getOtherSelects( currentOpt ) {
		return jQuery( currentOpt ).closest( '.frm_other_container' ).find( '.frm_other_input' );
	}

	function setOtherValueLimited( thisField, value ) {
		var otherText, baseId, parentInput,
			i = 0,
			idParts = thisField.id.split( '-' );
		idParts.pop(); //remove the last id
		baseId = idParts.join( '-' );
		otherText = document.querySelectorAll( '[id^=' + baseId + '-other][id$=otext]' );

		if ( otherText.length > 0 ) {
			for ( i = 0; i < otherText.length; i++ ) {
				if ( otherText[ i ].value === '' ) {
					otherText[ i ].value = value;
					parentInput = document.getElementById( otherText[ i ].id.replace( '-otext', '' ) );
					if ( parentInput !== null ) {
						parentInput.checked = true;
					}
				}
			}
		}
	}

	function savingDraftEntry( object ) {
		var isDraft = false,
			savingDraft = jQuery( object ).find( '.frm_saving_draft' );

		if ( savingDraft.length ) {
			isDraft = savingDraft.val();
		}
		return isDraft;
	}

	function goingToPrevPage( object ) {
		var goingBack = false,
			nextPage = jQuery( object ).find( '.frm_next_page' );

		if ( nextPage.length && nextPage.val() ) {
			var formID = jQuery( object ).find( 'input[name="form_id"]' ).val();
			var prevPage = jQuery( object ).find( 'input[name="frm_page_order_' + formID + '"]' );
			if ( prevPage.length ) {
				prevPage = parseInt( prevPage.val() );
			} else {
				prevPage = 0;
			}

			if ( ! prevPage || ( parseInt( nextPage.val() ) < prevPage ) ) {
				goingBack = true;
			}
		}

		return goingBack;
	}

	function afterFormSubmitted( event, form ) {
		// if the success message is showing, run the logic
		checkConditionalLogic( 'pageLoad' );
		doEditInPlaceCleanUp( form );
		checkFieldsOnPage();
		maybeShowMoreStepsButton();
	}

	function afterPageChanged() {
		checkFieldsOnPage();
		addTopAddRowBtnForRepeater();
		maybeDisableCheckboxesWithLimit();
		// make sure this comes last so that the total is calculated only after knowing the visible fields
		calcProductsTotal();
		maybeShowMoreStepsButton();
		// calculations in a repeater need to be done again after page is changed to fix issue #3414
		triggerChangeOnCalcTriggers();
	}

	function triggerChangeOnCalcTriggers() {
		if ( 'undefined' === typeof __FRMCALC || 'undefined' === typeof __FRMCALC.fieldKeys ) {
			return;
		}

		Object.values( __FRMCALC.fieldKeys ).forEach( function( key ) {
			jQuery( key + ':not(label):not([type=hidden])' ).each( function() {
				jQuery( this ).trigger({ type: 'change', selfTriggered: true });
			});
		});
	}

	/** Google Tables **/

	function generateGoogleTables( graphs, graphType ) {
		for ( var num = 0; num < graphs.length; num++ ) {
			generateSingleGoogleTable( graphs[ num ], graphType );
		}
	}

	function generateSingleGoogleTable( opts, type ) {
		google.load( 'visualization', '1.0', { packages: [ type ], callback: function() {
			compileGoogleTable( opts );
		} });
	}

	function compileGoogleTable( opts ) {
		var data = new google.visualization.DataTable(),
			showID = false;

		if ( jQuery.inArray( 'id', opts.options.fields ) !== -1 ) {
			showID = true;
			data.addColumn( 'number', frm_js.id );
		}

		var colCount = opts.fields.length;
		var type = 'string';
		for ( var i = 0, l = colCount; i < l; i++ ) {
			var thisCol = opts.fields[ i ];
			type = getGraphType( thisCol );

			data.addColumn( type, thisCol.name );
		}

		var showEdit = false;
		if ( opts.options.edit_link ) {
			showEdit = true;
			data.addColumn( 'string', opts.options.edit_link );
		}

		var showDelete = false;
		if ( opts.options.delete_link ) {
			showDelete = true;
			data.addColumn( 'string', opts.options.delete_link );
		}

		var col = 0;
		if ( opts.entries !== null ) {
			var entryCount = opts.entries.length;
			data.addRows( entryCount );

			var row = 0;

			for ( var e = 0, len = entryCount; e < len; e++ ) {
				col = 0;
				var entry = opts.entries[ e ];
				if ( showID ) {
					data.setCell( row, col, entry.id );
					col++;
				}

				for ( var field = 0, fieldCount = colCount; field < fieldCount; field++ ) {
					var thisEntryCol = opts.fields[ field ];
					type = getGraphType( thisEntryCol );

					var fieldVal = entry.metas[ thisEntryCol.id ];
					if ( type === 'number' && ( fieldVal === null || fieldVal === '' ) ) {
						fieldVal = 0;
					} else if ( type === 'boolean' ) {
						if ( fieldVal === null || fieldVal == 'false' || fieldVal === false ) {
							fieldVal = false;
						} else {
							fieldVal = true;
						}
					}

					data.setCell( row, col, fieldVal );

					col++;
				}

				if ( showEdit ) {
					if ( typeof entry.editLink !== 'undefined' ) {
						data.setCell( row, col, '<a href="' + entry.editLink + '">' + opts.options.edit_link + '</a>' );
					} else {
						data.setCell( row, col, '' );
					}
					col++;
				}

				if ( showDelete ) {
					if ( typeof entry.deleteLink !== 'undefined' ) {
						data.setCell( row, col, '<a href="' + entry.deleteLink + '" class="frm_delete_link" data-frmconfirm="' + opts.options.confirm + '">' + opts.options.delete_link + '</a>' );
					} else {
						data.setCell( row, col, '' );
					}
				}

				row++;
			}
		} else {
			data.addRows( 1 );
			col = 0;

			for ( i = 0, l = colCount; i < l; i++ ) {
				if ( col > 0 ) {
					data.setCell( 0, col, '' );
				} else {
					data.setCell( 0, col, opts.options.no_entries );
				}
				col++;
			}
		}

		var chart = new google.visualization.Table( document.getElementById( 'frm_google_table_' + opts.options.form_id ) );
		chart.draw( data, opts.graphOpts );
	}

	/** Google Graphs **/

	function generateGoogleGraphs( graphs ) {
		var l, i;
		l = graphs.length;
		for ( i = 0; i < l; i++ ) {
			generateSingleGoogleGraph( graphs[ i ]);

			if ( 'string' === typeof graphs[ i ].options.width && '%' === graphs[ i ].options.width.substr( -1 ) ) {
				addResponsiveGraphListener( graphs[ i ]);
			}
		}
	}

	function addResponsiveGraphListener( graphData ) {
		window.addEventListener(
			'resize',
			function() {
				generateSingleGoogleGraph( graphData );
			}
		);
	}

	function generateSingleGoogleGraph( graphData ) {
		google.charts.load( 'current', { packages: [ graphData.package ] });
		google.charts.setOnLoadCallback( function() {
			compileGoogleGraph( graphData );
		});
	}

	function compileGoogleGraph( graphData ) {
		var data = new google.visualization.DataTable();
		data = google.visualization.arrayToDataTable( graphData.data );

		var chartDiv = document.getElementById( 'chart_' + graphData.graph_id );
		if ( chartDiv === null ) {
			return;
		}

		var type = ( graphData.type.charAt( 0 ).toUpperCase() + graphData.type.slice( 1 ) );
		if ( type !== 'Histogram' && type !== 'Table' ) {
			type += 'Chart';
		}

		var chart = new google.visualization[ type ]( chartDiv );
		chart.draw( data, graphData.options );
		jQuery( document ).trigger( 'frmDrawChart', [ chart, 'chart_' + graphData.graph_id, data ]);
	}

	function getGraphType( field ) {
		var type = 'string';
		if ( field.type === 'number' ) {
			type = 'number';
		} else if ( field.type === 'checkbox' || field.type === 'select' ) {
			var optCount = field.options.length;
			if ( field.type === 'select' && field.options[ 0 ] === '' ) {
				if ( field.field_options.post_field === 'post_status' ) {
					optCount = 3;
				} else {
					optCount = optCount - 1;
				}
			}
			if ( optCount == 1 ) {
				type = 'boolean';
			}
		}
		return type;
	}

	/** Repeating Fields **/

	function removeRow() {
		if ( ! confirmRowRemoval() ) {
			return;
		}

		/*jshint validthis:true */
		var rowNum = jQuery( this ).data( 'key' ),
			sectionID = jQuery( this ).data( 'parent' ),
			id = 'frm_section_' + sectionID + '-' + rowNum,
			thisRow = jQuery( this ).parents( 'div[id^="frm_section_"]' ),
			fields = thisRow.find( 'input, select, textarea' ),
			formId = jQuery( this ).closest( 'form' ).find( 'input[name="form_id"]' ).val();

		thisRow.fadeOut( 'slow', function() {
			thisRow.remove();

			fields.each( function() {
				/* update calculations when a row is removed */
				var fieldID = frmFrontForm.getFieldId( this, false );
				if ( this.type != 'file' ) {
					doCalculation( fieldID, jQuery( this ) );
				}

				var container = 'frm_field_' + fieldID + '-' + sectionID + '-' + rowNum + '_container';
				removeFromHideFields( container, formId );

				// If the field is an RTE, remove the editor associated with it
				//  this is done so that if/when the field is Added again it get initialized
				if ( this.classList.contains( 'wp-editor-area' ) ) {
					removeRichText( this.id );
				}
			});

			showAddButton( sectionID );

			/* eslint-disable camelcase */
			if ( typeof frmThemeOverride_frmRemoveRow === 'function' ) {
				frmThemeOverride_frmRemoveRow( id, thisRow );
			}
			/* eslint-enable camelcase */

			jQuery( document ).trigger( 'frmAfterRemoveRow' );
		});

		return false;
	}

	function confirmRowRemoval() {
		if ( ! frm_js.repeaterRowDeleteConfirmation ) {
			return true;
		}
		return confirm( frm_js.repeaterRowDeleteConfirmation );
	}

	function hideAddButton( sectionID ) {
		jQuery( '#frm_field_' + sectionID + '_container .frm_add_form_row' ).addClass( 'frm_hide_add_button' );
	}

	function showAddButton( sectionID ) {
		jQuery( '#frm_field_' + sectionID + '_container .frm_add_form_row' ).removeClass( 'frm_hide_add_button' );
	}

	function addRow() {
		/*jshint validthis:true */
		var thisBtn, id, i, numberOfSections, lastRowIndex, stateField, state, form, data, success, error, extraParams;

		// If row is currently being added, leave now
		if ( currentlyAddingRow === true ) {
			return false;
		}

		// Indicate that a row is being added (so double clicking Add button doesn't cause problems)
		currentlyAddingRow = true;

		thisBtn = jQuery( this );
		id = thisBtn.data( 'parent' );
		i = 0;
		numberOfSections = jQuery( '.frm_repeat_' + id ).length;

		if ( numberOfSections > 0 ) {
			lastRowIndex = jQuery( '.frm_repeat_' + id ).last().attr( 'id' ).replace( 'frm_section_' + id + '-', '' );
			if ( lastRowIndex.indexOf( 'i' ) > -1 ) {
				i = 1;
			} else {
				i = 1 + parseInt( lastRowIndex );
			}
		}

		stateField = document.querySelector( 'input[name="frm_state"]' );
		state = null !== stateField ? stateField.value : '';

		form = jQuery( this ).closest( 'form' ).get( 0 );
		data = {
			action: 'frm_add_form_row',
			field_id: id,
			i: i,
			numberOfSections: numberOfSections,
			nonce: frm_js.nonce,
			frm_state: state
		};
		success = function( r ) {
			var html, item, checked, fieldID, fieldObject, reset, repeatArgs, j, inputRanges;

			//only process row if row actually added
			if ( r.html ) {
				html = r.html;
				item = jQuery( html ).addClass( 'frm-fade-in' );
				thisBtn.parents( '.frm_section_heading' ).append( item );
				inputRanges = item[0].querySelectorAll( 'input[type=range]' );
				for ( j = 0; j < inputRanges.length; j++ ) {
					handleSliderEvent.call( inputRanges[j]);
				}

				if ( r.is_repeat_limit_reached ) {
					hideAddButton( id );
				}

				checked = [ 'other' ];
				reset = 'reset';
				repeatArgs = {
					repeatingSection: id.toString(),
					repeatRow: i.toString()
				};

				// hide fields with conditional logic
				jQuery( html ).find( 'input, select, textarea' ).each( function() {
						// Readonly dropdown fields won't have a name attribute
						if ( this.name === '' ) {
							return true;
						}
						if ( this.type == 'file' ) {
							fieldID = this.name.replace( 'file', '' ).split( '-' )[0];
						} else {
							fieldID = this.name.replace( 'item_meta[', '' ).split( ']' )[ 2 ].replace( '[', '' );
						}
						if ( jQuery.inArray( fieldID, checked ) == -1 ) {
							if ( this.id === false || this.id === '' ) {
								return;
							}

							fieldObject = jQuery( '#' + this.id );
							checked.push( fieldID );
							hideOrShowFieldById( fieldID, repeatArgs );
							updateWatchingFieldById( fieldID, repeatArgs, 'value changed' );
							// TODO: maybe trigger a change instead of running these three functions
							checkFieldsWithConditionalLogicDependentOnThis( fieldID, fieldObject );
							checkFieldsWatchingLookup( fieldID, fieldObject, 'value changed' );
							doCalculation( fieldID, fieldObject );
							maybeDoCalcForSingleField( fieldObject.get( 0 ) );
							reset = 'persist';
						}
				});

				jQuery( html ).find( '.frm_html_container' ).each(
					function() {
						// check heading logic
						var fieldID = this.id.replace( 'frm_field_', '' ).split( '-' )[ 0 ];
						checked.push( fieldID );
						hideOrShowFieldById( fieldID, repeatArgs );
					}
				);

				loadDropzones( repeatArgs.repeatRow );
				loadSliders();

				// trigger autocomplete
				loadChosen();

				// load rich textboxes
				jQuery( html ).find( '.frm_html_container' ).each( function() {
					// check heading logic
					var fieldID = this.id.replace( 'frm_field_', '' ).split( '-' )[ 0 ];
					checked.push( fieldID );
					hideOrShowFieldById( fieldID, repeatArgs );
				});

				// Find any RTEs in the new row/repeat and initialize the editor for them
				//  there is an assumption here that tinyMCEPreInit.mceInit[0] referes to
				//  an RTE that was initially loaded on the page. It's a safe assumption as
				//  adding a row/repeat wouldn't have any RTEs if there wasn't one on the
				//  page in the first place.
				jQuery( html ).find( '.wp-editor-area' ).each( function() {
					initRichText( this.id );
				});
			}

			/* eslint-disable camelcase */
			if ( typeof frmThemeOverride_frmAddRow === 'function' ) {
				frmThemeOverride_frmAddRow( id, r );
			}
			/* eslint-enable camelcase */

			jQuery( document ).trigger( 'frmAfterAddRow' );

			jQuery( '.frm_repeat_' + id ).each( function( i ) {
				this.style.zIndex = 999 - i;
			});

			currentlyAddingRow = false;
		};

		error = function() {
			currentlyAddingRow = false;
		};

		extraParams = { dataType: 'json' };

		postToAjaxUrl( form, data, success, error, extraParams );

		return false;
	}

	function triggerToggleClickOnSpace( e ) {
		if ( 32 === e.which ) {
			this.click();
		}
	}

	function removeRichText( id ) {
		tinymce.EditorManager.execCommand( 'mceRemoveEditor', true, id );
	}

	function initRichText( id ) {
		var key = Object.keys( tinyMCEPreInit.mceInit )[0],
			orgSettings = tinyMCEPreInit.mceInit[ key ],
			newValues = {
				selector: '#' + id,
				body_class: orgSettings.body_class.replace( key, id )
			},
			newSettings = Object.assign({}, orgSettings, newValues );
		tinymce.init( newSettings );
	}

	/*****************************************************
	 * In-place edit
	 ******************************************************/

	function editEntry() {
		/*jshint validthis:true */
		var $edit = jQuery( this ),
			entryId = $edit.data( 'entryid' ),
			prefix = $edit.data( 'prefix' ),
			postId = $edit.data( 'pageid' ),
			formId = $edit.data( 'formid' ),
			cancel = $edit.data( 'cancel' ),
			fields = $edit.data( 'fields' ),
			excludeFields = $edit.data( 'excludefields' ),
			startPage = $edit.data( 'startpage' ),
			$cont = jQuery( document.getElementById( prefix + entryId ) ),
			orig = $cont.html();

		$cont.html( '<span class="frm-loading-img" id="' + prefix + entryId + '"></span><div class="frm_orig_content" style="display:none">' + orig + '</div>' );
		jQuery.ajax({
			type: 'POST',
			url: frm_js.ajax_url,
			dataType: 'html',
			data: {
				action: 'frm_entries_edit_entry_ajax',
				post_id: postId,
				entry_id: entryId,
				id: formId,
				nonce: frm_js.nonce,
				fields: fields,
				exclude_fields: excludeFields,
				start_page: startPage
			},
			success: function( html ) {
				$cont.children( '.frm-loading-img' ).replaceWith( html );
				$edit.removeClass( 'frm_inplace_edit' ).addClass( 'frm_cancel_edit' );
				$edit.html( cancel );
				checkConditionalLogic( 'editInPlace' );

				// Make sure fields just loaded are properly bound
				if ( typeof frmFrontForm.fieldValueChanged === 'function' ) {
					jQuery( document ).on( 'change', '.frm-show-form input[name^="item_meta"], .frm-show-form select[name^="item_meta"], .frm-show-form textarea[name^="item_meta"]', frmFrontForm.fieldValueChanged );
				}
				checkFieldsOnPage( prefix + entryId );
				triggerEvent( document, 'frmInPlaceEdit' );
			}
		});
		return false;
	}

	function cancelEdit() {
		/*jshint validthis:true */

		var $cancelLink = jQuery( this ),
			prefix = $cancelLink.data( 'prefix' ),
			entryId = $cancelLink.data( 'entryid' ),
			$cont = jQuery( document.getElementById( prefix + entryId ) );

		$cont.children( '.frm_forms' ).replaceWith( '' );
		$cont.children( '.frm_orig_content' ).fadeIn( 'slow' ).removeClass( 'frm_orig_content' );

		switchCancelToEdit( $cancelLink );
	}

	/**
	 * Switch a Cancel Link back to Edit
	 *
	 * @since 2.03.08
	 *
	 * @param {object} $link
     */
	function switchCancelToEdit( $link ) {
		var label = $link.data( 'edit' );
		$link.removeClass( 'frm_cancel_edit' ).addClass( 'frm_inplace_edit' );
		$link.html( label );
	}

	function deleteEntry() {
		/*jshint validthis:true */
		var entryId, prefix,
			$link = jQuery( this ),
			confirmText = $link.data( 'deleteconfirm' );

		if ( confirm( confirmText ) ) {
			entryId = $link.data( 'entryid' );
			prefix = $link.data( 'prefix' );

			$link.replaceWith( '<span class="frm-loading-img" id="frm_delete_' + entryId + '"></span>' );
			jQuery.ajax({
				type: 'POST',
				url: frm_js.ajax_url,
				data: { action: 'frm_entries_destroy', entry: entryId, nonce: frm_js.nonce },
				success: function( html ) {
					if ( html.replace( /^\s+|\s+$/g, '' ) === 'success' ) {
						var container = jQuery( document.getElementById( prefix + entryId ) );
						container.fadeOut( 'slow', function() {
							container.remove();
						});
						jQuery( document.getElementById( 'frm_delete_' + entryId ) ).fadeOut( 'slow' );
						jQuery( document ).trigger( 'frmEntryDeleted', [ entryId ]);
					} else {
						jQuery( document.getElementById( 'frm_delete_' + entryId ) ).replaceWith( html );
					}
				}
			});
		}
		return false;
	}

	/**
	 * Switch Cancel link back to Edit link after entry is updated in-place
	 *
	 * @since 2.03.08
	 *
	 * @param {object} form
     */
	function doEditInPlaceCleanUp( form ) {
		var entryIdField = jQuery( form ).find( 'input[name="id"]' );

		if ( entryIdField.length ) {
			var link = document.getElementById( 'frm_edit_' + entryIdField.val() );

			if ( isCancelLink( link ) ) {
				switchCancelToEdit( jQuery( link ) );
			}
		}
	}

	/**
	 * Check if a link is a cancel link
	 *
	 * @since 2.03.08
	 *
	 * @param {object} link
	 * @returns {boolean}
     */
	function isCancelLink( link ) {
		return ( link !== null && link.className.indexOf( 'frm_cancel_edit' ) > -1 );
	}

	/**********************************************
	 * Ajax time field unique check
	 *********************************************/

	function loadUniqueTimeFields() {
		var timeFields, i, dateField;

		if ( typeof __frmUniqueTimes === 'undefined' ) {
			return;
		}

		timeFields = __frmUniqueTimes;
		for ( i = 0; i < timeFields.length; i++ ) {
			dateField = document.getElementById( timeFields[ i ].dateID );
			jQuery( dateField ).on( 'change', maybeTriggerUniqueTime );
			if ( '' !== dateField.value ) {
				jQuery( dateField ).trigger( 'change' );
			}
		}
	}

	function maybeTriggerUniqueTime() {
		/*jshint validthis:true */
		var timeFields = __frmUniqueTimes;
		for ( var i = 0; i < timeFields.length; i++ ) {
			if ( timeFields[ i ].dateID == this.id ) {
				frmProForm.removeUsedTimes( this, timeFields[ i ].timeID );
			}
		}
	}

	/**********************************************
	 * General Helpers
	 *********************************************/

	function checkFieldsOnPage( chosenContainer ) {
		checkPreviouslyHiddenFields();
		loadDateFields();
		loadCustomInputMasks();
		loadSliders();
		loadChosen( chosenContainer );
		checkDynamicFields();
		checkLookupFields();
		triggerCalc();
		loadDropzones();
		checkPasswordFields();
	}

	function checkPasswordFields() {
		var passwordFields = document.querySelectorAll( '.frm_strength_meter' ),
			event = document.createEvent( 'HTMLEvents' );

		event.initEvent( 'keyup', true, true );

		for ( var i = 0; i < passwordFields.length; i++ ) {
			passwordFields[ i ].dispatchEvent( event );
		}
	}

	function checkPreviouslyHiddenFields() {
		if ( typeof __frmHideFields !== 'undefined' ) {
			frmProForm.hidePreviouslyHiddenFields();
		}
	}

	function loadChosen( chosenContainer ) {
		if ( jQuery().chosen ) {
			var opts = {
				allow_single_deselect: true,
				no_results_text: frm_js.no_results,
				search_contains: true
			};
			if ( typeof __frmChosen !== 'undefined' ) {
				opts = '{' + __frmChosen + '}';
			}

			if ( typeof chosenContainer !== 'undefined' ) {
				jQuery( '#' + chosenContainer ).find( '.frm_chzn' ).chosen( opts );
			} else {
				jQuery( '.frm_chzn' ).chosen( opts );
			}
		}
	}

	function loadStars() {
		/*jshint validthis:true */
		updateStars( this );
	}

	function hoverStars() {
		/*jshint validthis:true */
		var input = this.previousSibling;
		updateStars( input );
	}

	function updateStars( hovered ) {
		var starGroup = hovered.parentElement,
			stars = starGroup.children,
			current = parseInt( hovered.value ),
			starClass = 'star-rating',
			selectLabel = false;

		starGroup.className += ' frm-star-hovered';

		for ( var i = 0; i < stars.length; i++ ) {
			if ( stars[ i ].matches( '.' + starClass ) ) {
				if ( selectLabel ) {
					stars[ i ].className += ' star-rating-hover';
				} else {
					stars[ i ].classList.remove( 'star-rating-hover', 'star-rating-on' );
				}
			} else {
				selectLabel = ( parseInt( stars[ i ].value ) <= current );
			}
		}
	}

	function unhoverStars() {
		/*jshint validthis:true */
		var input = this.previousSibling,
			starGroup = input.parentElement;
		starGroup.classList.remove( 'frm-star-hovered' );
		var stars = starGroup.children;
		var selected = jQuery( starGroup ).find( 'input:checked' ).attr( 'id' );
		var isSelected = '';
		var starClass = 'star-rating';

		for ( var i = stars.length - 1; i > 0; i-- ) {
			if ( stars[ i ].matches( '.' + starClass ) ) {
				stars[ i ].classList.remove( 'star-rating-hover' );
				if ( isSelected === '' && typeof selected !== 'undefined' && stars[ i ].getAttribute( 'for' ) == selected ) {
					isSelected = ' star-rating-on';
				}
				if ( isSelected !== '' ) {
					stars[ i ].className += isSelected;
				}
			}
		}
	}

	/**
	 * Clears star rating.
	 *
	 * @since 5.4
	 *
	 * @param {HTMLElement} starGroup    Star group element.
	 * @param {Boolean}     noClearInput Whether to clear input value or not. Default is `false`.
	 */
	function clearStars( starGroup, noClearInput ) {
		var labels, input;

		labels = starGroup.querySelectorAll( '.star-rating-on' );
		if ( labels && labels.length ) {
			labels.forEach( function( el ) {
				el.classList.remove( 'star-rating-on' );
			});
		}

		if ( ! noClearInput ) {
			input = starGroup.querySelector( 'input[type="radio"]:checked' );
			if ( input ) {
				input.checked = false;
			}
		}
	}

	function handleSliderEvent() {
		var i, c, fieldKey, currency;

		c = this.parentNode.children;
		for ( i = 0; i < c.length; i++ ) {
			if ( c[ i ].className !== 'frm_range_value' ) {
				continue;
			}

			fieldKey = getFieldKey( this.id, this.name );
			if ( 'undefined' !== typeof __FRMCALC && __FRMCALC.calc[ fieldKey ]) {
				currency = getCurrencyFromCalcRule( __FRMCALC.calc[ fieldKey ]);
				c[i].textContent = formatCurrency( normalizeTotal( this.value, currency ), currency );
			} else {
				c[i].textContent = this.value;
			}

			break;
		}
	}

	function loadSliders() {
		jQuery( document ).on( 'input change', 'input[data-frmrange]', handleSliderEvent );
	}

	function getCurrencyFromCalcRule( calcRule ) {
		return 'undefined' !== typeof calcRule.custom_currency ? calcRule.custom_currency : getCurrency( calcRule.form_id );
	}

	function setInlineFormWidth() {
		var children, f, inlineForm,
			inlineForms = jQuery( '.frm_inline_form .frm_fields_container' );

		if ( inlineForms.length ) {
			for ( f = 0; f < inlineForms.length; f++ ) {
				inlineForm = jQuery( inlineForms[ f ]);
				children = inlineForm.children( '.frm_form_field' );

				if ( children.length <= 12 && ! fieldHasLayoutClass( children.last() ) ) {
					addAutoInlineLayout( inlineForm, children );
				}
			}
		}
	}

	function fieldHasLayoutClass( field ) {
		var i,
			classList = field.attr( 'class' ),
			layoutClasses = [ 'frm_full', 'half', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth' ];

		if ( typeof classList === 'undefined' ) {
			return false;
		}
		for ( i = 1; i <= 12; i++ ) {
			if ( field.hasClass( 'frm' + i ) ) {
				return true;
			}

			if ( i === 12 ) {
				for ( var c = 0; c < layoutClasses.length; c++ ) {
					if ( classList.indexOf( layoutClasses[ c ]) !== -1 ) {
						return true;
					}

					if ( c === layoutClasses.length - 1 ) {
						return false;
					}
				}
			}
		}
	}

	function addAutoInlineLayout( inlineForm, children ) {
		var fieldCount, colCount, i;

		fieldCount = children.length + 1; // include the submit button
		colCount = Math.max( 2, Math.ceil( 12 / fieldCount ) );
		for ( i = 0; i < children.length; i++ ) {
			if ( ! fieldHasLayoutClass( jQuery( children[ i ]) ) ) {
				jQuery( children[ i ]).addClass( 'frm' + colCount );
			}
		}

		inlineForm.children( '.frm_submit' ).addClass( 'frm' + colCount );
	}

	function checkConditionalLogic( event ) {
		if ( typeof __frmHideOrShowFields !== 'undefined' ) {
			frmProForm.hideOrShowFields( __frmHideOrShowFields, event );
		} else {
			showForm();
		}
	}

	function showForm() {
		jQuery( '.frm_pro_form' ).fadeIn( 'slow' );
	}

	function checkDynamicFields() {
		if ( typeof __frmDepDynamicFields !== 'undefined' ) {
			frmProForm.checkDependentDynamicFields( __frmDepDynamicFields );
		}
	}

	function checkLookupFields() {
		if ( typeof __frmDepLookupFields !== 'undefined' ) {
			frmProForm.checkDependentLookupFields( __frmDepLookupFields );
		}
	}

	function triggerChange( input, fieldKey ) {
		if ( typeof fieldKey === 'undefined' ) {
			fieldKey = 'dependent';
		}

		if ( input.length > 1 ) {
			input = input.eq( 0 );
		}

		input.trigger({ type: 'change', selfTriggered: true, frmTriggered: fieldKey });
	}

	function loadCustomInputMasks() {
		if ( typeof __frmMasks === 'undefined' ) {
			return;
		}

		var maskFields = __frmMasks;
		for ( var i = 0; i < maskFields.length; i++ ) {
			jQuery( maskFields[ i ].trigger ).attr( 'data-frmmask', maskFields[ i ].mask );
		}
	}

	// Get the section ID and repeat row from a field name
	function getRepeatArgsFromFieldName( fieldName ) {
		var repeatArgs = { repeatingSection: '', repeatRow: '' };

		if ( typeof fieldName !== 'undefined' && isRepeatingFieldByName( fieldName ) ) {
			var inputNameParts = fieldName.split( '][' );
			repeatArgs.repeatingSection = inputNameParts[ 0 ].replace( 'item_meta[', '' );
			repeatArgs.repeatRow = inputNameParts[ 1 ];
		}

		return repeatArgs;
	}

	function fadeOut( $remove ) {
		$remove.fadeOut( 'slow', function() {
			$remove.remove();
		});
	}

	function objectSearch( array, value ) {
		for ( var prop in array ) {
			if ( array.hasOwnProperty( prop ) ) {
				if ( array[ prop ] === value ) {
					return prop;
				}
			}
		}
		return null;
	}

	function isNumeric( obj ) {
		return ! Array.isArray( obj ) && ( obj - parseFloat( obj ) + 1 ) >= 0;
	}

	function checkPasswordField() {
		var fieldId,
			fieldIdSplit,
			checks,
			split,
			suffix,
			check,
			span;

		/*jshint validthis:true */
		if ( this.className.indexOf( 'frm_strength_meter' ) > -1 ) {
			fieldId = this.name.substr( this.name.indexOf( '[' ) + 1 ).replace( /\]\[\d\]\[/, '-' );
			fieldId = fieldId.substr( 0, fieldId.length - 1 );
			fieldIdSplit = fieldId.split( '-' );

			if ( fieldIdSplit.length === 2 ) {
				fieldId = fieldIdSplit[1] + '-' + fieldIdSplit[0];
			}

			checks = passwordChecks();
			split = this.id.split( '-' );
			suffix = split.length > 1 && ! isNaN( split[ split.length - 1 ]) ? '-' + split[ split.length - 1 ] : '';

			for ( check in checks ) {
				span = document.getElementById( 'frm-pass-' + check + '-' + fieldId + suffix );
				addOrRemoveVerifyPass( checks[ check ], this.value, span );
			}
		}
	}

	function passwordChecks() {
		return {
			'eight-char': /^.{8,}$/,
			number: /\d/,
			uppercase: /[A-Z]/,
			lowercase: /[a-z]/,
			'special-char': /(?=.*[^a-zA-Z0-9])/
		};
	}

	function addOrRemoveVerifyPass( regEx, password, span ) {
		if ( span !== null ) {
			var remove = regEx.test( password );
			if ( remove ) {
				maybeRemovePassReq( span );
			} else {
				maybeRemovePassVerified( span );
			}
		}
	}

	function maybeRemovePassReq( span ) {
		if ( span.classList.contains( 'frm-pass-req' ) ) {
			span.classList.remove( 'frm-pass-req' );
			span.classList.add( 'frm-pass-verified' );
		}
	}

	function maybeRemovePassVerified( span ) {
		if ( span.classList.contains( 'frm-pass-verified' ) ) {
			span.classList.remove( 'frm-pass-verified' );
			span.classList.add( 'frm-pass-req' );
		}
	}

	function checkCheckboxSelectionLimit() {
		/*jshint validthis:true */
		var limit = parseInt( this.getAttribute( 'data-frmlimit' ) ),
			checked = this.checked;

		if ( ! limit ) {
			return;
		}

		var allBoxes = jQuery( this ).parents( '.frm_opt_container' ).find( 'input[type="checkbox"]' );
		if ( limit >= allBoxes.length ) {
			return;
		}

		var checkedBoxes = allBoxes.filter( function() {
			return this.checked;
		});

		if ( checked ) {
			if ( checkedBoxes.length >= limit ) {
				allBoxes.filter( function() {
					return ! this.checked;
				}).attr( 'disabled', 'disabled' );
			}
		} else {
			allBoxes.prop( 'disabled', false );
		}
	}

	function addTopAddRowBtnForRepeater() {
		// Set up 'Add Row' buttons that will show up when all repeated fields have been removed.
		// Do it this way while we await CSS4 selectors that can do sth like .frm_repeat_sec:first-of-class
		jQuery( '.frm_section_heading:has(div[class*="frm_repeat_"])' ).each( function() {
			var firstRepeatedSection = jQuery( this ).find( 'div[class*="frm_repeat_"]' ).first();
			firstRepeatedSection.before( firstRepeatedSection.find( '.frm_add_form_row' ).clone().addClass( 'frm_hidden' ) );
		});
	}

	function maybeDisableCheckboxesWithLimit() {
		// Disable unchecked ones after form submit error if limit is already reached.
		jQuery( 'input[type="checkbox"][data-frmlimit]:not(:checked)' ).each( function() {
			var limit = parseInt( this.getAttribute( 'data-frmlimit' ) );

			if ( ! limit ) {
				return;
			}

			var allBoxes = jQuery( this ).parents( '.frm_opt_container' ).find( 'input[type="checkbox"]' );
			if ( limit >= allBoxes.length ) {
				// Unreasonable limit
				return;
			}

			var checkedBoxes = allBoxes.filter( function() {
				return this.checked;
			});

			if ( limit > checkedBoxes.length ) {
				return;
			}

			this.setAttribute( 'disabled', 'disabled' );
		});
	}

	function checkQuantityFieldMinMax( input ) {
		var val = parseFloat( input.value ? input.value.trim() : 0 ),
			max = input.hasAttribute( 'max' ) ? parseFloat( input.getAttribute( 'max' ) ) : 0,
			min = input.hasAttribute( 'min' ) ? parseFloat( input.getAttribute( 'min' ) ) : 0;

		if ( isNaN( val ) ) {
			return 0;
		}

		max = isNaN( max ) ? 0 : max;
		min = isNaN( min ) ? 0 : ( min < 0 ? 0 : min );

		if ( val < min ) {
			input.value = min;
			return min;
		}

		if ( 0 !== max && val > max ) {
			input.value = max;
			return max;
		}

		return val;
	}

	/**
	 * @since 4.04.01
	 */
	function setHiddenProduct( input ) {
		input.setAttribute( 'data-frmhidden', '1' );
		triggerChange( jQuery( input ) );
	}

	/**
	 * @since 4.04.01
	 */
	function setHiddenProductContainer( container ) {
		if ( container.innerHTML.indexOf( 'data-frmprice' ) !== -1 ) {
			jQuery( container ).find( 'input[data-frmprice], select:has([data-frmprice])' ).attr( 'data-frmhidden', '1' );
		}
	}

	/**
	 * @since 4.04.01
	 */
	function setShownProduct( input ) {
		var wasHidden = input.getAttribute( 'data-frmhidden' );

		if ( wasHidden !== null ) {
			input.removeAttribute( 'data-frmhidden' );
			triggerChange( jQuery( input ) );
		}
	}

	/**
	 * @since 4.04
	 */
	function calcProductsTotal( e ) {
		var formTotals = [],
			totalFields;

		if ( typeof __FRMCURR  === 'undefined' ) {
			return;
		}

		if ( undefined !== e && 'undefined' !== typeof e.target && ( 'keyup' === e.type || 'change' === e.type ) ) {
			// an event has been fired
			var el = e.target;
			if ( el.hasAttribute( 'data-frmprice' ) && el instanceof HTMLInputElement && 'text' === el.type ) {
				// user-defined product
				el.setAttribute( 'data-frmprice', el.value.trim() );
			}
		}

		totalFields = jQuery( '[data-frmtotal]' );
		if ( ! totalFields.length ) {
			return;
		}

		totalFields.each( function() {
			var currency, formId, formatted,
				total = 0,
				totalField = jQuery( this ),
				$form = totalField.closest( 'form' ),
				isRepeatingTotal = isRepeatingFieldByName( this.name );

			if ( ! $form.length ) {
				return;
			}

			formId = $form.find( 'input[name="form_id"]' ).val();
			currency = getCurrency( formId );

			if ( typeof formTotals[ formId ] !== 'undefined' && ! isRepeatingTotal ) {
				total = formTotals[ formId ];
			} else {

				$form.find( 'input[data-frmprice],select:has([data-frmprice])' ).each( function() {
					var quantity, $this,
						price = 0,
						isUserDef = false,
						isSingle = false;

					// total fields inside repeaters are for their corresponding rows only.
					if ( isRepeatingTotal && ! isRepeatingWithTotal( this, totalField[0]) ) {
						return;
					}

					if ( this.hasAttribute( 'data-frmhigherpg' ) || isProductFieldHidden( this ) ) {
						return;
					}

					if ( this.tagName === 'SELECT' ) {
						if ( this.selectedIndex !== -1 ) {
							price = this.options[ this.selectedIndex ].getAttribute( 'data-frmprice' );
						}
					} else {
						isUserDef = 'text' === this.type;
						isSingle = 'hidden' === this.type;
						$this = jQuery( this );
						if ( ( ! isUserDef && ! isSingle ) && ! $this.is( ':checked' ) ) {
							return;
						}
						price = this.getAttribute( 'data-frmprice' );
					}

					if ( ! price ) {
						price = 0;
					} else {
						price = preparePrice( price, currency );
						quantity = getQuantity( isUserDef, this );
						price = parseFloat( quantity ) * parseFloat( price );
					}

					total += price;
				});

				if ( ! isRepeatingTotal ) {
					formTotals[ formId ] = total;
				}
			}

			total = isNaN( total ) ? 0 : total;

			// Set a decimal separator for currency if no default for it
			currency.decimal_separator = currency.decimal_separator.trim(); // first remove unnecessary space(s)
			if ( ! currency.decimal_separator.length ) {
				currency.decimal_separator = '.';
			}

			total = normalizeTotal( total, currency );
			totalField.val( total );

			// because of e.g. fields that might be using this field for calculations
			triggerChange( totalField );

			total = formatCurrency( total, currency );
			formatted = totalField.prev( '.frm_total_formatted' );
			if ( formatted.length < 1 ) {
				// In case paragraphs have been added to the form.
				formatted = totalField.closest( '.frm_form_field' ).find( '.frm_total_formatted' );
			}
			if ( formatted.length ) {
				formatted.html( total );
			}
		});
	}

	/**
	 * Round total and maybe add trailing zeros so formatCurrency has a proper format to work with.
	 */
	function normalizeTotal( total, currency ) {
		total = currency.decimals > 0 ? Math.round10( total, currency.decimals ) : Math.ceil( total );
		return maybeAddTrailingZeroToPrice( total, currency );
	}

	/**
	 * @since 4.05.01
	 */
	function formatCurrency( total, currency ) {
		var leftSymbol, rightSymbol;
		total = maybeAddTrailingZeroToPrice( total, currency );
		total = maybeRemoveTrailingZerosFromPrice( total, currency );
		total = addThousands( total, currency );
		leftSymbol = currency.symbol_left + currency.symbol_padding;
		rightSymbol = currency.symbol_padding + currency.symbol_right;
		return leftSymbol + total + rightSymbol;
	}

	/**
	 * @since 5.0.15
	 */
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

	/**
	 * @since 5.0.10
	 */
	function addRteRequiredMessages() {
		var keys, length, index, key, field;
		if ( 'undefined' === typeof __FRMRTEREQMESSAGES ) {
			return;
		}
		keys = Object.keys( __FRMRTEREQMESSAGES );
		length = keys.length;
		for ( index = 0; index < length; ++index ) {
			key = keys[ index ];
			field = document.getElementById( key );
			if ( field ) {
				field.setAttribute( 'data-reqmsg', __FRMRTEREQMESSAGES[ key ]);
			}
		}
	}

	/**
	 * @since 4.04.01
	 *
	 * This function is most suited for (product) fields that are
	 * on their own page i.e. not hidden (in a multi-paged form).
	 *
	 * As for fields that are conditionally hidden - themselves or
	 * their parent - and are not on their own page, the PHP side
	 * handles them well; either their HTML is not included on the
	 * page at all (e.g. fields in a conditionally hidden section)
	 * or their value is empty & price is thus 0, so no worries here.
	 */
	function isProductFieldHidden( input ) {
		return input.getAttribute( 'data-frmhidden' ) !== null;
	}

	/**
	 * @since 4.04
	 */
	function isRepeatingWithTotal( input, total ) {
		// .+ is safer than d+ because field keys might be used at times.
		var regex = /item_meta\[.+?\]\[.+?\]/;

		return isRepeatingFieldByName( input.name ) && ( total.name.match( regex )[0] === input.name.match( regex )[0]);
	}

	/**
	 * @since 4.04
	 */
	function getCurrency( formId ) {
		if ( typeof __FRMCURR  !== 'undefined' && typeof __FRMCURR[ formId ] !== 'undefined' ) {
			return __FRMCURR[ formId ];
		}
	}

	/**
	 * @since 4.04
	 */
	function getQuantity( isUserDef, field ) {
		var quantity, quantityFields, isRepeating, fieldID,
			$this = jQuery( field );

		fieldID = frmFrontForm.getFieldId( field, false );
		if ( ! fieldID ) {
			return 0;
		}

		isRepeating = isRepeatingFieldByName( field.name );

		if ( isRepeating ) {
			// .+ is safer than d+ because field keys might be used at times.
			var match = field.name.match( /item_meta\[.+?\]\[.+?\]/ );
			if ( null === match ) {
				// very unlikely though
				return 0;
			}
			// we may need this in getQuantityFields
			$this.nameMatch = match[0];
		}

		quantity = getQuantityField( $this, fieldID, isRepeating );

		if ( quantity ) {
			quantity = checkQuantityFieldMinMax( quantity );
		} else {
			quantityFields = getQuantityFields( $this, isRepeating );
			if ( 1 === quantityFields.length && '' === quantityFields[0].getAttribute( 'data-frmproduct' ).trim() ) {
				quantity = checkQuantityFieldMinMax( quantityFields[0]);
			} else {
				// If there is no quantity field, assume 1.
				quantity = 1;
			}
		}

		if ( 0 === quantity && isUserDef ) {
			// only user-defined fields may not have attached quantity fields
			quantity = 1;
		}

		return quantity;
	}

	function getQuantityField( elementObj, fieldID, isRepeating ) {
		var quantity,
			quantityFields = elementObj.closest( 'form' ).find( '[data-frmproduct]' );

		fieldID = fieldID.toString();

		quantityFields.each( function() {
			var ids;

			if ( isRepeating && -1 === this.name.indexOf( elementObj.nameMatch ) ) {
				return true;
			}

			ids = JSON.parse( this.getAttribute( 'data-frmproduct' ).trim() );
			if ( '' === ids ) {
				return true;
			}

			// convert to array if necessary cos of existing fields that are already using single product fields
			ids = 'string' === typeof ids ? [ ids.toString() ] : ids;
			if ( ids.indexOf( fieldID ) > -1 ) {
				quantity = this;
				return false;
			}
		});

		return quantity;
	}

	/**
	 * @since 4.04
	 */
	function getQuantityFields( elementObj, isRepeating ) {
		var quantityFields;
		if ( isRepeating ) {
			quantityFields = elementObj.closest( 'form' ).find( '[name^="' + elementObj.nameMatch + '"]' + '[data-frmproduct]' );
		} else {
			// make sure the search is form-based (i.e. per form) cos there could be more than 1 form on the page
			// not([id*="-"]) means : not inside a repeater
			quantityFields = elementObj.closest( 'form' ).find( '[data-frmproduct]:not([id*="-"])' );
		}

		return quantityFields;
	}

	/**
	 * @since 4.04
	 */
	function preparePrice( price, currency ) {
		var matches;

		if ( ! price ) {
			return 0;
		}
		price = price + ''; // convert to string just to be sure

		matches = price.match( /[0-9,.]*\.?\,?[0-9]+/g );
		if ( null === matches ) {
			return 0;
		}

		price = matches.length ? matches[ matches.length - 1 ] : 0;
		if ( price ) {
			price = maybeUseDecimal( price, currency );
			price = price.replace( currency.thousand_separator, '' ).replace( currency.decimal_separator, '.' );
		}

		return price;
	}

	/**
	 * @since 4.04
	 */
	function maybeUseDecimal( amount, currency ) {
		var usedForDecimal, amountParts;
		if ( currency.thousand_separator == '.' ) {
			amountParts = amount.split( '.' );
			usedForDecimal = ( 2 == amountParts.length && 2 == amountParts[1].length );
			if ( usedForDecimal ) {
				amount = amount.replace( '.', currency.decimal_separator );
			}
		}
		return amount;
	}

	/**
	 * @since 4.04
	 */
	function maybeAddTrailingZeroToPrice( price, currency ) {
		if ( 'number' !== typeof price ) {
			return price;
		}

		price += ''; // first convert to string
		var pos = price.indexOf( '.' );

		if ( pos === -1 ) {
			price = price + '.00';
		} else if ( price.substring( pos + 1 ).length < 2 ) {
			price += '0';
		}

		return price.replace( '.', currency.decimal_separator );
	}

	/**
	 * @since 4.04.04
	 */
	function addThousands( total, currency ) {
		if ( currency.thousand_separator ) {
			total = total.toString().replace( /\B(?=(\d{3})+(?!\d))/g, currency.thousand_separator );
		}
		return total;
	}

	/**
	 * @since 5.1
	 */
	function setAutoHeightForTextArea() {
		document.querySelectorAll( '.frm-show-form textarea' ).forEach(
			function( element ) {
				var minHeight, callback;

				if ( typeof element.dataset.autoGrow === 'undefined' || element.getAttribute( 'frm-autogrow' ) ) {
					return;
				}

				minHeight = getElementHeight( element );
				element.style.overflowY = 'hidden';
				element.style.transition = 'none';

				callback = function() {
					adjustHeight( element, minHeight );
				};

				callback();
				element.addEventListener( 'input', callback );
				window.addEventListener( 'resize', callback );
				document.addEventListener( 'frmShowField', callback );
				element.setAttribute( 'frm-autogrow', 1 );
			}
		);
	}

	function getElementHeight( element ) {
		var clone, container, height;

		clone = element.cloneNode( true );
		clone.style.position = 'absolute';
		clone.style.left = '-10000px';
		clone.style.top = '-10000px';

		container = jQuery( element ).closest( '.frm_forms' ).get( 0 );
		container.appendChild( clone );

		height = clone.clientHeight;

		container.removeChild( clone );

		return height;
	}

	/**
	 * @since 5.1
	 */
	function adjustHeight( el, minHeight ) {
		if ( minHeight >=  el.scrollHeight ) {
			return;
		}

		el.style.height = 0;
		el.style.height = Math.max( minHeight, el.scrollHeight ) + 'px';
	}

	function updateContentLength() {
		function onChange( e ) {
			var length, max, type,
				messageEl = e.target.nextElementSibling,
				countEl = messageEl.querySelector( 'span' );

			if ( ! countEl ) {
				return;
			}

			type = messageEl.getAttribute( 'data-max-type' );
			max  = parseInt( messageEl.getAttribute( 'data-max' ) );
			if ( 'word' === type ) {
				length = e.target.value.split( /\s+/ ).filter(
					function( word ) {
						return word;
					}
				).length;
			} else {
				length = e.target.value.length;
			}

			countEl.innerText = length;

			messageEl.classList.toggle( 'frm_limit_error', length > max );
		}

		document.addEventListener( 'input', function( e ) {
			var target;
			// loop parent nodes from the target to the delegation node
			for ( target = e.target; target && target != this; target = target.parentNode ) {
				if ( target.matches( 'textarea' ) && target.nextElementSibling && target.nextElementSibling.matches( '.frm_pro_max_limit_desc' ) ) {
					onChange( e );
					break;
				}
			}
		}, false );
	}

	/**
	 * Triggers custom event.
	 *
	 * @since 5.1
	 * @since 5.3.3 Added the third parameter.
	 *
	 * @param {HTMLElement} element   Element.
	 * @param {String}      eventType Event name.
	 * @param {Object}      data      Data to add to the event.
	 */
	function triggerEvent( element, eventType, data ) {
		var event;

		if ( typeof window.CustomEvent === 'function' ) {
			event = new CustomEvent( eventType );
		} else if ( document.createEvent ) {
			event = document.createEvent( 'HTMLEvents' );
			event.initEvent( eventType, false, true );
		} else {
			return;
		}

		event.frmData = data;

		element.dispatchEvent( event );
	}

	/**
	 * Handles start over button.
	 *
	 * @since 5.4
	 */
	function startOverButton() {
		/**
		 * Gets all field inputs.
		 *
		 * @param {HTMLElement} formEl Form element.
		 * @return {Array}
		 */
		function getInputs( formEl ) {
			return getInputsInFieldOnPage( formEl );
		}

		/**
		 * Resets field inputs.
		 *
		 * @param {HTMLElement} formEl Form element.
		 */
		function resetInputs( formEl ) {
			var inputs = getInputs( formEl );

			// Remove repeater sections.
			document.querySelectorAll( '.frm_repeat_sec:not(:first-of-type)' ).forEach( function( el ) {
				el.remove();
			});

			// Clear field value.
			clearValueForInputs( inputs, '', true );

			// Remove "disabled" attribute added by limit selections option.
			inputs.forEach( function( input ) {
				if ( input.disabled && input.getAttribute( 'data-frmlimit' ) ) {
					input.removeAttribute( 'disabled' );
				}
			});
		}

		/**
		 * Checks if given form is a multiple pages form.
		 *
		 * @param {Number} formId Form Id.
		 * @return {Boolean}
		 */
		function isMultiPagesForm( formId ) {
			return document.getElementById( 'frm_page_order_' + formId ) || document.querySelector( '#frm_form_' + formId + '_container input[name="frm_next_page"]' );
		}

		/**
		 * Reloads form.
		 *
		 * @param {Number}      formId Form ID.
		 * @param {HTMLElement} formEl Form element.
		 */
		function reloadForm( formId, formEl ) {
			formEl.classList.add( 'frm_loading_form' );
			postToAjaxUrl(
				formEl,
				{
					action: 'frm_load_form',
					form: formId,
					_ajax_nonce: frm_js.nonce
				},
				function( response ) {
					if ( ! response.success ) {
						console.log( response );
						return;
					}
					jQuery( formEl.closest( '.frm_forms' ) ).replaceWith( response.data );
					maybeShowMoreStepsButton();

					if ( 'undefined' !== typeof __frmAjaxDropzone ) {
						window.__frmDropzone = __frmAjaxDropzone;
					}

					checkConditionalLogic( 'pageLoad' );
					checkFieldsOnPage( 'frm_form_' + formId + '_container' );

					triggerCompletedEvent( formId );
				},
				function( response ) {
					console.log( response );
				}
			);
		}

		/**
		 * Checks if form has Save draft enabled.
		 *
		 * @param {HTMLElement} formEl Form element.
		 * @return {Boolean}
		 */
		function hasSaveDraft( formEl ) {
			return ! ! formEl.querySelector( '.frm_save_draft' );
		}

		/**
		 * Deletes draft version.
		 *
		 * @param {Number}      formId Form ID.
		 * @param {HTMLElement} formEl Form element.
		 */
		function deleteDraft( formId, formEl ) {
			postToAjaxUrl(
				formEl,
				{
					action: 'frm_delete_draft_entry',
					form: formId,
					_ajax_nonce: frm_js.nonce
				}
			);
		}

		function onClickStartOver( e ) {
			e.preventDefault();

			var formEl, formId, draftIdInput;
			formEl = e.target.closest( 'form' );
			if ( ! formEl ) {
				return;
			}

			formId = formEl.querySelector( 'input[name="form_id"]' ).value;

			if ( hasSaveDraft( formEl ) ) {
				deleteDraft( formId, formEl );

				// Clear draft inputs.
				draftIdInput = formEl.querySelector( 'input[name="id"]' );
				if ( draftIdInput ) {
					draftIdInput.remove();
				}

				formEl.querySelector( 'input[name="frm_action"]' ).value = 'create';
			}

			if ( isMultiPagesForm( formId ) ) {
				reloadForm( formId, formEl );
			} else {
				resetInputs( formEl );
				triggerCompletedEvent( formId );
			}
		}

		function triggerCompletedEvent( formId ) {
			triggerEvent( document, 'frm_after_start_over', { formId: formId });
		}

		document.addEventListener( 'click', function( e ) {
			var target;

			// loop parent nodes from the target to the delegation node.
			for ( target = e.target; target && target != this; target = target.parentNode ) {
				if ( target.matches( '.frm_start_over' ) ) {
					onClickStartOver.call( target, e );
					break;
				}
			}
		}, false );
	}

	function maybeShowMoreStepsButton() {
		var i, listWrappers, listWrapper, rootlineSteps,
			wrappingElementsCount, startIndex, hiddenSteps,
			showMoreButtonLi, showMoreButton, hiddenStepsWrapper,
			oldIE;

		listWrappers = document.getElementsByClassName( 'frm_rootline' );
		copyRootlines( listWrappers );
		oldIE = isOldIEVersion( 10 );

		for ( i = 0; i < listWrappers.length; i++ ) {
			listWrapper = listWrappers[ i ];
			if ( oldIE ) {
				// Hide the rootlines in < IE 11.
				listWrapper.className += ' frm_hidden';
				continue;
			}

			rootlineSteps = listWrapper.children;

			wrappingElementsCount = countOverflowPages( rootlineSteps );

			if ( ! wrappingElementsCount ) {
				continue;
			}

			showMoreButton = listWrapper.querySelector( '.frm_rootline_show_more_btn' );
			if ( ! showMoreButton ) {
				continue;
			}

			showMoreButton.addEventListener( 'click', showMoreSteps );

			// Show the existing "..." button.
			showMoreButtonLi = showMoreButton.parentNode;
			showMoreButtonLi.className = showMoreButtonLi.className.replace( ' frm_hidden', '' );

			// Don't count the overflow step in the HTML.
			startIndex  = rootlineSteps.length - wrappingElementsCount > 1 ? rootlineSteps.length - wrappingElementsCount - 3 : 0;
			hiddenSteps = [].slice.call( rootlineSteps, Math.max( startIndex, 1 ), rootlineSteps.length - 2 );

			hiddenStepsWrapper = showMoreButtonLi.querySelector( '.frm_rootline_hidden_steps' );

			hiddenSteps.forEach( function( hiddenStep ) {
				hiddenStepsWrapper.appendChild( hiddenStep );
			});

			moveRootlineTitles( hiddenStepsWrapper, listWrapper, showMoreButton );

			listWrapper.insertBefore( showMoreButtonLi, listWrapper.children[ listWrapper.children.length - 1 ]);

			if ( listWrapper.children[ listWrapper.children.length - 1 ].className.indexOf( 'frm_current_page' ) !== -1 ) {
				updateRootlineStyle( hiddenStepsWrapper );
			}
		}
	}

	/**
	 * Check if the browser is IE. If it matches the max or is lower, return true.
	 *
	 * @param int max
	 * @returns bool
	 */
	function isOldIEVersion( max ) {
		var version,
			myNav = navigator.userAgent.toLowerCase();
		version = myNav.indexOf( 'msie' ) !== -1 ? parseInt( myNav.split( 'msie' )[1]) : false;
		return version !== false && max >= version;
	}

	function countOverflowPages( rootlineSteps ) {
		var j,
			wrappingElementsCount = 0;

		for ( j = 0; j < rootlineSteps.length; j++ ) {
			if ( rootlineSteps[ j ].offsetTop !== rootlineSteps[0].offsetTop && rootlineSteps[ j ].className.indexOf( 'frm_rootline_show_hidden_steps_btn' ) === -1 ) {
				wrappingElementsCount++;
			}
		}
		return wrappingElementsCount;
	}

	function moveRootlineTitles( hiddenStepsWrapper, listWrapper, showMoreButton ) {
		var currentPageTitle, currentStepTitle, rootlineGroup,
			activeHiddenStepLink = hiddenStepsWrapper.querySelector( 'input:not(.frm_page_back):not(.frm_page_skip)' );

		if ( activeHiddenStepLink ) {
			currentPageTitle = activeHiddenStepLink.parentElement.querySelector( '.frm_rootline_title' );

			maybeUpdateRootlineTitles( activeHiddenStepLink.parentElement.previousElementSibling, hiddenStepsWrapper );

			showMoreButton.parentElement.className += ' active';
			showMoreButton.className += ' active';
		} else {
			currentPageTitle = listWrapper.querySelector( '.frm_current_page' ).querySelector( '.frm_rootline_title' );
		}

		if ( ! currentPageTitle ) {
			return;
		}

		currentStepTitle = currentPageTitle.textContent;
		if ( ! currentStepTitle ) {
			return;
		}

		// Add current step on top of rootline bar.
		rootlineGroup = listWrapper.closest( '.frm_rootline_group' );

		showCurrentHiddenStepText( currentStepTitle );
	}

	function showCurrentHiddenStepText( currentStepTitle ) {
		var rootlineCurrentStep = document.createElement( 'span' );
		rootlineCurrentStep.className = 'frm_rootline_title';
		rootlineCurrentStep.textContent = currentStepTitle;
		document.querySelector( '.frm_rootline_show_hidden_steps_btn' ).appendChild( rootlineCurrentStep );
	}

	function copyRootlines( listWrappers ) {
		var i, listWrappers, listWrapper, rootlinesBackup;
		rootlinesBackup = {};
		for ( i = 0; i < listWrappers.length; i++ ) {
			listWrapper = listWrappers[ i ];
			rootlinesBackup[ listWrapper.closest( 'form' ).getAttribute( 'id' ) ] = listWrapper.cloneNode( true );
		}
		listWrappersOriginal = rootlinesBackup;
	}

	function maybeUpdateRootlineTitles( previousPageLink, hiddenStepsWrapper ) {
		var i;
		if ( previousPageLink ) {
			i = 0;
			while ( previousPageLink ) {
				i++;
				previousPageLink = previousPageLink.previousElementSibling;
			}
			updateRootlineStyle( hiddenStepsWrapper, i );
		}
	}

	function updateRootlineStyle( hiddenStepsWrapper, uptoIndex ) {
		var rootlineTitles, rootlineTitle;

		if ( ! uptoIndex ) {
			uptoIndex = hiddenStepsWrapper.children.length;
		}
		rootlineTitles = [].slice.call( hiddenStepsWrapper.children, 0, uptoIndex );
		rootlineTitles.forEach( function( el ) {
			rootlineTitle = el.querySelector( '.frm_rootline_title' );
			if ( rootlineTitle ) {
				rootlineTitle.className += ' frm_prev_page_title';
			}
		});
	}

	function showMoreSteps( e ) {
		var hiddenStepsWrapper = e.target.parentElement.querySelector( 'ul' );
		if ( hiddenStepsWrapper.className.indexOf( 'frm_hidden' ) > -1 ) {
			hiddenStepsWrapper.className = hiddenStepsWrapper.className.replace( ' frm_hidden', '' );
		} else {
			hiddenStepsWrapper.className += ' frm_hidden';
		}
	}

	/**
	 * Maybe add polyfills.
	 *
	 * @since 5.4
	 */
	function maybeAddPolyfills() {
		if ( ! Element.prototype.matches ) {
			// IE9 supports matches but as msMatchesSelector instead.
			Element.prototype.matches = Element.prototype.msMatchesSelector;
		}

		if ( ! Element.prototype.closest ) {
			Element.prototype.closest = function( s ) {
				var el = this;

				do {
					if ( el.matches( s ) ) {
						return el;
					}
					el = el.parentElement || el.parentNode;
				} while ( el !== null && el.nodeType === 1 );

				return null;
			};
		}

		// Element.remove().
		( function( arr ) {
			arr.forEach( function( item ) {
				if ( item.hasOwnProperty( 'remove' ) ) {
					return;
				}
				Object.defineProperty( item, 'remove', {
					configurable: true,
					enumerable: true,
					writable: true,
					value: function remove() {
						this.parentNode.removeChild( this );
					}
				});
			});
		}([ Element.prototype, CharacterData.prototype, DocumentType.prototype ]) );

		// NodeList.forEach().
		if ( window.NodeList && ! NodeList.prototype.forEach ) {
			NodeList.prototype.forEach = function( callback, thisArg ) {
				thisArg = thisArg || window;
				for ( var i = 0; i < this.length; i++ ) {
					callback.call( thisArg, this[ i ], i, this );
				}
			};
		}
	}

	function validateFieldValue() {
		document.addEventListener( 'frm_validate_field_value', function( event ) {
			if ( 'object' !== typeof event.frmData.field || 'object' !== typeof event.frmData.errors ) {
				return;
			}

			if ( 'password' === event.frmData.field.type ) {
				validatePasswordStrength( event.frmData.field, event.frmData.errors );
			}
		});
	}

	function validatePasswordStrength( field, errors ) {
		var check, regex, checks;

		if ( 'object' !== typeof window.frm_password_checks ) {
			return;
		}

		if ( -1 === field.className.indexOf( 'frm_strong_pass' ) || 0 === field.id.indexOf( 'field_conf_' ) ) {
			return;
		}

		checks = window.frm_password_checks;

		for ( check in checks ) {
			regex = checks[ check ].regex.slice( 1, checks[ check ].regex.length - 1 );
			regex = new RegExp( regex );
			if ( ! regex.test( field.value ) ) {
				errors[ frmFrontForm.getFieldId( field ) ] = checks[ check ].message;
				return;
			}
		}
	}

	function maybeTriggerCalc( event ) {
		if ( event.persisted || ( window.performance && window.performance.getEntriesByType( 'navigation' )[0].type === 'back_forward' ) ) {
			triggerCalc();
		}
	}

	function showMoreStepsButtonEvents() {
		var timeout;

		window.addEventListener( 'resize', function() {
			var i, listWrappers, listWrapper, form;
			listWrappers = document.getElementsByClassName( 'frm_rootline' );
			for ( i = 0; i < listWrappers.length; i++ ) {
				listWrapper = listWrappers[ i ];
				form = listWrapper.closest( 'form' );
				form.querySelector( '.frm_rootline_group' ).replaceChild( listWrappersOriginal[ form.getAttribute( 'id' ) ], listWrapper );
			}

			clearTimeout( timeout );
			timeout = setTimeout( maybeShowMoreStepsButton(), 100 );
		});
	}

	function handleShowPasswordBtn() {
		documentOn( 'click', '.frm_show_password_btn', function( event ) {
			var input = event.target.closest( '.frm_show_password_wrapper' ).querySelector( 'input' ),
				button = input.nextElementSibling;

			if ( 'password' === input.type ) {
				input.type = 'text';
				button.setAttribute( 'data-show-password-label', button.title );
				button.title = button.getAttribute( 'data-hide-password-label' );
			} else {
				input.type = 'password';
				button.title = button.getAttribute( 'data-show-password-label' );
			}

			button.setAttribute( 'aria-label', button.title );
		});
	}

	/**
	 * Does the same as jQuery( document ).on( 'event', 'selector', handler ).
	 *
	 * @since 6.3.1
	 *
	 * @param {String}         event    Event name.
	 * @param {String}         selector Selector.
	 * @param {Function}       handler  Handler.
	 * @param {Boolean|Object} options  Options to be added to `addEventListener()` method. Default is `false`.
	 */
	function documentOn( event, selector, handler, options ) {
		if ( 'undefined' === typeof options ) {
			options = false;
		}

		document.addEventListener( event, function( e ) {
			var target;

			// loop parent nodes from the target to the delegation node.
			for ( target = e.target; target && target != this; target = target.parentNode ) {
				if ( target && target.matches && target.matches( selector ) ) {
					handler.call( target, e );
					break;
				}
			}
		}, options );
	}

	/**
	 * @since 6.5.1
	 */
	function handleElementorPopupConflicts() {
		var elementorPopupWrapper = document.querySelector( '.elementor-popup-modal' );

		if ( null !== elementorPopupWrapper ) {
			// Make dropzone items clickable.
			elementorPopupWrapper.querySelectorAll( '.frm_dropzone' ).forEach( function( item ) {
				item.classList.remove( 'dz-clickable' );
			});

			// Remove field chosen containers.
			elementorPopupWrapper.querySelectorAll( '.frm_form_field .chosen-container' ).forEach( function( chosenContainer ) {
				chosenContainer.remove();
			});
		}

		loadDropzones();
		loadChosen();
	}

	return {
		init: function() {
			maybeAddPolyfills();

			addEventListener( 'pageshow', maybeTriggerCalc );

			jQuery( document ).on( 'frmFormComplete', afterFormSubmitted );
			jQuery( document ).on( 'frmPageChanged', afterPageChanged );
			jQuery( document ).on( 'frmAfterAddRow frmAfterRemoveRow', calcProductsTotal );

			jQuery( document ).on( 'click', '.frm_trigger', toggleSection );
			jQuery( document ).on( 'keydown', '.frm_trigger', toggleSection );

			var $blankField = jQuery( '.frm_blank_field' );
			if ( $blankField.length ) {
				$blankField.closest( '.frm_toggle_container' ).prev( '.frm_trigger' ).trigger( 'click' );
			}

			jQuery( document ).on( 'click', '.frm_remove_link', removeFile );

			jQuery( document ).on( 'focusin', 'input[data-frmmask]', function() {
				jQuery( this ).mask( jQuery( this ).data( 'frmmask' ).toString(), { autoclear: false });
			});

			jQuery( document ).on( 'frmFieldChanged', maybeCheckDependent );

			jQuery( document ).on( 'keyup', 'input.frm_strength_meter', checkPasswordField );
			jQuery( document ).on( 'keydown', '.frm_switch', triggerToggleClickOnSpace );

			jQuery( document ).on( 'mouseenter click', '.frm-star-group input', loadStars );
			jQuery( document ).on( 'mouseenter', '.frm-star-group .star-rating:not(.star-rating-readonly)', hoverStars );
			jQuery( document ).on( 'mouseleave', '.frm-star-group .star-rating:not(.star-rating-readonly)', unhoverStars );

			jQuery( document ).on(
				'click',
				'.frm-show-form input[type="submit"], .frm-show-form input[name="frm_prev_page"], .frm_page_back, .frm_page_skip, .frm-show-form .frm_save_draft, .frm_prev_page, .frm_button_submit, .frm_rootline_show_hidden_steps_btn .frm_rootline_single',
				setNextPage
			);

			jQuery( document ).on( 'change', '.frm_other_container input[type="checkbox"], .frm_other_container input[type="radio"], .frm_other_container select', showOtherText );
			jQuery( document ).on( 'change', '.frm_switch_block input[type="checkbox"]', setToggleAriaChecked );

			jQuery( document ).on( 'click', '.frm_remove_form_row', removeRow );
			jQuery( document ).on( 'click', '.frm_add_form_row', addRow );

			// In place edit
			jQuery( '.frm_edit_link_container' ).on( 'click', 'a.frm_inplace_edit', editEntry );
			jQuery( '.frm_edit_link_container' ).on( 'click', 'a.frm_cancel_edit', cancelEdit );
			jQuery( document ).on( 'click', '.frm_ajax_delete', deleteEntry );

			// toggle collapsible entries shortcode
			jQuery( '.frm_month_heading, .frm_year_heading' ).on( 'click', function() {
				var content = jQuery( this ).children( '.ui-icon-triangle-1-e, .ui-icon-triangle-1-s' );
				if ( content.hasClass( 'ui-icon-triangle-1-e' ) ) {
					content.addClass( 'ui-icon-triangle-1-s' ).removeClass( 'ui-icon-triangle-1-e' );
					jQuery( this ).next( '.frm_toggle_container' ).fadeIn( 'slow' );
				} else {
					content.addClass( 'ui-icon-triangle-1-e' ).removeClass( 'ui-icon-triangle-1-s' );
					jQuery( this ).next( '.frm_toggle_container' ).hide();
				}
			});

			// Elementor popup show event. Fix Elementor Popup && FF PRO fields( file upload | dynamic field ) conflicts
			jQuery( document ).on( 'elementor/popup/show', handleElementorPopupConflicts );

			addTopAddRowBtnForRepeater();

			// Checkbox Selection Limit
			jQuery( document ).on( 'click', 'input[type="checkbox"][data-frmlimit]', checkCheckboxSelectionLimit );

			// Total field calc
			jQuery( document ).on( 'change', '[type="checkbox"][data-frmprice],[type="radio"][data-frmprice],[type="hidden"][data-frmprice],select:has([data-frmprice])', calcProductsTotal );
			jQuery( document ).on( 'keyup change', '[data-frmproduct],[type="text"][data-frmprice]', calcProductsTotal );

			jQuery( document ).on( 'frmFormComplete frmPageChanged frmInPlaceEdit frmAfterAddRow', setAutoHeightForTextArea );

			maybeDisableCheckboxesWithLimit();

			setInlineFormWidth();
			checkConditionalLogic( 'pageLoad' );
			checkFieldsOnPage();
			processPendingAjax();

			addRteRequiredMessages();
			setAutoHeightForTextArea();
			updateContentLength();

			// make sure this comes last, particularly after checkConditionalLogic & checkFieldsOnPage
			calcProductsTotal();

			startOverButton();

			maybeShowMoreStepsButton();

			showMoreStepsButtonEvents();

			validateFieldValue();

			handleShowPasswordBtn();
		},

		savingDraft: function( object ) {
			return savingDraftEntry( object );
		},

		goingToPreviousPage: function( object ) {
			return goingToPrevPage( object );
		},

		hideOrShowFields: function( ids, event ) {
			if ( 'pageLoad' === event ) {
				clearHideFields();
			}
			var len = ids.length,
				repeatArgs = { repeatingSection: '', repeatRow: '' };
			for ( var i = 0, l = len; i < l; i++ ) {
				hideOrShowFieldById( ids[ i ], repeatArgs );
				if ( i == ( l - 1 ) ) {
					showForm();
				}
			}
		},

		hidePreviouslyHiddenFields: function() {
			var hiddenFields = getAllHiddenFields(),
				len = hiddenFields.length;
			for ( var i = 0, l = len; i < l; i++ ) {
				var container = document.getElementById( hiddenFields[ i ]);

				//check for submit button
				if ( container == null ) {
					container = document.querySelector( '#' + hiddenFields[ i ]);
					if ( container != null && hiddenFields[ i ].indexOf( 'frm_final_submit' ) > -1 ) {
						hidePreviouslyHiddenSubmitButton( hiddenFields[ i ]);
						continue;
					}
				}

				if ( container !== null ) {
					container.style.display = 'none';
					setHiddenProductContainer( container );
				}
			}
		},

		submitAllowed: function( object ) {
			var formElementId = object.getAttribute( 'id' );

			if ( ! isSubmitButtonOnPage( formElementId + ' .frm_final_submit' ) || goingToPrevPage( object ) || savingDraftEntry( object ) ) {
				return true;
			}

			var formKey = getFormKeyFromFormElementID( formElementId );

			return ! isOnPageSubmitButtonHidden( formKey );
		},

		checkDependentDynamicFields: function( ids ) {
			var len = ids.length,
				repeatArgs = { repeatingSection: '', repeatRow: '' };
			for ( var i = 0, l = len; i < l; i++ ) {
				hideOrShowFieldById( ids[ i ], repeatArgs );
			}
		},

		checkDependentLookupFields: function( ids ) {
			var fieldId,
				repeatArgs = { repeatingSection: '', repeatRow: '' };
			for ( var i = 0, l = ids.length; i < l; i++ ) {
				fieldId = ids[ i ];
				updateWatchingFieldById( fieldId, repeatArgs, 'value changed' );
			}
		},

		loadGoogle: function() {
			var graphs, packages, i;

			if ( typeof google === 'undefined' || ! google || ! google.load ) {
				setTimeout( frmProForm.loadGoogle, 30 );
				return;
			}

			graphs = __FRMTABLES;
			packages = Object.keys( graphs );

			for ( i = 0; i < packages.length; i++ ) {
				if ( packages[ i ] === 'graphs' ) {
					generateGoogleGraphs( graphs[ packages[ i ] ]);
				} else {
					generateGoogleTables( graphs[ packages[ i ] ], packages[ i ]);
				}
			}
		},

		removeUsedTimes: function( obj, timeField ) {
			var $form, form, e, data, success, extraParams;

			$form = jQuery( obj ).parents( 'form' ).first();
			form = $form.get( 0 );
			e = $form.find( 'input[name="id"]' );

			data = {
				action: 'frm_fields_ajax_time_options',
				time_field: timeField,
				date_field: obj.id,
				entry_id: ( e ? e.val() : '' ), date: jQuery( obj ).val(),
				nonce: frm_js.nonce
			};

			success = function( opts ) {
				var $timeField = jQuery( document.getElementById( timeField ) );
				$timeField.find( 'option' ).prop( 'disabled', false );
				if ( opts.length > 0 ) {
					for ( var i = 0, l = opts.length; i < l; i++ ) {
						$timeField.get( 0 ).querySelectorAll( 'option[value="' + opts[ i ] + '"]' ).forEach(
							function( option ) {
								option.disabled = true;
								if ( option.selected ) {
									option.selected = false;
								}
							}
						);
					}
				}
			};

			extraParams = { dataType: 'json' };

			postToAjaxUrl( form, data, success, false, extraParams );
		},

		changeRte: function( editor ) {
			editor.on( 'change', function() {
				var content = editor.getBody().innerHTML;
				jQuery( '#' + editor.id ).val( content ).trigger( 'change' );
			});
		},

		addFormidableClassToDatepicker: function( _, options ) {
			if ( options.dpDiv ) {
				options.dpDiv.addClass( 'frm-datepicker' );
				options.dpDiv.get( 0 ).setAttribute( 'is-formidable-datepicker', 1 );
			}
			return options;
		},

		removeFormidableClassFromDatepicker: function( _, options ) {
			var dpDiv;
			if ( options.dpDiv ) {
				dpDiv = options.dpDiv.get( 0 );
				dpDiv.removeAttribute( 'is-formidable-datepicker' );

				// Delay it slightly so it doesn't use styling while it's fading out.
				setTimeout(
					function() {
						if ( dpDiv.hasAttribute( 'is-formidable-datepicker' ) ) {
							// Avoid removing it the class if the datepicker was immediately opened again.
							return;
						}
						jQuery( dpDiv ).removeClass( 'frm-datepicker' );
					},
					400
				);
			}
		}
	};
}
var frmProForm = frmProFormJS();

jQuery( document ).ready( function() {
	frmProForm.init();
});

( function() {
	if ( ! Math.round10 ) {
		// https://www.jacklmoore.com/notes/rounding-in-javascript/
		Math.round10 = function( value, decimals ) {
			return Number( Math.round( value + 'e' + decimals ) + 'e-' + decimals );
		};
	}
}() );
