<?php

/**
 * layout_wrapper_start
 */
function layout_wrapper_start()
{
    $type_single_jobs = 'type-1';
    $class_layout = array('site-layout');

    if (is_single() && get_post_type() == 'company') {
        $single_company_style = civi_get_option('single_company_style');
        $single_company_style = !empty($_GET['layout']) ? civi_clean(wp_unslash($_GET['layout'])) : $single_company_style;

        if ($single_company_style == 'large-cover-img') {
            $class_layout[] = 'has-large-thumbnail';
        }
    }

    if (is_single() && get_post_type() == 'candidate') {
        $single_candidate_style = civi_get_option('single_candidate_style');
        $single_candidate_style = !empty($_GET['layout']) ? civi_clean(wp_unslash($_GET['layout'])) : $single_candidate_style;

        if ($single_candidate_style == 'large-cover-img') {
            $class_layout[] = 'has-large-thumbnail';
        }
    }


    if (is_tax() || is_archive()) {
        if (!is_author()) {
            $class_layout[] = 'has-sidebar';
        }
    }

    if (is_single() && ((get_post_type() == 'jobs') || (get_post_type() == 'company') || (get_post_type() == 'candidate'))) {
        $class_layout[] = 'has-sidebar';
    }

    if (is_author()) {
        $class_layout[] = 'has-sidebar';
    }

?>
    <div class="main-content">
        <div class="container">
            <div class="<?php echo join(' ', $class_layout); ?>">
            <?php
        }

        /**
         * layout_wrapper_end
         */
        function layout_wrapper_end()
        {
            ?>
            </div>
        </div>
    </div>
<?php
        }

        /**
         * output_content_wrapper
         */
        function output_content_wrapper_start()
        {
            civi_get_template('global/wrapper-start.php');
        }

        /**
         * output_content_wrapper
         */
        function output_content_wrapper_end()
        {
            civi_get_template('global/wrapper-end.php');
        }

        /**
         * archive jobs before
         */
        function archive_jobs_post()
        {
            civi_get_template('global/related-post.php');
        }

        /**
         * archive page title
         */
        function archive_page_title()
        {
            civi_get_template('archive-jobs/page-title.php');
        }

        /**
         * archive information
         */
        function archive_information()
        {
            civi_get_template('archive-jobs/information.php');
        }

        /**
         * archive categories
         */
        function archive_categories()
        {
            civi_get_template('archive-jobs/categories.php');
        }

        /**
         * archive map filter
         */
        function archive_map_filter()
        {
            wp_enqueue_script('google-map');
            wp_enqueue_script('markerclusterer');
            $map_type = civi_get_option('map_type', 'mapbox');
            if ($map_type == 'mapbox') {
                $mapbox_api_key = civi_get_option('mapbox_api_key');
                $map_zoom_level = civi_get_option('map_zoom_level');
                $google_map_style = civi_get_option('mapbox_style', 'streets-v11');
            } else if ($map_type == 'openstreetmap') {
                $openstreetmap_api_key = civi_get_option('openstreetmap_api_key');
                $map_zoom_level = civi_get_option('map_zoom_level');
                $openstreetmap_style = civi_get_option('openstreetmap_style', 'streets-v11');
            }
            civi_get_map_enqueue();
?>
    <div class="civi-filter-search-map">
        <div class="entry-map">
            <input id="pac-input" class="controls" type="text" placeholder="<?php esc_html_e('Search...', 'civi-framework'); ?>">
            <?php if ($map_type == 'google_map') { ?>
                <div id="jobs-map-filter" class="civi-map-filter maptype" style="width: 100%;" data-maptype="<?php echo $map_type; ?>"></div>
            <?php } else if ($map_type == 'openstreetmap') { ?>
                <div id="maps" class="civi-openstreetmap-filter maptype" style="width: 100%; height: 100%;" data-maptype="<?php echo $map_type; ?>" data-key="<?php if ($openstreetmap_api_key) {
                                                                                                                                                                    echo $openstreetmap_api_key;
                                                                                                                                                                } ?>" data-level="<?php if ($map_zoom_level) {
                                                                                                                                                                                        echo $map_zoom_level;
                                                                                                                                                                                    } ?>" data-style="<?php if ($openstreetmap_style) {
                                                                                                                                                                                                            echo $openstreetmap_style;
                                                                                                                                                                                                        } ?>"></div>
            <?php } else { ?>
                <div id="map" class="maptype" style="width: 100%; height: 100%;" data-maptype="<?php echo $map_type; ?>" data-key="<?php if ($mapbox_api_key) {
                                                                                                                                        echo $mapbox_api_key;
                                                                                                                                    } ?>" data-level="<?php if ($map_zoom_level) {
                                                                                                                                                            echo $map_zoom_level;
                                                                                                                                                        } ?>" data-type="<?php if ($google_map_style) {
                                                                                                                                                                                echo $google_map_style;
                                                                                                                                                                            } ?>"></div>
            <?php } ?>
            <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
            <div class="no-result"><span><?php esc_html_e("We didn't find any results", 'civi-framework'); ?></span>
            </div>
        </div>
    </div>
