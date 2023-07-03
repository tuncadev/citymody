<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$mess_empty = CIVI_PLUGIN_URL . 'assets/images/mess-empty.svg';

$link_user = $user_name = '';
if (in_array("civi_user_candidate", (array)$current_user->roles)) {
    $user_name = 'companies';
    $link_user = get_post_type_archive_link('company');
} elseif (in_array("civi_user_employer", (array)$current_user->roles)){
    $user_name = 'candidate';
    $link_user = get_post_type_archive_link('candidate');
}
?>
<div class="mess-list empty">
    <div class="tab-mess">
        <ul class="tab-list-mess">
            <li class="tab-item tab-all"><a href="#tab-all"><?php esc_html_e('All', 'civi-framework'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-all">
                <p class="no-mess"><?php esc_html_e('You have no messages', 'civi-framework'); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="mess-content empty">
    <img src="<?php echo esc_url($mess_empty); ?>" alt=""/>
    <h2><?php esc_html_e('Welcome to Messages', 'civi-framework'); ?></h2>
    <p><?php echo sprintf(esc_html__('When you contact a %s, it will appear here.', 'civi-framework'), $user_name) ?></p>
    <a href="<?php echo esc_url($link_user); ?>" target="_blank"
       class="civi-button"><?php echo sprintf(esc_html__('Find %s', 'civi-framework'), $user_name)?></a>
</div>


