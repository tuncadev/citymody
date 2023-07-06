<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
if (!empty($data_notification)) {
    foreach ($data_notification as $index => $noti) {
        $count = $index + 1;
    }
    if($count > 99){
        $count = '99';
    }
} else {
    $count = 0;
}
?>

<?php if (in_array('civi_user_candidate', (array)$current_user->roles)
    || in_array('civi_user_employer', (array)$current_user->roles)) { ?>
    <a href="#" class="icon-noti">
        <i class="far fa-bell"></i>
        <span><?php echo $count; ?></span>
    </a>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="btn-login icon-noti notice-employer" data-notice="<?php esc_attr_e('Please login Employer or Candidate', 'civi-framework') ?>">
            <i class="far fa-bell"></i>
            <span>0</span>
        </a>
    </div>
<?php } ?>

