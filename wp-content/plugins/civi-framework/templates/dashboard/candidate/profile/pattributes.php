<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}
global $hide_candidate_fields, $candidate_data, $candidate_meta_data, $current_user;
$user_id = $current_user->ID;

 $candidate_id = civi_get_post_id_candidate();
$candidate_pattributes = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_pattributes']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_pattributes'][0] : '';

pre_me($candidate_meta_data);

?>

<div id="tab-skills" class="tab-info">
    <div class="skills-info block-from">
        <h5><?php esc_html_e('Skills', 'civi-framework') ?></h5>
        <div class="sub-head"><?php esc_html_e('We recommend at least one skill entry', 'civi-framework') ?></div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="candidate_pattributes"><?php esc_html_e('Select Skills', 'civi-framework') ?></label>
                <select class="civi-select2 point-mark" name="candidate_pattributes" id="candidate_pattributes" multiple required>
                    <?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_pattributes', false); ?>
                </select>
            </div>
        </div>
    </div>
</div>
