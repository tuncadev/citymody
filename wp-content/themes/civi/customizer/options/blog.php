<?php

$panel = 'blog';

$default = civi_get_default_theme_options();

// Blog
Civi_Kirki::add_panel($panel, array(
	'title'    => esc_html__('Blog', 'civi'),
	'priority' => 70,
));

// Blog archive
Civi_Kirki::add_section('blog_archive', array(
	'title' => esc_html__('Blog Archive', 'civi'),
	'panel' => $panel,
));

Civi_Kirki::add_field('theme', [
	'type'            => 'notice',
	'settings'        => 'blog_customize',
	'label'           => esc_html__('Blog Customize', 'civi'),
	'section'         => 'blog_archive',
	'partial_refresh' => [
		'blog_customize' => [
			'selector'        => '#primary.content-blog',
			'render_callback' => 'wp_get_document_title',
		],
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'radio-image',
	'settings'  => 'blog_sidebar',
	'label'     => esc_html__('Sidebar Layout', 'civi'),
	'section'   => 'blog_archive',
	'transport' => 'postMessage',
	'default'   => $default['blog_sidebar'],
	'choices'   => [
		'left-sidebar'  => get_template_directory_uri() . '/customizer/assets/images/left-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/customizer/assets/images/no-sidebar.png',
		'right-sidebar' => get_template_directory_uri() . '/customizer/assets/images/right-sidebar.png',
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'blog_sidebar_width',
	'label'     => esc_html__('Sidebar Width', 'civi'),
	'section'   => 'blog_archive',
	'transport' => 'postMessage',
	'default'   => $default['blog_sidebar_width'],
	'choices'   => [
		'min'  => 270,
		'max'  => 420,
		'step' => 1,
	],
]);

Civi_Kirki::add_field('theme', [
	'type'     => 'text',
	'settings' => 'blog_image_size',
	'label'    => esc_html__('Image size', 'civi'),
	'section'  => 'blog_archive',
	'default'  => $default['blog_image_size'],
]);

Civi_Kirki::add_field('theme', [
	'type'     => 'radio-image',
	'settings' => 'blog_content_layout',
	'label'    => esc_html__('Content Layout', 'civi'),
	'section'  => 'blog_archive',
	'default'  => $default['blog_content_layout'],
	'choices'  => [
		'layout-grid' => get_template_directory_uri() . '/customizer/assets/images/layout-grid.png',
		'layout-list' => get_template_directory_uri() . '/customizer/assets/images/layout-list.png',
		'layout-masonry' => get_template_directory_uri() . '/customizer/assets/images/layout-masonry.png',
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'radio-image',
	'settings'  => 'blog_number_column',
	'label'     => esc_html__('Columns', 'civi'),
	'section'   => 'blog_archive',
	'transport' => 'postMessage',
	'default'   => $default['blog_number_column'],
	'choices'   => [
		'columns-2' => get_template_directory_uri() . '/customizer/assets/images/col-2.png',
		'columns-3' => get_template_directory_uri() . '/customizer/assets/images/col-3.png',
		'columns-4' => get_template_directory_uri() . '/customizer/assets/images/col-4.png',
	],
	'active_callback' => [
		[
			'setting'  => 'blog_content_layout',
			'operator' => '!=',
			'value'    => 'layout-list',
		]
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'blog_enable_categories',
	'label'     => esc_html__('Enable Head Categories', 'civi'),
	'section'   => 'blog_archive',
	'transport' => 'postMessage',
	'default'   => $default['blog_enable_categories'],
	'active_callback' => [
		[
			'setting'  => 'blog_content_layout',
			'operator' => '!=',
			'value'    => 'layout-list',
		]
	],
]);

// Single post
Civi_Kirki::add_section('single_post', array(
	'title' => esc_html__('Single Post', 'civi'),
	'panel' => $panel,
));

Civi_Kirki::add_field('theme', [
	'type'      => 'radio-image',
	'settings'  => 'post_single_sidebar',
	'label'     => esc_html__('Sidebar Layout', 'civi'),
	'section'   => 'single_post',
	'transport' => 'postMessage',
	'default'   => $default['post_single_sidebar'],
	'choices'   => [
		'left-sidebar'  => get_template_directory_uri() . '/customizer/assets/images/left-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/customizer/assets/images/no-sidebar.png',
		'right-sidebar' => get_template_directory_uri() . '/customizer/assets/images/right-sidebar.png',
	],
]);

// Page Title
Civi_Kirki::add_section('page_title_blog', array(
	'title' => esc_html__('Page Title', 'civi'),
	'panel' => $panel,
));

Civi_Kirki::add_field('theme', [
	'type'            => 'notice',
	'settings'        => 'page_title_blog',
	'label'           => esc_html__('Page Title', 'civi'),
	'section'         => 'page_title_blog',
	'partial_refresh' => [
		'page_title_blog' => [
			'selector'        => '.page-title-blog',
			'render_callback' => 'wp_get_document_title',
		],
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'enable_page_title_blog',
	'label'     => esc_html__('Enable Page Title', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['enable_page_title_blog'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'text',
	'settings'  => 'page_title_blog_name',
	'label'     => esc_html__('Title', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['page_title_blog_name'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'radio-image',
	'settings'  => 'style_page_title_blog',
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'multiple'  => 1,
	'default'   => $default['style_page_title_blog'],
	'choices'   => [
		'normal' => get_template_directory_uri() . '/customizer/assets/images/text-uppercase.png',
		'italic' => get_template_directory_uri() . '/customizer/assets/images/text-italic.png',
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'bg_page_title_blog',
	'label'     => esc_html__('Background Color', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['bg_page_title_blog'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'color_page_title_blog',
	'label'     => esc_html__('Text Color', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['color_page_title_blog'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'image',
	'settings'  => 'bg_image_page_title_blog',
	'label'     => esc_html__('Background Image', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['bg_image_page_title_blog'],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_size_page_title_blog',
	'label'     => esc_html__('Background Size', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['bg_size_page_title_blog'],
	'choices'   => [
		'auto'    => esc_html__('Auto', 'civi'),
		'cover'   => esc_html__('Cover', 'civi'),
		'contain' => esc_html__('Contain', 'civi'),
		'initial' => esc_html__('Initial', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_repeat_page_title_blog',
	'label'     => esc_html__('Background Repeat', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['bg_repeat_page_title_blog'],
	'choices'   => [
		'no-repeat' => esc_html__('No Repeat', 'civi'),
		'repeat'    => esc_html__('Repeat', 'civi'),
		'repeat-x'  => esc_html__('Repeat X', 'civi'),
		'repeat-y'  => esc_html__('Repeat Y', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_position_page_title_blog',
	'label'     => esc_html__('Background Position', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['bg_position_page_title_blog'],
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
	'settings'  => 'bg_attachment_page_title_blog',
	'label'     => esc_html__('Background Attachment', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['bg_attachment_page_title_blog'],
	'choices'   => [
		'scroll' => esc_html__('Scroll', 'civi'),
		'fixed'  => esc_html__('Fixed', 'civi'),
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'font_size_page_title_blog',
	'label'     => esc_html__('Font Size', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['font_size_page_title_blog'],
	'choices'   => [
		'min'  => 12,
		'max'  => 50,
		'step' => 1,
	],
]);

Civi_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'letter_spacing_page_title_blog',
	'label'     => esc_html__('Letter Spacing', 'civi'),
	'section'   => 'page_title_blog',
	'transport' => 'postMessage',
	'default'   => $default['letter_spacing_page_title_blog'],
	'choices'   => [
		'min'  => 0,
		'max'  => 10,
		'step' => 0.5,
	],
]);
