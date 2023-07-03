/**
 * radio field script
 *
 */

var GLF_RadioClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_RadioClass.prototype = {
		init: function() {
			this.$container.find('input.glf-radio').on('change', function() {
				console.log(this);
				var $field = $(this).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_RadioObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-radio-inner').each(function () {
					var field = new GLF_RadioClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-radio').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-radio-inner');
				if ($items.length) {
					var field = new GLF_RadioClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_RadioObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_RadioObject);
	});
})(jQuery);