/**
 * Define class field
 */
var GLF_SortableClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_SortableClass.prototype = {
		init: function() {
			this.$container.sortable({
				placeholder: 'glf-sortable-sortable-placeholder',
				items: '.glf-field-sortable-item',
				handle: '.dashicons-menu',
				update: function (event, ui) {
					var $wrapper = $(event.target);

					var sortValue = '';
					$wrapper.find('input[type="checkbox"]').each(function() {
						var $this = $(this);
						if (sortValue === '') {
							sortValue += $this.val();
						}
						else {
							sortValue += '|' + $this.val();
						}
					});

					$wrapper.find('.glf-field-sortable-sort').val(sortValue);


					var $field = $wrapper.closest('.glf-field'),
						value = GLFFieldsConfig.fields.getValue($field);
					GLFFieldsConfig.required.checkRequired($field, value);
				}
			});

			$('.glf-field-sortable-inner .glf-field-sortable-checkbox').change(function() {
				var $field = $(this).closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_SortableObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-sortable-inner').each(function () {
					var field = new GLF_SortableClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-sortable').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-sortable-inner');
				if ($items.length) {
					var field = new GLF_SortableClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_SortableObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_SortableObject);
	});
})(jQuery);