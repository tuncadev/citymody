<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'my-apply');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'my-apply',
    'civi_my_apply_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_jobs' => esc_html__('No jobs found', 'civi-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);
$posts_per_page = 10;

$args = array(
    'post_type' => 'applicants',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'post_status' => 'publish',
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'author' => $user_id,
);
$data = new WP_Query($args);
?>
<div class="civi-my-apply entry-my-page">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="jobs_search" placeholder="<?php esc_attr_e('Search title,description', 'civi-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'civi-framework') ?></label>
            <div class="select2-field">
				<select class="search-control action-sorting civi-select2" name="jobs_sort_by">
					<option value="newest"><?php esc_html_e('Newest', 'civi-framework') ?></option>
					<option value="oldest"><?php esc_html_e('Oldest', 'civi-framework') ?></option>
				</select>
			</div>
        </div>
    </div>
    <?php if ($data->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="my-apply">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Job Title', 'civi-framework') ?></th>
                        <th><?php /* esc_html_e('Status', 'civi-framework') */ ?></th>
                        <th><?php esc_html_e('Date Applied', 'civi-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data->have_posts()) : $data->the_post();
                        $applicants_id = get_the_ID();
                        $jobs_id = get_post_meta($applicants_id, CIVI_METABOX_PREFIX . 'applicants_jobs_id');
                        if (!empty($jobs_id)) {
                            $jobs_id = intval($jobs_id[0]);
                        }
                        global $current_user;
                        wp_get_current_user();
                        $user_id = $current_user->ID;
                        $jobs_type = wp_get_post_terms($jobs_id, 'jobs-type');
                        $jobs_categories = wp_get_post_terms($jobs_id, 'jobs-categories');
                        $jobs_location = wp_get_post_terms($jobs_id, 'jobs-location');
                        $jobs_select_company = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_company');
                        $company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
                        $company_logo = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
                        $public_date = get_the_date('Y-m-d');
                    ?>
                        <tr>
                            <td>
                                <div class="company-header">
                                    <div class="img-comnpany" style="display:none">
                                        <?php if (!empty($company_logo[0]['url'])) : ?>
                                            <img class="logo-comnpany" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                        <?php else : ?>
                                            <div class="logo-comnpany"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info-jobs">
                                        <h3 class="title-jobs-dashboard">
                                            <a href="<?php echo get_permalink($jobs_id); ?>">
                                                <?php echo get_the_title($applicants_id); ?>
                                            </a>
                                        </h3>
                                        <p>
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
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="status">
                                <?php /*echo  civi_applicants_status($applicants_id) */ ?>
                            </td>
                            <td class="table-time">
                                <span class="start-time"><?php esc_html_e($public_date); ?></span>
                            </td>
                            <?php
                            ?>
                            <td class="action-setting jobs-control">
                                <a href="#" class="icon-setting"><i class="fal fa-ellipsis-h"></i></a>
                                <ul class="action-dropdown">
                                    <?php if ($user_demo == 'yes') : ?>
                                        <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                    <?php else : ?>
                                        <li><a class="btn-delete" jobs-id="<?php echo esc_attr($applicants_id); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data->max_num_pages;
            civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>

</div>
