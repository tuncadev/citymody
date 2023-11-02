<?php

/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */

get_header();

$post_single_sidebar = Civi_Helper::get_setting('post_single_sidebar');
$elementor_page      = get_post_meta(get_the_ID(), '_elementor_edit_mode', true);

$sidebar = !empty($_GET['sidebar']) ? Civi_Helper::civi_clean(wp_unslash($_GET['sidebar'])) : $post_single_sidebar;

$sidebar_classes[] = $sidebar;
if ($sidebar != 'no-sidebar' && empty($elementor_page) && is_active_sidebar('sidebar')) {
	$sidebar_classes[] = 'has-sidebar';
}
?>

<?php get_template_part('templates/post/post-thumbnail'); ?>

<div class="main-content content-blog">

	<div class="container">

		<div class="site-layout <?php echo join(' ', $sidebar_classes); ?>">

			<div id="primary" class="content-area">

				<main id="main" class="site-main">

					<?php
					/* Start the Loop */
					while (have_posts()) : the_post();

						get_template_part('templates/post/content-single');

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
