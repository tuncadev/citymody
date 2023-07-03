<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user, $wpdb;
wp_get_current_user();

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'my-review');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'my-review',
    'civi_my_review_vars',
    array(
        'ajax_url'    => CIVI_AJAX_URL,
        'not_company'   => esc_html__('No company found', 'civi-framework'),
    )
);

$user_id = $current_user->ID;
$user_login = $current_user->user_login;
$paged = 1;
$posts_per_page = 10;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);
$my_reviews = $wpdb->get_results("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.user_id = $user_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC LIMIT 999");
$company_ids = array();
foreach ($my_reviews as $my_review) {
    $company_ids[] = $my_review->comment_post_ID;
}
$args = array(
    'post_type'           => 'company',
    'post__in'            => $company_ids,
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => $posts_per_page,
    'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
$data = new WP_Query($args);
?>

<div class="entry-my-page civi-my-review">
    <div class="entry-title">
        <h4><?php esc_html_e('My Reviews', 'civi-framework') ?></h4>
    </div>
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="company_search" placeholder="<?php esc_attr_e('Find by name', 'civi-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'civi-framework') ?></label>
            <div class="select2-field">
				<select class="search-control action-sorting civi-select2" name="company_sort_by">
					<option value="newest"><?php esc_html_e('Newest', 'civi-framework') ?></option>
					<option value="oldest"><?php esc_html_e('Oldest', 'civi-framework') ?></option>
				</select>
			</div>
        </div>
    </div>
    <?php if ($data->have_posts() && !empty($company_ids)) : ?>

        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="my-review">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'civi-framework') ?></th>
                        <th><?php esc_html_e('My Rating', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Review Date', 'civi-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($data->have_posts()) : $data->the_post();
                        $company_id = get_the_ID();
                        $comment = get_comments(array(
                            'post_id' => $company_id,
                        ));
                        $comment_id = '';
                        if (!empty($comment)) {
                            $comment_id = $comment[0]->comment_ID;
                        }
                        $company_categories = get_the_terms($company_id, 'company-categories');
                        $company_location = get_the_terms($company_id, 'company-location');
                        $company_logo = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
                        $rating = get_comment_meta($comment_id, 'company_rating', true);
                    ?>
                        <tr>
                            <td>
                                <div class="company-header">
                                    <div class="img-comnpany">
                                        <?php if (!empty($company_logo[0]['url'])) : ?>
                                            <img class="logo-comnpany" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                        <?php else : ?>
                                            <div class="logo-comnpany"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info-company">
                                        <h3 class="title-company-dashboard">
                                            <a href="<?php echo get_the_permalink($company_id) . '/#company-review-details' ?>">
                                                <?php echo get_the_title($company_id) ?>
                                            </a>
                                        </h3>
                                        <p>
                                            <?php if (is_array($company_categories)) {
                                                foreach ($company_categories as $categories) { ?>
                                                    <?php esc_html_e($categories->name); ?>
                                            <?php }
                                            } ?>
                                            <?php if (is_array($company_location)) {
                                                foreach ($company_location as $location) { ?>
                                                    <?php esc_html_e('/ ' . $location->name); ?>
                                            <?php }
                                            } ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="rating-count">
                                    <i class="fas fa-star"></i>
                                    <span><?php esc_html_e($rating); ?></span>
                                </span>
                            </td>
                            <td>
                                <?php echo get_comment_date('Y-m-d', $comment_id); ?>
                            </td>
                            <td class="action-setting company-control">
                                <a href="#" class="icon-setting"><i class="fal fa-ellipsis-h"></i></a>
                                <ul class="action-dropdown">
                                    <li><a class="btn-edit" href="<?php echo get_the_permalink($company_id) . '/#company-review-details' ?>"><?php esc_html_e('Edit', 'civi-framework') ?></a></li>
                                    <?php if ($user_demo == 'yes') : ?>
                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>">
                                                <?php esc_html_e('Delete', 'civi-framework'); ?>
                                            </a></li>
                                    <?php else : ?>
                                        <li><a class="btn-delete" comment-id="<?php echo esc_attr($comment_id); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
        </div>

    <?php else : ?>

        <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>

    <?php endif; ?>

    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page && !empty($company_ids)) : ?>
        <div class="pagination-dashboard">
            <?php
            $max_num_pages = $data->max_num_pages;
            civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata();
            ?>
        </div>
    <?php endif; ?>

</div>
