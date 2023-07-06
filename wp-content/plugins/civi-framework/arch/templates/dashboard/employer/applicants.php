<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
$id = get_the_ID();
$posts_per_page = 10;
$action = 'submit-meetings';
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'meetings');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'meetings',
    'civi_meetings_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_applicants' => esc_html__('No meetings found', 'civi-framework'),
    )
);
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'applicants-dashboard');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'applicants-dashboard',
    'civi_applicants_dashboard_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_applicants' => esc_html__('No applicants found', 'civi-framework'),
    )
);
global $current_user;
$user_id = $current_user->ID;

$args_jobs = array(
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'author' => $user_id,
    'orderby' => 'date',
);
$data_jobs = new WP_Query($args_jobs);
$jobs_employer_id = array();
if ($data_jobs->have_posts()) {
    while ($data_jobs->have_posts()) : $data_jobs->the_post();
        $jobs_employer_id[] = get_the_ID();
    endwhile;
}

$args_applicants = array(
    'post_type' => 'applicants',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
            'value' => $jobs_employer_id,
            'compare' => 'IN'
        )
    ),
);
$data_applicants = new WP_Query($args_applicants);

$jobs_filter = array();
if ($data_applicants->have_posts()) {
    while ($data_applicants->have_posts()) : $data_applicants->the_post();
        $jobs_filter_id = get_the_ID();
        if (!empty($jobs_filter_id)) {
            $jobs_filter[] = get_post_meta($jobs_filter_id, CIVI_METABOX_PREFIX . 'applicants_jobs_id')[0];
        }
    endwhile;
}
$args_jobs_filter = array(
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'post__in' => $jobs_filter,
    'posts_per_page' => -1,
    'orderby' => 'date',
);
$data_jobs_filter = new WP_Query($args_jobs_filter);
?>

<div class="entry-my-page applicants-dashboard mettings-action-dashboard">
    <div class="entry-title">
        <h4><?php esc_html_e('All applicants', 'civi-framework') ?></h4>
    </div>
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="select2-field">
				<select class="search-control civi-select2" name="applicants_filter_jobs">
					<option value=""><?php esc_html_e('All Jobs', 'civi-framework') ?></option>
					<?php if ($data_jobs_filter->have_posts()) { ?>
						<?php while ($data_jobs_filter->have_posts()) : $data_jobs_filter->the_post(); ?>
							<option><?php esc_html_e(get_the_title()); ?></option>
						<?php endwhile; ?>
					<?php }
					wp_reset_postdata();
					?>
				</select>
			</div>
            <div class="action-search">
                <input class="search-control" type="text" name="applicants_search" placeholder="<?php esc_attr_e('Find by jobs', 'civi-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'civi-framework') ?></label>
            <div class="select2-field">
				<select class="search-control action-sorting civi-select2" name="applicants_sort_by">
					<option value="newest"><?php esc_html_e('Newest', 'civi-framework') ?></option>
					<option value="oldest"><?php esc_html_e('Oldest', 'civi-framework') ?></option>
				</select>
			</div>
        </div>
    </div>
    <?php if ($data_applicants->have_posts() && !empty($jobs_employer_id)) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="my-applicants">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Status', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Information', 'civi-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data_applicants->have_posts()) : $data_applicants->the_post(); ?>
                        <?php
                        $id = get_the_ID();
                        global $current_user;
                        wp_get_current_user();
                        $user_id = $current_user->ID;
                        $public_date = get_the_date('Y-m-d');
                        $jobs_id = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_jobs_id', true);
                        $applicants_email = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_email', true);
                        $applicants_phone = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_phone', true);
                        $applicants_message = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_message', true);
                        $applicants_cv = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_cv', true);
                        $applicants_status = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_status', true);
                        $author_id = get_post_field('post_author', $id);
                        $candidate_id = '';
                        if(!empty($author_id)){
                            $args_candidate = array(
                                'post_type' => 'candidate',
                                'posts_per_page' => 1,
                                'author' => $author_id,
                            );
                            $current_user_posts = get_posts($args_candidate);
                            $candidate_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
                            $candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                        }
                        ?>
                        <tr>
                            <td class="info-user">
                                <?php if (!empty($candidate_avatar)) : ?>
                                    <div class="image-applicants"><img class="image-candidates" src="<?php echo esc_url($candidate_avatar) ?>" alt="" /></div>
                                <?php else : ?>
                                    <div class="image-applicants"><i class="far fa-camera"></i></div>
                                <?php endif; ?>
                                <div class="info-details">
                                    <?php if (!empty(get_the_author())) { ?>
                                        <h3><a href="<?php echo get_post_permalink($candidate_id); ?>"><?php echo get_the_author(); ?></a></h3>
                                    <?php } ?>
                                    <?php if (!empty(get_the_title())) { ?>
                                        <div class="applied"><?php esc_html_e('Applied:', 'civi-framework') ?>
                                            <a href="<?php echo esc_url(get_permalink($jobs_id)); ?>" target="_blank">
                                                <span> <?php esc_html_e(get_the_title()); ?></span>
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="status">
                                <div class="approved">
                                    <?php echo civi_applicants_status($id); ?>
                                    <span class="applied-time"><?php esc_html_e('Applied:', 'civi-framework') ?><?php esc_html_e($public_date) ?></span>
                                </div>
                            </td>
                            <td class="info">
                                <?php if (!empty($applicants_email)) { ?>
                                    <span class="gmail"><?php esc_html_e($applicants_email) ?></span>
                                <?php } ?>
                                <?php if (!empty($applicants_phone)) { ?>
                                    <span class="phone"><?php esc_html_e($applicants_phone) ?></span>
                                <?php } ?>
                            </td>
                            <td class="applicants-control action-setting">
                                <div class="list-action">
                                    <a href="#" class="action icon-video tooltip btn-reschedule-meetings" data-id="<?php echo esc_attr($id); ?>" data-title="<?php esc_attr_e('Meetings', 'civi-framework') ?>"><i class="fas fa-video-plus"></i></a>
                                    <a href="#" class="action icon-messages tooltip" id="btn-mees-applicants" data-mess="<?php echo $applicants_message; ?>" data-title="<?php esc_attr_e('Messages', 'civi-framework') ?>"><i class="fab fa-facebook-messenger"></i></i></a>
                                    <a href="<?php echo esc_url($applicants_cv); ?>" class="action icon-download tooltip" data-title="<?php esc_attr_e('Download CV', 'civi-framework') ?>"><i class="fas fa-download"></i></a>
                                    <div class="action">
                                        <a href="#" class="icon-setting"><i class="fal fa-ellipsis-h"></i></a>
                                        <ul class="action-dropdown">
                                            <?php if (empty($applicants_status)) { ?>
                                                <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'civi-framework') ?></a></li>
                                                <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'civi-framework') ?></a></li>
                                                <?php } else {
                                                if ($applicants_status == 'approved') { ?>
                                                    <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'civi-framework') ?></a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'civi-framework') ?></a>
                                                    </li>
                                            <?php }
                                            } ?>
                                        </ul>
                                    </div>
                                </div>
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
    <?php $total_post = $data_applicants->found_posts;
    if ($total_post > $posts_per_page && !empty($jobs_employer_id)) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data_applicants->max_num_pages;
            civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
    <input type="hidden" name="mettings_action" value="<?php echo esc_attr($action) ?>" />
</div>
