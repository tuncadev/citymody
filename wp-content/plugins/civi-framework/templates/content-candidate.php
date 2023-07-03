<?php

/**
 * The Template for displaying content candidate
 */

defined('ABSPATH') || exit;

$content_candidate= civi_get_option('archive_company_layout', 'layout-list');

if (!empty($candidate_layout)) {
    $content_candidate = $candidate_layout;
}

$id = $image_size = '';

$id = get_the_ID();

if (!empty($candidate_id)) {
    $id = $candidate_id;
}

if (!empty($custom_candidate_image_size)) {
    $image_size = $custom_company_image_size;
}

$effect_class = 'skeleton-loading';

civi_get_template('candidate/content/' . $content_candidate . '.php', array(
    'candidate_id'                => $id,
    'custom_candidate_image_size' => $image_size,
    'layout'                      => $content_candidate,
    'effect_class'                => $effect_class,
));
