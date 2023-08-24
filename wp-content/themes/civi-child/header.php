<?php

/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<script src="https://kit.fontawesome.com/d4521e1f6c.js" crossorigin="anonymous"></script>	
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php if (!function_exists('aioseo') && !function_exists('yoast_breadcrumb')) { ?>
		<meta name="description" content="You are looking for a topic about jobs that optimize seo as well as current best features, please choose our Civi theme">
	<?php } ?>

	<?php if (is_singular() && pings_open(get_queried_object())) : ?>
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
	
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<?php $language = pll_current_language( 'slug' );
	if($language != "") { ?>
	<script>
		var userLocale = "tr";
	</script>
	<?php } else { ?>
		<script>
			var userLocale = "<?php echo $language; ?>";
		</script>
	<?php } ?>
<?php
$dir = '';
$enable_rtl_mode  = Civi_Helper::civi_get_option('enable_rtl_mode', 0);
if (is_rtl() || $enable_rtl_mode) {
	$dir = 'dir=rtl';
}

?>

<body <?php body_class() ?> <?php echo esc_attr($dir); ?>>

	<?php wp_body_open(); ?>

	<?php
	$layout_content         = Civi_Helper::get_setting('layout_content');
	$sticky_header          = Civi_Helper::get_setting('sticky_header');
	$float_header           = Civi_Helper::get_setting('float_header');
	$top_bar_enable         = Civi_Helper::get_setting("top_bar_enable");
	$id = get_the_ID();
	$page_header_show = $page_header_float = $page_header_sticky = $page_top_bar = $header_style = $page_header_rtl = '';
	if (!empty($id)) {
		$page_header_show   = get_post_meta($id, 'civi-header_show', true);
		$page_header_float  = get_post_meta($id, 'civi-show_header_float', true);
		$page_header_sticky = get_post_meta($id, 'civi-show_header_sticky', true);
		$page_header_rtl = get_post_meta($id, 'civi-show_header_rtl', true);
		$page_top_bar       = get_post_meta($id, 'civi-show_top_bar', true);
		$header_style       = get_post_meta($id, 'civi-header_style', true);
	}
	$header_classes = $topbar_classes = array();

	if ($header_style == 'light') {
		$header_classes[] = 'header-light';
	} else {
		$header_classes[] = 'header-dark';
	}

	if ($sticky_header) {
		if ($page_header_sticky == '0') {
			$header_classes[] = '';
		} else {
			$header_classes[] = 'sticky-header';
		}
	} else {
		if ($page_header_sticky == '1') {
			$header_classes[] = 'sticky-header';
		} else {
			$header_classes[] = '';
		}
	}

	if ($float_header) {
		if ($page_header_float == '0') {
			$header_classes[] = '';
		} else {
			$header_classes[] = 'float-header';
		}
	} else {
		if ($page_header_float == '1') {
			$header_classes[] = 'float-header';
		} else {
			$header_classes[] = '';
		}
	}

	if ($page_header_rtl == '1') {
		$header_classes[] = 'rtl';
		$topbar_classes[] = 'rtl';
	} else {
		$header_classes[] = '';
		$topbar_classes[] = '';
	}
	?>

	<div id="wrapper" class="<?php echo esc_attr($layout_content); ?>">

		<?php if ($page_header_show !== '0') : ?>
			<?php if ($top_bar_enable || $page_top_bar == '1') : ?>
				<div class="civi-top-bar <?php echo join(' ', $topbar_classes); ?>">
					<?php get_template_part('templates/top-bar/top-bar'); ?>
				</div>
			<?php endif; ?>
			<header class="site-header <?php echo join(' ', $header_classes); ?>">
				<?php get_template_part('templates/header/header'); ?>
			</header>
		<?php endif; ?>

		<div id="content" class="site-content">