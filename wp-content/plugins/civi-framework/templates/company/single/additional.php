<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$company_id = get_the_ID();
$company_meta_data = get_post_custom($company_id);
$company_data = get_post($company_id);
$custom_field_company = civi_render_custom_field('company');
$image_src = CIVI_PLUGIN_URL . 'assets/images/bg-video.webp';
if (count($custom_field_company) <= 0) {
    return;
}
?>
<?php foreach ($custom_field_company as $key => $field) { ?>
    <?php switch ($field['type']) {
        case 'text':
            if (!empty($company_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner company-additional-text">
                    <div class="additional-warpper">
                        <h4 class="title-company"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($company_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'url':
            if (!empty($company_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner company-additional-url">
                    <div class="additional-warpper">
                        <h4 class="title-company"><?php echo $field['title']; ?></h4>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($company_meta_data[$field['id']][0], array('wmode' => 'transparent')); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'textarea':
            if (!empty($company_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner company-additional-textarea">
                    <div class="additional-warpper">
                        <h4 class="title-company"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($company_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'select':
            if (!empty($company_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner company-additional-select">
                    <div class="additional-warpper">
                        <h4 class="title-company"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($company_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'checkbox_list':
            if (!empty($company_meta_data[$field['id']])) {
                ?>
                <div class="block-archive-inner company-additional-checkbox_list">
                    <div class="additional-warpper">
                        <h4 class="title-company"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php $company_field = get_post_meta($company_data->ID, $field['id'], true);
                            if (empty($company_field)) {
                                $company_field = array();
                            }
                            foreach ($field['options'] as $opt_value) :
                            if (in_array($opt_value, $company_field)) : ?>
                                    <div class="label label-skills"><?php esc_html_e($opt_value); ?></div>
                            <?php endif;
                             endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
    }
} ?>
