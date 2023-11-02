<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

defined('ABSPATH') || exit;

class Widget_Job_Search extends Base
{

	public function get_name()
	{
		return 'civi-job-search';
	}

	public function get_title()
	{
		return esc_html__('Job Search', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-search';
	}

	public function get_keywords()
	{
		return ['job', 'search'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-job-search'];
    }

	protected function register_controls()
	{
		$this->add_job_search_section();

		$this->add_box_style_section();
	}

	private function add_job_search_section()
	{
		$this->start_controls_section('job_search_section', [
			'label' => esc_html__('Job Search', 'civi'),
		]);

		$this->add_control('type', [
			'label'        => esc_html__('Type', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'jobs-categories' => esc_html__('Categories', 'civi'),
				'company' => esc_html__('Companies', 'civi'),
				'jobs-location' => esc_html__('Locations', 'civi'),
			],
			'default'      => 'categories',
		]);

		$this->add_control('image', [
			'label'   => esc_html__('Banner Image', 'civi'),
			'type'    => Controls_Manager::MEDIA,
			'dynamic' => [
				'active' => true,
			],
			'default' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		]);

		$this->add_control('title', [
			'label'       => esc_html__('Title', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('Categories', 'civi'),
		]);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__('Posts Per Page', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control('show_total_posts', [
			'label' => esc_html__('Show total posts', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$this->add_control('show_button', [
			'label' => esc_html__('Show button', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
			'separator'   => 'before',
		]);

		$this->add_control('link', [
			'label'       => esc_html__('Link', 'civi'),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'placeholder' => esc_html__('https://your-link.com', 'civi'),
			'condition' => [
				'show_button' => 'yes',
			],
		]);

		$this->add_control('button_text', [
			'label'       => esc_html__('Button Text', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('All Categories', 'civi'),
			'condition' => [
				'show_button' => 'yes',
			],
		]);

		$this->end_controls_section();
	}

	private function add_box_style_section()
	{
		$this->start_controls_section('box_style_section', [
			'label'     => esc_html__('Style', 'civi'),
			'tab'       => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('image_heading', [
			'label' => esc_html__('Image', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_responsive_control(
            'image_height',
            [
                'label' => __('Image Height', 'civi'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .thumbnail img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
            ]
        );

		$this->add_control('content_heading', [
			'label' => esc_html__('Content', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_responsive_control('content_padding', [
			'label'      => esc_html__('Content Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('title_heading', [
			'label' => esc_html__('Title', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title',
			'scheme'   => Typography::TYPOGRAPHY_1,
			'selector' => '{{WRAPPER}} .title',
		]);

		$this->add_control('title_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_responsive_control(
            'title_spacing',
            [
                'label' => __('Spacing', 'civi'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->add_control('list_heading', [
			'label' => esc_html__('List', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'list_typo',
			'scheme'   => Typography::TYPOGRAPHY_1,
			'selector' => '{{WRAPPER}} .list li a',
		]);

		$this->start_controls_tabs('list_style_tabs');

		$this->start_controls_tab('list_style_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'list',
			'selector' => '{{WRAPPER}} .list li a',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('list_style_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'list_hover',
			'selector' => '{{WRAPPER}} .list li a:hover',
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
            'list_spacing',
            [
                'label' => __('Spacing', 'civi'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .list' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->add_control('number_heading', [
			'label' => esc_html__('Number of posts', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'number_typo',
			'scheme'   => Typography::TYPOGRAPHY_1,
			'selector' => '{{WRAPPER}} .list li span',
		]);

		$this->add_control('number_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .list li span' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('button_heading', [
			'label' => esc_html__('Button', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'civi'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => esc_html__('Border Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'civi'),
			]
		);


		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label' => esc_html__('Border Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color_hover',
			[
				'label' => esc_html__('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$type = $settings['type'];
		$image = $settings['image'];
		$title = $settings['title'];
		$posts_per_page = $settings['posts_per_page'];
		$show_total_posts = $settings['show_total_posts'];
		$show_button = $settings['show_button'];
		$link = $settings['link'];
		$button_text = $settings['button_text'];
		if (!empty($link['url'])) {
			$this->add_link_attributes('job_search', $link);
		}
		?>
		<div class="job-search-box">
			<div class="inner">
				<?php if (!empty($image['url'])) : ?>
					<div class="thumbnail">
						<?php echo \Civi_Image::get_elementor_attachment([
							'settings' => $settings,
						]); ?>
					</div>
				<?php endif; ?>
				<div class="content">
					<?php
						// Show Title
						if( $title ) {
							echo sprintf('<h3 class="title">%s</h3>', $title);
						}

						// Show list job
						if( $type == 'company' ){
							$args = array(
								'post_type' => $type,
								'post_status' => 'publish',
								'posts_per_page' => $posts_per_page,
							);
							$the_query = new \WP_Query( $args );
							if( $the_query->have_posts() ){
								echo '<ul class="list">';
								while ( $the_query->have_posts() ) {
									$the_query->the_post();

									// Count Job
									$job_args = array(
										'post_type' => 'jobs',
										'post_status' => 'publish',
										'meta_query' => array(
											array(
												'key'     => CIVI_METABOX_PREFIX . 'jobs_select_company',
												'value'   => get_the_ID(),
												'compare' => '=',
											),
										),
										'posts_per_page' => -1,
									);
									$jobs = new \WP_Query( $job_args );

									echo '<li>';
									echo '<a href="' . esc_url(get_the_permalink()) . '">' . esc_html( get_the_title() ) . '</a>';
									if($show_total_posts){
										echo '<span>(' . $jobs->found_posts . ')</span>';
									}
									echo '</li>';
								}
								echo '</ul>';
							}
							wp_reset_postdata();
						} else {
							$taxonomy_terms = get_categories(
								array(
									'taxonomy' => $type,
									'orderby' => 'name',
									'order' => 'ASC',
									'hide_empty' => false,
									'parent' => 0,
									'number' => $posts_per_page
								)
							);
							if( $taxonomy_terms ){
								echo '<ul class="list">';
								foreach( $taxonomy_terms as $taxonomy_term ){
									$args = array(
										'post_type' => 'jobs',
										'post_status' => 'publish',
										'tax_query' => array(
											array(
												'taxonomy' => $type,
												'field'    => 'term_id',
												'terms'    => $taxonomy_term->term_id,
											),
										),
										'posts_per_page' => -1,
									);
									$tax_query = new \WP_Query( $args );
									echo '<li>';
									echo '<a href="' . esc_url(get_term_link($taxonomy_term->term_id)) . '">' . esc_html( $taxonomy_term->name ) . '</a>';
									if($show_total_posts){
										echo '<span>(' . $tax_query->found_posts . ')</span>';
									}
									echo '</li>';
								}
								echo '</ul>';
							}
						}

						// Show button
						if ($link['url'] !== '') {
							echo '<a ' . $this->get_render_attribute_string('job_search') . ' class="civi-button">';
							esc_html_e($button_text);
							echo '</a>';
						}
					?>
				</div>
			</div>
		</div>
		<?php
	}
}
