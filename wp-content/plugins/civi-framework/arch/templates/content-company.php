<?php

/**
 * The Template for displaying content company
 */

defined('ABSPATH') || exit;

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'login-to-view');
$content_company = civi_get_option('archive_company_layout', 'layout-list');

if (!empty($company_layout)) {
    $content_company = $company_layout;
}

$id = $image_size = '';

$id = get_the_ID();

if (!empty($company_id)) {
    $id = $company_id;
}

if (!empty($custom_company_image_size)) {
    $image_size = $custom_company_image_size;
}

$effect_class = 'skeleton-loading';
civi_get_template('company/content/' . $content_company . '.php', array(
    'company_id'                => $id,
    'custom_company_image_size' => $image_size,
    'layout'                  => $content_company,
    'effect_class'            => $effect_class,
));
