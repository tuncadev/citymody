<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$jobs_meta_data = get_post_custom($id);

global $post;

$id = get_the_ID();

$classes = array('civi-jobs-wrap', 'single-jobs-area');

?>
<div id="jobs-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-jobs-warrper">
        <div class="block-archive-top">
            <?php
            /**
             * Hook: civi_single_jobs_after_summary hook.
             */
            do_action('civi_single_jobs_after_summary'); ?>
            <?php
            /**
             * Hook: civi_single_jobs_summary hook.
             */
            do_action('civi_single_jobs_summary');
            ?>
        </div>
        <?php
        /**
         * Hook: civi_after_content_single_jobs_summary hook.
         */
        do_action('civi_after_content_single_jobs_summary');
        ?>
        <?php
        /**
        * Hook: civi_apply_single_jobs hook.
        */
        do_action('civi_apply_single_jobs',$id);
        ?>
    </div>
</div>