<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package talenthunt
 */

get_header();
	$choose_sidebar = get_theme_mod('blog_page_sidebar_position') ? get_theme_mod('blog_page_sidebar_position') : 'right';
	if( $choose_sidebar == 'right' ){
		$content_postion = 'two_third';
		$sidebar_position = 'one_third_last';	
	}elseif($choose_sidebar == 'left'){
		$content_postion = 'two_third_last';
		$sidebar_position = 'one_third';
	}else{
		$content_postion = 'fullwidth';
		$sidebar_position = '';
	}
	?>
	<div class="<?php echo esc_html($content_postion); ?> mid-content"> <!-- Middle content align -->
		<?php
		if ( have_posts() ) :
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>
	</div> <!-- End -->
	<?php if( $choose_sidebar != 'none' ){ ?>
	<div class="<?php echo esc_html($sidebar_position); ?>"> <!-- sidebar -->
		<?php get_sidebar(); ?>
	</div>
	<?php } ?>
<?php get_footer(); ?>