<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="entry-my-page my-candidate">
    <div class="entry-title">
        <h4><?php esc_html_e('My Following', 'civi-framework'); ?></h4>
    </div>
    <div class="tab-dashboard">
        <ul class="tab-list">
            <li class="tab-item tab-my-follow"><a href="#tab-my-follow" data-text="<?php esc_attr_e('Following', 'civi-framework'); ?>"><?php esc_html_e('Following', 'civi-framework'); ?></a></li>
            <li class="tab-item tab-candidates-dashboard"><a href="#tab-candidates-dashboard" data-text="<?php esc_attr_e('Followers', 'civi-framework'); ?>"><?php esc_html_e('Followers', 'civi-framework'); ?></a></li>
            <li class="tab-item tab-invite"><a href="#tab-invite" data-text="<?php esc_attr_e('Invite', 'civi-framework'); ?>"><?php esc_html_e('Invite', 'civi-framework'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-my-follow">
                <?php civi_get_template('dashboard/employer/candidates/my-follow.php'); ?>
            </div>
            <div class="tab-info" id="tab-candidates-dashboard">
                <?php civi_get_template('dashboard/employer/candidates/candidates-follow.php'); ?>
            </div>
            <div class="tab-info" id="tab-invite">
                <?php civi_get_template('dashboard/employer/candidates/candidates-invite.php'); ?>
            </div>
        </div>
    </div>
</div>