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
			this.slider();
			this.onChange();
			this.unique_id();
		},
		slider: function() {
			this.$container.find('.glf-text[type="range"]').each(function() {
				var $this = $(this),
					$parent = $this.closest('.glf-field-text-inner');
				$parent.append('<span class="glf-text-range-info">' + $this.val() + '</span>');

				/**
				 * Slide drag
				 */
				this.oninput = function() {
					$(this).next().text($(this).val());
				}
			});
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
		unique_id : function() {
			this.$container.find('.glf-text[data-unique_id="true"]').each(function(){
				var $this = $(this),
					prefix = $this.data('unique_id-prefix'),
					$field = $this.closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				if (value === '') {
					var random =  Math.floor(Math.random() * (999999 - 100000)) + 100000;
					$this.val(prefix + random);
				}
			});
		}
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