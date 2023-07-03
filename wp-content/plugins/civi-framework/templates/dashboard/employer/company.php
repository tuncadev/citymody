<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$company_id = isset($_GET['company_id']) ? civi_clean(wp_unslash($_GET['company_id'])) : '';
if (!empty($company_id)) {
    civi_get_template('company/edit.php');
} else {
    global $current_user;
    $user_id = $current_user->ID;
    $posts_per_page = 10;
    $user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);

    wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'company-dashboard');
    wp_localize_script(
        CIVI_PLUGIN_PREFIX . 'company-dashboard',
        'civi_company_dashboard_vars',
        array(
            'ajax_url'    => CIVI_AJAX_URL,
            'not_jobs'   => esc_html__('No jobs found', 'civi-framework'),
        )
    );
    $args = array(
        'post_type'           => 'company',
        'post_status'         => array('publish','pending'),
        'posts_per_page'      => $posts_per_page,
        'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
        'author'              => $user_id,
        'orderby'               => 'date',
    );
    $data = new WP_Query($args);
?>

    <div class="entry-my-page company-dashboard">
        <div class="entry-title">
            <h4><?php esc_html_e('Companies', 'civi-framework') ?></h4>
        </div>
        <?php if ($data->have_posts()) { ?>
            <div class="table-dashboard-wapper">
                <table class="table-dashboard" id="my-company">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'civi-framework') ?></th>
                            <th><?php esc_html_e('Status', 'civi-framework') ?></th>
                            <th><?php esc_html_e('Category', 'civi-framework') ?></th>
                            <th><?php esc_html_e('Active Jobs', 'civi-framework') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($data->have_posts()) : $data->the_post();
                            $id = get_the_ID();
                            $company_location =  get_the_terms($id, 'company-location');
                            $company_categories =  get_the_terms($id, 'company-categories');
                            $status = get_post_status($id);
                            $company_logo   = get_post_meta($id, CIVI_METABOX_PREFIX . 'company_logo');
                            $meta_query = civi_posts_company($id);
                            $company_dashboard_link = civi_get_permalink('company_dashboard');
                        ?>
                            <tr>
                                <td class="info-user">
                                    <?php
                                    if (!empty($company_logo[0]['url'])) { ?>
                                        <a href="<?php echo get_the_permalink($id); ?>">
                                            <img src="<?php echo $company_logo[0]['url'] ?>" alt="<?php echo get_the_title() ?>">
                                        </a>
                                    <?php } else { ?>
                                        <div class="img-company"><i class="far fa-camera"></i></div>
                                    <?php } ?>
                                    <div class="info-details">
                                        <h3><a href="<?php echo get_the_permalink($id); ?>"><?php echo get_the_title() ?></a></h3>
                                        <p>
                                            <?php if (is_array($company_location)) : ?>
                                                <?php foreach ($company_location as $location) { ?>
                                                    <span><?php echo $location->name; ?></span>
                                                <?php } ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($status == 'publish') : ?>
                                        <span class="label label-open"><?php esc_html_e('Opening', 'civi-framework') ?></span>
                                    <?php endif; ?>
                                    <?php if ($status == 'pending') : ?>
                                        <span class="label label-pending"><?php esc_html_e('Pending', 'civi-framework') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="cate">
                                        <?php if (is_array($company_categories)) : ?>
                                            <?php foreach ($company_categories as $categories) { ?>
                                                <span><?php echo $categories->name; ?></span>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="active-jobs"><?php echo $meta_query->post_count ?></span>
                                </td>
                                <td class="action-setting company-control">
                                    <a href="#" class="icon-setting"><i class="fal fa-ellipsis-h"></i></a>
                                    <ul class="action-dropdown">
                                        <li><a class="btn-edit" href="<?php echo esc_url($company_dashboard_link); ?>?company_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'civi-framework'); ?></a></li>

                                        <?php if ($user_demo == 'yes') : ?>
                                            <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                        <?php else : ?>
                                            <li><a class="btn-delete" company-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
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
        <?php $max_num_pages = $data->max_num_pages;
        $total_post = $data->found_posts;
        if ($total_post >= $posts_per_page) { ?>
            <div class="pagination-dashboard">
                <?php civi_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
                wp_reset_postdata(); ?>
            </div>
        <?php } ?>
        <a href="<?php echo get_permalink(civi_get_option('civi_submit_company_page_id')); ?>" class="civi-button">
            <i class="far fa-plus"></i><?php esc_html_e('Add new company', 'civi-framework') ?>
        </a>
    </div>
<?php } ?>
