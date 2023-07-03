<?php

/**
 * General Option
 *
 * @package Civi Theme
 * @version 1.0.0
 */

$panel = 'general';

$default = civi_get_default_theme_options();

// General
Civi_Kirki::add_panel($panel, array(
	'title'    => esc_html__('General', 'civi'),
	'priority' => 10,
));

// Site Identity
Civi_Kirki::add_section('site_identity', array(
	'title'    => esc_html__('Site Identity', 'civi'),
	'priority' => 10,
	'panel'    => $panel,
));

Civi_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_dark',
	'label'           => esc_html__('Logo Dark', 'civi'),
	'section'         => 'site_identity',
	'default'         => $default['logo_dark'],
]);

Civi_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_dark_retina',
	'label'           => esc_html__('Logo Dark Retina', 'civi'),
	'section'         => 'site_identity',
	'default'         => $default['logo_dark_retina'],
]);

Civi_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_light',
	'label'           => esc_html__('Logo Light', 'civi'),
	'section'         => 'site_identity',
	'default'         => $default['logo_light'],
]);

Civi_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_light_retina',
	'label'           => esc_html__('Logo Light Retina', 'civi'),
	'section'         => 'site_identity',
	'default'         => $default['logo_light_retina'],
]);

// Page Loading Effect
Civi_Kirki::add_section('page_loading_effect', array(
	'title'    => esc_html__('Page Loading Effect', 'civi'),
	'priority' => 10,
	'panel'    => $panel,
));

Civi_Kirki::add_field('theme', [
	'type'     => 'radio',
	'settings' => 'type_loading_effect',
	'label'    => esc_html__('Type Loading Effect', 'civi'),
	'section'  => 'page_loading_effect',
	'default'  => $default['type_loading_effect'],
	'choices'  => [
		'none'   		=> esc_html__('None', 'civi'),
		'css_animation' => esc_html__('CSS Animation', 'civi'),
		'image'  		=> esc_html__('Image', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'     => 'radio-buttonset',
	'settings' => 'animation_loading_effect',
	'label'    => esc_html__('Animation Type', 'civi'),
	'section'  => 'page_loading_effect',
	'default'  => $default['animation_loading_effect'],
	'choices'  => [
		'css-1'  => '<span class="civi-ldef-circle civi-ldef-loading"><span></span></span>',
		'css-2'  => '<span class="civi-ldef-dual-ring civi-ldef-loading"></span>',
		'css-3'  => '<span class="civi-ldef-facebook civi-ldef-loading"><span></span><span></span><span></span></span>',
		'css-4'  => '<span class="civi-ldef-heart civi-ldef-loading"><span></span></span>',
		'css-5'  => '<span class="civi-ldef-ring civi-ldef-loading"><span></span><span></span><span></span><span></span></span>',
		'css-6'  => '<span class="civi-ldef-roller civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-7'  => '<span class="civi-ldef-default civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-8'  => '<span class="civi-ldef-ellipsis civi-ldef-loading"><span></span><span></span><span></span><span></span></span>',
		'css-9'  => '<span class="civi-ldef-grid civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-10' => '<span class="civi-ldef-hourglass civi-ldef-loading"></span>',
		'css-11' => '<span class="civi-ldef-ripple civi-ldef-loading"><span></span><span></span></span>',
		'css-12' => '<span class="civi-ldef-spinner civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
	],
]);

Civi_Kirki::add_field('theme', [
	'type'     => 'image',
	'settings' => 'image_loading_effect',
	'label'    => esc_html__('Image', 'civi'),
	'section'  => 'page_loading_effect',
	'default'  => $default['image_loading_effect'],
]);

// Page Title
Civi_Kirki::add_section('page_title', array(
	'title'    => esc_html__('Page Title', 'civi'),
	'priority' => 10,
	'panel'    => $panel,
));


Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'page_title_text_color',
	'label'     => esc_html__('Text Color', 'civi'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_text_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'page_title_bg_color',
	'label'     => esc_html__('Background Color', 'civi'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_bg_color'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'image',
	'settings'  => 'page_title_bg_image',
	'label'     => esc_html__('Background Image', 'civi'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_bg_image'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_size',
	'label'     => esc_html__('Background Size', 'civi'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_size'],
	'transport' => 'postMessage',
	'choices'   => [
		'auto'    => esc_html__('Auto', 'civi'),
		'cover'   => esc_html__('Cover', 'civi'),
		'contain' => esc_html__('Contain', 'civi'),
		'initial' => esc_html__('Initial', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_repeat',
	'label'     => esc_html__('Background Repeat', 'civi'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_repeat'],
	'transport' => 'postMessage',
	'choices'   => [
		'no-repeat' => esc_html__('No Repeat', 'civi'),
		'repeat'    => esc_html__('Repeat', 'civi'),
		'repeat-x'  => esc_html__('Repeat X', 'civi'),
		'repeat-y'  => esc_html__('Repeat Y', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_position',
	'label'     => esc_html__('Background Position', 'civi'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_position'],
	'transport' => 'postMessage',
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
	'settings'  => 'page_title_bg_attachment',
	'label'     => esc_html__('Background Attachment', 'civi'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_attachment'],
	'transport' => 'postMessage',
	'choices'   => [
		'scroll' => esc_html__('Scroll', 'civi'),
		'fixed'  => esc_html__('Fixed', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'page_title_font_size',
	'label'     => esc_html__('Font Size', 'civi'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_font_size'],
	'choices'   => [
		'min'  => 12,
		'max'  => 50,
		'step' => 1,
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'page_title_letter_spacing',
	'label'     => esc_html__('Letter Spacing', 'civi'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_letter_spacing'],
	'choices'   => [
		'min'  => 0,
		'max'  => 10,
		'step' => 0.5,
	],
]);
