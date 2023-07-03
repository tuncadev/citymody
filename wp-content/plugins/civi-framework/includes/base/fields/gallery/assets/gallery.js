/**
 * gallery field script
 *
 */

(function($) {
	"use strict";

	/**
	 * Define object field
	 */
	var GLF_GalleryObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-gallery-inner','.glf-field.glf-field-gallery').each(function () {
					var field = new GLF_GalleryClass($(this));
					field.init();
				});
			});

			$('.glf-field.glf-field-gallery').on('glf-gallery-selected glf-gallery-removed glf-gallery-sortable-updated ',function(event){
				var $field = $(event.target).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-gallery').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-gallery-inner');
				if ($items.length) {
					var field = new GLF_GalleryClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_GalleryObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_GalleryObject);
	});
})(jQuery);