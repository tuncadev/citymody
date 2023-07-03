<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Jobs_Category());

class Widget_Jobs_Category extends Widget_Base
{

	const QUERY_CONTROL_ID = 'query';
	const QUERY_OBJECT_POST = 'post';

	public function get_post_type()
	{
		return 'jobs';
	}

	public function get_name()
	{
		return 'civi-jobs-category';
	}

	public function get_title()
	{
		return esc_html__('Jobs Category', 'civi-framework');
	}

	public function get_icon()
	{
		return 'civi-badge eicon-folder-o';
	}

	public function get_keywords()
	{
		return ['jobs', 'category', 'carousel'];
	}

	public function get_style_depends()
	{
		return [CIVI_PLUGIN_PREFIX . 'jobs-category'];
	}

	protected function register_controls()
	{
		$this->register_layout_section();
		$this->register_slider_section();
		$this->register_layout_style_section();
		$this->register_title_style_section();
		$this->register_icon_style_section();
		$this->register_count_style_section();
	}

	private function register_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'civi-framework'),
			'tab' => Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__('Layout', 'civi-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => '01',
				'options' => [
					'01' => esc_html__('01', 'civi-framework'),
					'02' => esc_html__('02', 'civi-framework'),
				],
				'prefix_class' => 'civi-layout-',
			]
		);

		$this->add_control('hover_effect', [
			'label'        => esc_html__('Hover Effect', 'civi-framework'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''         => esc_html__('None', 'civi-framework'),
				'zoom-in'  => esc_html__('Zoom In', 'civi-framework'),
				'zoom-out' => esc_html__('Zoom Out', 'civi-framework'),
				'move-up'  => esc_html__('Move Up', 'civi-framework'),
			],
			'default'      => '',
			'prefix_class' => 'civi-animation-',
			'condition' => [
				'layout' => '02',
			],
		]);

		$this->add_control('position', [
			'label'        => esc_html__('Position', 'civi-framework'),
			'type'         => Controls_Manager::CHOOSE,
			'default'      => 'top',
			'options'      => [
				'left'  => [
					'title' => esc_html__('Left', 'civi-framework'),
					'icon'  => 'eicon-h-align-left',
				],
				'top'   => [
					'title' => esc_html__('Top', 'civi-framework'),
					'icon'  => 'eicon-v-align-top',
				],
				'right' => [
					'title' => esc_html__('Right', 'civi-framework'),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'prefix_class' => 'elementor-position-',
		]);

		$repeater = new Repeater();

		$taxonomy_terms = get_categories(
			array(
				'taxonomy' => 'jobs-categories',
				'orderby' => 'name',
				'order' => 'ASC',
				'hide_empty' => true,
				'parent' => 0,
			)
		);

		$categories = [];
		foreach ($taxonomy_terms as $category) {
			$categories[$category->slug] = $category->name;
		}
		$repeater->add_control(
			'category',
			[
				'label' => esc_html__('Categories', 'civi-framework'),
				'type' => Controls_Manager::SELECT,
				'options' => $categories,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label' => esc_html__('Icon', 'civi-framework'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control('image', [
			'label'   => esc_html__('Choose Image', 'civi-framework'),
			'type'    => Controls_Manager::MEDIA,
		]);

		$repeater->add_control(
			'icon_item_color',
			[
				'label' => esc_html__('Text Color', 'civi-framework'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .icon-cate' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'icon_item_bg_color',
			[
				'label' => esc_html__('Background Color', 'civi-framework'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .icon-cate' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'categories_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'text' => esc_html__('Category #1', 'civi-framework'),
					],
					[
						'text' => esc_html__('Category #2', 'civi-framework'),
					],
					[
						'text' => esc_html__('Category #3', 'civi-framework'),
					],
				],
				//                'title_field' => '{{{ category }}}',
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label' => esc_html__('Show Icon', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_arrow',
			[
				'label' => esc_html__('Show Arrow', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'layout' => '02',
				],
			]
		);

		$this->add_control(
			'show_count',
			[
				'label' => esc_html__('Show Count', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label' => esc_html__('Show Description', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

        $this->add_control(
            'text_style',
            [
                'label' => esc_html__('Text style', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'show_slider!' => 'yes',
                ],
            ]
        );

		$this->add_control(
			'show_list_cate',
			[
				'label' => esc_html__('Show List Categories', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'show_slider!' => 'yes',
				],
				'separator'  => 'before',
			]
		);

		$this->add_control('text_cate', [
			'label'       => esc_html__('Text', 'civi-framework'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('View all categories', 'civi-framework'),
			'condition' => [
				'show_list_cate!' => '',
				'show_slider!' => 'yes',
			],
		]);

        $this->add_control('link_cate', [
            'label'     => esc_html__('Link', 'civi'),
            'type'      => Controls_Manager::URL,
            'dynamic'   => [
                'active' => true,
            ],
            'default'   => [
                'url' => '',
            ],
            'condition' => [
                'show_list_cate!' => '',
                'show_slider!' => 'yes',
            ],
        ]);

		$this->add_control(
			'show_slider',
			[
				'label' => esc_html__('Show Slider', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'civi-framework'),
				'type' => Controls_Manager::NUMBER,
				'prefix_class' => 'elementor-grid%s-',
				'min' => 1,
				'max' => 8,
				'default' => 2,
				'required' => true,
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'required' => false,
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'required' => false,
					],
				],
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'condition' => [
					'show_slider!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __('Columns Gap', 'civi-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-carousel .list-cate-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => esc_html__('Rows Gap', 'civi-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-carousel .list-cate-item' => 'padding-top: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2);margin-bottom: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_slider_section()
	{
		$this->start_controls_section('slider_section', [
			'label' => esc_html__('Slider', 'civi-framework'),
			'tab' => Controls_Manager::TAB_CONTENT,
			'condition' => [
				'show_slider' => 'yes',
			],
		]);

		$slides_to_show = range(1, 10);
		$slides_to_show = array_combine($slides_to_show, $slides_to_show);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => esc_html__('Slides to Show', 'civi-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => [
					'' => esc_html__('Default', 'civi-framework'),
				] + $slides_to_show,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label' => esc_html__('Slides to Scroll', 'civi-framework'),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('Set how many slides are scrolled per swipe.', 'civi-framework'),
				'default' => '1',
				'options' => [
					'' => esc_html__('Default', 'civi-framework'),
				] + $slides_to_show,
			]
		);

		$this->add_control(
			'slides_number_row',
			[
				'label' => esc_html__('Number Row', 'civi-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 4,
				'default' => 1,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => esc_html__('Navigation', 'civi-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => esc_html__('Arrows and Dots', 'civi-framework'),
					'arrows' => esc_html__('Arrows', 'civi-framework'),
					'dots' => esc_html__('Dots', 'civi-framework'),
					'none' => esc_html__('None', 'civi-framework'),
				],
			]
		);

		$this->add_control(
			'center_mode',
			[
				'label' => esc_html__('Center Mode', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__('Pause on Hover', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__('Autoplay', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__('Autoplay Speed', 'civi-framework'),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => esc_html__('Infinite Loop', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'transition',
			[
				'label' => esc_html__('Transition', 'civi-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'civi-framework'),
					'fade' => esc_html__('Fade', 'civi-framework'),
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => esc_html__('Transition Speed', 'civi-framework') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->end_controls_section();
	}

	private function register_layout_style_section()
	{
		$this->start_controls_section(
			'section_layout_style',
			[
				'label' => esc_html__('Layout', 'civi-framework'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control('content_text_align', [
			'label'        => esc_html__('Text Align', 'civi-framework'),
			'type'         => Controls_Manager::CHOOSE,
			'default'      => 'left',
			'options'      => array(
				'left'   => [
					'title' => esc_html__('Left', 'civi-framework'),
					'icon'  => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'civi-framework'),
					'icon'  => 'eicon-text-align-center',
				],
				'right'  => [
					'title' => esc_html__('Right', 'civi-framework'),
					'icon'  => 'eicon-text-align-right',
				],
			),
			'selectors'    => [
				'{{WRAPPER}} .list-cate-item' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_control(
			'overlay_color',
			[
				'label' => esc_html__('Overlay Color', 'civi-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .has-image .cate-inner:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background',
				'label' => esc_html__('Background', 'civi-framework'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .cate-inner',
			]
		);

		$this->add_responsive_control('box_padding', [
			'label' => esc_html__('Padding', 'civi-framework'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .cate-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('layout_border_radius', [
			'label' => esc_html__('Border Radius', 'civi-framework'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .civi-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .cate-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'layout_border',
				'selector' => '{{WRAPPER}} .cate-inner',
			]
		);

		$this->end_controls_section();
	}

	private function register_title_style_section()
	{
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__('Title', 'civi-framework'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label' => esc_html__('Spacing', 'civi-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cate-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'  => esc_html__('Text Color', 'civi-framework'),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cate-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'civi-framework'),
				'selector' => '{{WRAPPER}} .cate-title',
			]
		);

		$this->end_controls_section();
	}

	private function register_icon_style_section()
	{
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__('Icon', 'civi-framework'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__('Spacing', 'civi-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-position-top .icon-cate' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-position-left .icon-cate' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-position-right .icon-cate' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__('Size', 'civi-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .icon-cate' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_width',
			[
				'label' => esc_html__('Width', 'civi-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .icon-cate' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'  => esc_html__('Text Color', 'civi-framework'),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-cate' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'  => esc_html__('Background Color', 'civi-framework'),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-cate' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_count_style_section()
	{
		$this->start_controls_section(
			'section_count_style',
			[
				'label' => esc_html__('Count', 'civi-framework'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_count' => 'yes',
				],
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'  => esc_html__('Text Color', 'civi-framework'),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cate-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__('Typography', 'civi-framework'),
				'selector' => '{{WRAPPER}} .cate-count',
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$is_rtl = is_rtl();
		$direction = $is_rtl ? 'rtl' : 'ltr';
		$settings = $this->get_settings_for_display();

		//Slider
		$show_dots = (in_array($settings['navigation'], ['dots', 'both']));
		$show_arrows = (in_array($settings['navigation'], ['arrows', 'both']));

		if (empty($settings['slides_to_show_tablet'])) : $settings['slides_to_show_tablet'] = $settings['slides_to_show'];
		endif;
		if (empty($settings['slides_to_show_mobile'])) : $settings['slides_to_show_mobile'] = $settings['slides_to_show'];
		endif;
		if (empty($settings['slides_to_scroll_tablet'])) : $settings['slides_to_scroll_tablet'] = $settings['slides_to_scroll'];
		endif;
		if (empty($settings['slides_to_scroll_mobile'])) : $settings['slides_to_scroll_mobile'] = $settings['slides_to_scroll'];
		endif;

		$slick_options = [
			'"slidesToShow":' . absint($settings['slides_to_show']),
			'"slidesToScroll":' . absint($settings['slides_to_scroll']),
			'"autoplaySpeed":' . absint($settings['autoplay_speed']),
			'"autoplay":' . (('yes' === $settings['autoplay']) ? 'true' : 'false'),
			'"infinite":' . (('yes' === $settings['infinite']) ? 'true' : 'false'),
			'"pauseOnHover":' . (('yes' === $settings['pause_on_hover']) ? 'true' : 'false'),
			'"centerMode":' . (('yes' === $settings['center_mode']) ? 'true' : 'false'),
			'"speed":' . absint($settings['transition_speed']),
			'"arrows":' . ($show_arrows ? 'true' : 'false'),
			'"dots":' . ($show_dots ? 'true' : 'false'),
			'"rtl":' . ($is_rtl ? 'true' : 'false'),
			'"rows":' . absint($settings['slides_number_row']),
			'"responsive": [{ "breakpoint":567, "settings":{ "slidesToShow":' . $settings["slides_to_show_mobile"] . ', "slidesToScroll":' . $settings["slides_to_scroll_mobile"] . '}},{ "breakpoint":767, "settings":{ "slidesToShow": 2, "slidesToScroll": 2} }, { "breakpoint":1024, "settings":{ "slidesToShow":' . $settings["slides_to_show_tablet"] . ', "slidesToScroll":' . $settings["slides_to_scroll_tablet"] . ' } } ]',
		];
		$slick_data = '{' . implode(', ', $slick_options) . '}';

		if ('fade' === $settings['transition']) {
			$slick_options['fade'] = true;
		}

		$carousel_classes = ['elementor-carousel'];
		$this->add_render_attribute('slides', [
			'class' => $carousel_classes,
			'data-slider_options' => $slick_data,
		]);

        $link_cate = '#';
        if(!empty($settings['link_cate']['url'])){
            $link_cate = $settings['link_cate']['url'];
        }

?>
		<?php if ($settings['show_slider'] == 'yes') { ?>
			<div class="elementor-slick-slider" dir="<?php echo esc_attr($direction); ?>">
				<div <?php echo $this->get_render_attribute_string('slides'); ?>>
				<?php } else { ?>
					<div class="elementor-grid-jobs" dir="<?php echo esc_attr($direction); ?>">
						<div class="elementor-grid">
						<?php } ?>
						<?php foreach ($settings['categories_list'] as $categorry) {
							$item_id = $categorry['_id'];
							$item_key = 'item_' . $item_id;
							$has_icon = !empty($categorry['icon']);
							if (!$has_icon && !empty($categorry['selected_icon']['value'])) {
								$has_icon = true;
							}
							$migrated = isset($categorry['__fa4_migrated']['selected_icon']);
							$is_new = !isset($categorry['icon']) && Icons_Manager::is_migration_allowed();

							$category_slug = $categorry['category'];
							if (!empty($category_slug)) {
								$cate = get_term_by('slug', $category_slug, 'jobs-categories');
								if ($cate) {
									$term_name = $cate->name;
									$term_count = $cate->count;
									$term_link = get_term_link($cate, 'jobs-categories');
									$term_des = $cate->description;
								}
								$image_style = $has_image = '';
								if ($categorry['image']['url'] !== '') {
                                    $image_style = $categorry['image']['url'];
									$has_image = ' has-image';
								}
								$this->add_render_attribute($item_key, 'class', array(
									'list-cate-item',
									'civi-box' . $has_image,
									'elementor-repeater-item-' . $item_id,
								));
						?>
								<div <?php echo $this->get_render_attribute_string($item_key); ?>>
									<?php if ($settings['layout'] == '02') { ?>
										<div class="civi-image">
                                            <div class="image">
                                                <img src="<?php echo $image_style; ?>" alt="">
                                            </div>
										<?php } ?>
										<div class="cate-inner">
											<?php if ($has_icon && $settings['show_icon'] == 'yes') : ?>
												<span class="icon-cate">
													<?php if ($is_new || $migrated) {
														Icons_Manager::render_icon($categorry['selected_icon'], ['aria-hidden' => 'true']);
													} elseif (!empty($categorry['icon'])) {
													?><i <?php echo $this->get_render_attribute_string('i'); ?>></i><?php
																											} ?>
												<?php endif; ?>
												</span>
												<div class="cate-content">
													<?php if (!empty($term_name)) : ?>
														<h4 class="cate-title"><?php esc_html_e($term_name); ?></h4>
													<?php endif; ?>
													<?php if (!empty($term_count) && $settings['show_count'] == 'yes') : ?>
														<p class="cate-count"><?php echo sprintf(esc_html__('%s jobs', 'civi-framework'), $term_count) ?></p>
													<?php endif; ?>
													<?php
													if ($settings['show_description'] == 'yes') { ?>
														<div class="cate-des"><?php esc_html_e($term_des); ?></div>
													<?php } ?>
													<?php if ($settings['show_arrow'] == 'yes' && $settings['layout'] == '02') { ?>
														<div class="icon-arrow"><span><?php esc_html_e('Learn more','civi-framework');?></span><i class="fas fa-arrow-right"></i></div>
													<?php } ?>
												</div>
												<a class="civi-link-item" href="<?php echo esc_url($term_link) ?>"></a>
										</div>
										<?php if ($settings['layout'] == '02') { ?>
										</div>
									<?php } ?>
								</div>
						<?php }
						} ?>
						<?php if ($settings['show_list_cate'] !== '' && !empty($settings['text_cate'] && $settings['show_slider'] !== 'yes')) { ?>
							<?php if ( $settings['text_style'] === 'yes' ) :  ?>
								</div>
								<div class="list-cate-item text-style">
									<div class="cate-inner view-cate">
										<a href="<?php echo $link_cate;?>" class="civi-button button-border-bottom">
											<?php esc_html_e($settings['text_cate']) ?>
										</a>
									</div>
								</div>
							<?php else : ?>
								<div class="list-cate-item <?php if ( $settings['text_style'] === 'yes' ) { echo ' text-style'; }  ?>">
									<div class="cate-inner view-cate">
										<a href="<?php echo $link_cate;?>" class="civi-button button-border-bottom">
											<?php esc_html_e($settings['text_cate']) ?>
										</a>
									</div>
								</div>
								</div>
							<?php endif; ?>
						<?php } ?>
					</div>
			<?php }
	}
