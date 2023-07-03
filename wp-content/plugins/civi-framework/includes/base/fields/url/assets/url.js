/**
 * text field script
 *
 */

var GLF_TextClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	GLF_TextClass.prototype = {
		init: function() {
			this.onChange();
		},

		onChange: function() {
			this.$container.find('.glf-text[data-field-control]').on('change', function() {
				var $this = $(this),
					type = $this.attr('type');
				var $field = $this.closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		},
	};

	/**
	 * Define object field
	 */
	var GLF_TextObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-text-inner').each(function () {
					var field = new GLF_TextClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-text').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-text-inner');
				if ($items.length) {
					var field = new GLF_TextClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_TextObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_TextObject);
	});
})(jQuery);