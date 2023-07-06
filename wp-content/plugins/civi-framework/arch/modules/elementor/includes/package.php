<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register( new Widget_Package() );

class Widget_Package extends Widget_Base
{

    public function get_name()
    {
        return 'civi-package';
    }

    public function get_title()
    {
        return esc_html__('Package', 'civi-framework');
    }

    public function get_icon()
    {
        return 'civi-badge eicon-price-table';
    }

    public function get_keywords()
    {
        return ['package'];
    }

    public function get_style_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'package'];
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

        $this->add_control('layout', [
            'label' => esc_html__('Layout', 'civi-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '01' => esc_html__('Layout 01', 'civi-framework'),
                '02' => esc_html__('Layout 02', 'civi-framework'),
            ],
            'default' => '01',
            'prefix_class' => 'civi-package-layout-',
        ]);

        $options = [];
        $args_package = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => CIVI_METABOX_PREFIX . 'package_order_display',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => CIVI_METABOX_PREFIX . 'package_visible',
                    'value' => '1',
                    'compare' => '=',
                )
            )
        );

        $data_package = new \WP_Query($args_package);
        if ($data_package->have_posts()) {
            while ($data_package->have_posts()) : $data_package->the_post();
                $id = get_the_id();
                $title = get_the_title($id);
                $options[$id] = $title;
            endwhile;
        }
        wp_reset_postdata();

        $this->add_control('title', [
            'label'       => esc_html__('Title Packge', 'civi'),
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
        $this->add_render_attribute('wrapper', 'class', 'civi-package civi-package-wrap');
        if (empty($settings['title'])) {
            return;
        }
        global $current_user;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $paid_submission_type = civi_get_option('paid_submission_type', 'no');

        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <div class="row">
                <?php
                $user_package_id = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_id', $user_id);
                foreach ($settings['title'] as $item) {
                    $include = explode(' ', $item);
                    $args = array(
                        'post_type' => 'package',
                        'posts_per_page' => -1,
                        'orderby' => 'meta_value_num',
                        'meta_key' => CIVI_METABOX_PREFIX . 'package_order_display',
                        'order' => 'ASC',
                        'post__in' => $include,
                        'meta_query' => array(
                            array(
                                'key' => CIVI_METABOX_PREFIX . 'package_visible',
                                'value' => '1',
                                'compare' => '=',
                            )
                        )
                    );
                    $data = new \WP_Query($args);
                    $total_records = $data->found_posts;
                    while ($data->have_posts()) : $data->the_post();
                        $package_id = get_the_ID();
                        $package_time_unit = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_time_unit', true);
                        $package_period = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_period', true);
                        $package_num_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_number_job', true);
                        $package_free = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_free', true);
                        if ($package_free == 1) {
                            $package_price = 0;
                        } else {
                            $package_price = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_price', true);
                        }
                        $package_unlimited_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job', true);
                        $package_unlimited_time = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_time', true);
                        $package_featured_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job_featured', true);
                        $package_num_featured_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_number_featured', true);
                        $package_featured = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_featured', true);
                        $package_additional = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_additional_details', true);
                        if ($package_additional > 0) {
                            $package_additional_text = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_details_text', true);
                        }

                        if ($package_period > 1) {
                            $package_time_unit .= 's';
                        }
                        if ($package_featured == 1) {
                            $is_featured = ' active';
                        } else {
                            $is_featured = '';
                        }
                        $civi_package = new \civi_Package();
                        $get_expired_date = $civi_package->get_expired_date($package_id, $user_id);
                        $current_date = date('Y-m-d');

                        $d1 = strtotime($get_expired_date);
                        $d2 = strtotime($current_date);

                        if ($get_expired_date === 'never expires') {
                            $d1 = 999999999999999999999999;
                        }

                        if ($user_package_id == $package_id && $d1 > $d2) {
                            $is_current = 'current';
                        } else {
                            $is_current = '';
                        }
                        $payment_link = civi_get_permalink('payment');
                        $payment_process_link = add_query_arg('package_id', $package_id, $payment_link);
                        ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="civi-package-item panel panel-default <?php echo esc_attr($is_current); ?> <?php echo esc_attr($is_featured); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="civi-package-thumbnail"><?php the_post_thumbnail(); ?></div>
                                <?php endif; ?>
                                <div class="civi-package-title">
                                    <h2 class="entry-title"><?php the_title(); ?></h2>
                                    <?php if ($package_featured == 1) { ?>
                                        <span class="recommended"><?php esc_html_e('Recommended', 'civi-framework'); ?></span>
                                    <?php } ?>
                                </div>
                                <div class="civi-package-price">
                                    <?php
                                    if ($package_price > 0) {
                                        echo civi_get_format_money($package_price, '', 2, true);
                                    } else {
                                        esc_html_e('Free', 'civi-framework');
                                    }
                                    ?>
                                </div>
                                <?php if($settings['layout'] == '02'){ ?>
                                    <div class="civi-package-choose">
                                        <?php if ($user_package_id == $package_id && $d1 > $d2) {?>
                                            <span class="civi-button button-block"><?php esc_html_e('Package Actived', 'civi-framework'); ?></span>
                                        <?php } else {?>
                                            <a href="<?php echo esc_url($payment_process_link); ?>" class="civi-button button-outline-accent button-block"><?php esc_html_e('Get Started', 'civi-framework'); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <i class="fas fa-check"></i>
                                        <span class="badge">
										<?php if ($package_unlimited_job == 1) {
                                            esc_html_e('Unlimited', 'civi-framework');
                                        } else {
                                            esc_html_e($package_num_job);
                                        } ?>
									</span>
                                        <?php esc_html_e('job posting', 'civi-framework'); ?>
                                    </li>

                                    <li class="list-group-item">
                                        <i class="fas fa-check"></i>
                                        <span class="badge">
										<?php if ($package_featured_job == 1) {
                                            esc_html_e('Unlimited', 'civi-framework');
                                        } else {
                                            esc_html_e($package_num_featured_job);
                                        } ?>
									</span>
                                        <?php esc_html_e('featured job', 'civi-framework') ?>
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-check"></i>
                                        <?php esc_html_e('Job post live for', 'civi-framework'); ?>
                                        <span class="badge">
										<?php if ($package_unlimited_time == 1) {
                                            esc_html_e('never expires', 'civi-framework');
                                        } else {
                                            echo ($package_period) . ' ' . \Civi_Package::get_time_unit($package_time_unit);
                                        }
                                        ?>
									</span>
                                    </li>
                                    <?php if ($package_additional > 0) {
                                        foreach ($package_additional_text as $value) { ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-check"></i>
                                                <span class="badge">
												<?php esc_html_e($value); ?>
											</span>
                                            </li>
                                        <?php }
                                    } ?>
                                </ul>
                                <?php
                                if($settings['layout'] == '01'){ ?>
                                    <div class="civi-package-choose">
                                        <?php if ($user_package_id == $package_id && $d1 > $d2) {?>
                                            <span class="civi-button button-block"><?php esc_html_e('Package Actived', 'civi-framework'); ?></span>
                                        <?php } else {?>
                                            <a href="<?php echo esc_url($payment_process_link); ?>" class="civi-button button-outline button-block"><?php esc_html_e('Get Started', 'civi-framework'); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endwhile;
                    wp_reset_query(); ?>
                <?php } ?>
            </div>
        </div>

        <?php
    }
}
