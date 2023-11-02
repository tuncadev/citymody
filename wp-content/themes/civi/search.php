<?php

/**
 * The template for displaying search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

get_header();

$blog_content_layout    = Civi_Helper::get_setting('blog_content_layout');
$blog_number_column     = Civi_Helper::get_setting('blog_number_column');
$blog_sidebar           = Civi_Helper::get_setting('blog_sidebar');
$blog_enable_categories = Civi_Helper::get_setting('blog_enable_categories');

$sidebar_classes[] = $blog_sidebar;
if ($blog_sidebar != 'no-sidebar' && is_active_sidebar('sidebar')) {
	$sidebar_classes[]  = 'has-sidebar';
	$blog_number_column = 'columns-2';
}
$post_classes = array('archive-post', 'grid', $blog_content_layout, $blog_number_column, 'columns-sm-2', 'columns-xs-1');

if ($blog_content_layout == 'layout-list') {
	$post_classes = array('archive-post', $blog_content_layout);
}

?>

<?php echo Civi_Templates::page_title(); ?>

<div class="main-content content-blog">

	<div class="container">

		<div class="site-layout <?php echo join(' ', $sidebar_classes); ?>">

			<div id="primary" class="content-area">

				<?php
				if ($blog_enable_categories) :
					echo Civi_Templates::post_categories();
				endif;
				?>

				<main id="main" class="site-main">

					<?php
					if (have_posts()) :
					?>

						<div class="<?php echo join(' ', $post_classes); ?>">

							<?php
							/* Start the Loop */
							while (have_posts()) : the_post();

								/*
							 * Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
								get_template_part('templates/archive/' . $blog_content_layout);

							endwhile;
							?>

						</div>

						<?php echo Civi_Templates::pagination(); ?>

					<?php
					else :

						get_template_part('templates/post/content', 'none');

					endif;
					?>

				</main>

			</div>

			<?php if (is_active_sidebar('sidebar')) : ?>

				<?php get_sidebar(); ?>

			<?php endif; ?>

		</div>

	</div>

</div>

<?php
get_footer();
