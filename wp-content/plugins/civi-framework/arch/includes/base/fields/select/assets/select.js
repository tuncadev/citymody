/**
 * select field script
 *
 */

var GLF_SelectClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_SelectClass.prototype = {
		init: function() {
			this.$container.find('.glf-select').on('change', function() {
				var $field = $(this).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_SelectObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-select-inner').each(function () {
					var field = new GLF_SelectClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-select').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-select-inner');
				if ($items.length) {
					var field = new GLF_SelectClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_SelectObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_SelectObject);
	});
})(jQuery);