<?php

/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>

<aside id="secondary" class="widget-area">

	<div class="widget-area-init">

		<?php
		if (is_single() && (get_post_type() == 'jobs')) :
			do_action('civi_single_jobs_sidebar');
		endif;
		?>
		<?php
		if (is_single() && (get_post_type() == 'company')) :
			do_action('civi_single_company_sidebar');
		endif;
		?>
		<?php
		if (is_single() && (get_post_type() == 'candidate')) :
			do_action('civi_single_candidate_sidebar');
		endif;
		?>

		<?php if (get_post_type() == 'jobs') {
			dynamic_sidebar('jobs_sidebar');
		} elseif (get_post_type() == 'company') {
			dynamic_sidebar('company_sidebar');
		} elseif (get_post_type() == 'candidate') {
			dynamic_sidebar('candidate_sidebar');
		} else {
			dynamic_sidebar('sidebar');
		} ?>

	</div>

</aside>
