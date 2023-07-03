<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}
$candidate_id = civi_get_post_id_candidate();
$candidate_skills = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_skills', false);
$candidate_skills = !empty($candidate_skills) ?  $candidate_skills[0] : '';
$taxonomyName = "candidate_skills";
?>

<div id="tab-skills" class="tab-info">
    <div class="skills-info block-from">
        <h5><?php esc_html_e('Skills', 'civi-framework') ?></h5>
        <div class="sub-head"><?php esc_html_e('We recommend at least one skill entry', 'civi-framework') ?></div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="candidate_skills"><?php esc_html_e('Select Skills', 'civi-framework') ?></label>
                <select class="civi-select2 point-mark" name="candidate_skills" id="candidate_skills" multiple required>
					<?php list_skill_options($candidate_id, $taxonomyName); ?>
                </select>
            </div>
        </div>
    </div>
    <?php civi_custom_field_candidate('skills'); ?>
</div>
