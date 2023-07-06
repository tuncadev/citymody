<?php

/**
 * Pagination - Show numbered pagination for catalog pages.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed dcivictly
}

/**
 * @var $max_num_pages
 * @var $type
 * @var $layout
 * @var $total_post
 */
if ($max_num_pages < 1) {
    return;
}

if (empty($type)) {
    $type = 'normal';
}

if (is_post_type_archive('jobs') || is_tax(get_object_taxonomies('jobs'))) {
    $pagination_type = civi_get_option('jobs_pagination_type', 'loadmore');
} elseif (is_post_type_archive('company') || is_tax(get_object_taxonomies('company'))) {
    $pagination_type = civi_get_option('company_pagination_type', 'loadmore');
} elseif (is_post_type_archive('candidate') || is_tax(get_object_taxonomies('candidate'))) {
    $pagination_type = civi_get_option('candidate_pagination_type', 'loadmore');
}

if (!empty($layout)) {
    if ($layout == 'number') {
        $pagination_type = 'number';
    } elseif ($layout == 'loadmore') {
        $pagination_type = 'loadmore';
    }
}

global $wp_rewrite, $paged;

if (get_query_var('paged')) {
    $paged = get_query_var('paged');
} elseif (get_query_var('page')) {
    $paged = get_query_var('page');
} elseif (!empty($paged)) {
    $paged = $paged;
} else {
    $paged = 1;
}


$pagenum_link = html_entity_decode(get_pagenum_link());
$query_args = array();
$url_parts = explode('?', $pagenum_link);

$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$term_id = '';
if (!empty($current_term)) {
    $term_id = $current_term->term_id;
}
$taxonomy_name = get_query_var('taxonomy');

if (isset($url_parts[1])) {
    wp_parse_str($url_parts[1], $query_args);
}

$pagenum_link = esc_url(remove_query_arg(array_keys($query_args), $pagenum_link));
$pagenum_link = trailingslashit($pagenum_link) . '%_%';

$format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

$pages = paginate_links(apply_filters('civi_pagination_args', array(
    'base' => $pagenum_link,
    'format' => $format,
    'total' => $max_num_pages,
    'current' => $paged,
    'mid_size' => 1,
    'type' => 'array',
    'add_args' => array_map('urlencode', $query_args),
    'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'civi-framework'),
    'next_text' => __('<i class="fal fa-chevron-right"></i>', 'civi-framework'),
)));
?>

<div class="civi-pagination <?php echo esc_attr($type); ?>" data-type="<?php echo esc_attr($pagination_type); ?>">
    <?php if ($type == 'dashboard') :
    ?>
        <div class="items-pagination" data-max-number="<?php echo $total_post ?>">
            <div class="select2-field">
				<select class="search-control select-pagination civi-select2" name="item_amount">
					<option value="10" selected>10</option>
					<option value="20">20</option>
					<option value="30">30</option>
				</select>
			</div>
            <label class="text-pagination">
                <?php echo sprintf(__('<span class="num-first">1</span> - <span class="num-last">10</span> of <span class="num-total">%s</span> items', 'civi-framework'), $total_post);
                ?>
        </div>
    <?php endif; ?>

    <?php if ($pagination_type == 'number') : ?>
        <div class="pagination">
            <?php if (is_array($pages)) { ?>

                <?php foreach ($pages as $page) { ?>

                    <?php echo wp_kses_post($page); ?>

                <?php } ?>

            <?php } ?>
        </div>
    <?php endif; ?>

    <?php if ($pagination_type == 'loadmore') : ?>
        <div class="pagination loadmore">
            <a class="page-numbers next" href="#"><span><?php esc_html_e('Load More', 'civi-framework'); ?></span>
                <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
            </a>
        </div>
    <?php endif; ?>

    <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>">
    <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
    <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    <?php
    if (!empty($filters)) {
        foreach ($filters as $key => $value) {
            foreach ($value as $val) {
                echo '<input type="checkbox" class="hide" name="' . $key . '" value="' . $val . '" checked="checked">';
            }
        }
    }
    ?>
</div>
