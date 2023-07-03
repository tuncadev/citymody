<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'invite-candidate');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'invite-candidate',
    'civi_invite_candidate_vars',
    array(
        'ajax_url'    => CIVI_AJAX_URL,
        'not_candidate'   => esc_html__('No candidate found', 'civi-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$invite_candidate    = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'invite_candidate', true);

$posts_per_page = 10;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);
$invite_candidate    = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'invite_candidate', true);

$args_jobs = array(
    'posts_per_page' => -1,
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'author' => $user_id,
    'meta_query' => array(
        array(
            'key' => CIVI_METABOX_PREFIX . 'enable_jobs_package_expires',
            'value' => 0,
            'compare' => '=='
        )
    ),
);
$the_query = new WP_Query($args_jobs);
$list_jobs = array();
if ($the_query->have_posts()) {
    while ($the_query->have_posts()) :
        $the_query->the_post();
        $list_jobs[] = get_the_ID();
    endwhile;
}

$args = array(
    'post_type'           => 'candidate',
    'post__in'            => $invite_candidate,
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => $posts_per_page,
    'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
$data = new WP_Query($args);
?>

<div class="civi-invite-candidate">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="candidate_search" placeholder="<?php esc_attr_e('Find by name', 'civi-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'civi-framework') ?></label>
            <div class="select2-field">
				<select class="search-control action-sorting civi-select2" name="candidate_sort_by">
					<option value="newest"><?php esc_html_e('Newest', 'civi-framework') ?></option>
					<option value="oldest"><?php esc_html_e('Oldest', 'civi-framework') ?></option>
				</select>
			</div>
        </div>
    </div>
    <?php if ($data->have_posts() && !empty($invite_candidate)) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="invite-candidate">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'civi-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        global $post;
                        $author_id = $post->post_author;
                        $id = get_the_ID();
                        $candidate_current_position   = get_post_meta($id, CIVI_METABOX_PREFIX . 'candidate_current_position', true);
                        $candidate_locations = get_the_terms($id, 'candidate_locations');
                        $candidate_email = get_post_meta($id, CIVI_METABOX_PREFIX . 'candidate_email', true);
                        $candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                        $candidate_featured = get_post_meta($id, CIVI_METABOX_PREFIX . 'candidate_featured', true);
                        ?>
                        <tr>
                            <td class="info-user">
                                <?php if (!empty($candidate_avatar)) : ?>
                                    <img class="image-candidates" src="<?php echo esc_url($candidate_avatar) ?>" alt="" />
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
                                                <?php esc_html_e($candidate_current_position . ' / '); ?>
                                            </div>
                                        <?php } ?>
                                        <?php civi_get_salary_candidate($id, '-'); ?>
                                        <?php if (is_array($candidate_locations)) {
                                            foreach ($candidate_locations as $location) { ?>
                                                <?php esc_html_e(' / ' . $location->name); ?>
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
                                        <a href="#" class="action btn-delete tooltip" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'civi-framework') ?>"><i class="far fa-trash-alt"></i></a>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <input type="hidden" name="list_jobs" value="<?php echo esc_attr(json_encode($list_jobs)) ?>" />
            </table>
            <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page && !empty($invite_candidate)) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data->max_num_pages;
            civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
</div>
