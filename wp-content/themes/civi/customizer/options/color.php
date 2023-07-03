<?php

$section = 'color';

$default = civi_get_default_theme_options();

// Color
Civi_Kirki::add_section($section, array(
	'title'    => esc_html__('Color', 'civi'),
	'priority' => 30,
));

// Content
Civi_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'color_content',
	'label'    => esc_html__('Content', 'civi'),
	'section'  => $section,
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'text_color',
	'label'     => esc_html__('Text', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['text_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'accent_color',
	'label'     => esc_html__('Accent', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['accent_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'primary_color',
	'label'     => esc_html__('Primary', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['primary_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'secondary_color',
	'label'     => esc_html__('Secondary', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['secondary_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'border_color',
	'label'     => esc_html__('Border', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['border_color'],
]);

// Background
Civi_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'color_bg_body',
	'label'    => esc_html__('Background', 'civi'),
	'section'  => $section,
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'body_background_color',
	'label'     => esc_html__('Body Background', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['body_background_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'image',
	'settings'  => 'bg_body_image',
	'label'     => esc_html__('Body BG Image', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_image'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_size',
	'label'     => esc_html__('Background Size', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_size'],
	'choices'   => [
		'auto'    => esc_html__('Auto', 'civi'),
		'cover'   => esc_html__('Cover', 'civi'),
		'contain' => esc_html__('Contain', 'civi'),
		'initial' => esc_html__('Initial', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_repeat',
	'label'     => esc_html__('Background Repeat', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_repeat'],
	'choices'   => [
		'no-repeat' => esc_html__('No Repeat', 'civi'),
		'repeat'    => esc_html__('Repeat', 'civi'),
		'repeat-x'  => esc_html__('Repeat X', 'civi'),
		'repeat-y'  => esc_html__('Repeat Y', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_position',
	'label'     => esc_html__('Background Position', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_position'],
	'choices'   => [
		'left top'      => esc_html__('Left Top', 'civi'),
		'left center'   => esc_html__('Left Center', 'civi'),
		'left bottom'   => esc_html__('Left Bottom', 'civi'),
		'right top'     => esc_html__('Right Top', 'civi'),
		'right center'  => esc_html__('Right Center', 'civi'),
		'right bottom'  => esc_html__('Right Bottom', 'civi'),
		'center top'    => esc_html__('Center Top', 'civi'),
		'center center' => esc_html__('Center Center', 'civi'),
		'center bottom' => esc_html__('Center Bottom', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_attachment',
	'label'     => esc_html__('Background Attachment', 'civi'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_attachment'],
	'choices'   => [
		'scroll' => esc_html__('Scroll', 'civi'),
		'fixed'  => esc_html__('Fixed', 'civi'),
	],
]);
