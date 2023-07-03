/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
(function($) {
  "use strict";
 	$(function() {
		// Logo Change
		$('#customize-control-choose_logo select').change(function(){
			$('#customize-control-custom_logo').hide();
			$('#customize-control-blogname').hide();
			$('#customize-control-blogdescription').hide();
			$('#customize-control-display_header_text').hide();
			$('#customize-control-text_logo_color').hide();
			$('#customize-control-text_logo_tagline_color').hide();
			$('#customize-control-site_logo_title_fontsize').hide();
			$('#customize-control-site_title_font_weight').hide();
			$('#customize-control-site_title_font_family').hide();
			$('#customize-control-site_title_font_style').hide();
			$('#customize-control-site_title_font_title').hide();
			$('#customize-control-site_tag_line_font_title').hide();
			$('#customize-control-site_logo_tag_line_fontsize').hide();
			$('#customize-control-site_tag_line_font_weight').hide();
			$('#customize-control-site_tag_line_font_style').hide();
			$('#customize-control-site_tag_line_font_family').hide();
			var choose_logo = $('#customize-control-choose_logo select option:selected').val();
			switch( choose_logo ){
				case 'img_logo':
					$('#customize-control-custom_logo').show();
					break;
				case 'text_logo':
					$('#customize-control-text_logo_color').show();
					$('#customize-control-text_logo_tagline_color').show();				
					$('#customize-control-blogname').show();
					$('#customize-control-blogdescription').show();
					$('#customize-control-display_header_text').show();
					$('#customize-control-site_logo_title_fontsize').show();
					$('#customize-control-site_title_font_weight').show();
					$('#customize-control-site_title_font_family').show();
					$('#customize-control-site_title_font_style').show();
					$('#customize-control-site_title_font_title').show();
					$('#customize-control-site_tag_line_font_title').show();
					$('#customize-control-site_logo_tag_line_fontsize').show();
					$('#customize-control-site_tag_line_font_weight').show();
					$('#customize-control-site_tag_line_font_style').show();
					$('#customize-control-site_tag_line_font_family').show();
					break;
			}
		}).change();

		// Footer
		$('#customize-control-choose_footer_type select').change(function(){
			$('#customize-control-main_footer_page').hide();
			$('#customize-control-page_footer_bg_color').hide();
			$('#customize-control-page_footer_content_color').hide();
			$('#customize-control-page_footer_link_color').hide();
			$('#customize-control-page_footer_link_hover_color').hide();
			$('#customize-control-footer_copy_rights').hide();
			$('#customize-control-footer_bg_color').hide();
			$('#customize-control-footer_content_color').hide();
			$('#customize-control-footer_link_color').hide();
			$('#customize-control-footer_link_hover_color').hide();
			var choose_footer_type = $('#customize-control-choose_footer_type select option:selected').val();
			switch( choose_footer_type ){
				case 'page_footer':
					$('#customize-control-main_footer_page').show();
					$('#customize-control-page_footer_bg_color').show();
					$('#customize-control-page_footer_content_color').show();
					$('#customize-control-page_footer_link_color').show();
					$('#customize-control-page_footer_link_hover_color').show();
					break;
				case 'default':
					$('#customize-control-footer_copy_rights').show();
					$('#customize-control-footer_bg_color').show();
					$('#customize-control-footer_content_color').show();
					$('#customize-control-footer_link_color').show();
					$('#customize-control-footer_link_hover_color').show();
					break;
			}
		}).change();
	
		// Footer
		$('#customize-control-choose_pagetitle_bg select').change(function(){
			$('#customize-control-page_titlebar_bg_color').hide();
			$('#customize-control-page_titlebar_bg_image').hide();
			var choose_pagetitle_bg = $('#customize-control-choose_pagetitle_bg select option:selected').val();
			switch( choose_pagetitle_bg ){
				case 'bg_color':
					$('#customize-control-page_titlebar_bg_color').show();
					break;
				case 'bg_img':
					$('#customize-control-page_titlebar_bg_image').show();
					break;
			}
		}).change();

		
	});
})(jQuery);