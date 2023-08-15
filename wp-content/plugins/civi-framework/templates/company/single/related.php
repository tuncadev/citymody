<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//global $wp_query;
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'company-related');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'company-related',
    'civi_company_related_vars',
    array(
        'ajax_url'    => CIVI_AJAX_URL,
    )
);

$company_id = get_the_ID();
$enable_single_company_related    = civi_get_option('enable_single_company_related', '1');
$posts_per_page = 4;
$args = array(
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'posts_per_page'      => $posts_per_page,
    'offset'   => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'     => CIVI_METABOX_PREFIX . 'jobs_select_company',
            'value'   => $company_id,
            'compare' => '=='
        )
    ),
);
$related  = get_posts($args);
$wp_query  = new WP_Query($args);
$language = pll_current_language( 'slug' );
?>

<?php if ($enable_single_company_related && !empty($related)) : ?>
<div class="block-archive-inner company-related-details">
	<?php if($language != "tr") { ?>
	<h4 class="title-company"><?php esc_html_e('Job at ', 'civi-framework') ?><?php the_title(); ?></h4>
	<?php  } else { ?>
	<h4 class="title-company"><?php the_title(); ?> -
		<?php esc_html_e('Job at ', 'civi-framework') ?></h4>
	<?php } ?>

	<div class="related-inner">
		<div class="related-company">
			<?php foreach ($related as $relateds) { ?>
			<?php civi_get_template('content-jobs.php', array(
                        'jobs_id' => $relateds->ID,
                        'jobs_layout' => 'layout-list',
                    )); ?>
			<?php } ?>
		</div>
		<div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
	</div>
	<?php $total_post = $wp_query->found_posts;
        $max_num_pages = $wp_query->max_num_pages;
        if ($total_post > $posts_per_page) {
            civi_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'total_post' => $total_post, 'layout' => 'number'));
            wp_reset_postdata();
        }
        ?>
	<input type="hidden" name="item_amount" value="<?php echo esc_attr($posts_per_page) ?>">
	<input type="hidden" name="company_id" value="<?php echo esc_attr($company_id) ?>">
</div>
<?php endif; ?>