<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$type_loading_effect      = Civi_Helper::get_setting('type_loading_effect');
$animation_loading_effect = Civi_Helper::get_setting('animation_loading_effect');
$image_loading_effect     = Civi_Helper::get_setting('image_loading_effect');

$args = array('css-1'  => '<span class="civi-ldef-circle civi-ldef-loading"><span></span></span>', 'css-2'  => '<span class="civi-ldef-dual-ring civi-ldef-loading"></span>', 'css-3' => '<span class="civi-ldef-facebook civi-ldef-loading"><span></span><span></span><span></span></span>', 'css-4'  => '<span class="civi-ldef-heart civi-ldef-loading"><span></span></span>', 'css-5'  => '<span class="civi-ldef-ring civi-ldef-loading"><span></span><span></span><span></span><span></span></span>', 'css-6'  => '<span class="civi-ldef-roller civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>', 'css-7'  => '<span class="civi-ldef-default civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>', 'css-8'  => '<span class="civi-ldef-ellipsis civi-ldef-loading"><span></span><span></span><span></span><span></span></span>', 'css-9'  => '<span class="civi-ldef-grid civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>', 'css-10'  => '<span class="civi-ldef-hourglass civi-ldef-loading"></span>', 'css-11'  => '<span class="civi-ldef-ripple civi-ldef-loading"><span></span><span></span></span>', 'css-12'  => '<span class="civi-ldef-spinner civi-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>');

?>

<?php if ($type_loading_effect !== 'none') { ?>

	<div class="page-loading-effect">
		<div class="bg-overlay"></div>

		<div class="entry-loading">
			<?php if ($type_loading_effect == 'css_animation') { ?>
				<?php echo wp_kses($args[$animation_loading_effect], Civi_Helper::civi_kses_allowed_html()); ?>
			<?php } ?>

			<?php if ($type_loading_effect == 'image') { ?>
				<img src="<?php echo esc_url($image_loading_effect); ?>" alt="<?php esc_attr_e('Image Effect', 'civi'); ?>">
			<?php } ?>
		</div>
	</div>

<?php } ?>