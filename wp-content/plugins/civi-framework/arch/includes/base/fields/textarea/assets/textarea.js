/**
 * your_field field script
 *
 */

var GLF_YourFieldClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_YourFieldClass.prototype = {
		init: function() {
			this.$container.find('.glf-textarea').on('change', function() {
				var $field = $(this).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_YourFieldObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-your_field-inner').each(function () {
					var field = new GLF_YourFieldClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-your_field').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-your_field-inner');
				if ($items.length) {
					var field = new GLF_YourFieldClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_YourFieldObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_YourFieldObject);
	});
})(jQuery);