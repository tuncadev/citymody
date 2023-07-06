<?php

/**
 * Import Demos Box
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

$demos       = Civi_Importer::get_import_demos();
$demos_count = count($demos);
?>
<div class="civi-box civi-box--green civi-box--import-demos">
	<div class="civi-box__header">
		<span class="civi-box__icon"><i class="fad fa-download"></i></span>
		<h3>
			<?php
			if (!empty($demos) && 1 < $demos_count) {
				esc_html_e('Select a demo to import', 'civi-framework');
			} elseif (1 === $demos_count) {
				$demo     = reset($demos);
				$name     = isset($demo['name']) ? $demo['name'] : esc_html__('Import Demo', 'civi-framework');
				$imported = get_option(GLF_THEME_SLUG . '_' . key($demos) . '_imported', false);

				if (!$imported) :
					esc_html_e($name);
				else :
					esc_html_e($name);
			?>
					<small><?php esc_html_e('(has been imported before)', 'civi-framework'); ?></small>
			<?php
				endif;
			}
			?>
		</h3>
		<?php if (1 === $demos_count) : ?>
			<a href="#" class="button civi-import-demo__button" data-demo-slug="<?php echo esc_attr(key($demos)); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('fetch_demo_steps')); ?>"><?php esc_html_e('Import Demo Data', 'civi-framework'); ?></a>
		<?php endif; ?>

		<a href="#" class="button civi-import-refresh__button"><?php esc_html_e('Refresh Data', 'civi-framework'); ?></a>
	</div>
	<div class="civi-box__body<?php echo esc_attr(1 < $demos_count) ? ' civi-box__body--flex' : ''; ?>">

		<?php
		/**
		 * Hook: civi_box_import_demos_before_content
		 */
		do_action('civi_box_import_demos_before_content');
		?>

		<p class="civi-error-text"></p>

		<?php if (!empty($demos)) : ?>

			<?php
			$grid_class = '';
			if (0 < $demos_count) {
				$grid_class .= ' grid columns-3';
			}
			?>
			<div class="list-demo <?php echo esc_attr($grid_class); ?>">

				<?php foreach ($demos as $demo_slug => $demo) : ?>
					<?php $imported = get_option(GLF_THEME_SLUG . '_' . $demo_slug . '_imported', false); ?>
					<?php if (isset($demo['name'], $demo['preview_image_url'])) : ?>
						<?php
						$css_class = "civi-import-demo civi-import-demo--{$demo_slug}";
						?>
						<div class="<?php echo esc_attr($css_class); ?>">
							<div class="civi-import-demo__inner">
								<div class="civi-import-demo__preview">
									<img src="<?php echo esc_attr($demo['preview_image_url']); ?>" alt="<?php echo esc_attr($demo['name']); ?>" />
								</div>

								<?php if (1 < $demos_count) : ?>
									<div class="civi-import-demo__footer">
										<p class="civi-import-demo__name">
											<?php if (!$imported) : ?>
												<span><?php esc_html_e($demo['name']); ?></span>
											<?php else : ?>
												<span>
													<?php esc_html_e($demo['name']); ?>
													<small><?php esc_html_e('(has been imported before)', 'civi-framework'); ?></small>
												</span>
											<?php endif; ?>
											<?php if (isset($demo['description'])) : ?>
												<span class="civi-import-demo__help hint--right" aria-label="<?php echo esc_attr($demo['description']); ?>"><i class="fad fa-question-circle"></i></span>
											<?php endif; ?>
										</p>
										<a href="#" class="button civi-import-demo__button" data-demo-slug="<?php echo esc_attr($demo_slug); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('fetch_demo_steps')); ?>">
											<?php esc_html_e('Import', 'civi-framework'); ?>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<!-- /Import <?php esc_html_e($demo['name']); ?> -->
					<?php endif; ?>
				<?php endforeach; ?>

			</div>
		<?php endif; ?>

		<?php
		/**
		 * Hook: civi_box_import_demos_after_content
		 */
		do_action('civi_box_import_demos_after_content');
		?>

	</div>

	<div id="civi-import-demo-popup" class="civi-popup mfp-hide">
	</div>
</div>
