<?php

/**
 * The Template for displaying all single candidate
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


get_header('civi');

/**
 * @Hook: civi_single_candidate_before
 *
 * @hooked gallery_candidate
 */
do_action('civi_single_candidate_before');

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
 * @Hook: civi_single_candidate_hero
 *
 * @hooked civi_single_candidate_hero
 */
do_action('civi_single_candidate_hero');
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

    <?php civi_get_template_part('content', 'single-candidate'); ?>

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
 * @hooked civi_sidebar_candidate
 */
do_action('civi_candidate_sidebar');
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
 * @Hook: civi_single_candidate_after
 *
 * @hooked related_candidate
 */
do_action('civi_single_candidate_after');

get_footer('civi');
