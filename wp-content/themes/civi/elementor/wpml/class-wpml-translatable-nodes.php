<?php

namespace Civi_Elementor;

defined('ABSPATH') || exit;

class WPML_Translatable_Nodes
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize()
	{
		add_action('init', [$this, 'wp_init']);
	}

	public function wp_init()
	{
		add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter']);
	}

	public function get_translatable_node()
	{
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-google-map.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-list.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-attribute-list.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-pricing-table.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-table.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-modern-carousel.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-modern-slider.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-team-member-carousel.php';
		require_once CIVI_ELEMENTOR_DIR . '/wpml/class-translate-widget-testimonial-carousel.php';

		$widgets['civi-attribute-list'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Attribute_List',
		];

		$widgets['civi-heading'] = [
			'fields' => [
				[
					'field'       => 'title',
					'type'        => esc_html__('Modern Heading: Primary', 'civi'),
					'editor_type' => 'AREA',
				],
				'title_link' => [
					'field'       => 'url',
					'type'        => esc_html__('Modern Heading: Link', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__('Modern Heading: Description', 'civi'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'sub_title_text',
					'type'        => esc_html__('Modern Heading: Secondary', 'civi'),
					'editor_type' => 'AREA',
				],
			],
		];

		$widgets['civi-button'] = [
			'fields' => [
				[
					'field'       => 'text',
					'type'        => esc_html__('Button: Text', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__('Button: Badge', 'civi'),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Button: Link', 'civi'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['civi-banner'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__('Banner: Title', 'civi'),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Banner: Link', 'civi'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['civi-circle-progress-chart'] = [
			'fields' => [
				[
					'field'       => 'inner_content_text',
					'type'        => esc_html__('Circle Chart: Text', 'civi'),
					'editor_type' => 'LINE',
				],
			],
		];

		$widgets['civi-flip-box'] = [
			'fields' => [
				[
					'field'       => 'title_text_a',
					'type'        => esc_html__('Flip Box: Front Title', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_a',
					'type'        => esc_html__('Flip Box: Front Description', 'civi'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'title_text_b',
					'type'        => esc_html__('Flip Box: Back Title', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_b',
					'type'        => esc_html__('Flip Box: Back Description', 'civi'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Flip Box: Button Text', 'civi'),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Flip Box: Link', 'civi'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['civi-google-map'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Google_Map',
		];

		$widgets['civi-icon'] = [
			'fields' => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Icon: Link', 'civi'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['civi-icon-box'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__('Icon Box: Title', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__('Icon Box: Description', 'civi'),
					'editor_type' => 'AREA',
				],
				'link'        => [
					'field'       => 'url',
					'type'        => esc_html__('Icon Box: Link', 'civi'),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Icon Box: Button', 'civi'),
					'editor_type' => 'LINE',
				],
				'button_link' => [
					'field'       => 'url',
					'type'        => esc_html__('Icon Box: Button Link', 'civi'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['civi-image-box'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__('Image Box: Title', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__('Image Box: Content', 'civi'),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Image Box: Link', 'civi'),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Image Box: Button', 'civi'),
					'editor_type' => 'LINE',
				],
			],
		];

		$widgets['civi-list'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_List',
		];

		$widgets['civi-popup-video'] = [
			'fields' => [
				[
					'field'       => 'video_text',
					'type'        => esc_html__('Popup Video: Text', 'civi'),
					'editor_type' => 'LINE',
				],
				'video_url' => [
					'field'       => 'url',
					'type'        => esc_html__('Popup Video: Link', 'civi'),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'poster_caption',
					'type'        => esc_html__('Popup Video: Caption', 'civi'),
					'editor_type' => 'AREA',
				],
			],
		];

		$widgets['civi-pricing-table'] = [
			'fields'            => [
				[
					'field'       => 'heading',
					'type'        => esc_html__('Pricing Table: Heading', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => esc_html__('Pricing Table: Description', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'currency',
					'type'        => esc_html__('Pricing Table: Currency', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'price',
					'type'        => esc_html__('Pricing Table: Price', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'period',
					'type'        => esc_html__('Pricing Table: Period', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Pricing Table: Button', 'civi'),
					'editor_type' => 'LINE',
				],
				'button_link' => [
					'field'       => 'url',
					'type'        => esc_html__('Pricing Table: Button Link', 'civi'),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Pricing_Table',
		];

		$widgets['civi-table'] = [
			'fields'            => [],
			'integration-class' => [
				'\Civi_Elementor\Translate_Widget_Pricing_Table_Head',
				'\Civi_Elementor\Translate_Widget_Pricing_Table_Body',
			],
		];

		$widgets['civi-team-member'] = [
			'fields' => [
				[
					'field'       => 'name',
					'type'        => esc_html__('Team Member: Name', 'civi'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => esc_html__('Team Member: Content', 'civi'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'position',
					'type'        => esc_html__('Team Member: Position', 'civi'),
					'editor_type' => 'LINE',
				],
				'profile' => [
					'field'       => 'url',
					'type'        => esc_html__('Team Member: Profile', 'civi'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['civi-modern-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Modern_Carousel',
		];

		$widgets['civi-modern-slider'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Modern_Slider',
		];

		$widgets['civi-team-member-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Team_Member_Carousel',
		];

		$widgets['civi-testimonial-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Civi_Elementor\Translate_Widget_Testimonial_Carousel',
		];

		return $widgets;
	}

	public function wpml_widgets_to_translate_filter($widgets)
	{
		$civi_widgets = $this->get_translatable_node();

		foreach ($civi_widgets as $widget_name => $widget) {
			$widgets[$widget_name]               = $widget;
			$widgets[$widget_name]['conditions'] = [
				'widgetType' => $widget_name,
			];
		}

		return $widgets;
	}
}

WPML_Translatable_Nodes::instance()->initialize();
