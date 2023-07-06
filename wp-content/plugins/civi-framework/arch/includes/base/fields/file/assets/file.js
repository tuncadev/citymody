/**
 * file field script
 *
 */

var GLF_FileClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_FileClass.prototype = {
		init: function() {
			this.select();
			this.remove();
			this.sortable();
		},
		select: function() {
			var self = this,
				$button = self.$container.find('.glf-file-add > button'),
				library_filter = self.$container.data('lib-filter'),
				options = {
					title: sfFileFieldMeta.title,
					button: sfFileFieldMeta.button
				},
				_media = new GLFMedia();
			if ((typeof (library_filter) != "undefined") && (library_filter != null) && (library_filter != '')) {
				options.filter = library_filter;
			}

			_media.selectGallery($button, options, function(attachments) {
				if (attachments.length) {
					var $this = $(_media.clickedButton),
						$input = self.$container.find('input[type="hidden"]'),
						valInput = $input.val(),
						arrInput = valInput.split('|'),
						imgHtml = '',
						removeText = self.$container.data('remove-text');
					attachments.each(function(attachment) {
						attachment = attachment.toJSON();

						if (arrInput.indexOf('' + attachment.id) != -1) {
							return;
						}
						if (valInput != '') {
							valInput += '|' + attachment.id;
						}
						else {
							valInput = '' + attachment.id;
						}
						arrInput.push('' + attachment.id);
						imgHtml += '<div class="glf-file-item" data-file-id="' + attachment.id + '">';
						imgHtml += '<span class="dashicons dashicons-media-document"></span>';
						imgHtml +='<div class="glf-file-info">';
						imgHtml += '<a class="glf-file-title" href="' + attachment.editLink + '" target="_blank">' + attachment.title + '</a>';
						imgHtml += '<div class="glf-file-name">' + attachment.filename + '</div>';
						imgHtml += '<div class="glf-file-action">';
						imgHtml += '<span class="glf-file-remove"><span class="dashicons dashicons-no-alt"></span> ' + removeText + '</span>';
						imgHtml += '</div>';
						imgHtml += '</div>';
						imgHtml += '</div>';
					});
					$input.val(valInput);

					var $element = $(imgHtml);

					$this.parent().before($element);

					self.remove($element);

					var $field = $this.closest('.glf-field'),
						value = GLFFieldsConfig.fields.getValue($field);
					GLFFieldsConfig.required.checkRequired($field, value);
				}
			});
		},
		remove: function($item) {
			if (typeof ($item) === "undefined") {
				$item = this.$container;
			}
			$item.find('.glf-file-remove').on('click', function() {
				var $this = $(this).closest('.glf-file-item');
				var $parent = $this.parent();
				var $input = $parent.find('input[type="hidden"]');
				$this.remove();
				var valInput = '';
				$('.glf-file-item', $parent).each(function() {
					if (valInput != '') {
						valInput += '|' + $(this).data('file-id');
					}
					else {
						valInput = '' + $(this).data('file-id');
					}
				});
				$input.val(valInput);

				var $field = $parent.closest('.glf-field'),
					value = GLFFieldsConfig.fields.getValue($field);
				GLFFieldsConfig.required.checkRequired($field, value);
			});
		},
		sortable: function () {
			this.$container.sortable({
				placeholder: "glf-file-sortable-placeholder",
				items: '.glf-file-item',
				handle: '.dashicons-media-document',
				update: function( event, ui ) {
					var $wrapper = $(event.target);
					var valInput = '';
					$('.glf-file-item', $wrapper).each(function() {
						if (valInput != '') {
							valInput += '|' + $(this).data('file-id');
						}
						else {
							valInput = '' + $(this).data('file-id');
						}
					});
					var $input = $wrapper.find('input[type="hidden"]');
					$input.val(valInput);

					var $field = $wrapper.closest('.glf-field'),
						value = GLFFieldsConfig.fields.getValue($field);
					GLFFieldsConfig.required.checkRequired($field, value);
				}
			});
		}
	};

	/**
	 * Define object field
	 */
	var GLF_FileObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-file-inner').each(function () {
					var field = new GLF_FileClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-file').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-file-inner');
				if ($items.length) {
					var field = new GLF_FileClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_FileObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_FileObject);
	});
})(jQuery);