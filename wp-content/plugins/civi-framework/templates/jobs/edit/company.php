<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="row">
    <div class="form-group col-md-6">
        <label><?php esc_html_e('Select company', 'civi-framework') ?></label>
        <div class="select2-field">
			<select name="jobs_select_company"  class="civi-select2">
				<?php civi_select_post_company(true); ?>
			</select>
		</div>
        <a href="<?php echo get_permalink(civi_get_option('civi_submit_company_page_id')); ?>" class="civi-button button-link" target="_blank"><i class="far fa-plus-circle"></i><?php esc_html_e('Create new company', 'civi-framework'); ?></a>
    </div>
</div>
