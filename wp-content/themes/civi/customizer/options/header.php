<?php

$section = 'header';

$default = civi_get_default_theme_options();

// Header
Civi_Kirki::add_section($section, array(
	'title'    => esc_html__('Header', 'civi'),
	'priority' => 50,
));

Civi_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'header_customize',
	'label'    => esc_html__('Header Customize', 'civi'),
	'section'  => $section,
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'header_background',
	'label'     => esc_html__('Background Color', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_background'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'top_bar_enable',
	'label'     => esc_html__('Enable Top Bar', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['top_bar_enable'],
]);


Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'sticky_header',
	'label'     => esc_html__('Enable Sticky', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['sticky_header'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'header_sticky_background',
	'label'     => esc_html__('Background Color Header Sticky', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_sticky_background'],
	'active_callback' => [
		[
			'setting'  => 'sticky_header',
			'operator' => '==',
			'value'    => '1',
		]
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'float_header',
	'label'     => esc_html__('Enable Float', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['float_header'],
]);


Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_canvas_menu',
	'label'     => esc_html__('Show Canvas Menu', 'civi'),
	'section'   => $section,
	'default'   => $default['show_canvas_menu'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_main_menu',
	'label'     => esc_html__('Show Main Menu', 'civi'),
	'section'   => $section,
	'default'   => $default['show_main_menu'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_login',
	'label'     => esc_html__('Show Login', 'civi'),
	'section'   => $section,
	'default'   => $default['show_login'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_icon_noti',
	'label'     => esc_html__('Show Notification', 'civi'),
	'section'   => $section,
	'default'   => $default['show_icon_noti'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_search_icon',
	'label'     => esc_html__('Show Search (Mobile)', 'civi'),
	'section'   => $section,
	'default'   => $default['show_search_icon'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_add_jobs_button',
	'label'     => esc_html__('Show Add Jobs/Update Profile', 'civi'),
	'section'   => $section,
	'default'   => $default['show_add_jobs_button'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'logo_width',
	'label'     => esc_html__('Logo Width', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['logo_width'],
	'choices'   => [
		'min'  => 0,
		'max'  => 500,
		'step' => 1,
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'header_padding_top',
	'label'     => esc_html__('Padding Top', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_padding_top'],
	'choices'   => [
		'min'  => 0,
		'max'  => 200,
		'step' => 1,
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'header_padding_bottom',
	'label'     => esc_html__('Padding Bottom', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_padding_bottom'],
	'choices'   => [
		'min'  => 0,
		'max'  => 500,
		'step' => 1,
	],
]);
