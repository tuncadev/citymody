<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'login-to-view');
$candidate_meta_data = get_post_custom($id);

global $post;

$id = get_the_ID();

$classes = array('civi-candidate-wrap', 'single-candidate-area');

?>
<div id="candidate-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-candidate-warrper">
        <div class="block-archive-top">
            <?php
            /**
             * Hook: civi_single_candidate_after_summary hook.
             */
            do_action('civi_single_candidate_after_summary'); ?>
            <?php
            /**
             * Hook: civi_single_candidate_summary hook.
             */
            do_action('civi_single_candidate_summary');
            ?>
        </div>
        <?php
        /**
         * Hook: civi_after_content_single_candidate_summary hook.
         */
        do_action('civi_after_content_single_candidate_summary'); ?>
    </div>
</div>