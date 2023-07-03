<?php

/**
 * The Template for displaying all single company
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('civi');

/**
 * @Hook: civi_single_company_before
 *
 * @hooked single_company_thumbnail
 */
do_action('civi_single_company_before');

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

    <?php civi_get_template_part('content', 'single-company'); ?>

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
 * @hooked civi_sidebar_company
 */
do_action('civi_sidebar_company');
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
 * @Hook: civi_single_company_after
 *
 * @hooked related_company
 */
do_action('civi_single_company_after');

get_footer('civi');
