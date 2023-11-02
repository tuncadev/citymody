<?php

namespace Civi_Elementor;

defined('ABSPATH') || exit;

class Widget_Utils
{
	public static function get_control_options_horizontal_alignment()
	{
		return [
			'left'   => [
				'title' => esc_html__('Left', 'civi'),
				'icon'  => 'eicon-h-align-left',
			],
			'center' => [
				'title' => esc_html__('Center', 'civi'),
				'icon'  => 'eicon-h-align-center',
			],
			'right'  => [
				'title' => esc_html__('Right', 'civi'),
				'icon'  => 'eicon-h-align-right',
			],
		];
	}

	public static function get_control_options_horizontal_alignment_full()
	{
		return [
			'left'    => [
				'title' => esc_html__('Left', 'civi'),
				'icon'  => 'eicon-h-align-left',
			],
			'center'  => [
				'title' => esc_html__('Center', 'civi'),
				'icon'  => 'eicon-h-align-center',
			],
			'right'   => [
				'title' => esc_html__('Right', 'civi'),
				'icon'  => 'eicon-h-align-right',
			],
			'stretch' => [
				'title' => esc_html__('Stretch', 'civi'),
				'icon'  => 'eicon-h-align-stretch',
			],
		];
	}

	public static function get_control_options_vertical_alignment()
	{
		return [
			'top'    => [
				'title' => esc_html__('Top', 'civi'),
				'icon'  => 'eicon-v-align-top',
			],
			'middle' => [
				'title' => esc_html__('Middle', 'civi'),
				'icon'  => 'eicon-v-align-middle',
			],
			'bottom' => [
				'title' => esc_html__('Bottom', 'civi'),
				'icon'  => 'eicon-v-align-bottom',
			],
		];
	}

	public static function get_control_options_vertical_full_alignment()
	{
		return [
			'top'     => [
				'title' => esc_html__('Top', 'civi'),
				'icon'  => 'eicon-v-align-top',
			],
			'middle'  => [
				'title' => esc_html__('Middle', 'civi'),
				'icon'  => 'eicon-v-align-middle',
			],
			'bottom'  => [
				'title' => esc_html__('Bottom', 'civi'),
				'icon'  => 'eicon-v-align-bottom',
			],
			'stretch' => [
				'title' => esc_html__('Stretch', 'civi'),
				'icon'  => 'eicon-v-align-stretch',
			],
		];
	}

	public static function get_control_options_text_align()
	{
		return [
			'left'   => [
				'title' => esc_html__('Left', 'civi'),
				'icon'  => 'eicon-text-align-left',
			],
			'center' => [
				'title' => esc_html__('Center', 'civi'),
				'icon'  => 'eicon-text-align-center',
			],
			'right'  => [
				'title' => esc_html__('Right', 'civi'),
				'icon'  => 'eicon-text-align-right',
			],
		];
	}

	public static function get_control_options_flex_align()
	{
		return [
			'flex-start'   => [
				'title' => esc_html__('Left', 'civi'),
				'icon'  => 'eicon-text-align-left',
			],
			'center' => [
				'title' => esc_html__('Center', 'civi'),
				'icon'  => 'eicon-text-align-center',
			],
			'flex-end'  => [
				'title' => esc_html__('Right', 'civi'),
				'icon'  => 'eicon-text-align-right',
			],
		];
	}

	public static function get_control_options_text_align_full()
	{
		return [
			'left'    => [
				'title' => esc_html__('Left', 'civi'),
				'icon'  => 'eicon-text-align-left',
			],
			'center'  => [
				'title' => esc_html__('Center', 'civi'),
				'icon'  => 'eicon-text-align-center',
			],
			'right'   => [
				'title' => esc_html__('Right', 'civi'),
				'icon'  => 'eicon-text-align-right',
			],
			'justify' => [
				'title' => esc_html__('Justified', 'civi'),
				'icon'  => 'eicon-text-align-justify',
			],
		];
	}

	public static function get_button_style()
	{
		return [
			'classic' => esc_html__('Classic', 'civi'),
			'outline' => esc_html__('Outline', 'civi'),
			'link'    => esc_html__('Link', 'civi'),
			'border-bottom' => esc_html__('Border Bottom', 'civi'),
		];
	}

	public static function get_button_shape()
	{
		return [
			'rounded' => esc_html__('Rounded', 'civi'),
			'square'  => esc_html__('Square', 'civi'),
			'round'   => esc_html__('Round', 'civi'),
		];
	}

	public static function get_button_size()
	{
		return [
			'xs' => esc_html__('Extra Small', 'civi'),
			'sm' => esc_html__('Small', 'civi'),
			'md' => esc_html__('Medium', 'civi'),
			'lg' => esc_html__('Large', 'civi'),
			'xl' => esc_html__('Extra Large', 'civi'),
		];
	}

	/**
	 * Get recommended social icons for control ICONS.
	 *
	 * @return array
	 */
	public static function get_recommended_social_icons()
	{
		return [
			'fa-brands' => [
				'android',
				'apple',
				'behance',
				'bitbucket',
				'codepen',
				'delicious',
				'deviantart',
				'digg',
				'dribbble',
				'envelope',
				'facebook',
				"facebook-f",
				"facebook-messenger",
				"facebook-square",
				'flickr',
				'foursquare',
				'free-code-camp',
				'github',
				'gitlab',
				'globe',
				'houzz',
				'instagram',
				'jsfiddle',
				'link',
				'linkedin',
				'medium',
				'meetup',
				'mix',
				'mixcloud',
				'odnoklassniki',
				'pinterest',
				'product-hunt',
				'reddit',
				'rss',
				'shopping-cart',
				'skype',
				'slideshare',
				'snapchat',
				'soundcloud',
				'spotify',
				'stack-overflow',
				'steam',
				'telegram',
				'thumb-tack',
				'tripadvisor',
				'tumblr',
				'twitch',
				'twitter',
				'viber',
				'vimeo',
				'vk',
				'weibo',
				'weixin',
				'whatsapp',
				'wordpress',
				'xing',
				'yelp',
				'youtube',
				'500px',
			],
		];
	}

	public static function get_grid_metro_size()
	{
		return [
			'1:1'   => esc_html__('Width 1 - Height 1', 'civi'),
			'1:2'   => esc_html__('Width 1 - Height 2', 'civi'),
			'1:0.7' => esc_html__('Width 1 - Height 70%', 'civi'),
			'1:1.3' => esc_html__('Width 1 - Height 130%', 'civi'),
			'2:1'   => esc_html__('Width 2 - Height 1', 'civi'),
			'2:2'   => esc_html__('Width 2 - Height 2', 'civi'),
		];
	}
}
