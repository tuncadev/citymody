<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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

			if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>

			<?php
			endif;

			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;
			talenthunt_kaya_pagination();
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
