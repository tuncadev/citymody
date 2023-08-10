<?php
$key_employer = array(
    "dashboard" => esc_html__('Dashboard', 'civi-framework'),
    "jobs_dashboard" => esc_html__('Jobs', 'civi-framework'),
    "applicants" => esc_html__('Applicants', 'civi-framework'),
    "candidates" => esc_html__('Candidates', 'civi-framework'),
    "user_package" => esc_html__('Package', 'civi-framework'),
   // "messages" => esc_html__('Messages', 'civi-framework'),
   // "meetings" => esc_html__('Meetings', 'civi-framework'),
    "company" => esc_html__('Company', 'civi-framework'),
    "settings" =>  esc_html__('Settings', 'civi-framework'),
    "logout" =>  esc_html__('Logout', 'civi-framework'),
);
$current_user = wp_get_current_user();
$enable_post_your = civi_get_option('show_employer_jobs_post_your');
?>
<div class="nav-dashboard-inner">
    <div class="bg-overlay"></div>
    <div class="nav-dashboard-wapper custom-scrollbar">
        <div class="nav-dashboard nav-employer_dashboard">
            <div class="nav-dashboard-header">
                <div class="header-wrap">
                    <?php echo Civi_Templates::site_logo('dark'); ?>
                </div>
                <a href="#" class="closebtn">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <?php if (in_array('civi_user_employer', (array)$current_user->roles)) : ?>
                <ul class="list-nav-dashboard">
                    <?php
                    foreach ($key_employer as $key => $value) {
                        $show_employer = civi_get_option('show_employer_' . $key, '1');
                        $image_employer = civi_get_option('image_employer_' . $key, '');
                        $id = civi_get_option('civi_' . $key . '_page_id');
                        ?>
                        <?php if ($show_employer) : ?>
                            <li class="nav-item <?php if (is_page($id) && $key !== "logout") : echo esc_attr('active');
                            endif; ?>">
                                <?php if ($key === "logout") { ?>
                                <a href="<?php echo wp_logout_url(home_url()); ?>">
                                    <?php } else { ?>
                                    <a href="<?php echo get_permalink($id); ?>" class="civi-icon-items"
                                       data-title="<?php echo $value; ?>">
                                        <?php } ?>
                                        <?php if (!empty($image_employer['url'])) : ?>
                                            <span class="image">
                                            <?php if (civi_get_option('type_icon_employer') === 'svg') { ?>
                                                <object class="civi-svg" type="image/svg+xml"
                                                        data="<?php echo esc_url($image_employer['url']) ?>"></object>
                                            <?php } else { ?>
                                                <img src="<?php echo esc_url($image_employer['url']) ?>"
                                                     alt="<?php echo $value; ?>"/>
                                            <?php } ?>
                                            </span>
                                        <?php endif; ?>
                                        <span><?php echo $value; ?></span>
                                        <?php if ($key === "messages") { ?>
                                            <?php civi_get_total_unread_message();?>
                                        <?php } ?>
                                    </a>
                            </li>
                        <?php endif; ?>
                    <?php } ?>
                </ul>
            <?php endif; ?>

                    <a href="<?php echo civi_get_permalink('jobs_submit'); ?>" class="civi-button">
						<i class="far fa-plus"></i><?php esc_html_e('Post a job', 'civi-framework'); ?>
					</a>

        </div>
    </div>
    <a href="#" class="icon-nav-mobie">
        <i class="far fa-bars"></i>
        <span><?php esc_html_e('Sidebar', 'civi-framework'); ?></span>
    </a>
</div>
