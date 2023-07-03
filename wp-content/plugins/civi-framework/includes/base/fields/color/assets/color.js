/**
 * color field script
 *
 */

var GLF_ColorClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_ColorClass.prototype = {
		init: function() {
			var data = $.extend(
				{
					change: function () {
						var $this = $(this),
							$field = $this.closest('.glf-field');
						if (!$this.hasClass('glf-color-init-done')) {
							$this.addClass('glf-color-init-done');
						}
						else {
							setTimeout(function() {
								var value = GLFFieldsConfig.fields.getValue($field);
								GLFFieldsConfig.required.checkRequired($field, value);
							}, 50);
						}
					},
					clear: function () {
						var $field = $(this).closest('.glf-field');

						setTimeout(function() {
							var value = GLFFieldsConfig.fields.getValue($field);
							GLFFieldsConfig.required.checkRequired($field, '');
						}, 50);
					}
				}
			);
			this.$container.find('.glf-color').wpColorPicker(data);
		}
	};

	/**
	 * Define object field
	 */
	var GLF_ColorObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-color-inner').each(function () {
					var field = new GLF_ColorClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-color').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-color-inner');
				if ($items.length) {
					var field = new GLF_ColorClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_ColorObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_ColorObject);
	});
})(jQuery);