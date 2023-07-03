<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$jobs_id = isset($_GET['jobs_id']) ? civi_clean(wp_unslash($_GET['jobs_id'])) : '';
$action = 'submit-meetings';

wp_enqueue_script('chart');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'chart');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'meetings');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'meetings',
    'civi_meetings_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_applicants' => esc_html__('No meetings found', 'civi-framework'),
    )
);
$number_days = '7';
$labels = array();
for ($i = $number_days; $i >= 0; $i--) {
    $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
    $labels[] = date('M j, Y', $date);
}

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'applicants-dashboard');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'applicants-dashboard',
    'civi_applicants_dashboard_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_applicants' => esc_html__('No applicants found', 'civi-framework'),
    )
);
$id = get_the_ID();
$posts_per_page = 10;
global $current_user;
$user_id = $current_user->ID;

$args_applicants = array(
    'post_type' => 'applicants',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
            'value' => $jobs_id,
            'compare' => '='
        )
    ),
);
$data_applicants = new WP_Query($args_applicants);
?>

<div class="entry-my-page jobs-performance-dashboard mettings-action-dashboard">
    <div class="entry-title">
        <h4><?php echo get_the_title($jobs_id); ?></h4>
    </div>
    <div class="tab-dashboard">
        <ul class="tab-list">
            <li class="tab-item"><a href="#tab-statics"><?php esc_html_e('Statics', 'civi-framework'); ?></a></li>
            <li class="tab-item "><a href="#tab-applicants"><?php esc_html_e('Applicants', 'civi-framework'); ?>
                    (<?php esc_html_e($data_applicants->found_posts) ?>)</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-statics">
                <div class="civi-chart-warpper">
                    <div class="chart-header">
                        <h4 class="title-chart"><?php esc_html_e('Job views', 'civi-framework'); ?></h4>
                        <div class="form-chart">
                            <div class="select2-field">
								<select name="chart-date" class="civi-select2">
									<option value="7"><?php esc_html_e('7 days', 'civi-framework'); ?></option>
									<option value="15"><?php esc_html_e('15 days', 'civi-framework'); ?></option>
									<option value="30"><?php esc_html_e('30 days', 'civi-framework'); ?></option>
								</select>
							</div>
                        </div>
                    </div>
                    <canvas id="civi-dashboard_chart" data-labels="<?php echo esc_attr(json_encode($labels)); ?>" data-values_view="<?php echo esc_attr(json_encode(civi_view_jobs_date($jobs_id, $number_days))); ?>" data-label_view="<?php esc_attr_e('Page View', 'civi-framework'); ?>" data-values_apply="<?php echo esc_attr(json_encode(civi_total_jobs_apply($jobs_id, $number_days))); ?>" data-label_apply="<?php esc_attr_e('Apply Click', 'civi-framework'); ?>" data-jobs-id="<?php echo $jobs_id ?>">
                    </canvas>
                    <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
                </div>
            </div>
            <div class="tab-info applicants-dashboard jobs_details" id="tab-applicants">
                <div class="search-dashboard-warpper">
                    <div class="search-left">
                        <div class="action-search">
                            <input class="search-control" type="text" name="applicants_search" placeholder="<?php esc_attr_e('Find by name', 'civi-framework') ?>">
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
                <?php if ($data_applicants->have_posts() && !empty($jobs_id)) { ?>
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
                                    $applicants_cv = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_cv', true);
                                    $applicants_status = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_status', true);
                                    $author_id = get_post_field('post_author', $id);
                                    $candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
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
                                                    <h3><?php echo get_the_author(); ?></h3>
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
                                                <a href="<?php echo esc_url($applicants_cv); ?>" class="action icon-download tooltip" data-title="Download CV"><i class="fas fa-download"></i></a>
                                                <div class="action">
                                                    <a href="#" class="icon-setting"><i class="fal fa-ellipsis-h"></i></a>
                                                    <ul class="action-dropdown">
                                                        <?php if (empty($applicants_status)) { ?>
                                                            <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'civi-framework') ?></a>
                                                            </li>
                                                            <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'civi-framework') ?></a>
                                                            </li>
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
                        <input name="applicants_jobs_id" type="hidden" value="<?php echo esc_attr($jobs_id); ?>" />
                    </div>
                <?php } else { ?>
                    <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
                <?php } ?>
                <?php $total_post = $data_applicants->found_posts;
                if ($total_post > $posts_per_page && !empty($jobs_id)) { ?>
                    <div class="pagination-dashboard">
                        <?php $max_num_pages = $data_applicants->max_num_pages;
                        civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
                        wp_reset_postdata(); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="mettings_action" value="<?php echo esc_attr($action) ?>" />
</div>

<?php function civi_reschedule_meeting()
{
    civi_get_template('jobs/meeting/reschedule.php');
}

add_action('wp_footer', 'civi_reschedule_meeting');
