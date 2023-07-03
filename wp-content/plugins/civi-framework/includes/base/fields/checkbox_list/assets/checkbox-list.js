/**
 * checkbox_list field script
 */

/**
 * Define class field
 */
var GLF_CheckboxListClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_CheckboxListClass.prototype = {
		init: function() {
			this.$container.find('input.glf-checkbox_list').on('change', function() {
				var $field = $(this).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_CheckboxListObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-checkbox_list-inner').each(function () {
					var field = new GLF_CheckboxListClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-checkbox_list').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-checkbox_list-inner');
				if ($items.length) {
					var field = new GLF_CheckboxListClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_CheckboxListObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_CheckboxListObject);
	});
})(jQuery);