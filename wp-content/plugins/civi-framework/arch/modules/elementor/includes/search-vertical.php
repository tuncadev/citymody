<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Search_Vertical());

class Widget_Search_Vertical extends Widget_Base
{

    public function get_name()
    {
        return 'civi-search-vertical';
    }

    public function get_title()
    {
        return esc_html__('Search Vertical PostTypes', 'civi-framework');
    }

    public function get_icon()
    {
        return 'civi-badge eicon-search-results';
    }

    public function get_keywords()
    {
        return ['jobs', 'companies', 'candidate', 'search'];
    }

    public function get_script_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'search-vertical',CIVI_PLUGIN_PREFIX . 'search-location','jquery-ui-autocomplete'];
    }

    public function get_style_depends()
    {
        return [CIVI_PLUGIN_PREFIX . 'search-vertical'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
        $this->add_layout_jobs_section();
        $this->add_layout_companies_section();
        $this->add_layout_candidates_section();
        $this->add_layout_style_section();
        $this->add_nav_style_section();
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
            'prefix_class' => 'civi-search-vertical-layout-',
        ]);

        $this->add_control(
            'show_jobs',
            [
                'label' => esc_html__('Show Jobs', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_company',
            [
                'label' => esc_html__('Show Company', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_candidate',
            [
                'label' => esc_html__('Show Candidate', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
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
                'show_jobs' => 'yes',
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
                'show_company' => 'yes',
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
                'show_candidate' => 'yes',
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
                    '{{WRAPPER}} .civi-search-vertical' => 'max-width: {{SIZE}}{{UNIT}}',
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
            'selectors_dictionary' => [
                'left' => 'margin-right: auto',
                'center' => 'margin: 0 auto',
                'right' => 'margin-left: auto',
            ],
            'selectors' => [
                '{{WRAPPER}} .civi-search-vertical' => '{{VALUE}}',
            ],
        ]);

        $this->add_control('box_border_radius', [
            'label' => esc_html__('Border Radius', 'civi-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .civi-search-vertical' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('box_padding', [
            'label' => esc_html__('Padding Box', 'civi-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .civi-search-vertical' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function add_nav_style_section()
    {
        $this->start_controls_section('nav_style_section', [
            'label' => esc_html__('Nav', 'civi-framework'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('nav_text_align', [
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
                '{{WRAPPER}} .tab-dashboard' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_control(
            'nav_spacing',
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
                    '{{WRAPPER}} .tab-list' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'nav_title',
            'selector' => '{{WRAPPER}} .tab-item a',
        ]);

        $this->add_control(
            'nav_color',
            [
                'label' => esc_html__('Text Color', 'civi-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tab-item a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'nav_color_active',
            [
                'label' => esc_html__('Active Color', 'civi-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tab-dashboard .tab-item.active a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tab-dashboard .tab-item:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'civi-search-vertical');
        if ($settings['show_jobs'] == 'yes') {
            $taxonomy_key_jobs = 'jobs-skills';
            $search_placeholder_jobs = esc_attr__('Jobs title or keywords', 'civi-framework');
            $taxonomies_field_jobs = array(
                esc_html__('Locations', 'civi-framework') => "jobs-location",
                esc_html__('Categories', 'civi-framework') => "jobs-categories",
                esc_html__('Skills', 'civi-framework') => "jobs-skills",
                esc_html__('Type', 'civi-framework') => "jobs-type",
                esc_html__('Career', 'civi-framework') => "jobs-career",
                esc_html__('Experience', 'civi-framework') => "jobs-experience",
            );
        }
        if ($settings['show_company'] == 'yes') {
            $taxonomy_key_company = 'company-categories';
            $search_placeholder_company = esc_attr__('Company title or keywords', 'civi-framework');
            $taxonomies_field_company  = array(
                esc_html__('Locations', 'civi-framework') => "company-location",
                esc_html__('Categories', 'civi-framework') => "company-categories",
                esc_html__('Size', 'civi-framework') => "company-size",
            );
        }
        if ($settings['show_candidate'] == 'yes') {
            $taxonomy_key_candidate = 'candidate_categories';
            $search_placeholder_candidate = esc_attr__('Candidate title or keywords', 'civi-framework');
            $taxonomies_field_candidate = array(
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
            <div class="tab-post-type tab-dashboard">
                <ul class="tab-list">
                    <?php  if ($settings['show_jobs'] == 'yes') { ?>
                        <li class="tab-item tab-jobs-item"><a href="#tab-jobs"><?php esc_html_e('For Jobs', 'civi-framework'); ?></a></li>
                    <?php } ?>
                    <?php  if ($settings['show_company'] == 'yes') { ?>
                        <li class="tab-item tab-company-item"><a href="#tab-company"><?php esc_html_e('For Companies', 'civi-framework'); ?></a></li>
                    <?php } ?>
                    <?php  if ($settings['show_candidate'] == 'yes') { ?>
                        <li class="tab-item tab-candidate-item"><a href="#tab-candidate"><?php esc_html_e('For Candidates', 'civi-framework'); ?></a></li>
                    <?php } ?>
                </ul>
                <div class="tab-content">
                    <?php  if ($settings['show_jobs'] == 'yes') { ?>
                        <div class="tab-info" id="tab-jobs">
                            <?php $this->print_content_form('jobs',$settings,$taxonomy_key_jobs,$search_placeholder_jobs,$taxonomies_field_jobs); ?>
                        </div>
                    <?php } ?>
                    <?php  if ($settings['show_company'] == 'yes') { ?>
                        <div class="tab-info" id="tab-company">
                            <?php $this->print_content_form('company',$settings,$taxonomy_key_company,$search_placeholder_company,$taxonomies_field_company); ?>
                        </div>
                    <?php } ?>
                    <?php  if ($settings['show_candidate'] == 'yes') { ?>
                        <div class="tab-info" id="tab-candidate">
                            <?php $this->print_content_form('candidate',$settings,$taxonomy_key_candidate,$search_placeholder_candidate,$taxonomies_field_candidate); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php }

    private function print_content_form($post_type,array $settings,$taxonomy_key,$search_placeholder,$taxonomies_field){ ?>
        <form action="<?php echo esc_url(get_site_url()); ?>" method="get" class="form-search-vertical">
            <div class="search-vertical-inner">
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
                    <input class="search-vertical-<?php echo $post_type;?>" data-key='<?php echo $post_type_keyword ?>' type="text" name="s"
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
                    <button type="submit" class="btn-search-vertical civi-button">
                        <?php esc_html_e('Search', 'civi-framework') ?>
                    </button>
                </div>
            </div>
            <input type="hidden" name="post_type" class="post-type" value="<?php echo $post_type;?>">
        </form>
    <?php }
}
