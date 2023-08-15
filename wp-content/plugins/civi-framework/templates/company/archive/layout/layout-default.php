<?php

/**
 * The Template for displaying company archive
 */

defined('ABSPATH') || exit;
$founded_min = civi_get_option('value_founded_min');
$founded_max = civi_get_option('value_founded_max');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'company-archive');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'company-archive',
    'civi_company_archive_vars',
    array(
        'not_company' => esc_html__('No company found', 'civi-framework'),
        'range_min' => $founded_min,
        'range_max' => $founded_max,
    )
);

$archive_company_items_amount = civi_get_option('archive_company_items_amount', '12');
$content_company              = civi_get_option('archive_company_layout', 'layout-list');
$hide_company_top_filter_fields = civi_get_option('hide_company_top_filter_fields');
$enable_company_filter_top = civi_get_option('enable_company_filter_top');
$company_filter_sidebar_option = civi_get_option('company_filter_sidebar_option');
$content_company = !empty($_GET['layout']) ? Civi_Helper::civi_clean(wp_unslash($_GET['layout'])) : $content_company;
$company_filter_sidebar_option = !empty($_GET['filter']) ? Civi_Helper::civi_clean(wp_unslash($_GET['filter'])) : $company_filter_sidebar_option;

$enable_company_show_map = civi_get_option('enable_company_show_map');
$company_map_postion = civi_get_option('company_map_postion');
$company_map_postion = !empty($_GET['map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['map'])) : $company_map_postion;
$enable_company_show_map = !empty($_GET['has_map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_map'])) : $enable_company_show_map;

if ($content_company == 'layout-list') {
    $class_view = 'list-view';
    $class_inner[] = 'layout-list';
} else {
    $class_view = 'grid-view';
    $class_inner[] = 'layout-grid';
}

$key          = isset($_GET['s']) ? civi_clean(wp_unslash($_GET['s'])) : '';
$archive_class   = array();
$archive_class[] = 'content-company area-company area-archive';
$archive_class[] = $class_view;

$tax_query = array();
$args = array(
    'posts_per_page'      => $archive_company_items_amount,
    'post_type'           => 'company',
    'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'tax_query'           => $tax_query,
    's'                   => $key,
    'orderby'             => 'meta_value',
);

//Current term
$company_location = isset($_GET['company-location']) ? civi_clean(wp_unslash($_GET['company-location'])) : '';
if (!empty($company_location)) {
    $current_term = get_term_by('slug', $company_location, get_query_var('taxonomy'));
} else {
    $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
}
$current_term_name = '';
if (!empty($current_term)) {
    $current_term_name = $current_term->name;
}
if (is_tax() && !empty($current_term)) {
    $taxonomy_title = $current_term->name;
    $taxonomy_name = get_query_var('taxonomy');
    if (!empty($taxonomy_name)) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy_name,
            'field' => 'slug',
            'terms' => $current_term->slug
        );
    }
}

$tax_count = count($tax_query);
if ($tax_count > 0) {
    $args['tax_query'] = array(
        'relation' => 'AND',
        $tax_query
    );
}
$data       = new WP_Query($args);
$total_post = $data->found_posts;;

if ($enable_company_show_map == 1) {
    $class_inner[] = 'has-map';
} else {
    $class_inner[] = 'no-map';
}
?>
<?php if ($enable_company_show_map == 1 && $company_map_postion == 'map-top') { ?>
    <div class="col-right">
        <?php
        /**
         * @Hook: civi_archive_map_filter
         *
         * @hooked archive_map_filter
         */
        do_action('civi_archive_map_filter');
        ?>
    </div>
<?php } ?>

<?php if ($enable_company_filter_top == 1) { ?>
    <?php do_action('civi_archive_company_top_filter', $current_term, $total_post); ?>
<?php } ?>