<?php
        }

        /**
         * archive jobs top filter
         */
        function archive_jobs_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'search-autocomplete');
            $jobs_search_fields = civi_get_option('jobs_search_fields');
            $jobs_search_fields_top = isset($jobs_search_fields['top']) ? $jobs_search_fields['top'] : array();
            unset($jobs_search_fields_top['__no_value__']);
            unset($jobs_search_fields_top['salary']);

            $search_color = $search_image = '';
            $enable_jobs_search_bg = civi_get_option('enable_jobs_search_bg');
            $jobs_search_color = civi_get_option('jobs_search_color');
            $jobs_search_image = civi_get_option('jobs_search_image');
            $enable_jobs_search_bg = !empty($_GET['has_bg']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_bg'])) : $enable_jobs_search_bg;
            if ($enable_jobs_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($jobs_search_color)) {
                $search_color = 'background-color :' . $jobs_search_color . ';';
            }
            if (!empty($jobs_search_image['url'])) {
                $search_image = "background-image : url({$jobs_search_image['url']})";
            }
?>
		
    <div class="archive-jobs-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_jobs_search_bg == 1) { ?> style="<?php echo $search_color . $search_image ?>" <?php } ?>>
        <div class="container">
            <h2><?php esc_html_e('Find Your Dream Jobs', 'civi-framework'); ?></h2>
            <form method="post" class="form-jobs-top-filter form-archive-top-filter">
                <div class="row">
                    <?php
                    $jobs_skills = array();
                    $taxonomy_kills = get_categories(
                        array(
                            'taxonomy' => 'jobs-skills',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_kills)) {
                        foreach ($taxonomy_kills as $term) {
                            $jobs_skills[] = $term->name;
                        }
                    }
                    $jobs_keyword = json_encode($jobs_skills);
                    ?>
                    <div class="form-group">
                        <input class="jobs-search-control archive-search-control" data-key='<?php echo $jobs_keyword ?>' id="jobs_filter_search" type="text" name="jobs_filter_search" placeholder="<?php esc_attr_e('Jobs title or keywords', 'civi-framework') ?>" value="<?php if (isset($_GET['s']) && $_GET['s'] != '') {
                                                                                                                                                                                                                                                                                echo civi_clean(wp_unslash($_GET['s']));
                                                                                                                                                                                                                                                                            } ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>

                    <?php foreach ($jobs_search_fields_top as $key_field => $field) {
                        $jobs_search_fields_icon = civi_get_option('jobs_search_fields_' . $key_field);
                        if ($key_field == 'jobs-location') { ?>
                            <div class="form-group civi-form-location">
                                <input class="archive-search-location" type="text" name="jobs-search-location" placeholder="<?php esc_attr_e('All Locations', 'civi-framework') ?>" value="<?php if (isset($_GET['jobs-location']) && $_GET['jobs-location'] != '') {
                                                                                                                                                                                                echo civi_clean(wp_unslash($_GET['jobs-location']));
                                                                                                                                                                                            } ?>">
                                <select name="<?php echo esc_attr($key_field) ?>" class="civi-select2 hide">
                                    <?php civi_get_taxonomy('jobs-location', false, false); ?>
                                </select>
                                <span class="icon-location">
                                    <span class="tooltip" data-title="<?php echo esc_attr('Your Location', 'civi-framework') ?>">
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 12C19 15.866 15.866 19 12 19C8.13401 19 5 15.866 5 12C5 8.13401 8.13401 5 12 5C15.866 5 19 8.13401 19 12Z" stroke="currentColor" stroke-width="2" />
                                            <path d="M19 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M3 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 19L12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 3L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2" />
                                        </svg>
                                    </span>
                                </span>
                                <span class="icon-arrow">
								<i class="fal fa-angle-down"></i>
                                </span>
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
								<?php echo $jobs_search_fields_icon; ?>
                                <select name="<?php echo esc_attr($key_field) ?>" class="civi-select2">
                                    <?php echo '<option value="">' . esc_html__('All ' . $field, 'civi-framework') . '</option>'; ?>
                                    <?php civi_get_taxonomy($key_field, false, false); ?>
                                </select>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <div class="form-group">
                        <span class="civi-clear-top-filter"><?php esc_html_e('Clear', 'civi-framework') ?></span>
                        <button type="submit" class="btn-top-filter civi-button" name="jobs-top-filter">
                            <?php esc_html_e('Search', 'civi-framework') ?>
                            <span class="btn-loading"><i class="fal fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive jobs sidebar filter
         */
        function archive_jobs_sidebar_filter($current_term, $total_post)
        {
            $key = isset($_GET['s']) ? civi_clean(wp_unslash($_GET['s'])) : '';
            $location = isset($_GET['jobs_']) ? civi_clean(wp_unslash($_GET['jobs_location'])) : '';
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $jobs_search_fields = civi_get_option('jobs_search_fields');
            $jobs_search_fields_sidebar = isset($jobs_search_fields['sidebar']) ? $jobs_search_fields['sidebar'] : array();
            unset($jobs_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="civi-nav-filter">
                <div class="civi-filter-toggle">
                    <span><?php esc_html_e('Filter', 'civi-framework'); ?></span>
                </div>
                <div class="civi-clear-filter">
                    <i class="far fa-sync fa-spin"></i>
                    <span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
                </div>
            </div>
            <div class="civi-menu-filter">
                <?php
                if ($jobs_search_fields_sidebar) : foreach ($jobs_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'jobs-salary':
                ?>
                                <div class="filter-salary">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Salary', 'civi-framework'); ?></h4>
                                        <div class="salary-filter">
                                            <div class="filter filter-salary-min">
                                                <input type="number" name="jobs_filter_salary_min" placeholder="<?php echo esc_attr('Min', 'civi-framework') ?>" />
                                            </div>
                                            <div class="filter filter-salary-max">
                                                <input type="number" name="jobs_filter_salary_max" placeholder="<?php echo esc_attr('Max', 'civi-framework') ?>" />
                                            </div>
                                            <select name="jobs_filter_rate" class="civi-select2">
                                                <option value=""><?php esc_html_e('Rate', 'civi-framework') ?></option>
                                                <option value="hours"><?php esc_html_e('Hours', 'civi-framework') ?></option>
                                                <option value="days"><?php esc_html_e('Days', 'civi-framework') ?></option>
                                                <option value="week"><?php esc_html_e('Week', 'civi-framework') ?></option>
                                                <option value="month"><?php esc_html_e('Month', 'civi-framework') ?></option>
                                                <option value="year"><?php esc_html_e('Year', 'civi-framework') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                <?php
                                break;

                            case 'jobs-location':
                                $title = esc_html__('Jobs Location', 'civi-framework');
                                get_search_filter_submenu('jobs-location', $title);
                                break;

                            case 'jobs-categories':
                                $title = esc_html__('Jobs Categories', 'civi-framework');
                                get_search_filter_submenu('jobs-categories', $title);
                                break;

                            case 'jobs-skills':
                                $title = esc_html__('Jobs Skills', 'civi-framework');
                                get_search_filter_submenu('jobs-skills', $title);
                                break;
																

                            case 'jobs-type':
                                $title = esc_html__('Jobs Type', 'civi-framework');
                                get_search_filter_submenu('jobs-type', $title);
                                break;

                            case 'jobs-experience':
                                $title = esc_html__('Jobs Experience', 'civi-framework');
                                get_search_filter_submenu('jobs-experience', $title);
                                break;

                            case 'jobs-career':
                                $title = esc_html__('Jobs Career', 'civi-framework');
                                get_search_filter_submenu('jobs-career', $title);
                                break;
                        }
                    }
                endif;
                ?>
            </div>
        </div>
        <div class="show-result">
            <a href="#" class="civi-button button-block">
                <span><?php echo esc_html__('Show', 'civi-framework'); ?></span>
                <span class="result-count">
                    <?php if (!empty($key)) { ?>
                        <?php printf(esc_html__('%1$s jobs for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                    <?php } else { ?>
                        <?php printf(esc_html__('%1$s jobs', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
                    <?php } ?>
                </span>
            </a>
        </div>
    </div>
    <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($jobs_search_fields_sidebar); ?>'>
    <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
    <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    <input type="hidden" name="title" value="<?php echo esc_attr($key); ?>">
    <input type="hidden" name="jobs_location" value="<?php echo esc_attr($location); ?>">
<?php
        }

        /**
         * archive company top filter
         */
        function archive_company_top_filter()
        {
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'search-autocomplete');

            $company_search_fields = civi_get_option('company_search_fields');
            $company_search_fields_top = isset($company_search_fields['top']) ? $company_search_fields['top'] : array();
            unset($company_search_fields_top['__no_value__']);

            $search_color = $search_image = '';
            $enable_company_search_bg = civi_get_option('enable_company_search_bg');
            $company_search_color = civi_get_option('company_search_color');
            $company_search_image = civi_get_option('company_search_image');
            $enable_company_search_bg = !empty($_GET['has_bg']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_bg'])) : $enable_company_search_bg;
            if ($enable_company_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($company_search_color)) {
                $search_color = 'background-color :' . $company_search_color . ';';
            }
            if (!empty($company_search_image['url'])) {
                $search_image = "background-image : url({$company_search_image['url']})";
            }
?>
    <div class="archive-company-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_company_search_bg == 1) { ?> style="<?php echo $search_color . $search_image ?>" <?php } ?>>
        <div class="container">
            <h2><?php esc_html_e('Companies Hiring Internationally', 'civi-framework'); ?></h2>
            <form method="post" class="form-company-top-filter form-archive-top-filter">
                <div class="row">
                    <?php $company_categories = array();
                    $taxonomy_categories = get_categories(
                        array(
                            'taxonomy' => 'company-categories',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_categories)) {
                        foreach ($taxonomy_categories as $term) {
                            $company_categories[] = $term->name;
                        }
                    }
                    $company_keyword = json_encode($company_categories);
                    ?>
                    <div class="form-group">
                        <input class="company-search-control archive-search-control" data-key='<?php echo $company_keyword ?>' id="company_filter_search" type="text" name="company_filter_search" placeholder="<?php esc_attr_e('Company title or keywords', 'civi-framework') ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>
                    <?php if ($company_search_fields_top) : foreach ($company_search_fields_top as $field => $v) {
                            switch ($field) {
                                case 'company-rating':
                                    $company_search_icon_ratting = civi_get_option('company_search_fields_company-rating'); ?>
                                    <div class="form-group">
                                        <select name="company-rating" class="civi-select2">
                                            <option value=""><?php echo esc_html__('All Rating', 'civi-framework'); ?></option>
                                            <option value="rating_five"><?php echo esc_html__('Five Star', 'civi-framework'); ?></option>
                                            <option value="rating_four"><?php echo esc_html__('Four Star', 'civi-framework'); ?></option>
                                            <option value="rating_three"><?php echo esc_html__('Three Star', 'civi-framework'); ?></option>
                                            <option value="rating_two"><?php echo esc_html__('Two Star', 'civi-framework'); ?></option>
                                            <option value="rating_one"><?php echo esc_html__('One Star', 'civi-framework'); ?></option>
                                        </select>
                                        <?php echo $company_search_icon_ratting; ?>
                                    </div>
                                <?php break;
                                case 'company-size':
                                    $company_search_icon_size = civi_get_option('company_search_fields_company-size');
                                ?>
                                    <div class="form-group">
                                        <select name="company-size" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Size', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('company-size', false, false); ?>
                                        </select>
                                        <?php echo $company_search_icon_size; ?>
                                    </div>
                                <?php break;
                                case 'company-location':
                                ?>
                                    <div class="form-group civi-form-location">
                                        <input class="archive-search-location" type="text" name="company-search-location" placeholder="<?php esc_attr_e('All Locations', 'civi-framework') ?>">
                                        <select name="company-location" class="civi-select2 hide">
                                            <?php civi_get_taxonomy('company-location', false, false); ?>
                                        </select>
                                        <span class="icon-location">
                                            <span class="tooltip" data-title="<?php echo esc_attr('Your Location', 'civi-framework') ?>">
                                                <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19 12C19 15.866 15.866 19 12 19C8.13401 19 5 15.866 5 12C5 8.13401 8.13401 5 12 5C15.866 5 19 8.13401 19 12Z" stroke="currentColor" stroke-width="2" />
                                                    <path d="M19 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M3 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M12 19L12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M12 3L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="icon-arrow">
										<i class="fal fa-angle-down"></i>
                                        </span>
                                    </div>
                                <?php break;
                                case 'company-categories':
                                    $company_search_icon_categories = civi_get_option('company_search_fields_company-categories');
                                ?>
                                    <div class="form-group">
                                        <select name="company-categories" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Categories', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('company-categories', false, false); ?>
                                        </select>
                                        <?php echo $company_search_icon_categories; ?>
                                    </div>
                                <?php break;
                                case 'company-founded':
                                    $company_search_icon_founded = civi_get_option('company_search_fields_company-founded');
                                ?>
                                    <div class="form-group">
                                        <select name="company-founded" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Founded', 'civi-framework') . '</option>'; ?>
                                            <?php echo civi_get_company_founded(true) ?>
                                        </select>
                                        <?php echo $company_search_icon_founded; ?>
                                    </div>
                    <?php break;
                            }
                        }
                    endif;
                    ?>

                    <div class="form-group">
                        <span class="civi-clear-top-filter"><?php esc_html_e('Clear', 'civi-framework') ?></span>
                        <button type="submit" class="btn-top-filter civi-button" name="company-top-filter">
                            <?php esc_html_e('Search', 'civi-framework') ?>
                            <span class="btn-loading"><i class="fal fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive company sidebar filter
         */
        function archive_company_sidebar_filter($current_term, $total_post)
        {
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $company_search_fields = civi_get_option('company_search_fields');
            $company_search_fields_sidebar = isset($company_search_fields['sidebar']) ? $company_search_fields['sidebar'] : array();
            unset($company_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="civi-nav-filter">
                <div class="civi-filter-toggle">
                    <span><?php esc_html_e('Filter', 'civi-framework'); ?></span>
                </div>
                <div class="civi-clear-filter">
                    <i class="far fa-sync fa-spin"></i>
                    <span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
                </div>
            </div>
            <div class="civi-menu-filter">
                <?php
                if ($company_search_fields_sidebar) : foreach ($company_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'company-rating': ?>
                                <div class="filter-rating">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Rating', 'civi-framework'); ?></h4>
                                        <ul class="rating filter-control custom-scrollbar">
                                            <li>
                                                <input type="checkbox" id="company_rating_five" class="custom-checkbox input-control" name="company_rating[]" value="rating_five" />
                                                <label for="company_rating_five">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_four" class="custom-checkbox input-control" name="company_rating[]" value="rating_four" />
                                                <label for="company_rating_four">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_three" class="custom-checkbox input-control" name="company_rating[]" value="rating_three" />
                                                <label for="company_rating_three">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_two" class="custom-checkbox input-control" name="company_rating[]" value="rating_two" />
                                                <label for="company_rating_two">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_one" class="custom-checkbox input-control" name="company_rating[]" value="rating_one" />
                                                <label for="company_rating_one">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;
                            case 'company-size':
                                $title = esc_html__('Size', 'civi-framework');
                                get_search_filter_submenu('company-size', $title);
                                break;
                            case 'company-location':
                                $title = esc_html__('Location', 'civi-framework');
                                get_search_filter_submenu('company-location', $title);
                                break;
                            case 'company-categories':
                                $title = esc_html__('Categories', 'civi-framework');
                                get_search_filter_submenu('company-categories', $title);
                                break;
                            case 'company-founded':
                            ?>
                                <div class="filter-founded">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Founded Date', 'civi-framework'); ?></h4>
                                        <div id="range-slider">
                                            <div id="slider-range"></div>
                                            <p><input type="text" id="amount" readonly></p>
                                        </div>
                                    </div>
                                </div>
                <?php
                                break;
                        }
                    }
                endif;
                ?>
            </div>
        </div>
        <div class="show-result">
            <a href="#" class="civi-button button-block">
                <span><?php echo esc_html__('Show', 'civi-framework'); ?></span>
                <span class="result-count">
                    <?php if (!empty($key)) { ?>
                        <?php printf(esc_html__('%1$s companies for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                    <?php } else { ?>
                        <?php printf(esc_html__('%1$s companies', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
                    <?php } ?>
                </span>
            </a>
        </div>
        <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($company_search_fields_sidebar); ?>'>
        <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
        <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    </div>
<?php
        }

        /**
         * Archive candidate top filter
         */
        function archive_candidate_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'search-autocomplete');
            $candidate_search_fields = civi_get_option('candidate_search_fields');
            $candidate_search_fields_top = isset($candidate_search_fields['top']) ? $candidate_search_fields['top'] : array();
            unset($candidate_search_fields_top['__no_value__']);

            $search_color = $search_image = '';
            $enable_candidate_search_bg = civi_get_option('enable_candidate_search_bg');
            $candidate_search_color = civi_get_option('candidate_search_color');
            $candidate_search_image = civi_get_option('candidate_search_image');
            $enable_candidate_search_bg = !empty($_GET['has_bg']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_bg'])) : $enable_candidate_search_bg;
            if ($enable_candidate_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($candidate_search_color)) {
                $search_color = 'background-color :' . $candidate_search_color . ';';
            }
            if (!empty($candidate_search_image['url'])) {
                $search_image = "background-image : url({$candidate_search_image['url']})";
            }
?>
	<?php if (!is_tax(array('candidate_skills', 'candidate_categories'))) { ?>
    <div class="archive-candidate-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_candidate_search_bg == 1) { ?> style="<?php echo $search_color . $search_image ?>" <?php } ?>>
        <div class="container">
				
            <h2><?php esc_html_e('Hire people for your business', 'civi-framework'); ?></h2>
            <form method="post" class="form-candidate-top-filter form-archive-top-filter">
                <div class="row">
                    <?php $candidate_categories = array();
                    $taxonomy_categories = get_categories(
                        array(
                            'taxonomy' => 'candidate_categories',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_categories)) {
                        foreach ($taxonomy_categories as $term) {
                            $candidate_categories[] = $term->name;
                        }
                    }
                    $candidate_keyword = json_encode($candidate_categories);
                    ?>
                    <div class="form-group">
                        <input class="candidate-search-control archive-search-control" data-key='<?php echo $candidate_keyword ?>' id="candidate_filter_search" type="text" name="candidate_filter_search" placeholder="<?php esc_attr_e('Candidate title or keywords', 'civi-framework') ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>

                    <?php if ($candidate_search_fields_top) : foreach ($candidate_search_fields_top as $field => $v) {
                            switch ($field) {
                                case 'candidate_rating':
                                    $candidate_search_icon_ratting = civi_get_option('candidate_search_fields_candidate_rating'); ?>
                                    <div class="form-group">
                                        <select name="candidate_rating" class="civi-select2">
                                            <option value=""><?php echo esc_html__('All Rating', 'civi-framework'); ?></option>
                                            <option value="rating_five"><?php echo esc_html__('Five Star', 'civi-framework'); ?></option>
                                            <option value="rating_four"><?php echo esc_html__('Four Star', 'civi-framework'); ?></option>
                                            <option value="rating_three"><?php echo esc_html__('Three Star', 'civi-framework'); ?></option>
                                            <option value="rating_two"><?php echo esc_html__('Two Star', 'civi-framework'); ?></option>
                                            <option value="rating_one"><?php echo esc_html__('One Star', 'civi-framework'); ?></option>
                                        </select>
                                        <?php echo $candidate_search_icon_ratting; ?>
                                    </div>
                                <?php break;
                                case 'candidate_gender':
                                    $candidate_search_icon_gender = civi_get_option('candidate_search_fields_candidate_gender'); ?>
                                    <div class="form-group">
                                        <select name="candidate_gender" class="civi-select2">
                                            <option value=""><?php echo esc_html__('All Gender', 'civi-framework'); ?></option>
                                            <option value="female"><?php echo esc_html__('Female', 'civi-framework'); ?></option>
                                            <option value="male"><?php echo esc_html__('Male', 'civi-framework'); ?></option>
                                            <option value="other"><?php echo esc_html__('Other', 'civi-framework'); ?></option>
                                        </select>
                                        <?php echo $candidate_search_icon_gender; ?>
                                    </div>
                                <?php break;
                                case 'candidate_locations': ?>
                                    <div class="form-group civi-form-location">
                                        <input class="archive-search-location" type="text" name="candidate-search-location" placeholder="<?php esc_attr_e('All Locations', 'civi-framework') ?>">
                                        <select name="candidate-location" class="civi-select2 hide">
                                            <?php civi_get_taxonomy('candidate_locations', false, false); ?>
                                        </select>
                                        <span class="icon-location">
                                            <span class="tooltip" data-title="<?php echo esc_attr('Your Location', 'civi-framework') ?>">
                                                <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19 12C19 15.866 15.866 19 12 19C8.13401 19 5 15.866 5 12C5 8.13401 8.13401 5 12 5C15.866 5 19 8.13401 19 12Z" stroke="currentColor" stroke-width="2" />
                                                    <path d="M19 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M3 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M12 19L12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M12 3L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="icon-arrow">
																					<i class="fal fa-angle-down"></i>
                                        </span>
                                    </div>
                                <?php break;
                                case 'candidate_categories':
                                    $candidate_search_icon_categories = civi_get_option('candidate_search_fields_candidate_categories');
                                ?>
                                    <div class="form-group">
                                        <select name="candidate_categories" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Categories', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('candidate_categories', false, false); ?>
                                        </select>
                                        <?php echo $candidate_search_icon_categories; ?>
                                    </div>
                                <?php break;
                                case 'candidate_yoe':
                                    $candidate_search_icon_yoe = civi_get_option('candidate_search_fields_candidate_yoe');
                                ?>
                                    <div class="form-group">
                                        <select name="candidate_yoe" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Experience', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('candidate_yoe', false, false); ?>
                                        </select>
                                        <?php echo $candidate_search_icon_yoe; ?>
                                    </div>
                                <?php break;
                                case 'candidate_qualification':
                                    $candidate_search_icon_qualification = civi_get_option('candidate_search_fields_candidate_qualification');
                                ?>
                                    <div class="form-group">
                                        <select name="candidate_qualification" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Qualification', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('candidate_qualification', false, false); ?>
                                        </select>
                                        <?php echo $candidate_search_icon_qualification; ?>
                                    </div>
                                <?php break;
                                case 'candidate_ages':
                                    $candidate_search_icon_ages = civi_get_option('candidate_search_fields_candidate_ages');
                                ?>
                                    <div class="form-group">
                                        <select name="candidate_ages" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Ages', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('candidate_ages', false, false); ?>
                                        </select>
                                        <?php echo $candidate_search_icon_ages; ?>
                                    </div>
                                <?php break;

                                case 'candidate_skills':
                                    $candidate_search_icon_skills = civi_get_option('candidate_search_fields_candidate_skills');
                                ?>
                                    <div class="form-group">
                                        <select name="candidate_skills" class="civi-select2">
                                            <?php echo '<option value="">' . esc_html__('All Skills', 'civi-framework') . '</option>'; ?>
                                            <?php civi_get_taxonomy('candidate_skills', false, false); ?>
                                        </select>
                                        <?php echo $candidate_search_icon_skills; ?>
                                    </div>
                                <?php break;

                            }
                        }
                    endif;
                    ?>
                    <div class="form-group">
                        <span class="civi-clear-top-filter"><?php esc_html_e('Clear', 'civi-framework') ?></span>
                        <button type="submit" class="btn-top-filter civi-button" name="candidate-top-filter">
                            <?php esc_html_e('Search', 'civi-framework') ?>
                            <span class="btn-loading"><i class="fal fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
		<?php } ?>
<?php }

        /**
         * archive candidate sidebar filter
         */
        function archive_candidate_sidebar_filter($current_term, $total_post)
        {
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $candidate_search_fields = civi_get_option('candidate_search_fields');
            $candidate_search_fields_sidebar = isset($candidate_search_fields['sidebar']) ? $candidate_search_fields['sidebar'] : array();
            unset($candidate_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="civi-nav-filter">
                <div class="civi-filter-toggle">
                    <span><?php esc_html_e('Filter', 'civi-framework'); ?></span>
                </div>
                <div class="civi-clear-filter">
                    <i class="far fa-sync fa-spin"></i>
                    <span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
                </div>
            </div>
            <div class="civi-menu-filter">
                <?php
                if ($candidate_search_fields_sidebar) : foreach ($candidate_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'candidate_rating': ?>
                                <div class="filter-rating">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Rating', 'civi-framework'); ?></h4>
                                        <ul class="rating filter-control custom-scrollbar">
                                            <li>
                                                <input type="checkbox" id="candidate_rating_five" class="custom-checkbox input-control" name="candidate_rating[]" value="rating_five" />
                                                <label for="candidate_rating_five">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="candidate_rating_four" class="custom-checkbox input-control" name="candidate_rating[]" value="rating_four" />
                                                <label for="candidate_rating_four">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="candidate_rating_three" class="custom-checkbox input-control" name="candidate_rating[]" value="rating_three" />
                                                <label for="candidate_rating_three">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="candidate_rating_two" class="custom-checkbox input-control" name="candidate_rating[]" value="rating_two" />
                                                <label for="candidate_rating_two">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="candidate_rating_one" class="custom-checkbox input-control" name="candidate_rating[]" value="rating_one" />
                                                <label for="candidate_rating_one">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;

                            case 'candidate_yoe':
                                $title = esc_html__('Experience Level', 'civi-framework');
                                get_search_filter_submenu('candidate_yoe', $title);
                                break;

                            case 'candidate_locations':
                                $title = esc_html__('Locations', 'civi-framework');
                                get_search_filter_submenu('candidate_locations', $title);
                                break;

                            case 'candidate_categories':
                                $title = esc_html__('Categories', 'civi-framework');
                                get_search_filter_submenu('candidate_categories', $title);
                                break;

                            case 'candidate_yoe':
                                $title = esc_html__('Experience Level', 'civi-framework');
                                get_search_filter_submenu('candidate_yoe', $title);
                                break;

                            case 'candidate_qualification':
                                $title = esc_html__('Qualification', 'civi-framework');
                                get_search_filter_submenu('candidate_qualification', $title);
                                break;

                            case 'candidate_ages':
                                $title = esc_html__('Ages', 'civi-framework');
                                get_search_filter_submenu('candidate_ages', $title);
                                break;

                            case 'candidate_skills':
                                $title = esc_html__('Skills', 'civi-framework');
                                get_search_filter_submenu('candidate_skills', $title);
                                break;

														/* Physical Attr */

														case 'candidate_height':
															$title = esc_html__('Heights', 'civi-framework');
															get_search_filter_submenu('candidate_height', $title);
															break;


														/* *********** */

													
                            case 'candidate_languages':
                                $title = esc_html__('Native Language', 'civi-framework');
                                get_search_filter_submenu('candidate_languages', $title);
                                break;

                            case 'candidate_gender':
                                $item_list_gender = array(
                                    'female' => esc_html__('Female', 'civi-framework'),
                                    'male' => esc_html__('Male', 'civi-framework'),
                                    'other' => esc_html__('Other', 'civi-framework'),
                                );
                                $item_html = '';

                                foreach ($item_list_gender as $keys => $value) {
                                    $item_html .= '<li>';
                                    $item_html .= '<input type="checkbox" class="custom-checkbox input-control" name="candidate_gender[]"' . ' value="' . $keys . '"/>';

                                    $item_html .= '<label>' . $value . '</label>';

                                    $item_html .= '</li>';
                                }

                            ?>

                                <div class="filter-gender">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Gender', 'civi-framework'); ?></h4>
                                        <ul class="filter-control custom-scrollbar candidate-gender">
                                            <?php echo $item_html; ?>
                                        </ul>
                                    </div>
                                </div>
                <?php
                                break;
                        }
                    }
                endif;
                ?>
            </div>
        </div>
        <div class="show-result">
            <a href="#" class="civi-button button-block">
                <span><?php echo esc_html__('Show', 'civi-framework'); ?></span>
                <span class="result-count">
                    <?php if (!empty($key)) { ?>
                        <?php printf(esc_html__('%1$s candidates for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                    <?php } else { ?>
                        <?php printf(esc_html__('%1$s candidates', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
                    <?php } ?>
                </span>
            </a>
        </div>
        <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($candidate_search_fields_sidebar); ?>'>
        <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
        <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    </div>
<?php
        }

        // civi_oembed_get
        function civi_oembed_get($url, $args = '')
        {
            if ($url) {
                // Manually build the IFRAME embed with the related videos option disabled and autoplay turned on
                if (preg_match("/youtube.com\/watch\?v=([^&]+)/i", $url, $aMatch)) {
                    return '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . $aMatch[1] . '?rel=0&autoplay=1&controls=0&loop=1&mute=1&disablekb=1" allowfullscreen></iframe>';
                }

                require_once(ABSPATH . WPINC . '/class-oembed.php');
                $oembed = _wp_oembed_get_object();
                return $oembed->get_html($url, $args);
            }
        }

        /**
         * sidebar jobs
         */
        function sidebar_jobs()
        {
            civi_get_template('global/sidebar-jobs.php');
        }

        /**
         * single jobs head
         */
        function single_jobs_head($job_id)
        {
            civi_get_template('jobs/single/head.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * Open Job Detail Tab
         */
        function single_jobs_tabs($job_id)
        {
            echo '<div class="tab-content">';
            civi_get_template('jobs/single/insights.php', array(
                'job_id' => $job_id,
            ));
            echo '</div>';
        }

        /**
         * single jobs meta
         */
        function single_jobs_insights($job_id)
        {
            civi_get_template('jobs/single/insights.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs short description
         */
        function single_jobs_short_description($job_id)
        {
            civi_get_template('jobs/single/short-description.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs description
         */
        function single_jobs_description($job_id)
        {
            civi_get_template('jobs/single/description.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs_thumbnai
         */
        function single_jobs_thumbnail($job_id)
        {
            civi_get_template('jobs/single/thumbnail.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs skills
         */
        function single_jobs_skills($job_id)
        {
            civi_get_template('jobs/single/skills.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs map
         */
        function single_jobs_map()
        {
            civi_get_template('jobs/single/map.php');
        }

        /**
         * single jobs video
         */
        function single_jobs_video()
        {
            civi_get_template('jobs/single/video.php');
        }

        /**
         * single jobs gallery
         */
        function gallery_jobs()
        {
            civi_get_template('jobs/single/gallery.php');
        }

        function single_jobs_additional()
        {
            civi_get_template('jobs/single/additional.php');
        }

        /**
         * single jobs apply
         */
        function single_jobs_apply($job_id)
        {
            civi_get_template('jobs/single/apply.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * related jobs
         */
        function single_jobs_related($job_id)
        {
            civi_get_template('jobs/single/related.php', array(
                'job_id' => $job_id,
            ));
        }

        //Sidebar
        /**
         * single jobs apply
         */
        function single_jobs_sidebar_apply()
        {
            civi_get_template('jobs/single/sidebar/apply.php');
        }

        function single_jobs_sidebar_insights()
        {
            civi_get_template('jobs/single/sidebar/insights.php');
        }

        function single_jobs_sidebar_company()
        {
            civi_get_template('jobs/single/sidebar/company.php');
        }

        //Company

        /**
         * sidebar company
         */
        function sidebar_company()
        {
            civi_get_template('global/sidebar-company.php');
        }

        /**
         * single company thumbnail
         */
        function single_company_thumbnail()
        {
            civi_get_template('company/single/thumbnail.php');
        }

        /**
         * single company head
         */
        function single_company_head()
        {
            civi_get_template('company/single/head.php');
        }

        /**
         * single company overview
         */
        function single_company_overview()
        {
            civi_get_template('company/single/overview.php');
        }

        /**
         * single company gallery
         */
        function single_company_gallery()
        {
            civi_get_template('company/single/gallery.php');
        }

        /**
         * single company video
         */
        function single_company_video()
        {
            civi_get_template('company/single/video.php');
        }

        /**
         * single company additional
         */
        function single_company_additional()
        {
            civi_get_template('company/single/additional.php');
        }

        /**
         * single company related
         */
        function single_company_related()
        {
            civi_get_template('company/single/related.php');
        }

        /**
         * single related review
         */
        function single_company_review()
        {
            civi_get_template('company/single/review.php');
        }

        //Company Sidebar
        /**
         * single sidebar company info
         */
        function single_company_sidebar_info()
        {
            civi_get_template('company/single/sidebar/info.php');
        }

        /**
         * single sidebar company location
         */
        function single_company_sidebar_location()
        {
            civi_get_template('company/single/sidebar/location.php');
        }


        add_filter('elementor/frontend/print_google_fonts', '__return_false');


        /**
         * single candidate thumbnail
         */
        function single_candidate_thumbnail()
        {
            civi_get_template('candidate/single/thumbnail.php');
        }

        /**
         *  Single candidate head
         */
        function single_candidate_head()
        {
            civi_get_template('candidate/single/head.php');
        }

        /**
         *  Single candidate about me
         */
        function single_candidate_about_me()
        {
            civi_get_template('candidate/single/about-me.php');
        }

        /**
         *  Single candidate photos
         */
        function single_candidate_photos()
        {
            civi_get_template('candidate/single/photos.php');
        }

        /**
         * Single Candidate video
         */
        function single_candidate_video()
        {
            civi_get_template('candidate/single/video.php');
        }
				/**
         * Single Candidate Audio
         */
				function single_candidate_audio()
        {
            civi_get_template('candidate/single/audio.php');
        }
        /**
         *  Single candidate skills
         */
        function single_candidate_skills()
        {
            civi_get_template('candidate/single/skills.php');
        }
				/**
         *  Single candidate Bank
         */
        /**
         *  Single candidate experience
         */
        function single_candidate_experience()
        {
            civi_get_template('candidate/single/experience.php');
        }

        /**
         *  Single candidate education
         */
        function single_candidate_education()
        {
            civi_get_template('candidate/single/education.php');
        }

        /**
         *  Single candidate projects
         */
        function single_candidate_projects()
        {
            civi_get_template('candidate/single/projects.php');
        }

        /**
         *  Single candidate awards
         */
        function single_candidate_awards()
        {
            civi_get_template('candidate/single/awards.php');
        }

        function single_candidate_additional()
        {
            civi_get_template('candidate/single/additional.php');
        }

        // Candidate Sidebar
        /**
         *  Single sidebar candidate info
         */
        function single_candidate_sidebar_info()
        {
            civi_get_template('candidate/single/sidebar/info.php');
        }

        /**
         *  Single sidebar candidate location
         */
        function single_candidate_sidebar_location()
        {
            civi_get_template('candidate/single/sidebar/location.php');
        }

        /**
         * sidebar Candidate
         */
        function sidebar_candidate()
        {
            civi_get_template('global/sidebar-candidate.php');
        }

        /**
         *  Single candidate cover image
         */
        function single_candidate_cover_hero()
        {
            civi_get_template('candidate/single/cover.php');
        }

        /**
         *  Single candidate review
         */
        function single_candidate_review()
        {
            civi_get_template('candidate/single/review.php');
        }
