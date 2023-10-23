/**
 * This file is loaded on the entries page.
 */
( function() {
	/** globals frmDom, wp */

	if ( 'undefined' === typeof frmDom || 'undefined' === typeof wp ) {
		return;
	}

	addFilter( 'frm_on_multiple_entries_delete', confirmDeleteAllEntriesModal );

	function confirmDeleteAllEntriesModal( args ) {

		const self = this;
		const { link, initModal } = args;

		this.modal = initModal( '#frm_confirm_modal', '500px' );

		this.wrapper           = document.getElementById( 'frm_confirm_modal' );
		this.confirmButton     = document.getElementById( 'frm-confirmed-click' );

		this.confirmationInput = self.wrapper.querySelector( '.frm-delete-confirmation-input' );

		this.timeoutInterval;

		this.modalOptions = {
			heading: 'Delete all %entriesCount% entries?',
			headingSingleEntry: 'Delete the entry?',
			copy: '',
			inputPlaceholder: 'Type in "DELETE ALL" to delete all entries',
			entriesCount: 0
		};

		this.deleteAllFormEntries = false;

		this.countEntries = function() {
			if ( null !== link.getAttribute( 'data-total-entries' ) ) {
				self.modalOptions.entriesCount = parseInt( link.getAttribute( 'data-total-entries' ), 10 );
				self.deleteAllFormEntries = true;
				return self.modalOptions.entriesCount;
			}

			self.modalOptions.entriesCount = 0;
			document.querySelectorAll( 'input[name="item-action[]"]' ).forEach( function( checkbox ) {
				if ( checkbox.checked ) {
					self.modalOptions.entriesCount++;
				}
			});
			return self.modalOptions.entriesCount;
		};

		this.getHeading = function() {
			if ( 1 === self.modalOptions.entriesCount ) {
				return self.modalOptions.headingSingleEntry;
			}
			return self.modalOptions.heading.replace( '%entriesCount%', self.modalOptions.entriesCount );
		};

		this.getCopy = function() {
			let copy;
			if ( ! link.getAttribute( 'data-frmverify' ) ) {
				return self.modalOptions.copy;
			}
			copy = frmDom.tag( 'span' );
			copy.innerHTML = link.getAttribute( 'data-frmverify' );

			if ( false === self.deleteAllFormEntries ) {
				if ( 1 === self.modalOptions.entriesCount ) {
					copy.innerHTML = link.getAttribute( 'data-frmverify' ).replace( 'ALL entries', 'The selected entry' );
				} else {
					copy.innerHTML = link.getAttribute( 'data-frmverify' ).replace( 'ALL entries', 'The selected entries' );
				}
			}

			return self.modalOptions.copy = copy;
		};

		this.getConfirmationInput = function() {
			self.confirmationInput = frmDom.tag( 'input', { className: 'frm-delete-confirmation-input' });
			self.confirmationInput.setAttribute( 'type', 'text' );
			return self.confirmationInput;
		};

		this.initConfirmButton = function( active ) {
			self.confirmButton.classList.add( link.getAttribute( 'data-frmverify-btn' ) );
			if ( true === active ) {
				self.confirmButton.classList.remove( 'frm-btn-inactive' );
				self.confirmButton.classList.add( 'dismiss' );
				self.confirmButton.setAttribute( 'href', link.getAttribute( 'href' ) );
				return;
			}
			self.confirmButton.setAttribute( 'href', '#' );
			self.confirmButton.classList.add( 'frm-btn-inactive' );
			self.confirmButton.classList.remove( 'dismiss' );
		};

		this.initConfirmationInput = function() {
			self.confirmationInput.placeholder = self.modalOptions.inputPlaceholder;
			self.confirmationInput.addEventListener( 'keydown', function() {
				clearTimeout( self.timeoutInterval );
				self.timeoutInterval = setTimeout( self.confirmationCheck, 100 );
			});
		};

		this.confirmationCheck = function() {
			if ( 'delete all' === self.confirmationInput.value.toLowerCase().trim() ) {
				self.initConfirmButton( true );
				return;
			}
			self.initConfirmButton( false );
		};

		this.initModal = function() {
			let copyWrapper;
			if ( null === self.wrapper || null === self.wrapper.querySelector( '.frm-confirm-msg' ) ) {
				return;
			}
			copyWrapper           = self.wrapper.querySelector( '.frm-confirm-msg' );
			copyWrapper.classList.add( 'frm-delete-all-entries-modal-confirmation' );
			copyWrapper.innerHTML = '';
			copyWrapper.append( frmDom.tag( 'h2', self.getHeading() ) );
			copyWrapper.append( self.getCopy() );
			copyWrapper.append( self.getConfirmationInput() );

			self.initConfirmationInput();
			self.initConfirmButton( false );

		};

		this.openModal = function() {
			if ( false === self.modal || 0 === self.countEntries() ) {
				return false;
			}
			self.initModal();
			self.modal.dialog( 'open' );
		};

		return this.openModal();
	}

	function addFilter( hookName, callback ) {
		wp.hooks.addFilter( hookName, 'formidable', callback );
	}

}() );