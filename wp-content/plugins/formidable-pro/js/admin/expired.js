( function() {
	/** globals wp, frmDom */

	if ( 'undefined' === typeof frmDom || 'undefined' === typeof wp ) {
		return;
	}

	const __ = wp.i18n.__;
	const { tag, div, a, img } = frmDom;
	const { maybeCreateModal, footerButton } = frmDom.modal;

	wp.hooks.addAction(
		'frm_show_expired_modal',
		'formidable',
		function() {
			const modal = maybeCreateModal(
				'frm_expired_modal',
				{
					title: __( 'You don\'t have access to do that', 'formidable-pro' ),
					content: getExpiredModalContent()
				}
			);
			modal.classList.add( 'frm_common_modal' );

			const footer = modal.querySelector( '.frm_modal_footer' );
			if ( footer ) {
				footer.remove();
			}
		}
	);

	function getExpiredModalContent() {
		return div({
			className: 'frmcenter inside',
			children: [
				img({ src: getProPluginUrl() + '/images/expired.svg' }),
				tag( 'h3', __( 'Your account license has expired', 'formidable-pro' ) ),
				div( __( 'In order to access Pro features, please renew your subscription.', 'formidable-pro' ) ),
				tag( 'br' ),
				div({
					child: footerButton({
						text: __( 'Renew', 'formidable-pro' ),
						buttonType: 'primary',
						href: 'https://formidableforms.com/lite-upgrade/?utm_source=WordPress&utm_medium=expired_modal&utm_campaign=liteplugin&utm_content=renew',
						noDismiss: true
					})
				}),
				tag( 'br' ),
				div({
					child: a({
						className: 'dismiss',
						text: __( 'Not Now', 'formidable-pro' )
					})
				})
			]
		});
	}

	function getProPluginUrl() {
		var freePluginUrlSplitBySlashes = frmGlobal.url.split( '/' );
		freePluginUrlSplitBySlashes.pop();
		freePluginUrlSplitBySlashes.push( 'formidable-pro' );
		return freePluginUrlSplitBySlashes.join( '/' );
	}

}() );
