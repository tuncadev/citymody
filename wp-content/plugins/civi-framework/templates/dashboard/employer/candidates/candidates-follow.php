<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'candidates-dashboard');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'candidates-dashboard',
    'civi_candidates_dashboard_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_candidates' => esc_html__('No candidates found', 'civi-framework'),
    )
);
global $current_user;
$user_id = $current_user->ID;
$id = get_the_ID();
$posts_per_page = 10;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);

$args_company = array(
    'post_type' => 'company',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'author' => $user_id,
);
$data_company = new WP_Query($args_company);
$jobs_company_id = array();
if ($data_company->have_posts()) {
    while ($data_company->have_posts()) : $data_company->the_post();
        $jobs_company_id[] = get_the_ID();
    endwhile;
}
$users_candidates = get_users(array('role__in' => array('civi_user_candidate')));
$user_follow = $company = array();
foreach ($users_candidates as $user) {
    $my_follow = get_user_meta($user->ID, CIVI_METABOX_PREFIX . 'my_follow', true);
    if (is_array($my_follow)) {
        $check_company = array_intersect($my_follow, $jobs_company_id);
        if (is_array($check_company) && !empty($check_company)) {
            array_push($user_follow, $user->ID);
            array_push($company, $check_company);
        }
    }
}
$user_follow_company = array_combine($user_follow, $company);
$user_follow = implode(',', $user_follow);
$args_candidates = array(
    'post_type' => 'candidate',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'author' => $user_follow,
);
$data_candidates = new WP_Query($args_candidates);
$candidates_id = array();
if ($data_candidates->have_posts()) {
    while ($data_candidates->have_posts()) : $data_candidates->the_post();
        $candidates_id[] = get_the_ID();
    endwhile;
}
$args = array(
    'post_type' => 'candidate',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
if (!empty($user_follow)) {
    $args['post__in'] = $candidates_id;
}
$data = new WP_Query($args);

?>
<div class="candidates-dashboard">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="candidates_search" placeholder="<?php esc_attr_e('Find by name', 'civi-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'civi-framework') ?></label>
            <div class="select2-field">
				<select class="search-control action-sorting civi-select2" name="candidates_sort_by">
					<option value="newest"><?php esc_html_e('Newest', 'civi-framework') ?></option>
					<option value="oldest"><?php esc_html_e('Oldest', 'civi-framework') ?></option>
				</select>
			</div>
        </div>
    </div>
    <?php if ($data->have_posts() && !empty($candidates_id) && !empty($user_follow)) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="candidates-db">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'civi-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $list_id_candidates = array();
                    while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        global $post;
                        $author_id = $post->post_author;
                        $id = get_the_ID();
                        $list_id_candidates[] = $id;
                        $candidate_current_position   = get_post_meta($id, CIVI_METABOX_PREFIX . 'candidate_current_position', true);
                        $candidate_locations = get_the_terms($id, 'candidate_locations');
                        $candidate_email = get_post_meta($id, CIVI_METABOX_PREFIX . 'candidate_email', true);
                        $candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                        $candidate_featured = get_post_meta($id, CIVI_METABOX_PREFIX . 'candidate_featured', true);
                        $user_follow_athour = '';
                        if (!empty($user_follow_company[$author_id])) {
                            $user_follow_athour = implode(',', $user_follow_company[$author_id]);
                        }
                        ?>
                        <tr>
                            <td class="info-user">
                                <?php if (!empty($candidate_avatar)) : ?>
                                    <img class="image-candidates" src="<?php echo esc_attr($candidate_avatar) ?>" alt="" />
                                <?php else : ?>
                                    <div class="image-candidates"><i class="far fa-camera"></i></div>
                                <?php endif; ?>
                                <div class="info-details">
                                    <h3>
                                        <a href="<?php echo esc_url(get_the_permalink($id)); ?>"><?php esc_html_e(get_the_title($id)); ?></a>
                                        <?php if ($candidate_featured == 1) : ?>
                                            <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'civi-framework') ?>"><i class="fas fa-check"></i></span>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="cate-info">
                                        <?php if (!empty($candidate_current_position)) { ?>
                                            <div class="candidate-current-position">
                                                <?php esc_html_e($candidate_current_position . ' /'); ?>
                                            </div>
                                        <?php } ?>
                                        <?php civi_get_salary_candidate($id, '-'); ?>
                                        <?php if (is_array($candidate_locations)) {
                                            foreach ($candidate_locations as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            </td>
                            <td class="action-setting">
                                <div class="list-action">
                                    <a href="<?php echo esc_url(get_the_permalink($id)); ?>" target="_blank" class="action icon-view tooltip" data-title="<?php echo esc_attr('View', 'civi-framework') ?>"><i class="far fa-eye"></i></i></a>
                                    <a href="mailto: <?php esc_html_e($candidate_email); ?>" class="action icon-gmail tooltip" data-title="<?php echo esc_attr('Send Email', 'civi-framework') ?>"><i class="far fa-envelope-open-text"></i></a>

                                    <?php if ($user_demo == 'yes') : ?>
                                        <a href="#" class="btn-add-to-message action tooltip" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>" data-title="<?php echo esc_attr('Delete', 'civi-framework') ?>"><i class="far fa-trash-alt"></i></a>
                                        </a>
                                    <?php else : ?>
                                        <a href="#" class="action btn-delete tooltip" athour-id="<?php echo esc_attr($author_id) ?>" follow_company="<?php echo $user_follow_athour; ?>" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'civi-framework') ?>"><i class="far fa-trash-alt"></i></a>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <input type="hidden" name="candidates_id" value="<?php echo implode(',', $candidates_id); ?>">
            </table>
            <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page && !empty($user_follow)) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data->max_num_pages;
            civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
</div>
