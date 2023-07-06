<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register( new Widget_Jobs_Apply() );

class Widget_Jobs_Apply extends Widget_Base
{

    public function get_name()
    {
        return 'civi-jobs-apply';
    }

    public function get_title()
    {
        return esc_html__('Jobs Apply', 'civi-framework');
    }

    public function get_icon()
    {
        return 'civi-badge eicon-anchor';
    }

    public function get_keywords()
    {
        return ['jobs'];
    }

    public function get_style_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'jobs-apply'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
    }

    private function add_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'civi-framework'),
        ]);

        $options = [];
        $args_jobs = array(
            'post_type'           => 'jobs',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => -1,
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

        $this->add_control('title', [
            'label'       => esc_html__('Title', 'civi-framework'),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $options,
            'default'     => [],
            'label_block' => true,
            'multiple'    => true,
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'civi-jobs-apply');
        if (empty($settings['title'])) {
            return;
        }
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <?php foreach ($settings['title'] as $item) {
                $include = explode(' ', $item);
                $the_query = new \WP_Query(array(
                    'post_type'           => 'jobs',
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => 1,
                    'posts_per_page'      => -1,
                    'post__in'            => $include,
                ));
                while ($the_query->have_posts()) : $the_query->the_post();
                    $jobs_id = get_the_ID();
                    $jobs_type = get_the_terms($jobs_id, 'jobs-type');
                    $jobs_location = get_the_terms($jobs_id, 'jobs-location');
                    $jobs_categories = get_the_terms($jobs_id, 'jobs-categories');
                    ?>
                    <div class="civi-jobs-item layout-list">
                        <div class="jobs-archive-header">
                            <div class="jobs-header-left">
                                <h3 class="jobs-title"><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a>
                                </h3>
                                <div class="cate-wapprer">
                                    <?php if (is_array($jobs_categories)) {
                                        foreach ($jobs_categories as $categories) { ?>
                                            <?php esc_html_e($categories->name); ?>
                                        <?php }
                                    } ?>
                                    <?php if (is_array($jobs_type)) {
                                        foreach ($jobs_type as $type) { ?>
                                            <?php esc_html_e('/ ' . $type->name); ?>
                                        <?php }
                                    } ?>
                                    <?php if (is_array($jobs_location)) {
                                        foreach ($jobs_location as $location) { ?>
                                            <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <div class="jobs-header-right">
                                <a href="<?php echo get_the_permalink() ?>" class="civi-button"><?php esc_html_e('Apply now', 'civi-framework')?></a>
                            </div>
                        </div>
                        <a class="civi-link-item" href="<?php echo get_the_permalink() ?>"></a>
                    </div>
                <?php endwhile;
                wp_reset_query(); ?>
            <?php } ?>
        </div>
    <?php }
}
