/**
 * ace-editor field script
 */

/**
 * Define class field
 */
var GLF_AceEditorClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_AceEditorClass.prototype = {
		init: function() {
			this.$fieldText = this.$container.find('textarea');
			this.$editorField = this.$container.find('.glf-ace-editor');
			var params = this.$fieldText.data('options'),
				mode = this.$fieldText.data('mode'),
				theme = this.$fieldText.data('theme');
			this.editor = ace.edit(this.$editorField.attr('id'));
			this.$editorField.attr('id', '');
			if (mode != '') {
				this.editor.session.setMode('ace/mode/' + mode);
			}
			if (theme != '') {
				this.editor.setTheme('ace/theme/' + theme);
			}

			this.editor.setAutoScrollEditorIntoView(true);
			this.editor.setOptions(params);
			var self = this;
			this.editor.on('change', function (event) {
				self.$fieldText.val(self.editor.getSession().getValue());

				var $field = self.$container.closest('.glf-field');
				$field.trigger('glf_field_change');
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_AceEditorObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-ace-editor-inner').each(function () {
					var field = new GLF_AceEditorClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-ace_editor').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-ace-editor-inner');
				if ($items.length) {
					var field = new GLF_AceEditorClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_AceEditorObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_AceEditorObject);
	});
})(jQuery);