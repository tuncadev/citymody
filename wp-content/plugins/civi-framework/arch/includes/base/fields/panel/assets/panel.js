/**
 * panel field script
 *
 */

var GLF_PanelClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_PanelClass.prototype = {
		init: function() {
			var self = this;
			self.toggleElement();
			self.panelTitleElement();
		},

		panelTitleElement: function() {
			var $panelTitle = this.$container.find('[data-panel-title="true"]:first');
			$panelTitle.on('change', function() {
				var $this = $(this),
					value = $this.val(),
					$title = $this.closest('.glf-clone-field-panel').find('.glf-panel-title'),
					label = $title.data('label');
				if (value == '') {
					$title.text(label);
				}
				else {
					$title.text(label + ': ' + value);
				}
			});
			$panelTitle.trigger('change');
		},

		toggleElement: function($element) {
			var $toggle = this.$container.find('> h4'),
				$inner = this.$container.find('>.glf-clone-field-panel-inner');
			$toggle.on('click', function(event) {
				if ($(event.target).closest('.glf-clone-button-remove').length == 0) {
					$toggle.find('.glf-panel-toggle').toggleClass('dashicons-arrow-up').toggleClass('dashicons-arrow-down');
					$inner.slideToggle();
				}
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_PanelObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-clone-field-panel').each(function () {
					var field = new GLF_PanelClass($(this));
					field.init();
				});
				GLF_PanelObject.sortableFieldPanel();
				GLF_PanelObject.addCloneButton();
			});
		},
		addCloneButton: function() {
			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-panel').on('glf_add_clone_field', function(event){
				var $items = $(event.target);
				if ($items.length) {
					var field = new GLF_PanelClass($items);
					field.init();

					GLFFieldsConfig.cloneField.makeCloneTemplateElement($items);
					$items.find('.glf-field').each(function() {
						var $field = $(this),
							fieldType = $field.data('field-type');
						if (typeof (fieldType) != 'undefined') {
							var $container = $field.find('.glf-field-' + fieldType + '-inner');
							try {
								var field = eval("new " + GLF_PanelObject.getFieldClass(fieldType) + "($container)");
								field.init();
							}
							catch (ex) {}
						}
					});
					$items.find('.glf-field').each(function() {
						var $field = $(this);
						$field.on('glf_check_required', GLFFieldsConfig.required.onChangeEvent);
						$field.trigger('glf_check_required');
						$field.trigger('glf_check_preset');
					});
				}
			});
		},
		getFieldClass: function(fieldType) {
			var arr = fieldType.split('_');
			for (var i = 0; i < arr.length; i++) {
				arr[i] = this.ucwords(arr[i]);
			}
			return 'GLF_' + arr.join('') + 'Class';
		},
		ucwords: function(str) {
			return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
				return $1.toUpperCase();
			})
		},
		sortableFieldPanel: function() {
			var self = this;
			$('.glf-field-panel-sortable').sortable({
				placeholder: "glf-field-panel-sortable-placeholder",
				handle: '.glf-field-panel-title',
				items: '.glf-clone-field-panel',
				update: function(event) {
					var $wrapper = $(event.target),
						$field = $wrapper.closest('.glf-field');
					GLFFieldsConfig.cloneField.reIndexFieldName($wrapper.parent(), false);
					$field.trigger('glf_field_change');
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_PanelObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_PanelObject);
	});
})(jQuery);