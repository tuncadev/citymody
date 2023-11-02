<?php

$section = 'typography';

$default = civi_get_default_theme_options();

// Typography
Civi_Kirki::add_section($section, array(
	'title'    => esc_html__('Typography', 'civi'),
	'priority' => 20,
));

// Body Font
Civi_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'notice_body_font',
	'label'    => esc_html__('Body Font', 'civi'),
	'section'  => $section,
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'typography',
	'settings'  => 'body_font_type',
	'label'     => esc_html__('Body Font Type', 'civi'),
	'section'   => $section,
	'transport' => 'auto',
	'default'   => array(
		'font-family'    => $default['font-family'],
		'font-size'      => $default['font-size'],
		'variant'        => $default['font-weight'],
		'letter-spacing' => $default['letter-spacing'],
	),
	'output' => [
		[
			'element' => 'body',
		],
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'typography',
	'settings'  => 'heading_font_type',
	'label'     => esc_html__('Heading Font Type', 'civi'),
	'section'   => $section,
	'transport' => 'auto',
	'default'   => array(
		'font-family'    => $default['heading-font-family'],
		'font-size'      => $default['heading-font-size'],
		'line-height'    => $default['heading-line-height'],
		'variant'        => $default['heading-variant'],
		'letter-spacing' => $default['heading-letter-spacing'],
	),
	'output' => [
		[
			'element' => 'h1,h2,h3,h4,h5,h6,.block-heading .entry-title,.civi-filter-toggle>span',
		],
	],
]);
