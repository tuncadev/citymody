<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_company_fields;
?>

<div class="row">
    <?php if (!in_array('fields_company_name', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label for="company_title"><?php esc_html_e('Company name', 'civi-framework') ?> <sup>*</sup></label>
            <input type="text" id="company_title" name="company_title"
                   placeholder="<?php esc_attr_e('Name', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_category', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Categories', 'civi-framework') ?> <sup>*</sup></label>
            <div class="select2-field">
				<select name="company_categories" class="civi-select2">
					<?php civi_get_taxonomy('company-categories', false, true); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_url', $hide_company_fields)) : ?>
        <div class="form-group col-md-12">
            <label><?php esc_html_e('Company Url Slug', 'civi-framework') ?></label>
            <div class="company-url-warp">
                <input class="input-url" type="text"
                       placeholder="<?php echo esc_url(get_post_type_archive_link('company')) ?>" disabled>
                <input class="input-slug" type="text" id="company_url" name="company_url"
                       placeholder="<?php esc_attr_e('company-name', 'civi-framework') ?>">
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_about', $hide_company_fields)) : ?>
        <div class="form-group col-md-12">
            <label class="label-des-company"><?php esc_html_e('About company', 'civi-framework'); ?>
                <sup>*</sup></label>
            <?php
            $content = '';
            $editor_id = 'company_des';
            $settings = array(
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => $editor_id,
                'textarea_rows' => get_option('default_post_edit_rows', 8),
                'tabindex' => '',
                'editor_css' => '',
                'editor_class' => '',
                'teeny' => false,
                'dfw' => false,
                'tinymce' => true,
                'quicktags' => true
            );
            wp_editor(html_entity_decode(stripcslashes($content)), $editor_id, $settings); ?>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_website', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e(' Website', 'civi-framework'); ?></label>
            <input type="url" id="company_website" name="company_website"
                   placeholder="<?php esc_attr_e('www.domain.com', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_phone', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Phone Number', 'civi-framework'); ?></label>
            <input type="number" id="company_phone" name="company_phone"
                   placeholder="<?php esc_attr_e('+00 12 334 5678', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_email', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Email', 'civi-framework') ?> <sup>*</sup></label>
            <input type="email" id="company_email" name="company_email"
                   placeholder="<?php esc_attr_e('hello@domain.com', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_founded', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Founded in', 'civi-framework') ?></label>
            <div class="select2-field">
				<select name="company_founded" class="civi-select2">
					<?php echo civi_get_company_founded(); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_size', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Company size', 'civi-framework') ?> <sup>*</sup></label>
            <div class="select2-field">
				<select name="company_size" class="civi-select2">
					<?php civi_get_taxonomy('company-size', false, true); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
</div>