<div class="inner-content container <?php echo join(' ', $class_inner); ?>">
    <div class="col-left">
        <?php if ($company_filter_sidebar_option !== 'filter-right') {
            do_action('civi_archive_company_sidebar_filter', $current_term, $total_post);
        } ?>

        <?php
        /**
         * @Hook: civi_output_content_wrapper_start
         *
         * @hooked output_content_wrapper_start
         */
        do_action('civi_output_content_wrapper_start');
        ?>

        <div class="filter-warpper">
            <div class="entry-left">
                <div class="btn-canvas-filter <?php if ($enable_company_show_map != 1) { ?>hidden-lg-up<?php } ?>">
                    <a href="#"><i class="fal fa-filter"></i><?php esc_html_e('Filter', 'civi-framework'); ?></a>
                </div>
                <span class="result-count">
                    <?php if (!empty($key)) { ?>
                        <?php printf(esc_html__('%1$s companies for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                    <?php } elseif (is_tax()) { ?>
                        <?php printf(esc_html__('%1$s companies for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $current_term_name); ?>
                    <?php } else { ?>
                        <?php printf(esc_html__('%1$s companies', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
                    <?php } ?>
                </span>
            </div>
            <div class="entry-right">
                <div class="entry-filter">
                    <div class="civi-clear-filter hidden-lg-up">
                        <i class="far fa-sync fa-spin"></i>
                        <span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
                    </div>
                    <div class="company-layout switch-layout">
                        <a class="<?php if ($content_company == 'layout-grid') : echo 'active';
                                    endif; ?>" href="#" data-layout="layout-grid"><i class="far far fa-th-large icon-large"></i></a>
                        <a class="<?php if ($content_company == 'layout-list') : echo 'active';
                                    endif; ?>" href="#" data-layout="layout-list"><i class="far fa-list icon-large"></i></a>
                    </div>
                    <span class="text-sort-by"  style="display:none;"><?php esc_html_e('Sort by', 'civi-framework'); ?></span>
                    <select name="sort_by" class="sort-by filter-control civi-select2">
                        <option value="newest"><?php esc_html_e('Newest', 'civi-framework'); ?></option>
                        <option value="oldest"><?php esc_html_e('Oldest', 'civi-framework'); ?></option>
                        <option value="rating"><?php esc_html_e('Rating', 'civi-framework'); ?></option>
                    </select>
                    <?php if ($enable_company_show_map == 1 && $company_map_postion == 'map-right') { ?>
                        <div class="btn-control btn-switch btn-hide-map">
                            <span class="text-switch"><?php esc_html_e('Map', 'civi-framework'); ?></span>
                            <label class="switch">
                                <input type="checkbox" value="hide_map">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="entry-mobie">
            <span class="result-count">
                <?php if (!empty($key)) { ?>
                    <?php printf(esc_html__('%1$s companies for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                <?php } elseif (is_tax()) { ?>
                    <?php printf(esc_html__('%1$s companies for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $current_term_name); ?>
                <?php } else { ?>
                    <?php printf(esc_html__('%1$s companies', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
                <?php } ?>
            </span>
            <div class="civi-clear-filter hidden-lg-up">
                <i class="far fa-sync fa-spin"></i>
                <span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
            </div>
        </div>

        <div class="<?php echo join(' ', $archive_class); ?>">
            <?php if ($data->have_posts()) { ?>
                <?php while ($data->have_posts()) : $data->the_post(); ?>
                    <?php civi_get_template('content-company.php', array(
                        'company_layout' => $content_company,
                    )); ?>
                <?php endwhile; ?>
            <?php } else { ?>
                <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
            <?php } ?>
        </div>

        <?php
        $max_num_pages = $data->max_num_pages;
        civi_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call'));
        wp_reset_postdata();
        ?>
        <?php
        /**
         * @Hook: civi_output_content_wrapper_end
         *
         * @hooked output_content_wrapper_end
         */
        do_action('civi_output_content_wrapper_end');
        ?>

        <?php if ($company_filter_sidebar_option == 'filter-right' && $enable_company_show_map != 1) {
            do_action('civi_archive_company_sidebar_filter', $current_term, $total_post);
        } ?>

    </div>
    <?php if ($enable_company_show_map == 1 && $company_map_postion == 'map-right') { ?>
        <div class="col-right">
            <?php
            /**
             * @Hook: civi_archive_map_filter
             *
             * @hooked archive_map_filter
             */
            do_action('civi_archive_map_filter');
            ?>
        </div>
    <?php } ?>
</div>
