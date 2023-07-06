<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$candidate_id = get_the_ID();
$candidate_meta_data = get_post_custom($candidate_id);
$candidate_data = get_post($candidate_id);
$custom_field_candidate = civi_render_custom_field('candidate');
$image_src = CIVI_PLUGIN_URL . 'assets/images/bg-video.webp';
if (count($custom_field_candidate) <= 0) {
    return;
}
?>
<?php switch ($field['type']) {
    case 'text':
        if (!empty($candidate_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-candidate"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php echo sanitize_text_field($candidate_meta_data[$field['id']][0]); ?>
                </div>
            </div>
        <?php }
        break;
    case 'url':
        if (!empty($candidate_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-candidate"><?php echo $field['title']; ?></h4>
                <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                    <?php echo wp_oembed_get($candidate_meta_data[$field['id']][0], array('wmode' => 'transparent')); ?>
                </div>
            </div>
        <?php }
        break;
    case 'textarea':
        if (!empty($candidate_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-candidate"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php echo sanitize_text_field($candidate_meta_data[$field['id']][0]); ?>
                </div>
            </div>
        <?php }
        break;
    case 'select':
        if (!empty($candidate_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-candidate"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php echo sanitize_text_field($candidate_meta_data[$field['id']][0]); ?>
                </div>
            </div>
        <?php }
        break;
    case 'checkbox_list':
        if (!empty($candidate_meta_data[$field['id']])) {
            ?>
            <div class="additional-warpper">
                <h4 class="title-candidate"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php $candidate_field = get_post_meta($candidate_data->ID, $field['id'], true);
                    if (empty($candidate_field)) {
                        $candidate_field = array();
                    }
                    foreach ($field['options'] as $opt_value) :
                        if (in_array($opt_value, $candidate_field)) : ?>
                            <div class="label label-skills"><?php esc_html_e($opt_value); ?></div>
                        <?php endif;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php }
        break;
}
