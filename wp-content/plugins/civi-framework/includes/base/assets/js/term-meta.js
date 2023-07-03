(function($) {
	"use strict";

	//clear form after submit
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		try{
			var $respo = $.parseXML(xhr.responseText);
			//exit on error
			if ($($respo).find('wp_error').length) return;
			if ($($respo).find('.glf-term-meta-item-wrapper').length) {
				return;
			}

			var $taxWrappe = $('.glf-term-meta-wrapper'),
				taxonomy = $taxWrappe.data('taxonomy');
			$.ajax({
				type: "GET",
				url: glfMetaData.ajax_url,
				data: {
					action: 'glf_tax_meta_form',
					taxonomy: taxonomy
				},
				success : function(res) {
					$taxWrappe.html(res);
					for (var i = 0; i < GLFFieldsConfig.fieldInstance.length; i++) {
						GLFFieldsConfig.fieldInstance[i].init();
					}
					GLFFieldsConfig.onReady.init();
				}
			});

		}catch(err) {}
	});
})(jQuery);