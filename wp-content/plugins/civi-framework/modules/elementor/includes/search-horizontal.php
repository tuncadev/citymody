<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Search_Horizontal());

class Widget_Search_Horizontal extends Widget_Base
{

    public function get_name()
    {
        return 'civi-search-horizontal';
    }

    public function get_title()
    {
        return esc_html__('Search Horizontal PostTypes', 'civi-framework');
    }

    public function get_icon()
    {
        return 'civi-badge eicon-search';
    }

    public function get_keywords()
    {
        return ['jobs', 'companies', 'candidate', 'search'];
    }

    public function get_script_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'search-horizontal', CIVI_PLUGIN_PREFIX . 'search-location' ,'jquery-ui-autocomplete'];
    }

    public function get_style_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'search-horizontal'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
        $this->add_layout_jobs_section();
        $this->add_layout_companies_section();
        $this->add_layout_candidates_section();
        $this->add_layout_style_section();
    }

    private function add_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'civi-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout', [
            'label' => esc_html__('Layout', 'civi-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [

                '01' => esc_html__('Layout 01', 'civi-framework'),
            ],
            'default' => '01',
            'prefix_class' => 'civi-search-horizontal-layout-',
        ]);

        $this->add_control('post_type', [
            'label' => esc_html__('Post Type', 'civi-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'jobs' => esc_html__('Jobs', 'civi-framework'),
                'company' => esc_html__('Companies', 'civi-framework'),
                'candidate' => esc_html__('Candidates', 'civi-framework'),
            ],
            'default' => 'jobs',
        ]);

        $this->add_control(
            'show_popular',
            [
                'label' => esc_html__('Show Popular', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_arrow',
            [
                'label' => esc_html__('Show Arrow', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'show_clear',
            [
                'label' => esc_html__('Show Clear', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->end_controls_section();
    }

    private function add_layout_jobs_section()
    {
        $this->start_controls_section('layout_jobs', [
            'label' => esc_html__('Jobs', 'civi-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'jobs',
            ],
        ]);

        $taxonomies_jobs = array(
            "Categories" => "jobs-categories",
            "Skills" => "jobs-skills",
            "Type" => "jobs-type",
            "Location" => "jobs-location",
            "Career" => "jobs-career",
            "Experience" => "jobs-experience",
        );

        foreach ($taxonomies_jobs as $label_jobs => $jobs) {
            $this->add_control(
                'show_' . $jobs,
                [
                    'label' => esc_html__('Show ' . $label_jobs, 'civi-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $jobs, [
                'label' => esc_html__('Icon ' . $label_jobs, 'civi-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $jobs => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();

    }

    private function add_layout_companies_section()
    {
        $this->start_controls_section('layout_company', [
            'label' => esc_html__('Companies', 'civi-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'company',
            ],
        ]);

        $taxonomies_company  = array(
            "Categories" => "company-categories",
            "Location" => "company-location",
            "Size" => "company-size",
        );

        foreach ($taxonomies_company as $label_company => $company) {
            $this->add_control(
                'show_' . $company,
                [
                    'label' => esc_html__('Show ' . $label_company, 'civi-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $company, [
                'label' => esc_html__('Icon ' . $label_company, 'civi-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $company => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();

    }

    private function add_layout_candidates_section()
    {
        $this->start_controls_section('layout_candidate', [
            'label' => esc_html__('Candidates', 'civi-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'candidate',
            ],
        ]);

        $taxonomies_candidate = array(
            "Categories" => "candidate_categories",
            "Ages" => "candidate_ages",
            "Languages" => "candidate_languages",
            "Qualification" => "candidate_qualification",
            "Yoe" => "candidate_yoe",
            "Education" => "candidate_education_levels",
            "Skills" => "candidate_skills",
            "Locations" => "candidate_locations",
        );

        foreach ($taxonomies_candidate as $label_candidate => $candidate) {
            $this->add_control(
                'show_' . $candidate,
                [
                    'label' => esc_html__('Show ' . $label_candidate, 'civi-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $candidate, [
                'label' => esc_html__('Icon ' . $label_candidate, 'civi-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $candidate => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();

    }

    private function add_layout_style_section()
    {
        $this->start_controls_section('layout_style_section', [
            'label' => esc_html__('Layout', 'civi-framework'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

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
                    '{{WRAPPER}} .search-horizontal-inner' => 'max-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control('text_align', [
            'label' => esc_html__('Alignment', 'civi-framework'),
            'type' => Controls_Manager::CHOOSE,
            'options' => array(
                'left' => [
                    'title' => esc_html__('Left', 'civi-framework'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'civi-framework'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'civi-framework'),
                    'icon' => 'eicon-text-align-right',
                ],
            ),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .civi-search-horizontal' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Text Color', 'civi-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .popular-categories span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Categories Color', 'civi-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-category a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $has_arrow = '';
        if( $settings['show_arrow'] == 'yes'){
            $has_arrow = 'has-arrow';
        }
        $this->add_render_attribute('wrapper', 'class', array(
            'civi-search-horizontal',
            $has_arrow,
        ));
        if ($settings['post_type'] == 'jobs') {
            $taxonomy_key = 'jobs-skills';
            $search_placeholder = esc_attr__('Jobs title or keywords', 'civi-framework');
            $taxonomies_field = array(
                esc_html__('Locations', 'civi-framework') => "jobs-location",
                esc_html__('Categories', 'civi-framework') => "jobs-categories",
                esc_html__('Skills', 'civi-framework') => "jobs-skills",
                esc_html__('Type', 'civi-framework') => "jobs-type",
                esc_html__('Career', 'civi-framework') => "jobs-career",
                esc_html__('Experience', 'civi-framework') => "jobs-experience",
            );

        } elseif ($settings['post_type'] == 'company') {
            $taxonomy_key = 'company-categories';
            $search_placeholder = esc_attr__('Company title or keywords', 'civi-framework');
            $taxonomies_field  = array(
                esc_html__('Locations', 'civi-framework') => "company-location",
                esc_html__('Categories', 'civi-framework') => "company-categories",
                esc_html__('Size', 'civi-framework') => "company-size",
            );

        } else {
            $taxonomy_key = 'candidate_categories';
            $search_placeholder = esc_attr__('Candidate title or keywords', 'civi-framework');
            $taxonomies_field = array(
                esc_html__('Locations', 'civi-framework') => "candidate_locations",
                esc_html__('Categories', 'civi-framework') => "candidate_categories",
                esc_html__('Ages', 'civi-framework') => "candidate_ages",
                esc_html__('Languages', 'civi-framework') => "candidate_languages",
                esc_html__('Qualification', 'civi-framework') => "candidate_qualification",
                esc_html__('Yoe', 'civi-framework') => "candidate_yoe",
                esc_html__('Education', 'civi-framework') => "candidate_education_levels",
                esc_html__('Skills', 'civi-framework') => "candidate_skills",
            );
        }
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <form action="<?php echo esc_url(get_site_url()); ?>" method="get" class="form-search-horizontal">
                <div class="search-horizontal-inner">
                    <?php $key_name = array();
                    $taxonomy_post_type = get_categories(
                        array(
                            'taxonomy' => $taxonomy_key,
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_post_type)) {
                        foreach ($taxonomy_post_type as $term) {
                            $key_name[] = $term->name;
                        }
                    }
                    $post_type_keyword = json_encode($key_name);
                    ?>
                    <div class="form-group">
                        <input class="search-horizontal-control" data-key='<?php echo $post_type_keyword ?>'
                               id="search-horizontal_filter_search" type="text" name="s"
                               placeholder="<?php echo $search_placeholder; ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>

                    <?php  foreach ($taxonomies_field as $label_field => $field) {
                        if ($settings['show_' . $field]) {
                            if($field == 'jobs-location' || $field == 'company-location' || $field == 'candidate_locations'){ ?>
                                <div class="form-group civi-form-location">
                                    <input class="input-search-location" type="text" name="<?php echo $field ?>"
                                           placeholder="<?php esc_attr_e('All Locations', 'civi-framework') ?>">
                                    <select class="civi-select2">
                                        <?php civi_get_taxonomy($field, true, false); ?>
                                    </select>
                                    <span class="icon-location">
                                        <span class="tooltip" data-title="<?php echo esc_attr('Your Location', 'civi-framework') ?>">
                                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19 12C19 15.866 15.866 19 12 19C8.13401 19 5 15.866 5 12C5 8.13401 8.13401 5 12 5C15.866 5 19 8.13401 19 12Z" stroke="currentColor" stroke-width="2"/>
                                                <path d="M19 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M3 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 19L12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 3L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                         </span>
                                    </span>
                                    <span class="icon-arrow">
									<i class="fal fa-angle-down"></i>
                                    </span>
                                </div>
                            <?php } else { ?>
                                <div class="form-group">
									<?php Icons_Manager::render_icon( $settings['icon_' . $field ], [ 'aria-hidden' => 'true' ] );?>
                                    <select name="<?php echo $field ?>" class="civi-select2">
                                        <?php echo '<option value="">' . esc_html__('All ' . $label_field, 'civi-framework') . '</option>'; ?>
                                        <?php civi_get_taxonomy($field, true, false); ?>
                                    </select>
                                </div>
                            <?php } ?>
                        <?php }
                    }?>

                    <div class="form-group">
                        <?php if ($settings['show_clear'] == 'yes') { ?>
                            <span class="civi-clear-top-filter"><?php esc_html_e('Clear', 'civi-framework') ?></span>
                        <?php } ?>
                        <button type="submit" class="btn-search-horizontal civi-button">
                            <?php esc_html_e('Search', 'civi-framework') ?>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="post_type" class="post-type" value="<?php echo $settings['post_type'] ?>">
            </form>

            <?php if ($settings['show_popular'] == 'yes') { ?>
                <div class="popular-categories">
                    <span><?php esc_html_e('Popular Searches: ', 'civi-framework'); ?></span>
                    <ul class="list-category">
                        <?php
                        $taxonomy_terms = get_categories(
                            array(
                                'taxonomy' => $taxonomy_key,
                                'order' => 'DESC',
                                'orderby' => 'rand',
                                'hide_empty' => false,
                            )
                        );
                        shuffle($taxonomy_terms);

                        if (!empty($taxonomy_terms)) {
                            foreach ($taxonomy_terms as $index => $term) {
                                if ($index < 2) {
                                    $term_link = get_term_link($term);
                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url($term_link); ?>"><?php esc_html_e($term->name); ?></a>
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
    <?php }
}
