<?php

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 */

get_header();

$has_page_title = true;
$layout_sidebar = Civi_Helper::get_setting('layout_sidebar');
$elementor_page = get_post_meta(get_the_ID(), '_elementor_edit_mode', true);

if (!empty($elementor_page) || is_page('cart') || is_page('checkout') || Civi_Helper::civi_page_shortcode('[civi_packages]') || Civi_Helper::civi_page_shortcode('[civi_payment]') || Civi_Helper::civi_page_shortcode('[civi_payment_completed]')) {
	$layout_sidebar = 'no-sidebar';
}

$sidebar_classes[] = $layout_sidebar;

if ($layout_sidebar != 'no-sidebar' && is_active_sidebar('sidebar')) {
	$sidebar_classes[] = 'has-sidebar';
}

?>
<?php echo Civi_Templates::page_title(); ?>

<div class="main-content content-page">

	<div class="container">

		<div class="site-layout <?php echo join(' ', $sidebar_classes); ?>">

			<div id="primary" class="content-area">

				<main id="main" class="site-main">

					<?php
					/* Start the Loop */
					while (have_posts()) : the_post();

						get_template_part('templates/page/content', 'page');

						// If comments are open or we have at least one comment, load up the comment template.
						if ((comments_open() || get_comments_number()) && empty($elementor_page)) {
							comments_template();
						}

					endwhile; // End of the loop.
					?>

				</main>

			</div>

			<?php if (is_active_sidebar('sidebar') && empty($elementor_page)) : ?>

				<?php get_sidebar(); ?>

			<?php endif; ?>
		</div>

	</div>

</div>

<?php
get_footer();
