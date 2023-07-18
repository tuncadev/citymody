<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}
$candidate_id = civi_get_post_id_candidate();
$candidate_skills = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_skills', false);
$candidate_skills = !empty($candidate_skills) ?  $candidate_skills[0] : '';
$taxonomyName = "candidate_skills";
?>


