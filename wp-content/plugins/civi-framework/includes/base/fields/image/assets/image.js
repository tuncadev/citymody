/**
 * image field script
 *
 */

var GLF_ImageClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_ImageClass.prototype = {
		init: function() {
			this.selectMedia();
		},
		selectMedia: function() {
			var self = this,
				$idField = self.$container.find('.glf-image-id'),
				$urlField = self.$container.find('.glf-image-url'),
				$chooseImage = self.$container.find('.glf-image-choose-image'),
				$removeButton = self.$container.find('.glf-image-remove'),
				$preview = self.$container.find('.glf-image-preview img'),
				$selectImageDefaultDir = self.$container.find('.glf-image-choose-image-dir');

			if ($selectImageDefaultDir.length) {
				$.ajax({
					url: glfMetaData.ajax_url,
					data: {
						action: 'glf_select_default_image'
					},
					success: function (res) {
						var $popup = $('.glf-image-default-popup');
						if ($popup.length == 0) {
							$popup = $(res);
							$('body').append($popup);
							self.imageDefaultPopupEvent($popup);
						}
					}
				});
			}
			$selectImageDefaultDir.on('click', function() {
				var $popup = $('.glf-image-default-popup');
				if (!$popup.length) {
					return;
				}
				$popup.data('urlField', $urlField);
				$popup.data('idField', $idField);
				$popup.data('previewField', $preview);
				$popup.show();
			});

			/**
			 * Init Media
			 */
			var _media = new GLFMedia();
			_media.selectImage($chooseImage, {filter: 'image'}, function(attachment) {
				if (attachment) {
					var thumb_url = '';
					if (attachment.sizes.thumbnail == undefined) {
						if( attachment == 'svg' ){
							thumb_url = attachment.url;
						}else{
							thumb_url = attachment.sizes.full.url;
						}
					}
					else {
						thumb_url = attachment.sizes.thumbnail.url;
					}
					$preview.attr('src', thumb_url);
					$preview.show();
					$idField.val(attachment.id);
					$urlField.val(attachment.url);

					self.changeField(self.$container);
				}
			});

			/**
			 * Remove Image
			 */
			$removeButton.on('click', function() {
				$preview.attr('src', '');
				$preview.hide();
				$idField.val('');
				$urlField.val('');

				self.changeField(self.$container);
			});

			$urlField.on('change', function() {
				$.ajax({
					url: glfMetaData.ajax_url,
					data: {
						action: 'glf_get_attachment_id',
						url: $urlField.val()
					},
					type: 'GET',
					error: function() {
						$idField.val('0');
					},
					success: function(res) {
						$idField.val(res);
					}
				});
				if ($urlField.val() == '') {
					$preview.attr('src', '');
					$preview.hide();
				}
				else {
					$preview.attr('src', $urlField.val());
					$preview.show();
				}
			});
		},
		imageDefaultPopupEvent: function($popup) {
			var self = this;
			$popup.find('.glf-image-default-popup-content > h1 > span').on('click', function() {
				$popup.hide();
			});
			$popup.find('.glf-image-default-popup-item').on('click', function() {
				var $img = $(this).find('img'),
					src = $img.attr('src');

				$popup.data('previewField').attr('src', src);
				$popup.data('previewField').show();
				$popup.data('idField').val('0');
				$popup.data('urlField').val(src);
				$popup.hide();
				self.changeField(self.$container);
			});
		},
		changeField: function($item) {
			var $field = $item.closest('.glf-field'),
				value = GLFFieldsConfig.fields.getValue($field);
			GLFFieldsConfig.required.checkRequired($field, value);
		}
	};

	/**
	 * Define object field
	 */
	var GLF_ImageObject = {
		init: function() {
			/**
			 * Init Fields after make clone template
			 */
			var $configWrapper = $('.glf-meta-config-wrapper');
			$configWrapper = $configWrapper.length ? $configWrapper : $('body');

			$configWrapper.on('glf_make_template_done', function() {
				$('.glf-field-image-inner').each(function () {
					var field = new GLF_ImageClass($(this));
					field.init();
				});
			});

			/**
			 * Init Clone Field after field cloned
			 */
			$('.glf-field.glf-field-image').on('glf_add_clone_field', function(event){
				var $items = $(event.target).find('.glf-field-image-inner');
				if ($items.length) {
					console.log($items);
					var field = new GLF_ImageClass($items);
					field.init();
				}
			});
		}
	};

	/**
	 * Init Field when document ready
	 */
	$(document).ready(function() {
		GLF_ImageObject.init();
		GLFFieldsConfig.fieldInstance.push(GLF_ImageObject);
	});
})(jQuery);