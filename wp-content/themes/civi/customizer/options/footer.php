<?php

$section = 'footer';

$default = civi_get_default_theme_options();

// Footer
Civi_Kirki::add_section($section, array(
	'title'    => esc_html__('Footer', 'civi'),
	'priority' => 50,
));

Civi_Kirki::add_field('theme', [
	'type'            => 'notice',
	'settings'        => 'footer_customize',
	'label'           => esc_html__('Footer Customize', 'civi'),
	'section'         => $section,
	'partial_refresh' => [
		'header_type' => [
			'selector'        => 'footer.site-footer',
			'render_callback' => 'wp_get_document_title',
		],
	],
]);

Civi_Kirki::add_field('theme', [
	'type'     => 'select',
	'settings' => 'footer_type',
	'label'    => esc_html__('Footer Default', 'civi'),
	'section'  => $section,
	'default'  => $default['footer_type'],
	'choices'  => civi_get_footer_elementor(),
]);

Civi_Kirki::add_field('theme', [
	'type'     => 'text',
	'settings' => 'footer_copyright_text',
	'label'    => esc_html__('Copyright', 'civi'),
	'section'  => $section,
	'default'  => $default['footer_copyright_text'],
]);
