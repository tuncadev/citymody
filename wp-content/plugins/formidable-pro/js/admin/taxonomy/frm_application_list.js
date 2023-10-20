/**
 * This file is loaded on the list of custom applications page.
 */
( function() {
	/* globals frmDom, wp */

	if ( 'undefined' === typeof frmDom || 'undefined' === typeof wp ) {
		return;
	}

	const __ = wp.i18n.__;
	const { div, tag, span, a } = frmDom;
	const { doJsonFetch } = frmDom.ajax;
	const { newSearchInput } = frmDom.search;
	const { onClickPreventDefault } = frmDom.util;
	const container = document.getElementById( 'frm-custom-applications-index-container' );

	if ( ! container ) {
		return;
	}

	const elements = {
		noApplicationSearchResultsPlaceholder: false
	};

	const table = tag(
		'table',
		{ className: 'wp-list-table widefat fixed striped toplevel_page_formidable' }
	);

	doJsonFetch( 'get_applications_data&view=applications' ).then(
		data => {
			if ( ! data.applications.length ) {
				const noApplicationsPlaceholder = getNoApplicationsPlaceholder();
				if ( false !== noApplicationsPlaceholder ) {
					container.appendChild( noApplicationsPlaceholder );
				}
				return;
			}

			container.appendChild( table );
			addDataToTable( data );
		}
	);

	function getNoApplicationsPlaceholder() {
		const hookName = 'frm_no_applications_placeholder';
		return wp.hooks.applyFilters( hookName, false );
	}

	const topBar = document.getElementById( 'frm_top_bar' );
	if ( topBar ) {
		const wrapper = topBar.nextElementSibling;
		wrapper.insertBefore(
			div({
				id: 'frm_applications_search_wrapper',
				child: getSearch()
			}),
			wrapper.firstChild
		);
	}

	function getSearch() {
		const id = 'frm_applications_search_input';
		const placeholder = __( 'Search applications', 'formidable-pro' );
		const targetClassName = 'frm-application-row';
		const args = { handleSearchResult: handleApplicationSearch };
		return newSearchInput( id, placeholder, targetClassName, args );
	}

	function handleApplicationSearch({ foundSomething, notEmptySearchText }) {
		if ( false === elements.noApplicationSearchResultsPlaceholder ) {
			elements.noApplicationSearchResultsPlaceholder = getNoResultsPlaceholder();
			document.getElementById( 'frm-custom-applications-index-container' ).appendChild( elements.noApplicationSearchResultsPlaceholder );
		}

		const showTable = ! notEmptySearchText || foundSomething;
		table.classList.toggle( 'frm_hidden', ! showTable );

		const showNoResults = notEmptySearchText && ! foundSomething;
		elements.noApplicationSearchResultsPlaceholder.classList.toggle( 'frm_hidden', ! showNoResults );
	}

	function getNoResultsPlaceholder() {
		const placeholder = div( __( 'No applications match your search query.', 'formidable-pro' ) );
		placeholder.style.margin = '20px';
		return placeholder;
	}

	function addDataToTable( data ) {
		const tbody = tag( 'tbody' );
		addHeadersToTable();
		data.applications.forEach(
			rowData => tbody.appendChild( getTableRow( rowData ) )
		);
		table.appendChild( tbody );
	}

	function addHeadersToTable() {
		const headers = [
			__( 'Application Name', 'formidable-pro' ),
			__( 'Forms', 'formidable-pro' ),
			__( 'Views', 'formidable-pro' ),
			__( 'Pages', 'formidable-pro' ),
			__( 'Date Created', 'formidable-pro' ),
			__( 'Date Modified', 'formidable-pro' )
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

	function getHeaderElement( text ) {
		const className = 'manage-column';
		return th({ text, className });
	}

	function getTableRow( rowData ) {
		const anchor = a({
			className: 'row-title',
			text: rowData.name,
			href: rowData.editUrl
		});
		const row = tr({
			id: 'frm-application-row-' + rowData.termId,
			className: 'frm-application-row',
			children: [
				td({ children: [ tag( 'b', { child: anchor }), getRowActions( rowData ) ] }),
				td( rowData.formCount ),
				td( rowData.viewCount ),
				td( rowData.pageCount ),
				td( formatTimestamp( rowData.createdAt ) ),
				td( formatTimestamp( rowData.updatedAt ) )
			]
		});
		row.setAttribute( 'frm-search-text', rowData.name.toLowerCase() );
		return row;
	}

	function tr( args ) {
		return tag( 'tr', args );
	}

	function th( args ) {
		return tag( 'th', args );
	}

	function td( args ) {
		return tag( 'td', args );
	}

	function formatTimestamp( updatedAt ) {
		const date   = new Date( updatedAt * 1000 );
		const year   = date.getUTCFullYear();
		const month  = date.getUTCMonth() + 1;
		const day    = date.getUTCDate();
		const dateDescription = year + '/' + lpad( month ) + '/' + lpad( day );
		return dateDescription;
	}

	function lpad( value ) {
		return ( '00' + value ).slice( -2 );
	}

	function getRowActions( rowData ) {
		const editAnchor = a({
			text: __( 'Edit', 'formidable-pro' ),
			href: rowData.editUrl
		});

		const deleteAnchor = a( __( 'Delete', 'formidable-pro' ) );
		onClickPreventDefault( deleteAnchor, () => deleteApplication( rowData.termId ) );

		return div({
			className: 'row-actions',
			children: [
				span({
					className: 'edit',
					child: editAnchor
				}),
				document.createTextNode( ' | ' ),
				span({
					className: 'trash',
					child: deleteAnchor
				})
			]
		});
	}

	function deleteApplication( termId ) {
		const hookName = 'frm_trigger_delete_application_modal';
		wp.hooks.doAction( hookName, termId, () => fadeAfterDelete( termId ) );
	}

	function fadeAfterDelete( termId ) {
		const row = document.getElementById( 'frm-application-row-' + termId );

		if ( ! row ) {
			return;
		}

		jQuery( row ).fadeOut( 'slow', removeRowAfterFade );
		function removeRowAfterFade() {
			row.remove();
			const tbody = table.querySelector( 'tbody' );
			if ( ! tbody.children.length ) {
				const noApplicationsPlaceholder = getNoApplicationsPlaceholder();
				if ( false !== noApplicationsPlaceholder ) {
					container.appendChild( getNoApplicationsPlaceholder() );
				}
				table.remove();
			}
		}
	}
}() );
