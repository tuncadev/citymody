<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Jobs_Animation());

class Widget_Jobs_Animation extends Widget_Base
{

    public function get_name()
    {
        return 'civi-jobs-animation';
    }

    public function get_title()
    {
        return esc_html__('Jobs Animation', 'civi-framework');
    }

    public function get_icon()
    {
        return 'civi-badge eicon-animation';
    }

    public function get_keywords()
    {
        return ['jobs'];
    }

    public function get_style_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'jobs-animation'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
        $this->register_layout_style_section();
    }

    private function add_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'civi-framework'),
        ]);

        $repeater = new Repeater();
        $options = [];
        $args_jobs = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
        );

        $data_jobs = new \WP_Query($args_jobs);
        if ($data_jobs->have_posts()) {
            while ($data_jobs->have_posts()) : $data_jobs->the_post();
                $id = get_the_id();
                $title = get_the_title($id);
                $options[$id] = $title;
            endwhile;
        }
        wp_reset_postdata();

        $repeater->add_control('title_jobs', [
            'label' => esc_html__('Title', 'civi-framework'),
            'type' => Controls_Manager::SELECT2,
            'options' => $options,
            'default' => [],
            'label_block' => true,
            'multiple' => false,
        ]);

        $repeater->add_control(
            'jobs_background',
            [
                'label' => esc_html__('Background Color', 'civi-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'jobs_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title_jobs' => esc_html__('Title #1', 'civi-framework'),
                    ],
                    [
                        'title_jobs' => esc_html__('Title #2', 'civi-framework'),
                    ],
                    [
                        'title_jobs' => esc_html__('Title #3', 'civi-framework'),
                    ],
                    [
                        'title_jobs' => esc_html__('Title #3', 'civi-framework'),
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'row',
            [
                'label' => esc_html__('Row', 'civi-framework'),
                'type' => Controls_Manager::NUMBER,
                'min' => 2,
                'max' => 5,
                'default' => 4,
                'selectors' => [
                    '{{WRAPPER}} .civi-jobs-animation' => '--civi-jobs-item: {{SIZE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'box_spacing',
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
                    '{{WRAPPER}} .civi-jobs-animation' => '--civi-jobs-spacing: {{SIZE}}{{UNIT}}',
                ],
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

        $this->add_responsive_control(
            'box_max-width',
            [
                'label' => esc_html__('Max Width', 'civi-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .civi-jobs-item' => 'max-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control('box_padding', [
            'label' => esc_html__('Padding', 'civi-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .civi-jobs-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('layout_border_radius', [
            'label' => esc_html__('Border Radius', 'civi-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .civi-jobs-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'layout_border',
                'selector' => '{{WRAPPER}} .civi-jobs-item',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'civi-jobs-animation');
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <div class="jobs-inner">
                <?php $this->print_jobs_content($settings); ?>
                <?php $this->print_jobs_content($settings); ?>
            </div>
        </div>
    <?php }


    private function print_jobs_content(array $settings){
        if (empty($settings['jobs_list'])) {
            return;
        }
        foreach ($settings['jobs_list'] as $item) {
            $item_id = $item['_id'];
            $item_key = 'item_' . $item_id;
            $jobs_id = isset($item['title_jobs']) ? $item['title_jobs'] : '';
            $jobs_location = get_the_terms($jobs_id, 'jobs-location');
            $this->add_render_attribute($item_key, 'class', array(
                'civi-jobs-item',
                'elementor-repeater-item-' . $item_id,
            ));
            ?>
            <?php if(!empty($jobs_id)){ ?>
                <div <?php echo $this->get_render_attribute_string($item_key); ?>>
                    <div class="cate-wapprer">
                        <?php if (is_array($jobs_location)) {
                            foreach ($jobs_location as $location) { ?>
                                <?php esc_html_e($location->name); ?>
                            <?php }
                        } ?>
                    </div>
                    <h3 class="jobs-title"><a href="<?php echo get_the_permalink($jobs_id) ?>"><?php echo get_the_title($jobs_id) ?></a>
                    </h3>
                    <a class="civi-link-item" href="<?php echo get_the_permalink($jobs_id) ?>"></a>
                </div>
            <?php } ?>
        <?php }
    }
}
