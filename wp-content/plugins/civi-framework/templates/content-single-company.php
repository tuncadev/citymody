<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$company_meta_data = get_post_custom($id);
global $post;

$id = get_the_ID();

$classes = array('civi-company-wrap', 'single-company-area');

?>
<div id="company-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-company-warrper">
        <div class="block-archive-top">
            <?php
            /**
             * Hook: civi_single_company_after_summary hook.
             */
            do_action('civi_single_company_after_summary'); ?>
            <?php
            /**
             * Hook: civi_single_company_summary hook.
             */
            do_action('civi_single_company_summary');
            ?>
        </div>
        <?php
        /**
         * Hook: civi_after_content_single_company_summary hook.
         */
        do_action('civi_after_content_single_company_summary'); ?>
    </div>
</div>