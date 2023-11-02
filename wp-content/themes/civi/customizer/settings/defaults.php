<?php

/**
 * Calzones: Default Options
 * Created by letruong272@gmail.com
 *
 * @package WordPress
 * @subpackage Calzones Theme
 * @since 1.0
 */

/**
 *  Get default options
 */
if (!function_exists('civi_get_default_theme_options')) {
	function civi_get_default_theme_options()
	{
		$defaults = array();

		/**
		 *  General
		 */
		$defaults['logo_dark']         = CIVI_IMAGES . 'logo.png';
		$defaults['logo_dark_retina']  = CIVI_IMAGES . 'logo-retina.png';
		$defaults['logo_light']        = CIVI_IMAGES . 'logo-light.png';
		$defaults['logo_light_retina'] = CIVI_IMAGES . 'logo-light-retina.png';

		$defaults['type_loading_effect'] 	  = 'none';
		$defaults['animation_loading_effect'] = 'css-1';
		$defaults['image_loading_effect'] 	  = '';

		$defaults['url_facebook'] 	 = '';
		$defaults['url_twitter'] 	 = '';
		$defaults['url_instagram'] 	 = '';
		$defaults['url_youtube'] 	 = '';
		$defaults['url_google_plus'] = '';
		$defaults['url_skype'] 	  	 = '';
		$defaults['url_linkedin'] 	 = '';
		$defaults['url_pinterest'] 	 = '';
		$defaults['url_slack'] 	  	 = '';
		$defaults['url_rss'] 	  	 = '';

		$defaults['page_title_text_color']     = '#111';
		$defaults['page_title_bg_color']       = '#f9f9f9';
		$defaults['page_title_bg_image']       = '';
		$defaults['page_title_bg_size']        = 'auto';
		$defaults['page_title_bg_repeat']      = 'no-repeat';
		$defaults['page_title_bg_position']    = 'right top';
		$defaults['page_title_bg_attachment']  = 'scroll';
		$defaults['page_title_font_size']      = 40;
		$defaults['page_title_letter_spacing'] = 0;

		/**
		 *  Color
		 */
		$defaults['text_color'] 	  	   = '#555555';
		$defaults['accent_color'] 	  	   = '#007456';
		$defaults['primary_color'] 	  	   = '#111111';
		$defaults['secondary_color'] 	   = '#999';
		$defaults['border_color'] 	  	   = '#eee';
		$defaults['body_background_color'] = '#ffffff';
		$defaults['bg_body_image'] 	  	   = '';
		$defaults['bg_body_size'] 	  	   = 'auto';
		$defaults['bg_body_repeat'] 	   = 'no-repeat';
		$defaults['bg_body_position'] 	   = 'left top';
		$defaults['bg_body_attachment']    = 'scroll';

		/**
		 *  Typography
		 */
		$defaults['font-style'] 	= array('bold', 'italic');
		$defaults['font-family'] 	= 'Inter';
		$defaults['font-size'] 		= '16px';
		$defaults['font-weight'] 	= 'normal';
		$defaults['letter-spacing'] = 'inherit';

		$defaults['heading-font-style'] 	= array('bold', 'italic');
		$defaults['heading-font-family'] 	= 'Inter';
		$defaults['heading-font-size'] 		= '34px';
		$defaults['heading-line-height'] 	= 'inherit';
		$defaults['heading-variant'] 		= '500';
		$defaults['heading-letter-spacing'] = 'inherit';

		/**
		 *  Layout
		 */
		$defaults['layout_content'] = 'fullwidth';
		$defaults['content_width']  = 1920;
		$defaults['layout_sidebar'] = 'right-sidebar';
		$defaults['sidebar_width']  = 400;

		/**
		 *  Header
		 */
		$defaults['header_background']           = '#fff';
		$defaults['top_bar_enable']              = '0';
		$defaults['sticky_header']               = '0';
		$defaults['header_sticky_background']    = '#000000';
		$defaults['float_header']                = '0';
		$defaults['show_canvas_menu']            = '0';
		$defaults['show_main_menu']              = '1';
		$defaults['show_login']                  = '1';
		$defaults['show_add_jobs_button']        = '1';
		$defaults['show_icon_noti']              = '1';
		$defaults['show_search_icon']            = '1';
		$defaults['logo_width']                  = '80';
		$defaults['header_padding_top']          = '8';
		$defaults['header_padding_bottom']       = '8';

		/**
		 *  Top Bar
		 */
		$defaults['top_bar_text']      = esc_html__('Become searchable by top companies in your domain. Learn more', 'civi');
		$defaults['top_bar_phone']     = esc_html__('+84-65854332', 'civi');
		$defaults['top_bar_email']     = esc_html__('hello@uxper.co', 'civi');
		$defaults['top_bar_color']     = '#ddd';
		$defaults['top_bar_bg_color']  = '#000';

		/**
		 *  Footer
		 */
		$defaults['footer_type'] = '';
		$defaults['footer_copyright_enable'] = true;
		$defaults['footer_copyright_text']   = esc_html__('© 2023 Uxper. All Right Reserved.', 'civi');

		/**
		 *  Blog
		 */
		$defaults['blog_sidebar']                   = 'right-sidebar';
		$defaults['blog_sidebar_width']             = 400;
		$defaults['blog_image_size']             	= '740x640';
		$defaults['blog_content_layout']            = 'layout-list';
		$defaults['blog_enable_categories']         = '0';
		$defaults['blog_number_column']             = 'columns-3';
		$defaults['enable_page_title_blog']         = '1';
		$defaults['page_title_blog_name']         	= esc_html__('Our Blog', 'civi');
		$defaults['style_page_title_blog']          = 'normal';
		$defaults['bg_page_title_blog']             = '';
		$defaults['color_page_title_blog']          = '#111';
		$defaults['bg_image_page_title_blog']       = '';
		$defaults['bg_size_page_title_blog']        = 'auto';
		$defaults['bg_repeat_page_title_blog']      = 'no-repeat';
		$defaults['bg_position_page_title_blog']    = 'right top';
		$defaults['bg_attachment_page_title_blog']  = 'scroll';
		$defaults['font_size_page_title_blog']      = 40;
		$defaults['letter_spacing_page_title_blog'] = 0;

		/**
		 *  Single Post
		 */
		$defaults['post_single_sidebar'] = 'right-sidebar';
		$defaults['post_comment']  	     = '1';

		return $defaults;
	}
}

/**
 *  Get theme options
 */
if (!function_exists('get_option_customize')) {
	function get_option_customize($key)
	{

		$value = null;

		$default_options = civi_get_default_theme_options();

		if (empty($key)) {
			return;
		}

		if (class_exists('Civi_Framework')) {
			$theme_option = Kirki::get_option($key);
		}

		if (isset($theme_option)) {
			$value = $theme_option;
		} elseif (isset($default_options[$key])) {
			$value = $default_options[$key];
		}

		return $value;
	}
}

/**
 *  Get theme mod
 */
if (!function_exists('civi_get_theme_mod')) {
	function civi_get_theme_mod($key)
	{

		$value = null;

		if (empty($key)) {
			return;
		}

		$theme_option = get_theme_mod($key);

		if (!empty($theme_option)) {
			$value = $theme_option;
		} else {
			$value = false;
		}

		return $value;
	}
}
