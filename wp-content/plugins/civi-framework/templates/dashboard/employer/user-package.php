<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$package_id = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_id', $user_id);
$package_unlimited_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job', true);
$package_unlimited_featured_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job_featured', true);
$package_num_job = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_number_job', true);
$package_num_featured_job = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_number_featured', true);
$package_activate_date = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_activate_date', true);
$package_time_unit = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_time_unit', true);
$package_period = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_period', true);
$package_unlimited_time = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_time', true);
$package_name = get_the_title($package_id);
$user_info = get_userdata($user_id);
$civi_package = new Civi_Package();
$expired_date = $civi_package->get_expired_date($package_id, $user_id);
$paid_submission_type = civi_get_option('paid_submission_type', 'no');

$current_date = date('Y-m-d');
if ($current_date < $expired_date) {
    $seconds = strtotime($expired_date) - strtotime($current_date);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $expired_jobs = $dtF->diff($dtT)->format('%a');
} else {
    $expired_jobs = 0;
}
?>
<?php if ($paid_submission_type !== 'per_package') : ?>
    <p class="notice"><i class="fal fa-exclamation-circle"></i>
        <?php esc_html_e("You are on free submit active", 'civi-framework'); ?>
        <a href="<?php echo civi_get_permalink('jobs_submit'); ?>">
            <?php esc_html_e('Add Jobs', 'civi-framework'); ?>
        </a>
    </p>
<?php else : ?>
    <?php if ($current_date >= $expired_date) : ?>
        <p class="notice"><i class="fal fa-exclamation-circle"></i>
            <?php esc_html_e("Your package has expired please choose another one", 'civi-framework'); ?>
        </p>
    <?php endif; ?>
    <div class="entry-my-page pakages-dashboard">
        <div class="entry-title">
            <h4><?php esc_html_e('My packages', 'civi-framework') ?></h4>
        </div>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard <?php if ($current_date >= $expired_date && $paid_submission_type == 'per_package') {
                                                echo 'expired';
                                            } ?>">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'civi-framework') ?></th>
                        <th class="col-name"><?php esc_html_e('Package Name', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Number Jobs', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Number Featured', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Job Duration', 'civi-framework') ?></th>
                        <th><?php esc_html_e('Status', 'civi-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="package-id">
                                <?php if ($package_id) {
                                    echo "#$package_id";
                                } ?>
                            </span>
                        </td>
                        <td>
                            <h3><a href="<?php echo civi_get_permalink('package') ?>"><?php echo esc_attr($package_name) ?></a></h3>
                            <p><?php echo esc_attr($package_activate_date) ?></p>
                        </td>
                        <td>
                            <span class="limit">
                                <?php if ($package_unlimited_job == 1) {
                                    esc_html_e('Unlimited', 'civi-framework');
                                } else {
                                    echo $package_num_job;
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="days">
                                <?php if ($package_unlimited_featured_job == 1) {
                                    esc_html_e('Unlimited', 'civi-framework');
                                } else {
                                    echo $package_num_featured_job;
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="remaining">
                                <?php if ($package_unlimited_time == 1) {
                                    esc_html_e('never expires', 'civi-framework');
                                } else {
                                    esc_html_e($expired_jobs) . ' ' . esc_html('Days', 'civi-framework');
                                } ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            global $current_user;
                            $user_id = $current_user->ID;
                            $args_invoice = array(
                                'post_type'           => 'invoice',
                                'posts_per_page'      => 1,
                                'author'              => $user_id,
                            );
                            $data_invoice = new WP_Query($args_invoice);
                            if (!empty($data_invoice->post)) :
                                $invoice_id = $data_invoice->post->ID;
                                $invoice_status = get_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_payment_status', true);
                                if ($invoice_status == 0) { ?>
                                    <span class="label label-pending"><?php esc_html_e('Pending', 'civi-framework') ?></span>
                                <?php } else { ?>
                                    <?php if (($current_date < $expired_date) || ($package_unlimited_time == 1)) { ?>
                                        <span class="label label-open"><?php esc_html_e('Actived', 'civi-framework') ?></span>
                                    <?php } else { ?>
                                        <span class="label label-close"><?php esc_html_e('Expired', 'civi-framework') ?></span>
                            <?php
                                    }
                                }
                            endif;
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="<?php echo civi_get_permalink('package'); ?>" class="civi-button civi-new-package">
            <i class="far fa-plus"></i><?php esc_html_e('Add new package', 'civi-framework') ?>
        </a>
    </div>
<?php endif; ?>