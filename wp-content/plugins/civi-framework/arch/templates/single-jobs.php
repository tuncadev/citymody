<?php

/**
 * The Template for displaying all single jobs
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('civi');

/**
 * @Hook: civi_single_jobs_before
 *
 * @hooked gallery_jobs
 */
do_action('civi_single_jobs_before');

?>

<?php
/**
 * @Hook: civi_layout_wrapper_start
 *
 * @hooked layout_wrapper_start
 */
do_action('civi_layout_wrapper_start');
?>

<?php
/**
 * @Hook: civi_output_content_wrapper_start
 *
 * @hooked output_content_wrapper_start
 */
do_action('civi_output_content_wrapper_start');
?>

<?php while (have_posts()) : the_post(); ?>

    <?php civi_get_template_part('content', 'single-jobs'); ?>

<?php endwhile; // end of the loop.
?>

<?php
/**
 * @Hook: civi_output_content_wrapper_end
 *
 * @hooked output_content_wrapper_end
 */
do_action('civi_output_content_wrapper_end');
?>

<?php

/**
 * @hooked civi_sidebar_jobs
 */
do_action('civi_sidebar_jobs');

?>

<?php
/**
 * @Hook: civi_layout_wrapper_end
 *
 * @hooked layout_wrapper_end
 */
do_action('civi_layout_wrapper_end');
?>

<?php
/**
 * @Hook: civi_single_jobs_after
 *
 * @hooked related_jobs
 */
do_action('civi_single_jobs_after');

get_footer('civi');
