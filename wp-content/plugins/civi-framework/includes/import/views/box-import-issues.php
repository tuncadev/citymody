<?php

/**
 * Issues Box
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

?>
<div class="civi-box civi-box--red civi-box--import-issues">
	<div class="civi-box__header">
		<span class="civi-box__icon"><i class="fad fa-exclamation-triangle"></i></span>
		<span><?php esc_html_e('Issues Detected', 'civi-framework'); ?></span>
	</div>
	<div class="civi-box__body">

		<?php
		/**
		 * Hook: civi_box_import_issues_before_content
		 */
		do_action('civi_box_import_issues_before_content');
		?>

		<ol>
			<?php foreach ($import_issues as $issue) : ?>
				<li><?php echo wp_kses_post($issue); ?></li>
			<?php endforeach; ?>
		</ol>

		<?php
		/**
		 * Hook: civi_box_import_issues_after_content
		 */
		do_action('civi_box_import_issues_after_content');
		?>

	</div>
	<div class="civi-box__footer">
		<span style="color: #dc433f">
			<?php esc_html_e('Please solve all issues listed above before importing demo data.', 'civi-framework'); ?>
		</span>
	</div>
</div>