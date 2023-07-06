
var GLF_ButtonSetClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_ButtonSetClass.prototype = {
		init: function() {
			var self = this;

			self.allowClearChecked = false;

			self.$container.find('[data-field-control]').on('change', function() {
				var $field = $(this).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});

			self.$container.find('.glf-allow-clear').on('click mousedown', function(event) {
				var $input = $(this).closest('label').find('input[type="radio"]');

				if ($input.length > 0) {
					if (event.type == 'click') {
						setTimeout(function() {
							if (self.allowClearChecked) {
								$input[0].checked = false;
							}
						}, 10);
					}
					else {
						self.allowClearChecked = $input[0].checked;
					}
				}
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_ButtonSetObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-button_set-inner').each(function () {
					var field = new GLF_ButtonSetClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-button_set').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-button_set-inner');
				if ($items.length) {
					var field = new GLF_ButtonSetClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_ButtonSetObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_ButtonSetObject);
	});
})(jQuery);