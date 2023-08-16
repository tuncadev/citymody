<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="entry-my-page my-jobs">
    <div class="entry-title">
        <h4><?php esc_html_e('My Jobs', 'civi-framework'); ?></h4>
    </div>
    <div class="tab-dashboard">
        <ul class="tab-list">
            <li class="tab-item tab-apply-item"><a href="#tab-apply"><?php esc_html_e('Applied', 'civi-framework'); ?><span>(<?php echo civi_total_my_apply() ?>)</span></a></li>
            <li class="tab-item tab-wishlist-item"><a href="#tab-wishlist"><?php esc_html_e('Wishlist', 'civi-framework'); ?><span>(<?php echo civi_total_post('jobs', 'my_wishlist') ?>)</span></a></li>

        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-apply">
                <?php civi_get_template('dashboard/candidate/my-jobs/my-apply.php'); ?>
            </div>
            <div class="tab-info" id="tab-wishlist">
                <?php civi_get_template('dashboard/candidate/my-jobs/my-wishlist.php'); ?>
            </div>
            <div class="tab-info" id="tab-invite">
                <?php /* civi_get_template('dashboard/candidate/my-jobs/my-invite.php'); */ ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
window.onload = function(){
	var result = window.location.search;
    if (result != "") {
		jQuery(".tab-apply-item").removeClass("active");
		jQuery('.tab-apply').css('display','none');
		jQuery("#tab-apply").css('display','none');
		jQuery("#tab-wishlist").css('display','block');
		jQuery(".tab-wishlist-item").addClass("active");
	} 
}
</script>