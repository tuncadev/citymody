<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
$footer_type = Civi_Helper::get_setting("footer_type");
$id = get_the_ID();
$footer_show = $footer_page = '';
if (!empty($id)) {
	$footer_page = get_post_meta($id, 'civi-footer_type', true);
	$footer_show = get_post_meta($id, 'civi-footer_show', true);
} else {
	$footer_page = '';
}
?>

</div><!-- End #content -->
<?php if ($footer_show !== '0') { ?>
	<footer class="site-footer">
		<div class="inner-footer">
			<?php if ($footer_page == '') { ?>
				<?php if ($footer_type !== '') { ?>
					<?php
					if (!function_exists('elementor_location_exits') || !elementor_location_exits('footer', true)) {
						if (defined('ELEMENTOR_VERSION')) {
							echo \Elementor\Plugin::$instance->frontend->get_builder_content($footer_type);
						} else {
							$footer = get_post($footer_type);
							if (!empty($footer->post_content)) {
								$footer_content = $footer->post_content;
								echo wp_kses_post($footer_content);
							}
						}
					} else {
						if (function_exists('elementor_theme_do_location')) :
							elementor_theme_do_location('footer');
						endif;
					}
					?>
				<?php } else { ?>
					<?php get_template_part('templates/footer/copyright'); ?>
				<?php } ?>
			<?php } else {
				$footer = get_post($footer_page);
				if (!empty($footer->post_content)) {
					$footer_content = $footer->post_content;
					echo wp_kses_post($footer_content);
				}
			} ?>
		</div>
		<?php
$copyright_text   = Civi_Helper::get_setting('footer_copyright_text');
?>
<div class="copyright">
	<div class="container">
		<div class="area-copyright">
			<div class="copyright-text" style="text-align:center"><?php echo __("@2023 CityMody. All rights reserved", "civichild"); ?></div>
		</div>
	</div>
</div>
	</footer>
<?php } ?>
</div><!-- End #wrapper -->
<div class="copied_wrapper make_absolute" id="copied_wrapper">
	<div class="copied_txt"><?php esc_html_e('Link to profile was copied to clipboard!', 'civichild') ?></div>
</div>

<?php wp_footer(); ?>

</body>

</html>