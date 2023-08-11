<?php

/**
 * The Template for displaying candidate archive
 */

defined('ABSPATH') || exit;
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'candidate-archive');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'candidate-archive',
    'civi_candidate_archive_vars',
    array(
        'not_candidate' => esc_html__('No candidate found', 'civi-framework'),
    )
);

$key  = isset($_GET['s']) ? civi_clean(wp_unslash($_GET['s'])) : '';
$archive_candidate_items_amount = civi_get_option('archive_candidate_items_amount', '12');
$content_candidate              = civi_get_option('archive_candidate_layout', 'layout-list');
$hide_candidate_top_filter_fields = civi_get_option('hide_candidate_top_filter_fields');
$enable_candidate_filter_top = civi_get_option('enable_candidate_filter_top');
$candidate_filter_sidebar_option = civi_get_option('candidate_filter_sidebar_option');
$content_candidate = !empty($_GET['layout']) ? Civi_Helper::civi_clean(wp_unslash($_GET['layout'])) : $content_candidate;
$candidate_filter_sidebar_option = !empty($_GET['filter']) ? Civi_Helper::civi_clean(wp_unslash($_GET['filter'])) : $candidate_filter_sidebar_option;

$enable_candidate_show_map = civi_get_option('enable_candidate_show_map');
$candidate_map_postion = civi_get_option('candidate_map_postion');
$candidate_map_postion = !empty($_GET['map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['map'])) : $candidate_map_postion;
$enable_candidate_show_map = !empty($_GET['has_map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_map'])) : $enable_candidate_show_map;

$tax_query = array();
$args = array(
    'posts_per_page'      => $archive_candidate_items_amount,
    'post_type'           => 'candidate',
    'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'tax_query'           => $tax_query,
    's'                   => $key,
    'meta_key' => 'civi-candidate_featured',
    'orderby' => 'meta_value date',
    'order' => 'DESC',
);

//Current term
$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
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

$data = new WP_Query($args);
$total_post = $data->found_posts;;

$archive_class = array();

if ($content_candidate == 'layout-list') {
    $class_view = 'list-view';
    $class_inner[] = 'layout-list';
} else {
    $class_view = 'grid-view';
    $class_inner[] = 'layout-grid';
}

$archive_class[] = $class_view;
$archive_class[] = 'content-candidate area-candidates area-archive';

if ($enable_candidate_show_map == 1) {
    $class_inner[] = 'has-map';
} else {
    $class_inner[] = 'no-map';
}
?>
<?php if ($enable_candidate_show_map == 1 && $candidate_map_postion == 'map-top') { ?>
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

<?php if ($enable_candidate_filter_top == 1) { ?>
    <?php do_action('civi_archive_candidate_top_filter', $current_term, $total_post); ?>
<?php } ?>

<div class="inner-content container <?php echo join(' ', $class_inner); ?>">
    <div class="col-left">
        <?php if ($candidate_filter_sidebar_option !== 'filter-right') {
            do_action('civi_archive_candidate_sidebar_filter', $current_term, $total_post);
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
                <div class="btn-canvas-filter <?php if ($enable_candidate_show_map != 1) { ?>hidden-lg-up<?php } ?>">
                    <a href="#"><i class="fal fa-filter"></i><?php esc_html_e('Filter', 'civi-framework'); ?></a>
                </div>
                <span class="result-count">
                    <?php if (!empty($key)) { ?>
                        <?php printf(esc_html__('%1$s candidates for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                    <?php } elseif (is_tax()) { ?>
                        <?php printf(esc_html__('%1$s candidates for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $current_term_name); ?>
                    <?php } else { ?>
                        <?php printf(esc_html__('%1$s candidates', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
                    <?php } ?>
                </span>
            </div>
            <div class="entry-right" style="width:120px">
                <div class="entry-filter">
                    <div class="civi-clear-filter hidden-lg-up">
                        <i class="far fa-sync fa-spin"></i>
                        <span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
                    </div>
                    <div class="candidate-layout switch-layout" style="display:none">
                        <a class="<?php if ($content_candidate == 'layout-grid') : echo 'active';
                                    endif; ?>" href="#" data-layout="layout-grid"><i class="far far fa-th-large icon-large"></i></a>
                        <a class="<?php if ($content_candidate == 'layout-list') : echo 'active';
                                    endif; ?>" href="#" data-layout="layout-list"><i class="far fa-list icon-large"></i></a>
                    </div>
                    <span class="text-sort-by" style="display:none"><?php esc_html_e('Sort by', 'civi-framework'); ?></span>
                    <select name="sort_by" class="sort-by filter-control civi-select2">
                        <option value="newest"><?php esc_html_e('Newest', 'civi-framework'); ?></option>
                        <option value="oldest"><?php esc_html_e('Oldest', 'civi-framework'); ?></option>
                        <option value="rating"><?php esc_html_e('Rating', 'civi-framework'); ?></option>
                    </select>
                    <?php if ($enable_candidate_show_map == 1 && $candidate_map_postion == 'map-right') { ?>
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
                    <?php printf(esc_html__('%1$s candidates for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
                <?php } elseif (is_tax()) { ?>
                    <?php printf(esc_html__('%1$s candidates for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $current_term_name); ?>
                <?php } else { ?>
                    <?php printf(esc_html__('%1$s candidates', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
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
                    <?php civi_get_template('content-candidate.php', array(
                        'candidate_layout' => $content_candidate,
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

        <?php if ($candidate_filter_sidebar_option == 'filter-right') {
            do_action('civi_archive_candidate_sidebar_filter', $current_term, $total_post);
        } ?>

    </div>
    <?php if ($enable_candidate_show_map == 1 && $candidate_map_postion == 'map-right') { ?>
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
