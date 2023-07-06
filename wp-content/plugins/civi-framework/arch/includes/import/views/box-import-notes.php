<?php

/**
 * Important Notes Box
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

add_thickbox();
?>
<div class="civi-box civi-box--orange civi-box--import-notes">
	<div class="civi-box__header">
		<span class="civi-box__icon"><i class="fad fa-comment-exclamation"></i></span>
		<h3><?php esc_html_e('Important Notes', 'civi-framework'); ?></h3>
	</div>
	<div class="civi-box__body">

		<?php
		/**
		 * Hook: civi_box_import_notes_before_content
		 */
		do_action('civi_box_import_notes_before_content');
		?>

		<ol>
			<li>
				<?php
				echo sprintf(
					/* translators: %s: WordPress Reset plugin URL */
					wp_kses_post(__('No existing posts, pages, categories, images, widgets or any other data will be deleted or modifed, but we recommend installing demo data on a clean WordPress website to prevent conflicts with your current content.<br/>To reset your website before importing, use <a href="%s" class="thickbox" title="Install WordPress Reset">WordPress Reset</a> plugin.', 'civi-framework')),
					esc_url(admin_url('/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&TB_iframe=true&width=800&height=550'))
				);
				?>
			</li>
			<li><?php echo wp_kses_post(__('<strong>All required plugins</strong> should be installed.', 'civi-framework')); ?></li>
			<li>
				<?php echo wp_kses_post(__('Posts, pages, images, widgets, menus and more data will get imported.<br/>Please click on the "Import" button only once and wait until the process is completed, it may take a while.', 'civi-framework')); ?>
			</li>
            <li>
                <?php echo wp_kses_post(__('<strong>Refresh Data</strong> after successful <strong>Import Demo Data</strong> to avoid data errors.', 'civi-framework')); ?></li>
            </li>
		</ol>

		<?php
		/**
		 * Hook: civi_box_import_notes_after_content
		 */
		do_action('civi_box_import_notes_after_content');
		?>

	</div>
</div>