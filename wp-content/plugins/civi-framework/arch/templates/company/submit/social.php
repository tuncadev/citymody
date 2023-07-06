<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$enable_social_twitter = civi_get_option('enable_social_twitter', '1');
$enable_social_linkedin = civi_get_option('enable_social_linkedin', '1');
$enable_social_facebook = civi_get_option('enable_social_facebook', '1');
$enable_social_instagram = civi_get_option('enable_social_instagram', '1');
?>

<div class="row">
    <?php if ($enable_social_twitter == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Twitter', 'civi-framework') ?></label>
            <input type="url" name="company_twitter" id="company_twitter"
                   placeholder="<?php esc_attr_e('twitter.com/company', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if ($enable_social_linkedin == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Linkedin', 'civi-framework') ?></label>
            <input type="url" name="company_linkedin" id="company_linkedin"
                   placeholder="<?php esc_attr_e('linkedin.com/company', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if ($enable_social_facebook == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Facebook', 'civi-framework') ?></label>
            <input type="url" name="company_facebook" id="company_facebook"
                   placeholder="<?php esc_attr_e('facebook.com/company', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if ($enable_social_instagram == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Instagram', 'civi-framework') ?></label>
            <input type="url" name="company_instagram" id="company_instagram"
                   placeholder="<?php esc_attr_e('instagram.com/company', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
</div>

<div class="field-social-clone">
    <div class="clone-wrap">
        <div class="soical-remove-inner">
            <a href="#" class="remove-social"><i class="fas fa-times"></i></a>
            <span><?php esc_html_e('Network', 'civi-framework') ?><span class="number-network"></span></span>
        </div>
        <div class="row field-wrap">
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Name', 'civi-framework') ?></label>
				<input type="text" name="company_social_name[]"
                       placeholder="<?php esc_attr_e('Company', 'civi-framework') ?>">
            </div>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Url', 'civi-framework') ?></label>
                <input type="url" name="company_social_url[]"
                       placeholder="<?php esc_attr_e('url.com/company', 'civi-framework') ?>">
            </div>
        </div>
    </div>
</div>

<div class="add-social-list"></div>
<a class="civi-button button-link add-social" href="#addsocial">
    <span class="civi-button-icon"><i class="far fa-chevron-down"></i></span>
    <?php esc_html_e('Add more', 'civi-framework') ?>
</a>


<script>
    (function ($) {
        "use strict";
        jQuery(document).ready(function () {

            $('.add-social').on('click', function (e) {
                e.preventDefault();
                $('.errors-log').text('');
                $('.add-social').addClass('disabled');
                var clone = $('.field-social-clone').html();
                $('.add-social-list').append(clone);
                $('.add-social-list .clone-wrap').each(function (index) {
                    index += 1;
                    $(this).find('.number-network').html(index);
                });
                $('.add-social-list .clone-wrap:last-child').find('.icon-delete').trigger('click');
            });
            $(".add-social-list").bind("DOMSubtreeModified", function () {
                $('.remove-social').on('click', function (e) {
                    e.preventDefault();
                    $(this).parents('.clone-wrap').remove();
                });
            });
        });
    })(jQuery);
</script>
